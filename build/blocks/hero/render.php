<?php
/**
 * Server-side render for soywd/hero.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_layout    = soywd_attr_enum( $attributes, 'layout', array( 'split', 'centered' ), 'split' );
$soywd_alignment = soywd_attr_enum( $attributes, 'alignment', array( 'left', 'center', 'right' ), 'left' );
$soywd_htag      = 'h' . soywd_attr_int( $attributes, 'headingLevel', 1, 2, 1 );

$soywd_eyebrow        = soywd_attr_html( $attributes, 'eyebrow' );
$soywd_title          = soywd_attr_html( $attributes, 'title' );
$soywd_subtitle       = soywd_attr_html( $attributes, 'subtitle' );
$soywd_cta_text       = soywd_attr_text( $attributes, 'ctaText' );
$soywd_cta_url        = soywd_attr_url( $attributes, 'ctaUrl' );
$soywd_sec_text       = soywd_attr_text( $attributes, 'secondaryCtaText' );
$soywd_sec_url        = soywd_attr_url( $attributes, 'secondaryCtaUrl' );
$soywd_microcopy      = soywd_attr_text( $attributes, 'microcopy' );
$soywd_image_url      = soywd_attr_url( $attributes, 'imageUrl' );
$soywd_image_alt      = soywd_attr_text( $attributes, 'imageAlt' );
$soywd_bg_image_url   = soywd_attr_url( $attributes, 'backgroundImageUrl' );
$soywd_bg_image_alt   = soywd_attr_text( $attributes, 'backgroundImageAlt' );
$soywd_overlay        = soywd_attr_float( $attributes, 'overlayOpacity', 0.0, 0.9, 0.5 );
$soywd_overlay_color  = soywd_attr_color( $attributes, 'overlayColor' );
$soywd_title_color    = soywd_attr_color( $attributes, 'titleColor' );
$soywd_subtitle_color = soywd_attr_color( $attributes, 'subtitleColor' );

$soywd_has_bg = '' !== $soywd_bg_image_url;

$soywd_btn_align = 'center' === $soywd_alignment
	? 'justify-center'
	: ( 'right' === $soywd_alignment ? 'justify-end' : 'justify-start' );

$soywd_subtitle_mx = 'center' === $soywd_alignment ? 'mx-auto' : '';

/*
 * Overlay variant: full-bleed background image + dark overlay + content on top.
 * Triggered whenever a background image is set, regardless of layout. Text is
 * light (cream) by default; the split/centered code path below is untouched.
 */
if ( $soywd_has_bg ) {
	$soywd_wrapper = get_block_wrapper_attributes(
		array( 'class' => 'soywd-hero relative overflow-hidden text-' . $soywd_alignment )
	);
	?>
	<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		echo soywd_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'src'           => $soywd_bg_image_url,
				'alt'           => $soywd_bg_image_alt,
				'class'         => 'absolute inset-0 w-full h-full object-cover',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'aria_hidden'   => true,
			)
		);
		?>
		<div class="absolute inset-0 <?php echo $soywd_overlay_color ? '' : 'bg-secondary'; ?>" style="<?php echo $soywd_overlay_color ? 'background-color:' . esc_attr( $soywd_overlay_color ) . ';' : ''; ?>opacity:<?php echo esc_attr( (string) $soywd_overlay ); ?>" aria-hidden="true"></div>
		<div class="relative z-10 flex items-center min-h-[60vh] py-[6.25rem]">
			<div class="soywd-container">
				<?php if ( '' !== $soywd_eyebrow ) : ?>
					<p class="soywd-eyebrow text-cream/80 mb-3"><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<<?php echo esc_attr( $soywd_htag ); ?> class="mb-6 <?php echo $soywd_title_color ? '' : 'text-cream'; ?>"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
				<?php if ( '' !== $soywd_subtitle ) : ?>
					<p class="soywd-hero__subtitle mb-8 max-w-xl font-medium leading-relaxed <?php echo esc_attr( $soywd_subtitle_mx ); ?> <?php echo $soywd_subtitle_color ? '' : 'text-cream/85'; ?>"<?php echo $soywd_subtitle_color ? ' style="color:' . esc_attr( $soywd_subtitle_color ) . '"' : ''; ?>><?php echo $soywd_subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<div class="flex gap-3 flex-wrap <?php echo esc_attr( $soywd_btn_align ); ?>">
					<?php if ( '' !== $soywd_cta_text ) : ?>
						<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="soywd-btn-accent">
							<?php echo esc_html( $soywd_cta_text ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $soywd_sec_text ) : ?>
						<a href="<?php echo esc_url( $soywd_sec_url ? $soywd_sec_url : '#' ); ?>" class="soywd-btn-outline">
							<?php echo esc_html( $soywd_sec_text ); ?>
						</a>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $soywd_microcopy ) : ?>
					<p class="mt-4 text-sm text-cream/70"><?php echo esc_html( $soywd_microcopy ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	return;
}

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'soywd-hero text-' . $soywd_alignment )
);

$soywd_text_col_style = 'padding-left:max(2rem, calc((100vw - 1400px) / 2 + 2rem));padding-right:2rem;';

ob_start();
?>
<div class="py-[6.25rem] flex flex-col justify-center" style="<?php echo esc_attr( $soywd_text_col_style ); ?>">
	<?php if ( '' !== $soywd_eyebrow ) : ?>
		<p class="soywd-eyebrow mb-3"><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<<?php echo esc_attr( $soywd_htag ); ?> class="mb-6 <?php echo $soywd_title_color ? '' : 'text-primary'; ?>"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
	<?php if ( '' !== $soywd_subtitle ) : ?>
		<p class="soywd-hero__subtitle mb-8 max-w-xl font-medium leading-relaxed <?php echo $soywd_subtitle_color ? '' : 'text-muted'; ?>"<?php echo $soywd_subtitle_color ? ' style="color:' . esc_attr( $soywd_subtitle_color ) . '"' : ''; ?>><?php echo $soywd_subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
	<div class="flex gap-3 flex-wrap <?php echo esc_attr( $soywd_btn_align ); ?>">
		<?php if ( '' !== $soywd_cta_text ) : ?>
			<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="soywd-btn-accent">
				<?php echo esc_html( $soywd_cta_text ); ?>
			</a>
		<?php endif; ?>
		<?php if ( '' !== $soywd_sec_text ) : ?>
			<a href="<?php echo esc_url( $soywd_sec_url ? $soywd_sec_url : '#' ); ?>" class="soywd-btn-outline">
				<?php echo esc_html( $soywd_sec_text ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php if ( '' !== $soywd_microcopy ) : ?>
		<p class="mt-4 text-sm text-muted"><?php echo esc_html( $soywd_microcopy ); ?></p>
	<?php endif; ?>
</div>
<?php
$soywd_text_col = ob_get_clean();
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'split' === $soywd_layout ) : ?>
		<div class="grid md:grid-cols-2 gap-0 items-stretch">
			<?php echo $soywd_text_col; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="min-h-[520px] flex items-center justify-center overflow-hidden">
				<?php
				$soywd_hero_src = '' !== $soywd_image_url
					? $soywd_image_url
					: SOYWD_THEME_URI . '/assets/placeholders/hero.jpg';
				echo soywd_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'src'           => $soywd_hero_src,
						'alt'           => $soywd_image_alt,
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
			<?php echo $soywd_text_col; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
</div>
