<?php
declare(strict_types=1);

use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

require __DIR__ . '/../vendor/autoload.php';

$container = new ServiceManager();
