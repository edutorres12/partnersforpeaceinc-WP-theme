<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'soywd', SOYWD_THEME_DIR . '/languages' );

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
				'primary'      => __( 'Primary menu', 'soywd' ),
				'footer'       => __( 'Footer menu', 'soywd' ),
				'footer_legal' => __( 'Footer legal menu', 'soywd' ),
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
				'label' => __( 'Rounded square', 'soywd' ),
			)
		);

		// Section overlay color presets. Clicking one in the group block's
		// "Styles" panel activates the overlay and picks its color; authors
		// add a `soywd-overlay-op-*` class for opacity. CSS lives in
		// src/tailwind.css (the `is-style-overlay-*` selectors).
		$soywd_overlay_styles = array(
			'overlay-primary'     => __( 'Overlay: Sage', 'soywd' ),
			'overlay-secondary'   => __( 'Overlay: Olive (dark)', 'soywd' ),
			'overlay-accent'      => __( 'Overlay: Clay', 'soywd' ),
			'overlay-base'        => __( 'Overlay: Ivory', 'soywd' ),
			'overlay-cream-light' => __( 'Overlay: Cream Light', 'soywd' ),
			'overlay-muted'       => __( 'Overlay: Taupe', 'soywd' ),
			'overlay-bark'        => __( 'Overlay: Bark', 'soywd' ),
			'overlay-surface'     => __( 'Overlay: Sand', 'soywd' ),
			'overlay-white'       => __( 'Overlay: White', 'soywd' ),
		);
		foreach ( $soywd_overlay_styles as $soywd_style_name => $soywd_style_label ) {
			register_block_style(
				'core/group',
				array(
					'name'  => $soywd_style_name,
					'label' => $soywd_style_label,
				)
			);
		}
	}
);
