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
use Psr\Log\LoggerInterface;

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
    public function addAssociation(Request $req, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter une association.');
            return $this->redirectToRoute('app_login');
        }

        $association = new Association();
        $form = $this->createForm(AddEditAssociationsType::class, $association);
        $form->handleRequest($req);

        $logger->info('Formulaire soumis : ' . ($form->isSubmitted() ? 'Oui' : 'Non'));
        if ($form->isSubmitted()) {
            $logger->info('Formulaire valide : ' . ($form->isValid() ? 'Oui' : 'Non'));
            if ($form->isValid()) {
                $imageFile = $form->get('image')->getData();
                $logger->info('Image présente : ' . ($imageFile ? 'Oui' : 'Non'));

                if ($imageFile) {
                    $mimeType = $imageFile->getMimeType();
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $logger->info('Type MIME : ' . $mimeType);

                    if (!in_array($mimeType, $allowedTypes)) {
                        $this->addFlash('error', 'Le fichier doit être une image (JPEG, PNG ou GIF)');
                        $logger->warning('Type MIME invalide : ' . $mimeType);
                        return $this->render('association/add.html.twig', [
                            'form' => $form->createView(),
                            'title' => 'Ajouter une association',
                        ]);
                    }

                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                    $logger->info('Nouveau nom de fichier : ' . $newFilename);

                    try {
                        $imageFile->move(
                            $this->getParameter('associations_images_directory'),
                            $newFilename
                        );
                        $association->setImage($newFilename);
                    } catch (\Exception $e) {
                        $logger->error('Erreur lors du déplacement de l\'image : ' . $e->getMessage());
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
                        return $this->render('association/add.html.twig', [
                            'form' => $form->createView(),
                            'title' => 'Ajouter une association',
                        ]);
                    }
                }

                $em->persist($association);
                $em->flush();
                $logger->info('Association ajoutée avec ID : ' . $association->getId());

                $this->addFlash('success', 'L\'association a été ajoutée avec succès.');
                return $this->redirectToRoute('app_afficheradmin');
            } else {
                $errors = $form->getErrors(true);
                $errorMessage = $errors->count() > 0 ? $errors->__toString() : 'Données invalides';
                $logger->error('Erreurs du formulaire : ' . $errorMessage);
                $this->addFlash('error', 'Le formulaire contient des erreurs : ' . $errorMessage);
            }
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

        $oldImageFilename = $association->getImage();
        $form = $this->createForm(AddEditAssociationsType::class, $association);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('associations_images_directory'),
                        $newFilename
                    );

                    if ($oldImageFilename) {
                        $oldImagePath = $this->getParameter('associations_images_directory') . '/' . $oldImageFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $association->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_association_edit', ['id' => $id]);
                }
            }

            $em->flush();
            $this->addFlash('success', 'L\'association a été modifiée avec succès.');
            return $this->redirectToRoute('app_afficheradmin');
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
            $imagePath = $this->getParameter('associations_images_directory') . '/' . $imageFilename;
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
        }

        $em->remove($association);
        $em->flush();

        return $this->redirectToRoute('app_afficheradmin');
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

        $qrCodeContent = $siteWeb;
        $qrCode = new QrCode($qrCodeContent);
        $writer = new PngWriter();
        $qrCodeResult = $writer->write($qrCode);
        $qrCodeImage = $qrCodeResult->getString();

        return new Response($qrCodeImage, 200, ['Content-Type' => 'image/png']);
    }

    #[Route('/associations', name: 'app_affiche')]
    public function afficheAssociation(AssociationRepository $repo, Request $request): Response
    {
        $user = $this->getUser();
        $searchTerm = $request->query->get('search', '');
        $associations = $repo->findBySearchTerm($searchTerm);

        $adapter = new ArrayAdapter($associations);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        $pagerfanta->setMaxPerPage(10);

        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->render('association/afficher.html.twig', [
                'pager' => $pagerfanta,
                'searchTerm' => $searchTerm,
            ]);
        } else {
            return $this->render('association/affiche_user.html.twig', [
                'pager' => $pagerfanta,
                'user' => $user,
                'searchTerm' => $searchTerm,
            ]);
        }
    }

    #[Route('/associations/search', name: 'app_association_search', methods: ['GET'])]
    public function searchAssociations(Request $request, AssociationRepository $repo): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');
        $associations = $repo->findBySearchTerm($searchTerm);

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
    public function afficheAssociationUSer(AssociationRepository $repo, Request $request): Response
    {
        $user = $this->getUser();
        $searchTerm = $request->query->get('search', '');
        $allAssociations = $repo->findBySearchTerm($searchTerm);

        // Filtrer pour n'afficher que les associations avec pourcentage < 100
        $associations = array_filter($allAssociations, function ($association) {
            return $association->getPourcentageProgression() < 100;
        });
        $adapter = new ArrayAdapter($associations);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        $pagerfanta->setMaxPerPage(10);

        return $this->render('association/affiche_user.html.twig', [
            'pager' => $pagerfanta,
            'user' => $user,
            'searchTerm' => $searchTerm,
        ]);
    }
    #[Route('/associationsUser/search', name: 'app_association_user_search', methods: ['GET'])]
    public function searchAssociationsUser(Request $request, AssociationRepository $repo): JsonResponse
    {
        $searchTerm = $request->query->get('search', '');
        $allAssociations = $repo->findBySearchTerm($searchTerm);

        // Filtrer pour n'inclure que les associations avec pourcentage < 100
        $associations = array_filter($allAssociations, function ($association) {
            return $association->getPourcentageProgression() < 100;
        });

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

    #[Route('/associationsAdmin', name: 'app_afficheradmin')]
    public function afficheAssociationAdmin(AssociationRepository $repo, Request $request): Response
    {
        $user = $this->getUser();
        $searchTerm = $request->query->get('search', '');
        $associations = $repo->findBySearchTerm($searchTerm);

        $adapter = new ArrayAdapter($associations);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        $pagerfanta->setMaxPerPage(10);

        return $this->render('association/afficher.html.twig', [
            'pager' => $pagerfanta,
            'searchTerm' => $searchTerm,
        ]);
    }
}