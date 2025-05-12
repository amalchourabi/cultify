<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Event;
use App\Entity\Reclamation;
use App\Entity\User;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Check if the user is logged in
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
    
        // Retrieve events and reclamations where id_user = current user id
        $events = $entityManager->getRepository(Event::class)->findBy(['idUser' => $user]);
        $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['idUser' => $user]);
    
        // Pass the data to the Twig template
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'events' => $events,
            'reclamations' => $reclamations,
        ]);
    }
    
}
