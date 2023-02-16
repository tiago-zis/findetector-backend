<?php

namespace App\Filter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class Configurator
{
    protected $em;
    protected $reader;
    private $security;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Security $security, Reader $reader)
    {
        $this->em              = $em;
        $this->tokenStorage = $tokenStorage->getToken();
        $this->reader          = $reader;
        $this->security = $security;
    }

    public function onKernelRequest()
    {
        if (/** @var User */ $user = $this->security->getUser()) {
            $filter = $this->em->getFilters()->enable('appUserFilter');
            
            $filter->setParameter('id', $user->getId());
            $filter->setAnnotationReader($this->reader);
        }
    }    

}