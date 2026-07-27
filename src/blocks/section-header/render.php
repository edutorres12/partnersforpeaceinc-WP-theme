<?php
/**
 * Server-side render for soywd/section-header.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_alignment = soywd_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'center' );
$soywd_eyebrow   = soywd_attr_html( $attributes, 'eyebrow' );
$soywd_headline  = soywd_attr_html( $attributes, 'headline' );
$soywd_intro     = soywd_attr_html( $attributes, 'intro' );
$soywd_level     = soywd_attr_int( $attributes, 'headingLevel', 1, 6, 2 );
$soywd_tag       = 'h' . $soywd_level;
$soywd_intro_mx  = 'center' === $soywd_alignment ? 'mx-auto' : '';

// When the user/parent sets a custom text color, drop the default `text-muted`
// on the intro so it inherits (otherwise the utility wins and the intro stays
// taupe even on a dark-bg section).
$soywd_has_custom_text = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );
$soywd_intro_color     = $soywd_has_custom_text ? '' : 'text-muted';

$soywd_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'soywd-section-header text-' . $soywd_alignment,
	)
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( '' !== $soywd_eyebrow ) : ?>
		<p class="soywd-eyebrow mb-3"><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<<?php echo esc_attr( $soywd_tag ); ?> class="soywd-section-header__headline"><?php echo $soywd_headline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_tag ); ?>>
	<?php if ( '' !== $soywd_intro ) : ?>
		<p class="soywd-section-header__intro font-bold text-xl <?php echo esc_attr( trim( $soywd_intro_color . ' mt-4 max-w-2xl ' . $soywd_intro_mx ) ); ?>"><?php echo $soywd_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
</div>
