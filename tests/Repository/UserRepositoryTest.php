<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var UserRepository
     */
    private $repository;
    private $role;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $this->repository = $entityManager->getRepository(User::class);
        $roles = $entityManager->getRepository(Role::class)->findAll();
        foreach ($roles as $role){
            if ($role->getRoleName()[0] == "ROLE_USER"){
                $this->role= $role;
            }
        }

    }

    public function testFind(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $result = $this->repository->find($user->getId());

        $this->assertInstanceOf(User::class, $result);

    }

    public function testFindOneBy(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $result = $this->repository->findOneBy(['username'=>$username]);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testFindAll(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $result = $this->repository->findAll();

        $this->assertContainsOnlyInstancesOf(User::class,$result);
        $this->assertIsArray($result);
    }

    public function testFindBy(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $result = $this->repository->findBy(['email'=>'user@gmail.com']);

        $this->assertContainsOnlyInstancesOf(User::class,$result);
        $this->assertIsArray($result);
    }

    public function testAddWithFlushTrue(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $add=$this->repository->add($user, true);

        $result = $this->repository->find($user->getId());

        $this->assertInstanceOf(User::class, $result);
        $this->assertTrue($add);
    }

    public function testAddWithFlushFalse(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $add = $this->repository->add($user);

        $this->assertFalse($add);
    }

    public function testRemoveWithFlushTrue(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $delete = $this->repository->remove($user, true);

        $this->assertTrue($delete);

    }

    public function testRemoveWithFlushFalse(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $delete = $this->repository->remove($user);

        $this->assertFalse($delete);

    }

    public function testUpgradePassword(): void
    {
        $user = new User();
        $nb = uniqid();
        $username = 'user'.$nb;
        $user->setUsername($username)
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($this->role);

        $this->repository->add($user, true);

        $pwd1 = $this->repository->findOneBy(['username'=>$username])->getPassword();

        $newHashedPassword = 'nouveauMotDePasse123';

        $this->repository->upgradePassword($user, $newHashedPassword);

        $pwd2 = $this->repository->findOneBy(['username'=>$username])->getPassword();

        $this->assertNotEquals($pwd1,$pwd2);

    }


}