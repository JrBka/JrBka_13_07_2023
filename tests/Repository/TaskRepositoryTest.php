<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    /**
     * @var TaskRepository
     */
    private $repository;
    private $task;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $this->repository = $entityManager->getRepository(Task::class);
        $this->task = new Task();
        $this->task->setTitle('La tache 1')
            ->setContent('Il faut faire ceci !');
    }

    public function testFind(): void
    {

        $this->repository->add($this->task, true);

        $result = $this->repository->find($this->task->getId());

        $this->assertInstanceOf(Task::class, $result);

    }

    public function testFindOneBy(): void
    {

        $this->repository->add($this->task, true);

        $result = $this->repository->findOneBy(['id'=> $this->task->getId()]);

        $this->assertInstanceOf(Task::class, $result);
    }

    public function testFindAll(): void
    {
        $this->repository->add($this->task, true);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
    }

    public function testFindBy(): void
    {
        $this->repository->add($this->task, true);

        $id = $this->task->getId();

        $result = $this->repository->findBy(['id' => $id]);

        $this->assertIsArray($result);
    }

    public function testAddWithFlushTrue(): void
    {
        $add = $this->repository->add($this->task, true);

        $result = $this->repository->find($this->task->getId());

        $this->assertInstanceOf(Task::class, $result);
        $this->assertTrue($add);
    }

    public function testAddWithFlushFalse(): void
    {
        $add = $this->repository->add($this->task);

        $this->assertFalse($add);
    }

    public function testRemoveWithFlushTrue(): void
    {
        $this->repository->add($this->task, true);

        $remove = $this->repository->remove($this->task, true);

        $this->assertTrue($remove);
    }

    public function testRemoveWithFlushFalse(): void
    {
        $this->repository->add($this->task, true);

        $remove = $this->repository->remove($this->task);

        $this->assertFalse($remove);
    }

}