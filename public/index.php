<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;
use Respect\Validation\Validator as v;

use App\UserCreateRequest;

require __DIR__ . '/../vendor/autoload.php';

//dotenv
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set up the Container
$container = require __DIR__ . '/../config/bootstrap.php';
AppFactory::setContainer($container);

$app = AppFactory::create();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Auth Middleware
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "headers" => ["Authorization"],
    "secret" => $_ENV['JWT_SECRET'],
    "ignore" => ["/auth/login"],
    "error" => function ($response, $arguments) {
        $data = [
            'status' => 'error',
            'message' => $arguments["message"]
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(401);
    }
]));

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$app->get('/users', function (Request $request, Response $response, $args) use ($container) {
    $userService = $container->get('UserService');

    // params to array
    $queryParamsStaging = $request->getUri()->getQuery();
    $queryParams = [];
    if (!empty($queryParamsStaging)) {
        foreach (explode('&', $request->getUri()->getQuery()) as $value) {
            $param = explode('=', $value);
            $queryParams[$param[0]] = $param[1];
        }
    }

    try {
        // validation
        $dateValidator = v::date();
        $stringValidator = v::stringType();

        if (!empty($queryParams['firstName']) && !$stringValidator->validate($queryParams['firstName'])) {
            throw new \Exception('Invalid first name', 400);
        }

        if (!empty($queryParams['lastName']) && !$stringValidator->validate($queryParams['lastName'])) {
            throw new \Exception('Invalid last name', 400);
        }

        if (!empty($queryParams['birthday'])) {
            $dates = explode('%7C', $queryParams['birthday']);

            if (isset($dates[0])) $queryParams['startDate'] = $dates[0];
            if (isset($dates[1])) $queryParams['endDate'] = $dates[1];

            if (!empty($queryParams['startDate']) && !$dateValidator->validate($queryParams['startDate'])) {
                throw new \Exception('Invalid start date', 400);
            }

            if (!empty($queryParams['endDate']) && !$dateValidator->validate($queryParams['endDate'])) {
                throw new \Exception('Invalid end date', 400);
            }

            unset($queryParams['birthday']);
        }

        $allUsers = $userService->getAll($queryParams);
        $response
            ->getBody()
            ->write(json_encode(['users' => $allUsers]));
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode());
    }
});

$app->post('/users', function (Request $request, Response $response, $args) use ($container) {
    $userService = $container->get('UserService');
    $body = $request->getParsedBody();

    $userCreationRequest = new UserCreateRequest(
        $body['firstName'],
        $body['lastName'],
        $body['email'],
        $body['username'],
        $body['password'],
        $body['birthday']
    );

    try {
        $newUser = $userService->signUp($userCreationRequest);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $response->getBody()->write(json_encode(['user' => $newUser->toArray()]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/auth/login', function (Request $request, Response $response, $args) use ($container) {
    $authService = $container->get('AuthService');

    $body = $request->getParsedBody();

    try {
        $tokenData = $authService->login($body['username'], $body['password']);
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode());
    }
    
    $response->getBody()->write(json_encode($tokenData));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();