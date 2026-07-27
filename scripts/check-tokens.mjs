#!/usr/bin/env node
/**
 * Verify the color palette + font families stay aligned between
 * theme.json (WP) and tailwind.config.js (Tailwind). Drift between them
 * causes the editor and the frontend to use different values — the most
 * common cause of "looks right in the editor, wrong on the page" bugs.
 *
 * Notes about the deliberate mismatch we allow:
 *   - `theme.json` has slug `base`, `tailwind.config.js` mirrors that
 *     as `canvas`. They are the same color but distinct names because
 *     Tailwind's `text-base` utility means "base font-size", so we
 *     can't use `base` there.
 *   - `contrast` and `primary` share the same hex on purpose —
 *     `primary` is the design token, `contrast` is what theme.json
 *     exposes to WP's contrast picker.
 *
 * Exit code 1 on drift so CI fails until the configs are reconciled.
 */
import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';

const here = dirname( fileURLToPath( import.meta.url ) );
const root = join( here, '..' );
const require = createRequire( import.meta.url );

const themeJson = JSON.parse(
	readFileSync( join( root, 'theme.json' ), 'utf8' )
);
const tailwindConfig = require( join( root, 'tailwind.config.js' ) );

const themePalette = Object.fromEntries(
	( themeJson.settings?.color?.palette ?? [] ).map( ( c ) => [
		c.slug,
		c.color.toLowerCase(),
	] )
);
const tailwindColors = Object.fromEntries(
	Object.entries( tailwindConfig.theme?.extend?.colors ?? {} ).map(
		( [ slug, hex ] ) => [ slug, String( hex ).toLowerCase() ]
	)
);

// Allowed deliberate mismatches: tailwind slug → theme.json slug it mirrors.
const ALIASES = {
	canvas: 'base',
};

const issues = [];

// Every tailwind color (except aliases handled separately) should appear in
// theme.json with the same hex.
for ( const [ slug, hex ] of Object.entries( tailwindColors ) ) {
	const themeSlug = ALIASES[ slug ] ?? slug;
	const themeHex = themePalette[ themeSlug ];
	if ( ! themeHex ) {
		issues.push(
			`tailwind.config.js has slug "${ slug }" but theme.json has no matching slug "${ themeSlug }".`
		);
		continue;
	}
	if ( themeHex !== hex ) {
		issues.push(
			`Color drift: tailwind "${ slug }" = ${ hex }, theme.json "${ themeSlug }" = ${ themeHex }.`
		);
	}
}

// Slugs Tailwind already provides via its default palette — we don't need
// to redeclare them under theme.extend.colors for utilities to exist.
const TAILWIND_BUILTINS = new Set( [ 'white', 'black', 'transparent' ] );

// Every theme.json slug should appear in tailwind (either directly, via
// alias, or as a built-in Tailwind color).
const tailwindKnown = new Set( [
	...Object.keys( tailwindColors ),
	...Object.values( ALIASES ),
	...TAILWIND_BUILTINS,
] );
for ( const slug of Object.keys( themePalette ) ) {
	if ( ! tailwindKnown.has( slug ) ) {
		issues.push(
			`theme.json has slug "${ slug }" but tailwind.config.js doesn't expose a matching utility.`
		);
	}
}

// Font families: theme.json slug must match tailwind.config.js key.
const themeFonts = new Set(
	( themeJson.settings?.typography?.fontFamilies ?? [] ).map( ( f ) => f.slug )
);
const tailwindFonts = new Set(
	Object.keys( tailwindConfig.theme?.extend?.fontFamily ?? {} )
);
// We allow extra slugs in tailwind (e.g. legacy `serif`/`sans` aliases) but
// every theme.json slug must exist in tailwind.
for ( const slug of themeFonts ) {
	if ( ! tailwindFonts.has( slug ) ) {
		issues.push(
			`Font family slug "${ slug }" is in theme.json but missing from tailwind.config.js fontFamily.`
		);
	}
}

if ( issues.length ) {
	console.error( '✗ Token drift detected:\n' );
	for ( const i of issues ) {
		console.error( '  - ' + i );
	}
	console.error(
		'\nReconcile theme.json (settings.color.palette / typography.fontFamilies)'
	);
	console.error(
		'with tailwind.config.js (theme.extend.colors / fontFamily) and re-run.'
	);
	process.exit( 1 );
}

console.log(
	`✓ Tokens aligned: ${ Object.keys( tailwindColors ).length } colors, ${ themeFonts.size } font families.`
);
