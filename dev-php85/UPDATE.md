# PHP 8.5 Migration — `ctw/ctw-middleware-pagecache`

- **Branch:** `php85` (cut from `master`)
- **Runtime:** PHP 8.3.31 → **8.5.7**
- **PHPUnit:** 12 → **13.2.1**
- **Status:** ✅ done

Full-page-caching PSR-15 middleware for Mezzio. This is the **heaviest** of the
middleware migrations: it declares `laminas-diactoros`, `laminas-cache` (plus the
filesystem storage adapter) and two `mezzio/*` packages directly. Under PHP 8.5
the original `composer update -W` failed on `laminas/laminas-diactoros` 2.x
(caps PHP at `~8.3.0`), and `laminas-cache` 3.x emits genuinely
PHP-8.5-specific `SplObjectStorage` deprecations. Beyond the shared diactoros
bump it carries two **first-party** findings: a fatal BC break in the
`laminas-cache` 4.x `Serializer` storage plugin, and two `src/` type errors that
the stricter `laminas-cache` 4.x annotations surface under PHPStan.

---

## Audit checklist

### `test/AbstractCase.php` — first-party (fatal / BC)

- [x] **(fatal) `test/AbstractCase.php`** — laminas-cache 4.x no longer allows `new Serializer()` for the `Serializer` storage plugin; it requires a serializer-adapter plugin manager from `laminas/laminas-serializer`. The no-arg constructor throws under 4.x.
  **Fix:** added `laminas/laminas-serializer: ^3.0` to `require` (3.3.0; needed at runtime to serialize the cached response array on the Filesystem-backed adapter), and `test/AbstractCase.php` now builds the plugin as `new Serializer(new AdapterPluginManager(new ServiceManager()))`. The `ExceptionHandler` plugin still uses its no-arg constructor + `getOptions()->setThrowExceptions(false)` (unchanged in 4.x). No `src/` change was needed for this — `src/` consumes the storage adapter from the container and uses only the stable `getItem()` / `setItem()` API.

### `src/IdGenerator` — first-party (type / static analysis)

- [x] **(tooling) `src/IdGenerator/AbstractIdGenerator.php:23`** — PHPStan `argument.type`: `implode()` `$array` expects `array<string>`, `array<mixed>` given. The untyped `$vars` array carries `UriInterface` objects, ints, strings and nulls, so the `array_filter()` result was not provably a string array.
  **Fix:** `getHash()` now iterates `$vars`, keeps only scalar / `Stringable` values and casts each to string before `implode()`; the parameter is annotated `@param array<mixed>` and the return `@return non-empty-string` (PHPStan infers `hash('sha256', …)` as a non-empty string).
- [x] **(tooling) `src/PageCacheMiddleware.php:23` & `:29`** — PHPStan `argument.type`: `AbstractAdapter::getItem()` / `setItem()` `$key` expects `non-empty-string`, `string` given. The laminas-cache 4.x adapter tightened the key parameter to `non-empty-string`, while `IdGeneratorInterface::generate()` only promised `string`.
  **Fix:** annotated `IdGeneratorInterface::generate()` and the three concrete generators (`FullUriIdGenerator`, `RequestUriGenerator`, `IdGeneratorExample`) with `@return non-empty-string`, which propagates the non-empty SHA-256 hash from `getHash()` to the `$cacheId` used at the `getItem()` / `setItem()` call sites. No suppression, casts or baseline entries used. `PageCacheMiddleware.php` runtime logic is unchanged from `master`.

### Vendor (cleared by dependency bumps)

- [x] **(deprecation) `vendor/laminas/laminas-cache` `AbstractAdapter.php`** — `SplObjectStorage::attach()` (271), `::contains()` (279, 290) and `::detach()` (292) are deprecated since PHP 8.5; the 3.x `AbstractAdapter` still called them.
  **Fix:** not fixable in this repo's `src/`. Cleared by `laminas/laminas-cache ^4.3` (4.3.0), whose `AbstractAdapter` uses the non-deprecated offset API. 4.3 is also the first cache line that supports PHP 8.5.
- [x] **(deprecation) `vendor/middlewares/utils`** — five "implicitly nullable parameter" deprecations (`Dispatcher::run()` `$request`; `Factory::createUploadedFile()` `$size`/`$filename`/`$mediaType`; `CallableHandler::__construct()` `$responseFactory`).
  **Fix:** not fixable in this repo's `src/`. Cleared by `middlewares/utils` v4 (4.0.2) via `ctw/ctw-middleware: dev-php85`.

### Tooling

- [x] **(tooling) PHPUnit 12 → 13.** Suite runs green on PHPUnit 13.2.1.
  **Fix:** `phpunit/phpunit ^12 → ^13`, `ctw/ctw-qa → dev-php85`, `phpunit.xml.dist` schema → 13.2.
- [x] **(tooling) PHPStan `missingType.*` unmatched-ignore.** Resolved centrally in `ctw/ctw-qa` (`reportUnmatchedIgnoredErrors: false`) via `ctw/ctw-qa: dev-php85`.

---

## composer.json & CI

- [x] `require.php`: `^8.3` → **`^8.5`**.
- [x] `laminas/laminas-diactoros`: `^2.11` → **`^3.0`** (3.8.0) — clears the diactoros 2.x PHP cap.
- [x] `laminas/laminas-cache`: `^3.1` → **`^4.3`** (4.3.0) — first cache line supporting PHP 8.5; clears the `SplObjectStorage` deprecations and brings the new Serializer-plugin API.
- [x] `laminas/laminas-cache-storage-adapter-filesystem`: `^2.0` → **`^3.2`** (3.2.0) — matching filesystem adapter major.
- [x] `laminas/laminas-serializer`: **added `^3.0`** (3.3.0) — runtime dependency for the `Serializer` storage plugin under laminas-cache 4.x.
- [x] `ctw/ctw-middleware`: `^4.0` → **`dev-php85`** — brings diactoros ^3 + middlewares/utils ^4, and **widens `laminas/laminas-servicemanager` to `^4.5` in `ctw/ctw-middleware`** (laminas-cache 4.3 requires servicemanager `^4.5`, which the base package's old `^3.12` pin made unsatisfiable). Installs servicemanager 4.5.1. Re-tag to a stable release before merge.
- [x] `ctw/ctw-qa`: `^5.0` → **`dev-php85`**. Re-tag before merge.
- [x] `phpunit/phpunit`: `^12.0` → **`^13.0`** (installs 13.2.1).
- [x] `phpunit.xml.dist`: schema → 13.2.
- [x] `.github/workflows/tests.yml`: matrix → **PHP 8.5 only** (`php: [ '8.5' ]`).

> `mezzio/mezzio-fastroute ^3.1` and `mezzio/mezzio-session ^1.4` were left as-is —
> they resolve cleanly under PHP 8.5 once diactoros is on ^3, so no constraint bump
> was required.

---

## Final audit (PHP 8.5.7)

- [x] `php -v` → **PHP 8.5.7** (cli).
- [x] `composer update -W` → **clean** (rc=0; no security advisories). `laminas/laminas-json` reports as abandoned — a pre-existing transitive note, not a PHP 8.5 blocker.
- [x] `phpunit --no-coverage --display-deprecations --display-warnings --display-notices --display-errors` → **6 tests, 6 assertions, 0 issues** (the MISS → serialize → store → retrieve → unserialize → HIT round-trip passes) under PHPUnit 13.2.1 / PHP 8.5.7.
- [x] PHPStan → **clean** (no issues found) after the `IdGenerator` annotations above.
