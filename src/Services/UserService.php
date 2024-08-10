<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use App\User\Domain\User;

final class UserService
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function signUp(string $email): User
    {
        $newUser = new User($email);

        $this->em->persist($newUser);
        $this->em->flush();

        return $newUser;
    }

    public function getAll(): array
    {
        $users = $this->em->getRepository(User::class)->findAll();
        if(!empty($users)) {
            foreach($users as &$user) {
                $user = $user->toArray();
            }
            return $users;
        }

        return [];
    }
}