<?php

namespace App\Controller;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Form\AddEditQuizType;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
  

    #[Route('/quiz/submit/{id}', name: 'app_quiz_submit', methods: ['POST'])]
    public function submitQuiz(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        // Récupérer les réponses soumises par l'utilisateur
        $userResponses = $request->request->all()['responses'] ?? [];
    
        // Sérialiser les réponses en JSON
        $reponsesChoisit = json_encode($userResponses);
    
        // Stocker les réponses dans ReponseChoisit
        $quiz->setReponseChoisit($reponsesChoisit);
    
        // Calculer le score
        $score = $quiz->calculateScore();
        $quiz->setScoreQuiz($score);
    
        // Enregistrer les modifications en base de données
        $em->flush();
    
        // Rediriger vers la page des détails du quiz
        return $this->redirectToRoute('app_quiz_details', ['id' => $quiz->getIdQuiz()]);
    }
  
    #[Route('/quiz/details/{id}', name: 'app_quiz_details')]
    public function quizDetails($id, QuizRepository $quizRepository): Response
    {
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException("Quiz non trouvé !");
        }
        return $this->render('quiz/details.html.twig', [
            'quiz' => $quiz
        ]);
    }
}
