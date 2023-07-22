<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $urlGenerator;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testListActionWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_list'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testListActionWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_list'));

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testCreateActionWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_create'));

        $this->client->submitForm('Ajouter',[
                'task[title]'=> 'Tâche créé',
                'task[content]' => 'Contenu de la tache créé'
            ]
        );

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    public function testCreateActionWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_create'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testEditActionWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $this->client->request('GET', '/tasks/'.$taskId.'/edit');

        $this->client->submitForm('Modifier',[
            'task[title]'=> 'Tâche modifiée10',
            'task[content]' => 'Contenu modifié de la tâche10'
            ]
        );

        $this->assertResponseStatusCodeSame(302);
    }

    public function testEditActionWithoutUserConnected()
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $this->client->request('GET', '/tasks/'.$taskId.'/edit');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testToggleTaskActionWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/toggle"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
    }

    public function testToggleTaskActionWithoutUserConnected()
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testDeleteTaskActionWithUserAuthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/delete"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success','La tâche a bien été supprimé');
    }

    public function testDeleteTaskActionWithUserUnauthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'anonyme']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/delete"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger','Vous n\'avez pas l\'autorisation de faire ca');
    }

    public function testDeleteTaskActionWithoutUserConnected()
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

}