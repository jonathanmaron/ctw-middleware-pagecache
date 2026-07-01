# Package "ctw/ctw-middleware-pagecache"

[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-pagecache/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-pagecache)
[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-pagecache/actions/workflows/ci.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-pagecache/actions/workflows/ci.yml)

PSR-15 middleware providing full page caching for Mezzio applications with configurable caching strategies and cache ID generators.

## Introduction

### Why This Library Exists

Dynamic web pages that don't change frequently can benefit enormously from full page caching. Instead of executing PHP code, querying databases, and rendering templates for every request, cached responses are served directly from storage.

This middleware provides application-level page caching with:

- **Strategy-based caching**: Define which routes should be cached using route name matching
- **Pluggable ID generators**: Generate cache keys from full URIs, request URIs, or custom logic
- **Laminas Cache integration**: Uses Laminas Cache adapters for flexible storage backends
- **Response serialization**: Caches complete HTTP responses including headers and body
- **Cache status headers**: Adds `X-Page-Cache: Hit` or `Miss` header for debugging

### Problems This Library Solves

1. **Unnecessary computation**: Static or semi-static pages re-render on every request
2. **Database load**: Repeated queries for unchanged content strain database resources
3. **Response latency**: Complex pages with multiple service calls have high response times
4. **Server scaling costs**: Without caching, handling traffic spikes requires more servers
5. **Inconsistent caching**: Ad-hoc caching implementations vary across the application

### Where to Use This Library

- **Content-heavy sites**: Blogs, news sites, documentation pages
- **Marketing pages**: Landing pages, product pages, company information
- **Semi-static content**: Pages that change infrequently (hourly, daily)
- **High-traffic routes**: Cache popular endpoints to handle traffic spikes
- **Read-heavy applications**: Sites where reads vastly outnumber writes

### Design Goals

1. **Route-based strategy**: Cache specific routes rather than all requests
2. **Transparent operation**: Cached responses are indistinguishable from fresh ones
3. **Storage agnostic**: Works with any Laminas Cache storage adapter
4. **Configurable TTL**: Set expiration times per cache backend configuration
5. **Debug visibility**: `X-Page-Cache` header shows cache hit/miss status

## Requirements

- PHP 8.3 or higher
- ctw/ctw-middleware ^4.0
- laminas/laminas-cache ^3.1
- laminas/laminas-cache-storage-adapter-filesystem ^2.0
- mezzio/mezzio-fastroute ^3.1
- mezzio/mezzio-session ^1.4

## Installation

Install by adding the package as a [Composer](https://getcomposer.org) requirement:

```bash
composer require ctw/ctw-middleware-pagecache
```

## Usage Examples

### Basic Pipeline Registration (Mezzio)

```php
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;

// In config/pipeline.php - place after routing, before dispatch
$app->pipe(PageCacheMiddleware::class);
```

### ConfigProvider Registration

```php
// config/config.php
return [
    // ...
    \Ctw\Middleware\PageCacheMiddleware\ConfigProvider::class,
];
```

### Cache Status Header

Every response includes the cache status:

```http
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
X-Page-Cache: Hit
```

| Header Value | Description |
|--------------|-------------|
| `Hit` | Response served from cache |
| `Miss` | Response generated fresh, then cached |

### Caching Strategies

#### RouteNameStrategy

Cache pages based on route names:

```php
use Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy\RouteNameStrategy;

// Configure routes to cache
$strategy = new RouteNameStrategy();
$strategy->setNames([
    'home',
    'about',
    'blog.list',
    'product.detail',
]);
```

### Cache ID Generators

#### FullUriIdGenerator

Generates cache IDs from the complete URI including scheme, host, path, and query string:

```php
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGenerator;

// https://example.com/page?id=1 → hashed cache ID
```

#### RequestUriGenerator

Generates cache IDs from the request URI path only:

```php
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGenerator;

// /page?id=1 → hashed cache ID
```

### Enabling/Disabling Cache

The cache can be enabled or disabled at runtime:

```php
$middleware->setEnabled(true);  // Enable caching
$middleware->setEnabled(false); // Disable caching (bypass)
```

### Cache Flow

```
Request → Middleware → Strategy.shouldCache()?
                       ├─ No → Handler → Response
                       └─ Yes → Cache.get(id)?
                                ├─ Hit → Cached Response
                                └─ Miss → Handler → Cache.set(id) → Response
```

### Response Header Example

```bash
curl -I https://example.com/

# First request (cache miss):
# X-Page-Cache: Miss

# Subsequent requests (cache hit):
# X-Page-Cache: Hit
```
