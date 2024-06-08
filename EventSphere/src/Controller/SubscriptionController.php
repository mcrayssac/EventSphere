<?php

namespace App\Controller;

use App\Entity\Event;
use App\Service\SubscriptionService;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends AbstractController
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    #[Route('/event/subscribe/{id}', name: 'event_subscribe')]
    public function subscribe(Event $event, Request $request): Response
    {
        $user = $this->getUser();
        $result = $this->subscriptionService->subscribe($user, $event);

        switch ($result) {
            case 'success':
                $this->addFlash('success', 'You have successfully subscribed to the event.');
                break;
            case 'event_passed':
                // This should never show because we hide the subscribe button if the event has already passed but just in case
                $this->addFlash('error', 'Unable to subscribe to the event. The event has already passed.');
                break;
            case 'max_participants':
                $this->addFlash('error', 'Unable to subscribe to the event. The maximum number of participants has been reached.');
                break;
            case 'already_subscribed':
                // This should never show because we hide the subscribe button if the user is already subscribed but just in case
                $this->addFlash('error', 'Unable to subscribe to the event. You are already subscribed.');
                break;
            default:
                $this->addFlash('error', 'Unable to subscribe to the event. An unknown error occurred.');
                break;
        }

        return $this->redirectToRoute('app_event_detail', ['id' => $event->getId()]);
    }

    #[Route('/event/unsubscribe/{id}', name: 'event_unsubscribe')]
    public function unsubscribe(Event $event, Request $request): Response
    {
        $user = $this->getUser();

        if ($this->subscriptionService->unsubscribe($user, $event)) {
            $this->addFlash('success', 'You have successfully unsubscribed from the event ' . $event->getTitle() . '.');
        } else {
            $this->addFlash('error', 'Unable to unsubscribe from the event. You might not be subscribed.');
        }

        return $this->redirectToRoute('app_event_detail', ['id' => $event->getId()]);
    }

    #[Route('/my-events', name: 'user_events')]
    public function showUserEvents(SubscriptionRepository $subscriptionRepository): Response
    {
        $user = $this->getUser();
        $subscriptions = $subscriptionRepository->findBy(['user' => $user]);
        $events = array_map(fn($subscription) => $subscription->getEvent(), $subscriptions);

        return $this->render('subscription/index.html.twig', [
            'events' => $events,
        ]);
    }
}