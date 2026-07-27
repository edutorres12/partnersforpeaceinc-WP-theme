<?php
/**
 * Shared helpers for custom blocks.
 *
 * Centralizes patterns repeated across `src/blocks/*\/render.php`:
 *   - Attribute sanitization helpers (text / html / url / color / float / int
 *     / enum / array / bool).
 *   - Image rendering with WebP fallback for theme placeholders.
 *
 * Every helper returns a SAFE value — already passed through the matching
 * WP sanitize/esc function — so callers can echo without re-escaping for
 * the same context (still use esc_attr() when interpolating into an
 * attribute, esc_url() when re-emitting as an href, etc.).
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default protocols allowed for block CTAs / link attributes.
 */
const WPTPL_LINK_PROTOCOLS = array( 'http', 'https', 'mailto', 'tel' );

/**
 * Rich text fields edited with <RichText> in the editor → wp_kses_post.
 */
function wptpl_attr_html( array $attrs, string $key, string $default = '' ): string {
	return isset( $attrs[ $key ] ) ? wp_kses_post( (string) $attrs[ $key ] ) : $default;
}

/**
 * Plain text → sanitize_text_field (strips tags, normalizes whitespace).
 */
function wptpl_attr_text( array $attrs, string $key, string $default = '' ): string {
	return isset( $attrs[ $key ] ) ? sanitize_text_field( (string) $attrs[ $key ] ) : $default;
}

/**
 * URL with protocols restricted to http/https/mailto/tel by default.
 */
function wptpl_attr_url( array $attrs, string $key, string $default = '', array $protocols = WPTPL_LINK_PROTOCOLS ): string {
	if ( ! isset( $attrs[ $key ] ) ) {
		return $default;
	}
	return esc_url( (string) $attrs[ $key ], $protocols );
}

/**
 * Hex color via sanitize_hex_color; returns '' (not null) on invalid input
 * so callers can simply test with empty checks.
 */
function wptpl_attr_color( array $attrs, string $key, string $default = '' ): string {
	if ( ! isset( $attrs[ $key ] ) ) {
		return $default;
	}
	$value = sanitize_hex_color( (string) $attrs[ $key ] );
	return is_string( $value ) ? $value : $default;
}

/**
 * Clamped float for overlay opacity / similar.
 */
function wptpl_attr_float( array $attrs, string $key, float $min, float $max, float $default ): float {
	if ( ! isset( $attrs[ $key ] ) ) {
		return $default;
	}
	return max( $min, min( $max, (float) $attrs[ $key ] ) );
}

/**
 * Clamped integer for column counts / heading levels / item caps.
 */
function wptpl_attr_int( array $attrs, string $key, int $min, int $max, int $default ): int {
	if ( ! isset( $attrs[ $key ] ) ) {
		return $default;
	}
	return max( $min, min( $max, (int) $attrs[ $key ] ) );
}

/**
 * Enum: return the value if it's in $allowed (strict), otherwise $default.
 */
function wptpl_attr_enum( array $attrs, string $key, array $allowed, string $default ): string {
	if ( isset( $attrs[ $key ] ) && in_array( $attrs[ $key ], $allowed, true ) ) {
		return (string) $attrs[ $key ];
	}
	return $default;
}

/**
 * Repeatable items → array (empty array if missing or not an array).
 */
function wptpl_attr_array( array $attrs, string $key ): array {
	return isset( $attrs[ $key ] ) && is_array( $attrs[ $key ] ) ? $attrs[ $key ] : array();
}

/**
 * Bool with explicit default. Matches Gutenberg's own truthy semantics:
 * any non-empty value (true, 1, '1', 'yes', etc.) is treated as true.
 */
function wptpl_attr_bool( array $attrs, string $key, bool $default = false ): bool {
	if ( ! array_key_exists( $key, $attrs ) ) {
		return $default;
	}
	return ! empty( $attrs[ $key ] );
}

/**
 * Return the same URL with `.webp` extension if a matching WebP file exists
 * inside the theme directory; otherwise return null.
 *
 * Only resolves URLs that live under the theme. WordPress Media Library
 * uploads are left to WP / image-optimization plugins.
 */
function wptpl_webp_variant( string $url ): ?string {
	if ( '' === $url ) {
		return null;
	}

	$theme_uri = WPTPL_THEME_URI;
	if ( 0 !== strpos( $url, $theme_uri ) ) {
		return null;
	}

	if ( ! preg_match( '/\.(jpe?g|png)(\?.*)?$/i', $url ) ) {
		return null;
	}

	$relative   = substr( $url, strlen( $theme_uri ) );
	$webp_rel   = preg_replace( '/\.(jpe?g|png)(\?.*)?$/i', '.webp$2', $relative );
	$webp_path  = WPTPL_THEME_DIR . $webp_rel;
	$query_pos  = strpos( $webp_path, '?' );
	$check_path = false !== $query_pos ? substr( $webp_path, 0, $query_pos ) : $webp_path;

	return file_exists( $check_path ) ? $theme_uri . $webp_rel : null;
}

/**
 * Render a responsive image tag, wrapping it in <picture> with a WebP
 * <source> when the URL points to a theme asset that has a `.webp`
 * sibling. Falls back to a plain <img> otherwise.
 *
 * @param array $args {
 *     @type string $src      Image URL (required).
 *     @type string $alt      Alt text (required, empty string for decorative).
 *     @type string $class    Class attribute applied to <img>.
 *     @type string $loading  'lazy' (default) or 'eager'.
 *     @type string $fetchpriority Optional: 'high' / 'low' / 'auto'.
 *     @type string $decoding 'async' (default) or 'sync' / 'auto'.
 *     @type bool   $aria_hidden Adds aria-hidden="true" to <img> when true.
 * }
 */
function wptpl_render_picture( array $args ): string {
	$src   = isset( $args['src'] ) ? (string) $args['src'] : '';
	$alt   = isset( $args['alt'] ) ? (string) $args['alt'] : '';
	$class = isset( $args['class'] ) ? (string) $args['class'] : '';

	if ( '' === $src ) {
		return '';
	}

	$loading       = isset( $args['loading'] ) ? (string) $args['loading'] : 'lazy';
	$decoding      = isset( $args['decoding'] ) ? (string) $args['decoding'] : 'async';
	$fetchpriority = isset( $args['fetchpriority'] ) ? (string) $args['fetchpriority'] : '';
	$aria_hidden   = ! empty( $args['aria_hidden'] );

	$img_attrs = sprintf(
		'src="%s" alt="%s"',
		esc_url( $src ),
		esc_attr( $alt )
	);
	if ( '' !== $class ) {
		$img_attrs .= ' class="' . esc_attr( $class ) . '"';
	}
	$img_attrs .= ' loading="' . esc_attr( $loading ) . '"';
	$img_attrs .= ' decoding="' . esc_attr( $decoding ) . '"';
	if ( '' !== $fetchpriority ) {
		$img_attrs .= ' fetchpriority="' . esc_attr( $fetchpriority ) . '"';
	}
	if ( $aria_hidden ) {
		$img_attrs .= ' aria-hidden="true"';
	}

	$webp = wptpl_webp_variant( $src );
	if ( null === $webp ) {
		return '<img ' . $img_attrs . ' />';
	}

	return sprintf(
		'<picture><source srcset="%s" type="image/webp" /><img %s /></picture>',
		esc_url( $webp ),
		$img_attrs // already escaped piece by piece above.
	);
}

/**
 * ID of the post that headlines the Blog hub: the first sticky post if any,
 * otherwise the most recent published post. Shared by the featured-post and
 * post-grid blocks so the grid can exclude whatever the featured card shows.
 *
 * @return int Post ID, or 0 when there are no published posts yet.
 */
function wptpl_blog_featured_id(): int {
	$wptpl_sticky = get_option( 'sticky_posts' );
	if ( ! empty( $wptpl_sticky ) && is_array( $wptpl_sticky ) ) {
		$wptpl_q = new WP_Query(
			array(
				'post__in'            => $wptpl_sticky,
				'posts_per_page'      => 1,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => 1,
				'post_status'         => 'publish',
				'no_found_rows'       => true,
			)
		);
		if ( ! empty( $wptpl_q->posts ) ) {
			return (int) $wptpl_q->posts[0]->ID;
		}
	}

	$wptpl_recent = get_posts(
		array(
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	return $wptpl_recent ? (int) $wptpl_recent[0]->ID : 0;
}
