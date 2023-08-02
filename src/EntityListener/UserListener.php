<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserListener{

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }

    /**
     * This function hashes the password and define role user before it is persisted
     *
     * @param User $user
     * @return void
     */
    public function PrePersist(User $user):void
    {
        $this->encodePassword($user);
    }

    /**
     * This function hashes the password before it is updated
     *
     * @param User $user
     * @return void
     */
    public function preUpdate(User $user):void
    {
        $this->encodePassword($user);
    }

    /**
     * This function hashes the password
     *
     * @param User $user
     * @return void
     */
    public function encodePassword(User $user):void
    {
        if (empty($user->getPlainPassword())){
            return;
        }else{
            $user->setPassword(
                $this->hasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
        }
    }

}

