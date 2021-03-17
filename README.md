# ctw/ctw-middleware-page-cache

[![Build Status](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-page-cache/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-page-cache/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-page-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-page-cache/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-page-cache/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-page-cache)

:warning: **This component is under heavy development. Do not (yet) use it in a production environment.**

```bash
$ composer require ctw/ctw-middleware-page-cache
```
Intro

[middlewares/utils](https://packagist.org/packages/middlewares/utils) provides utility classes for working with PSR-15.

## Installation

Install the middleware using Composer:

```bash
$ composer require ctw/ctw-middleware-page-cache
```

## Standalone Example

```php
// standalone example
```

## Example in [Mezzio](https://docs.mezzio.dev/)

The middleware has been extensively tested in Mezzio.

After using Composer to install, simply make the following changes to your application's configuration.

In `config/config.php`:

```php
$providers = [
    // [..]
    \Ctw\Middleware\PageCacheMiddleware\ConfigProvider::class,
    // [..]    
];
```

In `config/pipeline.php`:

```php
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // [..]
    $app->pipe(PageCacheMiddleware::class);
    // [..]
};
```

