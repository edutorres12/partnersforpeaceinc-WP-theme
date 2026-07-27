<?php
defined( 'ABSPATH' ) || exit;

// Defaults mirror inc/customizer.php — neutral placeholders. Fields left empty
// there are skipped by the conditionals below instead of rendering a blank row.
$wptpl_practice     = wptpl_setting( 'practice_name', __( 'Practice Name', 'wptpl' ) );
$wptpl_practitioner = wptpl_setting( 'practitioner', __( 'Practitioner Name', 'wptpl' ) );
$wptpl_license      = wptpl_setting( 'license' );
$wptpl_hours        = wptpl_setting( 'hours', __( 'Monday – Friday', 'wptpl' ) );
$wptpl_modality     = wptpl_setting( 'modality' );
$wptpl_languages    = wptpl_setting( 'languages' );
?>
</main>

<?php
// The 404 renders as a full-viewport centered cover — the footer would force a
// scroll and clutter a transient error state, so it's dropped there. wp_footer()
// and the document close below still run so scripts and markup stay intact.
if ( ! is_404() ) :
	?>
<footer class="wptpl-footer bg-secondary text-on-dark">
	<?php // Alert bar — moved here from above the header so it sits at the top of the footer. ?>
	<?php wptpl_render_topbar(); ?>

	<div class="wptpl-container py-14">
		<div class="grid gap-10 md:grid-cols-4 mb-10">
			<div class="md:col-span-1">
				<div class="wptpl-eyebrow text-on-dark mb-2"><?php echo esc_html( strtoupper( $wptpl_practice ) ); ?></div>
				<div class="text-sm text-on-dark mb-6">
					<?php echo esc_html( $wptpl_practitioner ); ?>
					<?php if ( '' !== $wptpl_license ) : ?>
						&nbsp;·&nbsp; <?php echo esc_html( $wptpl_license ); ?>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $wptpl_hours ) : ?>
					<div class="mb-4">
						<div class="wptpl-eyebrow text-on-dark mb-1"><?php esc_html_e( 'Hours', 'wptpl' ); ?></div>
						<div class="text-sm text-on-dark"><?php echo nl2br( esc_html( $wptpl_hours ) ); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $wptpl_modality ) : ?>
					<div class="mb-4">
						<div class="wptpl-eyebrow text-on-dark mb-1"><?php esc_html_e( 'Modality', 'wptpl' ); ?></div>
						<div class="text-sm text-on-dark"><?php echo esc_html( $wptpl_modality ); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $wptpl_languages ) : ?>
					<div class="wptpl-eyebrow text-on-dark mb-1"><?php echo esc_html( strtoupper( $wptpl_languages ) ); ?></div>
				<?php endif; ?>
			</div>

			<div>
				<div class="wptpl-eyebrow text-on-dark mb-3"><?php esc_html_e( 'Links', 'wptpl' ); ?></div>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer',
						'container'            => 'nav',
						'container_aria_label' => __( 'Footer', 'wptpl' ),
						'menu_class'           => 'leading-loose text-sm space-y-1',
						'fallback_cb'          => false,
						'depth'                => 1,
					)
				);
				?>
			</div>

			<div>
				<div class="wptpl-eyebrow text-on-dark mb-3"><?php esc_html_e( 'Legal', 'wptpl' ); ?></div>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer_legal',
						'container'            => 'nav',
						'container_aria_label' => __( 'Legal', 'wptpl' ),
						'menu_class'           => 'leading-loose text-sm space-y-1',
						'fallback_cb'          => false,
						'depth'                => 1,
					)
				);
				?>
			</div>

			<div></div>
		</div>

		<div class="border-t border-white/15 pt-5 flex flex-col md:flex-row md:items-center md:justify-between gap-2 text-xs uppercase tracking-widest">
			<div class="text-on-dark opacity-60">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $wptpl_practice ); ?>
			</div>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
