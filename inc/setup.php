<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'wptpl', WPTPL_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

		// Editor / Gutenberg.
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'build/tailwind.css' );

		register_nav_menus(
			array(
				'primary'      => __( 'Primary menu', 'wptpl' ),
				'footer'       => __( 'Footer menu', 'wptpl' ),
				'footer_legal' => __( 'Footer legal menu', 'wptpl' ),
			)
		);
	}
);

add_action(
	'init',
	function () {
		register_block_style(
			'core/image',
			array(
				'name'  => 'rounded-square',
				'label' => __( 'Rounded square', 'wptpl' ),
			)
		);

		// Section overlay color presets. Clicking one in the group block's
		// "Styles" panel activates the overlay and picks its color; authors
		// add a `wptpl-overlay-op-*` class for opacity. CSS lives in
		// src/tailwind.css (the `is-style-overlay-*` selectors).
		$wptpl_overlay_styles = array(
			'overlay-primary'      => __( 'Overlay: Primary', 'wptpl' ),
			'overlay-secondary'    => __( 'Overlay: Secondary (dark)', 'wptpl' ),
			'overlay-accent'       => __( 'Overlay: Accent', 'wptpl' ),
			'overlay-base'         => __( 'Overlay: Base', 'wptpl' ),
			'overlay-on-dark'      => __( 'Overlay: On dark', 'wptpl' ),
			'overlay-primary-soft' => __( 'Overlay: Primary soft', 'wptpl' ),
			'overlay-muted'        => __( 'Overlay: Muted', 'wptpl' ),
			'overlay-surface'      => __( 'Overlay: Surface', 'wptpl' ),
			'overlay-white'        => __( 'Overlay: White', 'wptpl' ),
		);
		foreach ( $wptpl_overlay_styles as $wptpl_style_name => $wptpl_style_label ) {
			register_block_style(
				'core/group',
				array(
					'name'  => $wptpl_style_name,
					'label' => $wptpl_style_label,
				)
			);
		}
	}
);
