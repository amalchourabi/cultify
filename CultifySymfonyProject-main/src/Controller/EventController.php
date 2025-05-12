<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\EventType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use App\Repository\EventRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $query = $request->query->get('q');
        $currentPage = $request->query->getInt('page', 1);
        $limit = 10;

        if ($query) {
            $paginator = $eventRepository->findBySearchQuery($query, $currentPage, $limit);
        } else {
            $paginator = $eventRepository->findPaginated($currentPage, $limit);
        }

        $events = $paginator->getIterator();
        $totalEvents = $paginator->count();
        $totalPages = ceil($totalEvents / $limit);

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'query' => $query,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MessageBusInterface $bus): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $mimeType = $imageFile->getMimeType();
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!in_array($mimeType, $allowedTypes)) {
                    $this->addFlash('error', 'Le fichier doit être une image (JPEG, PNG ou GIF)');
                    return $this->redirectToRoute('app_event_new');
                }

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $event->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_event_new');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $email = (new Email())
                ->from('mailtrap@demomailtrap.com')
                ->to('amalchourabi203@gmail.com')
                ->subject('Nouvel événement créé')
                ->text('Un nouvel événement a été créé avec succès')
                ->html('<p>Un nouvel événement a été créé avec succès.</p>');

            $bus->dispatch(new SendEmailMessage($email));

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id_e<\d+>}', name: 'app_event_show', methods: ['GET'])]
    public function show(int $id_e, EventRepository $eventRepository): Response
    {
        // Recherche de l'événement par ID
        $event = $eventRepository->find($id_e);
    
        // Si l'événement n'existe pas, on lance une exception 404
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }
    
        // Si l'entité existe, on la passe au template
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id_e}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_e}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getIdE(), $request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id_e}/reservation/new', name: 'app_event_reservation_new', methods: ['GET', 'POST'])]
    public function newReservation(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'event' => $event,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($reservation->getEvent() === null) {
                $reservation->setEvent($event);
            }

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['id_e' => $event->getIdE()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/reservation_new.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }
    #[Route('/statistics', name: 'app_event_statistics', methods: ['GET'])]
public function statistics(EventRepository $eventRepository): Response
{
    $stats = $eventRepository->countEventsByCategory();

    return $this->render('event/statistics.html.twig', [
        'stats' => $stats,
    ]);
}
}