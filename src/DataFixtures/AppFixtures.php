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

        $user1 = new User();
        $user1->setUsername('user1')
            ->setPlainPassword('Password123$')
            ->setEmail('user1@gmail.com')
            ->setRoles($role2);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user2')
            ->setPlainPassword('Password123$')
            ->setEmail('user2@gmail.com')
            ->setRoles($role2);
        $manager->persist($user2);

        $task0 = new Task();
        $task0->setTitle('La tache 0')
            ->setContent('Il faut faire ceci !');
        $manager->persist($task0);

        $task1 = new Task();
        $task1->setTitle('La tache 1')
            ->setContent('Il faut faire ceci !');
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('La tache 2')
            ->setContent('Il faut faire cela !')
            ->setUser($admin);
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setTitle('La tache 3')
            ->setContent('Cette tâche est importante !')
            ->setUser($user1);
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setTitle('La tache 4')
            ->setContent('Cette tâche est importante !')
            ->setUser($user1);
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setTitle('La tache 5')
            ->setContent('Cette tâche est importante !')
            ->setUser($user2);
        $manager->persist($task5);

        $task6 = new Task();
        $task6->setTitle('La tache 6')
            ->setContent('Cette tâche est importante !')
            ->setUser($admin);
        $manager->persist($task6);

        $task7 = new Task();
        $task7->setTitle('La tache 7')
            ->setContent('Cette tâche est importante !')
            ->setUser($admin);
        $manager->persist($task7);

        $task8 = new Task();
        $task8->setTitle('La tache 8')
            ->setContent('Cette tâche est importante !')
            ->toggle(true)
            ->setUser($admin);
        $manager->persist($task8);

        $task9 = new Task();
        $task9->setTitle('La tache 9')
            ->setContent('Cette tâche est importante !')
            ->toggle(true)
            ->setUser($user1);
        $manager->persist($task9);

        $manager->flush();
    }



}
