<?php
/**
 * Server-side render for wptpl/category-filter.
 *
 * Renders an "All" pill plus one pill per post category (dynamically — new
 * categories appear on their own). The pills are buttons carrying the category
 * slug in `data-filter`; assets/js/blog-filter.js reads that and shows/hides the
 * matching `.wptpl-post-grid__item` cards on the same page. "All" is active by
 * default. Outputs nothing when there are no categories with posts.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_cats = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

if ( empty( $wptpl_cats ) ) {
	return;
}

$wptpl_wrapper = get_block_wrapper_attributes( array( 'class' => 'wptpl-cat-filter' ) );
?>
<div <?php echo $wptpl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="group" aria-label="<?php esc_attr_e( 'Filter posts by category', 'wptpl' ); ?>">
	<button type="button" class="wptpl-cat-filter__pill is-active" data-filter="all" aria-pressed="true">
		<?php esc_html_e( 'All', 'wptpl' ); ?>
	</button>
	<?php foreach ( $wptpl_cats as $wptpl_cat ) : ?>
		<button type="button" class="wptpl-cat-filter__pill" data-filter="<?php echo esc_attr( $wptpl_cat->slug ); ?>" aria-pressed="false">
			<?php echo esc_html( $wptpl_cat->name ); ?>
		</button>
	<?php endforeach; ?>
</div>
