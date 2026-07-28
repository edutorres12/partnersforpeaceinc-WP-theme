<?php
/**
 * Server-side render for wptpl/section-header.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_alignment = wptpl_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'center' );
$wptpl_eyebrow   = wptpl_attr_html( $attributes, 'eyebrow' );
$wptpl_headline  = wptpl_attr_html( $attributes, 'headline' );
$wptpl_intro     = wptpl_attr_html( $attributes, 'intro' );
$wptpl_level     = wptpl_attr_int( $attributes, 'headingLevel', 1, 6, 2 );
$wptpl_tag       = 'h' . $wptpl_level;
$wptpl_intro_mx  = 'center' === $wptpl_alignment ? 'mx-auto' : '';

// When the user/parent sets a custom text color, drop the default `text-muted`
// on the intro so it inherits (otherwise the utility wins and the intro stays
// muted even on a dark-bg section).
$wptpl_has_custom_text = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );
$wptpl_intro_color     = $wptpl_has_custom_text ? '' : 'text-muted';

$wptpl_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'wptpl-section-header text-' . $wptpl_alignment,
	)
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( '' !== $wptpl_eyebrow ) : ?>
		<p class="wptpl-eyebrow mb-3"><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<<?php echo esc_attr( $wptpl_tag ); ?> class="wptpl-section-header__headline"><?php echo $wptpl_headline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_tag ); ?>>
	<?php if ( '' !== $wptpl_intro ) : ?>
		<p class="wptpl-section-header__intro font-bold text-xl <?php echo esc_attr( trim( $wptpl_intro_color . ' mt-4 max-w-2xl ' . $wptpl_intro_mx ) ); ?>"><?php echo $wptpl_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
</div>
