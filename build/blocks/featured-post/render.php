<?php
/**
 * Server-side render for soywd/featured-post.
 *
 * Renders the large clay "Featured post" card from the blog's headline post
 * (sticky first, otherwise the most recent). Meant to sit inside the featured
 * section wrapper (the bark band + container) on the Blog hub page, replacing
 * the previously-static card. Outputs nothing when there are no posts yet.
 *
 * The whole card is clickable: the "Read the article" CTA is a stretched link
 * (its ::after covers the card, see .soywd-featured-post in src/tailwind.css),
 * so there is a single, accessible link per card. Title and image are plain
 * (not separate links) to avoid redundant links to the same URL.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_id = function_exists( 'soywd_blog_featured_id' ) ? soywd_blog_featured_id() : 0;
if ( ! $soywd_id ) {
	return;
}

$soywd_url     = get_permalink( $soywd_id );
$soywd_title   = get_the_title( $soywd_id );
$soywd_excerpt = get_the_excerpt( $soywd_id );
$soywd_thumb   = get_the_post_thumbnail_url( $soywd_id, 'large' );
$soywd_alt     = trim( (string) get_post_meta( get_post_thumbnail_id( $soywd_id ), '_wp_attachment_image_alt', true ) );

// The card is the block's root element. Match the static design: clay bg,
// 22px radius, cream text, generous bottom padding.
$soywd_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'soywd-featured-post has-accent-background-color has-text-color has-background',
		'style' => 'border-radius:22px;color:#e6ded3;padding:1.5rem 1.5rem 7rem 1.5rem',
	)
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $soywd_thumb ) : ?>
		<figure class="wp-block-image size-large is-style-rounded-square" style="margin:0">
			<img src="<?php echo esc_url( $soywd_thumb ); ?>" alt="<?php echo esc_attr( $soywd_alt ); ?>" style="display:block;width:100%;height:auto" loading="lazy" decoding="async" />
		</figure>
	<?php endif; ?>
	<h2 class="wp-block-heading has-text-color" style="color:#e6ded3;margin-top:1.5rem"><?php echo esc_html( $soywd_title ); ?></h2>
	<?php if ( '' !== $soywd_excerpt ) : ?>
		<p class="has-text-color" style="color:#ffffff"><?php echo esc_html( $soywd_excerpt ); ?></p>
	<?php endif; ?>
	<p style="margin-top:1.25rem">
		<a href="<?php echo esc_url( $soywd_url ); ?>" class="soywd-cta-arrow soywd-cta-arrow-light text-xs uppercase tracking-widest font-semibold">
			<?php esc_html_e( 'Read the article', 'soywd' ); ?> <span class="soywd-cta-arrow-icon" aria-hidden="true">&rarr;</span>
		</a>
	</p>
</div>
