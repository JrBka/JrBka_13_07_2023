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

    public function testListDoneWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testListDoneWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('task_list_done');
    }

    public function testListTodoWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_list_todo'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testListTodoWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', $this->urlGenerator->generate('task_list_todo'));

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('task_list_todo');
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
        $this->assertRouteSame('task_list_todo');
    }

    public function testCreateActionWithoutUserConnected()
    {
        $this->client->request('GET', $this->urlGenerator->generate('task_create'));

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
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
        $this->client->followRedirect();
        $this->assertRouteSame('task_list_todo');
    }

    public function testEditActionWithoutUserConnected()
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        $taskId = $task->getId();

        $this->client->request('GET', '/tasks/'.$taskId.'/edit');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testToggleTaskActionIsDoneFalseWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        if ($task->isDone() === true){
            $task->toggle(false);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/toggle"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('task_list_done');
    }

    public function testToggleTaskActionIsDoneTrueWithUserConnected()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy([]);
        if ($task->isDone() === false){
            $task->toggle(true);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/done');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/toggle"]')->form();

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('task_list_todo');
    }

    public function testToggleTaskActionWithoutUserConnected()
    {
        $this->client->request('GET', '/tasks/todo');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testDeleteTaskActionWithUserAdminForAdminTask()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);
        $adminId = $user->getId();

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user'=>$adminId]);

        $taskId = $task->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $form = $crawler->filter('form[action="/tasks/'.$taskId.'/delete"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success','La tâche a bien été supprimée.');
    }

    public function testDeleteTaskActionWithUserAdminForAnonymeTask()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($user);

        $anonyme = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'anonyme']);
        $anonymeId = $anonyme->getId();

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user'=>$anonymeId]);
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
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->client->loginUser($admin);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'user1']);
        $userId = $user->getId();

        $crawler = $this->client->request('GET', '/tasks/todo');

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user'=> $userId]);
        $taskId = $task->getId();

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
        $this->assertRouteSame('login');
    }

}