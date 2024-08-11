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

    public function getAll($params = []): array
    {
        $filters = [
            'firstName' => $params['firstName'] ?? null,
            'lastName' => $params['lastName'] ?? null,
            'startDate' => $params['startDate'] ?? null,
            'endDate' => $params['endDate'] ?? null
        ];

        $query = $this->em->createQueryBuilder()
        ->select('u')
        ->from('\App\User\Domain\User', 'u');

        if(!empty($filters['firstName'])) {
            $query = $query
            ->where('u.firstName = :firstName')
            ->setParameter('firstName', $filters['firstName']);
        }

        if(!empty($filters['lastName'])) {
            $query = $query
            ->andWhere('u.lastName = :lastName')
            ->setParameter('lastName', $filters['lastName']);
        }

        if(!empty($filters['startDate'])) {
            $query = $query
            ->andWhere('u.birthday >= :startDate')
            ->setParameter('startDate', $filters['startDate']);
        }

        if(!empty($filters['endDate'])) {
            $query = $query
            ->andWhere('u.birthday <= :endDate')
            ->setParameter('endDate', $filters['endDate']);
        }

        $users = $query->getQuery()->getResult();

        if(!empty($users)) {
            foreach($users as &$user) {
                $user = $user->toArray();
            }
            return $users;
        }

        return [];
    }
}