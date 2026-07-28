<?php
/**
 * Seed a WordPress install with the template's pages, menus, settings and
 * sample content.
 *
 * Run with WP-CLI from the theme directory (or pass an absolute path):
 *
 *   wp eval-file scripts/seed-wp.php               # dry run — prints the plan, writes nothing
 *   wp eval-file scripts/seed-wp.php apply         # actually write
 *   wp eval-file scripts/seed-wp.php apply force   # also replace seeded page content
 *   wp eval-file scripts/seed-wp.php apply prune   # also trash retired pages
 *
 * The flags are bare words, not `--apply`. WP-CLI parses anything starting with
 * `--` as one of its own options and errors out with "unknown --apply
 * parameter"; the `--` separator does not help for eval-file. Positional
 * arguments are handed to the script as $args, so that is what we use.
 *
 * SAFE BY DEFAULT. Without `apply` nothing is written. With `apply` the
 * script is idempotent: pages are matched by slug, menus by name, and anything
 * that already exists is left alone. `force` additionally overwrites the
 * content of pages the seeder owns — use it to re-apply the template after
 * editing the block markup, and expect it to discard manual edits to those
 * pages. `prune` trashes pages the template used to own and no longer does.
 *
 * Both only ever touch pages carrying the `_wptpl_seeded` mark, so a page a
 * site created by hand is never overwritten or trashed.
 *
 * The copy is deliberately generic (lorem ipsum, "Primary CTA", "Service One").
 * This mirrors the unstyled state of the theme: the structure is final, the
 * words are placeholders for the site to replace.
 *
 * NOTE: this file must NOT declare strict_types. `wp eval-file` runs it through
 * eval(), and a declare() is only legal as the first statement of a real script
 * — PHP fatals otherwise. The files under scripts/seed/ are loaded with
 * require(), so they keep their declaration.
 *
 * @package wptpl
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "This script must be run through WP-CLI: wp eval-file scripts/seed-wp.php\n";
	exit( 1 );
}

// ---------------------------------------------------------------------------
// Flags
// ---------------------------------------------------------------------------

$wptpl_argv = isset( $args ) && is_array( $args ) ? $args : array();

/*
 * These go into $GLOBALS explicitly. `wp eval-file` evaluates this script
 * inside a method, so a plain top-level assignment would be function-scoped and
 * the `global` statements in the helpers below would silently read a different,
 * empty variable — the symptom is a run that reports "0 action(s) planned".
 */
$GLOBALS['wptpl_apply'] = (bool) array_intersect( array( 'apply', '--apply' ), $wptpl_argv );
$GLOBALS['wptpl_force'] = (bool) array_intersect( array( 'force', '--force' ), $wptpl_argv );
$GLOBALS['wptpl_prune'] = (bool) array_intersect( array( 'prune', '--prune' ), $wptpl_argv );
$GLOBALS['wptpl_plan']  = array();

/*
 * Stand-in post IDs handed out during a dry run. Without them every page would
 * "return" 0, and everything keyed off a page ID — the front-page option, the
 * nested service items in the Primary menu — would silently drop out of the
 * plan, making the dry run under-report what `apply` will actually do.
 */
$GLOBALS['wptpl_fake_id'] = 900000;

$wptpl_apply = $GLOBALS['wptpl_apply'];
$wptpl_force = $GLOBALS['wptpl_force'];
$wptpl_prune = $GLOBALS['wptpl_prune'];

/**
 * Meta key marking a page as one the seeder created.
 *
 * This is what makes `force` and `prune` safe: they only ever touch pages
 * carrying this mark, so anything a site added by hand is invisible to them.
 * Pages seeded before the mark existed are adopted on the next run.
 */
const WPTPL_SEEDED_META = '_wptpl_seeded';

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
			/*
			 * Adopt it. Seeding a slug is the template asserting ownership of it,
			 * so mark the page even when we leave its content alone — otherwise
			 * pages seeded before the mark existed stay invisible to `force` and
			 * `prune` forever.
			 */
			if ( $GLOBALS['wptpl_apply'] ) {
				update_post_meta( $existing->ID, WPTPL_SEEDED_META, '1' );
			}
			return (int) $existing->ID;
		}
		$postarr['ID'] = $existing->ID;
		wptpl_seed_do(
			'update',
			sprintf( 'page "%s" (%s) — content replaced', $page['title'], $slug ),
			static function () use ( $postarr ) {
				wp_update_post( $postarr );
				update_post_meta( $postarr['ID'], WPTPL_SEEDED_META, '1' );
			}
		);
		return (int) $existing->ID;
	}

	$id = wptpl_seed_do(
		'create',
		sprintf( 'page "%s" (%s)', $page['title'], $slug ),
		static function () use ( $postarr ) {
			$new = wp_insert_post( $postarr, true );
			if ( ! is_wp_error( $new ) ) {
				update_post_meta( (int) $new, WPTPL_SEEDED_META, '1' );
			}
			return $new;
		}
	);

	if ( null === $id ) {
		// Dry run — hand back a stand-in so the rest of the plan still resolves.
		return ++$GLOBALS['wptpl_fake_id'];
	}
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
					$args = array(
						'menu-item-title'     => $item['title'],
						'menu-item-status'    => 'publish',
						'menu-item-parent-id' => $parent_id,
						'menu-item-classes'   => isset( $item['classes'] ) ? $item['classes'] : '',
					);
					if ( ! empty( $item['url'] ) ) {
						// An off-site link, not a page.
						$args['menu-item-type'] = 'custom';
						$args['menu-item-url']  = $item['url'];
					} else {
						$args['menu-item-type']      = 'post_type';
						$args['menu-item-object']    = 'page';
						$args['menu-item-object-id'] = (int) $item['page_id'];
					}
					$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
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

/**
 * Trash pages the template used to own and no longer does.
 *
 * Trash, never delete — a restructure should be reversible from wp-admin. Only
 * pages carrying WPTPL_SEEDED_META are eligible, so a page a site created by
 * hand at one of these slugs is reported and left alone.
 *
 * @param array<int, string> $slugs Retired slugs.
 */
function wptpl_seed_prune( array $slugs ): void {
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( ! $page instanceof WP_Post || 'trash' === $page->post_status ) {
			continue;
		}
		if ( '1' !== get_post_meta( $page->ID, WPTPL_SEEDED_META, true ) ) {
			wptpl_seed_do(
				'skip',
				sprintf( 'page "%s" (%s) is retired but was not seeded by this template — leaving it alone', $page->post_title, $slug )
			);
			continue;
		}
		wptpl_seed_do(
			'update',
			sprintf( 'trash retired page "%s" (%s)', $page->post_title, $slug ),
			static function () use ( $page ) {
				wp_trash_post( $page->ID );
			}
		);
	}
}

// ---------------------------------------------------------------------------
// Run
// ---------------------------------------------------------------------------

$wptpl_seed_dir = __DIR__ . '/seed';

WP_CLI::log( '' );
WP_CLI::log( $wptpl_apply ? '== Seeding WordPress (APPLY) ==' : '== Seeding WordPress (DRY RUN — nothing will be written) ==' );
if ( $wptpl_apply && $wptpl_force ) {
	WP_CLI::log( '   force: existing seeded pages will have their content replaced.' );
}
if ( $wptpl_prune ) {
	WP_CLI::log( '   prune: pages the template no longer owns will be moved to the trash.' );
}
WP_CLI::log( '' );

require $wptpl_seed_dir . '/blocks.php';
require $wptpl_seed_dir . '/pages.php';
require $wptpl_seed_dir . '/posts.php';

WP_CLI::log( 'Pages' );
$wptpl_page_ids = wptpl_seed_all_pages();
if ( $wptpl_prune ) {
	wptpl_seed_prune( wptpl_seed_retired_slugs() );
}

WP_CLI::log( '' );
WP_CLI::log( 'Settings' );
wptpl_seed_option( 'blogname', 'Partners for Peace' );
wptpl_seed_option( 'blogdescription', 'Faith-based counseling and mental health resources' );
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
wptpl_seed_theme_mod( 'wptpl_primary_cta_text', 'Request a Consultation' );
wptpl_seed_theme_mod( 'wptpl_primary_cta_url', '/contact/' );
wptpl_seed_theme_mod( 'wptpl_practice_name', 'Partners for Peace' );
wptpl_seed_theme_mod( 'wptpl_practitioner', 'Practitioner Name' );
wptpl_seed_theme_mod( 'wptpl_license', '' );
wptpl_seed_theme_mod( 'wptpl_hours', "Monday – Friday\n9:00 – 17:00" );
wptpl_seed_theme_mod( 'wptpl_modality', '' );
wptpl_seed_theme_mod( 'wptpl_languages', '' );
wptpl_seed_theme_mod( 'wptpl_alert_text', '' );

WP_CLI::log( '' );
WP_CLI::log( 'Menus' );
wptpl_seed_all_menus( $wptpl_page_ids );

WP_CLI::log( '' );
WP_CLI::log( 'Blog' );
wptpl_seed_all_posts();

WP_CLI::log( '' );
foreach ( $GLOBALS['wptpl_plan'] as $wptpl_line ) {
	WP_CLI::log( $wptpl_line );
}
WP_CLI::log( '' );

if ( $wptpl_apply ) {
	if ( function_exists( 'flush_rewrite_rules' ) ) {
		flush_rewrite_rules( false );
	}
	WP_CLI::success( sprintf( '%d action(s) applied.', count( $GLOBALS['wptpl_plan'] ) ) );
} else {
	WP_CLI::success(
		sprintf(
			'%d action(s) planned. Nothing was written — re-run with `apply` to commit.',
			count( $GLOBALS['wptpl_plan'] )
		)
	);
}
