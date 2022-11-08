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
    public function fileRestore(Request $request, $id): Response
    {
        $response = $this->imagefileManager->restore((int) $id);

        if ($response) {
            return $response;
        }

        return new Response('Upload file error!', 400);
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
}
