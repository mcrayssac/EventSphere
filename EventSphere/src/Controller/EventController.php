<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{
    private $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    #[Route('/event/create', name: 'event_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $event->setCreator($user);
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

        $date = $request->query->get('date');
        $remainingPlaces = $request->query->get('remainingPlaces');
        $isPublic = $request->query->get('isPublic');
        $search = $request->query->get('search');

        $queryBuilder = $eventRepository->createQueryBuilder('e')
            ->orderBy('e.dateTime', 'DESC');

        if (!$isAuthenticated) {
            $queryBuilder->andWhere('e.isPublic = :isPublic')
                ->setParameter('isPublic', true);
        }

        if (!empty($date)) {
            $queryBuilder->andWhere('e.dateTime >= :date')
                ->setParameter('date', new \DateTime($date));
        }

        if (!empty($remainingPlaces)) {
            $queryBuilder->andWhere('e.id IN (:eventsWithRemainingPlaces)')
                ->setParameter('eventsWithRemainingPlaces', $this->eventService->filterEventsByRemainingPlaces($remainingPlaces));
        }

        if ($isPublic !== null && $isPublic !== '') {
            $queryBuilder->andWhere('e.isPublic = :isPublicFilter')
                ->setParameter('isPublicFilter', $isPublic);
        }

        if (!empty($search)) {
            $queryBuilder->andWhere('e.title LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Number of items per page
        );

        foreach ($pagination as $event) {
            $event->remainingPlaces = $this->eventService->getRemainingPlaces($event);
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
        $remainingPlaces = $this->eventService->getRemainingPlaces($event);

        return $this->render('event/detail.html.twig', [
            'event' => $event,
            'isSubscribed' => $isSubscribed,
            'remainingPlaces' => $remainingPlaces,
            'currentTime' => $currentTime,
        ]);
    }

    #[Route('/manage/events', name: 'manage_events')]
    public function manageEvents(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $queryBuilder = $eventRepository->createQueryBuilder('e')
            ->where('e.creator = :creator')
            ->setParameter('creator', $user)
            ->orderBy('e.dateTime', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Number of items per page
        );

        foreach ($pagination as $event) {
            $event->remainingPlaces = $this->eventService->getRemainingPlaces($event);
        }

        return $this->render('event/manage.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/event/edit/{id}', name: 'event_edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('edit', $event);

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Event updated successfully.');

            return $this->redirectToRoute('manage_events');
        }

        return $this->render('event/edit.html.twig', [
            'eventForm' => $form->createView(),
        ]);
    }


    #[Route('/event/delete/{id}', name: 'event_delete', methods: ['POST'])]
    public function delete(Event $event, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete', $event);

        // Validate the CSRF token
        if (!$this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token.']);
        }

        $entityManager->remove($event);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Event deleted successfully.']);
    }


}

