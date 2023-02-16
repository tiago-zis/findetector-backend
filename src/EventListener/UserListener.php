<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\EntityBase;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserListener
{

    private $user;
    private $userPasswordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->user = $security->getUser();
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {

            if ($entity->getId() === null && $entity->getCreatedAt() === null) {
                $entity->setCreatedAt(new \DateTime());
            }

            $this->checkRules($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {            
            $entity->setUpdatedAt(new \DateTime());
            $this->checkRules($entity);
        }
    }

    private function checkRules($entity)
    {
        if ($entity->getPlainPassword()) {
            $entity->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $entity,
                    $entity->getPlainPassword()
                )
            );

            $entity->setPlainPassword(null);
        }
    }
}
