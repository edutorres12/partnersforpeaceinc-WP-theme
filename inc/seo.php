<?php
/**
 * SEO meta tags: description + Open Graph + Twitter Cards.
 *
 * Acts as a baseline so the theme is presentable without an SEO plugin.
 * If Yoast SEO, Rank Math, SEOPress, or All in One SEO is active, this
 * file bails out and lets the plugin own <head> — those plugins already
 * emit description + Open Graph + Twitter + JSON-LD + canonical, and
 * doubling up creates duplicate tags that confuse crawlers.
 *
 * Pinta los tags en <head> con info derivada del post actual (single/page),
 * con fallback a la info global del sitio (home, archives, 404).
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return true when an SEO plugin owns <head> tags already. Filterable so
 * callers can force-disable or force-enable this baseline if they want.
 */
function wptpl_seo_plugin_active(): bool {
	$active = defined( 'WPSEO_VERSION' )          // Yoast SEO
		|| class_exists( 'WPSEO_Options' )        // Yoast SEO (older)
		|| defined( 'RANK_MATH_VERSION' )         // Rank Math
		|| defined( 'SEOPRESS_VERSION' )          // SEOPress
		|| defined( 'AIOSEO_VERSION' );           // All in One SEO

	/**
	 * Allow forcing the theme's baseline SEO on/off regardless of plugin
	 * detection. Return TRUE here to disable the theme tags.
	 */
	return (bool) apply_filters( 'wptpl_seo_plugin_active', $active );
}

// If a real SEO plugin is on duty, bail before registering any hooks.
if ( wptpl_seo_plugin_active() ) {
	return;
}

/**
 * Resolve the canonical description for the current request.
 */
function wptpl_seo_description(): string {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
		if ( '' === $excerpt ) {
			$content = get_post_field( 'post_content', $post_id );
			$excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 30, '…' );
		}
		if ( '' !== $excerpt ) {
			return $excerpt;
		}
	}

	$tagline = get_bloginfo( 'description', 'display' );
	return '' !== $tagline ? $tagline : '';
}

/**
 * Resolve the canonical title for the current request.
 *
 * wp_get_document_title() honors theme-support('title-tag') and any SEO
 * plugins the user may add later.
 */
function wptpl_seo_title(): string {
	return wp_get_document_title();
}

/**
 * Resolve the OG image URL: featured image → first attachment → site logo →
 * theme placeholder.
 */
function wptpl_seo_image(): string {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$thumb   = get_the_post_thumbnail_url( $post_id, 'full' );
		if ( $thumb ) {
			return $thumb;
		}
	}

	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $logo ) {
			return $logo;
		}
	}

	return WPTPL_THEME_URI . '/assets/placeholders/hero.jpg';
}

/**
 * Resolve the canonical URL for the current request.
 */
function wptpl_seo_url(): string {
	if ( is_singular() ) {
		$permalink = get_permalink();
		if ( $permalink ) {
			return $permalink;
		}
	}
	return home_url( add_query_arg( null, null ) );
}

add_action(
	'wp_head',
	function () {
		$description = wptpl_seo_description();
		$title       = wptpl_seo_title();
		$image       = wptpl_seo_image();
		$url         = wptpl_seo_url();
		$site_name   = get_bloginfo( 'name', 'display' );
		$og_type     = is_singular( 'post' ) ? 'article' : 'website';
		$locale      = get_locale();

		if ( '' !== $description ) {
			printf( "<meta name=\"description\" content=\"%s\" />\n", esc_attr( $description ) );
		}

		printf( "<meta property=\"og:title\" content=\"%s\" />\n", esc_attr( $title ) );
		if ( '' !== $description ) {
			printf( "<meta property=\"og:description\" content=\"%s\" />\n", esc_attr( $description ) );
		}
		printf( "<meta property=\"og:type\" content=\"%s\" />\n", esc_attr( $og_type ) );
		printf( "<meta property=\"og:url\" content=\"%s\" />\n", esc_url( $url ) );
		printf( "<meta property=\"og:site_name\" content=\"%s\" />\n", esc_attr( $site_name ) );
		printf( "<meta property=\"og:locale\" content=\"%s\" />\n", esc_attr( $locale ) );
		printf( "<meta property=\"og:image\" content=\"%s\" />\n", esc_url( $image ) );

		echo "<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
		printf( "<meta name=\"twitter:title\" content=\"%s\" />\n", esc_attr( $title ) );
		if ( '' !== $description ) {
			printf( "<meta name=\"twitter:description\" content=\"%s\" />\n", esc_attr( $description ) );
		}
		printf( "<meta name=\"twitter:image\" content=\"%s\" />\n", esc_url( $image ) );
	},
	5
);
