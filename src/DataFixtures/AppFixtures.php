<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $anonymeUser = new User();
        $anonymeUser->setUsername('anonyme')
            ->setPlainPassword('Password123$')
            ->setEmail('anonyme@gmail.com');
        $manager->persist($anonymeUser);
        $manager->flush();

        $admin = new User();
        $admin->setUsername('admin')
            ->setPlainPassword('Password123$')
            ->setEmail('admin@gmail.com')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

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
