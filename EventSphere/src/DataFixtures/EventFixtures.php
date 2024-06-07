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
            [
                'title' => 'Event 6',
                'description' => 'Description for event 6',
                'dateTime' => new \DateTime('+6 days'),
                'maxParticipants' => 200,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 7',
                'description' => 'Description for event 7',
                'dateTime' => new \DateTime('+7 days'),
                'maxParticipants' => 40,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 8',
                'description' => 'Description for event 8',
                'dateTime' => new \DateTime('+8 days'),
                'maxParticipants' => 60,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 9',
                'description' => 'Description for event 9',
                'dateTime' => new \DateTime('+9 days'),
                'maxParticipants' => 80,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 10',
                'description' => 'Description for event 10',
                'dateTime' => new \DateTime('+10 days'),
                'maxParticipants' => 70,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 11',
                'description' => 'Description for event 11',
                'dateTime' => new \DateTime('+11 days'),
                'maxParticipants' => 90,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 12',
                'description' => 'Description for event 12',
                'dateTime' => new \DateTime('+12 days'),
                'maxParticipants' => 120,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 13',
                'description' => 'Description for event 13',
                'dateTime' => new \DateTime('+13 days'),
                'maxParticipants' => 150,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 14',
                'description' => 'Description for event 14',
                'dateTime' => new \DateTime('+14 days'),
                'maxParticipants' => 180,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 15',
                'description' => 'Description for event 15',
                'dateTime' => new \DateTime('+15 days'),
                'maxParticipants' => 160,
                'isPublic' => true,
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

