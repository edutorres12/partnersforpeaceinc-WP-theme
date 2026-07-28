# Therapy Theme Template — WordPress custom theme

Unstyled starter theme for therapy-practice sites, using **Tailwind CSS** + **custom Gutenberg blocks**.

It ships **structure without design**: every block, template, container, spacing
token and responsive rule is in place, but the palette is neutral grayscale and
both font stacks are the system sans. Building a site on it means replacing hex
values, font stacks and photography — not rewriting layout. The visual target
for the un-restyled state is `docs/wireframes/*.html`.

Where the design lives, and the only places you edit to apply one:

| What | Where |
|---|---|
| Palette | `theme.json` `settings.color.palette` + `tailwind.config.js` `theme.extend.colors` + the `@layer utilities` fallback at the bottom of `src/tailwind.css` (all three, kept in sync by `npm run check:tokens`) |
| Fonts | `theme.json` `settings.typography.fontFamilies` + `tailwind.config.js` `fontFamily` + the `@layer base` block in `src/tailwind.css`. Register webfonts in `inc/enqueue.php` |
| Imagery | `assets/placeholders/` (see its `CREDITS.md`) |

## Philosophy

- Content is edited from the page editor (Gutenberg) without touching code.
- Extra utility classes are added from the block inspector ("Additional CSS classes").
- **Global CSS** and **reusable components** are changed in the theme (`src/tailwind.css`).
- **Header and footer are global** (PHP partials, NOT per-page blocks). Editable strings live in the Customizer. The footer crisis disclaimer is legal content, not an optional block.

## Structure

```
.
├── style.css                 # WP theme header (metadata)
├── functions.php             # Bootstrap (loads inc/ in this order:
│                             #   setup → cleanup → enqueue → seo →
│                             #   block-helpers → blocks → customizer)
├── theme.json                # FSE settings: palette, typography, layout
├── header.php / footer.php   # Global layout, reads from Customizer
├── index.php / page.php / single.php
├── inc/
│   ├── setup.php             # add_theme_support, menus, editor styles
│   ├── cleanup.php           # Strips unused WP <head> (emoji, oEmbed, …)
│   ├── enqueue.php           # Tailwind + motion bootstrap (no webfonts)
│   ├── seo.php               # Baseline meta + OG + Twitter tags. Bails
│   │                         #   automatically if Yoast / Rank Math /
│   │                         #   SEOPress / AIOSEO is active.
│   ├── block-helpers.php     # Shared helpers used by every render.php:
│   │                         #   wptpl_attr_* (sanitize), wptpl_render_picture
│   ├── blocks.php            # Auto-registers blocks from build/blocks/
│   └── customizer.php        # Customizer fields (CTA + footer info)
├── src/
│   ├── tailwind.css          # Entry point + @layer components
│   └── blocks/                # Source for each custom block
│       ├── section-header/   # Eyebrow + headline + intro
│       ├── checklist/        # ✓ list (also covers dark trust bar)
│       ├── tag-list/         # Pills/chips with optional URL
│       ├── steps/            # Numbered steps
│       ├── cta-banner/       # Closing banner (dark/light)
│       ├── faq/              # Native <details> accordion
│       ├── hero/             # Hero with primary + secondary CTA + microcopy
│       └── feature-card/     # Versatile card (icon/image/CTA)
├── assets/
│   ├── js/                   # nav.js, blog-filter.js
│   └── placeholders/         # JPG + .webp wireframe boxes for hero/cards/CTA bg
├── scripts/
│   ├── gen-placeholders.mjs  # Regenerate the neutral placeholder images
│   ├── optimize-images.mjs   # Regenerate .webp from JPG/PNG
│   └── check-tokens.mjs      # Verify theme.json ↔ tailwind.config alignment
├── build/                    # ⚠ COMMITTED (see "Deploy" below)
├── docs/wireframes/          # Static HTML mockups (design source of truth)
├── .github/workflows/
│   ├── ci.yml                # PRs: lint + build + token check
│   └── deploy.yml            # main → build → force-push to `deploy` branch
├── .husky/pre-commit         # lint-staged (PHPCS + ESLint + Prettier)
├── .phpcs.xml.dist           # PHPCS ruleset (WordPress-Extra, project tuned)
├── composer.json             # PHP dev tooling (WPCS + PHPCompatibility)
├── tailwind.config.js
├── postcss.config.js
└── package.json
```

## Local setup

```bash
npm install            # JS deps (blocks, Tailwind, sharp, husky, lint-staged)
composer install       # PHP linting deps (WPCS via PHPCS)
npm run build          # production build (blocks + Tailwind)
npm run dev            # watch mode
```

Then copy the theme to `wp-content/themes/soy-web-development/` (or symlink) and activate it from **Appearance → Themes**.

## Quality tooling

Configured once via `npm install` + `composer install`:

| Command | What it does |
|---|---|
| `npm run lint:js` | ESLint via `@wordpress/scripts` (Prettier + WP rules) |
| `npm run lint:php` | PHPCS with the WordPress-Extra ruleset (`.phpcs.xml.dist`) |
| `npm run lint:php:fix` | PHPCBF — auto-fixes most PHP issues |
| `npm run format` | Prettier across JS / JSON via `@wordpress/scripts` |
| `npm run check:tokens` | Verifies `theme.json` ↔ `tailwind.config.js` palette + fonts are in sync |
| `npm run gen:placeholders` | Regenerates the neutral wireframe placeholder images |
| `npm run optimize:images` | Regenerates `.webp` siblings for every JPG/PNG in `assets/placeholders/` |
| `npm run build` | Compiles blocks + Tailwind to `build/` |
| `npm run dev` | Watch mode |

The **Husky pre-commit hook** runs `lint-staged` on changed files only:
PHPCS on `*.php`, ESLint on `*.{js,jsx,ts,tsx}`, Prettier on `*.json`.
CSS lint is intentionally skipped — `wp-scripts`' stylelint config doesn't
accept `@tailwind` directives without further setup and would block every
commit.

CI mirrors lint + build + token check — see `.github/workflows/ci.yml`.
A push to `main` also triggers `.github/workflows/deploy.yml` (see below).

## Deploy

> **Current state**: while migrating, **both flows work in parallel**. `build/` is still committed to `main`, AND the deploy workflow rebuilds and force-pushes to a `deploy` branch on every push to `main`. Pick one path below when you're ready.

### Option A — Hostinger pulls `main` (current default, no Hostinger changes)

- `build/` is committed to `main` (NOT in `.gitignore`).
- Before pushing to `main`, run `npm run build` locally and commit `build/`.
- CI (`.github/workflows/ci.yml`) catches the "forgot to rebuild" mistake: it fails the PR if `build/` is out of date.

```bash
npm run build
git add -A
git commit -m "…"
git push origin <branch>     # PR or direct push
# Merge into main → Hostinger pulls automatically.
```

### Option B — Hostinger pulls `deploy` (recommended, automated)

The `deploy.yml` workflow runs `npm ci && npm run build` on every push to `main` and force-pushes the result (source + fresh `build/`) to a `deploy` branch. To switch:

1. Open the Hostinger Git panel and change the tracked branch from `main` to `deploy`. Trigger one manual pull so it lands on the workflow's first commit.
2. Verify the site looks correct.
3. Stop committing `build/` from local: `git rm -r --cached build/` then add `build/` to `.gitignore`.
4. (Optional) delete the "Verify build/ is up to date" step from `ci.yml` once `build/` is no longer in `main`.

After switching, the dev loop is just:

```bash
# Edit src/ or PHP
git commit -m "…"
git push origin main          # CI builds + publishes to deploy → Hostinger pulls
```

## How it works

### Tailwind

- `src/tailwind.css` is the entry. It compiles to `build/tailwind.css`.
- That same file is enqueued in **frontend and editor** (`inc/enqueue.php`) so utility classes look the same in both.
- `tailwind.config.js` scans `*.php`, `src/**`, and `build/**`. Classes the client types **manually** from the block inspector's "Additional CSS classes" field aren't in those files, so a bounded `safelist` covers the patterns we expect: spacing on the 0–32 ramp, brand colors, common layout / grid / responsive `md:` variants. Editing the safelist? Keep it narrow — open-ended catch-alls (`/^hover:.+/`, `/^(sm|md|...):.+/`) inflate the bundle.
- The `has-*-color` rules in the `@layer utilities` block at the bottom of `src/tailwind.css` are **hardcoded fallbacks** for when WP's preset CSS doesn't reach the frontend (caching/minification plugins). If you change a palette color, update those hex values too — `npm run check:tokens` catches drift on the `theme.json` ↔ `tailwind.config.js` side but not on the fallback block.

### Custom blocks (server-side render)

All custom blocks use **`render.php`** (server-side render), not `save.js`. Benefits:

- When we tweak a block's HTML, existing pages **re-render automatically** — no "Block validation failed".
- Better escaping/sanitization on the server.
- `edit.js` can diverge from frontend (friendlier UI in the editor).

Each block's structure:

```
src/blocks/<slug>/
├── block.json     # metadata + attributes + supports + render: file:./render.php
├── index.js       # registration: registerBlockType( ..., { edit, save: () => null } )
├── edit.js        # editor UI (Gutenberg)
├── render.php     # frontend HTML — uses helpers in inc/block-helpers.php
├── style.css      # styles shared editor + frontend
└── editor.css     # (optional) editor-only styles
```

`@wordpress/scripts` compiles them to `build/blocks/<slug>/`. `inc/blocks.php` walks that folder and registers each one with `register_block_type( $path )`. They appear under the **"Theme Blocks"** category in the inserter.

#### Helpers used by every `render.php`

Defined in `inc/block-helpers.php`. **Always use these** when reading or rendering block attributes — the escaping rules live there so all 8 blocks stay consistent.

| Helper | Returns | Use for |
|---|---|---|
| `wptpl_attr_html( $attrs, $key )` | string, `wp_kses_post`'d | `RichText` fields (eyebrow, title, body) |
| `wptpl_attr_text( $attrs, $key )` | string, `sanitize_text_field`'d | Plain text (CTA label, alt text) |
| `wptpl_attr_url( $attrs, $key )` | string, `esc_url` + http/https/mailto/tel | Any link URL |
| `wptpl_attr_color( $attrs, $key )` | hex, `sanitize_hex_color`'d | Custom color pickers |
| `wptpl_attr_float( $attrs, $key, $min, $max, $default )` | clamped float | Opacity / similar 0–1 inputs |
| `wptpl_attr_int( $attrs, $key, $min, $max, $default )` | clamped int | Column counts, heading levels |
| `wptpl_attr_enum( $attrs, $key, $allowed, $default )` | one of `$allowed` | Variant / layout / theme attributes |
| `wptpl_attr_array( $attrs, $key )` | array (`[]` fallback) | Repeatable items |
| `wptpl_attr_bool( $attrs, $key, $default = false )` | bool | Toggle attributes |
| `wptpl_render_picture( [ src, alt, class, loading, fetchpriority, decoding, aria_hidden ] )` | `<picture>` with WebP `<source>` when the URL points to a theme asset that has a `.webp` sibling; plain `<img>` otherwise | Every `<img>` we emit |

The matching `block.json` should declare `minimum`/`maximum` on number attributes and `enum` on string attributes that have a fixed set of values. The helpers clamp at read time as a defense-in-depth, but enforcing in the schema means the editor's NumberControl / SelectControl won't silently store out-of-range values.

### Editing in Gutenberg

Each block exposes:
- Editable attributes via `RichText`, `InspectorControls`, `BlockControls`.
- Native Gutenberg supports: alignment, color, padding/margin, anchor, **additional CSS classes**.
- Repeatable items (checklist, tags, steps, FAQ) with custom UI in the inspector.

### Changing CSS

- **Palette and fonts** live in two places that MUST stay in sync: `theme.json` (`settings.color.palette` + `settings.typography.fontFamilies`) and `tailwind.config.js` (`theme.extend.colors` + `fontFamily`). `npm run check:tokens` (also in CI) fails if they drift. When adding or changing a color, also update the `@layer utilities` fallback block at the bottom of `src/tailwind.css` (hardcoded hex; the script doesn't check that one because it's intentionally a duplicate). Note the deliberate alias: theme.json's `base` slug is exposed to Tailwind as `canvas`, because `text-base` is already a Tailwind font-size utility.
- **Reusable components** (buttons, container, eyebrow, nav, lang switch): `src/tailwind.css` inside `@layer components`. Use `@apply text-primary` etc. — never hardcode hex inside `@layer`.
- **Block styles registered via `register_block_style()`**: live OUTSIDE `@layer` (so Tailwind doesn't purge classes that only appear in WP markup). Use `var(--wp--preset--color--*)` here, not `@apply` (which won't apply outside layers).
- **Per-block CSS**: `src/blocks/<slug>/style.css` or `editor.css`.

### SEO

`inc/seo.php` writes a baseline `<meta description>` + Open Graph + Twitter Cards into `<head>` so the theme is presentable without an SEO plugin. The file **detects Yoast SEO, Rank Math, SEOPress, and All in One SEO** and bails out at load time when any of them is active, so installing one of those plugins later doesn't produce duplicate tags. To override the detection from code, filter `wptpl_seo_plugin_active`.

When you install Yoast (recommended): configure *Yoast → Social* with a default OG image, *Search appearance* with title templates per post type, and *Knowledge graph → Organization* with the practice's LocalBusiness fields. Yoast then owns `<head>` end-to-end (description, OG, Twitter, JSON-LD, canonical, sitemap).

### Performance & accessibility

- **Fonts**: none are loaded. The template runs on the system sans stack, so there is no webfont request, no preconnect and no preload. When a site picks its typefaces, register them in `inc/enqueue.php` — self-hosted `.woff2` under `assets/fonts/` beats a third-party CDN — and preload the faces that render above the fold.
- **Images**: `assets/placeholders/*.jpg` are generated wireframe boxes (`npm run gen:placeholders`) and ship next to `.webp` variants generated by `npm run optimize:images` (sharp). `wptpl_render_picture()` emits a `<picture>` with a WebP `<source>` whenever the URL points to a theme asset that has a `.webp` sibling — the browser picks WebP when supported, falls back to JPG otherwise. The hero image renders with `loading="eager" fetchpriority="high"`; everything else below the fold uses `loading="lazy" decoding="async"`.
- **`<head>` cleanup**: `inc/cleanup.php` strips WP defaults the theme doesn't use — emoji detection scripts, oEmbed discovery, `wp_generator` (hides the version), RSD / WLW manifest links, the `s.w.org` DNS prefetch.
- **Skip-to-content** link injected at the top of `header.php` before the `<header>` tag; targets `<main id="wptpl-main">`. Visible only when focused.

### Global strings (header/footer)

Not blocks. Edited in **Appearance → Customize → Theme Settings**:

- **Header & CTA**: primary CTA text + URL.
- **Footer / Practice info**: practice name, practitioner, license, hours, modality, languages, alert bar message.

Every field ships as a neutral placeholder or empty; the footer skips the empty
ones instead of rendering blank rows. The alert bar (`inc/topbar.php`) renders
at the top of the footer only when **Alert bar message** holds text, and can be
hidden per page from the editor sidebar.

## Available blocks

| Slug                  | Purpose                                              |
|-----------------------|------------------------------------------------------|
| `wptpl/hero`          | Hero with eyebrow, primary + secondary CTA, image    |
| `wptpl/section-header`| Eyebrow + headline + intro (used in EVERY section)   |
| `wptpl/feature-card`  | Versatile card: icon/image, CTA as button or arrow   |
| `wptpl/checklist`     | ✓ list, vertical or horizontal (covers trust bar)    |
| `wptpl/tag-list`      | Pills/chips with optional URL, outline or filled     |
| `wptpl/steps`         | Numbered steps with optional CTA                     |
| `wptpl/cta-banner`    | Closing banner, dark or light                        |
| `wptpl/faq`           | Accordion (HTML `<details>`, no JS)                  |

## Setting up WordPress (first time)

The fastest path is the seeder — it creates every page, menu, setting and sample
post in one command. See **Seeding a new site** below.

To do it by hand instead:

1. Activate the theme in **Appearance → Themes**.
2. Configure **Appearance → Customize → Theme Settings**:
   - Header & CTA: primary button text + URL.
   - Footer / Practice info: fill in every field.
3. Create empty pages: About, Services, Resources, Blog, Fees, Contact, Crisis Resources, Privacy Policy, Terms of Service, Accessibility, Good Faith Estimate.
4. **Appearance → Menus** create 3 menus:
   - "Primary" → assign to **Primary menu**.
   - "Footer Links" → assign to **Footer menu**.
   - "Footer Legal" → assign to **Footer legal menu**.
5. **Settings → Reading**: static front page = "Home".
6. Edit the Home page → Code Editor (`Ctrl+Shift+Alt+M`) → paste the homepage block markup.

## Seeding a new site

`scripts/seed-wp.php` builds the whole WordPress side of a fresh install: the 20
pages with their block markup, the three menus wired to their theme locations,
the front-page and permalink settings, the Customizer fields, blog categories
and six sample posts (one sticky, which is what the featured card picks up).

Run it through WP-CLI from the theme directory:

```bash
wp eval-file scripts/seed-wp.php                # dry run — prints the plan, writes nothing
wp eval-file scripts/seed-wp.php apply          # write
wp eval-file scripts/seed-wp.php apply force
```

The flags are **bare words, not `--apply`** — WP-CLI parses anything starting
with `--` as one of its own options and errors out with "unknown --apply
parameter", and the `--` separator does not help for `eval-file`.

**It is safe by default.** Without `apply` nothing is written; you get a plan
listing every action it would take. With `apply` it is idempotent — pages are
matched by slug, menus by name, options and theme mods are only touched when
they differ, and anything that already exists is left alone. `force`
additionally replaces the content of pages the seeder owns; use it to re-apply
the template after editing the markup, and expect it to discard manual edits to
those pages.

### What it creates

| | |
|---|---|
| Pages | Home, About, Services + 7 service subpages, Resources, Blog, Fees, Contact, Crisis Resources, Free Guide, Thank You, Privacy, Terms, Accessibility |
| Menus | Primary (with the services nested under Services), Footer Links, Footer Legal — each assigned to its location |
| Settings | static front page, `/%postname%/` permalinks, site title + tagline, posts per page |
| Customizer | header CTA, and every Footer / Practice info field |
| Blog | 5 categories, 6 posts, and the default "Hello world!" / "Sample Page" / auto-created Privacy Policy draft trashed |

The blog hub is a **normal page**, not `page_for_posts` — its listing comes from
the `wptpl/featured-post` and `wptpl/post-grid` blocks, which is what lets it
carry a hero and a category filter. The seeder deliberately leaves
`page_for_posts` unset; setting it would make WordPress ignore the page content.

### The copy is placeholder

Everything the seeder writes is lorem ipsum, "Primary CTA", "Service One". That
matches the unstyled state of the theme: the structure is final, the words are
slots. Two things need real content before a site goes live, and the seeder says
so on the page itself:

- **Crisis Resources** ships placeholder cards. Replace them with the real
  emergency numbers for the site's region.
- **Legal pages** ship placeholder sections. Replace them with copy reviewed for
  the relevant jurisdiction.

The Contact and Free Guide pages carry a `wptpl-form` wrapper with a note in
place of the form — drop the form plugin's shortcode in and the styling applies.

### Editing what it produces

Page markup lives in `scripts/seed/pages.php`, one builder per page, composed
from the helpers in `scripts/seed/blocks.php` (`wptpl_section()`,
`wptpl_columns()`, `wptpl_block()`, …). Blog content is in
`scripts/seed/posts.php`. Change a builder, re-run with `apply force`.

Two constraints on `scripts/seed-wp.php` itself, both because `wp eval-file`
runs it through `eval()`: it must not `declare(strict_types=1)`, and top-level
variables the helpers read via `global` must be assigned into `$GLOBALS`
explicitly — a plain assignment is function-scoped there, and the symptom is a
run that reports "0 action(s) planned".

## Verifying content changes

`.github/workflows/preview-seed.yml` runs on every PR that touches the seeder,
the blocks or the templates. It installs a throwaway SQLite-backed WordPress
inside the runner, activates the theme, and puts the seeder through its paces:

- dry run, then `apply`
- a second `apply`, which must create nothing — a failure here means the seeder
  would duplicate content on re-run
- structural checks: 20 pages, the seven services nested under `/services/`,
  three menus assigned, front page set
- renders all 17 public URLs and fails on any non-200 or any PHP
  notice/warning/fatal in the output

The plan, the counts and the render table land in the job summary, with the raw
logs as artifacts.

**It deliberately does not run against the staging site.** The check needs to
`apply` — and sometimes `apply force` — destructively on every PR, which would
eat any copy written in the WordPress editor. That is harmless while staging is
empty and gets progressively worse as it fills up. Concurrent PRs would also
stomp each other on a shared server. Staging is where a human looks at the real
thing; the runner is where the machine proves nothing is broken.

## Deploying content from CI

`.github/workflows/deploy-content.yml` updates the theme on the WordPress host
over SSH and runs the seeder there, so a content change is: edit
`scripts/seed/pages.php` → PR → merge → run the workflow.

**A merge can never rewrite site content.** A push to `main` updates the theme and
runs the seeder in **dry-run** only; the plan lands in the job summary and
nothing touches the database. Writing requires opening the Actions tab and
running the workflow manually with `mode: apply`. `apply-force` — which replaces
the content of every seeded page and discards edits made in the WordPress editor
— additionally requires typing `FORCE` into the confirm field.

Before any write the workflow exports the database to `~/wptpl-backups/` on the
server (gzipped, ten most recent kept). Backups live in the home directory, not
under `public_html`, so they are not downloadable from the web.

The backup drives `mysqldump` from the shell rather than using `wp db export`.
Shared hosts commonly disable `exec`/`shell_exec`/`passthru`/`popen` in PHP, and
`wp db export` shells out to mysqldump through them — on Hostinger it exits 255
with no message at all.

It also refuses to seed unless the expected theme is the active one, which stops
a misconfigured `THEME_DIR` from writing pages into whatever site the credentials
happen to reach.

Until the secrets exist the workflow skips cleanly and says which ones are
missing, rather than failing — a red X on `main` for "not set up yet" teaches
everyone to ignore the workflow, and then a real failure goes unread.

### One-time setup

**Settings → Secrets and variables → Actions.**

Generate a key dedicated to deployment rather than reusing a personal one:

```bash
ssh-keygen -t ed25519 -f deploy_key -N ""
```

Paste `deploy_key.pub` into hPanel → Advanced → SSH Access → Add key, and
`deploy_key` (the private half, whole file including the BEGIN/END lines) into
the secret below.

| Secret | Value |
|---|---|
| `HOSTINGER_SSH_KEY` | contents of `deploy_key` |
| `HOSTINGER_HOST` | e.g. `123.45.67.89` |
| `HOSTINGER_USER` | e.g. `u123456789` |

| Variable | Value |
|---|---|
| `HOSTINGER_PORT` | `65002` (Hostinger's SSH port; the default if unset) |
| `WP_PATH` | absolute path to the WordPress root |
| `THEME_DIR` | theme path relative to `WP_PATH` (default `wp-content/themes/partnersforpeace`) |

Host and user are secrets so they stay out of the logs; the paths are plain
variables so a failure is actually debuggable.

Note that a Hostinger SSH key grants access to the whole hosting account, not a
single site. Keep unrelated client sites on a different account.

## Conventions

- CSS/PHP prefix: `wptpl-` / `wptpl_`.
- Block namespace: `wptpl/<slug>`.
- Text domain: `wptpl`.
- English UI strings throughout. We still use `__('text', 'wptpl')` so translations can be added without refactoring.

### Git: branches & commits (English only)

Branches use `type/ticket-short-description`, commits use Conventional
Commits `type: short description`. Valid types: `feat`, `fix`, `hotfix`,
`refactor`, `docs`, `test`, `chore`.

```
# branches
feat/acl-5443-save-utm-localstorage
fix/acl-5321-mobile-menu-overflow
chore/prettier-formatting

# commits
feat: add utm localStorage persistence
fix: resolve CLS issue on gated resources
refactor: simplify component renderer logic
```

Lowercase, `-` not spaces/underscores in branches, present tense in commit
subjects, no trailing period, include the Jira ticket (`ACL-####`) when
available. Full rules in [`AGENTS.md`](./AGENTS.md#git-conventions).

## Pending / next steps

- Mobile menu (hamburger with JS).
- Block patterns for remaining pages (services, contact, fees, blog, etc.).
- CPTs `service` and `guide`.
- Polylang/WPML if a second language is added; switch to Tailwind logical utilities (`ms-*`, `me-*`, `text-start`, `text-end`) at the same time.
- Replace the wireframe JPGs in `assets/placeholders/` with the site's real photography. After dropping new files in, run `npm run optimize:images` to regenerate the WebP variants and commit both.
- Form plugin (Fluent Forms / Gravity Forms) for Contact and opt-ins.
- Pick the site's typefaces and self-host them as `.woff2` under `assets/fonts/`, registered in `inc/enqueue.php` (no third-party CDN; lets us preload the exact font files).
