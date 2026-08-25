# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A small WordPress plugin ("Matomo Server Side Tracking", text domain `lap-matomo-http-api`) that sends page views to Matomo from PHP on each front-end request, with an optional client-side JS snippet. Requires PHP 7.2+, WordPress 5.2+.

## Commands

There is no build step, package manager, test suite, or linter configured. Plain PHP files are loaded directly by WordPress.

- Syntax check a file: `php -l send-stats.php`
- Syntax check everything: `for f in *.php; do php -l "$f"; done`
- Manual testing requires a WordPress install with the plugin directory symlinked/copied into `wp-content/plugins/`. Apache/MariaDB do not start at boot on this dev machine — ask before starting them.

## Architecture

Four plugin files plus a vendored library:

- `lap-matomo-http-api.php` — plugin header, includes the two files below, adds a "Settings" link on the Plugins screen.
- `options-page.php` — Settings API registration. Everything is stored in a single option array `lap_matomo_http_api_options` with keys `url`, `idsite`, `tokenAuth`, `debugging`, `enable_js`, `track_admins`. Checkboxes are stored as the string `'1'` (or empty); code compares with `=== '1'`. `lap_matomo_http_api_validate_options()` is the sanitize callback and is the only place new option keys get persisted — add new keys there too or they are silently dropped.
- `send-stats.php` — front-end tracking. Two hooks:
  - `wp_head` → `lap_matomo_http_api_js()` prints the Matomo JS snippet when `enable_js` is on (cookies disabled, heartbeat timer, link tracking).
  - `wp_body_open` → `lap_matomo_http_api_head()` performs the server-side `doTrackPageView()` via `MatomoTracker` using `tokenAuth` (needed so Matomo accepts the visitor IP override). Resolves the client IP from `X-Forwarded-For` (first entry) then `CF-Connecting-IP`, falling back to `REMOTE_ADDR`. Sets the Matomo user ID to the WP display name for logged-in users. Request timeout is 2 s; failures are caught and `error_log`ged so page rendering never breaks.
  - Both hooks skip admin-capable users (`manage_options`) unless `track_admins` is on. Debug output (`<details>` block, printed after the request) is only shown to admins with `debugging` on; its `Status` line reads OK when Matomo returns its 1×1 GIF, otherwise the error text. Non-GIF responses are always `error_log`ged.
- `MatomoTracker.php` / `PiwikTracker.php` — the upstream [matomo-php-tracker](https://github.com/matomo-org/matomo-php-tracker) library, vendored unmodified (BSD licence). Do not edit; update by replacing with a newer upstream copy. It is only included inside `lap_matomo_http_api_head()`, not globally.

All functions are prefixed `lap_matomo_http_api_`; keep that convention for new global functions. Every file starts with an `ABSPATH` guard.
