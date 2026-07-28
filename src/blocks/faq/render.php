<?php
/**
 * Server-side render for wptpl/faq.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_items = wptpl_attr_array( $attributes, 'items' );

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'wptpl-faq wptpl-container-narrow' )
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $wptpl_items as $wptpl_item ) : ?>
		<?php
		$wptpl_q = wptpl_attr_text( $wptpl_item, 'question' );
		$wptpl_a = wptpl_attr_html( $wptpl_item, 'answer' );
		if ( '' === $wptpl_q ) {
			continue;
		}
		?>
		<details class="wptpl-faq__item border-b border-accent py-4 group">
			<summary class="wptpl-faq__summary flex items-center justify-between gap-4 cursor-pointer list-none text-xl" style="font-family:Arial,Helvetica,sans-serif;font-weight:600;">
				<span><?php echo esc_html( $wptpl_q ); ?></span>
				<span class="text-2xl text-accent shrink-0 group-open:rotate-45 transition-transform" aria-hidden="true">+</span>
			</summary>
			<div class="text-secondary mt-3 leading-relaxed"><?php echo $wptpl_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</details>
	<?php endforeach; ?>
</div>
