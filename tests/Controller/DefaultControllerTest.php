<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    private $client;
    private $urlGenerator;

    public function setUp() : void
    {
        $this->client = static::createClient();

        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }

    public function testHomePageWithoutUser()

    {
        $this->client->request('get', $this->urlGenerator->generate('homepage'));

        $this->assertResponseStatusCodeSame(302);
    }

    public function testHomePageWithUser()

    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);

        $this->client->loginUser($user);

        $this->client->request('get', $this->urlGenerator->generate('homepage'));

        $this->assertResponseStatusCodeSame(200);
    }

}
