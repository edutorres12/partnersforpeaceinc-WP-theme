# Soy Web Development — WordPress custom theme

Custom WordPress theme using **Tailwind CSS** + **custom Gutenberg blocks**, built for a therapy practice (English-first; Farsi may be added later).

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
│   ├── enqueue.php           # Tailwind + Google Fonts (preconnect/preload)
│   ├── seo.php               # Baseline meta + OG + Twitter tags. Bails
│   │                         #   automatically if Yoast / Rank Math /
│   │                         #   SEOPress / AIOSEO is active.
│   ├── block-helpers.php     # Shared helpers used by every render.php:
│   │                         #   soywd_attr_* (sanitize), soywd_render_picture
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
│   ├── icons/                # SVG icons
│   └── placeholders/         # JPG + .webp variants for hero/cards/CTA bg
├── scripts/
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

`@wordpress/scripts` compiles them to `build/blocks/<slug>/`. `inc/blocks.php` walks that folder and registers each one with `register_block_type( $path )`. They appear under the **"Soy Web Development"** category in the inserter.

#### Helpers used by every `render.php`

Defined in `inc/block-helpers.php`. **Always use these** when reading or rendering block attributes — the escaping rules live there so all 8 blocks stay consistent.

| Helper | Returns | Use for |
|---|---|---|
| `soywd_attr_html( $attrs, $key )` | string, `wp_kses_post`'d | `RichText` fields (eyebrow, title, body) |
| `soywd_attr_text( $attrs, $key )` | string, `sanitize_text_field`'d | Plain text (CTA label, alt text) |
| `soywd_attr_url( $attrs, $key )` | string, `esc_url` + http/https/mailto/tel | Any link URL |
| `soywd_attr_color( $attrs, $key )` | hex, `sanitize_hex_color`'d | Custom color pickers |
| `soywd_attr_float( $attrs, $key, $min, $max, $default )` | clamped float | Opacity / similar 0–1 inputs |
| `soywd_attr_int( $attrs, $key, $min, $max, $default )` | clamped int | Column counts, heading levels |
| `soywd_attr_enum( $attrs, $key, $allowed, $default )` | one of `$allowed` | Variant / layout / theme attributes |
| `soywd_attr_array( $attrs, $key )` | array (`[]` fallback) | Repeatable items |
| `soywd_attr_bool( $attrs, $key, $default = false )` | bool | Toggle attributes |
| `soywd_render_picture( [ src, alt, class, loading, fetchpriority, decoding, aria_hidden ] )` | `<picture>` with WebP `<source>` when the URL points to a theme asset that has a `.webp` sibling; plain `<img>` otherwise | Every `<img>` we emit |

The matching `block.json` should declare `minimum`/`maximum` on number attributes and `enum` on string attributes that have a fixed set of values. The helpers clamp at read time as a defense-in-depth, but enforcing in the schema means the editor's NumberControl / SelectControl won't silently store out-of-range values.

### Editing in Gutenberg

Each block exposes:
- Editable attributes via `RichText`, `InspectorControls`, `BlockControls`.
- Native Gutenberg supports: alignment, color, padding/margin, anchor, **additional CSS classes**.
- Repeatable items (checklist, tags, steps, FAQ) with custom UI in the inspector.

### Changing CSS

- **Brand colors and fonts** live in two places that MUST stay in sync: `theme.json` (`settings.color.palette` + `settings.typography.fontFamilies`) and `tailwind.config.js` (`theme.extend.colors` + `fontFamily`). `npm run check:tokens` (also in CI) fails if they drift. When adding a new color, also add it to the `@layer utilities` fallback block at the bottom of `src/tailwind.css` (hardcoded hex; the script doesn't check that one because it's intentionally a duplicate).
- **Reusable components** (buttons, container, eyebrow, nav, lang switch): `src/tailwind.css` inside `@layer components`. Use `@apply text-primary` etc. — never hardcode hex inside `@layer`.
- **Block styles registered via `register_block_style()`**: live OUTSIDE `@layer` (so Tailwind doesn't purge classes that only appear in WP markup). Use `var(--wp--preset--color--*)` here, not `@apply` (which won't apply outside layers).
- **Per-block CSS**: `src/blocks/<slug>/style.css` or `editor.css`.

### SEO

`inc/seo.php` writes a baseline `<meta description>` + Open Graph + Twitter Cards into `<head>` so the theme is presentable without an SEO plugin. The file **detects Yoast SEO, Rank Math, SEOPress, and All in One SEO** and bails out at load time when any of them is active, so installing one of those plugins later doesn't produce duplicate tags. To override the detection from code, filter `soywd_seo_plugin_active`.

When you install Yoast (recommended for this site): configure *Yoast → Social* with a default OG image, *Search appearance* with title templates per post type, and *Knowledge graph → Organization* with the LocalBusiness fields. Yoast then owns `<head>` end-to-end (description, OG, Twitter, JSON-LD, canonical, sitemap).

### Performance & accessibility

- **Fonts**: Google Fonts is loaded with a `preconnect` to `fonts.gstatic.com` (saves ~100–300ms on cold loads) plus a `preload` on the stylesheet itself. The font `.woff2` URLs are not preloaded directly because Google rotates their hash on every version bump.
- **Images**: `assets/placeholders/*.jpg` ship next to `.webp` variants generated by `npm run optimize:images` (sharp). `soywd_render_picture()` emits a `<picture>` with a WebP `<source>` whenever the URL points to a theme asset that has a `.webp` sibling — the browser picks WebP when supported, falls back to JPG otherwise. The hero image renders with `loading="eager" fetchpriority="high"`; everything else below the fold uses `loading="lazy" decoding="async"`.
- **`<head>` cleanup**: `inc/cleanup.php` strips WP defaults the theme doesn't use — emoji detection scripts, oEmbed discovery, `wp_generator` (hides the version), RSD / WLW manifest links, the `s.w.org` DNS prefetch.
- **Skip-to-content** link injected at the top of `header.php` before the `<header>` tag; targets `<main id="soywd-main">`. Visible only when focused.

### Global strings (header/footer)

Not blocks. Edited in **Appearance → Customize → Soy Web Development**:

- **Header & CTA**: primary CTA text + URL.
- **Footer / Practice info**: practice name, practitioner, license, hours, modality/languages, crisis disclaimer.

## Available blocks

| Slug                  | Purpose                                              |
|-----------------------|------------------------------------------------------|
| `soywd/hero`          | Hero with eyebrow, primary + secondary CTA, image    |
| `soywd/section-header`| Eyebrow + headline + intro (used in EVERY section)   |
| `soywd/feature-card`  | Versatile card: icon/image, CTA as button or arrow   |
| `soywd/checklist`     | ✓ list, vertical or horizontal (covers trust bar)    |
| `soywd/tag-list`      | Pills/chips with optional URL, outline or filled     |
| `soywd/steps`         | Numbered steps with optional CTA                     |
| `soywd/cta-banner`    | Closing banner, dark or light                        |
| `soywd/faq`           | Accordion (HTML `<details>`, no JS)                  |

## Setting up WordPress (first time)

1. Activate the theme in **Appearance → Themes**.
2. Configure **Appearance → Customize → Soy Web Development**:
   - Header & CTA: primary button text + URL.
   - Footer / Practice info: fill in every field.
3. Create empty pages: About, Services, Resources, Blog, Fees, Contact, Crisis Resources, Privacy Policy, Terms of Service, Accessibility, Good Faith Estimate.
4. **Appearance → Menus** create 3 menus:
   - "Primary" → assign to **Primary menu**.
   - "Footer Links" → assign to **Footer menu**.
   - "Footer Legal" → assign to **Footer legal menu**.
5. **Settings → Reading**: static front page = "Home".
6. Edit the Home page → Code Editor (`Ctrl+Shift+Alt+M`) → paste the homepage block markup.

## Conventions

- CSS/PHP prefix: `soywd-` / `soywd_`.
- Block namespace: `soywd/<slug>`.
- Text domain: `soywd`.
- English UI strings throughout. We still use `__('text', 'soywd')` so future translations (Farsi, Spanish, etc.) can be added without refactoring.

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
- Polylang/WPML when Farsi is added; switch to Tailwind logical utilities (`ms-*`, `me-*`, `text-start`, `text-end`) at the same time.
- Replace placeholder JPGs in `assets/placeholders/` with the final brand photos. After dropping new files in, run `npm run optimize:images` to regenerate the WebP variants and commit both.
- Form plugin (Fluent Forms / Gravity Forms) for Contact and opt-ins.
- Optional: self-host Urbanist + Quicksand `.woff2` under `assets/fonts/` (drop the Google Fonts dependency entirely; saves a DNS lookup + lets us preload the exact font files).
