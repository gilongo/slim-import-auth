<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'pgsql',
            'host' => 'db',
            'name' => 'slim_app',
            'user' => 'slim_user',
            'pass' => 'secret_password',
            'port' => '5432',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
