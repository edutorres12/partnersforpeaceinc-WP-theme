<?php
/**
 * Asset enqueueing for frontend and editor.
 *
 * The same Tailwind CSS is loaded on frontend and editor so utility classes
 * added by the user from the block inspector look the same in both contexts.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a built asset URL, falling back gracefully if not yet compiled.
 */
function wptpl_asset_uri( string $relative ): string {
	return WPTPL_THEME_URI . '/' . ltrim( $relative, '/' );
}

function wptpl_asset_version( string $relative ): string {
	$path = WPTPL_THEME_DIR . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : WPTPL_THEME_VERSION;
}

/*
 * Fonts: none. The template ships on the system sans stack (theme.json
 * settings.typography.fontFamilies + tailwind.config.js fontFamily), so there
 * is no webfont request, no preconnect and no preload to manage. When a site
 * built on this template picks its typefaces, register them here — self-hosted
 * .woff2 under assets/fonts/ preferred over a third-party CDN — and add a
 * matching preload for the faces that render above the fold.
 */

/**
 * Motion bootstrap. Runs inline in <head> so it sets `wptpl-anim-ready` on
 * <html> before first paint — that gates the scroll-reveal CSS (src/tailwind.css
 * "Motion" block) so elements start hidden without a flash. The class is only
 * added when IntersectionObserver exists and the user allows motion; on any
 * error it is removed, so content is never left hidden. Keep the selector in
 * sync with the reveal target list in the CSS.
 */
add_action(
	'wp_head',
	function () {
		?>
<script>
( function () {
	var mq = window.matchMedia;
	if ( ! ( 'IntersectionObserver' in window ) || ( mq && mq( '(prefers-reduced-motion: reduce)' ).matches ) ) {
		return;
	}
	var root = document.documentElement;
	root.classList.add( 'wptpl-anim-ready' );
	var start = function () {
		try {
			var els = document.querySelectorAll( '.wptpl-section-header, .wptpl-feature-card, .wptpl-checklist, .wptpl-faq, .wptpl-tag-list, .wptpl-hero__subtitle, .wptpl-steps .wp-block-column, .wptpl-steps .grid > *' );
			var io = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						io.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' } );
			els.forEach( function ( el ) { io.observe( el ); } );
		} catch ( err ) {
			root.classList.remove( 'wptpl-anim-ready' );
		}
	};
	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
} )();
</script>
		<?php
	},
	1
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'wptpl-tailwind',
			wptpl_asset_uri( 'build/tailwind.css' ),
			array(),
			wptpl_asset_version( 'build/tailwind.css' )
		);
		wp_enqueue_script(
			'wptpl-nav',
			wptpl_asset_uri( 'assets/js/nav.js' ),
			array(),
			wptpl_asset_version( 'assets/js/nav.js' ),
			true
		);
		wp_enqueue_script(
			'wptpl-blog-filter',
			wptpl_asset_uri( 'assets/js/blog-filter.js' ),
			array(),
			wptpl_asset_version( 'assets/js/blog-filter.js' ),
			true
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style(
			'wptpl-tailwind-editor',
			wptpl_asset_uri( 'build/tailwind.css' ),
			array(),
			wptpl_asset_version( 'build/tailwind.css' )
		);
	}
);
