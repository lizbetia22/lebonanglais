<?php

namespace App\EventListener;

use App\Controller\AdvertNotificationMailerController;
use App\Entity\Advert;
use App\Repository\AdminUserRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AdvertCreatedEventListener
{
    private AdvertNotificationMailerController $mailerController;
    private AdminUserRepository $adminUserRepository;

    public function __construct(
        AdvertNotificationMailerController $mailerController,
        AdminUserRepository $adminUserRepository
    ) {
        $this->mailerController = $mailerController;
        $this->adminUserRepository = $adminUserRepository;
    }

    public function postPersist(Advert $advert, LifecycleEventArgs $event): void
    {
        $adminUsers = $this->adminUserRepository->findAll();

        foreach ($adminUsers as $adminUser) {
            $this->mailerController->sendAdvertNotificationEmail($adminUser->getEmail(), $advert);
        }
    }
}