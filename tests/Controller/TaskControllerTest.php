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
    private $adminId;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testListDoneWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testListDoneWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testListTodoWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_list_todo'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testListTodoWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_list_todo'));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateActionWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_create'));

        $nb = uniqid();
        $this->client->submitForm('Ajouter',[
                'task[title]'=> 'Tâche créé'.$nb,
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

        $nb = uniqid();
        $this->client->submitForm('Modifier',[
            'task[title]'=> 'Tâche modifiée n°'.$nb,
            'task[content]' => 'Contenu modifié de la tâche n°'.$nb
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

    public function testToggleTaskActionIsDoneFalseWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $task->toggle(false);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/toggle"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
    }

    public function testToggleTaskActionIsDoneTrueTrueWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $task->toggle(true);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/done');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/toggle"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
    }

    public function testToggleTaskActionWithoutUserConnected()
    {
        $this->client->request('GET', '/tasks/todo');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    public function testDeleteTaskActionWithUserAuthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $this->adminId = $user->getId();

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user'=>$this->adminId]);

        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/delete"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success','La tâche a bien été supprimée.');
    }

    public function testDeleteTaskActionWithUserUnauthorized()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'user1']);
        $this->client->loginUser($user);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/delete"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger','Vous n\'avez pas l\'autorisation de faire ca');
    }

    public function testDeleteTaskActionWithoutUserConnected()
    {
        $this->client->request('GET', '/tasks/todo');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorExists('form[action="/login_check"]');
    }

}