<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    private $client;
    private $urlGenerator;
    private $entityManager;

    public function setUp() : void
    {
        $this->client = static::createClient();

        $this->urlGenerator = $this->client->getContainer()->get('router.default');

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

    }

    public function testLoginPage()
    {
        $this->client->request('GET', $this->urlGenerator->generate('login'));

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('login');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('login'));

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'toto';
        $form['_password'] = 'Password';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $this->client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger','invalid credentials');
        $this->assertRouteSame('login');
    }

    public function testLoginWithGoodCredentials()
    {
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('login'));

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'Password123$';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $this->client->followRedirect();

        $this->assertSelectorTextContains('h1','Bienvenue sur Todo List');
        $this->assertRouteSame('homepage');
    }

    public function testLogout()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('logout'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();

        $this->assertRouteSame('login');
    }
}
