<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $events = [
            [
                'title' => 'Event 1',
                'description' => 'Description for event 1',
                'dateTime' => new \DateTime('+1 day'),
                'maxParticipants' => 50,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 2',
                'description' => 'Description for event 2',
                'dateTime' => new \DateTime('+2 days'),
                'maxParticipants' => 30,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 3',
                'description' => 'Description for event 3',
                'dateTime' => new \DateTime('+3 days'),
                'maxParticipants' => 100,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 4',
                'description' => 'Description for event 4',
                'dateTime' => new \DateTime('+4 days'),
                'maxParticipants' => 20,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 5',
                'description' => 'Description for event 5',
                'dateTime' => new \DateTime('+5 days'),
                'maxParticipants' => 10,
                'isPublic' => false,
            ],
        ];

        foreach ($events as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setDateTime($eventData['dateTime']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setIsPublic($eventData['isPublic']);

            $manager->persist($event);
        }

        $manager->flush();
    }
}

