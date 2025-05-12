<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reponse;
use App\Entity\Reclamation;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\AddEditReponseType;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

final class ReponseController extends AbstractController
{
    #[Route('/reponse/new', name: 'app_reponse_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(AddEditReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la pièce jointe
            $file = $form->get('piece_jointe')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    $reponse->setPieceJointe($fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload du fichier.');
                    return $this->redirectToRoute('app_reponse_new');
                }
            }

            $reponse->setDateReponse(new \DateTime());

            $reclamation = $reponse->getReclamation();
            if ($reclamation) {
                $reclamation->setStatut("Résolu");
            }
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_list');
        }

        return $this->render('reponse/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter une réponse',
        ]);
    }
    #[Route('/reponse', name: 'app_reponse_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $reponses = $doctrine->getRepository(Reponse::class)->findAll();

        return $this->render('reponse/list.html.twig', [
            'reponses' => $reponses,
        ]);
    }

    #[Route('/reponse/{id}/edit', name: 'app_reponse_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $reponse = $em->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Aucune réponse n\'a été trouvée.');
        }

        $form = $this->createForm(AddEditReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la pièce jointe
            $file = $form->get('piece_jointe')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    $reponse->setPieceJointe($fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload du fichier.');
                    return $this->redirectToRoute('app_reponse_edit', ['id' => $id]);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_reponse_list');
        }

        return $this->render('reponse/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier une réponse',
        ]);
    }
    #[Route('/reponse/{id}/delete', name: 'app_reponse_delete', methods: ['GET', 'POST'])]
public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $reponse = $entityManager->getRepository(Reponse::class)->find($id);

    if (!$reponse) {
        throw $this->createNotFoundException('Aucune réponse trouvée.');
    }

    $submittedToken = $request->query->get('_token') ?? $request->request->get('_token');

    if ($this->isCsrfTokenValid('delete' . $reponse->getId(), $submittedToken)) {
        $entityManager->remove($reponse);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_reponse_list');
}

}
