<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleRepositoryTest extends KernelTestCase
{
    /**
     * @var RoleRepository
     */
    private $repository;
    private $role;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $this->repository = $entityManager->getRepository(Role::class);
        $this->role = new Role();
        $this->role->setRoleName(["ROLE_TEST"]);
    }

    public function testFind(): void
    {

        $this->repository->add($this->role, true);

        $result = $this->repository->find($this->role->getId());

        $this->assertInstanceOf(Role::class, $result);

    }

    public function testFindOneBy(): void
    {

        $this->repository->add($this->role, true);

        $result = $this->repository->findOneBy(['id'=> $this->role->getId()]);

        $this->assertInstanceOf(Role::class, $result);
    }

    public function testFindAll(): void
    {
        $this->repository->add($this->role, true);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
    }

    public function testFindBy(): void
    {
        $this->repository->add($this->role, true);

        $id = $this->role->getId();

        $result = $this->repository->findBy(['id' => $id]);

        $this->assertIsArray($result);
    }

    public function testAddWithFlushTrue(): void
    {
        $add = $this->repository->add($this->role, true);

        $result = $this->repository->find($this->role->getId());

        $this->assertInstanceOf(Role::class, $result);
        $this->assertTrue($add);
    }

    public function testAddWithFlushFalse(): void
    {
        $add = $this->repository->add($this->role);

        $this->assertFalse($add);
    }

    public function testRemoveWithFlushTrue(): void
    {
        $this->repository->add($this->role, true);

        $remove = $this->repository->remove($this->role, true);

       $this->assertTrue($remove);

    }

    public function testRemoveWithFlushFalse(): void
    {
        $this->repository->add($this->role, true);

        $remove = $this->repository->remove($this->role);

        $this->assertFalse($remove);

    }

}