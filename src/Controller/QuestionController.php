<?php

namespace App\Controller;
use App\Entity\Question;
use App\Form\AddEditQuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionController extends AbstractController
{
    #[Route('/question', name: 'app_question')]
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }
    #[Route('/question/list', name: 'app_question_list')]
    public function listQuestion(QuestionRepository $QuestionRepository){
        $QuestionDB= $QuestionRepository->findAll();
        return $this->render('question/list.html.twig',[
            'Questions' => $QuestionDB
        ]);
    }
    #[Route('/question/new', name: 'app_question_new')]
    public function newQuestion(Request $request,EntityManagerInterface $em){
        $Question= new Question();
        $form= $this->createForm(AddEditQuestionType::class,$Question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($Question);
            $em->flush();
            return $this->redirectToRoute('app_question_list');
        }
        return $this->render('question/form.html.twig',[
            'title' => 'Add Question',
            'form'=> $form
        ]);
    }
    #[Route('/question_edit/{id}', name: 'app_question_edit')]
    public function editQuestion($id, Request $request,EntityManagerInterface $em, QuestionRepository $QuestionRepository){
        $Question= $QuestionRepository->find($id);
        $form= $this->createForm(AddEditQuestionType::class,$Question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //$em->persist($Question);
            $em->flush();
            return $this->redirectToRoute('app_question_list');
        }
        return $this->render('Question/form.html.twig',[
            'title' => 'Update Question',
            'form'=> $form
        ]);
    }
    #[Route('/question_remove/{id}', name: 'app_question_remove')]
    public function removeQuestion($id, QuestionRepository $QuestionRepository, EntityManagerInterface $em){
        $Question= $QuestionRepository->find($id);
        $em->remove($Question);
        $em->flush();
        return $this->redirectToRoute('app_question_list');
        //return new Response('Question deleted');
    }
}
