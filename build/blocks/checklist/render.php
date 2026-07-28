<?php
/**
 * Server-side render for wptpl/checklist.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_items      = wptpl_attr_array( $attributes, 'items' );
$wptpl_direction  = wptpl_attr_enum( $attributes, 'direction', array( 'vertical', 'horizontal' ), 'vertical' );
$wptpl_columns    = wptpl_attr_int( $attributes, 'columns', 1, 4, 1 );
$wptpl_theme      = wptpl_attr_enum( $attributes, 'theme', array( 'light', 'dark' ), 'light' );
$wptpl_icon_style = wptpl_attr_enum( $attributes, 'iconStyle', array( 'check', 'plus', 'dot', 'none' ), 'check' );

$wptpl_icon_glyph = array(
	'check' => '&#10003;',
	'plus'  => '+',
	'dot'   => '&bull;',
	'none'  => '',
);

$wptpl_wrapper_classes = array( 'wptpl-checklist' );
if ( 'dark' === $wptpl_theme ) {
	$wptpl_wrapper_classes[] = 'bg-secondary text-white py-4 px-6';
}

if ( 'horizontal' === $wptpl_direction ) {
	$wptpl_list_classes = 'flex flex-wrap items-center justify-center gap-[0.8rem] md:gap-x-[4.5rem] md:gap-y-2';
} else {
	$wptpl_list_classes = 'grid gap-3 grid-cols-' . $wptpl_columns;
}

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => implode( ' ', $wptpl_wrapper_classes ) )
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<ul class="<?php echo esc_attr( $wptpl_list_classes ); ?>">
		<?php
		$wptpl_visible_items = array_values(
			array_filter(
				$wptpl_items,
				function ( $i ) {
					return ! empty( $i['text'] );
				}
			)
		);
		// Horizontal layout (trust bar style) renders smaller uppercase items.
		$wptpl_item_classes = 'horizontal' === $wptpl_direction
			? 'flex items-center gap-2 text-xs uppercase tracking-widest'
			: 'flex items-start gap-2';
		// Horizontal trust-bar labels render bold; vertical list items render
		// medium weight for readability.
		$wptpl_text_classes = 'horizontal' === $wptpl_direction ? 'font-bold' : 'font-medium';
		?>
		<?php foreach ( $wptpl_visible_items as $wptpl_item ) : ?>
			<li class="<?php echo esc_attr( $wptpl_item_classes ); ?>">
				<?php if ( 'none' !== $wptpl_icon_style ) : ?>
					<span aria-hidden="true" class="opacity-70"><?php echo $wptpl_icon_glyph[ $wptpl_icon_style ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<span<?php echo $wptpl_text_classes ? ' class="' . esc_attr( $wptpl_text_classes ) . '"' : ''; ?>>
				<?php
					// Hand-placed <br> are desktop line breaks; ensure a space before each so
					// that when it's hidden once the columns stack on mobile (see the
					// .wptpl-checklist br rule) the words around it never run together.
					echo preg_replace( '#\s*<br\s*/?>#i', ' <br>', wptpl_attr_html( $wptpl_item, 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
					</span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
