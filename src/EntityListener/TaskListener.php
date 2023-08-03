<?php

namespace App\EntityListener;

use App\Entity\Task;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class TaskListener
{
    private $user;
    private $repository;
    private $security;

    public function __construct(UserRepository $repository, Security $security){
        $this->repository = $repository;
        $this->security = $security;
    }

    /**
     *
     * This function defines the author of the task before being persisted
     *
     * @param Task $task
     * @return void
     */
    public function PrePersist(Task $task): void
    {

        if(!empty($task->getUser())) {
            return;
        }
        elseif ($this->security->getUser()){
            $this->user = $this->security->getUser();
        }
        else{
            $this->user = $this->repository->findOneBy(['username' => 'anonyme']);
        }

        $task->setUser($this->user);
    }

}

