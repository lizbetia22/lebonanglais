<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerControllerTest extends WebTestCase
{
    public function testSendEmailSuccess(): void
    {
        $client = static::createClient();

        $mailer = $this->createMock(MailerInterface::class);
        $client->getContainer()->set('Symfony\Component\Mailer\MailerInterface', $mailer);

        $userEmail = 'test@example.com';

        $mailerController = $client->getContainer()->get('App\Controller\MailerController');

        $email = (new Email())
            ->from('lebonangle@gmail.com')
            ->to($userEmail)
            ->subject('Advertisement LeBonAngle!')
            ->text('Your advertisement was published!')
            ->html('<p>Your advertisement was published!</p>');

        $mailer->expects($this->once())
            ->method('send')
            ->with($this->equalTo($email));

        $response = $mailerController->sendEmail($mailer, $userEmail);
        $this->assertEquals('Email sent successfully!', $response->getContent());
    }

    public function testSendEmailFailure(): void
    {
        $client = static::createClient();
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->method('send')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $client->getContainer()->set('Symfony\Component\Mailer\MailerInterface', $mailer);

        $userEmail = 'test@example.com';

        $mailerController = $client->getContainer()->get('App\Controller\MailerController');

        $response = $mailerController->sendEmail($mailer, $userEmail);
        $this->assertEquals('Failed to send email: ', $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }
}
