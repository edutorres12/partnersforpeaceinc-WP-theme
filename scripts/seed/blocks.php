<?php
/**
 * Gutenberg block-markup builders used by the seeder.
 *
 * Every custom block in this theme renders server-side (`save: () => null`), so
 * its markup is a single self-closing comment carrying the attributes as JSON.
 * Core blocks that wrap content (group, columns) need an opening and closing
 * comment plus the HTML the editor would have saved.
 *
 * Attribute names and their allowed values come from
 * `src/blocks/<slug>/block.json` — keep these helpers in sync with it.
 *
 * @package wptpl
 */

declare( strict_types = 1 );

/**
 * Encode block attributes the way the block editor does: no attribute at all
 * when the set is empty, otherwise compact JSON with unescaped slashes and
 * unicode so the markup stays readable in the code editor.
 *
 * @param array<string, mixed> $attrs Block attributes.
 */
function wptpl_attrs( array $attrs ): string {
	$attrs = array_filter(
		$attrs,
		static function ( $value ) {
			return null !== $value && '' !== $value;
		}
	);
	if ( ! $attrs ) {
		return '';
	}
	return ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

/**
 * A self-closing dynamic block.
 *
 * @param string               $name  Block name without the namespace, e.g. "hero".
 * @param array<string, mixed> $attrs Block attributes.
 */
function wptpl_block( string $name, array $attrs = array() ): string {
	return sprintf( '<!-- wp:wptpl/%s%s /-->', $name, wptpl_attrs( $attrs ) );
}

/**
 * A core block that wraps inner blocks.
 *
 * @param string               $name    Core block name, e.g. "group".
 * @param array<string, mixed> $attrs   Block attributes.
 * @param string               $open    Opening HTML the editor saves.
 * @param string               $close   Closing HTML.
 * @param array<int, string>   $inner   Inner block markup.
 */
function wptpl_wrap( string $name, array $attrs, string $open, string $close, array $inner ): string {
	return implode(
		"\n",
		array_merge(
			array( sprintf( '<!-- wp:%s%s -->', $name, wptpl_attrs( $attrs ) ), $open ),
			$inner,
			array( $close, sprintf( '<!-- /wp:%s -->', $name ) )
		)
	);
}

/**
 * A full-width section band. This is the shape every section on the site takes:
 * an `alignfull` group carrying the vertical rhythm class, wrapping a container
 * group that supplies the horizontal inset.
 *
 * @param array<int, string> $inner     Inner block markup.
 * @param string             $bg        Palette slug for the band background, or '' for none.
 * @param string             $container One of container, container-md, container-narrow.
 * @param string             $extra     Extra CSS classes for the band.
 */
function wptpl_section( array $inner, string $bg = '', string $container = 'container-md', string $extra = '' ): string {
	$band_classes = trim( 'wptpl-section ' . $extra );

	$band_attrs = array(
		'align'     => 'full',
		'className' => $band_classes,
	);
	$band_html_classes = 'wp-block-group alignfull ' . $band_classes;

	if ( '' !== $bg ) {
		$band_attrs['backgroundColor'] = $bg;
		$band_html_classes           .= ' has-' . $bg . '-background-color has-background';
	}

	$container_group = wptpl_wrap(
		'group',
		array( 'className' => 'wptpl-' . $container ),
		sprintf( '<div class="wp-block-group wptpl-%s">', $container ),
		'</div>',
		$inner
	);

	return wptpl_wrap(
		'group',
		$band_attrs,
		sprintf( '<div class="%s">', $band_html_classes ),
		'</div>',
		array( $container_group )
	);
}

/**
 * A columns row. Each entry of `$columns` is a list of inner block markup.
 *
 * @param array<int, array<int, string>> $columns Inner markup per column.
 * @param array<int, string>             $widths  Optional per-column width (e.g. "60%").
 */
function wptpl_columns( array $columns, array $widths = array() ): string {
	$rendered = array();

	foreach ( $columns as $index => $blocks ) {
		$attrs = array();
		$style = '';
		if ( isset( $widths[ $index ] ) && '' !== $widths[ $index ] ) {
			$attrs['width'] = $widths[ $index ];
			$style          = sprintf( ' style="flex-basis:%s"', $widths[ $index ] );
		}
		$rendered[] = wptpl_wrap(
			'column',
			$attrs,
			sprintf( '<div class="wp-block-column"%s>', $style ),
			'</div>',
			$blocks
		);
	}

	return wptpl_wrap(
		'columns',
		array(),
		'<div class="wp-block-columns">',
		'</div>',
		$rendered
	);
}

/**
 * A core heading.
 *
 * @param string $text      Heading text.
 * @param int    $level     Heading level, 2..6.
 * @param string $classname Extra CSS classes.
 * @param string $align     Text alignment.
 */
function wptpl_heading( string $text, int $level = 2, string $classname = '', string $align = '' ): string {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
	}
	if ( '' !== $align ) {
		$attrs['textAlign'] = $align;
	}

	$classes = trim( ( '' !== $align ? 'has-text-align-' . $align . ' ' : '' ) . $classname );
	$attr    = '' !== $classes ? sprintf( ' class="%s"', $classes ) : '';

	return sprintf(
		"<!-- wp:heading%s -->\n<h%d%s>%s</h%d>\n<!-- /wp:heading -->",
		wptpl_attrs( $attrs ),
		$level,
		$attr,
		$text,
		$level
	);
}

/**
 * A core paragraph.
 *
 * @param string $text      Paragraph text.
 * @param string $classname Extra CSS classes.
 * @param string $align     Text alignment.
 */
function wptpl_paragraph( string $text, string $classname = '', string $align = '' ): string {
	$attrs = array();
	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
	}
	if ( '' !== $align ) {
		$attrs['align'] = $align;
	}

	$classes = trim( ( '' !== $align ? 'has-text-align-' . $align . ' ' : '' ) . $classname );
	$attr    = '' !== $classes ? sprintf( ' class="%s"', $classes ) : '';

	return sprintf(
		"<!-- wp:paragraph%s -->\n<p%s>%s</p>\n<!-- /wp:paragraph -->",
		wptpl_attrs( $attrs ),
		$attr,
		$text
	);
}

/**
 * A core separator using the theme's wide style.
 */
function wptpl_separator(): string {
	return "<!-- wp:separator {\"className\":\"is-style-wide\"} -->\n"
		. '<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>' . "\n"
		. '<!-- /wp:separator -->';
}

/**
 * Placeholder image from the theme's own wireframe boxes.
 *
 * @param string $file One of hero, portrait, steps-bg, cta-bg, service-card, guide-card.
 * @param string $classname Extra CSS classes.
 */
function wptpl_image( string $file, string $classname = '' ): string {
	$url   = get_template_directory_uri() . '/assets/placeholders/' . $file . '.jpg';
	$attrs = array( 'sizeSlug' => 'large' );
	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
	}
	$classes = trim( 'wp-block-image size-large ' . $classname );

	return sprintf(
		"<!-- wp:image%s -->\n<figure class=\"%s\"><img src=\"%s\" alt=\"\"/></figure>\n<!-- /wp:image -->",
		wptpl_attrs( $attrs ),
		$classes,
		esc_url( $url )
	);
}

/**
 * Lorem ipsum of a requested length, so the seeded pages read like the
 * wireframes rather than like real copy.
 *
 * @param string $length One of short, medium, long.
 */
function wptpl_lorem( string $length = 'medium' ): string {
	$short  = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
	$medium = $short . ' Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
	$long   = $medium . ' Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

	switch ( $length ) {
		case 'short':
			return $short;
		case 'long':
			return $long;
		default:
			return $medium;
	}
}
