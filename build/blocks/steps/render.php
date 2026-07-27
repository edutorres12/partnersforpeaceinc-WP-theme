<?php
/**
 * Server-side render for soywd/steps.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_items           = soywd_attr_array( $attributes, 'items' );
$soywd_heading         = soywd_attr_html( $attributes, 'heading' );
$soywd_intro           = soywd_attr_html( $attributes, 'intro' );
$soywd_show_cta        = soywd_attr_bool( $attributes, 'showCta' );
$soywd_cta_text        = soywd_attr_text( $attributes, 'ctaText' );
$soywd_cta_url         = soywd_attr_url( $attributes, 'ctaUrl' );
$soywd_microcopy       = soywd_attr_text( $attributes, 'microcopy' );
$soywd_bg_image_url    = soywd_attr_url( $attributes, 'backgroundImageUrl' );
$soywd_bg_image_alt    = soywd_attr_text( $attributes, 'backgroundImageAlt' );
$soywd_overlay         = soywd_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.5 );
$soywd_use_placeholder = soywd_attr_bool( $attributes, 'usePlaceholder' );

// Overlay tint color. Defaults to `secondary` (olive) so every existing steps
// block renders exactly as before. Slugs map to hardcoded Tailwind bg utilities
// (already safelisted) rather than CSS vars, so the tint survives even when
// WP's preset color CSS doesn't reach the frontend. `base` (Ivory) has no
// Tailwind slug of its own — it shares the `cream` color.
$soywd_overlay_color  = soywd_attr_enum(
	$attributes,
	'overlayColor',
	array( 'primary', 'secondary', 'accent', 'base', 'cream-light', 'muted', 'bark', 'surface', 'white' ),
	'secondary'
);
$soywd_overlay_bg_map = array(
	'primary'     => 'bg-primary',
	'secondary'   => 'bg-secondary',
	'accent'      => 'bg-accent',
	'base'        => 'bg-cream',
	'cream-light' => 'bg-cream-light',
	'muted'       => 'bg-muted',
	'bark'        => 'bg-bark',
	'surface'     => 'bg-surface',
	'white'       => 'bg-white',
);
$soywd_overlay_bg = isset( $soywd_overlay_bg_map[ $soywd_overlay_color ] ) ? $soywd_overlay_bg_map[ $soywd_overlay_color ] : 'bg-secondary';

if ( '' === $soywd_bg_image_url && $soywd_use_placeholder ) {
	$soywd_bg_image_url = SOYWD_THEME_URI . '/assets/placeholders/steps-bg.jpg';
	$soywd_bg_image_alt = '';
}

$soywd_has_image    = '' !== $soywd_bg_image_url;

$soywd_count = max( 1, min( 4, count( $soywd_items ) ) );

// Static grid classes so Tailwind can detect them during purge.
// Dynamic interpolation (e.g. `md:grid-cols-{$count}`) would silently
// collapse to grid-cols-1 because the variant is never emitted to CSS.
$soywd_grid_cols  = array(
	1 => 'md:grid-cols-1',
	2 => 'md:grid-cols-2',
	3 => 'md:grid-cols-3',
	4 => 'md:grid-cols-4',
);
$soywd_grid_class = isset( $soywd_grid_cols[ $soywd_count ] ) ? $soywd_grid_cols[ $soywd_count ] : 'md:grid-cols-3';

// Equal-height cards on the stacked single-column layout (mobile + narrow
// tablet): auto-rows-fr sizes every implicit row to the tallest, so the
// bordered cards share one height instead of each hugging its own text. In the
// md:grid-cols-* layout it's a no-op (a single row, already stretched). Scoped
// to the photo variant because only its cards are bordered + h-full; the plain
// variant has no visible box to equalize.
$soywd_grid_class .= $soywd_has_image ? ' auto-rows-fr' : '';

$soywd_wrapper_class = 'soywd-steps text-center';
if ( $soywd_has_image ) {
	$soywd_wrapper_class .= ' relative overflow-hidden text-white py-[6.25rem]';
}

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => $soywd_wrapper_class )
);

$soywd_btn_class = $soywd_has_image
	? 'soywd-btn-accent'
	: 'soywd-btn-primary';

$soywd_body_class = $soywd_has_image ? 'text-cream/85 mt-2 font-medium' : 'text-muted mt-2 font-medium';

// When the card has a border (image bg variant), the number circle is
// pulled up so it overhangs the top border by 50%. The card gets a larger
// `pt-16` to leave room below the circle before the title.
$soywd_card_class = $soywd_has_image
	? 'relative border border-cream rounded-lg pt-16 px-6 pb-6 h-full'
	: '';
$soywd_number_class = $soywd_has_image
	? 'w-20 h-20 rounded-full bg-accent text-cream flex items-center justify-center absolute -top-10 left-1/2 -translate-x-1/2'
	: 'w-20 h-20 rounded-full bg-accent text-white flex items-center justify-center mx-auto mb-4';

// On the photo variant the heading, step titles and number use ivory
// (#e3ded4 = cream) rather than pure white to soften against the image.
$soywd_heading_class = $soywd_has_image ? 'soywd-steps__heading text-cream' : 'soywd-steps__heading';
$soywd_title_class   = $soywd_has_image ? 'text-cream' : '';

// The number circle is pulled up (-top-10) so it overhangs the card top,
// which eats most of the default mb-12 gap and crowds the intro. mb-20
// restores breathing room above the circles on the photo variant.
$soywd_header_mb = $soywd_has_image ? 'mb-20' : 'mb-12';
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
		<div class="absolute inset-0 <?php echo esc_attr( $soywd_overlay_bg ); ?>" style="opacity:<?php echo esc_attr( (string) $soywd_overlay ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="soywd-container-md <?php echo $soywd_has_image ? 'relative z-10' : ''; ?>">
		<?php if ( '' !== $soywd_heading || '' !== $soywd_intro ) : ?>
			<div class="<?php echo esc_attr( $soywd_header_mb ); ?>">
				<?php if ( '' !== $soywd_heading ) : ?>
					<h2 class="<?php echo esc_attr( $soywd_heading_class ); ?>"><?php echo $soywd_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $soywd_intro ) : ?>
					<p class="mt-3 font-bold text-xl <?php echo $soywd_has_image ? 'text-cream/85' : 'text-muted'; ?>"><?php echo $soywd_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="grid gap-6 grid-cols-1 <?php echo esc_attr( $soywd_grid_class ); ?>">
			<?php foreach ( $soywd_items as $soywd_index => $soywd_item ) : ?>
				<div class="<?php echo esc_attr( $soywd_card_class ); ?>">
					<div class="<?php echo esc_attr( $soywd_number_class ); ?>" style="font-family:'Urbanist',sans-serif;font-weight:600;font-size:3.5rem;">
						<?php echo (int) ( $soywd_index + 1 ); ?>
					</div>
					<h3<?php echo $soywd_title_class ? ' class="' . esc_attr( $soywd_title_class ) . '"' : ''; ?>><?php echo soywd_attr_html( $soywd_item, 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<p class="<?php echo esc_attr( $soywd_body_class ); ?>"><?php echo soywd_attr_html( $soywd_item, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $soywd_show_cta && '' !== $soywd_cta_text ) : ?>
			<div class="mt-10">
				<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="<?php echo esc_attr( $soywd_btn_class ); ?>">
					<?php echo esc_html( $soywd_cta_text ); ?>
				</a>
				<?php if ( '' !== $soywd_microcopy ) : ?>
					<p class="mt-3 text-sm <?php echo $soywd_has_image ? 'text-white/70' : 'text-muted'; ?>"><?php echo esc_html( $soywd_microcopy ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
