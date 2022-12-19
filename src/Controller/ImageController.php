<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ImagefileManager;
use Doctrine\ORM\EntityManagerInterface;

class ImageController extends AbstractController
{

    private $imagefileManager;

    public function __construct(
        ImagefileManager $imagefileManager,        
    ) {        
        $this->imagefileManager = $imagefileManager;        
    }


    #[Route('/api/image/upload', name: 'image_upload', methods: ['POST'])]
    public function fileUpload(Request $request, EntityManagerInterface $entityManager): Response
    {

        $result = $this->imagefileManager->upload($request->files);

        if (!empty($result) && $result[0] instanceof File) {
            $file = $result[0];
            $image = new Image();
            $image->setFile($file);

            $entityManager->persist(($image));
            $entityManager->flush();
            return new JsonResponse($this->serializeFile($file));
        }

        return new Response('Upload file error!', 400);
    }

    #[Route('/api/image/restore/{id}', name: 'api_image_restore', methods: ['GET'])]
    public function fileRestore(Request $request, $id, EntityManagerInterface $entityManager): Response
    {

        $image = $entityManager->getRepository(Image::class)->find((int)$id);

        if ($image) {
            $name = $image->getFile()->getName();
            return $this->file($_ENV['FILE_UPLOAD_PATH'].$name);
        }

        return new Response('Upload file error!', 400);
    }

    #[Route('/api/image/crop', name: 'api_image_crop', methods: ['POST'])]
    public function cropImage(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $id = isset($parameters['id']) ? (int)$parameters['id'] : 0;
        $uid = isset($parameters['uid']) ? $parameters['uid'] : '';

        /**@var Image */
        $image = $entityManager->getRepository(Image::class)->find((int)$id);

        if ($image) {
            $name = $image->getFile()->getName();
            $data = $image->getProcessedData();
            $boxes = $data['boxes'];

            foreach($boxes as $box) {                

                if ($box['uid'] === $uid) {

                    $p1 = $box['p1'];
                    $p2 = $box['p2'];
                    $width = $p2['x'] - $p1['x'];
                    $height = $p2['y'] - $p1['y'];

                    $im = null;
                    $type = '';

                    if (in_array($image->getFile()->getMime(),['image/png', 'png'])) {
                        $im = imagecreatefrompng($_ENV['FILE_UPLOAD_PATH'].$name);
                        $type = 'png';
                    } else if (in_array($image->getFile()->getMime(),['image/jpeg', 'jpg', 'jpeg'])) {
                        $im = imagecreatefromjpeg($_ENV['FILE_UPLOAD_PATH'].$name);
                        $type = 'jpeg';
                    }


                    if ($im) {
                        $im2 = imagecrop($im, ['x' => $p1['x'], 'y' => $p1['y'], 'width' => $width, 'height' => $height]);
                        imagedestroy($im);

                        if ($im2 !== FALSE) {

                            $base64 = $this->GDImageToBase64($im2, $type);
                            return new Response($base64);

                            return $this->file($base64);
                        }                        
                    }                    
                }

            }
        }

        return new Response('Crop file error!', 400);
    }

    #[Route('/api/image/isvalid', name: 'api_image_isvalid', methods: ['POST'])]
    public function isValid(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $id = isset($parameters['id']) ? (int)$parameters['id'] : 0;
        $uid = isset($parameters['uid']) ? $parameters['uid'] : '';
        $valid = isset($parameters['valid']) ? $parameters['valid'] : null;

        if ($valid === 'true') {
            $valid = true;
        } else if ($valid === 'false') {
            $valid = false;
        }

        /**@var Image */
        $image = $entityManager->getRepository(Image::class)->find((int)$id);

        if ($image) {
            $data = $image->getProcessedData();
            $boxes = $data['boxes'];

            foreach($boxes as $k => $box) {
                if ($box['uid'] === $uid) {
                    $boxes[$k]['valid'] = $valid;                    
                }
            }

            $data['boxes'] = $boxes;
            $image->setProcessedData($data);
            
            $entityManager->persist($image);
            $entityManager->flush();

            return new JsonResponse(['id'=>$id, 'uid'=>$uid]);
        }

        return new Response('Crop file error!', 400);
    }

    private function serializeFile(File $file): array {

        return [
            "id"=>$file->getId(),
            "name"=>$file->getName(),
            "mime"=>$file->getMime(),
            "size"=>$file->getSize(),
            "driveId"=>$file->getDriveId()
        ];
    }

    private function GDImageToBase64(\GdImage $imagem, string $formato) {
        $formato                        = strtolower($formato);
        ob_start();
    
        match ($formato) {
            "gif"                       => imagegif(image: $imagem),
            "png"                       => imagepng(image: $imagem, quality: 0),
            "webp"                      => imagewebp(image: $imagem, quality: 100),
            "bitmap"                    => imagebmp(image: $imagem, compressed: False),
            "jpeg"                      => imagejpeg(image: $imagem, quality: 100),
            default                     => imagejpeg(image: $imagem, quality: 100),
        };
    
        $base64                         = ob_get_clean();
        $base64                         = base64_encode($base64);
        return "data:image/$formato;base64,$base64";
    }
}
