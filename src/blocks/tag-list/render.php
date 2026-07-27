<?php
/**
 * Server-side render for wptpl/tag-list.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_items     = wptpl_attr_array( $attributes, 'items' );
$wptpl_alignment = wptpl_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'center' );
$wptpl_variant   = wptpl_attr_enum( $attributes, 'variant', array( 'outline', 'filled' ), 'outline' );
$wptpl_border    = wptpl_attr_color( $attributes, 'pillBorderColor' );

$wptpl_justify = 'center' === $wptpl_alignment
	? 'justify-center'
	: ( 'right' === $wptpl_alignment ? 'justify-end' : 'justify-start' );

// Outline pills default to a translucent currentColor border. A custom
// pillBorderColor (outline only) swaps that for a solid color via inline style.
$wptpl_outline_border = ( 'outline' === $wptpl_variant && '' !== $wptpl_border ) ? 'border' : 'border border-current/60';

$wptpl_tag_class = 'filled' === $wptpl_variant
	? 'inline-block bg-secondary text-white px-4 py-1.5 rounded-2xl text-xs border border-secondary'
	: 'inline-block ' . $wptpl_outline_border . ' px-4 py-1.5 rounded-2xl text-xs';

$wptpl_tag_style = ( 'outline' === $wptpl_variant && '' !== $wptpl_border )
	? ' style="border-color:' . esc_attr( $wptpl_border ) . '"'
	: '';

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'wptpl-tag-list' )
);

// Items that will actually render (non-empty label).
$wptpl_visible = array_values(
	array_filter(
		$wptpl_items,
		function ( $wptpl_item ) {
			return '' !== wptpl_attr_text( $wptpl_item, 'label' );
		}
	)
);

// Force a row break after a fixed number of pills (lg+ only). A full-basis
// spacer forces the flex to wrap at that point regardless of pill width, so a
// chosen layout (e.g. 3 + 3) stays stable even as the pill size changes. When
// `rowBreak` is 0 (default), fall back to auto-splitting long lists into two
// balanced rows once there are enough of them (e.g. 14 → 7 + 7). The spacer is
// hidden below `lg` so narrow screens keep wrapping naturally.
$wptpl_row_break = wptpl_attr_int( $attributes, 'rowBreak', 0, 99, 0 );
if ( 0 === $wptpl_row_break && count( $wptpl_visible ) >= 8 ) {
	$wptpl_row_break = (int) ceil( count( $wptpl_visible ) / 2 );
}
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<ul class="flex flex-wrap gap-2 <?php echo esc_attr( $wptpl_justify ); ?>">
		<?php foreach ( $wptpl_visible as $wptpl_index => $wptpl_item ) : ?>
			<?php
			$wptpl_label = wptpl_attr_text( $wptpl_item, 'label' );
			$wptpl_url   = wptpl_attr_url( $wptpl_item, 'url' );
			?>
			<li>
				<?php if ( '' !== $wptpl_url ) : ?>
					<a href="<?php echo esc_url( $wptpl_url ); ?>" class="<?php echo esc_attr( $wptpl_tag_class ); ?>"<?php echo $wptpl_tag_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $wptpl_label ); ?>
					</a>
				<?php else : ?>
					<span class="<?php echo esc_attr( $wptpl_tag_class ); ?>"<?php echo $wptpl_tag_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $wptpl_label ); ?>
					</span>
				<?php endif; ?>
			</li>
			<?php if ( 0 !== $wptpl_row_break && $wptpl_index + 1 === $wptpl_row_break ) : ?>
				<li class="hidden lg:block basis-full" aria-hidden="true"></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
