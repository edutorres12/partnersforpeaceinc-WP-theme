<?php
/**
 * Fallback template.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<div class="soywd-container py-12">
	<h1 class="text-3xl font-bold mb-8">
		<?php
		if ( is_search() ) {
			/* translators: %s: search query. */
			printf( esc_html__( 'Search results for: %s', 'soywd' ), esc_html( get_search_query() ) );
		} elseif ( is_archive() ) {
			the_archive_title();
		} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
			echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) );
		} else {
			esc_html_e( 'Blog', 'soywd' );
		}
		?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'mb-12' ); ?>>
				<h2 class="text-2xl font-bold mb-2">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<div class="prose max-w-none"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No content to show yet.', 'soywd' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
