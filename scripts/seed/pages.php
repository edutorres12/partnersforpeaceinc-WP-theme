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
 * The seven service subpages. Generic on purpose — the template ships slots,
 * not a service catalogue.
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
// Fees
// ---------------------------------------------------------------------------

/**
 * Fees: hero, private-pay intro, payment methods, insurance, superbill,
 * sliding scale, Good Faith Estimate notice, closing CTA.
 */
function wptpl_seed_page_fees(): string {
	$text_section = static function ( string $headline, string $eyebrow = '' ) {
		return wptpl_section(
			array(
				wptpl_block(
					'section-header',
					array(
						'eyebrow'   => $eyebrow,
						'headline'  => $headline,
						'intro'     => wptpl_lorem( 'long' ),
						'alignment' => 'left',
					)
				),
			),
			'',
			'container-narrow'
		);
	};

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'eyebrow'   => 'Fees',
					'title'     => 'Fees headline',
					'subtitle'  => wptpl_lorem( 'medium' ),
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => 'Primary CTA',
					'ctaUrl'    => '/contact/',
				)
			),

			$text_section( 'Session rates' ),

			// Payment methods beside an image.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'service-card' ) ),
							array(
								wptpl_heading( 'Payment methods', 2 ),
								wptpl_block(
									'checklist',
									array(
										'items' => array(
											array( 'text' => 'Payment method one' ),
											array( 'text' => 'Payment method two' ),
											array( 'text' => 'Payment method three' ),
										),
									)
								),
							),
						),
						array( '40%', '60%' )
					),
				),
				'surface'
			),

			// Insurance beside an image.
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'guide-card' ) ),
							array(
								wptpl_heading( 'Insurance', 2 ),
								wptpl_paragraph( wptpl_lorem( 'long' ) ),
							),
						),
						array( '40%', '60%' )
					),
				)
			),

			$text_section( 'Superbills' ),
			$text_section( 'Sliding scale' ),

			// Good Faith Estimate notice.
			wptpl_section(
				array(
					wptpl_heading( 'Good Faith Estimate', 3 ),
					wptpl_paragraph( wptpl_lorem( 'medium' ) . ' <a href="/terms/">Read more</a>.' ),
				),
				'surface',
				'container-narrow'
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline'         => 'The first conversation is free',
					'text'             => wptpl_lorem( 'short' ),
					'ctaText'          => 'Primary CTA',
					'ctaUrl'           => '/contact/',
					'secondaryCtaText' => 'Secondary CTA',
					'secondaryCtaUrl'  => '/services/',
					'theme'            => 'dark',
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
// Crisis resources
// ---------------------------------------------------------------------------

/**
 * Crisis resources: intro, three emergency cards, non-emergency support, note.
 *
 * Numbers and service names are placeholders — a live site must replace them
 * with the real hotlines for its region before publishing.
 */
function wptpl_seed_page_crisis(): string {
	$card = static function ( string $title ) {
		return wptpl_block(
			'feature-card',
			array(
				'title'    => $title,
				'text'     => wptpl_lorem( 'short' ),
				'centered' => true,
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
							'eyebrow'      => 'Crisis resources',
							'headline'     => 'If you are in crisis',
							'intro'        => wptpl_lorem( 'medium' ),
							'headingLevel' => 1,
						)
					),
				),
				'',
				'container-narrow'
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'Immediate help' )
					),
					wptpl_columns(
						array(
							array( $card( 'Resource one' ) ),
							array( $card( 'Resource two' ) ),
							array( $card( 'Resource three' ) ),
						)
					),
					wptpl_paragraph( '<em>Replace these cards with the real emergency numbers for your region before publishing.</em>', '', 'center' ),
				),
				'surface'
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'Non-emergency support',
							'intro'    => wptpl_lorem( 'medium' ),
						)
					),
				),
				'',
				'container-narrow'
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'A note on this practice',
							'intro'    => wptpl_lorem( 'medium' ),
						)
					),
				),
				'surface',
				'container-narrow'
			),
		)
	);
}

// ---------------------------------------------------------------------------
// Resources + guide funnel
// ---------------------------------------------------------------------------

/**
 * Resources hub: hero, downloadable guide cards, blog pointer, crisis pointer, CTA.
 */
function wptpl_seed_page_resources(): string {
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
				'className' => 'wptpl-image-pop',
			)
		);
	};

	return implode(
		"\n\n",
		array(
			wptpl_block(
				'hero',
				array(
					'eyebrow'   => 'Resources',
					'title'     => 'Resources headline',
					'subtitle'  => wptpl_lorem( 'medium' ),
					'layout'    => 'centered',
					'alignment' => 'center',
					'ctaText'   => '',
					'ctaUrl'    => '',
				)
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'Free guides',
							'intro'    => wptpl_lorem( 'short' ),
						)
					),
					wptpl_columns(
						array(
							array( $guide( 1 ) ),
							array( $guide( 2 ) ),
							array( $guide( 3 ) ),
						)
					),
				)
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'From the blog',
							'intro'    => wptpl_lorem( 'short' ) . ' <a href="/blog/">Read the blog</a>.',
						)
					),
				),
				'surface',
				'container-narrow'
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'Crisis support',
							'intro'    => wptpl_lorem( 'short' ) . ' <a href="/crisis-resources/">Crisis resources</a>.',
						)
					),
				),
				'',
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

/**
 * Guide landing: opt-in beside the pitch, what's inside, who it's for, author,
 * download CTA, related service, crisis pointer.
 */
function wptpl_seed_page_guide_landing(): string {
	return implode(
		"\n\n",
		array(
			wptpl_section(
				array(
					wptpl_columns(
						array(
							array(
								wptpl_heading( 'Free guide headline', 1 ),
								wptpl_paragraph( wptpl_lorem( 'medium' ) ),
								wptpl_block(
									'checklist',
									array(
										'items' => array(
											array( 'text' => 'Benefit one' ),
											array( 'text' => 'Benefit two' ),
											array( 'text' => 'Benefit three' ),
											array( 'text' => 'Benefit four' ),
										),
									)
								),
							),
							array(
								wptpl_wrap(
									'group',
									array( 'className' => 'wptpl-form' ),
									'<div class="wp-block-group wptpl-form">',
									'</div>',
									array(
										wptpl_heading( 'Get the guide', 2 ),
										wptpl_paragraph( 'Replace this block with the email-capture form. Fields: name and email.' ),
									)
								),
							),
						),
						array( '60%', '40%' )
					),
				),
				'surface'
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => "What's inside" )
					),
					wptpl_columns(
						array(
							array(
								wptpl_heading( 'Part one', 3 ),
								wptpl_paragraph( wptpl_lorem( 'short' ) ),
							),
							array(
								wptpl_heading( 'Part two', 3 ),
								wptpl_paragraph( wptpl_lorem( 'short' ) ),
							),
							array(
								wptpl_heading( 'Part three', 3 ),
								wptpl_paragraph( wptpl_lorem( 'short' ) ),
							),
						)
					),
				)
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array( 'headline' => 'This guide is for you if…' )
					),
					wptpl_block(
						'checklist',
						array(
							'items' => array(
								array( 'text' => 'Reason one' ),
								array( 'text' => 'Reason two' ),
								array( 'text' => 'Reason three' ),
								array( 'text' => 'Reason four' ),
							),
						)
					),
				),
				'surface',
				'container-narrow'
			),

			wptpl_section(
				array(
					wptpl_columns(
						array(
							array( wptpl_image( 'portrait' ) ),
							array(
								wptpl_heading( 'About the author', 2 ),
								wptpl_paragraph( wptpl_lorem( 'medium' ) ),
							),
						),
						array( '30%', '70%' )
					),
				)
			),

			wptpl_block(
				'cta-banner',
				array(
					'headline' => 'Download the guide',
					'text'     => wptpl_lorem( 'short' ),
					'ctaText'  => 'Primary CTA',
					'ctaUrl'   => '#guide-form',
					'theme'    => 'dark',
				)
			),

			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'headline' => 'Working together',
							'intro'    => wptpl_lorem( 'short' ) . ' <a href="/services/">See services</a>.',
						)
					),
				),
				'',
				'container-narrow'
			),
		)
	);
}

/**
 * Guide thank-you: confirmation, download button, what happens next, CTA.
 */
function wptpl_seed_page_guide_thanks(): string {
	return implode(
		"\n\n",
		array(
			wptpl_section(
				array(
					wptpl_block(
						'section-header',
						array(
							'eyebrow'      => 'Thank you',
							'headline'     => 'Your guide is on its way',
							'intro'        => wptpl_lorem( 'medium' ),
							'headingLevel' => 1,
						)
					),
				),
				'',
				'container-narrow'
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
					'ctaUrl'   => '/contact/',
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
// Legal
// ---------------------------------------------------------------------------

/**
 * A long-form legal page: title, last-updated line, intro, then N sections.
 *
 * @param string             $title    Page title.
 * @param array<int, string> $sections Section headings.
 */
function wptpl_seed_page_legal( string $title, array $sections ): string {
	$blocks = array(
		wptpl_heading( $title, 1 ),
		wptpl_paragraph( '<em>Last updated: replace with a date.</em>' ),
		wptpl_paragraph( wptpl_lorem( 'long' ) ),
	);

	foreach ( $sections as $section ) {
		$blocks[] = wptpl_heading( $section, 2 );
		$blocks[] = wptpl_paragraph( wptpl_lorem( 'long' ) );
	}

	$blocks[] = wptpl_paragraph( '<em>This is placeholder text, not legal advice. Replace every section with copy reviewed for your jurisdiction before publishing.</em>' );

	return wptpl_section( $blocks, '', 'container-narrow' );
}

// ---------------------------------------------------------------------------
// Orchestration
// ---------------------------------------------------------------------------

/**
 * Seed every page. Returns a map of key => page ID.
 *
 * @return array<string, int>
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
			'slug'    => 'about',
			'title'   => 'About',
			'content' => wptpl_seed_page_about(),
			'order'   => 1,
		)
	);

	$ids['services'] = wptpl_seed_page(
		array(
			'slug'    => 'services',
			'title'   => 'Services',
			'content' => wptpl_seed_page_services(),
			'order'   => 2,
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

	$ids['resources'] = wptpl_seed_page(
		array(
			'slug'    => 'resources',
			'title'   => 'Resources',
			'content' => wptpl_seed_page_resources(),
			'order'   => 3,
		)
	);

	$ids['blog'] = wptpl_seed_page(
		array(
			'slug'    => 'blog',
			'title'   => 'Blog',
			'content' => wptpl_seed_page_blog(),
			'order'   => 4,
		)
	);

	$ids['fees'] = wptpl_seed_page(
		array(
			'slug'    => 'fees',
			'title'   => 'Fees',
			'content' => wptpl_seed_page_fees(),
			'order'   => 5,
		)
	);

	$ids['contact'] = wptpl_seed_page(
		array(
			'slug'    => 'contact',
			'title'   => 'Contact',
			'content' => wptpl_seed_page_contact(),
			'order'   => 6,
		)
	);

	$ids['crisis'] = wptpl_seed_page(
		array(
			'slug'    => 'crisis-resources',
			'title'   => 'Crisis Resources',
			'content' => wptpl_seed_page_crisis(),
			'order'   => 7,
		)
	);

	$ids['guide_landing'] = wptpl_seed_page(
		array(
			'slug'    => 'guide-landing',
			'title'   => 'Free Guide',
			'content' => wptpl_seed_page_guide_landing(),
			'order'   => 8,
		)
	);

	$ids['guide_thanks'] = wptpl_seed_page(
		array(
			'slug'    => 'guide-thank-you',
			'title'   => 'Thank You',
			'content' => wptpl_seed_page_guide_thanks(),
			'order'   => 9,
		)
	);

	$ids['privacy'] = wptpl_seed_page(
		array(
			'slug'    => 'privacy',
			'title'   => 'Privacy Policy',
			'content' => wptpl_seed_page_legal(
				'Privacy Policy',
				array(
					'Information we collect',
					'Third-party service providers',
					'Cookies and tracking technologies',
					'Website data vs. clinical records',
					'Your privacy rights',
					'Data retention',
					"Children's privacy",
					'Links to other websites',
					'Changes to this policy',
					'Contact',
				)
			),
			'order'   => 10,
		)
	);

	$ids['terms'] = wptpl_seed_page(
		array(
			'slug'    => 'terms',
			'title'   => 'Terms of Service',
			'content' => wptpl_seed_page_legal(
				'Terms of Service',
				array(
					'Use of this website',
					'No therapeutic relationship',
					'Not for emergencies',
					'Scheduling and cancellation',
					'Fees and payment',
					'Good Faith Estimate',
					'Intellectual property',
					'Limitation of liability',
					'Changes to these terms',
					'Contact',
				)
			),
			'order'   => 11,
		)
	);

	$ids['accessibility'] = wptpl_seed_page(
		array(
			'slug'    => 'accessibility',
			'title'   => 'Accessibility',
			'content' => wptpl_seed_page_legal(
				'Accessibility',
				array(
					'Our commitment',
					'Conformance status',
					'Measures we take',
					'Known limitations',
					'Feedback',
				)
			),
			'order'   => 12,
		)
	);

	return $ids;
}

/**
 * Seed the three menus and assign them to their theme locations.
 *
 * @param array<string, mixed> $ids Page IDs from wptpl_seed_all_pages().
 */
function wptpl_seed_all_menus( array $ids ): void {
	$primary = array(
		array(
			'key'     => 'about',
			'title'   => 'About',
			'page_id' => $ids['about'],
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
		'title'   => 'Resources',
		'page_id' => $ids['resources'],
	);
	$primary[] = array(
		'title'   => 'Blog',
		'page_id' => $ids['blog'],
	);
	$primary[] = array(
		'title'   => 'Fees',
		'page_id' => $ids['fees'],
	);
	$primary[] = array(
		'title'   => 'Contact',
		'page_id' => $ids['contact'],
	);

	wptpl_seed_menu( 'Primary', 'primary', $primary );

	wptpl_seed_menu(
		'Footer Links',
		'footer',
		array(
			array(
				'title'   => 'About',
				'page_id' => $ids['about'],
			),
			array(
				'title'   => 'Services',
				'page_id' => $ids['services'],
			),
			array(
				'title'   => 'Resources',
				'page_id' => $ids['resources'],
			),
			array(
				'title'   => 'Blog',
				'page_id' => $ids['blog'],
			),
			array(
				'title'   => 'Fees',
				'page_id' => $ids['fees'],
			),
			array(
				'title'   => 'Contact',
				'page_id' => $ids['contact'],
			),
			array(
				'title'   => 'Crisis Resources',
				'page_id' => $ids['crisis'],
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
				'title'   => 'Terms',
				'page_id' => $ids['terms'],
			),
			array(
				'title'   => 'Accessibility',
				'page_id' => $ids['accessibility'],
			),
		)
	);
}
