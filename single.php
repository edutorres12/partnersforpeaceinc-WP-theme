<?php
/**
 * Template para entradas individuales.
 *
 * Layout: big centered title + date, then the featured image (falls back to a
 * theme placeholder when the post has none), then the post content — all in a
 * 760px reading column for comfortable long-form measure.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$wptpl_has_thumb = has_post_thumbnail();
	$wptpl_thumb     = $wptpl_has_thumb
		? get_the_post_thumbnail_url( get_the_ID(), 'large' )
		: WPTPL_THEME_URI . '/assets/placeholders/hero.jpg';
	// Use the attachment's own alt text. Fall back to empty (decorative) rather
	// than the post title: the title is already the <h1> right above the image,
	// and the placeholder image conveys nothing.
	$wptpl_thumb_alt = $wptpl_has_thumb
		? trim( (string) get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) )
		: '';
	?>
	<article <?php post_class( 'wptpl-single py-16' ); ?>>
		<div class="wptpl-container-narrow">
			<header class="wptpl-single__header">
				<h1 class="wptpl-single__title"><?php the_title(); ?></h1>
				<p class="wptpl-single__meta"><?php echo esc_html( get_the_date() ); ?></p>
			</header>

			<figure class="wptpl-single__figure">
				<img src="<?php echo esc_url( $wptpl_thumb ); ?>" alt="<?php echo esc_attr( $wptpl_thumb_alt ); ?>" loading="lazy" decoding="async" />
			</figure>

			<div class="wptpl-post-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
