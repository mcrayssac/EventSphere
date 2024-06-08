<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{
    #[Route('/event/create', name: 'event_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Event created successfully.');

            return $this->redirectToRoute('event_create');
        }

        return $this->render('event/create.html.twig', [
            'eventForm' => $form->createView(),
        ]);
    }

    #[Route('/events', name: 'app_events')]
    public function list(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request, Security $security): Response
    {
        $isAuthenticated = $security->isGranted('IS_AUTHENTICATED_FULLY');

        $queryBuilder = $eventRepository->createQueryBuilder('e')
            ->orderBy('e.dateTime', 'DESC');

        if (!$isAuthenticated) {
            $queryBuilder->where('e.isPublic = :isPublic')
                ->setParameter('isPublic', true);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Number of items per page
        );

        foreach ($pagination as $event) {
            $event->remainingPlaces = $event->getRemainingPlaces();
        }

        return $this->render('event/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/event/{id}', name: 'app_event_detail')]
    public function show(int $id, EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        $event = $eventRepository->find($id);
        $currentTime = new \DateTime();

        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }

        $this->denyAccessUnlessGranted('view', $event);

        $user = $this->getUser();
        $isSubscribed = $subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);
        $remainingPlaces = $event->getRemainingPlaces();

        return $this->render('event/detail.html.twig', [
            'event' => $event,
            'isSubscribed' => $isSubscribed,
            'remainingPlaces' => $remainingPlaces,
            'currentTime' => $currentTime,
        ]);
    }
}

