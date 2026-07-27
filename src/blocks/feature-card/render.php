<?php
/**
 * Server-side render for soywd/feature-card.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_icon          = soywd_attr_text( $attributes, 'icon' );
$soywd_icon_image    = soywd_attr_url( $attributes, 'iconImageUrl' );
$soywd_eyebrow       = soywd_attr_html( $attributes, 'eyebrow' );
$soywd_title         = soywd_attr_html( $attributes, 'title' );
$soywd_text          = soywd_attr_html( $attributes, 'text' );
$soywd_title_right   = soywd_attr_html( $attributes, 'titleRight' );
$soywd_text_right    = soywd_attr_html( $attributes, 'textRight' );
$soywd_image_url     = soywd_attr_url( $attributes, 'imageUrl' );
$soywd_image_alt     = soywd_attr_text( $attributes, 'imageAlt' );
$soywd_show_image    = soywd_attr_bool( $attributes, 'showImage', true );
$soywd_tags          = soywd_attr_array( $attributes, 'tags' );
$soywd_cta_text      = soywd_attr_text( $attributes, 'ctaText' );
$soywd_cta_url       = soywd_attr_url( $attributes, 'ctaUrl' );
$soywd_cta_style     = soywd_attr_enum( $attributes, 'ctaStyle', array( 'button', 'arrow' ), 'button' );
// Split off the CTA's last word so the arrow CTA can keep it glued to the arrow
// (in a nowrap span) — otherwise a long label wraps and strands the arrow alone
// on its own line.
$soywd_cta_trim      = trim( $soywd_cta_text );
$soywd_cta_gap       = strrpos( $soywd_cta_trim, ' ' );
$soywd_cta_head      = false === $soywd_cta_gap ? '' : substr( $soywd_cta_trim, 0, $soywd_cta_gap );
$soywd_cta_last      = false === $soywd_cta_gap ? $soywd_cta_trim : substr( $soywd_cta_trim, $soywd_cta_gap + 1 );
$soywd_centered      = soywd_attr_bool( $attributes, 'centered' );
$soywd_layout        = soywd_attr_enum( $attributes, 'layout', array( 'vertical', 'horizontal-header', 'bilingual' ), 'vertical' );
$soywd_is_bilingual  = 'bilingual' === $soywd_layout;
$soywd_bordered      = soywd_attr_bool( $attributes, 'bordered', true );
$soywd_transparent   = soywd_attr_bool( $attributes, 'transparent' );
$soywd_half_width    = soywd_attr_bool( $attributes, 'halfWidthCentered' );
$soywd_overlay_color = isset( $attributes['imageOverlayColor'] ) ? sanitize_key( (string) $attributes['imageOverlayColor'] ) : '';
$soywd_overlay_op    = soywd_attr_float( $attributes, 'imageOverlayOpacity', 0.0, 0.7, 0.25 );
$soywd_bg_image_url  = soywd_attr_url( $attributes, 'backgroundImageUrl' );
$soywd_title_color   = soywd_attr_color( $attributes, 'titleColor' );
$soywd_htag          = 'h' . soywd_attr_int( $attributes, 'headingLevel', 2, 4, 3 );

// Detect a user-set background (Gutenberg color picker or inline style).
$soywd_has_user_bg = ! empty( $attributes['backgroundColor'] )
	|| ! empty( $attributes['gradient'] )
	|| ! empty( $attributes['style']['color']['background'] )
	|| ! empty( $attributes['style']['color']['gradient'] );

// Detect a user-set text color. When set, the default `text-muted` on the
// body would override it (Tailwind utility wins over inherited has-*-color),
// so we drop it and let the body inherit. Transparent cards also drop it so
// the body inherits the parent section's text color (otherwise a transparent
// card sitting on a `muted` section would render invisible muted-on-muted body
// copy). The arrow CTA gets the light (cream) treatment only on a genuinely
// dark custom bg; on a light custom bg it inherits the card's (dark) text
// color so it stays legible. A named-color bg we can't measure here falls back
// to the legacy "light" treatment.
$soywd_has_user_text = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );
$soywd_body_color    = ( $soywd_has_user_text || $soywd_transparent ) ? '' : 'text-muted';

$soywd_bg_is_dark = $soywd_has_user_bg;
if ( $soywd_has_user_bg && ! empty( $attributes['style']['color']['background'] ) ) {
	$soywd_bg_hex = ltrim( (string) $attributes['style']['color']['background'], '#' );
	if ( 1 === preg_match( '/^[0-9a-fA-F]{6}$/', $soywd_bg_hex ) ) {
		$soywd_bg_lum = ( 0.299 * hexdec( substr( $soywd_bg_hex, 0, 2 ) ) )
			+ ( 0.587 * hexdec( substr( $soywd_bg_hex, 2, 2 ) ) )
			+ ( 0.114 * hexdec( substr( $soywd_bg_hex, 4, 2 ) ) );
		$soywd_bg_is_dark = $soywd_bg_lum < 150;
	}
}

$soywd_cta_arrow_cls = 'soywd-cta-arrow text-xs uppercase tracking-widest font-semibold';
if ( $soywd_bg_is_dark ) {
	$soywd_cta_arrow_cls .= ' soywd-cta-arrow-light';
}

// Auto-fill placeholder for service/guide-style cards (has CTA, no image, no icon).
// Skipped entirely when the card opts out of imagery via showImage = false.
$soywd_use_placeholder = $soywd_show_image
	&& '' === $soywd_image_url
	&& '' === $soywd_icon_image
	&& '' === $soywd_icon
	&& '' !== $soywd_cta_text
	&& ! $soywd_transparent
	&& ! $soywd_has_user_bg;

if ( $soywd_use_placeholder ) {
	$soywd_image_url = SOYWD_THEME_URI . '/assets/placeholders/service-card.jpg';
	$soywd_image_alt = '';
}

// Whether a main image is actually rendered (an explicit imageUrl is hidden too
// when showImage is off).
$soywd_render_image = $soywd_show_image && '' !== $soywd_image_url;

// Default card: surface (sand) bg + soft shadow gives the card a visible
// boundary when it sits on a same-toned section (e.g. ivory body). When
// the user sets a custom Gutenberg background or marks the card
// transparent, both are skipped so the wrapper bg shows through.
$soywd_card_classes = array( 'soywd-feature-card', 'rounded-[14px]', 'overflow-hidden', 'h-full', 'flex', 'flex-col' );
if ( $soywd_bordered ) {
	$soywd_card_classes[] = 'border border-muted/25';
}
if ( ! $soywd_transparent && ! $soywd_has_user_bg && '' === $soywd_bg_image_url ) {
	$soywd_card_classes[] = 'bg-surface';
	// Rest + hover shadow handled in src/tailwind.css (.soywd-feature-card).
}
if ( $soywd_centered ) {
	$soywd_card_classes[] = 'text-center';
}
// Match the rendered width of a card in a 2-column row and center it, so a
// lone "odd" card can stand on its own line instead of stretching full width.
// A bilingual card needs more room for its two language columns, so it centers
// at a wider reading width instead of the 50% half-card width.
if ( $soywd_half_width ) {
	$soywd_card_classes[] = $soywd_is_bilingual ? 'soywd-card-bilingual-centered' : 'soywd-card-half-centered';
}

$soywd_wrapper_args = array( 'class' => implode( ' ', $soywd_card_classes ) );
if ( '' !== $soywd_bg_image_url ) {
	$soywd_wrapper_args['style'] = sprintf(
		'background-image:url(%s);background-size:cover;background-position:center;background-repeat:no-repeat;',
		esc_url( $soywd_bg_image_url )
	);
}
$soywd_wrapper = get_block_wrapper_attributes( $soywd_wrapper_args );
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $soywd_render_image ) : ?>
		<div class="p-3 pb-0">
			<div class="relative overflow-hidden rounded-[14px]">
				<?php
				echo soywd_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'src'   => $soywd_image_url,
						'alt'   => $soywd_image_alt,
						'class' => 'w-full h-48 object-cover',
					)
				);
				?>
				<?php if ( '' !== $soywd_overlay_color ) : ?>
					<div class="absolute inset-0 has-<?php echo esc_attr( $soywd_overlay_color ); ?>-background-color has-background" style="opacity:<?php echo esc_attr( (string) $soywd_overlay_op ); ?>" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="p-6 flex-1 flex flex-col">
		<?php if ( $soywd_is_bilingual ) : ?>
			<div class="grid md:grid-cols-2">
				<div class="text-left md:pr-8">
					<<?php echo esc_attr( $soywd_htag ); ?> class="mb-2"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
					<p class="<?php echo esc_attr( $soywd_body_color ); ?>"><?php echo $soywd_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<div dir="rtl" lang="fa" class="text-left mt-6 pt-6 border-t border-current md:mt-0 md:pt-0 md:border-t-0 md:border-l md:pl-8">
					<<?php echo esc_attr( $soywd_htag ); ?> class="mb-2"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
					<p class="<?php echo esc_attr( $soywd_body_color ); ?>"><?php echo $soywd_text_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
			</div>
		<?php else : ?>
			<?php
			$soywd_has_icon   = ( '' !== $soywd_icon_image || '' !== $soywd_icon ) && ! $soywd_render_image;
			$soywd_horizontal = 'horizontal-header' === $soywd_layout && $soywd_has_icon;
			?>
			<?php if ( $soywd_horizontal ) : ?>
				<div class="flex gap-3 items-start mb-2">
					<?php if ( '' !== $soywd_icon_image ) : ?>
						<div class="text-accent shrink-0" aria-hidden="true">
							<img src="<?php echo esc_url( $soywd_icon_image ); ?>" alt="" class="w-12 h-12" loading="lazy" decoding="async" />
						</div>
					<?php elseif ( '' !== $soywd_icon ) : ?>
						<div class="text-3xl shrink-0" aria-hidden="true"><?php echo esc_html( $soywd_icon ); ?></div>
					<?php endif; ?>
					<<?php echo esc_attr( $soywd_htag ); ?> class="flex-1 mb-0"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
				</div>
				<?php if ( '' !== $soywd_eyebrow ) : ?>
					<p class="soywd-eyebrow mb-2"><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( '' !== $soywd_icon_image && ! $soywd_render_image ) : ?>
					<div class="text-accent mb-4 <?php echo $soywd_centered ? 'flex justify-center' : ''; ?>" aria-hidden="true">
						<img src="<?php echo esc_url( $soywd_icon_image ); ?>" alt="" class="w-12 h-12" loading="lazy" decoding="async" />
					</div>
				<?php elseif ( '' !== $soywd_icon && ! $soywd_render_image ) : ?>
					<div class="text-3xl mb-3" aria-hidden="true"><?php echo esc_html( $soywd_icon ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $soywd_eyebrow ) : ?>
					<p class="soywd-eyebrow mb-2"><?php echo $soywd_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<<?php echo esc_attr( $soywd_htag ); ?> class="mb-2"<?php echo $soywd_title_color ? ' style="color:' . esc_attr( $soywd_title_color ) . '"' : ''; ?>><?php echo $soywd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $soywd_htag ); ?>>
			<?php endif; ?>
			<p class="<?php echo esc_attr( $soywd_body_color ); ?>"><?php echo $soywd_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $soywd_tags ) ) : ?>
			<ul class="flex flex-wrap gap-2 mt-4 <?php echo ( $soywd_centered || $soywd_is_bilingual ) ? 'justify-center' : 'justify-start'; ?>">
				<?php
				foreach ( $soywd_tags as $soywd_tag ) :
					$soywd_tag_label = is_array( $soywd_tag )
						? soywd_attr_text( $soywd_tag, 'label' )
						: sanitize_text_field( (string) $soywd_tag );
					if ( '' === $soywd_tag_label ) {
						continue;
					}
					?>
					<li class="inline-block border border-current px-4 py-1.5 rounded-full text-xs tracking-normal"><?php echo esc_html( $soywd_tag_label ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( '' !== $soywd_cta_text ) : ?>
			<div class="mt-auto pt-4 <?php echo $soywd_is_bilingual ? 'text-left lg:text-center' : ''; ?>">
				<?php if ( 'button' === $soywd_cta_style ) : ?>
					<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="soywd-btn-primary">
						<?php echo esc_html( $soywd_cta_text ); ?>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $soywd_cta_url ? $soywd_cta_url : '#' ); ?>" class="<?php echo esc_attr( $soywd_cta_arrow_cls ); ?>">
						<?php echo esc_html( $soywd_cta_head ); ?> <span class="whitespace-nowrap"><?php echo esc_html( $soywd_cta_last ); ?> <span class="soywd-cta-arrow-icon" aria-hidden="true">&rarr;</span></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
