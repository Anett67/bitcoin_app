<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPageIsUp() 
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

	public function testLoginPage()
    {
       $client = static::createClient();
       $crawler = $client->request('GET', '/connexion');
       $this->assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
       $this->assertSame(1, $crawler->filter('html:contains("Créer un compte")')->count());
    }

    public function testLogin()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.total-earnings-link', '-25 €');
    }

    public function testLoginError()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/connexion');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton("Se connecter")->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Identifiants invalides.")')->count());
    }

    public function testLogout()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $link = $crawler->selectLink("Se déconnecter")->link();
        $crawler = $client->click($link);

        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
}