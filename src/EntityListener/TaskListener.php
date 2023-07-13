<?php

namespace App\EntityListener;



use App\Entity\Task;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskListener extends AbstractController
{
    private $user;
    private $repository;

    public function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function PrePersist(Task $task): void
    {
        if ($this->getUser()){
            $this->user = $this->getUser();
        }else{
            $this->user = $this->repository->findOneBy(['username' => 'anonyme']);
        }

        $task->setUser($this->user);
    }

}

