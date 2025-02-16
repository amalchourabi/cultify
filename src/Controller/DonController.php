<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AssociationRepository;
use App\Entity\Don;
use App\Form\DonType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DonController extends AbstractController
{
    #[Route('/don', name: 'app_don_index')]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');

    if (!$userId) {
        $this->addFlash('error', 'Vous devez être connecté pour voir vos dons.');
        return $this->redirectToRoute('app_simulate_user');
    }

    // Récupérer les dons de l'utilisateur
    $dons = $entityManager->getRepository(Don::class)->findBy(['idUser' => $userId]);

    return $this->render('don/index.html.twig', [
        'dons' => $dons,
    ]);
}
#[Route('/don/new/{id}', name: 'app_don_new')]
public function new(Request $request,EntityManagerInterface $entityManager,AssociationRepository $associationRepository,UserRepository $userRepository, int $id): Response 
{
    // Récupérer l'association sélectionnée
    $association = $associationRepository->find($id);
    if (!$association) {
        throw $this->createNotFoundException('Association not found');
    }

    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
    $userRole = $session->get('user_role');

    if (!$userId || !$userRole) {
        $this->addFlash('error', 'Vous devez être connecté pour faire un don.');
        return $this->redirectToRoute('app_simulate_user');
    }

    // Récupérer l'objet User à partir de l'ID
    $user = $userRepository->find($userId);
    if (!$user) {
        $this->addFlash('error', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('app_simulate_user');
    }

    // Créer un nouveau don
    $don = new Don();
    $don->setAssociation($association);
    $don->setStatus('en_attente');
    $don->setIdUser($user); // Utiliser l'objet User
    $don->setDonorType($userRole); // Définir le donorType à partir du user_role
    if($user->getRole()=="organisateur"&&$user->getMontantAPayer()>0){
        $don->setType("contribution");
    }else{
        $don->setType("don");
    }

    // Créer le formulaire
    $form = $this->createForm(DonType::class, $don);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($don);
        $entityManager->flush();

        $this->addFlash('success', 'Votre don a été enregistré avec succès.');
        return $this->redirectToRoute('app_don_index');
    }

    return $this->render('don/new.html.twig', [
        'form' => $form->createView(),
        'association' => $association,
    ]);
}
#[Route('/don/edit/{id}', name: 'app_don_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, Don $don): Response
{
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
    
    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce don.');
        return $this->redirectToRoute('app_don_index'); // Redirection ici
    }

    // Créer le formulaire (mode édition)
    $form = $this->createForm(DonType::class, $don, [
        'is_edit' => true, // Mode édition
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Le don a été modifié avec succès.');
        return $this->redirectToRoute('app_don_index');
    }

    return $this->render('don/edit.html.twig', [
        'form' => $form->createView(),
        'don' => $don,
    ]);
}

#[Route('/don/delete/{id}', name: 'app_don_delete')]
public function delete(Request $request, EntityManagerInterface $entityManager, Don $don): Response
{
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');

    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce don.');
        return $this->redirectToRoute('app_don_index');
    }

    // Supprimer le don
    $entityManager->remove($don);
    $entityManager->flush();

    $this->addFlash('success', 'Le don a été supprimé avec succès.');

    return $this->redirectToRoute('app_don_index');
}
#[Route('/don/confirm/{id}', name: 'app_don_confirm')]
public function confirm(Request $request, EntityManagerInterface $entityManager, Don $don): Response
{
    // Récupérer l'utilisateur simulé depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');

    // Vérifier que l'utilisateur est le propriétaire du don
    if ($don->getIdUser()->getId() !== $userId) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à confirmer ce don.');
        return $this->redirectToRoute('app_don_index');
    }

    // Confirmer le don
    $don->setStatus('confirme');
    $entityManager->flush();

    $this->addFlash('success', 'Le don a été confirmé avec succès.');
    return $this->redirectToRoute('app_don_index');
}
}
