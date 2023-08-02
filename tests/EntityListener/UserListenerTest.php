<?php

namespace App\Tests\EntityListener;

use App\Entity\User;
use App\EntityListener\UserListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListenerTest extends TestCase
{
    private $hasher;
    private $userListener;

    protected function setUp(): void
    {
        $this->hasher = $this->getMockBuilder(UserPasswordHasherInterface::class)->addMethods(['hashPassword'])->getMock();
        $this->userListener = new UserListener($this->hasher);
    }


    public function testPrePersist()
    {
        $user = new User();
        $user->setPlainPassword("Password123$");

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'Password123$')
            ->willReturn('hashed_password');

        $this->userListener->prePersist($user);

        $this->assertEquals('hashed_password', $user->getPassword());
    }

    public function testPrePersistWithoutPlainPassword(): void
    {
        $user = new User();

        $this->hasher->expects($this->never())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');

        $this->userListener->preUpdate($user);

        $this->assertNull($user->getPassword());
    }

    public function testPreUpdateWithPlainPassword(): void
    {
        $user = new User();
        $user->setPlainPassword('password123');

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');

        $this->userListener->preUpdate($user);

        $this->assertEquals('hashed_password', $user->getPassword());
    }

    public function testPreUpdateWithoutPlainPassword(): void
    {
        $user = new User();

        $this->hasher->expects($this->never())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');

        $this->userListener->preUpdate($user);

        $this->assertNull($user->getPassword());
    }
}