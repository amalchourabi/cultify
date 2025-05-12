<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mailling', name: 'send_test_email')]
    public function sendTestEmail(MessageBusInterface $bus): Response
    {
        $email = (new Email())
            ->from('mailtrap@demomailtrap.com')
            ->to('amalchourabi203@gmail.com')
            ->subject('Test d\'envoi d\'email asynchrone')
            ->text('Ceci est un email de test envoyé via Mailtrap et Messenger.')
            ->html('<p>Ceci est un <strong>email asynchrone</strong>.</p>');

        // Ajouter l'email à la file d'attente Messenger
        $bus->dispatch(new SendEmailMessage($email));

        return new Response('Email en file d\'attente !');
    }
    #[Route('/test-mail', name: 'test_mail')]
public function testMail(MailerInterface $mailer): Response
{
    $email = (new Email())
        ->from('mailtrap@demomailtrap.com')
        ->to('amalchourabi203@gmail.com')
        ->subject('Test direct')
        ->text('Test sans Messenger.');

    $mailer->send($email);

    return new Response('Email envoyé directement !');
}

}
