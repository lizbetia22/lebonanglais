<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer, string $userEmail): Response
    {
        try {
            $email = (new Email())
                ->from('lebonangle@gmail.com')
                ->to($userEmail)
                ->subject('Advertisement LeBonAngle!')
                ->text('Your advertisement was published!')
                ->html('<p>Your advertisement was published!</p>');

            $mailer->send($email);

            return new Response('Email sent successfully!');
        } catch (TransportExceptionInterface $exception) {
            return new Response('Failed to send email: ' . $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
