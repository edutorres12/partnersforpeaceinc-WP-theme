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
 * Marks an attribute that must be emitted as an empty string rather than
 * dropped.
 *
 * Omitting an attribute is not the same as blanking it: several blocks declare
 * placeholder defaults in their block.json, and a block rendered without the
 * attribute falls back to that default. `wptpl/cta-banner` is the case that bit
 * — leaving `text` out renders its stock "Lorem ipsum…" body under every
 * headline. Passing a bare `''` does not help either, since the filter below
 * strips empty values. Pass this instead.
 */
const WPTPL_BLANK = "\0wptpl-blank";

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
	$attrs = array_map(
		static function ( $value ) {
			return WPTPL_BLANK === $value ? '' : $value;
		},
		$attrs
	);
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
 * `$text` is not optional decoration on a dark band. The body text color comes
 * from theme.json and is tuned for the light page background, so a band that
 * sets a dark background without also setting a light text color renders dark
 * text on dark — legible in the editor, invisible on the front end. Any band
 * whose background is darker than the page needs this.
 *
 * @param array<int, string> $inner     Inner block markup.
 * @param string             $bg        Palette slug for the band background, or '' for none.
 * @param string             $container One of container, container-md, container-narrow, or '' for none.
 * @param string             $extra     Extra CSS classes for the band.
 * @param string             $text      Palette slug for the text color, or '' to inherit.
 */
function wptpl_section( array $inner, string $bg = '', string $container = 'container-md', string $extra = '', string $text = '' ): string {
	$band_classes = trim( 'wptpl-section ' . $extra );

	$band_attrs = array(
		'align'     => 'full',
		'className' => $band_classes,
	);
	$band_html_classes = 'wp-block-group alignfull ' . $band_classes;

	if ( '' !== $text ) {
		$band_attrs['textColor'] = $text;
		$band_html_classes      .= ' has-' . $text . '-color has-text-color';
	}
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
 * `$block_gap` sets the channel between the columns. Leaving it unset falls back
 * to WordPress's own default, which is narrow enough that adjacent cards read as
 * one block rather than as separate cards — the reference sets it explicitly on
 * every row where the gap matters, most often 50px.
 *
 * @param array<int, array<int, string>> $columns         Inner markup per column.
 * @param array<int, string>             $widths          Optional per-column width (e.g. "60%").
 * @param string                         $classname       Extra CSS classes for the row.
 * @param string                         $margin_top      Top margin (e.g. "3rem"), or '' for none.
 * @param bool                           $vertical_center Vertically center the columns.
 * @param array<int, string>             $col_classes     Optional per-column CSS classes (e.g. wptpl-vrule).
 * @param string                         $block_gap       Gap between columns (e.g. "50px"), or '' for the default.
 */
function wptpl_columns( array $columns, array $widths = array(), string $classname = '', string $margin_top = '', bool $vertical_center = false, array $col_classes = array(), string $block_gap = '' ): string {
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
	$spacing = array();
	if ( '' !== $margin_top ) {
		$spacing['margin'] = array( 'top' => $margin_top );
		$row_style         = sprintf( ' style="margin-top:%s"', $margin_top );
	}
	if ( '' !== $block_gap ) {
		// `left` only: WordPress reads the horizontal half of blockGap for a
		// columns row, and writing both halves makes the editor show a linked
		// gap control the design never asked for.
		$spacing['blockGap'] = array( 'left' => $block_gap );
	}
	if ( $spacing ) {
		$row_attrs['style'] = array( 'spacing' => $spacing );
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
function wptpl_paragraph( string $text, string $classname = '', string $align = '', array $typography = array(), string $margin_top = '' ): string {
	$attrs = array();
	if ( '' !== $classname ) {
		$attrs['className'] = $classname;
	}
	if ( '' !== $align ) {
		$attrs['align'] = $align;
	}

	$style = array();
	$css   = '';
	if ( '' !== $margin_top ) {
		$style['spacing'] = array( 'margin' => array( 'top' => $margin_top ) );
		$css             .= 'margin-top:' . $margin_top . ';';
	}
	if ( $typography ) {
		$style['typography'] = $typography;
		foreach ( $typography as $prop => $value ) {
			// camelCase attribute names, kebab-case CSS — the editor writes both,
			// and the saved HTML has to carry the CSS half or the paragraph loses
			// its treatment until someone opens the block.
			$css .= strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $prop ) ) . ':' . $value . ';';
		}
	}
	if ( $style ) {
		$attrs['style'] = $style;
	}

	$classes  = trim( ( '' !== $align ? 'has-text-align-' . $align . ' ' : '' ) . $classname );
	$html_att = '' !== $classes ? sprintf( ' class="%s"', $classes ) : '';
	if ( '' !== $css ) {
		$html_att .= sprintf( ' style="%s"', esc_attr( rtrim( $css, ';' ) ) );
	}

	return sprintf(
		"<!-- wp:paragraph%s -->\n<p%s>%s</p>\n<!-- /wp:paragraph -->",
		wptpl_attrs( $attrs ),
		$html_att,
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
	// A Cover sizes itself to its content, so without this the band hugs whatever
	// is inside it and reads as a strip rather than as a section. Every other
	// band gets its rhythm from `.wptpl-section`; this one has to state it,
	// because the class would fight the Cover's own layout.
	$attrs['style'] = array(
		'spacing' => array(
			'padding' => array(
				'top'    => 'var(--wp--preset--spacing--50)',
				'bottom' => 'var(--wp--preset--spacing--50)',
			),
		),
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
	$open = str_replace(
		'class="' . esc_attr( $classes ) . '"',
		'class="' . esc_attr( $classes ) . '" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"',
		$open
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
 * A tinted group with no padding is not a card — it is a colored rectangle with
 * the copy jammed against its edges. `$padding` and `$radius` are what turn it
 * into one, and any group that sets a background needs both.
 *
 * @param array<int, string> $inner      Inner block markup.
 * @param string             $bg         Palette slug for the background, or ''.
 * @param string             $text       Palette slug for the text color, or ''.
 * @param string             $classname  Extra CSS classes.
 * @param string             $margin_top Top margin (e.g. "2.5rem"), or ''.
 * @param string             $padding    Inner padding (e.g. "50px"), or ''.
 * @param string             $radius     Corner radius (e.g. "22px"), or ''.
 */
function wptpl_group( array $inner, string $bg = '', string $text = '', string $classname = '', string $margin_top = '', string $padding = '', string $radius = '' ): string {
	$attrs   = array();
	$classes = 'wp-block-group';
	$style   = '';
	$css     = '';

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
	$block_style = array();
	if ( '' !== $margin_top ) {
		$block_style['spacing']['margin'] = array( 'top' => $margin_top );
		$css                             .= 'margin-top:' . $margin_top . ';';
	}
	if ( '' !== $padding ) {
		// Accepts CSS shorthand. The block attribute is per-side, so the
		// shorthand has to be expanded — writing "3.5rem 1.5rem 1.5rem" into
		// `top` produces `padding-top: 3.5rem 1.5rem 1.5rem`, which is invalid
		// and drops the whole declaration.
		$parts = preg_split( '/\s+/', trim( $padding ) );
		switch ( count( $parts ) ) {
			case 1:
				$sides = array( $parts[0], $parts[0], $parts[0], $parts[0] );
				break;
			case 2:
				$sides = array( $parts[0], $parts[1], $parts[0], $parts[1] );
				break;
			case 3:
				$sides = array( $parts[0], $parts[1], $parts[2], $parts[1] );
				break;
			default:
				$sides = array( $parts[0], $parts[1], $parts[2], $parts[3] );
				break;
		}
		$block_style['spacing']['padding'] = array(
			'top'    => $sides[0],
			'right'  => $sides[1],
			'bottom' => $sides[2],
			'left'   => $sides[3],
		);
		$css                              .= 'padding:' . $padding . ';';
	}
	if ( '' !== $radius ) {
		$block_style['border'] = array( 'radius' => $radius );
		$css                  .= 'border-radius:' . $radius . ';';
	}
	if ( $block_style ) {
		$attrs['style'] = $block_style;
		$style          = sprintf( ' style="%s"', esc_attr( rtrim( $css, ';' ) ) );
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
 * One column of a hand-built steps row: a numbered circle, a title and a body.
 *
 * The `wptpl/steps` block owns its whole band — heading, intro, background and
 * CTA. Some sections want only the three numbered columns inside a band they
 * already control, and that is what the reference builds here out of core
 * blocks. The circle is raw HTML because no block renders one, and it is pulled
 * up so it overhangs the column's top edge; the
 * `.wptpl-steps.has-global-padding` rule in tailwind.css adds the top padding
 * that keeps it from colliding with whatever sits above.
 *
 * @param int $number Step number.
 * @param int $title  Approximate title length.
 * @param int $text   Approximate body length.
 * @return array<int, string>
 */
function wptpl_step_column( int $number, int $title, int $text ): array {
	return array(
		// The circle sits above the card and half outside it, so it is emitted
		// as a sibling with a negative bottom margin rather than as the card's
		// first child — inside the card its own top padding would push it back
		// in, and the card's rounding would clip the half meant to escape.
		wptpl_html(
			sprintf(
				'<div style="position:relative;z-index:1;margin:0 auto -2.5rem;width:5rem;height:5rem;'
					. 'border-radius:50%%;display:flex;align-items:center;justify-content:center;'
					. 'font-size:2rem;font-weight:700;background:var(--wp--preset--color--accent);'
					. 'color:var(--wp--preset--color--on-dark)">%d</div>',
				$number
			)
		),
		wptpl_group(
			array(
				wptpl_heading( wptpl_lorem_len( $title ), 2, '', 'center' ),
				wptpl_paragraph( wptpl_lorem_len( $text ), '', 'center' ),
			),
			'',
			'',
			'wptpl-step-card',
			'',
			'3.5rem 1.5rem 1.5rem',
			'12px'
		),
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
 * shorter.
 *
 * The icon slot uses the square 96px placeholder. It deliberately carries no
 * background image: the reference dresses these cards in a paper texture, which
 * is part of its identity, and a photo placeholder in that slot renders behind
 * the copy and makes the card unreadable.
 *
 * @param int $title Approximate title length.
 * @param int $text  Approximate body length.
 */
function wptpl_card_icon( int $title, int $text ): string {
	return wptpl_block(
		'feature-card',
		array(
			'title'           => wptpl_lorem_len( $title ),
			'text'            => wptpl_lorem_len( $text ),
			'layout'          => 'horizontal-header',
			'bordered'        => false,
			'iconImageUrl'    => get_template_directory_uri() . '/assets/placeholders/icon.jpg',
			// The card is light and sits on a dark band. Without its own colors it
			// inherits the band's light text, and the title disappears into the
			// card. Both have to be set on the card, not on the band.
			'backgroundColor' => 'base',
			'textColor'       => 'muted',
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
