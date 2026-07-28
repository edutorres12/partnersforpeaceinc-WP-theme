#!/usr/bin/env node
/**
 * Regenerate the neutral wireframe placeholders in assets/placeholders/.
 *
 * These stand in for real photography until a site uploads its own, so they
 * deliberately look like the image boxes in docs/wireframes/*.html: #f0f0f0
 * fill, #999 border, #bbb diagonals, #999 centered label. Nothing here should
 * carry brand color — a site swaps the files, it does not restyle them.
 *
 * Filenames and dimensions are load-bearing: the paths are hardcoded in
 * src/blocks/{hero,steps,cta-banner,feature-card}/render.php, single.php and
 * inc/seo.php. Change a name here and you must change it there too.
 *
 * Run `npm run gen:placeholders`, then `npm run optimize:images` to refresh the
 * .webp siblings, and commit both formats.
 */
import sharp from 'sharp';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname( fileURLToPath( import.meta.url ) );
const outDir = join( here, '..', 'assets', 'placeholders' );

const FILES = [
	{ name: 'hero', w: 1600, h: 900, label: 'Hero image' },
	{ name: 'portrait', w: 800, h: 800, label: 'Photo' },
	{ name: 'steps-bg', w: 1600, h: 900, label: 'Background image' },
	{ name: 'cta-bg', w: 1920, h: 900, label: 'Background image' },
	{ name: 'service-card', w: 800, h: 500, label: 'Image' },
	{ name: 'guide-card', w: 800, h: 500, label: 'Image' },
];

/**
 * Build the wireframe box as SVG. The label sits on a small opaque patch so the
 * diagonals don't run through the text.
 */
function boxSvg( { w, h, label } ) {
	const stroke = Math.max( 2, Math.round( Math.min( w, h ) / 320 ) );
	const font = Math.round( Math.min( w, h ) / 18 );
	const half = stroke / 2;
	const patchW = font * label.length * 0.68;
	const patchH = font * 1.9;

	return `<svg xmlns="http://www.w3.org/2000/svg" width="${ w }" height="${ h }" viewBox="0 0 ${ w } ${ h }">
  <rect width="${ w }" height="${ h }" fill="#f0f0f0"/>
  <line x1="0" y1="0" x2="${ w }" y2="${ h }" stroke="#bbbbbb" stroke-width="${ stroke }"/>
  <line x1="${ w }" y1="0" x2="0" y2="${ h }" stroke="#bbbbbb" stroke-width="${ stroke }"/>
  <rect x="${ half }" y="${ half }" width="${ w - stroke }" height="${ h - stroke }" fill="none" stroke="#999999" stroke-width="${ stroke }"/>
  <rect x="${ w / 2 - patchW / 2 }" y="${ h / 2 - patchH / 2 }" width="${ patchW }" height="${ patchH }" fill="#f0f0f0"/>
  <text x="${ w / 2 }" y="${ h / 2 }" fill="#999999" font-family="Arial, Helvetica, sans-serif" font-size="${ font }" text-anchor="middle" dominant-baseline="central">${ label }</text>
</svg>`;
}

for ( const file of FILES ) {
	await sharp( Buffer.from( boxSvg( file ) ) )
		.jpeg( { quality: 92, chromaSubsampling: '4:4:4' } )
		.toFile( join( outDir, `${ file.name }.jpg` ) );
	console.log( `✓ ${ file.name }.jpg  ${ file.w }×${ file.h }` );
}

console.log( '\nNow run `npm run optimize:images` to refresh the .webp siblings.' );
