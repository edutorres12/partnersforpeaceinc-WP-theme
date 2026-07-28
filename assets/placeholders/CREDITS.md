# Placeholder images

These six JPGs are **generated wireframe boxes**, not photos — a gray panel with
a border, two diagonals and a label, matching the image slots in
`docs/wireframes/*.html`. They exist so no block ever renders an empty image
slot before real photography is uploaded, and they deliberately carry no brand
color.

No external licensing — they are produced by `npm run gen:placeholders`
(`scripts/gen-placeholders.mjs`, sharp). After regenerating, run
`npm run optimize:images` to refresh the `.webp` siblings and commit both
formats.

Replace each placeholder with a real photo from the WordPress editor → block
sidebar → Image / Background image panel. Once a real URL is set, the
placeholder is no longer used.

Filenames and dimensions are load-bearing — the paths are hardcoded in the
render templates listed below.

| File | Size | Where it shows |
|---|---|---|
| `hero.jpg` | 1600×900 | `wptpl/hero` when `imageUrl` is empty; also the OG image fallback (`inc/seo.php`) and the single-post fallback (`single.php`) |
| `portrait.jpg` | 800×800 | Bio / about column `core/image` (set as `src` in the page markup) |
| `steps-bg.jpg` | 1600×900 | `wptpl/steps` when `usePlaceholder=true` and `backgroundImageUrl` is empty |
| `cta-bg.jpg` | 1920×900 | `wptpl/cta-banner` when `theme=dark` and `backgroundImageUrl` is empty |
| `service-card.jpg` | 800×500 | `wptpl/feature-card` when `ctaText` is set and `imageUrl` is empty |
| `guide-card.jpg` | 800×500 | Same shape as `service-card` — pick this URL manually for guide cards |
