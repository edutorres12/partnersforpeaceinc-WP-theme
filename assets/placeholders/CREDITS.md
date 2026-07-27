# Placeholder images

These six JPGs are **synthesized**, not photos. They were generated from the V3 palette (cream / surface / olive / clay / taupe / dark) using gradients and soft blurred shapes so the site never shows an empty image slot before real photography is uploaded.

No external licensing — the assets are produced by `scripts/gen-placeholders.py` (run locally with Pillow).

Replace each placeholder with the real photo by going to the block in the WordPress editor → sidebar → upload via the Image / Background image panel. Once a real URL is set, the placeholder is no longer used.

| File | Purpose | Where it shows |
|---|---|---|
| `hero.jpg` | Hero right-column photo | `soywd/hero` when `imageUrl` is empty |
| `portrait.jpg` | Helia portrait | About column `core/image` (set as `src` in homepage markup) |
| `steps-bg.jpg` | Steps section background | `soywd/steps` when `usePlaceholder=true` and `backgroundImageUrl` is empty |
| `cta-bg.jpg` | Final CTA background | `soywd/cta-banner` when `theme=dark` and `backgroundImageUrl` is empty |
| `service-card.jpg` | Service card top image | `soywd/feature-card` when `ctaText` is set and `imageUrl` is empty |
| `guide-card.jpg` | Guide card top image | (same as service-card) – pick this URL manually for guide cards in Free Resources |
