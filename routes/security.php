<?php

use App\Routes\Router;

Router::get(
    uri: '/login/form',
    callback: 'AuthController@login'
);

Router::post(
    uri: '/logout',
    callback: 'AuthController@logout'
);
