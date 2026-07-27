<?php
/**
 * Server-side render for wptpl/feature-card.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_icon          = wptpl_attr_text( $attributes, 'icon' );
$wptpl_icon_image    = wptpl_attr_url( $attributes, 'iconImageUrl' );
$wptpl_eyebrow       = wptpl_attr_html( $attributes, 'eyebrow' );
$wptpl_title         = wptpl_attr_html( $attributes, 'title' );
$wptpl_text          = wptpl_attr_html( $attributes, 'text' );
$wptpl_title_right   = wptpl_attr_html( $attributes, 'titleRight' );
$wptpl_text_right    = wptpl_attr_html( $attributes, 'textRight' );
$wptpl_image_url     = wptpl_attr_url( $attributes, 'imageUrl' );
$wptpl_image_alt     = wptpl_attr_text( $attributes, 'imageAlt' );
$wptpl_show_image    = wptpl_attr_bool( $attributes, 'showImage', true );
$wptpl_tags          = wptpl_attr_array( $attributes, 'tags' );
$wptpl_cta_text      = wptpl_attr_text( $attributes, 'ctaText' );
$wptpl_cta_url       = wptpl_attr_url( $attributes, 'ctaUrl' );
$wptpl_cta_style     = wptpl_attr_enum( $attributes, 'ctaStyle', array( 'button', 'arrow' ), 'button' );
// Split off the CTA's last word so the arrow CTA can keep it glued to the arrow
// (in a nowrap span) — otherwise a long label wraps and strands the arrow alone
// on its own line.
$wptpl_cta_trim      = trim( $wptpl_cta_text );
$wptpl_cta_gap       = strrpos( $wptpl_cta_trim, ' ' );
$wptpl_cta_head      = false === $wptpl_cta_gap ? '' : substr( $wptpl_cta_trim, 0, $wptpl_cta_gap );
$wptpl_cta_last      = false === $wptpl_cta_gap ? $wptpl_cta_trim : substr( $wptpl_cta_trim, $wptpl_cta_gap + 1 );
$wptpl_centered      = wptpl_attr_bool( $attributes, 'centered' );
$wptpl_layout        = wptpl_attr_enum( $attributes, 'layout', array( 'vertical', 'horizontal-header', 'bilingual' ), 'vertical' );
$wptpl_is_bilingual  = 'bilingual' === $wptpl_layout;
$wptpl_bordered      = wptpl_attr_bool( $attributes, 'bordered', true );
$wptpl_transparent   = wptpl_attr_bool( $attributes, 'transparent' );
$wptpl_half_width    = wptpl_attr_bool( $attributes, 'halfWidthCentered' );
$wptpl_overlay_color = isset( $attributes['imageOverlayColor'] ) ? sanitize_key( (string) $attributes['imageOverlayColor'] ) : '';
$wptpl_overlay_op    = wptpl_attr_float( $attributes, 'imageOverlayOpacity', 0.0, 0.7, 0.25 );
$wptpl_bg_image_url  = wptpl_attr_url( $attributes, 'backgroundImageUrl' );
$wptpl_title_color   = wptpl_attr_color( $attributes, 'titleColor' );
$wptpl_htag          = 'h' . wptpl_attr_int( $attributes, 'headingLevel', 2, 4, 3 );

// Detect a user-set background (Gutenberg color picker or inline style).
$wptpl_has_user_bg = ! empty( $attributes['backgroundColor'] )
	|| ! empty( $attributes['gradient'] )
	|| ! empty( $attributes['style']['color']['background'] )
	|| ! empty( $attributes['style']['color']['gradient'] );

// Detect a user-set text color. When set, the default `text-muted` on the
// body would override it (Tailwind utility wins over inherited has-*-color),
// so we drop it and let the body inherit. Transparent cards also drop it so
// the body inherits the parent section's text color (otherwise a transparent
// card sitting on a `muted` section would render invisible muted-on-muted body
// copy). The arrow CTA gets the light (canvas) treatment only on a genuinely
// dark custom bg; on a light custom bg it inherits the card's (dark) text
// color so it stays legible. A named-color bg we can't measure here falls back
// to the legacy "light" treatment.
$wptpl_has_user_text = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );
$wptpl_body_color    = ( $wptpl_has_user_text || $wptpl_transparent ) ? '' : 'text-muted';

$wptpl_bg_is_dark = $wptpl_has_user_bg;
if ( $wptpl_has_user_bg && ! empty( $attributes['style']['color']['background'] ) ) {
	$wptpl_bg_hex = ltrim( (string) $attributes['style']['color']['background'], '#' );
	if ( 1 === preg_match( '/^[0-9a-fA-F]{6}$/', $wptpl_bg_hex ) ) {
		$wptpl_bg_lum = ( 0.299 * hexdec( substr( $wptpl_bg_hex, 0, 2 ) ) )
			+ ( 0.587 * hexdec( substr( $wptpl_bg_hex, 2, 2 ) ) )
			+ ( 0.114 * hexdec( substr( $wptpl_bg_hex, 4, 2 ) ) );
		$wptpl_bg_is_dark = $wptpl_bg_lum < 150;
	}
}

$wptpl_cta_arrow_cls = 'wptpl-cta-arrow text-xs uppercase tracking-widest font-semibold';
if ( $wptpl_bg_is_dark ) {
	$wptpl_cta_arrow_cls .= ' wptpl-cta-arrow-light';
}

// Auto-fill placeholder for service/guide-style cards (has CTA, no image, no icon).
// Skipped entirely when the card opts out of imagery via showImage = false.
$wptpl_use_placeholder = $wptpl_show_image
	&& '' === $wptpl_image_url
	&& '' === $wptpl_icon_image
	&& '' === $wptpl_icon
	&& '' !== $wptpl_cta_text
	&& ! $wptpl_transparent
	&& ! $wptpl_has_user_bg;

if ( $wptpl_use_placeholder ) {
	$wptpl_image_url = WPTPL_THEME_URI . '/assets/placeholders/service-card.jpg';
	$wptpl_image_alt = '';
}

// Whether a main image is actually rendered (an explicit imageUrl is hidden too
// when showImage is off).
$wptpl_render_image = $wptpl_show_image && '' !== $wptpl_image_url;

// Default card: surface (surface) bg + soft shadow gives the card a visible
// boundary when it sits on a same-toned section (e.g. base body). When
// the user sets a custom Gutenberg background or marks the card
// transparent, both are skipped so the wrapper bg shows through.
$wptpl_card_classes = array( 'wptpl-feature-card', 'rounded-[14px]', 'overflow-hidden', 'h-full', 'flex', 'flex-col' );
if ( $wptpl_bordered ) {
	$wptpl_card_classes[] = 'border border-muted/25';
}
if ( ! $wptpl_transparent && ! $wptpl_has_user_bg && '' === $wptpl_bg_image_url ) {
	$wptpl_card_classes[] = 'bg-surface';
	// Rest + hover shadow handled in src/tailwind.css (.wptpl-feature-card).
}
if ( $wptpl_centered ) {
	$wptpl_card_classes[] = 'text-center';
}
// Match the rendered width of a card in a 2-column row and center it, so a
// lone "odd" card can stand on its own line instead of stretching full width.
// A bilingual card needs more room for its two language columns, so it centers
// at a wider reading width instead of the 50% half-card width.
if ( $wptpl_half_width ) {
	$wptpl_card_classes[] = $wptpl_is_bilingual ? 'wptpl-card-bilingual-centered' : 'wptpl-card-half-centered';
}

$wptpl_wrapper_args = array( 'class' => implode( ' ', $wptpl_card_classes ) );
if ( '' !== $wptpl_bg_image_url ) {
	$wptpl_wrapper_args['style'] = sprintf(
		'background-image:url(%s);background-size:cover;background-position:center;background-repeat:no-repeat;',
		esc_url( $wptpl_bg_image_url )
	);
}
$wptpl_wrapper = get_block_wrapper_attributes( $wptpl_wrapper_args );
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $wptpl_render_image ) : ?>
		<div class="p-3 pb-0">
			<div class="relative overflow-hidden rounded-[14px]">
				<?php
				echo wptpl_render_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'src'   => $wptpl_image_url,
						'alt'   => $wptpl_image_alt,
						'class' => 'w-full h-48 object-cover',
					)
				);
				?>
				<?php if ( '' !== $wptpl_overlay_color ) : ?>
					<div class="absolute inset-0 has-<?php echo esc_attr( $wptpl_overlay_color ); ?>-background-color has-background" style="opacity:<?php echo esc_attr( (string) $wptpl_overlay_op ); ?>" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="p-6 flex-1 flex flex-col">
		<?php if ( $wptpl_is_bilingual ) : ?>
			<div class="grid md:grid-cols-2">
				<div class="text-left md:pr-8">
					<<?php echo esc_attr( $wptpl_htag ); ?> class="mb-2"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
					<p class="<?php echo esc_attr( $wptpl_body_color ); ?>"><?php echo $wptpl_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<div dir="rtl" lang="fa" class="text-left mt-6 pt-6 border-t border-current md:mt-0 md:pt-0 md:border-t-0 md:border-l md:pl-8">
					<<?php echo esc_attr( $wptpl_htag ); ?> class="mb-2"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
					<p class="<?php echo esc_attr( $wptpl_body_color ); ?>"><?php echo $wptpl_text_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
			</div>
		<?php else : ?>
			<?php
			$wptpl_has_icon   = ( '' !== $wptpl_icon_image || '' !== $wptpl_icon ) && ! $wptpl_render_image;
			$wptpl_horizontal = 'horizontal-header' === $wptpl_layout && $wptpl_has_icon;
			?>
			<?php if ( $wptpl_horizontal ) : ?>
				<div class="flex gap-3 items-start mb-2">
					<?php if ( '' !== $wptpl_icon_image ) : ?>
						<div class="text-accent shrink-0" aria-hidden="true">
							<img src="<?php echo esc_url( $wptpl_icon_image ); ?>" alt="" class="w-12 h-12" loading="lazy" decoding="async" />
						</div>
					<?php elseif ( '' !== $wptpl_icon ) : ?>
						<div class="text-3xl shrink-0" aria-hidden="true"><?php echo esc_html( $wptpl_icon ); ?></div>
					<?php endif; ?>
					<<?php echo esc_attr( $wptpl_htag ); ?> class="flex-1 mb-0"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
				</div>
				<?php if ( '' !== $wptpl_eyebrow ) : ?>
					<p class="wptpl-eyebrow mb-2"><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( '' !== $wptpl_icon_image && ! $wptpl_render_image ) : ?>
					<div class="text-accent mb-4 <?php echo $wptpl_centered ? 'flex justify-center' : ''; ?>" aria-hidden="true">
						<img src="<?php echo esc_url( $wptpl_icon_image ); ?>" alt="" class="w-12 h-12" loading="lazy" decoding="async" />
					</div>
				<?php elseif ( '' !== $wptpl_icon && ! $wptpl_render_image ) : ?>
					<div class="text-3xl mb-3" aria-hidden="true"><?php echo esc_html( $wptpl_icon ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $wptpl_eyebrow ) : ?>
					<p class="wptpl-eyebrow mb-2"><?php echo $wptpl_eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
				<<?php echo esc_attr( $wptpl_htag ); ?> class="mb-2"<?php echo $wptpl_title_color ? ' style="color:' . esc_attr( $wptpl_title_color ) . '"' : ''; ?>><?php echo $wptpl_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_attr( $wptpl_htag ); ?>>
			<?php endif; ?>
			<p class="<?php echo esc_attr( $wptpl_body_color ); ?>"><?php echo $wptpl_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $wptpl_tags ) ) : ?>
			<ul class="flex flex-wrap gap-2 mt-4 <?php echo ( $wptpl_centered || $wptpl_is_bilingual ) ? 'justify-center' : 'justify-start'; ?>">
				<?php
				foreach ( $wptpl_tags as $wptpl_tag ) :
					$wptpl_tag_label = is_array( $wptpl_tag )
						? wptpl_attr_text( $wptpl_tag, 'label' )
						: sanitize_text_field( (string) $wptpl_tag );
					if ( '' === $wptpl_tag_label ) {
						continue;
					}
					?>
					<li class="inline-block border border-current px-4 py-1.5 rounded-full text-xs tracking-normal"><?php echo esc_html( $wptpl_tag_label ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( '' !== $wptpl_cta_text ) : ?>
			<div class="mt-auto pt-4 <?php echo $wptpl_is_bilingual ? 'text-left lg:text-center' : ''; ?>">
				<?php if ( 'button' === $wptpl_cta_style ) : ?>
					<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="wptpl-btn-primary">
						<?php echo esc_html( $wptpl_cta_text ); ?>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $wptpl_cta_url ? $wptpl_cta_url : '#' ); ?>" class="<?php echo esc_attr( $wptpl_cta_arrow_cls ); ?>">
						<?php echo esc_html( $wptpl_cta_head ); ?> <span class="whitespace-nowrap"><?php echo esc_html( $wptpl_cta_last ); ?> <span class="wptpl-cta-arrow-icon" aria-hidden="true">&rarr;</span></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
