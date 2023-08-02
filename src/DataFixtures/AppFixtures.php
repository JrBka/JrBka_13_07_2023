<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * This function loads 2 roles, 3 users and 2 tasks in the database
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $role1 = new Role();
        $role1->setRoleName(['ROLE_ADMIN']);
        $manager->persist($role1);

        $role2 = new Role();
        $role2->setRoleName(['ROLE_USER']);
        $manager->persist($role2);

        $manager->flush();

        $anonymeUser = new User();
        $anonymeUser->setUsername('anonyme')
            ->setPlainPassword('Password123$')
            ->setEmail('anonyme@gmail.com')
            ->setRoles($role2);
        $manager->persist($anonymeUser);
        $manager->flush();

        $admin = new User();
        $admin->setUsername('admin')
            ->setPlainPassword('Password123$')
            ->setEmail('admin@gmail.com')
            ->setRoles($role1);
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user1')
            ->setPlainPassword('Password123$')
            ->setEmail('user@gmail.com')
            ->setRoles($role2);
        $manager->persist($user);

        $task1 = new Task();
        $task1->setTitle('La tache 1')
            ->setContent('Il faut faire ceci !');
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('La tache 2')
            ->setContent('Il faut faire cela !');
        $manager->persist($task2);

        $manager->flush();
    }



}
