<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPageIsUp() 
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testRegistrationPage()
    {
       $client = static::createClient();
       $crawler = $client->request('GET', '/connexion');
       $this->assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
       $this->assertSame(1, $crawler->filter('html:contains("Créer un compte")')->count());
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        $form = $crawler->selectButton("Créer un compte")->form();
        $form["registration_form[email]"] = "testuser@mail.fr";
        $form["registration_form[plainPassword]"] = "password";
        $client->submit($form);

        $crawler = $client->followRedirect();

        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('testuser@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.total-earnings-link', '0 €');
    }

    public function testRegistrationError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        $form = $crawler->selectButton("Créer un compte")->form();
        $form["registration_form[email]"] = "testuser@mail.fr";
        $form["registration_form[plainPassword]"] = "password";
        $client->submit($form);

        $this->assertSelectorTextContains('li', 'Il existe déjà un compte avec cet email');
    }
}