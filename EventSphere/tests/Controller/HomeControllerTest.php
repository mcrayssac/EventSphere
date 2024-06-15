<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\EventFixtures;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class HomeControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Request the home page
        $crawler = $client->request('GET', '/');

        $response = $client->getResponse();

        // Check the response
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Check for the presence of key elements
        $this->assertStringContainsString('Event Schedule', $response->getContent());
        $this->assertStringContainsString('Missed', $response->getContent());
        $this->assertStringContainsString('Today', $response->getContent());
        $this->assertStringContainsString('Tomorrow', $response->getContent());

        // Ensure unauthenticated user cannot access logged-in specific content
        $this->assertStringNotContainsString('Subscription', $response->getContent());
        $this->assertStringNotContainsString('Your Profile', $response->getContent());
    }

    public function testHomeAuthenticated(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Log as admin user
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Request the home page as an authenticated user
        $crawler = $client->request('GET', '/');

        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Check if the user as access to logged-only content
        $this->assertStringContainsString('Subcription', $response->getContent());
        $this->assertStringContainsString('Your Profile', $response->getContent());
    }

    private function loadFixtures($client)
    {
        $container = $client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $purger = new ORMPurger($em);
        $purger->purge();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $userFixtures = new UserFixtures($passwordHasher);
        $eventFixtures = new EventFixtures();

        // Load Fixtures
        $userFixtures->load($em);
        $eventFixtures->load($em);

        $em->flush();
    }
}
