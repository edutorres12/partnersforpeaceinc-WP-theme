#!/usr/bin/env node
/**
 * Generate .webp variants for every JPG/PNG under assets/placeholders/.
 *
 * We don't replace the originals — the WebP sits next to them, and the
 * render layer emits a <picture> with the .webp as a <source>. Browsers
 * that don't support WebP fall back to the JPG.
 *
 * Run on-demand: `npm run optimize:images`.
 */
import { readdirSync, statSync } from 'node:fs';
import { join, extname, basename, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import sharp from 'sharp';

const here = dirname( fileURLToPath( import.meta.url ) );
const root = join( here, '..' );
const targetDir = join( root, 'assets', 'placeholders' );

const isImage = ( name ) => /\.(jpe?g|png)$/i.test( name );

async function convert( file ) {
	const src = join( targetDir, file );
	const out = join( targetDir, basename( file, extname( file ) ) + '.webp' );

	const srcStat = statSync( src );
	try {
		const outStat = statSync( out );
		if ( outStat.mtimeMs >= srcStat.mtimeMs ) {
			return { file, skipped: true, reason: 'webp up to date' };
		}
	} catch {
		// no existing webp; proceed.
	}

	const inputBuf = await sharp( src ).toBuffer();
	const inputSize = inputBuf.length;

	const webpBuf = await sharp( inputBuf ).webp( { quality: 82, effort: 5 } ).toBuffer();
	await sharp( webpBuf ).toFile( out );

	const savings = ( ( 1 - webpBuf.length / inputSize ) * 100 ).toFixed( 1 );
	return {
		file,
		from: ( inputSize / 1024 ).toFixed( 1 ) + 'K',
		to: ( webpBuf.length / 1024 ).toFixed( 1 ) + 'K',
		savings: savings + '%',
	};
}

const files = readdirSync( targetDir ).filter( isImage ).sort();
if ( ! files.length ) {
	console.log( 'No images to convert in', targetDir );
	process.exit( 0 );
}

console.log( `Converting ${ files.length } image(s) → WebP...\n` );
const results = await Promise.all( files.map( convert ) );

let totalIn = 0;
let totalOut = 0;
for ( const r of results ) {
	if ( r.skipped ) {
		console.log( `  ⏭  ${ r.file } (${ r.reason })` );
		continue;
	}
	console.log( `  ✓ ${ r.file.padEnd( 22 ) } ${ r.from.padStart( 7 ) } → ${ r.to.padStart( 7 ) }  (-${ r.savings })` );
	totalIn += parseFloat( r.from );
	totalOut += parseFloat( r.to );
}

if ( totalIn > 0 ) {
	console.log(
		`\nTotal: ${ totalIn.toFixed( 1 ) }K → ${ totalOut.toFixed( 1 ) }K  (saved ${ ( totalIn - totalOut ).toFixed( 1 ) }K)`
	);
}
