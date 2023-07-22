<?php

namespace App\Tests\Entity;

use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{

    public function testGetId()
    {
        $role = new Role();
        $this->assertNull($role->getId());
    }

    public function testGetRoleName(): void
    {
        $roleName = ['ROLE_ADMIN'];

        $role = new Role();
        $role->setRoleName($roleName);

        $this->assertSame($roleName, $role->getRoleName());
    }

    public function testSetRoleName(): void
    {
        $roleName = ['ROLE_USER'];

        $role = new Role();
        $role->setRoleName($roleName);

        $this->assertSame($roleName, $role->getRoleName());
    }

    public function testGetUsers(): void
    {
        $role = new Role();
        $role->setRoleName(['ROLE_TEST']);

        $testUser = new User();
        $testUser->setUsername('test')
            ->setPlainPassword('Password123$')
            ->setEmail('test@gmail.com')
            ->setRoles($role);

        $role->addUser($testUser);

        $this->assertSame([$testUser], $role->getUsers()->toArray());
    }

    public function testAddUser(): void
    {
        $role = new Role();
        $role->setRoleName(['ROLE_TEST']);

        $testUser = new User();
        $testUser->setUsername('test')
            ->setPlainPassword('Password123$')
            ->setEmail('test@gmail.com')
            ->setRoles($role);

        $role->addUser($testUser);

        $this->assertTrue($role->getUsers()->contains($testUser));
        $this->assertSame($role->getRoleName(), $testUser->getRoles());
    }

    public function testRemoveUser(): void
    {
        $role = new Role();
        $role->setRoleName(['ROLE_TEST']);

        $testUser = new User();
        $testUser->setUsername('test')
            ->setPlainPassword('Password123$')
            ->setEmail('test@gmail.com')
            ->setRoles($role);

        $role->addUser($testUser);
        $role->removeUser($testUser);

        $this->assertFalse($role->getUsers()->contains($testUser));
    }

    public function testToArray(): void
    {
        $roleName = ['ROLE_ADMIN'];

        $role = new Role();
        $role->setRoleName($roleName);

        $this->assertSame($roleName, $role->__toArray());
    }

    public function testToString(): void
    {
        $roleName = ['ROLE_USER'];

        $role = new Role();
        $role->setRoleName($roleName);

        $this->assertSame('ROLE_USER', $role->__toString());
    }

}