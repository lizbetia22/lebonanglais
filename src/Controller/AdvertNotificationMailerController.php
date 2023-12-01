<?php

namespace App\Controller;

use App\Entity\Advert;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class AdvertNotificationMailerController
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendAdvertNotificationEmail(string $email,Advert $advert): void
    {
        $email = (new Email())
            ->from('lebonangle@gmail.com')
            ->to($email)
            ->subject('Advert created')
            ->html(
                $this->renderEmailTemplate('advert_notification_mailer/index.html.twig', ['advert' => $advert])
            );

        $this->mailer->send($email);
    }

    private function renderEmailTemplate(string $template, array $data): string
    {
        return $this->twig->render($template, $data);
    }
}