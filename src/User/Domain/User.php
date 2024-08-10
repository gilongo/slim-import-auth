<?php

namespace App\User\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\GeneratedValue;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

use DateTime;


#[Entity, Table(name: 'users')]
final class User
{
    #[Id, Column(type: 'string', unique: true)]
    private string|null $id = null;

    #[Column(name: 'first_name', type: 'string')]
    private string $firstName;

    #[Column(name: 'last_name', type: 'string')]
    private string $lastName;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;

    #[Column(type: 'string', nullable: false)]
    private string $username;

    #[Column(type: 'string', nullable: false)]
    private string $password;

    #[Column(name: 'birthday', type: 'date')]
    private DateTime|null $birthday = null;

    public function __construct(string $firstName, string $lastName, string $email, string $username, string $password, ?DateTime $birthday)
    {
        $this->id = (Uuid::v4())->toString();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->birthday = $birthday;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getBirthday(): DateTime
    {
        return $this->birhtday;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => $this->birthday ? $this->birthday->format('Y-m-d') : null
        ];
    }
}

