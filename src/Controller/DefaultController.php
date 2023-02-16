<?php

namespace App\Controller;

use App\Entity\File;
use App\Helper\TmpFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ImagefileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends AbstractController
{

    private $imagefileManager;

    public function __construct(
        ImagefileManager $imagefileManager,        
    ) {        
        $this->imagefileManager = $imagefileManager;        
    }
    
    #[Route('/api/test', name: 'api_test')]
    public function test(): Response
    {
        //$expiry_time = new \DateTime('2022-12-12 11:20:00');
        $expiry_time = new \DateTime('2022-11-29 13:20:00');
        $current_date = new \DateTime();
        $diff = $expiry_time->diff($current_date);

        echo ($diff->days > 0 ? ($diff->days . " dia(s) ") : "") .  $diff->format('%H hrs %I min'); die;

        return new Response('');
    }

    #[Route('/api/file/upload', name: 'file_upload', methods: ['POST'])]
    public function fileUpload(Request $request): Response
    {

        $result = $this->imagefileManager->upload($request->files);
        if (!empty($result)) {

            

            return new JsonResponse($this->serializeFile($result[0]));
        }

        return new Response('Upload file error!', 400);
    }

    #[Route('/api/file/restore/{id}', name: 'api_file_restore', methods: ['GET'])]
    public function fileRestore(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $image = $entityManager->getRepository(File::class)->find((int)$id);

        if ($image) {
            $name = $image->getName();
            return $this->file($_ENV['FILE_UPLOAD_PATH'].$name);
        }

        return new Response('Upload file error!', 400);
    }

    private function serializeFile(File $file): array {

        return [
            "id"=>$file->getId(),
            "name"=>$file->getOriginalName(),
            "mime"=>$file->getMime(),
            "size"=>$file->getSize(),
            "driveId"=>$file->getDriveId()
        ];
    }
}
