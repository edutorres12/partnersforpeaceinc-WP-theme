<?php
/**
 * Server-side render for wptpl/post-grid.
 *
 * Queries published posts and renders each as a wptpl/feature-card (reused via
 * render_block so card styling stays in one place), in a responsive 3-up grid.
 *
 * The headline post (see wptpl_blog_featured_id) IS included, but its grid item
 * ships `hidden` — on the default "All" view it shows once, up top, as the
 * featured card. When a category is selected, blog-filter.js hides the featured
 * hero and reveals this item if it matches, so a filtered category shows every
 * one of its posts (including the headline) with no duplicates and none missing.
 *
 * Each card is wrapped in `.wptpl-post-grid__item` with its category slugs in
 * `data-categories` (+ `data-featured` on the headline). `count` limits the
 * number of posts; 0 (default) shows all. Outputs nothing when empty.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_count    = wptpl_attr_int( $attributes, 'count', 0, 100, 0 );
$wptpl_featured = function_exists( 'wptpl_blog_featured_id' ) ? wptpl_blog_featured_id() : 0;

$wptpl_query = new WP_Query(
	array(
		'posts_per_page'      => $wptpl_count > 0 ? $wptpl_count : -1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	)
);

if ( ! $wptpl_query->have_posts() ) {
	return;
}

$wptpl_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'wptpl-post-grid grid gap-8 grid-cols-1 md:grid-cols-3' )
);
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	while ( $wptpl_query->have_posts() ) :
		$wptpl_query->the_post();
		$wptpl_thumb      = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		$wptpl_slugs      = implode( ' ', wp_list_pluck( get_the_category(), 'slug' ) );
		$wptpl_is_feature = ( get_the_ID() === $wptpl_featured );
		// The card title (an <h3>) already names the post, so the thumbnail is
		// decorative relative to it. Use the image's own alt when set, otherwise
		// empty — never the post title, which would be read twice.
		$wptpl_thumb_alt  = trim( (string) get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) );

		$wptpl_attrs = array(
			'title'     => get_the_title(),
			'text'      => get_the_excerpt(),
			'imageUrl'  => $wptpl_thumb ? $wptpl_thumb : '',
			'imageAlt'  => $wptpl_thumb_alt,
			'ctaText'   => __( 'Read the article', 'wptpl' ),
			'ctaUrl'    => get_permalink(),
			'ctaStyle'  => 'arrow',
			'bordered'  => false,
			'textColor' => 'muted',
			'className' => 'wptpl-post-card',
		);
		?>
		<div class="wptpl-post-grid__item" data-categories="<?php echo esc_attr( $wptpl_slugs ); ?>"<?php echo $wptpl_is_feature ? ' data-featured="1" hidden' : ''; ?>>
			<?php
			echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'blockName'    => 'wptpl/feature-card',
					'attrs'        => $wptpl_attrs,
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
