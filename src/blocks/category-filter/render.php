<?php
/**
 * Server-side render for soywd/category-filter.
 *
 * Renders an "All" pill plus one pill per post category (dynamically — new
 * categories appear on their own). The pills are buttons carrying the category
 * slug in `data-filter`; assets/js/blog-filter.js reads that and shows/hides the
 * matching `.soywd-post-grid__item` cards on the same page. "All" is active by
 * default. Outputs nothing when there are no categories with posts.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

$soywd_cats = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

if ( empty( $soywd_cats ) ) {
	return;
}

$soywd_wrapper = get_block_wrapper_attributes( array( 'class' => 'soywd-cat-filter' ) );
?>
<div <?php echo $soywd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="group" aria-label="<?php esc_attr_e( 'Filter posts by category', 'soywd' ); ?>">
	<button type="button" class="soywd-cat-filter__pill is-active" data-filter="all" aria-pressed="true">
		<?php esc_html_e( 'All', 'soywd' ); ?>
	</button>
	<?php foreach ( $soywd_cats as $soywd_cat ) : ?>
		<button type="button" class="soywd-cat-filter__pill" data-filter="<?php echo esc_attr( $soywd_cat->slug ); ?>" aria-pressed="false">
			<?php echo esc_html( $soywd_cat->name ); ?>
		</button>
	<?php endforeach; ?>
</div>
