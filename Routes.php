<?php

$routes->post('api/login', 'AuthController::login');
$routes->group('api', ['filter' => 'authjwt'], function($routes) {
    $routes->get('profile', 'UserController::profile');
});
