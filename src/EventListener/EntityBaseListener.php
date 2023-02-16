<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\EntityBase;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

class EntityBaseListener
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        
        $entity = $args->getObject();

        if ($entity instanceof EntityBase) {
            $entity->setCreatedAt(new DateTime());
            
            if (!$entity->getCreatedBy()) {
                $entity->setCreatedBy($this->user);
            }            
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        
        $entity = $args->getObject();

        if ($entity instanceof EntityBase) {
            $entity->setUpdatedAt(new DateTime());
            $entity->setUpdatedBy($this->user);
        }
    }
}