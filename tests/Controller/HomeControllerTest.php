<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testDashboardPageRedirects() 
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    public function testEarningsPageRedirects() 
    {
        $client = static::createClient();
        $client->request('GET', '/gains');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }
}