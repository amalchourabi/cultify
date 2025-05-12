<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // Render the admin dashboard
        return $this->render('admin/index.html.twig');
    }
    
    #[Route('/profileadmin', name: 'app_profile_admin')]
    public function show(): Response
    {
        $user = $this->getUser();


        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login'); 
        }
        return $this->render('admin/adminprofile.html.twig', [
            'user' => $user,
        ]);
    
}
}