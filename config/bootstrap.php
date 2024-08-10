<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Services\UserService;
use App\Services\AuthService;
use UMA\DIC\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container(require __DIR__ . '/../config/settings.php');

$container->set(EntityManager::class, static function (Container $c): EntityManager {
    $settings = $c->get('settings');

    $cache = $settings['doctrine']['dev_mode'] ?
        new ArrayAdapter() :
        new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']);

    $config = ORMSetup::createAttributeMetadataConfiguration(
        $settings['doctrine']['metadata_dirs'],
        $settings['doctrine']['dev_mode'],
        null,
        $cache
    );

    $connection = DriverManager::getConnection($settings['doctrine']['connection']);

    return new EntityManager($connection, $config);
});

$container->set('UserService', static function (Container $c) {
    return new UserService($c->get(EntityManager::class));
});

$container->set('AuthService', static function (Container $c) {
    return new AuthService($c->get(EntityManager::class));
});


return $container;