<?php
/**
 * Therapy Theme Template bootstrap.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

define( 'WPTPL_THEME_DIR', get_template_directory() );
define( 'WPTPL_THEME_URI', get_template_directory_uri() );
define( 'WPTPL_THEME_VERSION', wp_get_theme()->get( 'Version' ) );

require_once WPTPL_THEME_DIR . '/inc/setup.php';
require_once WPTPL_THEME_DIR . '/inc/cleanup.php';
require_once WPTPL_THEME_DIR . '/inc/enqueue.php';
require_once WPTPL_THEME_DIR . '/inc/seo.php';
require_once WPTPL_THEME_DIR . '/inc/block-helpers.php';
require_once WPTPL_THEME_DIR . '/inc/blocks.php';
require_once WPTPL_THEME_DIR . '/inc/customizer.php';
require_once WPTPL_THEME_DIR . '/inc/topbar.php';
