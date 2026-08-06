<?php

use App\Routes\Router;

$container = require __DIR__ . '/../bootstrap/app.php';

Router::setContainer($container);

require_once __DIR__ . '/../routes/web.php';

Router::dispatch();
