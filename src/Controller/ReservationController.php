<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Event;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route('/test', name: 'app_reservation_test', methods: ['GET'])]
public function testFindAll(EntityManagerInterface $entityManager): Response
{
    $reservations = $entityManager->getRepository(Reservation::class)->findAll();

    return $this->render('reservation/test.html.twig', [
        'reservations' => $reservations,
    ]);
}

    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new/{id_e}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(int $id_e, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Find the event by ID
        $event = $entityManager->getRepository(Event::class)->find($id_e);
        
        // Handle case where event is not found
        if (!$event) {
            throw $this->createNotFoundException("L'événement avec l'ID $id_e n'existe pas.");
        }

        // Create new Reservation object
        $reservation = new Reservation();
        $reservation->setEvent($event); // Associate the event with the reservation

        // Create the form
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the reservation and save to database
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Redirect to reservations index page
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        // Render the form for new reservation
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
            'event' => $event,  // Pass the event to the view
        ]);
    }

    #[Route('/{id_r<\d+>}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(int $id_r, EntityManagerInterface $entityManager): Response
    {
        // Find reservation by ID
        $reservation = $entityManager->getRepository(Reservation::class)->find($id_r);

        // Handle case where reservation is not found
        if (!$reservation) {
            throw $this->createNotFoundException("La réservation avec l'ID $id_r n'a pas été trouvée.");
        }

        // Render the reservation details page
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    

    #[Route('/{id_r}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    { 
        // Create the form for editing the reservation
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the changes to the database
            $entityManager->flush();

            // Redirect to reservations index page
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        // Render the form for editing reservation
        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_r}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Check CSRF token validity
        if ($this->isCsrfTokenValid('delete' . $reservation->getIdR(), $request->request->get('_token'))) {
            // Remove the reservation from the database
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        // Redirect to reservations index page
        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
