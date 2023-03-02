<?php

namespace App\Controller;

use App\Entity\TermsOfUse;
use App\Entity\UserTermsOfUse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class TermsOfUseController extends AbstractController
{

    #[Route('/api/termsofuse/accept', name: 'api_termsofuse_accept', methods: ['POST'])]
    public function fileRestore(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $content = json_decode($request->getContent(), true);
        
        if (isset($content['id']) && isset($content['accept']) && $content['accept'] == true) {
            $userTerms = $this->getUserTermsOfUse($entityManager, $content['id']);


            if ($userTerms) {
                $userTerms->setAccepted(true);
                $userTerms->setAcceptanceDate(new \DateTime());

                $entityManager->persist($userTerms);
                $entityManager->flush();

                return new JsonResponse(['success'=>true]);
            }
        }

        return new Response('Upload file error!', 400);
    }

    public function getUserTermsOfUse($em, $id = null): ?UserTermsOfUse
    {

        $list = $this->getUser()->getTermsOfUseList();

        foreach ($list as $item) {
            if ($id === $item->getTerms()->getId()) {
                return $item;
            }
        }

        $terms = $em->getRepository(TermsOfUse::class)->find((int)$id);

        if ($terms) {
            $userTerms = new UserTermsOfUse();
            $userTerms->setTerms($terms);
            $userTerms->setUser($this->getUser());
            return $userTerms;
        }

        return null;
    }
    
}
