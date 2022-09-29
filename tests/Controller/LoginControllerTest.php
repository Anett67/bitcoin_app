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
        echo $client->getResponse()->getContent();
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

        // test e.g. the profile page
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.total-earnings-link', '-25 €');
    }
}