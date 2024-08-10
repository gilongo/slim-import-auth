<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use App\User\Domain\User;
use App\UserCreateRequest;
use DateTime;

final class UserService
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function signUp(UserCreateRequest $request): User
    {
        $newUser = new User(
            $request->firstName,
            $request->lastName,
            $request->email,
            $request->username,
            password_hash($request->password, PASSWORD_BCRYPT),
            DateTime::createFromFormat('Y-m-d', $request->birthday)
        );

        try {
            $this->em->persist($newUser);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

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