<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Association;
use App\Form\AddEditAssociationsType as AddUpdate;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AssociationController extends AbstractController
{
    #[Route('/association', name: 'app_association')]
    public function index(): Response
    {
        return $this->render('association/index.html.twig', [
            'controller_name' => 'AssociationController',
        ]);
    }
    #[Route('/association/add', name: 'app_association_add')]
    public function addAssociation(Request $req,EntityManagerInterface $em){
        
    
        $association = new Association();
        $form = $this->createForm(AddUpdate::class, $association);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Déplacez le fichier dans le répertoire où les images sont stockées
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Mettez à jour le champ image de l'entité Association
                $association->setImage($newFilename);
            }
            $em->persist($association);
            $em->flush();

            $this->addFlash('success', 'L\'association a été ajoutée avec succès.');

            return $this->redirectToRoute('app_affiche');
        }

        return $this->render('association/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter une association',
        ]);

    }
    #[Route('/association/edit/{id}', name: 'app_association_edit')]
    public function editAssociation($id,Request $req,EntityManagerInterface $em,AssociationRepository $repo){
        $association=$repo->find($id);
        if(!$association){
            throw $this->createNotFoundException('Association non trouvée');
        }
        $form=$this->createForm(AddUpdate::class,$association);
        
        $form->handleRequest($req);
        if($form->isSubmitted()&&$form->isValid())
        {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacez le fichier dans le répertoire où les images sont stockées
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
    
                // Mettez à jour le champ image de l'entité Association
                $association->setImage($newFilename);
            }
            $em->flush();
            $this->addFlash('success', 'L\'association a été modifiée avec succès.');
            return $this->redirectToRoute('app_affiche');
        }
        return $this->render('association/edit.html.twig',[
            'form'=>$form->createView(),
            'association'=>$association
        ]);

    }
    #[Route('/association/delete/{id}', name: 'app_association_delete')]
    public function deleteAssociation($id, EntityManagerInterface $em, AssociationRepository $repo, Filesystem $filesystem)
    {
        // Récupérer l'association
        $association = $repo->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }

         // Supprimer l'image associée si elle existe
        $imageFilename = $association->getImage(); // Assurez-vous que cette méthode existe dans votre entité
        if ($imageFilename) {
            $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath); // Supprime le fichier image
            }
        }

        // Supprimer l'association de la base de données
        $em->remove($association);
        $em->flush();

        // Rediriger vers la liste des associations
        return $this->redirectToRoute('app_affiche');
    }
    #[Route('/associations', name: 'app_affiche')]
    public function afficheAssociation(AssociationRepository $repo) 
    {
        
        $asociations=$repo->findAll();
        return $this->render('association/afficher.html.twig', [    'associations' => $asociations,]);
    }
    #[Route('/associationsUser', name: 'app_afficher')]
    public function afficheAssociationUSer(AssociationRepository $repo,UserRepository $userRepository,Request $request) 
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');
        $user = $userRepository->find($userId);

        // Si l'utilisateur est un organisateur, calculer sa contribution
        if ($user && $user->getRole() === 'organisateur') {
            $userRepository->calculerContribution($user);
        }
        $asociations=$repo->findAll();
        return $this->render('association/affiche_user.html.twig', [    'associations' => $asociations,'user'=>$user]);
    }
}

