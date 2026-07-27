<?php
/**
 * Server-side render for soywd/checklist.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_items      = soywd_attr_array( $attributes, 'items' );
$soywd_direction  = soywd_attr_enum( $attributes, 'direction', array( 'vertical', 'horizontal' ), 'vertical' );
$soywd_columns    = soywd_attr_int( $attributes, 'columns', 1, 4, 1 );
$soywd_theme      = soywd_attr_enum( $attributes, 'theme', array( 'light', 'dark' ), 'light' );
$soywd_icon_style = soywd_attr_enum( $attributes, 'iconStyle', array( 'check', 'plus', 'dot', 'none' ), 'check' );

$soywd_icon_glyph = array(
	'check' => '&#10003;',
	'plus'  => '+',
	'dot'   => '&bull;',
	'none'  => '',
);

$soywd_wrapper_classes = array( 'soywd-checklist' );
if ( 'dark' === $soywd_theme ) {
	$soywd_wrapper_classes[] = 'bg-secondary text-white py-4 px-6';
}

if ( 'horizontal' === $soywd_direction ) {
	$soywd_list_classes = 'flex flex-wrap items-center justify-center gap-[0.8rem] md:gap-x-[4.5rem] md:gap-y-2';
} else {
	$soywd_list_classes = 'grid gap-3 grid-cols-' . $soywd_columns;
}

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => implode( ' ', $soywd_wrapper_classes ) )
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<ul class="<?php echo esc_attr( $soywd_list_classes ); ?>">
		<?php
		$soywd_visible_items = array_values(
			array_filter(
				$soywd_items,
				function ( $i ) {
					return ! empty( $i['text'] );
				}
			)
		);
		// Horizontal layout (trust bar style) renders smaller uppercase items.
		$soywd_item_classes = 'horizontal' === $soywd_direction
			? 'flex items-center gap-2 text-xs uppercase tracking-widest'
			: 'flex items-start gap-2';
		// Horizontal trust-bar labels render bold; vertical list items render
		// medium weight for readability.
		$soywd_text_classes = 'horizontal' === $soywd_direction ? 'font-bold' : 'font-medium';
		?>
		<?php foreach ( $soywd_visible_items as $soywd_item ) : ?>
			<li class="<?php echo esc_attr( $soywd_item_classes ); ?>">
				<?php if ( 'none' !== $soywd_icon_style ) : ?>
					<span aria-hidden="true" class="opacity-70"><?php echo $soywd_icon_glyph[ $soywd_icon_style ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<span<?php echo $soywd_text_classes ? ' class="' . esc_attr( $soywd_text_classes ) . '"' : ''; ?>>
				<?php
					// Hand-placed <br> are desktop line breaks; ensure a space before each so
					// that when it's hidden once the columns stack on mobile (see the
					// .soywd-checklist br rule) the words around it never run together.
					echo preg_replace( '#\s*<br\s*/?>#i', ' <br>', soywd_attr_html( $soywd_item, 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
					</span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
