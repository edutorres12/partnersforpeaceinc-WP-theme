<?php
/**
 * Server-side render for soywd/faq.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_items = soywd_attr_array( $attributes, 'items' );

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'soywd-faq soywd-container-narrow' )
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $soywd_items as $soywd_item ) : ?>
		<?php
		$soywd_q = soywd_attr_text( $soywd_item, 'question' );
		$soywd_a = soywd_attr_html( $soywd_item, 'answer' );
		if ( '' === $soywd_q ) {
			continue;
		}
		?>
		<details class="soywd-faq__item border-b border-accent py-4 group">
			<summary class="soywd-faq__summary flex items-center justify-between gap-4 cursor-pointer list-none text-xl" style="font-family:'Urbanist',sans-serif;font-weight:600;">
				<span><?php echo esc_html( $soywd_q ); ?></span>
				<span class="text-2xl text-accent shrink-0 group-open:rotate-45 transition-transform" aria-hidden="true">+</span>
			</summary>
			<div class="text-secondary mt-3 leading-relaxed"><?php echo $soywd_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</details>
	<?php endforeach; ?>
</div>
