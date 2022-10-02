<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
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

    public function testEarningsPageIsUp() 
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $crawler = $client->request('GET', '/gains');
        $this->assertSame(1, $crawler->filter('html:contains("Vos gains")')->count());
        $this->assertSelectorExists('canvas');
    }

    public function testDashboardCryptoListIsShown()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('.transactions-list');
        $this->assertSame(1, $crawler->filter('div.transaction')->count());
        $this->assertSelectorTextSame('div.transaction .crypto-symbol', 'ETC');
    }

    public function testEditLinkIsNotShown()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorNotExists('.edit-transaction');
    }

    public function testCryptoFormIsShown()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/supprimer');
        $this->assertSelectorExists('form[name="transaction_delete"]');
    }

    public function testAmountDeletion()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('johndoe@mail.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/supprimer');

        $form = $crawler->selectButton("Valider")->form();
        $form["transaction_delete[quantity]"] = 2;

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter("html:contains('Le montant a bien été supprimé.')")->count());
    }
}