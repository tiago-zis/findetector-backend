<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ImagefileManager;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{

    private $imagefileManager;

    public function __construct(
        ImagefileManager $imagefileManager,        
    ) {        
        $this->imagefileManager = $imagefileManager;        
    }

    #[Route('/api/user/image/upload', name: 'user_image_upload', methods: ['POST'])]
    public function fileUpload(Request $request, EntityManagerInterface $entityManager): Response
    {

        $result = $this->imagefileManager->upload($request->files);

        if (!empty($result) && $result[0] instanceof File) {
            $file = $result[0];
            
            /**@var User */
            $user = $this->getUser();
            $user->setImage($file);

            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse($this->serializeFile($file));
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
