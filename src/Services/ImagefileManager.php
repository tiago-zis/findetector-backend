<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\DriveMetaData;
use App\Entity\File as EntityFile;
use App\Dto\File;
use App\Helper\Constants;
use Doctrine\Persistence\ManagerRegistry;
use Google\Service\Drive\DriveFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class ImagefileManager
{

    private $path;
    private $container;
    private $doctrine;
    private $googleDrive;
    private $user;
    private $em;

    public function __construct(
        ParameterBagInterface $container,
        ManagerRegistry $doctrine,
        GoogleDrive $googleDrive,
        Security $security
    ) {

        $this->container = $container;
        $this->path = $this->container->get('images_path');
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
        $this->googleDrive = $googleDrive;
        $this->user = $security->getUser();
    }

    public function save(array $images): ?array
    {
        $entity = $this->getDriveMetaData();

        foreach ($images as $key => $image) {
            if (!isset($image['driveId'])) {
                $base64 = $this->webBase64Decode($image);
                $file = $this->googleDrive->uploadFileContent($image['fileName'], $image['type'], $base64, $entity->getDriveId());
                $image['driveId'] = $file->id;
            }

            unset($image['content']);
            $images[$key] = $image;
        }

        return $images;
    }

    public function saveGpxFile(?File $gpxFile): array
    {
        $entity = $this->getDriveMetaData(Constants::DRIVE_GPX_FOLDER, Constants::DRIVE_GPX_DATATYPE);
        $base64 = $this->fileToBase64($gpxFile);
        $file = $this->googleDrive->uploadFileContent($gpxFile->getFileName(), $gpxFile->getType(), $base64, $entity->getDriveId());

        return [
            'fileName' => $gpxFile->getFileName(),
            'type' => $gpxFile->getType(),
            'size' => $gpxFile->getSize(),
            'driveId' => $file->id
        ];
    }

    public function download(string $driveId): Response
    {
        return $this->googleDrive->downloadFile($driveId);
    }

    private function getDriveMetaData(
        string $folder = Constants::DRIVE_IMAGES_FOLDER,
        string $dataType = Constants::DRIVE_IMAGES_DATATYPE
    ): DriveMetaData {
        $folderName = $folder . ($_ENV['APP_ENV'] === 'dev' ? '_dev' : '');

        $entity = $this->doctrine->getManager()
            ->getRepository(DriveMetaData::class)
            ->findOneBy(['folderName' => $folderName, 'dataType' => $dataType]);

        if (!$entity) {
            throw new \Exception(sprintf('Unable to find drive folder entity!'));
        }

        return $entity;
    }

    public function recover(?array $images): ?array
    {

        if ($images === null) {
            return [];
        }

        foreach ($images as $key => $image) {
            $file = $this->path . '/' . $image['appName'];
            $base64 = base64_encode(file_get_contents($file));
            $src = 'data: ' . mime_content_type($file) . ';base64,' . $base64;

            $image['content'] = $src;
            $images[$key] = $image;
        }

        return $images;
    }

    private function webBase64Decode($file)
    {
        $data = $file['content'];
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        return base64_decode($data);
    }

    private function fileToBase64(File $file)
    {
        $data = $file->getContent();
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);

        return base64_decode($data);
    }

    public function upload(FileBag $bag): ?array
    {
        if ($bag->count() === 0) {
            return null;
        }

        $list = [];

        foreach ($bag as
            /** @var UploadedFile */
            $uploadedFile) {
            $file = $this->uploadToFolder($uploadedFile);
            $list[] = $file;
        }

        return $list;
    }

    public function uploadDrive(FileBag $bag): ?array
    {
        if ($bag->count() === 0) {
            return null;
        }

        $list = [];
        $driveMetaData = $this->getDriveMetaData();

        foreach ($bag as
            /** @var UploadedFile */
            $uploadedFile) {
            $file = $this->uploadToDrive($uploadedFile, $driveMetaData);
            $list[] = $file;
        }

        return $list;
    }

    public function uploadFromApp(FileBag $bag, int $id, string $module): ?int
    {
        $entity = $this->em->getRepository($module)->find($id);

        if ($bag->get('image') === null || !$entity) {
            return null;
        }

        $driveMetaData = $this->getDriveMetaData();
        $uploadedFile = $bag->get('image');
        $file = $this->uploadToDrive($uploadedFile, $driveMetaData);

        $entity->addFile($file);
        $this->em->persist($entity);
        $this->em->flush();

        return $file->getId();
    }

    private function uploadToDrive(
        UploadedFile $uploadedFile,
        DriveMetaData $driveMetaData
    ): EntityFile {
        $name = $uploadedFile->getClientOriginalName();
        $path = $uploadedFile->getPathname();
        $mime = $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();

        $driveFile = $this->googleDrive->uploadFile2($name, $path, $driveMetaData->getDriveId());
        $driveId = $driveFile->id;
        $d = new \DateTime();
        $file = new EntityFile($name, $mime, $size, $driveId, $this->user, $d);

        $this->em->persist($file);
        $this->em->flush();
        return $file;
    }

    private function uploadToFolder(
        UploadedFile $uploadedFile,
    ): EntityFile {
        $originalName = $uploadedFile->getClientOriginalName();
        $clientMime = $uploadedFile->getClientMimeType();
        $mime = $uploadedFile->guessExtension();
        $size = $uploadedFile->getSize();

        $name = 'img_' . time() . '.' . $mime;
        $d = new \DateTime();
        $uploadedFile->move($_ENV['FILE_UPLOAD_PATH'], $name);
        $file = new EntityFile($originalName, $name, $clientMime, $size, null, $this->user, $d);

        $this->em->persist($file);
        $this->em->flush();
        return $file;
    }

    public function remove(int $id): ?int
    {
        $entity = $this->em->getRepository(EntityFile::class)->find($id);

        if ($entity) {
            $entity->setDeletedAt(new \DateTime());
            $entity->setDeletedBy($this->user);

            return $entity->getId();
        }

        return null;
    }

    public function restore(int $id): ?Response
    {
        $entity = $this->em->getRepository(EntityFile::class)->find($id);

        if ($entity) {
            $response = $this->googleDrive->downloadFile($entity->getDriveId());
            $headers = $response->getHeaders();
            $headers['Access-Control-Expose-Headers'] = 'Content-Disposition, Content-Length, X-Content-Transfer-Id';
            $headers['Content-Disposition'] = 'inline; filename="' . $entity->getName() . '"';
            $headers['X-Content-Transfer-Id'] = $id;

            return new Response($response->getBody()->getContents(), 200, $headers);
        }

        return null;
    }
}
