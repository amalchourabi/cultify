<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Association;
use App\Form\AddEditAssociationsType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

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
public function addAssociation(Request $req, EntityManagerInterface $em): Response
{
    $association = new Association();
    $form = $this->createForm(AddEditAssociationsType::class, $association);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $mimeType = $imageFile->getMimeType();
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!in_array($mimeType, $allowedTypes)) {
                $this->addFlash('error', 'Le fichier doit être une image (JPEG, PNG ou GIF)');
                return $this->redirectToRoute('app_association_add');
            }

            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                // Déplacez le fichier vers le répertoire de téléchargement
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Enregistrez le nom du fichier dans l'entité
                $association->setImage($newFilename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                return $this->redirectToRoute('app_association_add');
            }
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
public function editAssociation($id, Request $req, EntityManagerInterface $em, AssociationRepository $repo): Response
{
    $association = $repo->find($id);
    if (!$association) {
        throw $this->createNotFoundException('Association non trouvée');
    }

    // Sauvegardez l'ancien nom de fichier
    $oldImageFilename = $association->getImage();

    $form = $this->createForm(AddEditAssociationsType::class, $association);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        // Si un nouveau fichier est téléchargé
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                // Déplacez le nouveau fichier vers le répertoire de téléchargement
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Supprimez l'ancien fichier s'il existe
                if ($oldImageFilename) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImageFilename;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Enregistrez le nouveau nom de fichier dans l'entité
                $association->setImage($newFilename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                return $this->redirectToRoute('app_association_edit', ['id' => $id]);
            }
        }

        $em->flush();
        $this->addFlash('success', 'L\'association a été modifiée avec succès.');
        return $this->redirectToRoute('app_affiche');
    }

    return $this->render('association/edit.html.twig', [
        'form' => $form->createView(),
        'association' => $association,
    ]);
}

    #[Route('/association/delete/{id}', name: 'app_association_delete')]
    public function deleteAssociation($id, EntityManagerInterface $em, AssociationRepository $repo, Filesystem $filesystem): Response
    {
        $association = $repo->find($id);
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }

        $imageFilename = $association->getImage();
        if ($imageFilename) {
            $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
        }

        $em->remove($association);
        $em->flush();

        return $this->redirectToRoute('app_affiche');
    }
    #[Route('/association/qrcode/{id}', name: 'app_association_qrcode')]
public function generateQrCode($id, AssociationRepository $repo): Response
{
    $association = $repo->find($id);
    if (!$association) {
        throw $this->createNotFoundException('Association non trouvée');
    }

    $siteWeb = $association->getSiteWeb();
    if (empty($siteWeb)) {
        throw $this->createNotFoundException('Aucun site web disponible pour cette association');
    }

    // Générer le QR code avec le site web de l'association
    $qrCodeContent = $siteWeb; // Le contenu du QR code est l'URL du site web
    $qrCode = new QrCode($qrCodeContent);
    $writer = new PngWriter();
    $qrCodeResult = $writer->write($qrCode);
    $qrCodeImage = $qrCodeResult->getString();

    // Retourner l'image du QR code en tant que réponse
    return new Response($qrCodeImage, 200, ['Content-Type' => 'image/png']);
}


#[Route('/associations', name: 'app_affiche')]
public function afficheAssociation(AssociationRepository $repo, UserRepository $userRepository, Request $request): Response
{
    // Récupérer l'utilisateur connecté depuis la session
    $session = $request->getSession();
    $userId = $session->get('user_id');
    $user = $userRepository->find($userId);

    // Récupérer le terme de recherche depuis la requête
    $searchTerm = $request->query->get('search', '');

    // Récupérer les associations filtrées par terme de recherche
    $associations = $repo->findBySearchTerm($searchTerm);

    // Créer un adaptateur Pagerfanta pour paginer les résultats
    $adapter = new ArrayAdapter($associations);
    $pagerfanta = new Pagerfanta($adapter);

    // Définir la page actuelle (par défaut 1)
    $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

    // Définir le nombre maximum d'éléments par page (ici 10)
    $pagerfanta->setMaxPerPage(10);

    // Si l'utilisateur est un admin, afficher la vue admin
    if ($user && $user->getRole() === 'admin') {
        return $this->render('association/afficher.html.twig', [
            'pager' => $pagerfanta, // Passer l'objet Pagerfanta au template
            'searchTerm' => $searchTerm, // Passer le terme de recherche pour l'affichage
        ]);
    } 
    // Sinon, afficher la vue utilisateur
    else {
        // Si l'utilisateur est un organisateur, calculer sa contribution
        if ($user && $user->getRole() === 'organisateur') {
            $userRepository->calculerContribution($user);
        }

        return $this->render('association/affiche_user.html.twig', [
            'pager' => $pagerfanta, // Passer l'objet Pagerfanta au template
            'user' => $user, // Passer l'utilisateur connecté
            'searchTerm' => $searchTerm, // Passer le terme de recherche pour l'affichage
        ]);
    }
}
    #[Route('/associations/search', name: 'app_association_search', methods: ['GET'])]
    public function searchAssociations(Request $request, AssociationRepository $repo): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');
        $associations = $repo->findBySearchTerm($searchTerm);
    
        // Formater les résultats pour JSON
        $results = [];
        foreach ($associations as $association) {
            $results[] = [
                'id' => $association->getId(),
                'nom' => $association->getNom(),
                'description' => $association->getDescription(),
                'contact' => $association->getContact(),
                'but' => $association->getBut(),
                'image' => $association->getImage(),
                'montantDesire' => $association->getMontantDesire(),
                'pourcentageProgression' => $association->getPourcentageProgression(),
                'siteWeb' => $association->getSiteWeb(),
            'qrCodeUrl' => $this->generateUrl('app_association_qrcode', ['id' => $association->getId()]),
            ];
        }
    
        return new JsonResponse($results);
    }

    #[Route('/associationsUser', name: 'app_afficher')]
public function afficheAssociationUSer(AssociationRepository $repo, UserRepository $userRepository, Request $request): Response
{
     // Récupérer l'utilisateur connecté depuis la session
     $session = $request->getSession();
     $userId = $session->get('user_id');
     $user = $userRepository->find($userId);
 
     // Récupérer le terme de recherche depuis la requête
     $searchTerm = $request->query->get('search', '');
 
     // Récupérer les associations filtrées par terme de recherche
     $associations = $repo->findBySearchTerm($searchTerm);
 
     // Créer un adaptateur Pagerfanta pour paginer les résultats
     $adapter = new ArrayAdapter($associations);
     $pagerfanta = new Pagerfanta($adapter);
 
     // Définir la page actuelle (par défaut 1)
     $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
 
     // Définir le nombre maximum d'éléments par page (ici 10)
     $pagerfanta->setMaxPerPage(10);
 
     // Si l'utilisateur est un admin, afficher la vue admin
     
         // Si l'utilisateur est un organisateur, calculer sa contribution
         if ($user && $user->getRole() === 'organisateur') {
             $userRepository->calculerContribution($user);
         }
 
         return $this->render('association/affiche_user.html.twig', [
             'pager' => $pagerfanta, // Passer l'objet Pagerfanta au template
             'user' => $user, // Passer l'utilisateur connecté
             'searchTerm' => $searchTerm, // Passer le terme de recherche pour l'affichage
         ]);
     
}
#[Route('/associationsAdmin', name: 'app_afficheradmin')]
public function afficheAssociationAdmin(AssociationRepository $repo, UserRepository $userRepository, Request $request): Response
{
     // Récupérer l'utilisateur connecté depuis la session
     $session = $request->getSession();
     $userId = $session->get('user_id');
     $user = $userRepository->find($userId);
 
     // Récupérer le terme de recherche depuis la requête
     $searchTerm = $request->query->get('search', '');
 
     // Récupérer les associations filtrées par terme de recherche
     $associations = $repo->findBySearchTerm($searchTerm);
 
     // Créer un adaptateur Pagerfanta pour paginer les résultats
     $adapter = new ArrayAdapter($associations);
     $pagerfanta = new Pagerfanta($adapter);
 
     // Définir la page actuelle (par défaut 1)
     $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
 
     // Définir le nombre maximum d'éléments par page (ici 10)
     $pagerfanta->setMaxPerPage(10);
 
     // Si l'utilisateur est un admin, afficher la vue admin

         return $this->render('association/afficher.html.twig', [
             'pager' => $pagerfanta, // Passer l'objet Pagerfanta au template
             'searchTerm' => $searchTerm, // Passer le terme de recherche pour l'affichage
         ]);
     
     
}
    
}