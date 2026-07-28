<?php
defined( 'ABSPATH' ) || exit;

$wptpl_cta_text = wptpl_setting( 'primary_cta_text', __( 'Book a free consultation', 'wptpl' ) );
$wptpl_cta_url  = wptpl_setting( 'primary_cta_url', '#book' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen bg-canvas text-contrast antialiased' ); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 wptpl-btn-primary" href="#wptpl-main">
	<?php esc_html_e( 'Skip to content', 'wptpl' ); ?>
</a>

<header class="wptpl-header border-b border-slate-200 bg-white">
	<div class="wptpl-container flex items-center gap-4 py-4">
		<?php // Left: logo. `flex-1` on both side groups keeps the centered nav dead-centre. ?>
		<div class="flex-1 flex items-center">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="inline-flex items-center" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<span class="inline-block h-11 w-11 border border-muted" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>

		<?php // Center: primary nav (desktop only). ?>
		<nav class="wptpl-nav hidden lg:flex justify-center" aria-label="<?php esc_attr_e( 'Primary', 'wptpl' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'flex items-center gap-6',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<?php // Right: CTA (desktop) + mobile menu toggle. ?>
		<div class="flex-1 flex items-center justify-end gap-4">
			<a class="wptpl-btn-accent hidden lg:inline-flex" href="<?php echo esc_url( $wptpl_cta_url ); ?>">
				<?php echo esc_html( $wptpl_cta_text ); ?>
			</a>

			<?php // Mobile menu toggle — shown below lg, where the desktop nav hides. ?>
			<button type="button" id="wptpl-nav-toggle" class="wptpl-nav-toggle lg:hidden" aria-controls="wptpl-mobile-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'wptpl' ); ?>">
				<span class="wptpl-nav-toggle__bar" aria-hidden="true"></span>
				<span class="wptpl-nav-toggle__bar" aria-hidden="true"></span>
				<span class="wptpl-nav-toggle__bar" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	<?php // Mobile menu panel — toggled by the button above (assets/js/nav.js). ?>
	<div id="wptpl-mobile-nav" class="wptpl-mobile-nav lg:hidden" hidden>
		<nav class="wptpl-mobile-nav__menu" aria-label="<?php esc_attr_e( 'Mobile menu', 'wptpl' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'wptpl-mobile-menu',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<div class="wptpl-mobile-nav__actions">
			<a class="wptpl-btn-accent inline-flex" href="<?php echo esc_url( $wptpl_cta_url ); ?>">
				<?php echo esc_html( $wptpl_cta_text ); ?>
			</a>
		</div>
	</div>
</header>

<main id="wptpl-main" class="wptpl-main" tabindex="-1">
