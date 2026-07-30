# AGENTS.md

## Cursor Cloud specific instructions

This repo is the **KAYAN WordPress theme** (`kayan-theme/kayan-theme/`), not a standalone app.
To develop/run it you need WordPress + PHP + MariaDB + a rewrite-capable web server. The
system packages (PHP 8.3, MariaDB, WP-CLI) plus a local WordPress install and its database
are baked into the VM snapshot, so the update script only re-links the theme.

### Layout of the running environment
- WordPress core lives at `~/wordpress` (outside the repo). Its DB and content persist in the snapshot.
- The theme is a symlink: `~/wordpress/wp-content/themes/kayan-theme` → `/workspace/kayan-theme/kayan-theme`, so edits in the repo are served live (plain PHP/CSS/JS — there is no build step or hot reload; just refresh the browser).
- Admin login: `admin` / `admin123` at `http://localhost:8080/wp-admin`.

### Starting the services (NOT in the update script — start these each session)
1. Start MariaDB (no systemd in this container):
   `sudo mkdir -p /run/mysqld && sudo chown mysql:mysql /run/mysqld && sudo mariadbd-safe &`
   (verify with `sudo mysqladmin ping`).
2. Start the WordPress dev server (PHP built-in server + router for pretty permalinks):
   `cd ~/wordpress && php -S 0.0.0.0:8080 -t ~/wordpress router.php`
   (`~/wordpress/router.php` routes non-file requests to `index.php`). Then open `http://localhost:8080`.

### Non-obvious gotchas discovered during setup
- **MariaDB socket path mismatch:** PHP's default mysqli socket is `/var/run/mysqld/mysqld.sock`, but MariaDB here listens on `/run/mysqld/mysqld.sock` (these are separate dirs in this container). `wp-config.php` therefore sets `DB_HOST` to `localhost:/run/mysqld/mysqld.sock`. Keep that form if you recreate the config.
- **WP-CLI cannot boot this site while the KAYAN theme is active.** WP-CLI loads WordPress inside a method scope, so the theme's top-level global `$ThemeTree` (set in `functions.php`) is not a real global, and the `init` hook fatals in `components/packs/taxonomies/setup.php` (`Call to a member function AddTaxonomy() on null`). This is specific to WP-CLI's loader — normal web requests run `index.php` at global scope and work fine. Use the browser admin or direct SQL (`mysql -u wp -pwppass wordpress`) for content changes; do not rely on `wp` subcommands that fire `init`.
- **Flushing rewrite rules:** because `wp rewrite flush` hits the fatal above, regenerate permalinks by deleting the option instead: `DELETE FROM wp_options WHERE option_name='rewrite_rules';` then load any front-end URL (WordPress rebuilds and saves the rules on the next request).
- **WordPress version:** the theme is "Tested up to 6.7"; the pinned core is 6.7.2. WP 7.0+ changed theme-`functions.php` loading and breaks this theme, so do not upgrade core.
- **Known pre-existing theme bug (not an environment problem):** the booking wizard's final submit posts a `services` form field, which collides with the `services` custom-post-type public query var (WordPress reads public query vars from `$_POST`). The request 302-redirects to the home page and the booking is never saved. The wizard UI itself (service selection → customer data → date/time → review with live subtotal/tax/total) works.

### Tests
- Static smoke tests (no WordPress DB needed): `php tests/kayan-smoke.php` from the repo root (expects `32 passed, 0 failed`).
