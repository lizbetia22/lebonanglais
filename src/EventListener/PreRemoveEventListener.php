<?php

namespace App\EventListener;

use App\Entity\AdminUser;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(Events::preRemove)]
class PreRemoveEventListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Category) {
            if (count($entity->getAdverts()) > 0) {
                throw new \Exception('You cannot delete a category with adverts.');
            }
        }

        if ($entity instanceof AdminUser){
            if ($entity->getEmail()  === $this->tokenStorage->getToken()->getUser()->getEmail()){
                throw new \Exception('You cannot delete your own account.');
            }
        }
    }
}