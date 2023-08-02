<?php

namespace App\Tests\EntityListener;

use App\Entity\Task;
use App\Entity\User;
use App\EntityListener\TaskListener;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class TaskListenerTest extends TestCase
{
    private $security;
    private $repository;
    private $listener;

    protected function setUp(): void
    {
        $this->security = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new TaskListener($this->repository, $this->security);
    }

    public function testPrePersistWithUser(): void
    {
        $user = new User();
        $task = new Task();

        $this->security->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $task->setUser(null);

        $this->listener->prePersist($task);

        $this->assertEquals($user, $task->getUser());
    }

    public function testPrePersistWithoutUser(): void
    {
        $anonymousUser = new User();
        $task = new Task();

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => 'anonyme'])
            ->willReturn($anonymousUser);

        $task->setUser(null);

        $this->listener->prePersist($task);

        $this->assertEquals($anonymousUser, $task->getUser());
    }
}