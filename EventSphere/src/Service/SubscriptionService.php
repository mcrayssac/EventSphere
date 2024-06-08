<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
    private $subscriptionRepository;
    private $entityManager;

    public function __construct(SubscriptionRepository $subscriptionRepository,EntityManagerInterface $entityManager)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    public function subscribe(User $user, Event $event): string
    {
        $now = new \DateTime();

        // Check if the event has already passed
        if ($event->getDateTime() < $now) {
            return 'event_passed';
        }

        // Check if the event has reached its max participants
        if (count($event->getSubscriptions()) >= $event->getMaxParticipants()) {
            return 'max_participants';
        }

        // Check if the user is already subscribed to the event
        foreach ($event->getSubscriptions() as $subscription) {
            if ($subscription->getUser() === $user) {
                return 'already_subscribed';
            }
        }

        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setEvent($event);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return 'success';
    }

    public function unsubscribe(User $user, Event $event): bool
    {
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user, 'event' => $event]);

        if (!$subscription) {
            return false;
        }

        $this->entityManager->remove($subscription);
        $this->entityManager->flush();

        return true;
    }
}
