<?php

namespace App\Tests\Controller;

use App\Controller\AdvertNotificationMailerController;
use App\Entity\Advert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class AdvertNotificationMailerControllerTest extends TestCase
{
    public function testSendAdvertNotificationEmail(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->willReturn('<html><body>Mocked email content</body></html>');

        $controller = new AdvertNotificationMailerController($mailer, $twig);

        $advert = new Advert();

        $email = 'test@example.com';
        $controller->sendAdvertNotificationEmail($email, $advert);
    }
}
