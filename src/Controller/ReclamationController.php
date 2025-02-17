<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Reclamation;
use App\Form\AddEditReclamationType;
use Doctrine\Persistence\ManagerRegistry;

final class ReclamationController extends AbstractController
{
    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function newReclamation(Request $request, ManagerRegistry $doctrine): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setStatut('En Cours'); // Initialize the statut field

        $em = $doctrine->getManager();
        $form = $this->createForm(AddEditReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only persist the email if it has been set (optional)
            if (!$reclamation->getEmail()) {
                $reclamation->setEmail(null);  // Set email as null if not provided
            }

            $em->persist($reclamation);
            $em->flush();

            return $this->redirectToRoute('app_reclamation_list');
        }

        return $this->render('reclamation/form.html.twig', [
            'title' => 'Ajouter une réclamation',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation', name: 'app_reclamation_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $reclamations = $doctrine->getRepository(Reclamation::class)->findAll();

        return $this->render('reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/{id}/edit', name: 'app_reclamation_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Aucune réclamation n\'a été trouvée.');
        }

        $form = $this->createForm(AddEditReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only persist the email if it has been set (optional)
            if (!$reclamation->getEmail()) {
                $reclamation->setEmail(null);  // Set email as null if not provided
            }

            $em->flush();
            return $this->redirectToRoute('app_reclamation_list');
        }

        return $this->render('reclamation/form.html.twig', [
            'title' => 'Modifier une réclamation',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}/delete', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Aucune réclamation n\'a été trouvée.');
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $submittedToken)) {
            $em->remove($reclamation);
            $em->flush();
        }

        return $this->redirectToRoute('app_reclamation_list');
    }
}
