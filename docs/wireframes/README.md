# Wireframes

Static HTML mockups used as the **design source of truth** for the theme.
They are NOT templates and are NOT rendered by WordPress.

Workflow:

1. The page is wireframed here as a single self-contained `*-wireframe.html`.
2. The block markup is then ported to the WP page editor (Gutenberg) using
   the custom `soywd/*` blocks defined in `src/blocks/`.
3. Once the page is built in WP, the wireframe stays as the spec — useful for
   diffing layout / spacing changes and onboarding new collaborators.

Pages currently wireframed:

| File | WP page |
|---|---|
| `homepage-wireframe.html` | Home |
| `about-wireframe.html` | About |
| `services-wireframe.html` | Services index |
| `service-page-wireframe.html` | Single service |
| `fees-wireframe.html` | Fees |
| `contact-wireframe.html` | Contact |
| `crisis-resources-wireframe.html` | Crisis Resources |
| `blog-wireframe.html` | Blog index |
| `blog-post-wireframe.html` | Single blog post |
| `guide-landing-wireframe.html` | Free guide landing |
| `guide-thankyou-wireframe.html` | Free guide thank-you |
| `legal-wireframe.html` | Privacy / Terms / Accessibility / GFE |

These files are excluded from the Tailwind content scan in
`tailwind.config.js` (only `*.php`, `src/**`, `build/**` are scanned), so
classes that appear ONLY here are not preserved at build time — that's by
design.
