<?php
/**
 * Page definitions for the seeder.
 *
 * Each builder returns Gutenberg block markup for one page. The section order
 * and the block types mirror the site the template was extracted from, so the
 * seeded install has the same shape; every string is placeholder copy.
 *
 * @package wptpl
 */

declare( strict_types = 1 );

/**
 * Service subpages, grouped by theme rather than by therapist.
 *
 * That is how families search — someone looks for help with anxiety, not for a
 * particular clinician — and it keeps the page count stable as the roster
 * changes. Straight from the client's sitemap.
 *
 * @return array<int, array{slug: string, title: string}>
 */
function wptpl_seed_services(): array {
	return array(
		array(
			'slug'  => 'anxiety-stress-overthinking',
			'title' => 'Anxiety, Stress &amp; Overthinking',
		),
		array(
			'slug'  => 'trauma-grief-healing',
			'title' => 'Trauma, Grief &amp; Healing Through Faith',
		),
		array(
			'slug'  => 'depression-burnout-renewed-hope',
			'title' => 'Depression, Burnout &amp; Renewed Hope',
		),
		array(
			'slug'  => 'marriage-family-relationship-counseling',
			'title' => 'Marriage, Family &amp; Relationship Counseling',
		),
		array(
			'slug'  => 'life-transitions-purpose-identity',
			'title' => 'Life Transitions, Purpose &amp; Identity in Christ',
		),
	);
}

/**
 * Slugs the template used to own and no longer does.
 *
 * Entries are paths, not slugs: a child page must be listed as
 * `parent/child`, because that is what `get_page_by_path()` matches.
 *
 * `wptpl_seed_prune()` moves these to the trash — never deletes them — so a
 * restructure does not leave orphans behind. Anything a site added on its own is
 * untouched: only pages carrying the `_wptpl_seeded` meta are eligible.
 *
 * @return array<int, string>
 */
function wptpl_seed_retired_slugs(): array {
	return array(
		// The numbered slots the template shipped before the real service list
		// landed. Superseded by the themed slugs in wptpl_seed_services().
		// These are child pages, so they need their full path — a bare slug
		// finds nothing and the page is silently left behind.
		'services/service-one',
		'services/service-two',
		'services/service-three',
		'services/service-four',
		'services/service-five',
		'services/service-six',
		'services/service-seven',
		// Superseded by `about-us`, the slug the sitemap uses.
		'about',
		// Sections the sitemap does not include.
		'resources',
		'fees',
		'crisis-resources',
		'guide-landing',
		'guide-thank-you',
	);
}

/**
 * Menus the template used to create and no longer does.
 *
 * A menu left behind points at trashed pages. WordPress hides those items, so
 * the menu renders empty — and the footer still prints its heading above the
 * nothing. Pruned alongside the retired pages.
 *
 * @return array<int, string>
 */
function wptpl_seed_retired_menus(): array {
	return array();
}

// ---------------------------------------------------------------------------
// Home
// ---------------------------------------------------------------------------

/**
 * Home: hero, trust bar, three-card intro, six-card service grid, bio,
 * numbered steps, FAQ, closing CTA.
 */
function wptpl_seed_page_home(): string {
	// Rows of three, however many services there are — the list is expected to
	// change, and a hardcoded card count breaks the moment it does.
	$service_cards = array();
	foreach ( wptpl_seed_services() as $service ) {
		$service_cards[] = wptpl_block(
			'feature-card',
			array(
				'title'    => $service['title'],
				'text'     => wptpl_lorem( 'short' ),
				'imageUrl' => get_template_directory_uri() . '/assets/placeholders/service-card.jpg',
				'ctaText'  => 'Learn more',
				'ctaUrl'   => '/services/' . $service['slug'] . '/',
				'ctaStyle' => 'arrow',
			)
		);
	}
	// `wptpl-services-carousel` is what the mobile row-gap rule keys off, so the
	// rows don't sit flush against each other once they stack on a phone. The
	// first row is set off from the section header; the rows after it are set
	// off from the row above by the same gap the columns use between cards.
	$service_rows = array();
	foreach ( array_chunk( $service_cards, 3 ) as $index => $row ) {
		$service_rows[] = wptpl_columns(
			array_map(
				static function ( $card ) {
					return array( $card );
				},
				$row
			),
			array(),
			'wptpl-services-carousel',
			0 === $index ? '3rem' : '1.5rem'
		);
	}
	$service_grid = implode(
		'

',
		$service_rows
	);

	return implode(
		"\n\n",
		array(
			// 1. Hero. Title, subtitle and one CTA — no eyebrow, no microcopy and
			// no secondary CTA. The block still supports all three; the home hero
			// just doesn't use them, so the template ships without placeholder
			// copy ("Niche + location identifier", a consultation microcopy line)
			// that a site would have to notice and delete.
			wptpl_block(
				'hero',
				array(
					'title'    => 'Warm, hopeful headline',
					'subtitle' => wptpl_lorem( 'medium' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'layout'   => 'split',
					'imageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
				)
			),

			// 2. Trust bar.
			wptpl_section(
				array(
					wptpl_block(
						'checklist',
						array(
							'items'     => array(
								array( 'text' => 'License info' ),
								array( 'text' => 'Years experience' ),
								array( 'text' => 'Specialization' ),
								array( 'text' => 'Languages' ),
							),
							'direction' => 'horizontal',
							'theme'     => 'dark',
						)
					),
				),
				'secondary',
				'container',
				'wptpl-section-tight'
			),

			// 3. Empathy — three cards.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'Empathetic headline',
							'intro'    => wptpl_lorem( 'short' ),
						)
					),
					wptpl_columns(
						array(
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'    => 'Pain point 1',
										'text'     => wptpl_lorem( 'short' ),
										'centered' => true,
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'    => 'Pain point 2',
										'text'     => wptpl_lorem( 'short' ),
										'centered' => true,
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'    => 'Pain point 3',
										'text'     => wptpl_lorem( 'short' ),
										'centered' => true,
									)
								),
							),
						),
						array(),
						'',
						'3rem'
					),
				),
				'surface'
			),

			// 4. Services grid — two rows of three.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Services headline' )
					),
					$service_grid,
				)
			),

			// 5. Specialties pills.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Specialties headline' )
					),
					wptpl_block(
						'tag-list',
						array_merge(
							array(
								'items' => array_map(
									static function ( $n ) {
										return array(
											'label' => 'Specialty ' . $n,
											'url'   => '',
										);
									},
									range( 1, 12 )
								),
							),
							wptpl_margin_top( '2rem' )
						)
					),
				),
				'surface'
			),

			// 6. Bio — portrait beside copy.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'portrait', 'wptpl-portrait-fill' ) ),
							array(
								wptpl_heading( 'Name + credentials', 2 ),
								wptpl_paragraph( wptpl_lorem( 'long' ) ),
								wptpl_paragraph( wptpl_lorem( 'medium' ) ),
								wptpl_block(
									'checklist',
									array(
										'items'     => array(
											array( 'text' => 'Credential' ),
											array( 'text' => 'Credential' ),
											array( 'text' => 'Credential' ),
											array( 'text' => 'Credential' ),
										),
										'direction' => 'horizontal',
									)
								),
							),
						),
						array( '33.33%', '66.66%' )
					),
				)
			),

			// 7. How to get started.
			wptpl_block(
				'steps',
				array(
					'heading'        => 'How to get started',
					'intro'          => wptpl_lorem( 'short' ),
					'items'          => array(
						array(
							'title' => 'Step one',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step two',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step three',
							'text'  => wptpl_lorem( 'short' ),
						),
					),
					'showCta'        => true,
					'ctaText'        => 'Primary CTA',
					'ctaUrl'         => '/contact/',
					'usePlaceholder' => true,
				)
			),

			// 8. FAQ.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Frequently asked questions' )
					),
					wptpl_block(
						'faq',
						array_merge(
							array(
								'items' => array(
									array(
										'question' => 'Question one?',
										'answer'   => wptpl_lorem( 'medium' ),
									),
									array(
										'question' => 'Question two?',
										'answer'   => wptpl_lorem( 'medium' ),
									),
									array(
										'question' => 'Question three?',
										'answer'   => wptpl_lorem( 'medium' ),
									),
									array(
										'question' => 'Question four?',
										'answer'   => wptpl_lorem( 'medium' ),
									),
								),
							),
							wptpl_margin_top( '2rem' )
						)
					),
				),
				'surface',
				// No container: wptpl/faq already renders inside
				// wptpl-container-narrow.
				''
			),

			// 9. Closing CTA.
			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'theme'    => 'dark',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// About
// ---------------------------------------------------------------------------

/**
 * About: hero, pull quote, portrait + story, approach, four modalities,
 * what sessions look like, who it is for, steps, closing CTA.
 */
function wptpl_seed_page_about(): string {
	// Modalities card: four approaches sharing one 500px content column, split
	// by wptpl-sep-wide dividers so heading, body and rule all line up.
	$modalities = array();
	foreach ( range( 1, 4 ) as $n ) {
		$modalities[] = wptpl_heading( wptpl_lorem_len( 38 ), 3, '', 'center' );
		$modalities[] = wptpl_paragraph( wptpl_lorem_len( 115 ) );
		if ( 4 !== $n ) {
			$modalities[] = wptpl_separator( 'wptpl-sep-wide' );
		}
	}

	// The two icon-beside-copy bands are the same shape, so build them once.
	// The body column carries wptpl-vrule: a bar between the columns that
	// overhangs the copy top and bottom, hidden once the columns stack.
	$rule_band = static function ( string $bg, int $head, array $paragraphs ): string {
		$body = array();
		foreach ( $paragraphs as $length ) {
			$body[] = wptpl_paragraph( wptpl_lorem_len( $length ) );
		}

		return wptpl_section(
			array(
				wptpl_columns(
					array(
						array(
							wptpl_image( 'service-card', '', '96px' ),
							wptpl_block(
								'section-header',
								array(
									'headline'  => wptpl_lorem_len( $head ),
									'alignment' => 'left',
								)
							),
						),
						$body,
					),
					array(),
					'',
					'',
					true,
					array( '', 'wptpl-vrule' )
				),
			),
			$bg
		);
	};

	return implode(
		"\n\n",
		array(
			// 1. Masthead: portrait beside the name, role line and opening story.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array(
								wptpl_group(
									array( wptpl_image( 'portrait', 'is-style-rounded-square', '440px' ) ),
									'white'
								),
							),
							array(
								wptpl_block(
									'section-header',
									array(
										'headline'     => 'Practitioner name',
										'alignment'    => 'left',
										'headingLevel' => 1,
										'className'    => 'wptpl-hero-header',
									)
								),
								wptpl_heading( 'Role line, credentials', 2 ),
								wptpl_paragraph( wptpl_lorem_len( 205 ) ),
								wptpl_paragraph( wptpl_lorem_len( 277 ) ),
								wptpl_paragraph( wptpl_lorem_len( 78 ) ),
							),
						),
						array( '440px', '' ),
						'',
						'',
						true
					),
				),
				'primary',
				'container-md',
				'is-style-overlay-primary'
			),

			// 2. Long-form story on a photo band, the copy held in a light card.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => wptpl_lorem_len( 62 ) )
					),
					wptpl_group(
						array(
							wptpl_paragraph( wptpl_lorem_len( 460 ) ),
							wptpl_paragraph( wptpl_lorem_len( 520 ) ),
							wptpl_paragraph( wptpl_lorem_len( 442 ) ),
							wptpl_html( '<div style="margin-top:1.5rem"><a class="wptpl-btn-accent" href="/contact/">Primary CTA</a></div>' ),
						),
						'on-dark',
						'',
						'',
						'2.5rem'
					),
				),
				'cta-bg'
			),

			// 3. Approach — icon + title beside the copy, split by a rule.
			$rule_band( 'muted', 47, array( 283, 194, 161 ) ),

			// 4. Modalities card on a photo band.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => wptpl_lorem_len( 33 ) )
					),
					wptpl_paragraph( wptpl_lorem_len( 134 ), '', 'center' ),
					wptpl_group( $modalities, 'accent', 'on-dark', 'wptpl-card-inset', '2.5rem' ),
				),
				'steps-bg'
			),

			// 5. What working together looks like — same shape as band 3.
			$rule_band( 'primary', 36, array( 190, 108 ) ),

			// 6. Who this is for: a 2-up card row plus a lone third card, matched
			// to the pair's rendered width by wptpl-card-solo.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => wptpl_lorem_len( 32 ) )
					),
					wptpl_columns(
						array(
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'     => wptpl_lorem_len( 73 ),
										'text'      => wptpl_lorem_len( 323 ),
										'centered'  => true,
										'bordered'  => false,
										'className' => 'wptpl-title-sm',
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'     => wptpl_lorem_len( 38 ),
										'text'      => wptpl_lorem_len( 251 ),
										'centered'  => true,
										'bordered'  => false,
										'className' => 'wptpl-title-sm',
									)
								),
							),
						),
						array(),
						'',
						'2.5rem'
					),
					wptpl_block(
						'feature-card',
						array_merge(
							array(
								'title'     => wptpl_lorem_len( 47 ),
								'text'      => wptpl_lorem_len( 178 ),
								'centered'  => true,
								'bordered'  => false,
								'className' => 'wptpl-title-sm wptpl-card-solo',
							),
							wptpl_margin_top( '1.5rem' )
						)
					),
				)
			),

			// 7. How to get started.
			wptpl_block(
				'steps',
				array(
					'heading'        => 'How to get started',
					'items'          => array(
						array(
							'title' => wptpl_lorem_len( 26 ),
							'text'  => wptpl_lorem_len( 59 ),
						),
						array(
							'title' => wptpl_lorem_len( 14 ),
							'text'  => wptpl_lorem_len( 49 ),
						),
						array(
							'title' => wptpl_lorem_len( 28 ),
							'text'  => wptpl_lorem_len( 73 ),
						),
					),
					'showCta'        => true,
					'ctaText'        => 'Primary CTA',
					'ctaUrl'         => '/contact/',
					'usePlaceholder' => true,
				)
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => wptpl_lorem_len( 61 ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'theme'    => 'dark',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Services index
// ---------------------------------------------------------------------------

/**
 * Services: hero over a photo, a six-card grid, one wide bilingual card, CTA.
 */
function wptpl_seed_page_services(): string {
	// Two per row, not three. These cards carry a title, a paragraph and an
	// arrow link with no image, so at three-up the text column gets narrow
	// enough that every title wraps. Each is an H2 held at the H3 visual size:
	// they are the page's real section headings, so the level has to be right
	// even though the design does not want them at H2 scale.
	$cards = array();
	foreach ( wptpl_seed_services() as $service ) {
		$cards[] = wptpl_block(
			'feature-card',
			array(
				'title'        => $service['title'],
				'text'         => wptpl_lorem_len( 170 ),
				'headingLevel' => 2,
				'showImage'    => false,
				'ctaText'      => 'Learn more',
				'ctaUrl'       => '/services/' . $service['slug'] . '/',
				'ctaStyle'     => 'arrow',
				'className'    => 'wptpl-h2-as-h3',
			)
		);
	}

	$rows = array();
	foreach ( array_chunk( $cards, 2 ) as $index => $row ) {
		$rows[] = wptpl_columns(
			array_map(
				static function ( $card ) {
					return array( $card );
				},
				$row
			),
			array(),
			'',
			0 === $index ? '' : '1.5rem'
		);
	}

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'title'              => 'Services headline',
					'subtitle'           => wptpl_lorem_len( 73 ),
					'alignment'          => 'center',
					'ctaText'            => 'Primary CTA',
					'ctaUrl'             => '/contact/',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
					'overlayOpacity'     => 0.6,
					'className'          => 'wptpl-hero-dark',
				)
			),

			// One band holds the whole list. No closing CTA banner: every card
			// already links onward, and a banner under them competes with all of
			// them at once.
			wptpl_section( $rows, '', 'container-md', 'is-style-overlay-primary' ),
		)
	);
}

function wptpl_seed_page_service( array $service, array $siblings ): string {
	$checklist = static function ( string $prefix ) {
		return array(
			array( 'text' => $prefix . ' one' ),
			array( 'text' => $prefix . ' two' ),
			array( 'text' => $prefix . ' three' ),
		);
	};

	$related = array();
	foreach ( array_slice( $siblings, 0, 3 ) as $sibling ) {
		$related[] = array(
			'label' => $sibling['title'],
			'url'   => '/services/' . $sibling['slug'] . '/',
		);
	}

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'title'              => $service['title'],
					'subtitle'           => wptpl_lorem( 'medium' ),
					'layout'             => 'centered',
					'alignment'          => 'center',
					'ctaText'            => 'Primary CTA',
					'ctaUrl'             => '/contact/',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
					'className'          => 'wptpl-hero-dark is-intro-wide',
				)
			),

			// Symptoms — narrow label column beside two checklist columns.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_heading( 'Symptoms', 2, 'wptpl-heading-shrink wptpl-h2-as-h3' ) ),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 'Symptom' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 'Symptom' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
						),
						array( '24%', '38%', '38%' )
					),
				),
				'primary'
			),

			// Long-form explainer on a tinted band. Wider than container-narrow
			// so the paragraph does not run as a thin ribbon down the middle of
			// the page, and long enough to sit like the copy it stands in for.
			wptpl_section(
				array(
					wptpl_paragraph( wptpl_lorem_len( 389 ) ),
				),
				'muted',
				'container-md',
				'wptpl-overlay-dark'
			),

			// This is for you if.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_heading( 'This is for you if…', 2, 'wptpl-heading-shrink wptpl-h2-as-h3' ) ),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 'Reason' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 'Reason' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
						),
						array( '24%', '38%', '38%' )
					),
				)
			),

			// Topic pills on a dark band.
			wptpl_section(
				array(
					wptpl_block(
						'tag-list',
						array(
							'rowBreak'  => 3,
							'items'     => array_map(
								static function ( $n ) {
									return array(
										'label' => 'Topic ' . $n,
										'url'   => '',
									);
								},
								range( 1, 6 )
							),
							'className' => 'wptpl-tags-light wptpl-tags-lg',
						)
					),
				),
				'secondary',
				'container-narrow',
				'wptpl-overlay-dark'
			),

			// FAQ.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Frequently asked questions' )
					),
					wptpl_block(
						'faq',
						array(
							'items' => array(
								array(
									'question' => 'Question one?',
									'answer'   => wptpl_lorem( 'medium' ),
								),
								array(
									'question' => 'Question two?',
									'answer'   => wptpl_lorem( 'medium' ),
								),
								array(
									'question' => 'Question three?',
									'answer'   => wptpl_lorem( 'medium' ),
								),
							),
						)
					),
				),
				'',
				// No container: wptpl/faq already renders inside
				// wptpl-container-narrow.
				''
			),

			// Related services.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Related services' )
					),
					wptpl_block(
						'tag-list',
						array(
							'items'     => $related,
							'className' => 'wptpl-tags-light wptpl-tags-lg',
						)
					),
				),
				'secondary',
				'container-narrow',
				'wptpl-section-tight'
			),

			wptpl_block(
				'steps',
				array(
					'heading'        => 'How to get started',
					'items'          => array(
						array(
							'title' => 'Step one',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step two',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step three',
							'text'  => wptpl_lorem( 'short' ),
						),
					),
					'showCta'        => true,
					'ctaText'        => 'Primary CTA',
					'ctaUrl'         => '/contact/',
					'overlayOpacity' => 0.7,
					'usePlaceholder' => true,
					'className'      => 'wptpl-steps-pad-top',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Contact
// ---------------------------------------------------------------------------

/**
 * Contact: hero, form beside practice info, steps, closing CTA.
 *
 * The form itself comes from a form plugin. The seeder drops a clearly marked
 * placeholder inside the `wptpl-form` wrapper the theme styles, so the layout
 * is right and swapping in the real shortcode is a one-line edit.
 */
function wptpl_seed_page_contact(): string {
	// Practice-info rows: a bold label beside its value, as a 40/60 split so
	// every value starts on the same vertical line however long its label is.
	$info_row = static function ( string $label, int $value ): string {
		return wptpl_columns(
			array(
				array( wptpl_paragraph( '<strong>' . $label . '</strong>' ) ),
				array( wptpl_paragraph( wptpl_lorem_len( $value ), 'has-small-font-size' ) ),
			),
			array( '40%', '60%' )
		);
	};

	return implode(
		"\n\n",
		array(
			// 1. Masthead over a photo — a section-header carrying the H1, not a
			// hero block. Contact opens on the page title and goes straight to the
			// form; a hero's CTA would only point back at the page you are on.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'     => wptpl_lorem_len( 66 ),
							'intro'        => 'Intro line',
							'headingLevel' => 1,
						)
					),
				),
				'hero'
			),

			// 2. Form beside the practice-info card.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array(
								wptpl_group(
									array(
										wptpl_paragraph( 'Replace this block with the form plugin shortcode. The theme styles any form inside a <code>wptpl-form</code> wrapper.' ),
									),
									'',
									'',
									'wptpl-form'
								),
								wptpl_paragraph( wptpl_lorem_len( 87 ), '', '' ),
							),
							array(
								wptpl_group(
									array(
										wptpl_heading( 'Practice info', 2, 'wptpl-h2-as-h3' ),
										$info_row( 'Hours', 32 ),
										$info_row( 'Location', 54 ),
									),
									'secondary',
									'on-dark'
								),
							),
						),
						array( '62%', '38%' )
					),
				),
				'surface',
				'container-md',
				'is-style-overlay-base'
			),

			// 3. What happens next — the steps band closes the page. No CTA banner
			// after it: the form above is the call to action.
			wptpl_block(
				'steps',
				array(
					'heading'        => 'What happens next',
					'items'          => array(
						array(
							'title' => wptpl_lorem_len( 24 ),
							'text'  => wptpl_lorem_len( 100 ),
						),
						array(
							'title' => wptpl_lorem_len( 20 ),
							'text'  => wptpl_lorem_len( 100 ),
						),
						array(
							'title' => wptpl_lorem_len( 26 ),
							'text'  => wptpl_lorem_len( 100 ),
						),
					),
					'overlayOpacity' => 0.7,
					'overlayColor'   => 'primary',
					'usePlaceholder' => true,
					'className'      => 'wptpl-steps-pad-top',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Blog hub
// ---------------------------------------------------------------------------

/**
 * Blog hub: hero, category filter, featured post, post grid, guide cards, CTA.
 *
 * This is a normal page, not `page_for_posts` — the listing comes from the
 * `wptpl/post-grid` and `wptpl/featured-post` blocks, which is what lets the
 * hub carry a hero and a filter.
 */
function wptpl_seed_page_blog(): string {
	$guide = static function ( int $n ) {
		return wptpl_block(
			'feature-card',
			array(
				'title'     => 'Guide ' . $n,
				'text'      => wptpl_lorem( 'short' ),
				'imageUrl'  => get_template_directory_uri() . '/assets/placeholders/guide-card.jpg',
				'ctaText'   => 'Download',
				'ctaUrl'    => '/guide-landing/',
				'ctaStyle'  => 'arrow',
				'className' => 'wptpl-post-card',
			)
		);
	};

	return implode(
		"\n\n",
		array(
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'     => 'Blog headline',
							'intro'        => wptpl_lorem( 'medium' ),
							'headingLevel' => 1,
							'className'    => 'wptpl-header-xwide',
						)
					),
				),
				'primary',
				'container',
				'wptpl-flush-x'
			),

			wptpl_section(
				array(
					wptpl_block( 'category-filter' ),
				),
				'primary',
				'container',
				'wptpl-section-sm'
			),

			wptpl_section(
				array(
					wptpl_block( 'featured-post' ),
				),
				'',
				'container-md'
			),

			wptpl_section(
				array(
					wptpl_block( 'post-grid', array( 'count' => 9 ) ),
					wptpl_columns(
						array(
							array( $guide( 1 ) ),
							array( $guide( 2 ) ),
							array( $guide( 3 ) ),
						)
					),
				),
				'',
				'container'
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'theme'    => 'dark',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Team
// ---------------------------------------------------------------------------

/**
 * Meet Our Therapists: intro, a grid of practitioner cards, closing CTA.
 *
 * The cards are slots, not people. Service pages are grouped by theme precisely
 * so the site does not need restructuring when the roster changes.
 */
function wptpl_seed_page_therapists(): string {
	$card = static function ( int $n ) {
		return wptpl_block(
			'feature-card',
			array(
				'title'    => 'Therapist ' . $n,
				'text'     => wptpl_lorem( 'short' ),
				'imageUrl' => get_template_directory_uri() . '/assets/placeholders/portrait.jpg',
				'centered' => true,
			)
		);
	};

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'title'     => 'Meet our therapists',
					'subtitle'  => wptpl_lorem( 'medium' ),
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => 'Primary CTA',
					'ctaUrl'    => '/contact/',
				)
			),
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( $card( 1 ) ),
							array( $card( 2 ) ),
							array( $card( 3 ) ),
						)
					),
					wptpl_columns(
						array(
							array( $card( 4 ) ),
							array( $card( 5 ) ),
							array( $card( 6 ) ),
						)
					),
				)
			),
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'How we work together',
							'intro'    => wptpl_lorem( 'medium' ),
						)
					),
				),
				'surface',
				'container-narrow'
			),
			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'theme'    => 'dark',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Conversion pages
// ---------------------------------------------------------------------------

/**
 * A conversion page: hero, body section, three supporting cards, steps, CTA.
 * Payment, Donate and Church Partnerships share this shape.
 *
 * @param string $title     Page title.
 * @param string $body_head Heading for the body section.
 * @param string $card_noun Noun used for the three supporting cards.
 * @param string $cta       Label for the calls to action.
 */
function wptpl_seed_page_conversion( string $title, string $body_head, string $card_noun, string $cta ): string {
	$card = static function ( int $n ) use ( $card_noun ) {
		return wptpl_block(
			'feature-card',
			array(
				'title'    => $card_noun . ' ' . $n,
				'text'     => wptpl_lorem( 'short' ),
				'centered' => true,
			)
		);
	};

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'title'     => $title,
					'subtitle'  => wptpl_lorem( 'medium' ),
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => $cta,
					'ctaUrl'    => '/contact/',
				)
			),
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => $body_head,
							'intro'     => wptpl_lorem( 'long' ),
							'alignment' => 'left',
						)
					),
				),
				'',
				'container-narrow'
			),
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( $card( 1 ) ),
							array( $card( 2 ) ),
							array( $card( 3 ) ),
						)
					),
				),
				'surface'
			),
			wptpl_block(
				'steps',
				array(
					'heading' => 'How it works',
					'items'   => array(
						array(
							'title' => 'Step one',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step two',
							'text'  => wptpl_lorem( 'short' ),
						),
						array(
							'title' => 'Step three',
							'text'  => wptpl_lorem( 'short' ),
						),
					),
				)
			),
			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => $cta,
					'ctaUrl'   => '/contact/',
					'theme'    => 'dark',
				)
			),
		)
	);
}

// ---------------------------------------------------------------------------
// AI information
// ---------------------------------------------------------------------------

/**
 * A page written for AI assistants and answer engines rather than for visitors.
 *
 * Unlike every other page here, its value IS the section list: an assistant
 * summarising the practice should find each fact under a predictable heading.
 * So this one seeds the real headings with placeholder bodies — the structure is
 * the deliverable, the prose is what the organisation fills in.
 *
 * Deliberately plain: no hero, no cards, one narrow reading column.
 */
function wptpl_seed_page_ai_information(): string {
	$sections = array(
		'Basic information'             => 'Legal and operating name, organisation type, headquarters, service area, contact details and web properties.',
		'What we do'                    => 'Mission statement, and the approach that distinguishes this practice from others.',
		'Services offered'              => 'Each service with its own URL and a one-line description.',
		'What we help with'             => 'The concerns people arrive with, each linked to the page that covers it.',
		'Our approach'                  => 'Therapeutic modalities used, and how faith is integrated into clinical practice.',
		'Who we serve'                  => 'Who this practice is for — and who it is not the right fit for.',
		'Credentials and trust signals' => 'Licensure, accreditations and compliance the organisation holds.',
		'Leadership and clinical team'  => 'Who leads the practice, their credentials, and how to reach them.',
		'What makes us different'       => 'Positioning against the alternatives someone would otherwise consider.',
		'How to reach us'               => 'Intake process, response times, and the fastest route to a first conversation.',
		'Key facts for AI assistants'   => 'The handful of statements that must be correct in any summary of this organisation.',
		'Common misconceptions'         => 'Frequent misunderstandings, each paired with the accurate statement.',
		'Source links'                  => 'The canonical page backing each claim above.',
		'Suggested summary'             => 'A short paragraph the organisation is happy to see quoted verbatim.',
	);

	$blocks = array(
		wptpl_heading( 'AI information', 1 ),
		wptpl_paragraph( 'This page holds structured information about the organisation, intended to help AI assistants, search engines and answer engines describe it accurately. It is maintained by the organisation and is the authoritative source for the facts below.' ),
		wptpl_paragraph( '<em>Every section below is a placeholder. Replace each one with verified information before publishing — the entire point of this page is that what it says is true.</em>' ),
	);

	foreach ( $sections as $heading => $what_goes_here ) {
		$blocks[] = wptpl_heading( $heading, 2 );
		$blocks[] = wptpl_paragraph( '<em>' . $what_goes_here . '</em>' );
		$blocks[] = wptpl_paragraph( wptpl_lorem( 'long' ) );
	}

	return wptpl_section( $blocks, '', 'container-narrow' );
}

/**
 * A legal page: title, a "replace this" note, then one heading + placeholder
 * body per section.
 *
 * The section headings are real — they are what each document has to cover —
 * while the bodies are lorem. Compliance copy has to be written or reviewed by
 * someone qualified, so seeding plausible-sounding legal text would be worse
 * than useless: it reads as finished and invites publishing it as-is.
 *
 * @param string             $title    Page title.
 * @param string             $intro    One-line description of the document.
 * @param array<int, string> $sections Section headings, in order.
 */
function wptpl_seed_page_legal( string $title, string $intro, array $sections ): string {
	$blocks = array(
		wptpl_heading( $title, 1 ),
		wptpl_paragraph( $intro ),
		wptpl_paragraph( '<em>Placeholder. This document must be written or reviewed by qualified counsel before publishing — do not ship the text below.</em>' ),
		wptpl_paragraph( '<em>Last updated: replace with the date this document was last reviewed.</em>' ),
	);

	foreach ( $sections as $heading ) {
		$blocks[] = wptpl_heading( $heading, 2 );
		$blocks[] = wptpl_paragraph( wptpl_lorem( 'long' ) );
	}

	return wptpl_section( $blocks, '', 'container-narrow' );
}

// ---------------------------------------------------------------------------
// Orchestration
// ---------------------------------------------------------------------------

/**
 * Seed every page. Returns a map of key => page ID.
 *
 * @return array<string, mixed>
 */
function wptpl_seed_all_pages(): array {
	$ids = array();

	$ids['home'] = wptpl_seed_page(
		array(
			'slug'    => 'home',
			'title'   => 'Home',
			'content' => wptpl_seed_page_home(),
			'order'   => 0,
		)
	);
	$ids['about'] = wptpl_seed_page(
		array(
			'slug'    => 'about-us',
			'title'   => 'About Us &amp; Our Faith-Centered Approach',
			'content' => wptpl_seed_page_about(),
			'order'   => 1,
		)
	);
	$ids['therapists'] = wptpl_seed_page(
		array(
			'slug'    => 'meet-our-therapists',
			'title'   => 'Meet Our Therapists',
			'content' => wptpl_seed_page_therapists(),
			'order'   => 2,
		)
	);
	$ids['services'] = wptpl_seed_page(
		array(
			'slug'    => 'services',
			'title'   => 'Services',
			'content' => wptpl_seed_page_services(),
			'order'   => 3,
		)
	);

	$services                = wptpl_seed_services();
	$ids['service_children'] = array();

	foreach ( $services as $index => $service ) {
		$siblings = array_values(
			array_filter(
				$services,
				static function ( $candidate ) use ( $service ) {
					return $candidate['slug'] !== $service['slug'];
				}
			)
		);
		$ids['service_children'][ $service['slug'] ] = wptpl_seed_page(
			array(
				'slug'    => $service['slug'],
				'path'    => 'services/' . $service['slug'],
				'title'   => $service['title'],
				'content' => wptpl_seed_page_service( $service, $siblings ),
				'parent'  => $ids['services'],
				'order'   => $index,
			)
		);
	}

	$ids['blog'] = wptpl_seed_page(
		array(
			'slug'    => 'blog',
			'title'   => 'Blog',
			'content' => wptpl_seed_page_blog(),
			'order'   => 4,
		)
	);
	$ids['contact'] = wptpl_seed_page(
		array(
			'slug'    => 'contact',
			'title'   => 'Contact',
			'content' => wptpl_seed_page_contact(),
			'order'   => 5,
		)
	);
	$ids['church'] = wptpl_seed_page(
		array(
			'slug'    => 'church-partnerships',
			'title'   => 'Church Partnerships',
			'content' => wptpl_seed_page_conversion( 'Church partnerships', 'Partnering with your congregation', 'Partnership', 'Start a conversation' ),
			'order'   => 6,
		)
	);
	$ids['payment'] = wptpl_seed_page(
		array(
			'slug'    => 'payment',
			'title'   => 'Payment &amp; Insurance',
			'content' => wptpl_seed_page_conversion( 'Payment &amp; insurance', 'What sessions cost', 'Option', 'Ask about fees' ),
			'order'   => 7,
		)
	);
	$ids['donate'] = wptpl_seed_page(
		array(
			'slug'    => 'donate',
			'title'   => 'Donate',
			'content' => wptpl_seed_page_conversion( 'Donate', 'Where your gift goes', 'Fund', 'Give today' ),
			'order'   => 8,
		)
	);
	$ids['ai_information'] = wptpl_seed_page(
		array(
			'slug'    => 'ai-information',
			'title'   => 'AI Information',
			'content' => wptpl_seed_page_ai_information(),
			'order'   => 9,
		)
	);
	$ids['privacy'] = wptpl_seed_page(
		array(
			'slug'    => 'privacy',
			'title'   => 'Privacy Policy',
			'content' => wptpl_seed_page_legal(
				'Privacy Policy',
				'How this practice collects, uses, stores and shares personal information.',
				array(
					'Information we collect',
					'How we use your information',
					'Protected health information',
					'How we share information',
					'Cookies and analytics',
					'Data retention',
					'Your rights and choices',
					'Children’s privacy',
					'Changes to this policy',
					'Contact us',
				)
			),
			'order'   => 10,
		)
	);
	$ids['terms'] = wptpl_seed_page(
		array(
			'slug'    => 'terms',
			'title'   => 'Terms of Use',
			'content' => wptpl_seed_page_legal(
				'Terms of Use',
				'The terms that govern use of this website.',
				array(
					'Acceptance of these terms',
					'No therapeutic relationship',
					'Not for emergencies',
					'Use of this site',
					'Intellectual property',
					'Third-party links',
					'Disclaimers',
					'Limitation of liability',
					'Governing law',
					'Contact us',
				)
			),
			'order'   => 11,
		)
	);
	$ids['accessibility'] = wptpl_seed_page(
		array(
			'slug'    => 'accessibility',
			'title'   => 'Accessibility Statement',
			'content' => wptpl_seed_page_legal(
				'Accessibility Statement',
				'Our commitment to keeping this website usable by everyone, and how to tell us when it is not.',
				array(
					'Our commitment',
					'Conformance status',
					'Measures we take',
					'Known limitations',
					'Compatibility with assistive technology',
					'Feedback and requests',
					'Contact us',
				)
			),
			'order'   => 12,
		)
	);

	return $ids;
}

/**
 * Seed the menus and assign them to their theme locations.
 *
 * The Footer Legal location holds the three compliance pages. Their bodies are
 * placeholders — see wptpl_seed_page_legal() — but the pages and the menu ship
 * so the footer is structurally complete and the copy has somewhere to land.
 *
 * @param array<string, mixed> $ids Page IDs from wptpl_seed_all_pages().
 */
function wptpl_seed_all_menus( array $ids ): void {
	// No Home item: the logo in the header already links there, and repeating it
	// costs a nav slot for nothing.
	$primary = array(
		array(
			'title'   => 'About',
			'page_id' => $ids['about'],
		),
		array(
			'title'   => 'Our Therapists',
			'page_id' => $ids['therapists'],
		),
		array(
			'key'     => 'services',
			'title'   => 'Services',
			'page_id' => $ids['services'],
		),
	);

	foreach ( wptpl_seed_services() as $service ) {
		$child_id = isset( $ids['service_children'][ $service['slug'] ] ) ? $ids['service_children'][ $service['slug'] ] : 0;
		if ( ! $child_id ) {
			continue;
		}
		$primary[] = array(
			'title'   => $service['title'],
			'page_id' => $child_id,
			'parent'  => 'services',
		);
	}

	$primary[] = array(
		'title'   => 'Blog',
		'page_id' => $ids['blog'],
	);
	$primary[] = array(
		'title'   => 'Contact',
		'page_id' => $ids['contact'],
	);
	// Church Partnerships sits last and carries its own class: it addresses a
	// different audience than everything before it, and the design sets it apart.
	$primary[] = array(
		'title'   => 'Church Partnerships',
		'page_id' => $ids['church'],
		'classes' => 'wptpl-nav-alt',
	);

	wptpl_seed_menu( 'Primary', 'primary', $primary );

	$ai_url = home_url( '/ai-information/' );
	$ask    = 'Tell me about the counseling practice described at ' . $ai_url;

	wptpl_seed_menu(
		'Footer Links',
		'footer',
		array(
			array(
				'title'   => 'Donate',
				'page_id' => $ids['donate'],
			),
			array(
				'title'   => 'Church Partnerships',
				'page_id' => $ids['church'],
			),
			array(
				'title'   => 'Contact',
				'page_id' => $ids['contact'],
			),
			array(
				'title'   => 'Payment',
				'page_id' => $ids['payment'],
			),
			array(
				'title'   => 'AI Info',
				'page_id' => $ids['ai_information'],
			),
			// These hand the AI information page straight to an assistant, so they
			// are external custom links rather than pages.
			array(
				'title' => 'Ask ChatGPT about us',
				'url'   => 'https://chatgpt.com/?q=' . rawurlencode( $ask ),
			),
			array(
				'title' => 'Ask Claude about us',
				'url'   => 'https://claude.ai/new?q=' . rawurlencode( $ask ),
			),
		)
	);

	wptpl_seed_menu(
		'Footer Legal',
		'footer_legal',
		array(
			array(
				'title'   => 'Privacy Policy',
				'page_id' => $ids['privacy'],
			),
			array(
				'title'   => 'Terms of Use',
				'page_id' => $ids['terms'],
			),
			array(
				'title'   => 'Accessibility',
				'page_id' => $ids['accessibility'],
			),
		)
	);
}
