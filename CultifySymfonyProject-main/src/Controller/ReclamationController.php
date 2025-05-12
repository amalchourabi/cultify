<?php

namespace App\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Reclamation;
use App\Form\AddEditReclamationType;
use App\Service\BadWordFilterService;
use App\Service\InfobipService; // Importer le service Infobip
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function newReclamation(
    Request $request,
    ManagerRegistry $doctrine,
    BadWordFilterService $badWordFilterService,
    InfobipService $infobipService,
    UserInterface $user // Inject the currently logged-in user
): Response {
    $reclamation = new Reclamation();
    $reclamation->setStatut('En Cours');

    // Set the logged-in user to the reclamation entity
    if ($user) {
        $reclamation->setIdUser($user); // Assuming the 'id_user' field is a User object
    }

    $entityManager = $doctrine->getManager();
    $form = $this->createForm(AddEditReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Nettoyer la description en utilisant BadWordFilterService
        $description = $reclamation->getDescription();
        $cleanedDescription = $badWordFilterService->filterText($description); // Utilisation du service
        $reclamation->setDescription($cleanedDescription);

        // Gérer l'upload de fichier
        $file = $form->get('piece_jointe')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',$originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($this->getParameter('upload_directory'), $newFilename);
                $reclamation->setPieceJointe($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier : ' . $e->getMessage());
                return $this->render('reclamation/form.html.twig', [
                    'title' => 'Créer une réclamation',
                    'form' => $form->createView(),
                ]);
            }
        }

        // Persister et enregistrer la réclamation
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Envoyer un SMS après la création de la réclamation
        $phoneNumber = '+21694435157'; // Remplacez par le numéro de téléphone du destinataire
        $smsMessage = 'Votre réclamation a été enregistrée avec succès. Merci !';
        $infobipService->sendSms($phoneNumber, $smsMessage);

        $this->addFlash('success', 'La réclamation a été créée avec succès.');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('reclamation/form.html.twig', [
        'title' => '‎ ',
        'form' => $form->createView(),
    ]);
}


    #[Route('/reclamation', name: 'app_reclamation_list', methods: ['GET'])]
    public function list(ManagerRegistry $doctrine): Response
    {
        $reclamations = $doctrine->getRepository(Reclamation::class)->findAll();

        return $this->render('reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ManagerRegistry $doctrine,
        BadWordFilterService $badWordFilterService, // Injection du service BadWordFilterService
        InfobipService $infobipService, // Injection du service InfobipService
        int $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Aucune réclamation trouvée pour l\'ID ' . $id);
        }

        $form = $this->createForm(AddEditReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Nettoyer la description avant de sauvegarder
            $description = $reclamation->getDescription();
            $cleanedDescription = $badWordFilterService->filterText($description); // Utilisation du service
            $reclamation->setDescription($cleanedDescription);

            $entityManager->flush();

           
            $phoneNumber = '+21694435157';
            $smsMessage = 'Une réclamation a été mise à jour avec succès. Merci !';
            $infobipService->sendSms($phoneNumber, $smsMessage);

            $this->addFlash('success', 'Réclamation mise à jour avec succès.');
            return $this->redirectToRoute('app_reclamation_list');
        }

        return $this->render('reclamation/form.html.twig', [
            'title' => '‎ ',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}/delete', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Aucune réclamation trouvée pour l\'ID ' . $id);
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $submittedToken)) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_reclamation_list');
    }
}