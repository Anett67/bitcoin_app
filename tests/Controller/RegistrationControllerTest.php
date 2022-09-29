<?php

namespace App\Tests\Controller;

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
}