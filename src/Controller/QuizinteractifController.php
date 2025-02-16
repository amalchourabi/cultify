<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuizinteractifController extends AbstractController
{
    #[Route('/quizinteractif', name: 'app_quizinteractif')]
    public function index(): Response
    {
        return $this->render('quizinteractif/index.html.twig', [
            'controller_name' => 'QuizinteractifController',
        ]);
    }
}
