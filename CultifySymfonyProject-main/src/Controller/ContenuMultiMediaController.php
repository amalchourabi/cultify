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
use Dompdf\Dompdf;
use Dompdf\Options;
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
public function listContenuMultiMedia(ContenuMultiMediaRepository $ContenuMultiMediaRepository): Response
{
    $contenus = $ContenuMultiMediaRepository->findAll();
    return $this->render('contenu_multi_media/list.html.twig', [
        'contenus' => $contenus  // Corrigé ici
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
    #[Route('/contenu_multi_media/search', name: 'app_contenu_multi_media_search')]
    public function search(Request $request, ContenuMultiMediaRepository $contenuMultiMediaRepository): Response
    {
        $query = $request->query->get('q');
        $contenus = $contenuMultiMediaRepository->findByTitle($query);
        return $this->render('contenu_multi_media/list.html.twig', [
            'contenus' => $contenus
        ]);
    }

    #[Route('/contenu_multi_media/pdf/{idContenu}', name: 'app_contenu_multi_media_pdf')]

    public function generatePdf(int $idContenu, ContenuMultiMediaRepository $contenuMultiMediaRepository): Response
    {
        $contenu = $contenuMultiMediaRepository->find($idContenu);
    
        if (!$contenu) {
            throw $this->createNotFoundException("Contenu non trouvé !");
        }
    
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/image/' . $contenu->getPhotoMedia();
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageBase64 = 'data:image/' . $imageType . ';base64,' . $imageData;
        $logoPath = $this->getParameter('kernel.project_dir') . '/public/logo.png';
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . $logoData;
        $html = $this->renderView('contenu_multi_media/pdf.html.twig', [
            'contenu' => $contenu,
            'imageBase64' => $imageBase64,
            'logoBase64' => $logoBase64

        ]);
    
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="contenus_multimedia.pdf"',
        ]);
    }
    
    #[Route('/contenu_multi_media/sort', name: 'app_contenu_multi_media_sort')]
    public function sort(ContenuMultiMediaRepository $repository): Response
    {
        $contenus = $repository->findBy([], ['DateMedia' => 'DESC']); // Exemple de tri
    
        return $this->render('contenu_multi_media/list.html.twig', [
            'contenus' => $contenus
        ]);
    }
    #[Route('/contenu_multi_media/statistics', name: 'app_contenu_multi_media_statistics', methods: ['GET'])]
public function statistics(ContenuMultiMediaRepository $contenuMultiMediaRepository): Response
{
    $stats = $contenuMultiMediaRepository->countContenusByCategory();

    return $this->render('contenu_multi_media/statistics.html.twig', [
        'stats' => $stats,
    ]);
}
    
}