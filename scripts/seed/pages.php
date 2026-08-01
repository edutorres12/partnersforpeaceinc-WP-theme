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
	// Service cards run in two banks, both dressed identically — same imagery,
	// same wptpl-image-md size modifier — which is what makes the rows read as
	// one grid rather than two unrelated ones.
	//
	// The second bank used to skip both. It still got a picture, because the
	// block auto-fills a placeholder on any CTA card without one, but without
	// the size modifier that picture rendered at full height and the bottom
	// cards came out taller than the three above them.
	$service_card = static function ( array $service, int $title, int $text ): string {
		return wptpl_block(
			'feature-card',
			array(
				'title'     => wptpl_lorem_len( $title ),
				'text'      => wptpl_lorem_len( $text ),
				'ctaText'   => 'Learn more',
				'ctaUrl'    => '/services/' . $service['slug'] . '/',
				'ctaStyle'  => 'arrow',
				'centered'  => true,
				'bordered'  => false,
				'imageUrl'  => get_template_directory_uri() . '/assets/placeholders/service-card.jpg',
				'className' => 'wptpl-image-md',
			)
		);
	};

	$services = wptpl_seed_services();
	$lengths  = array(
		array( 14, 203 ),
		array( 18, 161 ),
		array( 15, 131 ),
		array( 67, 151 ),
		array( 37, 120 ),
		array( 19, 150 ),
		array( 24, 139 ),
		array( 64, 196 ),
	);

	// One columns row for the whole grid, not one per bank of three.
	// `wptpl-card-grid` lays it three across and wraps the remainder onto a
	// centred second row. Two separate rows could not do that: a columns row is
	// a flex row, so the second bank's cards stretched to half the band and each
	// row sized itself independently — the bottom cards came out both wider and
	// taller than the three above them.
	$cards = array();
	foreach ( $services as $n => $service ) {
		$len     = $lengths[ $n % count( $lengths ) ];
		$cards[] = array( $service_card( $service, $len[0], $len[1] ) );
	}
	$service_grid = wptpl_columns(
		$cards,
		array(),
		'wptpl-services-carousel wptpl-card-grid',
		'3rem'
	);

	return implode(
		"\n\n",
		array(
			// 1. Hero — title, subtitle and one CTA. No eyebrow, no microcopy,
			// no secondary CTA: the reference uses none of the three.
			wptpl_block(
				'hero',
				array(
					'title'    => wptpl_lorem_len( 61 ),
					'subtitle' => wptpl_lorem_len( 294 ),
					'ctaText'  => wptpl_lorem_len( 41 ),
					'ctaUrl'   => '/contact/',
					'imageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
				)
			),

			// 2. Trust bar — three credentials, no icon. Wrapped in a full-width
			// column so the row spans the band rather than the content column.
			wptpl_section(
				array(
					wptpl_block(
						'checklist',
						array(
							'items'     => array(
								array( 'text' => wptpl_lorem_len( 36 ) ),
								array( 'text' => wptpl_lorem_len( 32 ) ),
								array( 'text' => wptpl_lorem_len( 49 ) ),
							),
							'direction' => 'horizontal',
							'iconStyle' => 'none',
						)
					),
				),
				'primary',
				'container',
				'wptpl-section-tight',
				'base'
			),

			// 3. Three cards, icon beside the title. `horizontal-header` is what
			// puts the icon and title on one line with the body beneath; without
			// it the icon stacks above and the card grows a third taller.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => wptpl_lorem_len( 65 ),
							'intro'     => wptpl_lorem_len( 68 ),
							'textColor' => 'base',
						)
					),
					wptpl_columns(
						array(
							array( wptpl_card_icon( 19, 152 ) ),
							array( wptpl_card_icon( 50, 206 ) ),
							array( wptpl_card_icon( 23, 176 ) ),
						),
						array(),
						'wptpl-card-grid',
						'3rem'
					),
				),
				'muted',
				'container-md',
				'',
				'base'
			),

			// 4. Services grid.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => wptpl_lorem_len( 74 ) )
					),
					$service_grid,
				)
			),

			// 5. Specialty pills on a dark band.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => wptpl_lorem_len( 40 ),
							'textColor' => 'base',
						)
					),
					wptpl_block(
						'tag-list',
						array_merge(
							array(
								'items' => array_map(
									static function ( $n ) {
										return array(
											'label' => wptpl_lorem_len( 12 + ( $n % 4 ) * 6 ),
											'url'   => '',
										);
									},
									range( 1, 14 )
								),
							),
							wptpl_margin_top( '2rem' )
						)
					),
				),
				'muted',
				'container-md',
				'wptpl-overlay-dark',
				'base'
			),

			// 6. Practitioner bio — fixed-width portrait beside the copy.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'portrait', 'is-style-rounded-square wptpl-portrait-fill', '380px' ) ),
							array(
								wptpl_heading( 'Practitioner name', 2 ),
								// The credential line is set large and widely tracked, so it
								// reads as a label rather than as the first line of the bio.
								// The class only carries the tablet/mobile step-down; the
								// desktop treatment lives here.
								wptpl_paragraph(
									'CREDENTIAL',
									'wptpl-credential-label',
									'',
									array(
										'fontSize'      => '27px',
										'fontWeight'    => '700',
										'letterSpacing' => '0.2em',
									),
									'12px'
								),
								wptpl_paragraph( wptpl_lorem_len( 182 ), '', '', array(), '12px' ),
								wptpl_paragraph( wptpl_lorem_len( 150 ) ),
								wptpl_paragraph( wptpl_lorem_len( 194 ) ),
								wptpl_group(
									array(
										wptpl_block(
											'checklist',
											array(
												'items'     => array(
													array( 'text' => '<strong>' . wptpl_lorem_len( 35 ) . '</strong>' ),
													array( 'text' => '<strong>' . wptpl_lorem_len( 76 ) . '</strong>' ),
													array( 'text' => '<strong>' . wptpl_lorem_len( 29 ) . '</strong>' ),
													array( 'text' => '<strong>' . wptpl_lorem_len( 29 ) . '</strong>' ),
												),
												'iconStyle' => 'plus',
											)
										),
									),
									'',
									'',
									'',
									'1rem'
								),
								wptpl_html( '<div style="margin-top:1.5rem"><a class="wptpl-btn-accent" href="/about-us/">Read the full story</a></div>' ),
							),
						),
						array( '380px', '' ),
						'',
						'',
						true,
						array( '', 'wptpl-bio-copy' ),
						'split'
					),
				),
				'primary-soft'
			),

			// 7. How to get started.
			wptpl_block(
				'steps',
				array(
					'heading'        => wptpl_lorem_len( 18 ),
					'intro'          => wptpl_lorem_len( 34 ),
					'items'          => array(
						array(
							'title' => wptpl_lorem_len( 31 ),
							'text'  => wptpl_lorem_len( 158 ),
						),
						array(
							'title' => wptpl_lorem_len( 28 ),
							'text'  => wptpl_lorem_len( 102 ),
						),
						array(
							'title' => wptpl_lorem_len( 29 ),
							'text'  => wptpl_lorem_len( 98 ),
						),
					),
					'showCta'        => true,
					'ctaText'        => wptpl_lorem_len( 37 ),
					'ctaUrl'         => '/contact/',
					'overlayOpacity' => 0.6,
					'usePlaceholder' => true,
				)
			),

			// 8. FAQ — seven entries, sized like real answers so the accordion
			// opens to a realistic height.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'FAQ' )
					),
					wptpl_block(
						'faq',
						array_merge(
							array(
								'items' => array(
									array(
										'question' => wptpl_lorem_len( 24 ),
										'answer'   => wptpl_lorem_len( 198 ),
									),
									array(
										'question' => wptpl_lorem_len( 30 ),
										'answer'   => wptpl_lorem_len( 339 ),
									),
									array(
										'question' => wptpl_lorem_len( 42 ),
										'answer'   => wptpl_lorem_len( 325 ),
									),
									array(
										'question' => wptpl_lorem_len( 39 ),
										'answer'   => wptpl_lorem_len( 340 ),
									),
									array(
										'question' => wptpl_lorem_len( 27 ),
										'answer'   => wptpl_lorem_len( 371 ),
									),
									array(
										'question' => wptpl_lorem_len( 28 ),
										'answer'   => wptpl_lorem_len( 242 ),
									),
									array(
										'question' => wptpl_lorem_len( 21 ),
										'answer'   => wptpl_lorem_len( 289 ),
									),
								),
							),
							wptpl_margin_top( '2rem' )
						)
					),
				)
			),

			// 9. Closing CTA over a photo.
			wptpl_block(
				'cta-banner',
				array(
					'headline'           => wptpl_lorem_len( 75 ),
					'text'               => WPTPL_BLANK,
					'ctaText'            => wptpl_lorem_len( 31 ),
					'ctaUrl'             => '/contact/',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/cta-bg.jpg',
					'overlayOpacity'     => 0.65,
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
		// Centered like its heading. Left-aligned, each body sits off-axis from
		// the title above it and the dividers stop reading as separators.
		$modalities[] = wptpl_paragraph( wptpl_lorem_len( 115 ), '', 'center' );
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
							wptpl_image( 'icon', '', '96px', 'center' ),
							// Centered, not left: this column is a label for the copy
							// beside it, and the icon above the headline is centered.
							// Left-aligned the headline drifts off the icon's axis and
							// reads as a stray caption. The reference leaves the block
							// at its own default, which is center.
							wptpl_block(
								'section-header',
								array(
									'headline'  => wptpl_lorem_len( $head ),
									'alignment' => 'center',
									'textColor' => 'base',
								)
							),
						),
						$body,
					),
					array(),
					'',
					'',
					true,
					array( '', 'wptpl-vrule' ),
					'split'
				),
			),
			$bg,
			'container-md',
			'',
			'base'
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
										'textColor'    => 'base',
									)
								),
								wptpl_heading( 'Role line, credentials', 2, '', '', 'h2' ),
								// The reference runs the credential line a step up and
								// bold, and closes on a bold line. Left plain, all three
								// paragraphs render at one weight and the column reads
								// as an undifferentiated block of text however much gap
								// sits between them.
								wptpl_paragraph(
									wptpl_lorem_len( 205 ),
									'has-custom-font-size',
									'',
									array(
										'fontSize'   => '1.125rem',
										'fontWeight' => '700',
									)
								),
								wptpl_paragraph( wptpl_lorem_len( 277 ) ),
								wptpl_paragraph( wptpl_lorem_len( 78 ), '', '', array( 'fontWeight' => '700' ) ),
							),
						),
						array( '440px', '' ),
						'',
						'',
						true,
						array( '', 'wptpl-bio-copy' ),
						'split'
					),
				),
				'primary',
				// The widest tier, like the reference. This band is a portrait
				// beside a whole opening story; at container-md the copy column
				// loses ~200px and the story runs a paragraph longer.
				'container',
				'is-style-overlay-primary',
				'base'
			),

			// 2. Long-form story on a photo band, the copy held in a light card.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => wptpl_lorem_len( 62 ),
							'textColor' => 'base',
						)
					),
					wptpl_group(
						array(
							wptpl_paragraph( wptpl_lorem_len( 460 ), '', 'center' ),
							wptpl_paragraph( wptpl_lorem_len( 520 ), '', 'center' ),
							wptpl_paragraph( wptpl_lorem_len( 442 ), '', 'center' ),
						),
						// The card is light and sits inside a band whose text color
						// is light, so without its own dark text it renders white
						// on near-white.
						'on-dark',
						'contrast',
						'',
						'2.5rem',
						'card-prose'
					),
				),
				'cta-bg',
				'secondary',
				55,
				'',
				// The reference holds this band at 815px. Without it the Cover
				// falls back to theme.json's 1400px and the headline above the
				// card runs the full width of the page.
				'815px'
			),

			// 3. Approach — icon + title beside the copy, split by a rule.
			$rule_band( 'muted', 47, array( 283, 194, 161 ) ),

			// 4. Modalities card on a photo band.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => wptpl_lorem_len( 33 ),
							'textColor' => 'base',
						)
					),
					// The lead under the header runs a step larger and bold: it is a
					// standfirst for the card below, not body copy.
					wptpl_paragraph(
						wptpl_lorem_len( 134 ),
						'has-custom-font-size',
						'center',
						array(
							'fontSize'   => '1.25rem',
							'fontWeight' => '700',
						),
						'1rem'
					),
					wptpl_group( $modalities, 'accent', 'on-dark', 'wptpl-card-inset', '2.5rem', 'card' ),
				),
				'steps-bg',
				'secondary',
				55,
				'',
				'var(--wptpl-container-narrow)'
			),

			// 5. What working together looks like — same shape as band 3.
			$rule_band( 'primary', 36, array( 190, 108 ) ),

			// 6. Who this is for: three cards, two across with the third centred
			// under them. One grid, not a 2-up row plus a loose card — that shape
			// gave the third card its own width and its own height, and nothing
			// short of hand-matching kept the three in step.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'  => wptpl_lorem_len( 32 ),
							'textColor' => 'accent',
						)
					),
					wptpl_columns(
						array(
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'           => wptpl_lorem_len( 73 ),
										'text'            => wptpl_lorem_len( 323 ),
										'centered'        => true,
										'bordered'        => false,
										'className'       => 'wptpl-title-sm',
										'backgroundColor' => 'accent',
										'textColor'       => 'on-dark',
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'           => wptpl_lorem_len( 38 ),
										'text'            => wptpl_lorem_len( 300 ),
										'centered'        => true,
										'bordered'        => false,
										'className'       => 'wptpl-title-sm',
										'backgroundColor' => 'accent',
										'textColor'       => 'on-dark',
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'           => wptpl_lorem_len( 47 ),
										'text'            => wptpl_lorem_len( 296 ),
										'centered'        => true,
										'bordered'        => false,
										'className'       => 'wptpl-title-sm',
										'backgroundColor' => 'accent',
										'textColor'       => 'on-dark',
									)
								),
							),
						),
						array(),
						'wptpl-card-grid is-2-up',
						'2.5rem'
					),
				),
				// A background placeholder, not a card one: `guide-card` is an
				// 800x500 box with the word "Image" drawn into it at card scale.
				// Stretched to cover a full-bleed band it scales that word to the
				// height of the section — the giant letters showing through behind
				// the cards. `cta-bg` is drawn for a band, and the wash keeps the
				// placeholder from competing with the copy the way an undimmed one
				// does.
				'cta-bg',
				'base',
				55,
				'',
				'var(--wptpl-container-md)'
			),

			// 7. How to get started. The reference builds this band by hand from
			// core columns, but doing the same here left About with step cards a
			// different size from every other steps band on the site — two
			// implementations of one section drift apart the moment either is
			// touched. One block, one shape, everywhere.
			//
			// No heading and no photo, which is what the reference's band has:
			// three bordered cards on the page background, nothing above them.
			// The heading plus the block's own header margin plus the band
			// padding stacked into a bank of empty space above the cards.
			wptpl_block(
				'steps',
				array(
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
					// The photo band, like every other steps section on the site.
					// Dropping it left About as the one page whose steps sat on
					// the page background with hairline cards — a different
					// section from the one the other pages were approved with.
					// It stays headerless, so the block gives it the taller top
					// on its own.
					'overlayOpacity' => 0.7,
					'usePlaceholder' => true,
				)
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => wptpl_lorem_len( 61 ),
					// Blank, not absent: an omitted `text` falls back to the
					// block's own placeholder body. The reference runs this
					// banner as a headline and a button, nothing else.
					'text'     => WPTPL_BLANK,
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

	// One grid for the whole list, two across. Chunking into a columns row per
	// pair let each row size itself, so an odd service ended up alone in a row
	// of one and stretched to the full band. `wptpl-card-grid is-2-up` keeps
	// every card on the same track and centres the odd one under the pair above.
	$grid = wptpl_columns(
		array_map(
			static function ( $card ) {
				return array( $card );
			},
			$cards
		),
		array(),
		'wptpl-card-grid is-2-up'
	);

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

			// One band holds the whole list.
			wptpl_section( array( $grid ), '', 'container-md', 'is-style-overlay-primary' ),

			// Closing CTA. An earlier pass dropped this on the reasoning that the
			// cards already link onward — but the reference has one, and it is the
			// only route on the page to the contact form rather than to another
			// service.
			wptpl_block(
				'cta-banner',
				array(
					'headline'           => wptpl_lorem_len( 61 ),
					'text'               => WPTPL_BLANK,
					'ctaText'            => wptpl_lorem_len( 31 ),
					'ctaUrl'             => '/contact/',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/cta-bg.jpg',
					'overlayOpacity'     => 0.65,
				)
			),
		)
	);
}

function wptpl_seed_page_service( array $service, array $siblings ): string {
	// Real sentence lengths, not "Symptom one". These rows are the page's
	// densest content and they set the height of the band around them; three
	// two-word labels leave it looking half-empty and give no sense of how the
	// finished list will sit.
	$checklist = static function ( int $seed ) {
		$rows = array();
		foreach ( array( 46, 62, 38, 54 ) as $i => $length ) {
			$rows[] = array( 'text' => wptpl_lorem_len( $length + ( $seed * 3 ) + $i ) );
		}

		return $rows;
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
					'title'     => $service['title'],
					'subtitle'  => wptpl_lorem_len( 125 ),
					'ctaText'   => 'Primary CTA',
					'ctaUrl'    => '/contact/',
					// `imageUrl`, not `backgroundImageUrl`: the reference runs this
					// hero as the split variant — a dark panel carrying the title,
					// lead and CTA with the photo beside it. A background image
					// switches the block to its overlay variant instead, which lays
					// the copy on top of the picture and centres it.
					'imageUrl'  => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
					'className' => 'wptpl-hero-dark is-intro-wide',
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
										'items'     => $checklist( 1 ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 2 ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
						),
						array( '24%', '38%', '38%' ),
						'',
						'',
						false,
						array(),
						'split',
						'',
						// The label column centres against the two checklists beside
						// it; the checklists stay top-aligned so their first items
						// line up with each other. Without this the label sits at the
						// top of a much taller row and reads as a stray heading.
						array(
							'row' => 'top',
							0     => 'center',
						)
					),
				),
				'primary',
				'container-md',
				'',
				'base'
			),

			// A single pull quote on a tinted band, centered and a step above body
			// size. One paragraph, not two: this band is a beat between the two
			// checklist sections, and a second paragraph turns it back into
			// ordinary copy.
			wptpl_section(
				array(
					wptpl_paragraph(
						wptpl_lorem_len( 389 ),
						'',
						'center',
						array(
							'fontSize'   => '20px',
							'lineHeight' => '1.3',
							'fontWeight' => '600',
						)
					),
				),
				'muted',
				'container-md',
				'wptpl-overlay-dark',
				'base'
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
										'items'     => $checklist( 3 ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
										'iconStyle' => 'plus',
										'items'     => $checklist( 4 ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
						),
						array( '24%', '38%', '38%' ),
						'',
						'',
						false,
						array(),
						'split',
						'',
						// The label column centres against the two checklists beside
						// it; the checklists stay top-aligned so their first items
						// line up with each other. Without this the label sits at the
						// top of a much taller row and reads as a stray heading.
						array(
							'row' => 'top',
							0     => 'center',
						)
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
							// Short, uniform labels, so they read well two across on
							// a phone. The related-services band below keeps the
							// stack: its labels are full service names.
							'className' => 'wptpl-tags-light wptpl-tags-lg wptpl-tags-2up',
						)
					),
				),
				'secondary',
				'container-md',
				'wptpl-overlay-dark',
				'base'
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
									'answer'   => wptpl_lorem_len( 125 ),
								),
								array(
									'question' => 'Question two?',
									'answer'   => wptpl_lorem_len( 125 ),
								),
								array(
									'question' => 'Question three?',
									'answer'   => wptpl_lorem_len( 125 ),
								),
							),
						) + wptpl_margin_top( '2rem' )
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
						array(
							'headline'  => 'Related services',
							'textColor' => 'base',
						)
					),
					wptpl_block(
						'tag-list',
						array(
							'items'     => $related,
							'className' => 'wptpl-tags-light wptpl-tags-lg',
						) + wptpl_margin_top( '2.5rem' )
					),
				),
				'secondary',
				// container-md, like every other band on this page. Narrow held the
				// pills to 760px, so a row of six wrapped to three lines while the
				// same list ran flat everywhere else.
				'container-md',
				// Full section padding, not tight: the reference gives this band
				// 100px top and bottom like any other.
				'',
				'base'
			),

			wptpl_block(
				'steps',
				array(
					'heading'        => 'How to get started',
					'items'          => array(
						array(
							'title' => 'Step one',
							'text'  => wptpl_lorem_len( 56 ),
						),
						array(
							'title' => 'Step two',
							'text'  => wptpl_lorem_len( 56 ),
						),
						array(
							'title' => 'Step three',
							'text'  => wptpl_lorem_len( 56 ),
						),
					),
					'showCta'        => true,
					'ctaText'        => 'Primary CTA',
					'ctaUrl'         => '/contact/',
					'overlayOpacity' => 0.7,
					'usePlaceholder' => true,
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
			array( '40%', '60%' ),
			'',
			'',
			false,
			array(),
			'rows',
			// Bottom, not top: the card's heading owns the gap above the first
			// row, so a top margin here would double it. Each row then spaces
			// itself from the next, and the last one from the card's floor.
			'1.5rem',
			array(
				'row' => 'top',
				0     => 'top',
				1     => 'top',
			)
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
							'intro'        => wptpl_lorem_len( 14 ),
							'headingLevel' => 1,
							'textColor'    => 'base',
						)
					),
				),
				'hero',
				'secondary',
				55,
				'',
				'var(--wptpl-container-md)'
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
								// A footnote under the form (privacy / response time), so
								// it runs a step below body copy and sits close to the
								// last field rather than reading as a new paragraph.
								wptpl_paragraph(
									wptpl_lorem_len( 87 ),
									'',
									'',
									array( 'fontSize' => '14px' ),
									'0.5rem'
								),
							),
							array(
								wptpl_group(
									array(
										wptpl_heading( 'Practice info', 2, 'wptpl-h2-as-h3' ),
										$info_row( 'Hours', 32 ),
										$info_row( 'Location', 54 ),
									),
									'secondary',
									'on-dark',
									'wptpl-card-rows',
									'',
									'card-tight'
								),
							),
						),
						array( '62%', '38%' ),
						'',
						'',
						false,
						array(),
						// The reference runs this one gap 6px wider than a plain
						// image-beside-copy split; see WPTPL_ROW_GAPS.
						'form',
						'',
						// Top, not center: the form is much taller than the info
						// card, and centering floats the card halfway down the
						// band with its heading level with the form's middle.
						array(
							'row' => 'top',
							0     => 'top',
							1     => 'top',
						)
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
					// No heading: the band is the last thing on the page and the
					// numbered steps carry their own meaning. Titles run long and
					// bodies short here — the reverse of the home page's steps —
					// because each step is a sentence, not a label with an
					// explanation under it.
					'items'          => array(
						array(
							'title' => wptpl_lorem_len( 44 ),
							'text'  => wptpl_lorem_len( 62 ),
						),
						array(
							'title' => wptpl_lorem_len( 47 ),
							'text'  => wptpl_lorem_len( 53 ),
						),
						array(
							'title' => wptpl_lorem_len( 30 ),
							'text'  => wptpl_lorem_len( 71 ),
						),
					),
					'overlayOpacity' => 0.7,
					'overlayColor'   => 'primary',
					// The background image is what makes the block render its
					// bordered cards with the number circle overhanging the top
					// edge — see $wptpl_card_class in the block's render.php.
					// Without it the band is bare columns.
					'usePlaceholder' => true,
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

	return implode(
		"\n\n",
		array(
			// Masthead over a photo, like every other page's. It was a flat
			// tinted band, which left the hub as the one page in the site whose
			// opening carries no imagery — and read as an unstyled strip rather
			// than as a hero.
			wptpl_cover(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline'     => 'Blog headline',
							'intro'        => wptpl_lorem_len( 125 ),
							'headingLevel' => 1,
							'className'    => 'wptpl-header-wide',
							'textColor'    => 'base',
						)
					),
				),
				'hero',
				'secondary',
				55,
				'base',
				'var(--wptpl-container-wide)'
			),

			// Tinted, like the reference's. The pills are outlined and lettered in
			// a light color — on the page background their borders all but
			// vanish and only the active pill stays legible.
			wptpl_section(
				array(
					wptpl_block( 'category-filter' ),
				),
				'primary',
				'container',
				'wptpl-section-sm',
				'base'
			),

			// The featured post and the grid below it are the two halves of the
			// hub, so they take different backgrounds. Left both untinted they
			// run together as one long white page and the featured slot stops
			// reading as featured.
			//
			// `wptpl-featured-section` is not decoration: assets/js/blog-filter.js
			// looks the band up by that class to hide it whenever a category is
			// selected. Without it the query returns null, the band never hides,
			// and the featured post shows on every filter alongside its own grid
			// card.
			wptpl_section(
				array(
					wptpl_block( 'featured-post' ),
				),
				'muted',
				'container-md',
				'wptpl-featured-section',
				'base'
			),

			// Post grid only. A static row of guide cards used to sit under it,
			// which read as three more posts that never updated.
			wptpl_section(
				array(
					// No top margin: nothing sits above the grid in this band, so a
					// margin here only adds to the band's own 100px and pushes the
					// first row of cards off the rhythm every other section keeps.
					wptpl_block( 'post-grid', array( 'count' => 9 ) ),
				),
				'',
				'container'
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					// The reference runs the blog’s closing banner as a headline and a
					// button, like About’s.
					'text'     => WPTPL_BLANK,
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
 * Meet the Team: a hero, the roster grid, one bio per person, the closing CTA.
 *
 * The slots are slots, not people. Six of them, because that is the roster this
 * page was built against; adding or removing one is a line in the list below.
 *
 * The page is built twice over, deliberately. What the markup says is: a grid of
 * cards, then six full bio sections stacked underneath, each anchored, and the
 * cards link down to them. That is the entire page with JavaScript off, and it
 * is what a crawler reads — nothing is fetched and nothing is hidden from the
 * document. assets/js/bio-modal.js then promotes each bio section into a native
 * <dialog>, so what a visitor gets is the grid, with a bio opening on demand.
 *
 * Both halves earn their place. Choosing a therapist means comparing them, and
 * six full bios stacked down a page can only be compared from memory — that is
 * what the grid fixes. But a bio here runs four or five paragraphs, which is not
 * something a card body can hold and not something worth cutting: it is the
 * material the choice is actually made on. Grid to compare, dialog to read.
 *
 * The bios keep their alternating portrait side and tint. Invisible inside a
 * dialog, which shows one person at a time, but the no-JS layout stacks them and
 * there the alternation is what marks the seam between one person and the next.
 *
 * Heading order matters here and is easy to get wrong: the page carries exactly
 * one H1 (the hero), the bio names are H2 and the card titles H3. The credential
 * line under a name is a styled paragraph, NOT a subheading — it qualifies the
 * name, it does not open a new section, and marking it up as a heading corrupts
 * the document outline that screen readers and search engines both read.
 *
 * Every bio is anchored (`#therapist-N`). With the dialogs live that anchor is
 * still the address: bio-modal.js opens the matching dialog on load and pushes
 * the hash when one is opened, so a bio stays linkable and Back still closes it.
 */
function wptpl_seed_page_therapists(): string {
	// The roster. Order is the hierarchy the page presents: whoever runs the
	// practice opens it, trainees close it. Each entry is one band.
	$roster = array(
		'Credentials | Founder &amp; CEO',
		'Credentials | Therapist',
		'Credentials | Therapist',
		'Credentials | Therapist',
		'Credentials | Therapist',
		'Credentials | Therapist intern',
	);

	// A roster card. This is the index: the thing a visitor scans and compares
	// before committing to reading anybody. It carries an excerpt, not the bio —
	// the bio is behind the link, in the dialog.
	$card = static function ( int $n, string $role ): string {
		return wptpl_block(
			'feature-card',
			array(
				'eyebrow'  => $role,
				'title'    => 'Therapist ' . $n,
				'text'     => wptpl_lorem_len( 150 ),
				'imageUrl' => get_template_directory_uri() . '/assets/placeholders/portrait.jpg',
				'imageAlt' => 'Therapist ' . $n,
				// Specialties as pills, so the roster can be scanned by need
				// ("who treats this?") rather than only by name. The trailing
				// period goes: wptpl_lorem_len() always closes a sentence, and a
				// pill is a label, not one.
				'tags'     => array(
					rtrim( wptpl_lorem_len( 12 ), '.' ),
					rtrim( wptpl_lorem_len( 16 ), '.' ),
				),
				// An in-page anchor, not a URL to nowhere. bio-modal.js turns it
				// into the dialog opener; with JS off it stays what it looks
				// like — a jump to that person's bio further down the page.
				'ctaText'  => 'Read full bio',
				'ctaUrl'   => '#therapist-' . $n,
				'ctaStyle' => 'arrow',
			)
		);
	};

	// One person: a portrait beside the name, the credential line and the bio.
	//
	// The portrait does NOT carry `wptpl-portrait-fill` the way the home page's
	// single bio does. That class stretches the image to its column's height,
	// which works when the copy beside it is a few paragraphs; against a bio this
	// long it would render a five-foot-tall portrait. Fixed width, top-aligned,
	// natural aspect ratio.
	$bio = static function ( int $n, string $role, bool $image_left ): string {
		$name = 'Therapist ' . $n;

		// Real alt text, not the empty string the decorative placeholders use.
		// These portraits ARE the content of the page.
		$portrait = wptpl_image( 'portrait', 'is-style-rounded-square', '440px', '', $name );

		$copy = array(
			wptpl_heading( $name, 2 ),
			// A paragraph, not a heading — see the note on the function. The
			// inline top margin keeps it tucked under the name; `wptpl-bio-copy`
			// would otherwise push it 1.75rem down and break the pairing.
			wptpl_paragraph(
				$role,
				'',
				'',
				array(
					'fontSize'   => '1.125rem',
					'fontWeight' => '700',
				),
				'8px'
			),
			wptpl_paragraph( wptpl_lorem_len( 380 ) ),
			wptpl_paragraph( wptpl_lorem_len( 430 ) ),
			wptpl_paragraph( wptpl_lorem_len( 350 ) ),
			wptpl_paragraph( wptpl_lorem_len( 300 ) ),
			// The action belongs with the person, not only at the foot of the
			// page. Someone who has just finished reading one bio has decided;
			// making them scroll back out to act is where that decision is lost.
			wptpl_html( '<div style="margin-top:2rem"><a class="wptpl-btn-primary" href="/contact/">Book with this therapist</a></div>' ),
		);

		$columns     = $image_left ? array( array( $portrait ), $copy ) : array( $copy, array( $portrait ) );
		$widths      = $image_left ? array( '440px', '' ) : array( '', '440px' );
		$col_classes = $image_left ? array( '', 'wptpl-bio-copy' ) : array( 'wptpl-bio-copy', '' );

		return wptpl_section(
			array(
				wptpl_columns(
					$columns,
					$widths,
					'',
					'',
					false,
					$col_classes,
					'split',
					'',
					// Top, not centre. The copy column is several times the
					// portrait's height, and centring floats the face into the
					// middle of the bio, away from the name it belongs to.
					array(
						'row' => 'top',
						0     => 'top',
						1     => 'top',
					)
				),
			),
			// Every other band tinted — the seam between one person and the next
			// when these are stacked in the page, which is the no-JS layout. The
			// dialog stylesheet drops the tint, since inside a modal there is
			// nothing to separate the band from.
			0 === $n % 2 ? 'surface' : '',
			'container-md',
			// The hook bio-modal.js looks for. Everything else about the band is
			// unchanged — it is a real section that happens to get promoted.
			'wptpl-bio-modal',
			'',
			'therapist-' . $n
		);
	};

	$bands = array(
		// 1. Hero. A photo band with a dark wash, like the services hero, and the
		// page's only H1.
		wptpl_block(
			'hero',
			array(
				'eyebrow'            => 'Team',
				'title'              => 'Meet the team',
				'subtitle'           => wptpl_lorem_len( 155 ),
				'alignment'          => 'center',
				'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
				'overlayOpacity'     => 0.6,
				'className'          => 'wptpl-hero-dark',
				'ctaText'            => 'Primary CTA',
				'ctaUrl'            => '/contact/',
			)
		),

		// 2. The roster grid. Six cards in one grid, not two rows of three — two
		// column rows size themselves independently, so the second bank's cards
		// came out taller than the first's.
		//
		// This is the page's index and the reason the bios moved into dialogs:
		// choosing a therapist means comparing them, and six full bios stacked
		// down a page can only be compared from memory. Here they sit side by
		// side, and the bio opens on demand.
		wptpl_section(
			array(
				wptpl_block(
					'section-header',
					array(
						'eyebrow'  => 'Counseling team',
						'headline' => wptpl_lorem_len( 40 ),
						'intro'    => wptpl_lorem_len( 150 ),
					)
				),
				wptpl_columns(
					array(
						array( $card( 1, $roster[0] ) ),
						array( $card( 2, $roster[1] ) ),
						array( $card( 3, $roster[2] ) ),
						array( $card( 4, $roster[3] ) ),
						array( $card( 5, $roster[4] ) ),
						array( $card( 6, $roster[5] ) ),
					),
					array(),
					'wptpl-services-carousel wptpl-card-grid',
					// A section-header's own bottom margin is sized for a
					// paragraph, not for a grid of cards.
					'3rem'
				),
			)
		),
	);

	// 3-8. One band per person, portrait side alternating.
	foreach ( $roster as $index => $role ) {
		$bands[] = $bio( $index + 1, $role, 0 === $index % 2 );
	}

	// 9. Closing CTA.
	$bands[] = wptpl_block(
		'cta-banner',
		array(
			'headline' => wptpl_lorem_len( 52 ),
			'text'     => wptpl_lorem_len( 96 ),
			'ctaText'  => 'Primary CTA',
			'ctaUrl'   => '/contact/',
			'theme'    => 'dark',
		)
	);

	return implode( "\n\n", $bands );
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
				'text'     => wptpl_lorem_len( 56 ),
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
					'subtitle'           => wptpl_lorem_len( 125 ),
					// A photo band with a dark wash, like the services hero. These
					// were the only heroes on the site carrying no imagery, which
					// left four pages opening on a bare strip of text.
					'alignment'          => 'center',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
					'overlayOpacity'     => 0.6,
					'className'          => 'wptpl-hero-dark',
					'ctaText'   => $cta,
					'ctaUrl'    => '/contact/',
				)
			),
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							// Centred, like every other lone section-header on the
							// site. Left-aligned in a 760px column it sat off to one
							// side of a band with nothing to balance it.
							'headline' => $body_head,
							'intro'    => wptpl_lorem_len( 233 ),
						)
					),
				),
				'',
				'container-narrow'
			),
			wptpl_section(
				array(
					// Same grid as every other card row on the site, so the three
					// share one height however unevenly their copy runs.
					wptpl_columns(
						array(
							array( $card( 1 ) ),
							array( $card( 2 ) ),
							array( $card( 3 ) ),
						),
						array(),
						'wptpl-services-carousel wptpl-card-grid'
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
							'text'  => wptpl_lorem_len( 56 ),
						),
						array(
							'title' => 'Step two',
							'text'  => wptpl_lorem_len( 56 ),
						),
						array(
							'title' => 'Step three',
							'text'  => wptpl_lorem_len( 56 ),
						),
					),
					// 0.7, the reference's value on every steps band but the home
					// page's. The wash is what keeps the background from competing
					// with the copy — and with a wireframe placeholder standing in
					// for the photo, a lighter wash lets its lettering read
					// straight through the cards.
					'overlayOpacity'  => 0.7,
					'usePlaceholder'  => true,
				)
			),
			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem_len( 56 ),
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
		$blocks[] = wptpl_paragraph( wptpl_lorem_len( 233 ) );
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
		$blocks[] = wptpl_paragraph( wptpl_lorem_len( 233 ) );
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
	// The slug stays `meet-our-therapists` on purpose. Retitling a page is a
	// content edit; changing its slug would orphan the live URL and retire the
	// old page, which is a migration, not a redesign.
	$ids['therapists'] = wptpl_seed_page(
		array(
			'slug'    => 'meet-our-therapists',
			'title'   => 'Meet the Team',
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
			'title'   => 'Meet the Team',
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
