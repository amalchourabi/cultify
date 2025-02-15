<?php

namespace App\Controller;
use App\Entity\Quiz;
use App\Form\AddEditQuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(): Response
    {
        return $this->render('quiz/index.html.twig', [
            'controller_name' => 'QuizController',
        ]);
    }
    #[Route('/quiz/list', name: 'app_quiz_list')]
    public function listQuiz(QuizRepository $QuizRepository){
        $QuizDB= $QuizRepository->findAll();
        return $this->render('quiz/list.html.twig',[
            'Quizs' => $QuizDB
        ]);
    }
    #[Route('/quiz/new', name: 'app_quiz_new')]
    public function newQuiz(Request $request,EntityManagerInterface $em){
        $Quiz= new Quiz();
        $form= $this->createForm(AddEditQuizType::class,$Quiz);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($Quiz);
            $em->flush();
            return $this->redirectToRoute('app_quiz_list');
        }
        return $this->render('quiz/form.html.twig',[
            'title' => 'Add Quiz',
            'form'=> $form
        ]);
    }
    #[Route('/quiz_edit/{id}', name: 'app_quiz_edit')]
    public function editQuiz($id, Request $request,EntityManagerInterface $em, QuizRepository $QuizRepository){
        $Quiz= $QuizRepository->find($id);
        $form= $this->createForm(AddEditQuizType::class,$Quiz);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //$em->persist($Quiz);
            $em->flush();
            return $this->redirectToRoute('app_quiz_list');
        }
        return $this->render('Quiz/form.html.twig',[
            'title' => 'Update Quiz',
            'form'=> $form
        ]);
    }
    #[Route('/quiz_remove/{id}', name: 'app_quiz_remove')]
    public function removeQuiz($id, QuizRepository $QuizRepository, EntityManagerInterface $em){
        $Quiz= $QuizRepository->find($id);
        $em->remove($Quiz);
        $em->flush();
        return $this->redirectToRoute('app_quiz_list');
        //return new Response('Quiz deleted');
    }
}
