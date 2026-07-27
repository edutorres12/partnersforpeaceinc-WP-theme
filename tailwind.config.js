/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		'./*.php',
		'./inc/**/*.php',
		'./templates/**/*.{php,html}',
		'./parts/**/*.{php,html}',
		'./src/**/*.{js,jsx,ts,tsx,php,json}',
		'./assets/**/*.js',
		'./build/**/*.{js,html}',
		'./theme.json',
	],
	// Safelist preserves classes the editor lets clients type by hand from the
	// block inspector's "Additional CSS classes" field — those strings live in
	// the WP database, not in any source file Tailwind scans, so without
	// safelist they'd be purged from the output. Keep the patterns BOUNDED:
	// open-ended catch-alls like /^(sm|md|...):.+/ inflate the bundle and trip
	// the "doesn't match any class" warning.
	safelist: [
		// Spacing utilities on the canonical 0–32 ramp.
		{
			pattern:
				/^(p|m|px|py|pt|pb|pl|pr|mx|my|mt|mb|ml|mr)-(0|1|2|3|4|5|6|8|10|12|16|20|24|32)$/,
		},
		{
			pattern:
				/^(w|h|min-w|min-h|max-w|max-h)-(full|screen|auto|0|1|2|4|8|16|32|64|96)$/,
		},
		// Brand color utilities. Tailwind automatically generates `hover:`
		// and `focus:` variants when it sees them in scanned source; we
		// only need the base form preserved here for editor-typed classes.
		{
			pattern:
				/^(text|bg|border)-(primary|secondary|accent|cream|cream-light|sage-light|contrast|muted|bark|surface|white|black|transparent)$/,
		},
		{ pattern: /^text-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl)$/ },
		{
			pattern:
				/^font-(thin|light|normal|medium|semibold|bold|extrabold|black|heading|body)$/,
		},
		{ pattern: /^rounded(-(sm|md|lg|xl|2xl|3xl|full|none))?$/ },
		{ pattern: /^(flex|grid|block|inline|inline-block|hidden)$/ },
		{
			pattern:
				/^(items|justify|content|self)-(start|end|center|between|around|evenly|stretch|baseline)$/,
		},
		{ pattern: /^gap-(0|1|2|3|4|5|6|8|10|12|16)$/ },
		{
			pattern: /^grid-cols-(1|2|3|4|5|6|12)$/,
			variants: [ 'md', 'lg' ],
		},
		{ pattern: /^shadow(-(sm|md|lg|xl|2xl|inner|none))?$/ },
		{ pattern: /^(opacity|z)-(0|10|20|30|40|50|60|70|80|90|100)$/ },
		// Responsive: the codebase only uses `md:` today; add `lg:` when
		// real usage shows up rather than safelisting every breakpoint.
		{
			pattern:
				/^(flex|flex-row|flex-col|grid|block|hidden|items-center|items-start|justify-between|justify-center|text-(left|center|right))$/,
			variants: [ 'md' ],
		},
		'soywd-section',
		'soywd-section-sm',
		'soywd-section-lg',
		'soywd-section-tight',
		// WP preset color classes — these live in our @layer utilities
		// fallback block (src/tailwind.css), but @layer utilities IS subject
		// to Tailwind's purge: without an explicit safelist entry the rules
		// disappear unless something in scanned source happens to mention
		// them. Tailwind emits a "doesn't match" warning here because the
		// pattern isn't a known utility shape — that warning is expected
		// and safe to ignore; the rules are kept regardless.
		{
			pattern:
				/^has-(primary|secondary|accent|base|cream-light|sage-light|contrast|muted|bark|surface|white)-(background-)?color$/,
		},
	],
	theme: {
		extend: {
			// Slugs match theme.json palette so Tailwind utilities (bg-primary, text-muted, …)
			// and WP preset classes (has-primary-background-color, …) share the same source of truth.
			// `cream` is an explicit alias for the `base` slug to avoid colliding with Tailwind's
			// built-in `text-base` font-size utility.
			colors: {
				primary: '#636d4d',
				secondary: '#2f3328',
				accent: '#8f6a4f',
				cream: '#e3ded4',
				// Slightly warmer cream used by the accent button text + dark-section
				// tag outlines. Distinct from `cream` so it never gets accidentally
				// overridden when the cream slug shifts.
				'cream-light': '#e6ded3',
				// Light sage — used as a softer overlay tint and the photo-button fill.
				'sage-light': '#a8b08a',
				contrast: '#2f3328',
				muted: '#5c5648',
				// Warm dark brown — close to muted/Taupe but a distinct token.
				bark: '#5e5646',
				surface: '#f0e8d9',
			},
			fontFamily: {
				heading: [
					'Urbanist',
					'ui-sans-serif',
					'system-ui',
					'-apple-system',
					'"Segoe UI"',
					'sans-serif',
				],
				body: [
					'Quicksand',
					'ui-sans-serif',
					'system-ui',
					'-apple-system',
					'"Segoe UI"',
					'sans-serif',
				],
				// legacy aliases — kept so any leftover usages keep building until migrated.
				serif: [ 'Urbanist', 'ui-sans-serif', 'sans-serif' ],
				sans: [ 'Quicksand', 'ui-sans-serif', 'sans-serif' ],
			},
			letterSpacing: {
				widest: '0.18em',
			},
		},
	},
	plugins: [],
	corePlugins: {
		preflight: true,
	},
};
