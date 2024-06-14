<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        // Create a client to make requests
        $client = static::createClient();

        // Request the home page
        $client->request('GET', '/');

        // Fetch the response
        $response = $client->getResponse();

        // Assertions to check the outcome
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Further assertions can verify the content of the response
        $this->assertStringContainsString('Event Schedule', $response->getContent());
    }
}
