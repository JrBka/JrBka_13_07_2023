<?php

namespace App\Tests\Entity;

use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testGetId()
    {
        $role = new Role();
        $this->assertNull($role->getId());
    }

    public function testGetUsername()
    {
    $user = new User();
    $user->setUsername('john_doe');

    $this->assertSame('john_doe', $user->getUsername());
    }

    public function testSetUsername()
    {
    $user = new User();
    $user->setUsername('john_doe');

    $this->assertSame('john_doe', $user->getUsername());
    }

    public function testGetUserIdentifier()
    {
    $user = new User();
    $user->setUsername('john_doe');

    $this->assertSame('john_doe', $user->getUserIdentifier());
    }

    public function testGetPassword()
    {
    $user = new User();
    $user->setPassword('hashed_password');

    $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testSetPassword()
    {
    $user = new User();
    $user->setPassword('hashed_password');

    $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testGetSalt()
    {
    $user = new User();

    $this->assertNull($user->getSalt());
    }

    public function testAddTask()
    {
    $user = new User();
    $task = new Task();
    $task->setTitle('La tache 1')
        ->setContent('Il faut faire ceci !');

    $user->addTask($task);

    $this->assertTrue($user->getTasks()->contains($task));
    $this->assertSame($user, $task->getUser());
    }

    public function testRemoveTask()
    {
    $user = new User();
    $task = new Task();
    $user->addTask($task);

    $user->removeTask($task);

    $this->assertFalse($user->getTasks()->contains($task));
    $this->assertNull($task->getUser());
    }

    public function testSetPlainPassword()
    {
    $user = new User();
    $user->setPlainPassword('plain_password');

    $this->assertSame('plain_password', $user->getPlainPassword());
    }

    public function testSetEmail()
    {
    $user = new User();
    $user->setEmail('john.doe@example.com');

    $this->assertSame('john.doe@example.com', $user->getEmail());
    }

    public function testGetRoles()
    {
    $user = new User();
    $role = new Role();
    $role->setRoleName(['ROLE_USER']);
    $user->setRoles($role);

    $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testGetRole()
    {
        $user = new User();
        $role = new Role();
        $role->setRoleName(['ROLE_USER']);

        $user->setRoles($role);

        $this->assertInstanceOf(Role::class, $user->getRole());
    }

}