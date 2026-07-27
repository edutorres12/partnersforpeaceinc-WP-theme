<?php
/**
 * Strip WordPress defaults the theme does not use.
 *
 * Removes emoji detection scripts, oEmbed discovery links, generator meta
 * (hides WP version), and legacy <link> tags (RSD, WLW manifest) that this
 * theme has no use for. Keeps wp_head() output lean and avoids exposing
 * version info that helps attackers.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		// Emoji scripts + styles (frontend and admin).
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		// oEmbed discovery (we don't expose our content as oEmbed providers).
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		// Legacy header bloat — version, RSD, WLW manifest.
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );

		// Adjacent post links (rare to use, add a request each).
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	}
);

/**
 * Drop the s.w.org DNS prefetch hint that the emoji script registered.
 */
add_filter(
	'wp_resource_hints',
	function ( $hints, $relation_type ) {
		if ( 'dns-prefetch' !== $relation_type ) {
			return $hints;
		}
		return array_values(
			array_filter(
				(array) $hints,
				static function ( $hint ) {
					$url = is_array( $hint ) && isset( $hint['href'] ) ? $hint['href'] : $hint;
					return false === strpos( (string) $url, 's.w.org' );
				}
			)
		);
	},
	10,
	2
);
