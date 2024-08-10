<?php 

namespace App;

use Doctrine\ORM\EntityManager;

class UserCreateRequest
{
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $username;
    public string $password;
    public string $birthday;

    public function __construct(string $firstName, string $lastName, string $email, string $username, string $password, string $birthday)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->birthday = $birthday;
    }
}