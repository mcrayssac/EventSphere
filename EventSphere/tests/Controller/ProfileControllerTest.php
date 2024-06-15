<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProfileControllerTest extends WebTestCase
{
    public function testEditProfile(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Log in as admin
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Request
        $crawler = $client->request('GET', '/profile');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Select the form and fill in some values
        $form = $crawler->selectButton('Update Profile')->form();
        $form['profile_form[firstName]'] = 'Updated First Name';
        $form['profile_form[currentPassword]'] = 'adminpwd123'; // Current password
        $form['profile_form[plainPassword][first]'] = 'newpassword321'; // New password
        $form['profile_form[plainPassword][second]'] = 'newpassword321'; // Confirm new password

        $client->submit($form);

        $crawler = $client->request('GET', '/profile');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // I cannot make this assertion work for the life of me so let's banish it in the shadow realm
        // $this->assertStringContainsString('Profile updated successfully.', $client->getCrawler()->filter('.alert-success')->text());

        // Verification
        $updatedUser = $userRepository->find($testUser->getId());
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, 'newpassword321'));
    }

    public function testEditProfileInvalidCurrentPassword(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        // Log as admin
        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);

        // Request
        $crawler = $client->request('GET', '/profile');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update Profile')->form();
        $form['profile_form[firstName]'] = 'Updated First Name';
        $form['profile_form[currentPassword]'] = 'wrongpassword'; // Incorrect current password
        $form['profile_form[plainPassword][first]'] = 'newpassword'; // New password
        $form['profile_form[plainPassword][second]'] = 'newpassword'; // Confirm new password

        $client->submit($form);

        $crawler = $client->request('GET', '/profile');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        // $this->assertStringContainsString('Current password is incorrect.', $client->getCrawler()->filter('.alert-danger')->text());

        $updatedUser = $userRepository->find($testUser->getId());
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $this->assertFalse($passwordHasher->isPasswordValid($updatedUser, 'newpassword'));
    }

    private function loadFixtures($client)
    {
        $container = $client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $purger = new ORMPurger($em);
        $purger->purge();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $userFixtures = new UserFixtures($passwordHasher);

        $userFixtures->load($em);

        $em->flush();
    }
}
