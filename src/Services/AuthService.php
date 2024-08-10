<?php 

namespace App\Services;

use Doctrine\ORM\EntityManager;
use App\User\Domain\User;
use Firebase\JWT\JWT;
use Tuupola\Base62;
use DateTime;

final class AuthService
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function login(string $username, string $password): array
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);

        if($user === null || !password_verify($password, $user->getPassword())) {
            throw new \Exception('Wrong credentials', 401);
        } 


        $now = new DateTime();
        $future = new DateTime("now +2 hours");

        $jti = (new Base62)->encode(random_bytes(16));

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => $jti,
            "sub" => $user->getId(),
        ];

        $secret = "supersecretkeyyoushouldnotcommittogithub";
        $token = JWT::encode($payload, $secret, "HS256");

        $data["token"] = $token;
        $data["expires"] = $future->getTimeStamp();


        // return JWT
        return $data;
    }
}