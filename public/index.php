<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;

require __DIR__ . '/../vendor/autoload.php';

// Set up the Container
$container = require __DIR__ . '/../config/bootstrap.php';
AppFactory::setContainer($container);

$app = AppFactory::create();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$app->get('/users', function (Request $request, Response $response, $args) use ($container) {
    $userService = $container->get('UserService');
    $allUsers = $userService->getAll();
    $response
        ->getBody()
        ->write(json_encode($allUsers));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/users', function (Request $request, Response $response, $args) use ($container) {
    $userService = $container->get('UserService');
    $newUser = $userService->signUp("test@palleAAAAA.com");
    $response->getBody()->write('User created: ' . $newUser->getEmail());
    return $response;
});

$app->run();