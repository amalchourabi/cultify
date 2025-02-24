<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mailling', name: 'send_test_email')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        // Crée un email
        $email = (new Email())
            ->from('mailtrap@demomailtrap.com') // Adresse d'expéditeur
            ->to('adamo1598@gmail.com') // Adresse du destinataire
            ->subject('Test d\'envoi d\'email avec Mailtrap') // Sujet de l'email
            ->text('Ceci est un email de test envoyé depuis Symfony avec Mailtrap.') // Contenu texte
            ->html('<p>Ceci est un <strong>email de test</strong> envoyé depuis Symfony avec Mailtrap.</p>'); // Contenu HTML

        // Envoie l'email
        $mailer->send($email);

        // Retourne une réponse
        return new Response('Email envoyé avec succès !');
    }
}
