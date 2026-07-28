<?php
/**
 * Seed a WordPress install with the template's pages, menus, settings and
 * sample content.
 *
 * Run with WP-CLI from the theme directory (or pass an absolute path):
 *
 *   wp eval-file scripts/seed-wp.php              # dry run — prints the plan, writes nothing
 *   wp eval-file scripts/seed-wp.php -- --apply   # actually write
 *   wp eval-file scripts/seed-wp.php -- --apply --force
 *
 * SAFE BY DEFAULT. Without `--apply` nothing is written. With `--apply` the
 * script is idempotent: pages are matched by slug, menus by name, and anything
 * that already exists is left alone. `--force` additionally overwrites the
 * content of pages the seeder owns — use it to re-apply the template after
 * editing the block markup, and expect it to discard manual edits to those
 * pages.
 *
 * The copy is deliberately generic (lorem ipsum, "Primary CTA", "Service One").
 * This mirrors the unstyled state of the theme: the structure is final, the
 * words are placeholders for the site to replace.
 *
 * @package wptpl
 */

declare( strict_types = 1 );

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "This script must be run through WP-CLI: wp eval-file scripts/seed-wp.php\n";
	exit( 1 );
}

// ---------------------------------------------------------------------------
// Flags
// ---------------------------------------------------------------------------

$wptpl_argv  = isset( $args ) && is_array( $args ) ? $args : array();
$wptpl_apply = in_array( '--apply', $wptpl_argv, true );
$wptpl_force = in_array( '--force', $wptpl_argv, true );

/**
 * Collected plan lines, printed at the end of a dry run.
 *
 * @var array<int, string>
 */
$wptpl_plan = array();

/**
 * Record an action. In apply mode it also runs; in dry-run mode it only logs.
 *
 * @param string        $verb   One of create / update / skip / set.
 * @param string        $what   Human description.
 * @param callable|null $action Work to perform when applying.
 */
function wptpl_seed_do( string $verb, string $what, ?callable $action = null ) {
	global $wptpl_apply, $wptpl_plan;

	$icon = array(
		'create' => '+',
		'update' => '~',
		'skip'   => '=',
		'set'    => '>',
	);
	$mark = isset( $icon[ $verb ] ) ? $icon[ $verb ] : '?';

	$wptpl_plan[] = sprintf( '  %s %s', $mark, $what );

	if ( ! $wptpl_apply || null === $action || 'skip' === $verb ) {
		return null;
	}
	return $action();
}

/**
 * Create or update a page, matched by slug. Returns the page ID (0 in dry run
 * for pages that do not exist yet).
 *
 * @param array<string, mixed> $page Keys: slug, title, content, parent, order, template.
 */
function wptpl_seed_page( array $page ): int {
	global $wptpl_force;

	$slug   = $page['slug'];
	$parent = isset( $page['parent'] ) ? (int) $page['parent'] : 0;

	$existing = get_page_by_path( isset( $page['path'] ) ? $page['path'] : $slug );

	$postarr = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $page['title'],
		'post_name'    => $slug,
		'post_content' => $page['content'],
		'post_parent'  => $parent,
		'menu_order'   => isset( $page['order'] ) ? (int) $page['order'] : 0,
	);

	if ( $existing instanceof WP_Post ) {
		if ( ! $wptpl_force ) {
			wptpl_seed_do( 'skip', sprintf( 'page "%s" (%s) already exists', $page['title'], $slug ) );
			return (int) $existing->ID;
		}
		$postarr['ID'] = $existing->ID;
		wptpl_seed_do(
			'update',
			sprintf( 'page "%s" (%s) — content replaced', $page['title'], $slug ),
			static function () use ( $postarr ) {
				wp_update_post( $postarr );
			}
		);
		return (int) $existing->ID;
	}

	$id = wptpl_seed_do(
		'create',
		sprintf( 'page "%s" (%s)', $page['title'], $slug ),
		static function () use ( $postarr ) {
			return wp_insert_post( $postarr, true );
		}
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( sprintf( 'Could not create "%s": %s', $slug, $id->get_error_message() ) );
		return 0;
	}
	return (int) $id;
}

/**
 * Set an option only when it differs from the target value.
 *
 * @param string $name  Option name.
 * @param mixed  $value Target value.
 */
function wptpl_seed_option( string $name, $value ): void {
	if ( get_option( $name ) === $value ) {
		wptpl_seed_do( 'skip', sprintf( 'option %s already set', $name ) );
		return;
	}
	wptpl_seed_do(
		'set',
		sprintf( 'option %s = %s', $name, is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) ),
		static function () use ( $name, $value ) {
			update_option( $name, $value );
		}
	);
}

/**
 * Set a theme mod only when it differs.
 *
 * @param string $name  Theme mod name.
 * @param string $value Target value.
 */
function wptpl_seed_theme_mod( string $name, string $value ): void {
	if ( get_theme_mod( $name, null ) === $value ) {
		wptpl_seed_do( 'skip', sprintf( 'theme_mod %s already set', $name ) );
		return;
	}
	wptpl_seed_do(
		'set',
		sprintf( 'theme_mod %s = "%s"', $name, $value ),
		static function () use ( $name, $value ) {
			set_theme_mod( $name, $value );
		}
	);
}

/**
 * Create a nav menu (if missing), fill it, and assign it to a location.
 *
 * @param string                     $name     Menu name.
 * @param string                     $location Theme location slug.
 * @param array<int, array<string,mixed>> $items Item definitions.
 */
function wptpl_seed_menu( string $name, string $location, array $items ): void {
	$menu = wp_get_nav_menu_object( $name );

	if ( $menu ) {
		wptpl_seed_do( 'skip', sprintf( 'menu "%s" already exists — items left untouched', $name ) );
	} else {
		wptpl_seed_do(
			'create',
			sprintf( 'menu "%s" with %d item(s)', $name, count( $items ) ),
			static function () use ( $name, $items ) {
				$menu_id = wp_create_nav_menu( $name );
				if ( is_wp_error( $menu_id ) ) {
					WP_CLI::warning( sprintf( 'Could not create menu "%s": %s', $name, $menu_id->get_error_message() ) );
					return;
				}
				$by_key = array();
				foreach ( $items as $item ) {
					$parent_id = 0;
					if ( ! empty( $item['parent'] ) && isset( $by_key[ $item['parent'] ] ) ) {
						$parent_id = $by_key[ $item['parent'] ];
					}
					$item_id = wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $item['title'],
							'menu-item-object'    => 'page',
							'menu-item-object-id' => (int) $item['page_id'],
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
							'menu-item-parent-id' => $parent_id,
						)
					);
					if ( ! is_wp_error( $item_id ) && isset( $item['key'] ) ) {
						$by_key[ $item['key'] ] = (int) $item_id;
					}
				}
			}
		);
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	if ( ! empty( $locations[ $location ] ) ) {
		wptpl_seed_do( 'skip', sprintf( 'location "%s" already assigned', $location ) );
		return;
	}

	wptpl_seed_do(
		'set',
		sprintf( 'assign menu "%s" to location "%s"', $name, $location ),
		static function () use ( $name, $location ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( ! $menu ) {
				return;
			}
			$locations              = get_theme_mod( 'nav_menu_locations', array() );
			$locations              = is_array( $locations ) ? $locations : array();
			$locations[ $location ] = (int) $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	);
}

// ---------------------------------------------------------------------------
// Run
// ---------------------------------------------------------------------------

$wptpl_seed_dir = __DIR__ . '/seed';

WP_CLI::log( '' );
WP_CLI::log( $wptpl_apply ? '== Seeding WordPress (APPLY) ==' : '== Seeding WordPress (DRY RUN — nothing will be written) ==' );
if ( $wptpl_apply && $wptpl_force ) {
	WP_CLI::log( '   --force: existing seeded pages will have their content replaced.' );
}
WP_CLI::log( '' );

require $wptpl_seed_dir . '/blocks.php';
require $wptpl_seed_dir . '/pages.php';
require $wptpl_seed_dir . '/posts.php';

WP_CLI::log( 'Pages' );
$wptpl_page_ids = wptpl_seed_all_pages();

WP_CLI::log( '' );
WP_CLI::log( 'Settings' );
wptpl_seed_option( 'blogname', 'Practice Name' );
wptpl_seed_option( 'blogdescription', 'Lorem ipsum dolor sit amet' );
wptpl_seed_option( 'show_on_front', 'page' );
if ( ! empty( $wptpl_page_ids['home'] ) ) {
	wptpl_seed_option( 'page_on_front', (string) $wptpl_page_ids['home'] );
}
/*
 * `page_for_posts` is deliberately NOT set. The blog hub is a normal page whose
 * listing comes from the wptpl/featured-post + wptpl/post-grid blocks — that is
 * what lets it carry a hero and a category filter. Pointing page_for_posts at it
 * would make WordPress ignore its content and render index.php instead.
 */
wptpl_seed_option( 'permalink_structure', '/%postname%/' );
wptpl_seed_option( 'posts_per_page', '9' );

WP_CLI::log( '' );
WP_CLI::log( 'Customizer' );
wptpl_seed_theme_mod( 'wptpl_primary_cta_text', 'Primary CTA' );
wptpl_seed_theme_mod( 'wptpl_primary_cta_url', '/contact/' );
wptpl_seed_theme_mod( 'wptpl_practice_name', 'Practice Name' );
wptpl_seed_theme_mod( 'wptpl_practitioner', 'Practitioner Name' );
wptpl_seed_theme_mod( 'wptpl_license', 'License #000000' );
wptpl_seed_theme_mod( 'wptpl_hours', "Monday – Friday\n9:00 – 17:00" );
wptpl_seed_theme_mod( 'wptpl_modality', 'Lorem, Ipsum, Dolor' );
wptpl_seed_theme_mod( 'wptpl_languages', 'Sessions in English' );
wptpl_seed_theme_mod( 'wptpl_alert_text', '' );

WP_CLI::log( '' );
WP_CLI::log( 'Menus' );
wptpl_seed_all_menus( $wptpl_page_ids );

WP_CLI::log( '' );
WP_CLI::log( 'Blog' );
wptpl_seed_all_posts();

WP_CLI::log( '' );
foreach ( $wptpl_plan as $wptpl_line ) {
	WP_CLI::log( $wptpl_line );
}
WP_CLI::log( '' );

if ( $wptpl_apply ) {
	if ( function_exists( 'flush_rewrite_rules' ) ) {
		flush_rewrite_rules( false );
	}
	WP_CLI::success( sprintf( '%d action(s) applied.', count( $wptpl_plan ) ) );
} else {
	WP_CLI::success(
		sprintf(
			'%d action(s) planned. Nothing was written — re-run with `-- --apply` to commit.',
			count( $wptpl_plan )
		)
	);
}
