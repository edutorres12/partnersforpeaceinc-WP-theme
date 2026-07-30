<?php
/**
 * Blog categories and sample posts for the seeder.
 *
 * The blog hub renders through `wptpl/featured-post` + `wptpl/post-grid`, so it
 * needs real posts to show anything. One post is sticky — that is what the
 * featured card picks up.
 *
 * @package wptpl
 */

declare( strict_types = 1 );

/**
 * Categories shown in the blog hub's filter pills.
 *
 * @return array<int, string>
 */
function wptpl_seed_categories(): array {
	return array(
		'Category One',
		'Category Two',
		'Category Three',
		'Category Four',
		'Category Five',
	);
}

/**
 * Create a category if it does not exist. Returns its term ID.
 */
function wptpl_seed_category( string $name ): int {
	$existing = get_term_by( 'name', $name, 'category' );
	if ( $existing instanceof WP_Term ) {
		wptpl_seed_do( 'skip', sprintf( 'category "%s" already exists', $name ) );
		return (int) $existing->term_id;
	}

	$id = wptpl_seed_do(
		'create',
		sprintf( 'category "%s"', $name ),
		static function () use ( $name ) {
			$term = wp_insert_term( $name, 'category' );
			return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
		}
	);

	return (int) $id;
}

/**
 * Body content for a sample post: a few headings and paragraphs so the
 * long-form typography rules in `.wptpl-post-content` have something to style.
 */
function wptpl_seed_post_body(): string {
	return implode(
		"\n\n",
		array(
			wptpl_paragraph( wptpl_lorem_len( 233 ) ),
			wptpl_heading( 'A section heading', 2 ),
			wptpl_paragraph( wptpl_lorem_len( 233 ) ),
			"<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>List item one</li><li>List item two</li><li>List item three</li></ul>\n<!-- /wp:list -->",
			wptpl_heading( 'Another section heading', 2 ),
			wptpl_paragraph( wptpl_lorem_len( 233 ) ),
			"<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">" . wptpl_paragraph( wptpl_lorem_len( 56 ) ) . "</blockquote>\n<!-- /wp:quote -->",
			wptpl_paragraph( wptpl_lorem_len( 125 ) ),
		)
	);
}

/**
 * Create a post if one with the same slug does not exist.
 *
 * @param string $slug     Post slug.
 * @param string $title    Post title.
 * @param int    $category Category term ID.
 * @param bool   $sticky   Whether to mark it sticky (drives the featured card).
 * @param int    $days_ago How far back to date it, so the grid has an order.
 */
function wptpl_seed_post( string $slug, string $title, int $category, bool $sticky, int $days_ago ): void {
	$existing = get_page_by_path( $slug, OBJECT, 'post' );
	if ( $existing instanceof WP_Post ) {
		wptpl_seed_do( 'skip', sprintf( 'post "%s" already exists', $slug ) );
		return;
	}

	wptpl_seed_do(
		'create',
		sprintf( 'post "%s"%s', $title, $sticky ? ' (sticky — becomes the featured card)' : '' ),
		static function () use ( $slug, $title, $category, $sticky, $days_ago ) {
			$post_id = wp_insert_post(
				array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_title'    => $title,
					'post_name'     => $slug,
					'post_excerpt'  => wptpl_lorem_len( 125 ),
					'post_content'  => wptpl_seed_post_body(),
					'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( sprintf( '-%d days', $days_ago ) ) ),
					'post_category' => $category ? array( $category ) : array(),
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				WP_CLI::warning( sprintf( 'Could not create post "%s": %s', $slug, $post_id->get_error_message() ) );
				return;
			}

			if ( $sticky ) {
				stick_post( (int) $post_id );
			}
		}
	);
}

/**
 * Seed the categories and the sample posts.
 */
function wptpl_seed_all_posts(): void {
	$term_ids = array();
	foreach ( wptpl_seed_categories() as $name ) {
		$term_ids[] = wptpl_seed_category( $name );
	}

	$posts = array(
		array( 'featured-post', 'Featured post title', true ),
		array( 'sample-post-one', 'Sample post one', false ),
		array( 'sample-post-two', 'Sample post two', false ),
		array( 'sample-post-three', 'Sample post three', false ),
		array( 'sample-post-four', 'Sample post four', false ),
		array( 'sample-post-five', 'Sample post five', false ),
	);

	foreach ( $posts as $index => $post ) {
		list( $slug, $title, $sticky ) = $post;
		$category                      = isset( $term_ids[ $index % max( 1, count( $term_ids ) ) ] )
			? $term_ids[ $index % max( 1, count( $term_ids ) ) ]
			: 0;
		wptpl_seed_post( $slug, $title, (int) $category, (bool) $sticky, $index * 7 );
	}

	// The default "Hello world!" post and "Sample Page" only get in the way.
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello instanceof WP_Post ) {
		wptpl_seed_do(
			'update',
			'trash the default "Hello world!" post',
			static function () use ( $hello ) {
				wp_trash_post( $hello->ID );
			}
		);
	}

	$sample = get_page_by_path( 'sample-page' );
	if ( $sample instanceof WP_Post ) {
		wptpl_seed_do(
			'update',
			'trash the default "Sample Page"',
			static function () use ( $sample ) {
				wp_trash_post( $sample->ID );
			}
		);
	}

	// WordPress auto-creates its own Privacy Policy draft; the seeder ships a
	// real one at /privacy/, so the draft is just clutter in the pages list.
	$wp_privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $wp_privacy ) {
		$draft = get_post( $wp_privacy );
		if ( $draft instanceof WP_Post && 'draft' === $draft->post_status ) {
			wptpl_seed_do(
				'update',
				'trash the auto-created "Privacy Policy" draft',
				static function () use ( $draft ) {
					update_option( 'wp_page_for_privacy_policy', 0 );
					wp_trash_post( $draft->ID );
				}
			);
		}
	}
}
