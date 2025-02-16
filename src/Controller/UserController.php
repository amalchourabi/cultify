<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
     // Déplacer cette route avant les autres qui y font référence
     #[Route('/simulate-user', name: 'app_simulate_user')]
     public function simulateUser(Request $request): Response
     {
         return $this->render('user/simulate_user.html.twig');
     }
 
     #[Route('/simulate-user/donateur', name: 'app_simulate_user_donateur')]
     public function simulateDonateur(Request $request): Response
     {
         $session = $request->getSession();
         // Forcer la sauvegarde de la session
         $session->set('user_id', 1);
         $session->set('user_role', 'admin');
         $session->save();  // Ajouter cette ligne
     
         $this->addFlash('success', 'Vous êtes maintenant connecté en tant que admin.');
         
         // Vérifier immédiatement si la session est bien définie
         if (!$session->get('user_id')) {
             $this->addFlash('error', 'Erreur de sauvegarde de session');
         }
     
         return $this->redirectToRoute('app_simulate_user');
     }
 
     #[Route('/simulate-user/user', name: 'app_simulate_user_user')]
     public function simulateUserRole(Request $request): Response
     {
         $session = $request->getSession();
         if (!$session->isStarted()) {
            $session->start();
        }
         $session->set('user_id', 2); // ID de l'utilisateur
         $session->set('user_role', 'user'); // Rôle de l'utilisateur
 
         $this->addFlash('success', 'Vous êtes maintenant connecté en tant qu\'utilisateur.');
         return $this->redirectToRoute('app_simulate_user');
     }
 
     #[Route('/simulate-user/organisateur', name: 'app_simulate_user_organisateur')]
     public function simulateOrganisateur(Request $request): Response
     {
         $session = $request->getSession();
         if (!$session->isStarted()) {
            $session->start();
        }
         $session->set('user_id', 3); // ID de l'utilisateur
         $session->set('user_role', 'organisateur'); // Rôle de l'utilisateur
 
         $this->addFlash('success', 'Vous êtes maintenant connecté en tant qu\'organisateur.');
         return $this->redirectToRoute('app_simulate_user');
     }
 
     #[Route('/simulate-user/clear-session', name: 'app_simulate_user_clear_session')]
     public function clearSession(Request $request): Response
     {
         $session = $request->getSession();
         $session->clear(); // Vider la session
 
         $this->addFlash('success', 'La session a été vidée.');
         return $this->redirectToRoute('app_simulate_user');
     }
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
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

   
}