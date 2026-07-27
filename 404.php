<?php
/**
 * 404 (Not Found) template.
 *
 * WordPress serves this whenever a URL matches no post, page, or archive —
 * broken links, deleted content, mistyped addresses. It sends a real 404
 * status (WP handles that).
 *
 * Design: a full-viewport, vertically-centered "cover" — no scroll. The header
 * stays (logo + nav + Book CTA are the escape routes); the footer is dropped on
 * 404 (see footer.php `is_404()` guard) so the error reads as a focused
 * interstitial, not a long content page.
 *
 * Server-level errors (500 / 503 / database down) can NOT be templated here —
 * when those happen WordPress itself is not running to render a theme file.
 * The branded page for those lives in `dropins/php-error.php` (self-contained).
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

$wptpl_cta_text = wptpl_setting( 'primary_cta_text', __( 'Book a free consultation', 'wptpl' ) );
$wptpl_cta_url  = wptpl_setting( 'primary_cta_url', '#book' );

get_header();
?>

<div class="wptpl-error-cover">
	<div class="wptpl-error wptpl-container-narrow">
		<p class="wptpl-eyebrow mb-4"><?php esc_html_e( 'Page not found', 'wptpl' ); ?></p>

		<p class="wptpl-error__code" aria-hidden="true">404</p>

		<h1 class="mb-5 text-primary"><?php esc_html_e( 'This page took a wrong turn', 'wptpl' ); ?></h1>

		<p class="wptpl-error__lead mb-8 text-muted">
			<?php esc_html_e( 'This page may have moved or no longer exists.', 'wptpl' ); ?>
		</p>

		<div class="flex gap-3 flex-wrap justify-center">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wptpl-btn-accent">
				<?php esc_html_e( 'Back to home', 'wptpl' ); ?>
			</a>
			<a href="<?php echo esc_url( $wptpl_cta_url ); ?>" class="wptpl-btn-outline">
				<?php echo esc_html( $wptpl_cta_text ); ?>
			</a>
		</div>
	</div>
</div>

<?php
get_footer();
