<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['email' => 'admin@example.com']);

        if (!$adminUser) {
            throw new \Exception('Admin user not found. Please ensure UserFixtures are loaded first.');
        }

        $events = [
            [
                'title' => 'Event 1',
                'description' => 'Description for event 1',
                'dateTime' => new \DateTime('-4 day'),
                'maxParticipants' => 50,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 2',
                'description' => 'Description for event 2',
                'dateTime' => new \DateTime('-3 day'),
                'maxParticipants' => 30,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 3',
                'description' => 'Description for event 3',
                'dateTime' => new \DateTime('-2 day'),
                'maxParticipants' => 20,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 4',
                'description' => 'Description for event 4',
                'dateTime' => new \DateTime('-1 day'),
                'maxParticipants' => 40,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 5',
                'description' => 'Description for event 5',
                'dateTime' => new \DateTime(),
                'maxParticipants' => 60,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 6',
                'description' => 'Description for event 6',
                'dateTime' => new \DateTime('+1 day'),
                'maxParticipants' => 25,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 7',
                'description' => 'Description for event 7',
                'dateTime' => new \DateTime('+2 day'),
                'maxParticipants' => 35,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 8',
                'description' => 'Description for event 8',
                'dateTime' => new \DateTime('+3 day'),
                'maxParticipants' => 45,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 9',
                'description' => 'Description for event 9',
                'dateTime' => new \DateTime('+4 day'),
                'maxParticipants' => 55,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 10',
                'description' => 'Description for event 10',
                'dateTime' => new \DateTime('+5 day'),
                'maxParticipants' => 65,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 11',
                'description' => 'Description for event 11',
                'dateTime' => new \DateTime('+6 day'),
                'maxParticipants' => 75,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 12',
                'description' => 'Description for event 12',
                'dateTime' => new \DateTime('+7 day'),
                'maxParticipants' => 85,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 13',
                'description' => 'Description for event 13',
                'dateTime' => new \DateTime('+8 day'),
                'maxParticipants' => 95,
                'isPublic' => false,
            ],
            [
                'title' => 'Event 14',
                'description' => 'Description for event 14',
                'dateTime' => new \DateTime('+9 day'),
                'maxParticipants' => 105,
                'isPublic' => true,
            ],
            [
                'title' => 'Event 15',
                'description' => 'Description for event 15',
                'dateTime' => new \DateTime('+10 day'),
                'maxParticipants' => 115,
                'isPublic' => false,
            ]
        ];
            

        foreach ($events as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setDateTime($eventData['dateTime']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setIsPublic($eventData['isPublic']);
            $event->setCreator($adminUser);

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

