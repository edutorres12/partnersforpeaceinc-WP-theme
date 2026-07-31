<?php
/**
 * Server-side render for wptpl/featured-post.
 *
 * Renders the large accent "Featured post" card from the blog's headline post
 * (sticky first, otherwise the most recent). Meant to sit inside the featured
 * section wrapper (the muted band + container) on the Blog hub page, replacing
 * the previously-static card. Outputs nothing when there are no posts yet.
 *
 * The whole card is clickable: the "Read the article" CTA is a stretched link
 * (its ::after covers the card, see .wptpl-featured-post in src/tailwind.css),
 * so there is a single, accessible link per card. Title and image are plain
 * (not separate links) to avoid redundant links to the same URL.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_id = function_exists( 'wptpl_blog_featured_id' ) ? wptpl_blog_featured_id() : 0;
if ( ! $wptpl_id ) {
	return;
}

$wptpl_url     = get_permalink( $wptpl_id );
$wptpl_title   = get_the_title( $wptpl_id );
$wptpl_excerpt = get_the_excerpt( $wptpl_id );
$wptpl_thumb   = get_the_post_thumbnail_url( $wptpl_id, 'large' );
$wptpl_alt     = trim( (string) get_post_meta( get_post_thumbnail_id( $wptpl_id ), '_wp_attachment_image_alt', true ) );

// The image is structural here, not decorative: the card's proportions are
// built around it, and its deep bottom padding is measured against it. A post
// with no featured image left a tall empty rectangle with a title floating at
// the top. Fall back to the same wireframe box every other block uses, so the
// slot reads correctly until a real post takes it.
if ( ! $wptpl_thumb ) {
	$wptpl_thumb = WPTPL_THEME_URI . '/assets/placeholders/post.jpg';
	$wptpl_alt   = '';
}

// The card is the block's root element. Match the static design: accent bg,
// 22px radius, canvas text, generous bottom padding.
$wptpl_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'wptpl-featured-post has-accent-background-color has-text-color has-background',
		'style' => 'border-radius:22px;color:#e6e6e6;padding:1.5rem 1.5rem 7rem 1.5rem',
	)
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $wptpl_thumb ) : ?>
		<figure class="wp-block-image size-large is-style-rounded-square" style="margin:0">
			<img src="<?php echo esc_url( $wptpl_thumb ); ?>" alt="<?php echo esc_attr( $wptpl_alt ); ?>" style="display:block;width:100%;height:auto" loading="lazy" decoding="async" />
		</figure>
	<?php endif; ?>
	<h2 class="wp-block-heading has-text-color" style="color:#e6e6e6;margin-top:1.5rem"><?php echo esc_html( $wptpl_title ); ?></h2>
	<?php if ( '' !== $wptpl_excerpt ) : ?>
		<p class="has-text-color" style="color:#ffffff"><?php echo esc_html( $wptpl_excerpt ); ?></p>
	<?php endif; ?>
	<p style="margin-top:1.25rem">
		<a href="<?php echo esc_url( $wptpl_url ); ?>" class="wptpl-cta-arrow wptpl-cta-arrow-light text-xs uppercase tracking-widest font-semibold">
			<?php esc_html_e( 'Read the article', 'wptpl' ); ?> <span class="wptpl-cta-arrow-icon" aria-hidden="true">&rarr;</span>
		</a>
	</p>
</div>
