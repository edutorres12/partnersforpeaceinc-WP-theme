# Drop-ins — branded server-error page

WordPress "drop-ins" are special files that live in **`wp-content/`** (not in
the theme) and override built-in behavior. They are **not** loaded automatically
from the theme directory — you copy them into place once per environment.

## `php-error.php` — the branded 500 / critical-error page

When a fatal PHP error occurs, WordPress normally shows a plain white
_"There has been a critical error on this website"_ page. This drop-in replaces
that with the Seasons of You Therapy branded page (`dropins/php-error.php`).

It runs **after** WordPress has crashed, so it is fully self-contained: inline
CSS, no theme functions, no required external assets. Brand fonts load from
Google Fonts when reachable and degrade gracefully when they are not.

### Why this isn't a theme template

A theme template (like `404.php`) only renders while WordPress is running. A
`500` means WordPress itself failed to boot — so there is no header, footer, or
stylesheet to render a theme file. That is exactly why the 500 page has to be a
standalone, dependency-free file.

### Install (once per environment)

Copy the file to `wp-content/`:

```bash
cp wp-content/themes/soywd/dropins/php-error.php wp-content/php-error.php
```

On Hostinger you can also do this from **File Manager**: copy
`dropins/php-error.php` from the theme folder into `wp-content/`.

That's it — WordPress picks it up automatically. To preview it without breaking
the site, temporarily add `dd();` to a plugin or drop a syntax error in a mu-plugin
on a **staging** copy.

### Optional: also use it for true server-level errors

For failures that never reach PHP (e.g. the PHP-FPM pool is down, so WordPress
never starts), point the web server at the same page.

**Apache / Hostinger** — in the site's root `.htaccess`:

```apache
ErrorDocument 500 /wp-content/php-error.php
ErrorDocument 502 /wp-content/php-error.php
ErrorDocument 503 /wp-content/php-error.php
```

**Nginx** — in the server block:

```nginx
error_page 500 502 503 504 /wp-content/php-error.php;
```

> Note: 400/403/404 at the server level are usually best left to WordPress
> (the theme's `404.php` handles the common not-found case). Only wire the
> `5xx` family here.
