<?php
/**
 * Asset enqueueing for frontend and editor.
 *
 * The same Tailwind CSS is loaded on frontend and editor so utility classes
 * added by the user from the block inspector look the same in both contexts.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a built asset URL, falling back gracefully if not yet compiled.
 */
function soywd_asset_uri( string $relative ): string {
	return SOYWD_THEME_URI . '/' . ltrim( $relative, '/' );
}

function soywd_asset_version( string $relative ): string {
	$path = SOYWD_THEME_DIR . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : SOYWD_THEME_VERSION;
}

/**
 * Google Fonts: Urbanist (headings, buttons, eyebrows) + Quicksand (body).
 */
function soywd_google_fonts_url(): string {
	return 'https://fonts.googleapis.com/css2?family=Urbanist:wght@500;600;700;800&family=Quicksand:wght@300;400;500;600;700&display=swap';
}

/**
 * Preconnect to Google Fonts CDNs so the TLS handshake starts before the
 * stylesheet is requested. Saves ~100-300ms on cold loads.
 */
add_filter(
	'wp_resource_hints',
	function ( $hints, $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$hints[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
			$hints[] = 'https://fonts.googleapis.com';
		}
		return $hints;
	},
	10,
	2
);

/**
 * Preload the Google Fonts stylesheet itself so it starts downloading in
 * parallel with the HTML, before WP's enqueued <link> tag is parsed.
 *
 * We do NOT preload specific .woff2 URLs because Google rotates the hash in
 * the path on each font version bump; a stale preload becomes a warning.
 * Preloading the CSS gets ~70% of the benefit and never rots.
 */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" as="style" href="%s" />' . "\n",
			esc_url( soywd_google_fonts_url() )
		);
	},
	2
);

/**
 * Motion bootstrap. Runs inline in <head> so it sets `soywd-anim-ready` on
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
	root.classList.add( 'soywd-anim-ready' );
	var start = function () {
		try {
			var els = document.querySelectorAll( '.soywd-section-header, .soywd-feature-card, .soywd-checklist, .soywd-faq, .soywd-tag-list, .soywd-hero__subtitle, .soywd-steps .wp-block-column, .soywd-steps .grid > *' );
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
			root.classList.remove( 'soywd-anim-ready' );
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
			'soywd-google-fonts',
			soywd_google_fonts_url(),
			array(),
			null
		);
		wp_enqueue_style(
			'soywd-tailwind',
			soywd_asset_uri( 'build/tailwind.css' ),
			array( 'soywd-google-fonts' ),
			soywd_asset_version( 'build/tailwind.css' )
		);
		wp_enqueue_script(
			'soywd-nav',
			soywd_asset_uri( 'assets/js/nav.js' ),
			array(),
			soywd_asset_version( 'assets/js/nav.js' ),
			true
		);
		wp_enqueue_script(
			'soywd-blog-filter',
			soywd_asset_uri( 'assets/js/blog-filter.js' ),
			array(),
			soywd_asset_version( 'assets/js/blog-filter.js' ),
			true
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style(
			'soywd-google-fonts-editor',
			soywd_google_fonts_url(),
			array(),
			null
		);
		wp_enqueue_style(
			'soywd-tailwind-editor',
			soywd_asset_uri( 'build/tailwind.css' ),
			array( 'soywd-google-fonts-editor' ),
			soywd_asset_version( 'build/tailwind.css' )
		);
	}
);
