<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $photoFile = $form->get('profilepicture')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                try {
                    $photoFile->move($this->getParameter('images_directory'), $newFilename);
                    $user->setProfilepicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload du fichier.');
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

   /* #[Route('/{id}/relations', name: 'user_relations', methods: ['GET'])]
    public function getUserWithRelations(UserRepository $userRepository, int $id): JsonResponse
    {
        $user = $userRepository->findUserWithRelations($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Manually formatting the response to avoid circular references
        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getName(), // Replace with actual getter
            'email' => $user->getEmail(), // Replace with actual getter
            'history' => array_map(fn($h) => [
                'id' => $h->getId(),
                'action' => $h->getAction(), // Replace with actual getter
                'date' => $h->getCreatedAt()?->format('Y-m-d H:i:s'),
            ], $user->getHistory()->toArray()),

            'contributedCategories' => array_map(fn($c) => [
                'id' => $c->getId(),
                'name' => $c->getName(), // Replace with actual getter
            ], $user->getContributedCategories()->toArray()),

            'offers' => array_map(fn($o) => [
                'id' => $o->getId(),
                'title' => $o->getTitle(), // Replace with actual getter
                'price' => $o->getPrice(), // Replace with actual getter
            ], $user->getOffers()->toArray()),

            'reclamations' => array_map(fn($r) => [
                'id' => $r->getId(),
                'issue' => $r->getIssue(), // Replace with actual getter
                'status' => $r->getStatus(), // Replace with actual getter
            ], $user->getReclamations()->toArray()),
        ]);
    }*/
}
