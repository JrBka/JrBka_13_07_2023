<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        $task = new Task();
        $this->assertNull($task->getId());
    }

    public function testGetCreatedAt()
    {
        $task = new Task();
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }

    public function testSetCreatedAt()
    {
        $task = new Task();
        $createdAt = new \DateTimeImmutable();
        $task->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $task->getCreatedAt());
    }

    public function testGetTitle()
    {
        $task = new Task();
        $this->assertNull($task->getTitle());
    }

    public function testSetTitle()
    {
        $task = new Task();
        $title = 'Example Title';
        $task->setTitle($title);
        $this->assertSame($title, $task->getTitle());
    }

    public function testGetContent()
    {
        $task = new Task();
        $this->assertNull($task->getContent());
    }

    public function testSetContent()
    {
        $task = new Task();
        $content = 'Example Content';
        $task->setContent($content);
        $this->assertSame($content, $task->getContent());
    }

    public function testIsDone()
    {
        $task = new Task();
        $this->assertFalse($task->isDone());
    }

    public function testToggle()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertTrue($task->isDone());
    }

    public function testGetUser()
    {
        $task = new Task();
        $this->assertNull($task->getUser());
    }

    public function testSetUser()
    {
        $task = new Task();
        $user = new User();
        $task->setUser($user);
        $this->assertSame($user, $task->getUser());
    }
}