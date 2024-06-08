<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\SubscriptionRepository;

class EventService
{
    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getRemainingPlaces(Event $event): int
    {
        $subscriptions = $this->subscriptionRepository->findBy(['event' => $event]);
        $numberOfSubscriptions = count($subscriptions);

        return $event->getMaxParticipants() - $numberOfSubscriptions;
    }
}
