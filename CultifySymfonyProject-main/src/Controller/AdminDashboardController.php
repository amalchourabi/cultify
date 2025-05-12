<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }
    #[Route('/Data', name: 'app_data')]
    public function getdata(): Response
    {
        $user = $this->getUser();

        // Check if the user is logged in
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login'); // Redirect to login if not logged in
        }

        // Pass the user data to the Twig template
        return $this->render('baseback.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/promote-to-admin/{id}', name: 'promote_to_admin')]
    public function promoteToAdmin(User $user, EntityManagerInterface $entityManager): Response
    {
        // Ensure only admins can promote users
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Add ROLE_ADMIN to the user's roles
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);

            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'User promoted to admin successfully.');
        } else {
            $this->addFlash('warning', 'User is already an admin.');
        }

        // Redirect back to the user list or profile page
        return $this->redirectToRoute('app_user_index'); // Adjust the route as needed
    }
    #[Route('/revoke-admin/{id}', name: 'revoke_admin')]
    public function revokeAdmin(User $user, EntityManagerInterface $entityManager): Response
    {
        // Ensure only admins can revoke admin roles
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Remove ROLE_ADMIN from the user's roles
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
            $user->setRoles($roles);

            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Admin role revoked successfully.');
        } else {
            $this->addFlash('warning', 'User is not an admin.');
        }

        // Redirect back to the user list or profile page
        return $this->redirectToRoute('app_user_index'); // Adjust the route as needed
    }
    

}
