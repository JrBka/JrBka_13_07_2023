<?php

namespace App\Tests\Controller;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $roleId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testListActionWithoutUserConnected()
    {
        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testListActionWithUserUnauthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'anonyme']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testListActionWithUserAuthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }


    public function testCreateAction()
    {
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        foreach ($roles as $role){
            if ($role->getRoleName()[0] == "ROLE_USER"){
                $this->roleId= $role->getId();
            }
        }

        $this->client->request('GET', '/users/create');

        $this->client->submitForm('Ajouter',[
                'user[username]'=> 'bibi5',
                'user[plainPassword][first]' => 'Password123$',
                'user[plainPassword][second]' => 'Password123$',
                'user[email]' => 'bibi2@gmail.com',
                'user[roles]' => $this->roleId
            ]
        );

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', 'L\'utilisateur a bien été créé');
    }

    public function testEditActionWithUserAuthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $userEdit = $this->entityManager->getRepository(User::class)->findOneBy([],['id'=>'DESC']);
        $userEditId = $userEdit->getId();
        $nb = random_int(2,2000);
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        foreach ($roles as $role){
            if ($role->getRoleName()[0] == "ROLE_USER"){
                $this->roleId= $role->getId();
            }
        }

        $this->client->request('GET', '/users/'.$userEditId.'/edit');

        $this->client->submitForm('Modifier',[
                'user[username]'=> 'bibi'.$nb,
                'user[plainPassword][first]' => 'Password123$',
                'user[plainPassword][second]' => 'Password123$',
                'user[email]' => 'bibi530@gmail.com',
                'user[roles]' => $this->roleId
            ]
        );

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', 'L\'utilisateur a bien été modifié');
    }

    public function testEditActionWithUserUnauthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'anonyme']);
        $this->client->loginUser($user);
        $userEdit = $this->entityManager->getRepository(User::class)->findOneBy([],['id'=>'DESC']);
        $userEditId = $userEdit->getId();

        $this->client->request('GET', '/users/'.$userEditId.'/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditActionWithoutUserConnected()
    {
        $userEdit = $this->entityManager->getRepository(User::class)->findOneBy([],['id'=>'DESC']);
        $userEditId = $userEdit->getId();

        $this->client->request('GET', '/users/'.$userEditId.'/edit');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }
}
