<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContenuMutimediaController extends AbstractController
{
    #[Route('/contenu/mutimedia', name: 'app_contenu_mutimedia')]
    public function index(): Response
    {
        return $this->render('contenu_mutimedia/index.html.twig', [
            'controller_name' => 'ContenuMutimediaController',
        ]);
    }
}
