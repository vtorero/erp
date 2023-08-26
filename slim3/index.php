<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection(require 'config/database.php');

$capsule->setAsGlobal();

$capsule->bootEloquent();


$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ]
];


$app = new \Slim\App();

$app->get('/users', 'UserController:getUsers');


$app->run();