<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\SubscriptionRepository;
use App\Repository\EventRepository;

class EventService
{
    private $subscriptionRepository;
    private $eventRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, EventRepository $eventRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->eventRepository = $eventRepository;
    }

    public function getRemainingPlaces(Event $event): int
    {
        $subscriptions = $this->subscriptionRepository->findBy(['event' => $event]);
        $numberOfSubscriptions = count($subscriptions);

        return $event->getMaxParticipants() - $numberOfSubscriptions;
    }

    public function filterEventsByRemainingPlaces(int $remainingPlaces): array
    {
        $events = $this->eventRepository->findAll();
        $filteredEvents = [];

        foreach ($events as $event) {
            if ($this->getRemainingPlaces($event) >= $remainingPlaces) {
                $filteredEvents[] = $event->getId();
            }
        }

        return $filteredEvents;
    }
}
