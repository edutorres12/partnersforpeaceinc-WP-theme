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
 * The service subpages.
 *
 * Deliberately still generic: the site's sitemap groups services by theme rather
 * than by therapist, but which groupings survive is an open question on their
 * side. Renaming these encodes a decision nobody has made — the slots stay
 * numbered until the real list lands, and then it is one edit here.
 *
 * @return array<int, array{slug: string, title: string}>
 */
function wptpl_seed_services(): array {
	return array(
		array(
			'slug'  => 'service-one',
			'title' => 'Service One',
		),
		array(
			'slug'  => 'service-two',
			'title' => 'Service Two',
		),
		array(
			'slug'  => 'service-three',
			'title' => 'Service Three',
		),
		array(
			'slug'  => 'service-four',
			'title' => 'Service Four',
		),
		array(
			'slug'  => 'service-five',
			'title' => 'Service Five',
		),
		array(
			'slug'  => 'service-six',
			'title' => 'Service Six',
		),
		array(
			'slug'  => 'service-seven',
			'title' => 'Service Seven',
		),
	);
}

/**
 * Slugs the template used to own and no longer does.
 *
 * `wptpl_seed_prune()` moves these to the trash — never deletes them — so a
 * restructure does not leave orphans behind. Anything a site added on its own is
 * untouched: only pages carrying the `_wptpl_seeded` meta are eligible.
 *
 * @return array<int, string>
 */
function wptpl_seed_retired_slugs(): array {
	return array(
		// Sections the sitemap does not include.
		'resources',
		'fees',
		'crisis-resources',
		'guide-landing',
		'guide-thank-you',
		// Compliance pages are held back until the board signs off on their copy
		// (licensure, HIPAA, Good Faith Estimate, 911/988 disclosures).
		'privacy',
		'terms',
		'accessibility',
	);
}

// ---------------------------------------------------------------------------
// Home
// ---------------------------------------------------------------------------

/**
 * Home: hero, trust bar, three-card intro, six-card service grid, bio,
 * numbered steps, FAQ, closing CTA.
 */
function wptpl_seed_page_home(): string {
	$service_cards = array();
	foreach ( wptpl_seed_services() as $index => $service ) {
		$service_cards[] = wptpl_block(
			'feature-card',
			array(
				'eyebrow'  => 'Category tag',
				'title'    => $service['title'],
				'text'     => wptpl_lorem( 'short' ),
				'imageUrl' => get_template_directory_uri() . '/assets/placeholders/service-card.jpg',
				'ctaText'  => 'Learn more',
				'ctaUrl'   => '/services/' . $service['slug'] . '/',
				'ctaStyle' => 'arrow',
			)
		);
		if ( 5 === $index ) {
			break; // The grid shows six; the seventh lives on the Services page.
		}
	}

	return implode(
		"\n\n",
		array(
			// 1. Hero.
			wptpl_block(
				'hero',
				array(
					'eyebrow'          => 'Niche + location identifier',
					'title'            => 'Warm, hopeful headline',
					'subtitle'         => wptpl_lorem( 'medium' ),
					'ctaText'          => 'Primary CTA',
					'ctaUrl'           => '/contact/',
					'secondaryCtaText' => 'Secondary CTA',
					'secondaryCtaUrl'  => '/services/',
					'microcopy'        => 'Free 15-minute consultation · No commitment · Virtual sessions',
					'layout'           => 'split',
					'imageUrl'         => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
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
							'eyebrow'  => 'Validating subheading',
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
						)
					),
				),
				'surface'
			),

			// 4. Services grid — two rows of three.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'eyebrow'  => 'Services intro',
							'headline' => 'Services headline',
						)
					),
					wptpl_columns(
						array(
							array( $service_cards[0] ),
							array( $service_cards[1] ),
							array( $service_cards[2] ),
						)
					),
					wptpl_columns(
						array(
							array( $service_cards[3] ),
							array( $service_cards[4] ),
							array( $service_cards[5] ),
						)
					),
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
						)
					),
				),
				'surface',
				'container-narrow'
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
	$modalities = array();
	foreach ( range( 1, 4 ) as $n ) {
		$modalities[] = wptpl_heading( 'Approach ' . $n, 3 );
		$modalities[] = wptpl_paragraph( wptpl_lorem( 'medium' ) );
		if ( 4 !== $n ) {
			$modalities[] = wptpl_separator();
		}
	}

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'eyebrow'   => 'About',
					'title'     => 'About headline',
					'subtitle'  => 'Role line, credentials',
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => 'Primary CTA',
					'ctaUrl'    => '/contact/',
				)
			),

			// Pull quote.
			wptpl_section(
				array(
					wptpl_paragraph( '<em>' . wptpl_lorem( 'medium' ) . '</em>', '', 'center' ),
				),
				'surface',
				'container-narrow'
			),

			// Portrait + story.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'portrait', 'wptpl-portrait-fill' ) ),
							array(
								wptpl_heading( 'My story', 2 ),
								wptpl_paragraph( wptpl_lorem( 'long' ) ),
								wptpl_paragraph( wptpl_lorem( 'medium' ) ),
							),
						),
						array( '40%', '60%' )
					),
				)
			),

			// Approach.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'eyebrow'  => 'Approach',
							'headline' => 'My approach',
							'intro'    => wptpl_lorem( 'medium' ),
						)
					),
				),
				'surface',
				'container-narrow'
			),

			// Four modalities.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Evidence-based approaches' )
					),
					wptpl_wrap(
						'group',
						array(),
						'<div class="wp-block-group">',
						'</div>',
						$modalities
					),
				),
				'',
				'container-narrow'
			),

			// What sessions look like.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'What working together looks like',
							'intro'    => wptpl_lorem( 'medium' ),
						)
					),
				),
				'surface',
				'container-narrow'
			),

			// Who this is for — copy beside an image, three audience cards.
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Who this practice was built for' )
					),
					wptpl_columns(
						array(
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'     => 'Audience one',
										'text'      => wptpl_lorem( 'short' ),
										'centered'  => true,
										'className' => 'wptpl-title-sm',
									)
								),
							),
							array(
								wptpl_block(
									'feature-card',
									array(
										'title'     => 'Audience two',
										'text'      => wptpl_lorem( 'short' ),
										'centered'  => true,
										'className' => 'wptpl-title-sm',
									)
								),
							),
						)
					),
					wptpl_block(
						'feature-card',
						array(
							'title'             => 'Audience three',
							'text'              => wptpl_lorem( 'short' ),
							'centered'          => true,
							'halfWidthCentered' => true,
							'className'         => 'wptpl-title-sm',
						)
					),
				)
			),

			// Steps.
			wptpl_block(
				'steps',
				array(
					'heading' => 'How to get started',
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
					'showCta' => true,
					'ctaText' => 'Primary CTA',
					'ctaUrl'  => '/contact/',
				)
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
// Services index
// ---------------------------------------------------------------------------

/**
 * Services: hero over a photo, a six-card grid, one wide bilingual card, CTA.
 */
function wptpl_seed_page_services(): string {
	$services = wptpl_seed_services();
	$cards    = array();

	foreach ( array_slice( $services, 0, 6 ) as $service ) {
		$cards[] = wptpl_block(
			'feature-card',
			array(
				'title'    => $service['title'],
				'text'     => wptpl_lorem( 'short' ),
				'ctaText'  => 'Learn more',
				'ctaUrl'   => '/services/' . $service['slug'] . '/',
				'ctaStyle' => 'arrow',
			)
		);
	}

	$seventh = $services[6];

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'eyebrow'            => 'Services',
					'title'              => 'Services headline',
					'subtitle'           => wptpl_lorem( 'medium' ),
					'layout'             => 'centered',
					'alignment'          => 'center',
					'ctaText'            => 'Primary CTA',
					'ctaUrl'             => '/contact/',
					'backgroundImageUrl' => get_template_directory_uri() . '/assets/placeholders/hero.jpg',
					'className'          => 'wptpl-hero-dark',
				)
			),

			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( $cards[0] ),
							array( $cards[1] ),
							array( $cards[2] ),
						)
					),
					wptpl_columns(
						array(
							array( $cards[3] ),
							array( $cards[4] ),
							array( $cards[5] ),
						)
					),
				)
			),

			// Seventh service gets the wide bilingual treatment.
			wptpl_section(
				array(
					wptpl_block(
						'feature-card',
						array(
							'title'             => $seventh['title'],
							'text'              => wptpl_lorem( 'medium' ),
							'titleRight'        => $seventh['title'] . ' (second language)',
							'textRight'         => wptpl_lorem( 'medium' ),
							'layout'            => 'bilingual',
							'halfWidthCentered' => true,
							'ctaText'           => 'Learn more',
							'ctaUrl'            => '/services/' . $seventh['slug'] . '/',
							'ctaStyle'          => 'arrow',
						)
					),
				),
				'surface'
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

/**
 * A single service subpage. Same template for all seven.
 *
 * @param array{slug: string, title: string} $service  This service.
 * @param array<int, array{slug: string, title: string}> $siblings Related services.
 */
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
					'eyebrow'            => 'Service',
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
										'items'     => $checklist( 'Symptom' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
										'items'     => $checklist( 'Symptom' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
						),
						array( '24%', '38%', '38%' )
					),
				)
			),

			wptpl_section(
				array(
					wptpl_paragraph( wptpl_lorem( 'long' ) ),
				),
				'surface',
				'container-narrow'
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
										'items'     => $checklist( 'Reason' ),
										'className' => 'wptpl-checklist-tight',
									)
								),
							),
							array(
								wptpl_block(
									'checklist',
									array(
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
				'container-narrow'
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
				'surface',
				'container-narrow'
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
				'container-narrow'
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
					'usePlaceholder' => true,
				)
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Closing headline',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '/contact/',
					'theme'    => 'light',
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
	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'eyebrow'   => 'Contact',
					'title'     => 'Contact headline',
					'subtitle'  => wptpl_lorem( 'medium' ),
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => '',
					'ctaUrl'    => '',
				)
			),

			wptpl_section(
				array(
					wptpl_columns(
						array(
							array(
								wptpl_wrap(
									'group',
									array( 'className' => 'wptpl-form' ),
									'<div class="wp-block-group wptpl-form">',
									'</div>',
									array(
										wptpl_heading( 'Send a message', 2 ),
										wptpl_paragraph( 'Replace this block with the form plugin shortcode. The theme styles any form inside a <code>wptpl-form</code> wrapper.' ),
										wptpl_paragraph( 'Fields to reproduce: name, email, phone (optional), preferred language, preferred session type, and an open message field.' ),
									)
								),
							),
							array(
								wptpl_heading( 'Practice info', 2 ),
								wptpl_paragraph( '<strong>Location</strong><br>' . wptpl_lorem( 'short' ) ),
								wptpl_paragraph( '<strong>Hours</strong><br>Monday – Friday' ),
								wptpl_paragraph( '<strong>Response time</strong><br>' . wptpl_lorem( 'short' ) ),
							),
						),
						array( '62%', '38%' )
					),
				)
			),

			wptpl_block(
				'steps',
				array(
					'heading' => 'What happens next',
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
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '#book',
					'theme'    => 'dark',
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
							'eyebrow'      => 'Blog',
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
				'eyebrow'  => 'Credentials',
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
					'eyebrow'   => 'Our team',
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
 * @param string $eyebrow   Section eyebrow.
 * @param string $title     Page title.
 * @param string $body_head Heading for the body section.
 * @param string $card_noun Noun used for the three supporting cards.
 * @param string $cta       Label for the calls to action.
 */
function wptpl_seed_page_conversion( string $eyebrow, string $title, string $body_head, string $card_noun, string $cta ): string {
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
					'eyebrow'   => $eyebrow,
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
			'content' => wptpl_seed_page_conversion( 'For churches', 'Church partnerships', 'Partnering with your congregation', 'Partnership', 'Start a conversation' ),
			'order'   => 6,
		)
	);
	$ids['payment'] = wptpl_seed_page(
		array(
			'slug'    => 'payment',
			'title'   => 'Payment &amp; Insurance',
			'content' => wptpl_seed_page_conversion( 'Payment', 'Payment &amp; insurance', 'What sessions cost', 'Option', 'Ask about fees' ),
			'order'   => 7,
		)
	);
	$ids['donate'] = wptpl_seed_page(
		array(
			'slug'    => 'donate',
			'title'   => 'Donate',
			'content' => wptpl_seed_page_conversion( 'Support the ministry', 'Donate', 'Where your gift goes', 'Fund', 'Give today' ),
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

	return $ids;
}

/**
 * Seed the menus and assign them to their theme locations.
 *
 * There is no Footer Legal menu: the compliance pages are held back until the
 * board signs off on their copy, so the location stays empty and the footer
 * renders nothing for it.
 *
 * @param array<string, mixed> $ids Page IDs from wptpl_seed_all_pages().
 */
function wptpl_seed_all_menus( array $ids ): void {
	$primary = array(
		array(
			'title'   => 'Home',
			'page_id' => $ids['home'],
		),
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
}
