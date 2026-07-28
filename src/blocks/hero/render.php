<?php
/**
 * Server-side render for wptpl/hero.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_layout    = wptpl_attr_enum( $attributes, 'layout', array( 'split', 'centered' ), 'split' );
$wptpl_alignment = wptpl_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'left' );
$wptpl_htag      = 'h' . wptpl_attr_int( $attributes, 'headingLevel', 1, 2, 1 );

$wptpl_eyebrow        = wptpl_attr_html( $attributes, 'eyebrow' );
$wptpl_title          = wptpl_attr_html( $attributes, 'title' );
$wptpl_subtitle       = wptpl_attr_html( $attributes, 'subtitle' );
$wptpl_cta_text       = wptpl_attr_text( $attributes, 'ctaText' );
$wptpl_cta_url        = wptpl_attr_url( $attributes, 'ctaUrl' );
$wptpl_sec_text       = wptpl_attr_text( $attributes, 'secondaryCtaText' );
$wptpl_sec_url        = wptpl_attr_url( $attributes, 'secondaryCtaUrl' );
$wptpl_microcopy      = wptpl_attr_text( $attributes, 'microcopy' );
$wptpl_image_url      = wptpl_attr_url( $attributes, 'imageUrl' );
$wptpl_image_alt      = wptpl_attr_text( $attributes, 'imageAlt' );
$wptpl_bg_image_url   = wptpl_attr_url( $attributes, 'backgroundImageUrl' );
$wptpl_bg_image_alt   = wptpl_attr_text( $attributes, 'backgroundImageAlt' );
$wptpl_overlay        = wptpl_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.5 );
$wptpl_overlay_color  = wptpl_attr_color( $attributes, 'overlayColor' );
$wptpl_title_color    = wptpl_attr_color( $attributes, 'titleColor' );
$wptpl_subtitle_color = wptpl_attr_color( $attributes, 'subtitleColor' );

$wptpl_has_bg = '' !== $wptpl_bg_image_url;

$wptpl_btn_align = 'center' === $wptpl_alignment
	? 'justify-center'
	: ( 'right' === $wptpl_alignment ? 'justify-end' : 'justify-start' );

$wptpl_subtitle_mx = 'center' === $wptpl_alignment ? 'mx-auto' : '';

/*
 * Overlay variant: full-bleed background image + dark overlay + content on top.
 * Triggered whenever a background image is set, regardless of layout. Text is
 * light (canvas) by default; the split/centered code path below is untouched.
 */
if ( $wptpl_has_bg ) {
	$wptpl_wrapper = get_block_wrapper_attributes(
		array( 'class' => 'wptpl-hero relative overflow-hidden text-' . $wptpl_alignment )
	);
	?>
	<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		echo wptpl_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'src'           => $wptpl_bg_image_url,
				'alt'           => $wptpl_bg_image_alt,
				'class'         => 'absolute inset-0 w-full h-full object-cover',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'aria_hidden'   => true,
			)
		);
		?>
		<div class="absolute inset-0 <?php echo $wptpl_overlay_color ? '' : 'bg-secondary'; ?>" style="<?php echo $wptpl_overlay_color ? 'background-color:' . esc_attr( $wptpl_overlay_color ) . ';' : ''; ?>opacity:<?php echo esc_attr( (string) $wptpl_overlay ); ?>" aria-hidden="true"></div>
		<div class="relative z-10 flex items-center min-h-[60vh] py-[6.25rem]">
			<div class="wptpl-container">
				<?php if ( '' !== $wptpl_eyebrow ) : ?>
					<p class="wptpl-eyebrow text-canvas/80 mb-3"><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<<?php echo esc_attr( $wptpl_htag ); ?> class="mb-6 <?php echo $wptpl_title_color ? '' : 'text-canvas'; ?>"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
				<?php if ( '' !== $wptpl_subtitle ) : ?>
					<p class="wptpl-hero__subtitle mb-8 max-w-xl font-medium leading-relaxed <?php echo esc_attr( $wptpl_subtitle_mx ); ?> <?php echo $wptpl_subtitle_color ? '' : 'text-canvas/85'; ?>"<?php echo $wptpl_subtitle_color ? ' style="color:' . esc_attr( $wptpl_subtitle_color ) . '"' : ''; ?>><?php echo $wptpl_subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<div class="flex gap-3 flex-wrap <?php echo esc_attr( $wptpl_btn_align ); ?>">
					<?php if ( '' !== $wptpl_cta_text ) : ?>
						<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="wptpl-btn-accent">
							<?php echo esc_html( $wptpl_cta_text ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $wptpl_sec_text ) : ?>
						<a href="<?php echo esc_url( $wptpl_sec_url ? $wptpl_sec_url : '#' ); ?>" class="wptpl-btn-outline">
							<?php echo esc_html( $wptpl_sec_text ); ?>
						</a>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $wptpl_microcopy ) : ?>
					<p class="mt-4 text-sm text-canvas/70"><?php echo esc_html( $wptpl_microcopy ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	return;
}

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'wptpl-hero text-' . $wptpl_alignment )
);

$wptpl_text_col_style = 'padding-left:max(2rem, calc((100vw - 1400px) / 2 + 2rem));padding-right:2rem;';

ob_start();
?>
<div class="py-[6.25rem] flex flex-col justify-center" style="<?php echo esc_attr( $wptpl_text_col_style ); ?>">
	<?php if ( '' !== $wptpl_eyebrow ) : ?>
		<p class="wptpl-eyebrow mb-3"><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<<?php echo esc_attr( $wptpl_htag ); ?> class="mb-6 <?php echo $wptpl_title_color ? '' : 'text-primary'; ?>"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
	<?php if ( '' !== $wptpl_subtitle ) : ?>
		<p class="wptpl-hero__subtitle mb-8 max-w-xl font-medium leading-relaxed <?php echo $wptpl_subtitle_color ? '' : 'text-muted'; ?>"<?php echo $wptpl_subtitle_color ? ' style="color:' . esc_attr( $wptpl_subtitle_color ) . '"' : ''; ?>><?php echo $wptpl_subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<div class="flex gap-3 flex-wrap <?php echo esc_attr( $wptpl_btn_align ); ?>">
		<?php if ( '' !== $wptpl_cta_text ) : ?>
			<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="wptpl-btn-accent">
				<?php echo esc_html( $wptpl_cta_text ); ?>
			</a>
		<?php endif; ?>
		<?php if ( '' !== $wptpl_sec_text ) : ?>
			<a href="<?php echo esc_url( $wptpl_sec_url ? $wptpl_sec_url : '#' ); ?>" class="wptpl-btn-outline">
				<?php echo esc_html( $wptpl_sec_text ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php if ( '' !== $wptpl_microcopy ) : ?>
		<p class="mt-4 text-sm text-muted"><?php echo esc_html( $wptpl_microcopy ); ?></p>
	<?php endif; ?>
</div>
<?php
$wptpl_text_col = ob_get_clean();
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'split' === $wptpl_layout ) : ?>
		<div class="grid md:grid-cols-2 gap-0 items-stretch">
			<?php echo $wptpl_text_col; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="min-h-[520px] flex items-center justify-center overflow-hidden">
				<?php
				$wptpl_hero_src = '' !== $wptpl_image_url
					? $wptpl_image_url
					: WPTPL_THEME_URI . '/assets/placeholders/hero.jpg';
				echo wptpl_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'src'           => $wptpl_hero_src,
						'alt'           => $wptpl_image_alt,
						'class'         => 'w-full h-full object-cover',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</div>
		</div>
	<?php else : ?>
		<div class="max-w-3xl mx-auto">
			<?php echo $wptpl_text_col; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
</div>
