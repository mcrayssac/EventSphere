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

        for ($x = 0; $x <= 15; $x++) {
            // Set datetime between -5 and +10 days from now in function of $x - 5
            $tempDate = new \DateTime();
            $tempDate->modify(($x - 5) . ' day');
            $tempDate->setTime(rand(0, 23), rand(0, 59), 0);
            
            $event = new Event();
            $event->setTitle('Event ' . $x);
            $event->setDescription('Description for event ' . $x);
            $event->setDateTime($tempDate);
            $event->setMaxParticipants(50 + $x * 10);
            $event->setIsPublic($x % 2 == 0);
            $event->setCreator($adminUser);
            $event->setIsPaid($x % 3 == 0);
            $event->setCost($x % 3 == 0 ? 10 + $x * 5 : null);

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

