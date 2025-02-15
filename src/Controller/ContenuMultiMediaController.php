<?php

namespace App\Controller;

use App\Entity\ContenuMultiMedia;
use App\Form\AddEditContenuMultiMediaType;
use App\Repository\ContenuMultiMediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ContenuMultiMediaController extends AbstractController
{
    #[Route('/contenu/multi/media', name: 'app_contenu_multi_media')]
    public function index(): Response
    {
        return $this->render('contenu_multi_media/index.html.twig', [
            'controller_name' => 'ContenuMultiMediaController',
        ]);
    }
    #[Route('/contenu_multi_media/list', name: 'app_contenu_multi_media_list')]
    public function listContenuMultiMedia(contenuMultiMediaRepository $ContenuMultiMediaRepository){
        $ContenuMultiMediaDB= $ContenuMultiMediaRepository->findAll();
        return $this->render('contenu_multi_media/list.html.twig',[
            'ContenusMultiMedia' => $ContenuMultiMediaDB
        ]);
    }
    #[Route('/contenu_multi_media/new', name: 'app_contenu_multi_media_new')]
    public function newContenuMultiMedia(Request $request,EntityManagerInterface $em){
        $ContenuMultiMedia= new ContenuMultiMedia();
        $form= $this->createForm(AddEditContenuMultiMediaType::class,$ContenuMultiMedia);
        $form->handleRequest($request);
        dump($request->request->all());
        if($form->isSubmitted() && $form->isValid()){
            $photoFile = $form->get('photoMedia')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                try {
                    $photoFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $ContenuMultiMedia->setPhotoMedia($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload du fichier.');
                }
            }
            $em->persist($ContenuMultiMedia);
            $em->flush();
            return $this->redirectToRoute('app_contenu_multi_media_list');
        }
        return $this->render('contenu_multi_media/form.html.twig',[
            'title' => 'Add ContenuMultiMedia',
            'form'=> $form->createView(),
        ]);
    }
    #[Route('/contenu_multi_media_edit/{id}', name: 'app_contenu_multi_media_edit')]
    public function editContenuMultiMedia($id, Request $request,EntityManagerInterface $em, contenuMultiMediaRepository $contenuMultiMediaRepository){
        $ContenuMultiMedia= $contenuMultiMediaRepository->find($id);
        $form= $this->createForm(AddEditContenuMultiMediaType::class,$ContenuMultiMedia);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //$em->persist($ContenuMultiMedia);
            $em->flush();
            return $this->redirectToRoute('app_contenu_multi_media_list');
        }
        return $this->render('contenu_multi_media/form.html.twig',[
            'title' => 'Update ContenuMultiMedia',
            'form'=> $form
        ]);
    }
    #[Route('/contenu_multi_media_remove/{id}', name: 'app_contenu_multi_media_remove')]
    public function removeContenuMultiMedia($id, contenuMultiMediaRepository $contenuMultiMediaRepository, EntityManagerInterface $em){
        $ContenuMultiMedia= $contenuMultiMediaRepository->find($id);
        $em->remove($ContenuMultiMedia);
        $em->flush();
        return $this->redirectToRoute('app_contenu_multi_media_list');
        //return new Response('ContenuMultiMedia deleted');
    }
    
}