<?php
/**
 * Server-side render for soywd/post-grid.
 *
 * Queries published posts and renders each as a soywd/feature-card (reused via
 * render_block so card styling stays in one place), in a responsive 3-up grid.
 *
 * The headline post (see soywd_blog_featured_id) IS included, but its grid item
 * ships `hidden` — on the default "All" view it shows once, up top, as the
 * featured card. When a category is selected, blog-filter.js hides the featured
 * hero and reveals this item if it matches, so a filtered category shows every
 * one of its posts (including the headline) with no duplicates and none missing.
 *
 * Each card is wrapped in `.soywd-post-grid__item` with its category slugs in
 * `data-categories` (+ `data-featured` on the headline). `count` limits the
 * number of posts; 0 (default) shows all. Outputs nothing when empty.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_count    = soywd_attr_int( $attributes, 'count', 0, 100, 0 );
$soywd_featured = function_exists( 'soywd_blog_featured_id' ) ? soywd_blog_featured_id() : 0;

$soywd_query = new WP_Query(
	array(
		'posts_per_page'      => $soywd_count > 0 ? $soywd_count : -1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	)
);

if ( ! $soywd_query->have_posts() ) {
	return;
}

$soywd_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'soywd-post-grid grid gap-8 grid-cols-1 md:grid-cols-3' )
);
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	while ( $soywd_query->have_posts() ) :
		$soywd_query->the_post();
		$soywd_thumb      = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		$soywd_slugs      = implode( ' ', wp_list_pluck( get_the_category(), 'slug' ) );
		$soywd_is_feature = ( get_the_ID() === $soywd_featured );
		// The card title (an <h3>) already names the post, so the thumbnail is
		// decorative relative to it. Use the image's own alt when set, otherwise
		// empty — never the post title, which would be read twice.
		$soywd_thumb_alt  = trim( (string) get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) );

		$soywd_attrs = array(
			'title'     => get_the_title(),
			'text'      => get_the_excerpt(),
			'imageUrl'  => $soywd_thumb ? $soywd_thumb : '',
			'imageAlt'  => $soywd_thumb_alt,
			'ctaText'   => __( 'Read the article', 'soywd' ),
			'ctaUrl'    => get_permalink(),
			'ctaStyle'  => 'arrow',
			'bordered'  => false,
			'textColor' => 'muted',
			'className' => 'soywd-post-card',
		);
		?>
		<div class="soywd-post-grid__item" data-categories="<?php echo esc_attr( $soywd_slugs ); ?>"<?php echo $soywd_is_feature ? ' data-featured="1" hidden' : ''; ?>>
			<?php
			echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'blockName'    => 'soywd/feature-card',
					'attrs'        => $soywd_attrs,
					'innerBlocks'  => array(),
					'innerHTML'    => '',
					'innerContent' => array(),
				)
			);
			?>
		</div>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</div>
