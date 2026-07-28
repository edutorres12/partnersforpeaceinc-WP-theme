<?php
/**
 * Template para páginas. El contenido viene 100% del page editor (Gutenberg).
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<article <?php post_class( 'wptpl-page' ); ?>>
	<?php
	while ( have_posts() ) :
		the_post();
		// the_content() renderiza los bloques: nuestros custom + cualquier core block
		// que el usuario use desde el editor.
		the_content();
	endwhile;
	?>
</article>

<?php
get_footer();
