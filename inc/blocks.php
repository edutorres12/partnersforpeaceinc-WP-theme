<?php
/**
 * Custom block registration.
 *
 * Cada bloque vive en src/blocks/<slug>/ con su block.json. Tras `npm run build`
 * se copia a build/blocks/<slug>/ desde donde lo registramos.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$blocks_dir = WPTPL_THEME_DIR . '/build/blocks';

		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		foreach ( (array) glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
			if ( file_exists( $block_path . '/block.json' ) ) {
				register_block_type( $block_path );
			}
		}
	}
);

/**
 * Categoría propia para agrupar nuestros bloques en el inserter.
 */
add_filter(
	'block_categories_all',
	function ( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'wptpl',
					'title' => __( 'Theme Blocks', 'wptpl' ),
					'icon'  => null,
				),
			),
			$categories
		);
	},
	10,
	1
);
