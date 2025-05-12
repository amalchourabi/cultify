<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AssociationRepository;
use App\Entity\Don;
use App\Entity\User; // Ajout de l'import pour typage
use App\Form\DonType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PdfGenerator;
use Psr\Log\LoggerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

final class DonController extends AbstractController
{
    #[Route('/don', name: 'app_don_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Typage explicite avec ?User pour indiquer que $user peut être null
        /** @var ?User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos dons.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer tous les dons de l'utilisateur avec vérification
        $dons = $entityManager->getRepository(Don::class)->findBy(['idUser' => $user->getId()]);

        $adapter = new ArrayAdapter($dons);
        $pager = new Pagerfanta($adapter);
        $pager->setCurrentPage($request->query->getInt('page', 1));
        $pager->setMaxPerPage(10);

      

        return $this->render('don/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/don/new/{id}', name: 'app_don_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AssociationRepository $associationRepository,
        int $id
    ): Response {
        $association = $associationRepository->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association not found');
        }

        /** @var ?User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour faire un don.');
            return $this->redirectToRoute('app_login');
        }

        $don = new Don();
        $don->setAssociation($association);
        $don->setStatus('en_attente');
        $don->setIdUser($user);
        $don->setDonorType($user->getRoles()[0]); // Utiliser le premier rôle de l'utilisateur
        if (in_array('ROLE_ORGANISATEUR', $user->getRoles()) && $user->getMontantApayer() > 0) {
            $don->setType('contribution');
        } else {
            $don->setType('don');
        }

        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($don);
            $entityManager->flush();

            $this->addFlash('success', 'Votre don a été enregistré avec succès.');
            return $this->redirectToRoute('app_don_index');
        }

        return $this->render('don/new.html.twig', [
            'form' => $form->createView(),
            'association' => $association,
        ]);
    }

    #[Route('/don/edit/{id}', name: 'app_don_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Don $don): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();
        if (!$user || $don->getIdUser() !== $user) {
            $this->addFlash('error', 'Vous n’êtes pas autorisé à modifier ce don.');
            return $this->redirectToRoute('app_don_index');
        }

        $form = $this->createForm(DonType::class, $don, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été modifié avec succès.');
            return $this->redirectToRoute('app_don_index');
        }

        return $this->render('don/edit.html.twig', [
            'form' => $form->createView(),
            'don' => $don,
        ]);
    }

    #[Route('/don/delete/{id}', name: 'app_don_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Don $don): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();
        if (!$user || $don->getIdUser() !== $user) {
            $this->addFlash('error', 'Vous n’êtes pas autorisé à supprimer ce don.');
            return $this->redirectToRoute('app_don_index');
        }

        $entityManager->remove($don);
        $entityManager->flush();

        $this->addFlash('success', 'Le don a été supprimé avec succès.');
        return $this->redirectToRoute('app_don_index');
    }

    #[Route('/don/confirm/{id}', name: 'app_don_confirm')]
    public function confirm(
        Request $request, 
        EntityManagerInterface $entityManager, 
        Don $don, 
        MailerInterface $mailer,
        LoggerInterface $logger,
        PdfGenerator $pdfGenerator,
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        if (!$user || $don->getIdUser() !== $user) {
            $this->addFlash('error', 'Vous n’êtes pas autorisé à confirmer ce don.');
            return $this->redirectToRoute('app_don_index');
        }

        try {
            $don->setStatus('confirme');
            $entityManager->flush();

            $html = $this->renderView('don/pdf_template.html.twig', [
                'don' => $don,
            ]);
            $filename = 'don_confirmation_' . $don->getId() . '.pdf';
            $filePath = $pdfGenerator->generatePdf($html, $filename);

            $userEmail = $don->getIdUser()->getEmail();
            $logger->info('Préparation de l\'email pour: ' . $userEmail);

            $email = (new Email())
                ->from(new Address('mailtrap@demomailtrap.com', 'Service des Dons'))
                ->to($userEmail)
                ->subject('Confirmation de votre don')
                ->html(sprintf(
                    '<h1>Confirmation de don</h1>
                    <p>Bonjour,</p>
                    <p>Votre don de <strong>%.2f €</strong> pour l\'association <strong>%s</strong> a été confirmé.</p>
                    <p>Merci pour votre générosité !</p>
                    <p>Cordialement,<br>L\'équipe</p>',
                    $don->getMontant(),
                    $don->getAssociation()->getNom()
                ));

            $pdfContent = file_get_contents($filePath);
            $email->attach($pdfContent, $filename, 'application/pdf');

            $mailer->send($email);

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->addFlash('success', 'Le don a été confirmé et un email de confirmation a été envoyé avec le PDF.');
            return $this->redirectToRoute('app_don_index');

        } catch (\Exception $e) {
            $logger->error('Erreur lors de la confirmation du don', [
                'don_id' => $don->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($don->getStatus() !== 'confirme') {
                $don->setStatus('confirme');
                $entityManager->flush();
            }

            $this->addFlash('warning', 'Le don a été confirmé mais l\'envoi de l\'email a échoué. Erreur: ' . $e->getMessage());
            return $this->redirectToRoute('app_don_index');
        }
    }
}