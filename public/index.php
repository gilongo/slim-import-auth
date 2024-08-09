<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use UMA\DIC\Container;
use App\Services\UserService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

// Set up the Doctrine ORM
$isDevMode = true;
$config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../src/Domain'], $isDevMode);
$connectionParams = [
    'dbname' => 'slim_app',
    'user' => 'slim_user',
    'password' => 'secret_password',
    'host' => 'db', 
    'driver' => 'pdo_pgsql',
];
$connection = DriverManager::getConnection($connectionParams, $config);

// Create the EntityManager instance
$entityManager = new EntityManager($connection, $config);

// Create the container
$container = new Container();
$container->set('doctrine.orm.default_entity_manager', $entityManager);
$container->set(UserService::class, function (Container $container) {
    return new UserService($container->get('doctrine.orm.default_entity_manager'));
});

// Create the Slim application
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$app->post('/users', function (Request $request, Response $response, $args) use ($container) {
    $userService = $container->get(UserService::class);
    $newUser = $userService->signUp("test@palle.com");
    $response->getBody()->write('User created: ' . $newUser->getEmail());
    return $response;
});

$app->run();