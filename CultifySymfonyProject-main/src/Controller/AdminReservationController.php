<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminReservationController extends AbstractController
{
    #[Route('/admin/reservation', name: 'app_admin_reservation')]
    public function index(): Response
    {
        return $this->render('admin_reservation/index.html.twig', [
            'controller_name' => 'AdminReservationController',
        ]);
        
    }
}
