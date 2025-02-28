<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AssociationRepository;
use App\Entity\Don;
use App\Form\DonType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PdfGenerator;
use Psr\Log\LoggerInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

final class DonController extends AbstractController
{
    #[Route('/don', name: 'app_don_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the user from the session
        $session = $request->getSession();
        $userId = $session->get('user_id');
    
        if (!$userId) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos dons.');
            return $this->redirectToRoute('app_simulate_user');
        }
    
        // Fetch dons from the database
        $dons = $entityManager->getRepository(Don::class)->findBy(['idUser' => $userId]);
    
        // Get the reCAPTCHA site key from the container
        $recaptchaSiteKey = $this->getParameter('recaptcha_site_key');
    
        return $this->render('don/index.html.twig', [
            'dons' => $dons,
            'recaptcha_site_key' => $recaptchaSiteKey,
        ]);
    }
#[Route('/don/new/{id}', name: 'app_don_new')]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    AssociationRepository $associationRepository,
    UserRepository $userRepository,
    int $id,
    Recaptcha3Validator $recaptcha3Validator
): Response {
    // Récupérer l'association sélectionnée
    $association = $associationRepository->find($id);
    if (!$association) {
        throw $this->createNotFoundException('Association not found');
    }

    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
    $userRole = $session->get('user_role');

    if (!$userId || !$userRole) {
        $this->addFlash('error', 'Vous devez être connecté pour faire un don.');
        return $this->redirectToRoute('app_simulate_user');
    }

    // Récupérer l'objet User à partir de l'ID
    $user = $userRepository->find($userId);
    if (!$user) {
        $this->addFlash('error', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('app_simulate_user');
    }

    // Créer un nouveau don
    $don = new Don();
    $don->setAssociation($association);
    $don->setStatus('en_attente');
    $don->setIdUser($user);
    $don->setDonorType($userRole);
    if ($user->getRole() == "organisateur" && $user->getMontantAPayer() > 0) {
        $don->setType("contribution");
    } else {
        $don->setType("don");
    }

    // Créer le formulaire
    $form = $this->createForm(DonType::class, $don);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Validate reCAPTCHA
        $recaptchaError = $recaptcha3Validator->getLastResponse()->getErrorCodes();
        if (!empty($recaptchaError)) {
            $this->addFlash('error', 'Veuillez compléter le reCAPTCHA.');
            return $this->redirectToRoute('app_don_new', ['id' => $id]);
        }

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
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
    
    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce don.');
        return $this->redirectToRoute('app_don_index'); // Redirection ici
    }

    // Créer le formulaire (mode édition)
    $form = $this->createForm(DonType::class, $don, [
        'is_edit' => true, // Mode édition
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
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');

    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce don.');
        return $this->redirectToRoute('app_don_index');
    }

    // Supprimer le don
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
    Recaptcha3Validator $recaptcha3Validator
): Response {
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
   
    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à confirmer ce don.');
        return $this->redirectToRoute('app_don_index');
    }

    try {
        // Confirmer le don
        $don->setStatus('confirme');
        $entityManager->flush();
        
        

        // Générer le PDF
        $html = $this->renderView('don/pdf_template.html.twig', [
            'don' => $don,
        ]);
        
        $filename = 'don_confirmation_' . $don->getId() . '.pdf';
        $filePath = $pdfGenerator->generatePdf($html, $filename);
        
        
        // Vérifier que le fichier existe
        

        // Vérifier la taille du fichier PDF
        $fileSize = filesize($filePath);
        

        // Envoyer un email de confirmation avec le PDF en pièce jointe
        $userEmail = $don->getIdUser()->getEmail();
        
        $logger->info('Préparation de l\'email pour: ' . $userEmail);
        
        $email = (new Email())
            ->from(new Address('no-reply@demomailtrap.co', 'Service des Dons'))
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
            
        // Lire le contenu du PDF
        $pdfContent = file_get_contents($filePath);
       
        
        // Attacher le PDF directement depuis le contenu binaire
        $email->attach($pdfContent, $filename, 'application/pdf');
       
        $mailer->send($email);
        

        // Supprimer le fichier temporaire après l'envoi
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
        
        // Mettre quand même le don en statut confirmé si l'erreur est survenue après
        if ($don->getStatus() !== 'confirme') {
            $don->setStatus('confirme');
            $entityManager->flush();
            
        }
        
        $this->addFlash('warning', 'Le don a été confirmé mais l\'envoi de l\'email a échoué. Erreur: ' . $e->getMessage());
        return $this->redirectToRoute('app_don_index');
    }
}
}
