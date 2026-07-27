<?php
/**
 * Server-side render for soywd/cta-banner.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_btn_styles      = array( 'auto', 'accent', 'sage', 'cream', 'primary', 'outline' );
$soywd_eyebrow         = soywd_attr_html( $attributes, 'eyebrow' );
$soywd_headline        = soywd_attr_html( $attributes, 'headline' );
$soywd_text            = soywd_attr_html( $attributes, 'text' );
$soywd_cta_text        = soywd_attr_text( $attributes, 'ctaText' );
$soywd_cta_url         = soywd_attr_url( $attributes, 'ctaUrl' );
$soywd_cta_style       = soywd_attr_enum( $attributes, 'ctaStyle', $soywd_btn_styles, 'auto' );
$soywd_sec_cta_text    = soywd_attr_text( $attributes, 'secondaryCtaText' );
$soywd_sec_cta_url     = soywd_attr_url( $attributes, 'secondaryCtaUrl' );
$soywd_sec_cta_style   = soywd_attr_enum( $attributes, 'secondaryCtaStyle', $soywd_btn_styles, 'sage' );
$soywd_sec_cta_color   = soywd_attr_color( $attributes, 'secondaryCtaTextColor' );
$soywd_btn_layout      = soywd_attr_enum( $attributes, 'buttonLayout', array( 'row', 'column' ), 'row' );
$soywd_theme           = soywd_attr_enum( $attributes, 'theme', array( 'dark', 'light' ), 'dark' );
$soywd_is_dark         = 'dark' === $soywd_theme;
$soywd_bg_image_url    = soywd_attr_url( $attributes, 'backgroundImageUrl' );
$soywd_bg_image_alt    = soywd_attr_text( $attributes, 'backgroundImageAlt' );
$soywd_overlay         = soywd_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.45 );
$soywd_headline_color  = soywd_attr_color( $attributes, 'headlineColor' );
$soywd_body_color      = soywd_attr_color( $attributes, 'bodyColor' );
$soywd_eyebrow_color   = soywd_attr_color( $attributes, 'eyebrowColor' );

// Auto-fill placeholder for the dark theme variant so the closing CTA always has a photo backdrop.
if ( '' === $soywd_bg_image_url && $soywd_is_dark ) {
	$soywd_bg_image_url = SOYWD_THEME_URI . '/assets/placeholders/cta-bg.jpg';
	$soywd_bg_image_alt = '';
}

$soywd_has_image = '' !== $soywd_bg_image_url;

$soywd_solid_bg       = $soywd_is_dark ? 'bg-secondary text-white' : 'bg-cream text-contrast';
$soywd_wrapper_class  = 'soywd-cta-banner relative text-center py-[6.25rem] px-6 overflow-hidden ' . ( $soywd_has_image ? 'text-white' : $soywd_solid_bg );
$soywd_eyebrow_class  = 'text-xs uppercase tracking-widest mb-3 ' . ( $soywd_has_image || $soywd_is_dark ? 'text-white/70' : 'text-muted' );
$soywd_text_class     = 'mt-4 max-w-2xl mx-auto text-xl ' . ( $soywd_has_image || $soywd_is_dark ? 'text-white/70' : 'text-muted' );
/*
 * Resolve a button style enum to its brand button class. The "auto" default
 * keeps the original context-aware behavior so existing banners are unchanged:
 *   photo background → sage button with dark text (matches Figma final CTA),
 *   solid dark theme → cream button, light theme → primary olive button.
 * Any explicit value (accent/sage/cream/primary/outline) lets a single banner
 * override its buttons — e.g. clay primary + sage secondary — without touching
 * other pages.
 */
$soywd_resolve_btn = static function ( string $soywd_style ) use ( $soywd_has_image, $soywd_is_dark ): string {
	switch ( $soywd_style ) {
		case 'accent':
			return 'soywd-btn-accent';
		case 'sage':
			return 'soywd-btn-photo';
		case 'cream':
			return 'soywd-btn bg-cream text-contrast hover:bg-primary hover:text-white';
		case 'primary':
			return 'soywd-btn-primary';
		case 'outline':
			return 'soywd-btn-outline';
		case 'auto':
		default:
			if ( $soywd_has_image ) {
				return 'soywd-btn-photo';
			}
			if ( $soywd_is_dark ) {
				return 'soywd-btn bg-cream text-contrast hover:bg-primary hover:text-white';
			}
			return 'soywd-btn-primary';
	}
};

$soywd_btn_class     = $soywd_resolve_btn( $soywd_cta_style );
$soywd_sec_btn_class = $soywd_resolve_btn( $soywd_sec_cta_style );
$soywd_has_secondary = '' !== $soywd_sec_cta_text;

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => $soywd_wrapper_class )
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $soywd_has_image ) : ?>
		<?php
		echo soywd_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'src'         => $soywd_bg_image_url,
				'alt'         => $soywd_bg_image_alt,
				'class'       => 'absolute inset-0 w-full h-full object-cover',
				'aria_hidden' => true,
			)
		);
		?>
		<div class="absolute inset-0 bg-secondary" style="opacity:<?php echo esc_attr( (string) $soywd_overlay ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="relative z-10">
		<?php if ( '' !== $soywd_eyebrow ) : ?>
			<p class="<?php echo esc_attr( $soywd_eyebrow_class ); ?>"<?php echo $soywd_eyebrow_color ? ' style="color:' . esc_attr( $soywd_eyebrow_color ) . '"' : ''; ?>><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<h2 class="max-w-3xl mx-auto"<?php echo $soywd_headline_color ? ' style="color:' . esc_attr( $soywd_headline_color ) . '"' : ''; ?>><?php echo $soywd_headline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php if ( '' !== $soywd_text ) : ?>
			<p class="<?php echo esc_attr( $soywd_text_class ); ?>"<?php echo $soywd_body_color ? ' style="color:' . esc_attr( $soywd_body_color ) . '"' : ''; ?>><?php echo $soywd_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( '' !== $soywd_cta_text || $soywd_has_secondary ) : ?>
			<div class="mt-6 flex gap-3 justify-center <?php echo 'column' === $soywd_btn_layout ? 'flex-col items-center' : 'flex-wrap'; ?>">
				<?php if ( '' !== $soywd_cta_text ) : ?>
					<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="<?php echo esc_attr( $soywd_btn_class ); ?>">
						<?php echo esc_html( $soywd_cta_text ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $soywd_has_secondary ) : ?>
					<a href="<?php echo esc_url( $soywd_sec_cta_url ? $soywd_sec_cta_url : '#' ); ?>" class="<?php echo esc_attr( $soywd_sec_btn_class ); ?>"<?php echo $soywd_sec_cta_color ? ' style="color:' . esc_attr( $soywd_sec_cta_color ) . '"' : ''; ?>>
						<?php echo esc_html( $soywd_sec_cta_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
