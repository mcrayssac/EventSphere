<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\EventFixtures;
use App\Entity\User;
use App\Entity\Event;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class EventControllerTest extends WebTestCase
{
    private function loadFixtures($client)
    {
        $container = $client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $purger = new ORMPurger($em);
        $purger->purge();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $userFixtures = new UserFixtures($passwordHasher);
        $eventFixtures = new EventFixtures();

        $userFixtures->load($em);
        $eventFixtures->load($em);

        $em->flush();
    }

    public function testCreateEvent(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Log admin in
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Request 
        $crawler = $client->request('GET', '/event/create');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create Event')->form([
            'event_form[title]' => 'Test Event',
            'event_form[description]' => 'This is a test event.',
            'event_form[dateTime]' => (new \DateTime('+1 day'))->format('Y-m-d H:i:s'),
            'event_form[maxParticipants]' => 100,
            'event_form[isPublic]' => true,
            'event_form[isPaid]' => false,
            'event_form[cost]' => 0,
        ]);

        $client->submit($form);
        $client->followRedirect();

        $this->assertStringContainsString('Event created successfully.', $client->getResponse()->getContent());

        // Verify the event was saved in the database
        $eventRepository = $container->get('doctrine')->getRepository(\App\Entity\Event::class);
        $event = $eventRepository->findOneBy(['title' => 'Test Event']);
        $this->assertNotNull($event);
        $this->assertEquals('This is a test event.', $event->getDescription());
    }

    public function testCreateEventInvalidData(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Log admin in
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Request 
        $crawler = $client->request('GET', '/event/create');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Fill in the form with invalid data and submit it
        $form = $crawler->selectButton('Create Event')->form([
            'event_form[title]' => 'Bad Event',
            'event_form[description]' => 'This is a bad test event.',
            'event_form[dateTime]' => 'invalid date',
            'event_form[maxParticipants]' => -10,
            'event_form[isPublic]' => true,
            'event_form[isPaid]' => false,
            'event_form[cost]' => 0,
        ]);

        $client->submit($form);

        // Verify that no bad event was saved in the database
        $eventRepository = $container->get('doctrine')->getRepository(Event::class);
        $event = $eventRepository->findOneBy(['title' => 'Bad Event']);
        $this->assertNull($event);
    }

    public function testShowEventDetail(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Create a test user and log them in
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Create an event with no remaining places
        $eventRepository = $container->get('doctrine')->getRepository(Event::class);
        $event = new Event();
        $event->setTitle('Full Event');
        $event->setDescription('This event has no remaining places.');
        $event->setDateTime(new \DateTime('+1 day'));
        $event->setMaxParticipants(0);
        $event->setIsPublic(true);
        $event->setCreator($testUser);

        $em = $container->get(EntityManagerInterface::class);
        $em->persist($event);
        $em->flush();

        // Request the event detail page
        $crawler = $client->request('GET', '/event/' . $event->getId());

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Check for the "No remaining places available." message
        $this->assertStringContainsString('No remaining places available.', $client->getResponse()->getContent());
    }
}
