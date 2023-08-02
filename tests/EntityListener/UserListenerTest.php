<?php

namespace App\Tests\EntityListener;

use App\Entity\Role;
use App\Entity\User;
use App\EntityListener\UserListener;
use App\Repository\RoleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserListenerTest extends TestCase
{
    private $hasher;
    private $security;
    private $roleRepository;
    private $userListener;

    protected function setUp(): void
    {
        $this->hasher = $this->getMockBuilder(UserPasswordHasherInterface::class)->addMethods(['hashPassword'])->getMock();
        $this->security = $this->createMock(Security::class);
        $this->roleRepository = $this->createMock(RoleRepository::class);
        $this->userListener = new UserListener($this->hasher,$this->roleRepository,$this->security);
    }


    public function testPrePersist()
    {
        $user = new User();
        $user->setPlainPassword("Password123$");

        $role = new Role();
        $role->setRoleName(["ROLE_USER"]);

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'Password123$')
            ->willReturn('hashed_password');

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->roleRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$role]);

        $this->userListener->prePersist($user);

        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertEquals(["ROLE_USER"], $user->getRoles());
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