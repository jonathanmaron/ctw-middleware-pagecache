# PHP 8.5.7 Upgrade — `ctw/ctw-middleware-pagecache`

- **Branch:** `php85` (cut from `master`)
- **Runtime:** PHP 8.3.31 → **8.5.7**
- **Date:** 2026-06-25

This is a **TODO list** of the changes required for this package to run cleanly
under PHP 8.5.7. Boxes are intentionally left unchecked.

---

## ✅ Fully green (diactoros blocker + laminas-cache 3→4 migration done)

> Supersedes the "❌ FAILS" analysis in §1.

`composer.json` changes (so `composer update -W` resolves under PHP 8.5):

- [x] `laminas/laminas-diactoros` `^2.11` → **`^3.0`** (3.8.0).
- [x] `laminas/laminas-cache` `^3.1` → **`^4.3`** (4.3.0 — first cache line that
  supports PHP 8.5; clears the `SplObjectStorage` deprecations in §2b).
- [x] `laminas/laminas-cache-storage-adapter-filesystem` `^2.0` → **`^3.2`** (3.2.0).
- [x] `ctw/ctw-middleware` `^4.0` → **`dev-php85`** — note this also required
  widening `laminas/laminas-servicemanager` to `^4.5` **in `ctw/ctw-middleware`**
  (laminas-cache 4.3 requires servicemanager `^4.5`, which the base package's old
  `^3.12`-only pin made unsatisfiable).

`composer update -W` is now **green** (rc=0).

- [x] **laminas-cache 3→4 API migration — done (first-party).** laminas-cache 4.x
  no longer lets the `Serializer` storage plugin be instantiated with `new
  Serializer()`; it requires a serializer-adapter plugin manager from
  `laminas/laminas-serializer`. Applied:
  - Added **`laminas/laminas-serializer: ^3.0`** to `require` (3.3.0; needed at
    runtime to serialize the cached response array on a Filesystem-backed cache).
  - `test/AbstractCase.php` now builds the plugin as
    `new Serializer(new AdapterPluginManager(new ServiceManager()))`.
  - `ExceptionHandler` still uses its no-arg constructor + `getOptions()
    ->setThrowExceptions(false)` (unchanged in 4.x).
  - `src/` needed no changes — it consumes the storage adapter from the container
    and uses only stable `getItem()`/`setItem()` APIs.
- [x] **PHPUnit 13:** `phpunit/phpunit` → `^13.0`, `ctw/ctw-qa` → `dev-php85`,
  phpunit.xml schema → 13.2.
- [x] **§3 done:** the shared PHPStan `missingType.*` unmatched-ignore is fixed
  centrally in `ctw/ctw-qa` (`reportUnmatchedIgnoredErrors: false`).

**Result:** `phpunit --no-coverage` → **6 tests, 6 assertions, 0 issues** (the
MISS→serialize→store→retrieve→unserialize→HIT round-trip passes) under PHPUnit
13.2.1 / PHP 8.5.7. Re-tag the `ctw/*` deps to stable before merge.

> ⚠️ **Largest set of dependency bumps required** — `laminas-diactoros`,
> `laminas-cache` (+ filesystem adapter) and two `mezzio/*` packages are all
> declared directly, and `laminas-cache` 3.x emits genuinely PHP-8.5-specific
> deprecations (§2b).

Detection commands used:

```bash
composer update -W
php vendor/bin/phpunit --no-coverage --display-deprecations --display-warnings --display-notices --display-errors
composer rector      # rector --dry-run
composer phpstan
```

---

## 1. `composer update -W` — ❌ FAILS (hard blocker, direct + transitive)

```
Problem 1
  - Root composer.json requires laminas/laminas-diactoros ^2.11
  - laminas/laminas-diactoros[2.11 ... 2.26] require php ~8.0 || ~8.1 || ~8.2 || ~8.3
    -> your php version (8.5.7) does not satisfy that requirement.
```

`laminas/laminas-diactoros` 2.x caps PHP at `~8.3.0`. Declared **directly**
(`^2.11`) and transitively via `ctw/ctw-middleware ^4.0`.

- [ ] **`composer.json`** — bump `laminas/laminas-diactoros` `^2.11` → **`^3.0`**.
- [ ] **`composer.json`** — bump `laminas/laminas-cache` `^3.1` → **`^4.0`** and
  `laminas/laminas-cache-storage-adapter-filesystem` `^2.0` → the matching
  `^3.0` (the 3.x cache line is the one that drops the deprecated
  `SplObjectStorage` calls in §2b and supports PHP 8.4/8.5).
- [ ] **`composer.json`** — bump the Mezzio constraints:
  `mezzio/mezzio-fastroute ^3.1` and `mezzio/mezzio-session ^1.4` predate PHP
  8.4/8.5; move to the current majors and re-resolve. **These caps are hidden
  now** — composer aborts on Diactoros first; re-run `composer update -W` after
  the Diactoros bump to surface them.
- [ ] **`composer.json`** — `psr/*` constraints may need widening for the new
  majors (e.g. `psr/http-message ^1.1 || ^2.0`).
- [ ] **Blocked on `ctw/ctw-middleware`** — bump its constraint once its PHP 8.5
  release is published (`ctw-middleware/dev-php85/UPDATE.md` §1).

> §2 was captured against the existing (master) lockfile because the update
> aborts. Re-run detection after the tree updates — more Mezzio/cache
> deprecations may appear.

---

## 2a. PHP 8.5 runtime deprecations — third-party (`middlewares/utils`)

The "implicitly nullable parameter" deprecation. **Not fixable in this repo's
`src/`.**

| Location | Method / parameter |
| --- | --- |
| `vendor/middlewares/utils/src/Dispatcher.php:21` | `Dispatcher::run()` `$request` |
| `vendor/middlewares/utils/src/Factory.php:88` | `Factory::createUploadedFile()` `$size` |
| `vendor/middlewares/utils/src/Factory.php:90` | `Factory::createUploadedFile()` `$filename` |
| `vendor/middlewares/utils/src/Factory.php:91` | `Factory::createUploadedFile()` `$mediaType` |
| `vendor/middlewares/utils/src/CallableHandler.php:25` | `CallableHandler::__construct()` `$responseFactory` |

## 2b. PHP 8.5 runtime deprecations — `laminas/laminas-cache` (PHP-8.5-specific)

`SplObjectStorage`'s array-ish methods are **deprecated since PHP 8.5**, and
`laminas-cache` 3.x still calls them in
`vendor/laminas/laminas-cache/src/Storage/Adapter/AbstractAdapter.php`:

| Location | Deprecated call → replacement |
| --- | --- |
| `AbstractAdapter.php:271` | `SplObjectStorage::attach()` → `offsetSet()` |
| `AbstractAdapter.php:279` | `SplObjectStorage::contains()` → `offsetExists()` |
| `AbstractAdapter.php:290` | `SplObjectStorage::contains()` → `offsetExists()` |
| `AbstractAdapter.php:292` | `SplObjectStorage::detach()` → `offsetUnset()` |

- [ ] **Not fixable in this repo's `src/`** — it is vendor code. Resolved by the
  `laminas/laminas-cache ^4.0` bump in §1 (the 4.x line uses the non-deprecated
  `ArrayObject`/offset API). Confirm after updating.

> Both §2a and §2b are third-party. **No first-party `src/` deprecations** were
> detected in this package.

---

## 3. QA tooling issues

- [ ] **PHPStan unmatched ignore pattern** (`missingType.generics`) — fix
  centrally in **`ctw/ctw-qa`** (`ctw-qa/dev-php85/UPDATE.md` §3). PHPStan
  currently reports **1 error**, this spurious one only.

---

## 4. Notes (non-blocking)

- Run locally with `--no-coverage` (no Xdebug/PCOV here). Not a PHP 8.5 issue.

---

## 5. Verification snapshot (current state on `php85`)

| Check | Result |
| --- | --- |
| `composer update -W` | ❌ fails — direct + transitive `laminas-diactoros` 2.x (§1); cache/Mezzio caps still hidden |
| PHPUnit (`--no-coverage`, stale deps) | 6 tests, 6 assertions, **7 deprecations** (5× `middlewares/utils` §2a + `SplObjectStorage`/`laminas-cache` §2b) |
| Rector (dry-run) | ✅ no changes proposed |
| PHPStan | ❌ 1 error (shared unmatched-ignore, §3) |

**First-party work needed here:** the `composer.json` constraint bumps in §1
(Diactoros + laminas-cache + Mezzio). All runtime deprecations are third-party
and clear via those bumps; no `src/` edits identified.
