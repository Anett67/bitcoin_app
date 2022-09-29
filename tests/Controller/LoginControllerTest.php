<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testConnexionPageIsUp() 
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        echo $client->getResponse()->getContent();
    }
}