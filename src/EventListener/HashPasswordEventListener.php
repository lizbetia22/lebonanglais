<?php

namespace App\EventListener;

use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
class HashPasswordEventListener
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,){

    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $this->setPassword($event);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->setPassword($event);
    }

    private function setPassword(PrePersistEventArgs|PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        if(!$entity instanceof AdminUser) {
            return;
        }

        if(empty($entity->getPlainPassword())) {
            return;
        }

        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPlainPassword()));
        $entity->setPlainPassword(null);
    }

}