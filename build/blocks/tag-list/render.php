<?php
/**
 * Server-side render for soywd/tag-list.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_items     = soywd_attr_array( $attributes, 'items' );
$soywd_alignment = soywd_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'center' );
$soywd_variant   = soywd_attr_enum( $attributes, 'variant', array( 'outline', 'filled' ), 'outline' );
$soywd_border    = soywd_attr_color( $attributes, 'pillBorderColor' );

$soywd_justify = 'center' === $soywd_alignment
	? 'justify-center'
	: ( 'right' === $soywd_alignment ? 'justify-end' : 'justify-start' );

// Outline pills default to a translucent currentColor border. A custom
// pillBorderColor (outline only) swaps that for a solid color via inline style.
$soywd_outline_border = ( 'outline' === $soywd_variant && '' !== $soywd_border ) ? 'border' : 'border border-current/60';

$soywd_tag_class = 'filled' === $soywd_variant
	? 'inline-block bg-secondary text-white px-4 py-1.5 rounded-2xl text-xs border border-secondary'
	: 'inline-block ' . $soywd_outline_border . ' px-4 py-1.5 rounded-2xl text-xs';

$soywd_tag_style = ( 'outline' === $soywd_variant && '' !== $soywd_border )
	? ' style="border-color:' . esc_attr( $soywd_border ) . '"'
	: '';

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'soywd-tag-list' )
);

// Items that will actually render (non-empty label).
$soywd_visible = array_values(
	array_filter(
		$soywd_items,
		function ( $soywd_item ) {
			return '' !== soywd_attr_text( $soywd_item, 'label' );
		}
	)
);

// Force a row break after a fixed number of pills (lg+ only). A full-basis
// spacer forces the flex to wrap at that point regardless of pill width, so a
// chosen layout (e.g. 3 + 3) stays stable even as the pill size changes. When
// `rowBreak` is 0 (default), fall back to auto-splitting long lists into two
// balanced rows once there are enough of them (e.g. 14 → 7 + 7). The spacer is
// hidden below `lg` so narrow screens keep wrapping naturally.
$soywd_row_break = soywd_attr_int( $attributes, 'rowBreak', 0, 99, 0 );
if ( 0 === $soywd_row_break && count( $soywd_visible ) >= 8 ) {
	$soywd_row_break = (int) ceil( count( $soywd_visible ) / 2 );
}
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<ul class="flex flex-wrap gap-2 <?php echo esc_attr( $soywd_justify ); ?>">
		<?php foreach ( $soywd_visible as $soywd_index => $soywd_item ) : ?>
			<?php
			$soywd_label = soywd_attr_text( $soywd_item, 'label' );
			$soywd_url   = soywd_attr_url( $soywd_item, 'url' );
			?>
			<li>
				<?php if ( '' !== $soywd_url ) : ?>
					<a href="<?php echo esc_url( $soywd_url ); ?>" class="<?php echo esc_attr( $soywd_tag_class ); ?>"<?php echo $soywd_tag_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $soywd_label ); ?>
					</a>
				<?php else : ?>
					<span class="<?php echo esc_attr( $soywd_tag_class ); ?>"<?php echo $soywd_tag_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $soywd_label ); ?>
					</span>
				<?php endif; ?>
			</li>
			<?php if ( 0 !== $soywd_row_break && $soywd_index + 1 === $soywd_row_break ) : ?>
				<li class="hidden lg:block basis-full" aria-hidden="true"></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
