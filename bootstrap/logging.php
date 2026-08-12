<?php

declare(strict_types=1);

use App\Logging\LoggerFactory;
use App\Logging\RequestContext;

RequestContext::id();
return LoggerFactory::create();
