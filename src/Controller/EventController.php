<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\EventType;
use App\Form\ReservationType; // Assurez-vous d'importer ReservationType
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager
            ->getRepository(Event::class)
            ->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id_e}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
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
            'form' => $form,
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
            'event' => $event,  // Passez l'événement au formulaire
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'événement n'est pas déjà défini dans la réservation
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
}
