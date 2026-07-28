<?php
/**
 * Server-side render for wptpl/cta-banner.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_btn_styles      = array( 'auto', 'accent', 'soft', 'canvas', 'primary', 'outline' );
$wptpl_eyebrow         = wptpl_attr_html( $attributes, 'eyebrow' );
$wptpl_headline        = wptpl_attr_html( $attributes, 'headline' );
$wptpl_text            = wptpl_attr_html( $attributes, 'text' );
$wptpl_cta_text        = wptpl_attr_text( $attributes, 'ctaText' );
$wptpl_cta_url         = wptpl_attr_url( $attributes, 'ctaUrl' );
$wptpl_cta_style       = wptpl_attr_enum( $attributes, 'ctaStyle', $wptpl_btn_styles, 'auto' );
$wptpl_sec_cta_text    = wptpl_attr_text( $attributes, 'secondaryCtaText' );
$wptpl_sec_cta_url     = wptpl_attr_url( $attributes, 'secondaryCtaUrl' );
$wptpl_sec_cta_style   = wptpl_attr_enum( $attributes, 'secondaryCtaStyle', $wptpl_btn_styles, 'soft' );
$wptpl_sec_cta_color   = wptpl_attr_color( $attributes, 'secondaryCtaTextColor' );
$wptpl_btn_layout      = wptpl_attr_enum( $attributes, 'buttonLayout', array( 'row', 'column' ), 'row' );
$wptpl_theme           = wptpl_attr_enum( $attributes, 'theme', array( 'dark', 'light' ), 'dark' );
$wptpl_is_dark         = 'dark' === $wptpl_theme;
$wptpl_bg_image_url    = wptpl_attr_url( $attributes, 'backgroundImageUrl' );
$wptpl_bg_image_alt    = wptpl_attr_text( $attributes, 'backgroundImageAlt' );
$wptpl_overlay         = wptpl_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.45 );
$wptpl_headline_color  = wptpl_attr_color( $attributes, 'headlineColor' );
$wptpl_body_color      = wptpl_attr_color( $attributes, 'bodyColor' );
$wptpl_eyebrow_color   = wptpl_attr_color( $attributes, 'eyebrowColor' );

// Auto-fill placeholder for the dark theme variant so the closing CTA always has a photo backdrop.
if ( '' === $wptpl_bg_image_url && $wptpl_is_dark ) {
	$wptpl_bg_image_url = WPTPL_THEME_URI . '/assets/placeholders/cta-bg.jpg';
	$wptpl_bg_image_alt = '';
}

$wptpl_has_image = '' !== $wptpl_bg_image_url;

$wptpl_solid_bg       = $wptpl_is_dark ? 'bg-secondary text-white' : 'bg-canvas text-contrast';
$wptpl_wrapper_class  = 'wptpl-cta-banner relative text-center py-[6.25rem] px-6 overflow-hidden ' . ( $wptpl_has_image ? 'text-white' : $wptpl_solid_bg );
$wptpl_eyebrow_class  = 'text-xs uppercase tracking-widest mb-3 ' . ( $wptpl_has_image || $wptpl_is_dark ? 'text-white/70' : 'text-muted' );
$wptpl_text_class     = 'mt-4 max-w-2xl mx-auto text-xl ' . ( $wptpl_has_image || $wptpl_is_dark ? 'text-white/70' : 'text-muted' );
/*
 * Resolve a button style enum to its brand button class. The "auto" default
 * keeps the original context-aware behavior so existing banners are unchanged:
 *   photo background → primary button with dark text (matches Figma final CTA),
 *   solid dark theme → canvas button, light theme → primary secondary button.
 * Any explicit value (accent/primary/canvas/primary/outline) lets a single banner
 * override its buttons — e.g. accent primary + primary secondary — without touching
 * other pages.
 */
$wptpl_resolve_btn = static function ( string $wptpl_style ) use ( $wptpl_has_image, $wptpl_is_dark ): string {
	switch ( $wptpl_style ) {
		case 'accent':
			return 'wptpl-btn-accent';
		case 'soft':
			return 'wptpl-btn-photo';
		case 'canvas':
			return 'wptpl-btn bg-canvas text-contrast hover:bg-primary hover:text-white';
		case 'primary':
			return 'wptpl-btn-primary';
		case 'outline':
			return 'wptpl-btn-outline';
		case 'auto':
		default:
			if ( $wptpl_has_image ) {
				return 'wptpl-btn-photo';
			}
			if ( $wptpl_is_dark ) {
				return 'wptpl-btn bg-canvas text-contrast hover:bg-primary hover:text-white';
			}
			return 'wptpl-btn-primary';
	}
};

$wptpl_btn_class     = $wptpl_resolve_btn( $wptpl_cta_style );
$wptpl_sec_btn_class = $wptpl_resolve_btn( $wptpl_sec_cta_style );
$wptpl_has_secondary = '' !== $wptpl_sec_cta_text;

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => $wptpl_wrapper_class )
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $wptpl_has_image ) : ?>
		<?php
		echo wptpl_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'src'         => $wptpl_bg_image_url,
				'alt'         => $wptpl_bg_image_alt,
				'class'       => 'absolute inset-0 w-full h-full object-cover',
				'aria_hidden' => true,
			)
		);
		?>
		<div class="absolute inset-0 bg-secondary" style="opacity:<?php echo esc_attr( (string) $wptpl_overlay ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="relative z-10">
		<?php if ( '' !== $wptpl_eyebrow ) : ?>
			<p class="<?php echo esc_attr( $wptpl_eyebrow_class ); ?>"<?php echo $wptpl_eyebrow_color ? ' style="color:' . esc_attr( $wptpl_eyebrow_color ) . '"' : ''; ?>><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<h2 class="max-w-3xl mx-auto"<?php echo $wptpl_headline_color ? ' style="color:' . esc_attr( $wptpl_headline_color ) . '"' : ''; ?>><?php echo $wptpl_headline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php if ( '' !== $wptpl_text ) : ?>
			<p class="<?php echo esc_attr( $wptpl_text_class ); ?>"<?php echo $wptpl_body_color ? ' style="color:' . esc_attr( $wptpl_body_color ) . '"' : ''; ?>><?php echo $wptpl_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( '' !== $wptpl_cta_text || $wptpl_has_secondary ) : ?>
			<div class="mt-6 flex gap-3 justify-center <?php echo 'column' === $wptpl_btn_layout ? 'flex-col items-center' : 'flex-wrap'; ?>">
				<?php if ( '' !== $wptpl_cta_text ) : ?>
					<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="<?php echo esc_attr( $wptpl_btn_class ); ?>">
						<?php echo esc_html( $wptpl_cta_text ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $wptpl_has_secondary ) : ?>
					<a href="<?php echo esc_url( $wptpl_sec_cta_url ? $wptpl_sec_cta_url : '#' ); ?>" class="<?php echo esc_attr( $wptpl_sec_btn_class ); ?>"<?php echo $wptpl_sec_cta_color ? ' style="color:' . esc_attr( $wptpl_sec_cta_color ) . '"' : ''; ?>>
						<?php echo esc_html( $wptpl_sec_cta_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
