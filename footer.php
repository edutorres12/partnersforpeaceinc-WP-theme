<?php
defined( 'ABSPATH' ) || exit;

$soywd_practice           = soywd_setting( 'practice_name', __( 'Seasons of You Therapy', 'soywd' ) );
$soywd_practitioner       = soywd_setting( 'practitioner', __( 'Helia Ziaee, LMFT', 'soywd' ) );
$soywd_license            = soywd_setting( 'license', __( 'LMFT #103036', 'soywd' ) );
$soywd_hours              = soywd_setting( 'hours', __( 'Monday – Friday', 'soywd' ) );
$soywd_modality           = soywd_setting( 'modality', soywd_setting( 'modality_languages', __( 'CBT, ACT, IBCT, MBCT', 'soywd' ) ) );
$soywd_languages          = soywd_setting( 'languages', __( 'Sessions in English and Farsi', 'soywd' ) );
?>
</main>

<?php
// The 404 renders as a full-viewport centered cover — the footer would force a
// scroll and clutter a transient error state, so it's dropped there. wp_footer()
// and the document close below still run so scripts and markup stay intact.
if ( ! is_404() ) :
	?>
<footer class="soywd-footer bg-secondary text-cream-light">
	<?php // Crisis resources bar — moved here from above the header so it sits at the top of the footer. ?>
	<?php soywd_render_topbar(); ?>

	<div class="soywd-container py-14">
		<div class="grid gap-10 md:grid-cols-4 mb-10">
			<div class="md:col-span-1">
				<div class="soywd-eyebrow text-cream-light mb-2"><?php echo esc_html( strtoupper( $soywd_practice ) ); ?></div>
				<div class="text-sm text-cream-light mb-6">
					<?php echo esc_html( $soywd_practitioner ); ?>
					<?php if ( '' !== $soywd_license ) : ?>
						&nbsp;·&nbsp; <?php echo esc_html( $soywd_license ); ?>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $soywd_hours ) : ?>
					<div class="mb-4">
						<div class="soywd-eyebrow text-cream-light mb-1"><?php esc_html_e( 'Hours', 'soywd' ); ?></div>
						<div class="text-sm text-cream-light"><?php echo nl2br( esc_html( $soywd_hours ) ); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $soywd_modality ) : ?>
					<div class="mb-4">
						<div class="soywd-eyebrow text-cream-light mb-1"><?php esc_html_e( 'Modality', 'soywd' ); ?></div>
						<div class="text-sm text-cream-light"><?php echo esc_html( $soywd_modality ); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $soywd_languages ) : ?>
					<div class="soywd-eyebrow text-cream-light mb-1"><?php echo esc_html( strtoupper( $soywd_languages ) ); ?></div>
				<?php endif; ?>
			</div>

			<div>
				<div class="soywd-eyebrow text-cream-light mb-3"><?php esc_html_e( 'Links', 'soywd' ); ?></div>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer',
						'container'            => 'nav',
						'container_aria_label' => __( 'Footer', 'soywd' ),
						'menu_class'           => 'leading-loose text-sm space-y-1',
						'fallback_cb'          => false,
						'depth'                => 1,
					)
				);
				?>
			</div>

			<div>
				<div class="soywd-eyebrow text-cream-light mb-3"><?php esc_html_e( 'Legal', 'soywd' ); ?></div>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer_legal',
						'container'            => 'nav',
						'container_aria_label' => __( 'Legal', 'soywd' ),
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
			<div class="text-cream-light opacity-60">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $soywd_practice ); ?>
			</div>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
