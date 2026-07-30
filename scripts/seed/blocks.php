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
 * Blocks that are section bands in their own right: they render their own
 * full-bleed background (a color, a photo or an overlay) and carry the 100px
 * band rhythm as `py-[6.25rem]`, so they are never meant to sit inside the
 * content column. Each declares `align: ["wide","full"]` in its block.json;
 * without `align: full` in the markup WordPress renders them constrained and
 * the background stops short of the viewport edge.
 *
 * @var array<int, string>
 */
const WPTPL_FULL_BLEED_BLOCKS = array( 'hero', 'steps', 'cta-banner' );

/**
 * A self-closing dynamic block.
 *
 * @param string               $name  Block name without the namespace, e.g. "hero".
 * @param array<string, mixed> $attrs Block attributes.
 */
function wptpl_block( string $name, array $attrs = array() ): string {
	if ( in_array( $name, WPTPL_FULL_BLEED_BLOCKS, true ) && ! isset( $attrs['align'] ) ) {
		$attrs['align'] = 'full';
	}
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
 * The band declares `layout: constrained`. Without it WordPress renders the
 * group as `is-layout-flow`, which neither constrains its children to
 * `settings.layout.contentSize` nor emits `has-global-padding` — so alignfull
 * children inside the band have nothing to align against and any rule scoped to
 * `has-global-padding` silently misses.
 *
 * Pass `''` for `$container` to skip the inner container group. Use that when
 * the inner block already supplies its own width tier — `wptpl/faq` renders
 * with `wptpl-container-narrow` baked in, so wrapping it in a second narrow
 * container just nests one cap inside an identical one.
 *
 * @param array<int, string> $inner     Inner block markup.
 * @param string             $bg        Palette slug for the band background, or '' for none.
 * @param string             $container One of container, container-md, container-narrow, or '' for none.
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

	$band_attrs['layout'] = array( 'type' => 'constrained' );

	if ( '' === $container ) {
		$band_inner = $inner;
	} else {
		$band_inner = array(
			wptpl_wrap(
				'group',
				array( 'className' => 'wptpl-' . $container ),
				sprintf( '<div class="wp-block-group wptpl-%s">', $container ),
				'</div>',
				$inner
			),
		);
	}

	return wptpl_wrap(
		'group',
		$band_attrs,
		sprintf( '<div class="%s">', $band_html_classes ),
		'</div>',
		$band_inner
	);
}

/**
 * The `style` attribute for a top margin, in the shape the block editor saves.
 *
 * A section-header's own bottom margin is sized for the paragraph that usually
 * follows it, not for a grid of cards or a bank of pills, so content placed
 * under one reads as stuck to it. Spread this into the following block's
 * attributes to set the gap the design calls for.
 *
 * @param string $value Margin value, e.g. "2rem".
 * @return array<string, mixed>
 */
function wptpl_margin_top( string $value ): array {
	return array(
		'style' => array( 'spacing' => array( 'margin' => array( 'top' => $value ) ) ),
	);
}

/**
 * A columns row. Each entry of `$columns` is a list of inner block markup.
 *
 * `$margin_top` exists because a bare columns row butts straight up against
 * whatever precedes it — a section-header's own bottom margin is sized for a
 * paragraph, not for a grid of cards, so a card row placed under one reads as
 * stuck to it. Pass the gap the design calls for (3rem under a section header,
 * 1.5rem between two banks of cards in the same row group).
 *
 * `$vertical_center` centers the columns against each other rather than
 * top-aligning them. It is what an image-beside-copy row needs whenever the two
 * columns are different heights, which is most of them.
 *
 * @param array<int, array<int, string>> $columns         Inner markup per column.
 * @param array<int, string>             $widths          Optional per-column width (e.g. "60%").
 * @param string                         $classname       Extra CSS classes for the row.
 * @param string                         $margin_top      Top margin (e.g. "3rem"), or '' for none.
 * @param bool                           $vertical_center Vertically center the columns.
 * @param array<int, string>             $col_classes     Optional per-column CSS classes (e.g. wptpl-vrule).
 */
function wptpl_columns( array $columns, array $widths = array(), string $classname = '', string $margin_top = '', bool $vertical_center = false, array $col_classes = array() ): string {
	$rendered = array();

	foreach ( $columns as $index => $blocks ) {
		$attrs   = array();
		$style   = '';
		$classes = 'wp-block-column';
		if ( $vertical_center ) {
			$attrs['verticalAlignment'] = 'center';
			$classes                   .= ' is-vertically-aligned-center';
		}
		if ( isset( $col_classes[ $index ] ) && '' !== $col_classes[ $index ] ) {
			$attrs['className'] = $col_classes[ $index ];
			$classes           .= ' ' . $col_classes[ $index ];
		}
		if ( isset( $widths[ $index ] ) && '' !== $widths[ $index ] ) {
			$attrs['width'] = $widths[ $index ];
			$style          = sprintf( ' style="flex-basis:%s"', $widths[ $index ] );
		}
		$rendered[] = wptpl_wrap(
			'column',
			$attrs,
			sprintf( '<div class="%s"%s>', esc_attr( $classes ), $style ),
			'</div>',
			$blocks
		);
	}

	$row_attrs   = array();
	$row_classes = 'wp-block-columns';
	$row_style   = '';

	if ( $vertical_center ) {
		$row_attrs['verticalAlignment'] = 'center';
		$row_classes                   .= ' are-vertically-aligned-center';
	}
	if ( '' !== $classname ) {
		$row_attrs['className'] = $classname;
		$row_classes           .= ' ' . $classname;
	}
	if ( '' !== $margin_top ) {
		$row_attrs['style'] = array( 'spacing' => array( 'margin' => array( 'top' => $margin_top ) ) );
		$row_style          = sprintf( ' style="margin-top:%s"', $margin_top );
	}

	return wptpl_wrap(
		'columns',
		$row_attrs,
		sprintf( '<div class="%s"%s>', $row_classes, $row_style ),
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
function wptpl_separator( string $classname = '' ): string {
	$classes = trim( 'is-style-wide ' . $classname );

	return sprintf(
		"<!-- wp:separator%s -->\n<hr class=\"wp-block-separator has-alpha-channel-opacity %s\"/>\n<!-- /wp:separator -->",
		wptpl_attrs( array( 'className' => $classes ) ),
		esc_attr( $classes )
	);
}

/**
 * A full-width photo band: a core Cover carrying a placeholder image with a
 * color wash over it, wrapping its own content.
 *
 * Sections that sit on photography are structural, not decorative — the band
 * exists whether or not the final image has been chosen — so the wireframe
 * ships them with a placeholder and the overlay already wired. Swapping the
 * image and the overlay color is then a two-field edit in the editor.
 *
 * @param array<int, string> $inner   Inner block markup.
 * @param string             $file    Placeholder image basename.
 * @param string             $overlay Palette slug for the wash.
 * @param int                $dim     Overlay opacity, 0-100.
 * @param string             $text    Optional palette slug for the text color.
 */
function wptpl_cover( array $inner, string $file = 'cta-bg', string $overlay = 'secondary', int $dim = 55, string $text = '' ): string {
	$url   = get_template_directory_uri() . '/assets/placeholders/' . $file . '.jpg';
	$attrs = array(
		'url'                 => $url,
		'dimRatio'            => $dim,
		'overlayColor'        => $overlay,
		'isUserOverlayColor'  => true,
		'minHeight'           => 0,
		'sizeSlug'            => 'large',
		'align'               => 'full',
		'layout'              => array( 'type' => 'constrained' ),
	);
	$classes = 'wp-block-cover alignfull';
	if ( '' !== $text ) {
		$attrs['textColor'] = $text;
		$classes           .= ' has-' . $text . '-color has-text-color';
	}

	$open = sprintf(
		'<div class="%s"><span aria-hidden="true" class="wp-block-cover__background has-%s-background-color has-background-dim-%d has-background-dim"></span>'
			. '<img class="wp-block-cover__image-background" alt="" src="%s" data-object-fit="cover"/>'
			. '<div class="wp-block-cover__inner-container">',
		esc_attr( $classes ),
		esc_attr( $overlay ),
		$dim,
		esc_url( $url )
	);

	return wptpl_wrap( 'cover', $attrs, $open, '</div></div>', $inner );
}

/**
 * Placeholder image from the theme's own wireframe boxes.
 *
 * @param string $file One of hero, portrait, steps-bg, cta-bg, service-card, guide-card.
 * @param string $classname Extra CSS classes.
 */
function wptpl_image( string $file, string $classname = '', string $width = '' ): string {
	$url   = get_template_directory_uri() . '/assets/placeholders/' . $file . '.jpg';
	$attrs = array();
	if ( '' !== $width ) {
		$attrs['width']  = $width;
		$attrs['height'] = 'auto';
	}
	$attrs['sizeSlug'] = 'large';
	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
	}
	$classes = trim( 'wp-block-image size-large ' . $classname );
	$style   = ( '' !== $width ) ? sprintf( ' style="width:%s;height:auto"', esc_attr( $width ) ) : '';

	return sprintf(
		"<!-- wp:image%s -->\n<figure class=\"%s\"><img src=\"%s\" alt=\"\"%s/></figure>\n<!-- /wp:image -->",
		wptpl_attrs( $attrs ),
		$classes,
		esc_url( $url ),
		$style
	);
}

/**
 * A core Group wrapping inner blocks, with optional background/text colors.
 *
 * `wptpl_section()` builds full-width section BANDS; this is the plain group
 * used inside them — a tinted card, a column's contents, a constrained run of
 * copy. It never carries `alignfull` or the section rhythm class.
 *
 * @param array<int, string> $inner      Inner block markup.
 * @param string             $bg         Palette slug for the background, or ''.
 * @param string             $text       Palette slug for the text color, or ''.
 * @param string             $classname  Extra CSS classes.
 * @param string             $margin_top Top margin (e.g. "2.5rem"), or ''.
 */
function wptpl_group( array $inner, string $bg = '', string $text = '', string $classname = '', string $margin_top = '' ): string {
	$attrs   = array();
	$classes = 'wp-block-group';
	$style   = '';

	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
		$classes           .= ' ' . $classname;
	}
	if ( '' !== $bg ) {
		$attrs['backgroundColor'] = $bg;
	}
	if ( '' !== $text ) {
		$attrs['textColor'] = $text;
		$classes           .= ' has-' . $text . '-color';
	}
	if ( '' !== $bg ) {
		$classes .= ' has-' . $bg . '-background-color';
	}
	if ( '' !== $text || '' !== $bg ) {
		$classes .= ( '' !== $text ? ' has-text-color' : '' ) . ( '' !== $bg ? ' has-background' : '' );
	}
	if ( '' !== $margin_top ) {
		$attrs['style'] = array( 'spacing' => array( 'margin' => array( 'top' => $margin_top ) ) );
		$style          = sprintf( ' style="margin-top:%s"', esc_attr( $margin_top ) );
	}

	$attrs['layout'] = array( 'type' => 'constrained' );

	return wptpl_wrap(
		'group',
		$attrs,
		sprintf( '<div class="%s"%s>', esc_attr( $classes ), $style ),
		'</div>',
		$inner
	);
}

/**
 * A raw core/html block.
 *
 * Used for the two things the block set has no first-class element for: the
 * numbered circles on hand-built step rows, and a standalone link styled as a
 * button outside a CTA block.
 *
 * @param string $html Markup to emit verbatim.
 */
function wptpl_html( string $html ): string {
	return "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->";
}

/**
 * A feature-card with an icon beside its title.
 *
 * `horizontal-header` is what puts the icon and the title on one line with the
 * body beneath them. Without it the icon stacks above the title and the card
 * grows about a third taller, which breaks the row when its neighbours are
 * shorter. The icon and background slots ship with placeholders so both are
 * visible in the editor and swapping them is a field edit.
 *
 * @param int $title Approximate title length.
 * @param int $text  Approximate body length.
 */
function wptpl_card_icon( int $title, int $text ): string {
	return wptpl_block(
		'feature-card',
		array(
			'title'              => wptpl_lorem_len( $title ),
			'text'               => wptpl_lorem_len( $text ),
			'layout'             => 'horizontal-header',
			'bordered'           => false,
			'iconImageUrl'       => get_template_directory_uri() . '/assets/placeholders/guide-card.jpg',
			'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/service-card.jpg',
		)
	);
}

/**
 * Lorem ipsum sized to an approximate character count.
 *
 * The three fixed lengths below are enough when a field just needs *some*
 * placeholder. They are not enough when the point is for the wireframe to sit
 * the way the finished page will: a heading that wraps to two lines, a bio
 * that fills its column, a card whose body is shorter than its neighbour's.
 * Those depend on how much text is there, so this builds to a target length,
 * breaking into sentences so a long field does not read as one unbroken run.
 *
 * Result lands at or just under `$chars`, always ending in a period.
 *
 * @param int $chars Approximate target length.
 */
function wptpl_lorem_len( int $chars ): string {
	static $words = null;
	if ( null === $words ) {
		$words = explode(
			' ',
			'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et '
			. 'dolore magna aliqua ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip '
			. 'ex ea commodo consequat duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu '
			. 'fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt in culpa qui officia deserunt '
			. 'mollit anim id est laborum curabitur pretium tincidunt lacus nulla gravida orci a odio nullam varius '
			. 'turpis et commodo pharetra est eros suscipit magna in dui magna vel ornare enim'
		);
	}

	// Every call starts further into the word list than the last. Without this
	// each short field got the same opening words, so a row of three cards read
	// as the same sentence three times — which looks like a bug in the layout
	// rather than like placeholder copy. The counter is per-process and the
	// call order is fixed, so a re-run still produces identical markup.
	static $cursor = 0;

	$chars  = max( 12, $chars );
	$total  = count( $words );
	$budget = $chars - 1; // the closing period takes the last character
	$out    = '';
	$len    = 0;
	$i      = $cursor;
	$cursor = ( $cursor + 5 ) % $total;

	while ( $len < $budget ) {
		$word = $words[ $i % $total ];
		++$i;
		$next     = ( '' === $out ) ? ucfirst( $word ) : $out . ' ' . $word;
		$next_len = strlen( $next );
		if ( $next_len > $budget ) {
			// Too long only because of THIS word. A shorter one further along
			// may still fit, and on a short target that is the difference
			// between hitting the length and falling a third short of it.
			$fitted = false;
			for ( $skip = 1; $skip <= 6; $skip++ ) {
				$candidate = $words[ ( $i + $skip ) % $total ];
				$try       = ( '' === $out ) ? ucfirst( $candidate ) : $out . ' ' . $candidate;
				if ( strlen( $try ) <= $budget ) {
					$out    = $try;
					$len    = strlen( $try );
					$i     += $skip + 1;
					$fitted = true;
					break;
				}
			}
			if ( ! $fitted ) {
				break;
			}
			continue;
		}
		$out = $next;
		$len = $next_len;
		// Break the run into sentences so a long field does not read as one
		// unbroken line — matches how real copy sits in these fields.
		if ( 0 === $i % 14 && $len < $budget - 19 ) {
			$out .= '. ' . ucfirst( $words[ $i % $total ] );
			$len  = strlen( $out );
			++$i;
		}
	}

	return $out . '.';
}
