# Agent instructions

Concise rules of the road for AI coding agents (Claude Code, Cursor, etc.)
working in this repo. The human-facing overview is in `readme.md` — read
that first for project philosophy and architecture. This file captures the
"do this, not that" patterns that matter at edit time.

## TL;DR

- WordPress theme, Tailwind 3 + Gutenberg server-side blocks. PHP 8.0+.
- It is an **unstyled template**: neutral grayscale palette, system font stack,
  wireframe placeholder images. Structure and spacing are final; the design
  layer is deliberately absent. Don't reintroduce brand color, webfonts or
  page-specific one-off CSS unless a site actually asks for it.
- Every block renders from `src/blocks/<slug>/render.php` — there is NO `save.js`.
- Use the helpers in `inc/block-helpers.php` for attribute access and image
  rendering. Don't inline `isset() ? sanitize_x() : ''` patterns.
- Design tokens live in **two** files that MUST stay in sync: `theme.json` and
  `tailwind.config.js`. `npm run check:tokens` enforces this. Note the alias:
  theme.json's `base` slug is `canvas` in Tailwind (`text-base` is taken).
- `build/` is currently committed. Don't forget `npm run build` before pushing
  to `main` (CI catches this).

## Commands you'll need

```bash
npm install && composer install     # one-time setup
npm run build                       # blocks + Tailwind → build/
npm run dev                         # watch mode (parallel blocks + Tailwind)
npm run lint:js                     # ESLint
npm run lint:php                    # PHPCS (WordPress-Extra ruleset)
npm run lint:php:fix                # PHPCBF auto-fix
npm run check:tokens                # theme.json ↔ tailwind.config drift check
npm run format                      # Prettier (JS/JSON) — runs the WP scripts formatter
npm run gen:placeholders            # regenerate the neutral placeholder images
wp eval-file scripts/seed-wp.php    # seed a WP install (dry run; add `apply` to write)
npm run optimize:images             # regenerate .webp from JPG/PNG (sharp)
```

Pre-commit hook (Husky + lint-staged) runs PHPCS / ESLint / Prettier on
staged files only — never bypass with `--no-verify` unless the user
explicitly asks.

## Coding conventions

- **Prefixes**: `wptpl_` for PHP functions / globals, `wptpl-` for CSS
  classes, `wptpl/` for block names, `WPTPL_` for PHP constants. WPCS rejects
  prefixes shorter than 4 characters, so don't shorten it.
- **Text domain**: `'wptpl'` — always wrap user-visible strings with
  `__()` / `_e()` even if we're English-only today.
- **Indentation**: tabs (PHP, JS, CSS, JSON). PHPCS + Prettier enforce.
- **PHP**: strict typing where possible (function signatures use scalar
  type hints + return types). PHPCS uses WordPress-Extra (NOT
  WordPress-Docs — `@param` tags for typed signatures are intentional
  noise we don't write).
- **JS**: no nested ternaries (ESLint rule). Use `if`/`else` blocks
  instead.
- **CSS**: never hardcode hex inside `@layer components` — use
  `@apply text-primary` etc. Outside `@layer`, use
  `var(--wp--preset--color--*)`. The `@layer utilities` fallback block at
  the bottom of `src/tailwind.css` is the ONE place where hardcoded hex
  is correct (and intentional).

## Git conventions

**All branch names, commit messages, and PR titles/descriptions are written
in English.**

### Branch names

Format: `type/ticket-short-description`

| Prefix | Use for |
|---|---|
| `feat/` | New features |
| `fix/` | Bug fixes |
| `hotfix/` | Urgent production fixes |
| `refactor/` | Internal improvements without changing functionality |
| `docs/` | Documentation updates |
| `test/` | Testing-related work |
| `chore/` | Maintenance / config / Prettier / dependency updates |

Examples:

```
feat/acl-5443-save-utm-localstorage
fix/acl-5321-mobile-menu-overflow
hotfix/acl-5500-hubspot-form-submit
refactor/component-renderer-cleanup
docs/contentful-workflow-guide
chore/prettier-formatting
```

Rules:

- Lowercase only.
- Use `-` instead of spaces or underscores.
- Include the Jira ticket when available (`ACL-####`, `IS-####`, etc.).
- Keep it short but descriptive.

> Note: Claude Code on the web auto-generates branch names like
> `claude/<random>` for its sessions. When you create a branch yourself
> (locally or for a human-tracked ticket), follow the convention above.

### Commit messages

Conventional Commits style: `type: short description`

Valid `type`s match the branch prefixes: `feat`, `fix`, `hotfix`,
`refactor`, `docs`, `test`, `chore`.

Examples:

```
feat: add utm localStorage persistence
fix: resolve CLS issue on gated resources
hotfix: fix production hubspot form submission
refactor: simplify component renderer logic
docs: update deployment workflow documentation
chore: apply prettier formatting
```

Rules:

- Keep the subject short and clear.
- Present tense ("add", not "added"/"adds").
- No period at the end.
- Focus on what the change does.
- Lowercase unless a proper noun / acronym is required (WordPress, SEO, OG…).
- Extra context goes in the commit body (blank line after the subject),
  not in the subject line.

## Where things live

| What | Where |
|---|---|
| Theme bootstrap | `functions.php` → loads `inc/*.php` in a specific order |
| WP cleanup (emoji, oEmbed, generator) | `inc/cleanup.php` |
| Asset enqueue + motion bootstrap (no webfonts) | `inc/enqueue.php` |
| Baseline SEO (Yoast-aware bail-out) | `inc/seo.php` |
| Block attribute helpers + `wptpl_render_picture` | `inc/block-helpers.php` |
| Block auto-discovery from `build/blocks/` | `inc/blocks.php` |
| Customizer fields (CTA + footer info) | `inc/customizer.php` |
| Palette + fonts | `theme.json` + `tailwind.config.js` (BOTH) |
| Reusable component styles (buttons, container, eyebrow) | `src/tailwind.css` inside `@layer components` |
| `register_block_style()` rules | `src/tailwind.css` OUTSIDE `@layer` (uses `var(--wp--preset--color--*)`) |
| `has-*-color` hardcoded fallbacks | `src/tailwind.css` `@layer utilities` block |
| Custom block source | `src/blocks/<slug>/` |
| Compiled blocks (registered from here) | `build/blocks/<slug>/` |
| Image scripts | `scripts/gen-placeholders.mjs`, `scripts/optimize-images.mjs`, `scripts/check-tokens.mjs` |
| WordPress seeder | `scripts/seed-wp.php` (runner) + `scripts/seed/{blocks,pages,posts}.php` |
| Content deploy (SSH → host) | `.github/workflows/deploy-content.yml` |
| Seeder verification (throwaway WP in CI) | `.github/workflows/preview-seed.yml` |
| Design wireframes | `docs/wireframes/` (NOT in repo root) |

## Common tasks

### Adding a new block

1. Create `src/blocks/<slug>/` with `block.json`, `index.js`, `edit.js`,
   `render.php`, `style.css`.
2. Set `name`: `"wptpl/<slug>"`, `category`: `"wptpl"`, `textdomain`: `"wptpl"`.
3. In `render.php`, use the helpers from `inc/block-helpers.php` — never
   inline `isset() ? sanitize_x() : ''`.
4. For number attributes, declare `minimum` + `maximum` in `block.json`.
   For strings with a fixed set of values, declare `enum`. The render
   helpers clamp at read time, but the schema is the first line of defense.
5. Run `npm run build` so it lands in `build/blocks/<slug>/` and gets
   auto-registered by `inc/blocks.php`.

### Adding or changing a palette color

1. Edit `theme.json` `settings.color.palette` (add a slug + name + hex).
2. Edit `tailwind.config.js` `theme.extend.colors` — same slug, same hex.
3. Edit the safelist regex in `tailwind.config.js` if you want the utility
   to survive purge when typed manually in the editor.
4. Edit the `@layer utilities` hardcoded fallback block at the bottom of
   `src/tailwind.css` — add `has-<slug>-background-color` and
   `has-<slug>-color` rules with the literal hex.
5. Run `npm run check:tokens` to verify the first three sources are
   aligned. (CI runs the same check.)

### Adding an image to a block

- Place the file in `assets/placeholders/<name>.jpg` (or `.png`). To regenerate
  the neutral wireframe boxes instead, edit the `FILES` list in
  `scripts/gen-placeholders.mjs` and run `npm run gen:placeholders`.
- Run `npm run optimize:images` to generate the `.webp` sibling and commit
  both files.
- In `render.php`, use `wptpl_render_picture( [ 'src' => ..., 'alt' => ...,
  'class' => ..., 'loading' => 'lazy' ] )`. The helper auto-detects the
  `.webp` and emits a `<picture>` with a WebP `<source>`. Use
  `loading => 'eager'` and `fetchpriority => 'high'` only for above-the-fold
  hero imagery.

### Changing what the seeder creates

`scripts/seed-wp.php` is the runner (flags, helpers, orchestration); the content
lives in `scripts/seed/`. Page markup is one builder function per page in
`pages.php`, composed from `blocks.php` helpers — never hand-write block comment
markup, and never inline a JSON attribute blob. Attribute names must match
`src/blocks/<slug>/block.json`; a typo silently renders the block's default.

After editing, verify the markup still balances before touching a real site:
dry-run it (`wp eval-file scripts/seed-wp.php`), which writes nothing and prints
the full plan.

`seed-wp.php` runs through `eval()`, which imposes two rules on that file only:
no `declare(strict_types=1)`, and any top-level variable the helpers read with
`global` must be assigned into `$GLOBALS` explicitly. Flags are bare words
(`apply`, `force`) because WP-CLI claims anything starting with `--`.

### Touching SEO

- Don't add meta tags to `header.php` directly. `inc/seo.php` owns the
  baseline tags and ALREADY detects Yoast / Rank Math / SEOPress / AIOSEO
  and bails out so plugins can take over. If you need conditional behavior,
  filter `wptpl_seo_plugin_active`.

## Don't do

- ❌ Inline `isset( $attributes['x'] ) ? sanitize_text_field( $attributes['x'] ) : ''` in `render.php`. Use `wptpl_attr_text( $attributes, 'x' )`.
- ❌ Hardcode hex inside `@layer components`. Use `@apply text-primary` or `text-on-dark` etc.
- ❌ Add `<meta description>` or OG tags to `header.php` directly. They go in `inc/seo.php`.
- ❌ Add a color to `theme.json` without also adding it to `tailwind.config.js` AND the fallback block. CI will fail.
- ❌ Reintroduce a brand color, a webfont, or a page-specific one-off CSS rule
  into the template itself. Those belong to a site built on it, not here.
- ❌ Put real copy in `scripts/seed/`. The seeder writes placeholders on purpose;
  a site replaces them in the editor. The one exception is structural text that
  tells the editor what to do ("Replace these cards with…").
- ❌ Run the seeder with `apply` on a site you have not dry-run first. It is
  idempotent, but `force` replaces page content.
- ❌ Point the PR preview at the staging site. It applies destructively on every
  run; staging is where a human reviews, not where CI writes.
- ❌ Make the deploy workflow write on `push`. A merge updates the theme and
  dry-runs; only a manual dispatch may write, and `apply-force` must stay behind
  its typed confirmation.
- ❌ `git push --force` to `main`. `deploy` branch is force-pushed BY CI; manual force-pushes anywhere else need explicit user permission.
- ❌ Run `git rebase --amend` or `git commit --amend` on commits that are already pushed unless the user explicitly asks.
- ❌ Add WebP via raw `<img src="...webp">`. Use `wptpl_render_picture()` so the JPG fallback is preserved.
- ❌ Forget `npm run build` before committing changes that touch `src/`. The CI workflow's "Verify build/ is up to date" step will fail the PR.
- ❌ Bypass the pre-commit hook with `--no-verify`. If it fails, fix the underlying issue.

## Deploy

`build/` is committed for now AND `.github/workflows/deploy.yml` builds + force-pushes to a `deploy` branch on every push to `main`. Hostinger currently pulls `main`; the migration plan in `readme.md → Deploy → Option B` lays out the switch. Until then, the dev workflow still requires a local `npm run build && git add build/`.

## Reference docs

- `readme.md` — project overview, philosophy, available blocks, Customizer setup, full deploy options.
- `docs/wireframes/README.md` — explains the static HTML mockups as design source of truth.
