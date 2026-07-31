<?php
/**
 * Server-side render for wptpl/steps.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_items           = wptpl_attr_array( $attributes, 'items' );
$wptpl_heading         = wptpl_attr_html( $attributes, 'heading' );
$wptpl_intro           = wptpl_attr_html( $attributes, 'intro' );
$wptpl_show_cta        = wptpl_attr_bool( $attributes, 'showCta' );
$wptpl_cta_text        = wptpl_attr_text( $attributes, 'ctaText' );
$wptpl_cta_url         = wptpl_attr_url( $attributes, 'ctaUrl' );
$wptpl_microcopy       = wptpl_attr_text( $attributes, 'microcopy' );
$wptpl_bg_image_url    = wptpl_attr_url( $attributes, 'backgroundImageUrl' );
$wptpl_bg_image_alt    = wptpl_attr_text( $attributes, 'backgroundImageAlt' );
$wptpl_overlay         = wptpl_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.5 );
$wptpl_use_placeholder = wptpl_attr_bool( $attributes, 'usePlaceholder' );

// Overlay tint color. Defaults to `secondary` so every existing steps block
// renders exactly as before. Slugs map to hardcoded Tailwind bg utilities
// (already safelisted) rather than CSS vars, so the tint survives even when
// WP's preset color CSS doesn't reach the frontend. The `base` slug has no
// Tailwind utility of its own — it maps to `canvas` (see tailwind.config.js).
$wptpl_overlay_color  = wptpl_attr_enum(
	$attributes,
	'overlayColor',
	array( 'primary', 'primary-soft', 'secondary', 'accent', 'base', 'on-dark', 'muted', 'surface', 'white' ),
	'secondary'
);
$wptpl_overlay_bg_map = array(
	'primary'      => 'bg-primary',
	'primary-soft' => 'bg-primary-soft',
	'secondary'    => 'bg-secondary',
	'accent'       => 'bg-accent',
	'base'         => 'bg-canvas',
	'on-dark'      => 'bg-on-dark',
	'muted'        => 'bg-muted',
	'surface'      => 'bg-surface',
	'white'        => 'bg-white',
);
$wptpl_overlay_bg = isset( $wptpl_overlay_bg_map[ $wptpl_overlay_color ] ) ? $wptpl_overlay_bg_map[ $wptpl_overlay_color ] : 'bg-secondary';

if ( '' === $wptpl_bg_image_url && $wptpl_use_placeholder ) {
	$wptpl_bg_image_url = WPTPL_THEME_URI . '/assets/placeholders/steps-bg.jpg';
	$wptpl_bg_image_alt = '';
}

$wptpl_has_image    = '' !== $wptpl_bg_image_url;

$wptpl_count = max( 1, min( 4, count( $wptpl_items ) ) );

// Static grid classes so Tailwind can detect them during purge.
// Dynamic interpolation (e.g. `md:grid-cols-{$count}`) would silently
// collapse to grid-cols-1 because the variant is never emitted to CSS.
$wptpl_grid_cols  = array(
	1 => 'md:grid-cols-1',
	2 => 'md:grid-cols-2',
	3 => 'md:grid-cols-3',
	4 => 'md:grid-cols-4',
);
$wptpl_grid_class = isset( $wptpl_grid_cols[ $wptpl_count ] ) ? $wptpl_grid_cols[ $wptpl_count ] : 'md:grid-cols-3';

// Equal-height cards on the stacked single-column layout (mobile + narrow
// tablet): auto-rows-fr sizes every implicit row to the tallest, so the
// bordered cards share one height instead of each hugging its own text. In the
// md:grid-cols-* layout it's a no-op (a single row, already stretched).
$wptpl_grid_class .= ' auto-rows-fr';

// The band rhythm is the block's own, not the photo's: a steps band is a full
// section wherever it appears, so the padding is unconditional. It used to hang
// off `$wptpl_has_image`, which left every plain-variant band with no vertical
// padding at all — the cards ran straight into whatever followed, usually the
// footer.
$wptpl_wrapper_class = 'wptpl-steps text-center py-[6.25rem]';
if ( $wptpl_has_image ) {
	$wptpl_wrapper_class .= ' relative overflow-hidden text-white';
}

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => $wptpl_wrapper_class )
);

$wptpl_btn_class = $wptpl_has_image
	? 'wptpl-btn-accent'
	: 'wptpl-btn-primary';

// mt-4, not mt-2: the title runs at h3 and often wraps to two lines, and 8px
// under it left the body reading as a third line of the title rather than as
// its own paragraph.
$wptpl_body_class = $wptpl_has_image ? 'text-canvas/85 mt-4 font-medium' : 'text-muted mt-4 font-medium';

// One card shape for both variants. The number circle is pulled up so it
// overhangs the top border by 50%, and `pt-16` leaves room below it before the
// title. Only the border color changes with the background: canvas reads on a
// photo, and would vanish on the plain band, which takes a tinted rule instead.
// This used to be conditional, so a steps band without a photo rendered as
// three unboxed stacks of text — the same section, two different components.
$wptpl_card_class = 'relative border rounded-lg pt-16 px-6 pb-6 h-full '
	. ( $wptpl_has_image ? 'border-canvas' : 'border-primary-soft' );
$wptpl_number_class = 'w-20 h-20 rounded-full bg-accent flex items-center justify-center absolute -top-10 left-1/2 -translate-x-1/2 '
	. ( $wptpl_has_image ? 'text-canvas' : 'text-white' );

// On the photo variant the heading, step titles and number use base
// (#ffffff = canvas) rather than pure white to soften against the image.
$wptpl_heading_class = $wptpl_has_image ? 'wptpl-steps__heading text-canvas' : 'wptpl-steps__heading';
$wptpl_title_class   = $wptpl_has_image ? 'text-canvas' : '';

// The number circle is pulled up (-top-10) so it overhangs the card top, which
// eats most of a normal gap and crowds the intro. mb-20 restores breathing room
// above the circles. Unconditional, because the circle now overhangs on both
// variants.
$wptpl_header_mb = 'mb-20';
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
		<div class="absolute inset-0 <?php echo esc_attr( $wptpl_overlay_bg ); ?>" style="opacity:<?php echo esc_attr( (string) $wptpl_overlay ); ?>" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="wptpl-container-md <?php echo $wptpl_has_image ? 'relative z-10' : ''; ?>">
		<?php if ( '' !== $wptpl_heading || '' !== $wptpl_intro ) : ?>
			<div class="<?php echo esc_attr( $wptpl_header_mb ); ?>">
				<?php if ( '' !== $wptpl_heading ) : ?>
					<h2 class="<?php echo esc_attr( $wptpl_heading_class ); ?>"><?php echo $wptpl_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $wptpl_intro ) : ?>
					<p class="mt-3 font-bold text-xl <?php echo $wptpl_has_image ? 'text-canvas/85' : 'text-muted'; ?>"><?php echo $wptpl_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="grid gap-6 grid-cols-1 <?php echo esc_attr( $wptpl_grid_class ); ?>">
			<?php foreach ( $wptpl_items as $wptpl_index => $wptpl_item ) : ?>
				<div class="<?php echo esc_attr( $wptpl_card_class ); ?>">
					<div class="<?php echo esc_attr( $wptpl_number_class ); ?>" style="font-family:Arial,Helvetica,sans-serif;font-weight:600;font-size:3.5rem;">
						<?php echo (int) ( $wptpl_index + 1 ); ?>
					</div>
					<h3<?php echo $wptpl_title_class ? ' class="' . esc_attr( $wptpl_title_class ) . '"' : ''; ?>><?php echo wptpl_attr_html( $wptpl_item, 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<p class="<?php echo esc_attr( $wptpl_body_class ); ?>"><?php echo wptpl_attr_html( $wptpl_item, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $wptpl_show_cta && '' !== $wptpl_cta_text ) : ?>
			<div class="mt-10">
				<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="<?php echo esc_attr( $wptpl_btn_class ); ?>">
					<?php echo esc_html( $wptpl_cta_text ); ?>
				</a>
				<?php if ( '' !== $wptpl_microcopy ) : ?>
					<p class="mt-3 text-sm <?php echo $wptpl_has_image ? 'text-white/70' : 'text-muted'; ?>"><?php echo esc_html( $wptpl_microcopy ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
