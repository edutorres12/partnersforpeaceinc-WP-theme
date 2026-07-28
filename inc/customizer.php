<?php
/**
 * Customizer fields: header CTA + footer practice info.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_panel(
			'wptpl_global',
			array(
				'title'    => __( 'Theme Settings', 'wptpl' ),
				'priority' => 30,
			)
		);

		// Header & CTA section.
		$wp_customize->add_section(
			'wptpl_header',
			array(
				'title' => __( 'Header & CTA', 'wptpl' ),
				'panel' => 'wptpl_global',
			)
		);

		$wp_customize->add_setting(
			'wptpl_primary_cta_text',
			array(
				'default'           => __( 'Book a free consultation', 'wptpl' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wptpl_primary_cta_text',
			array(
				'label'   => __( 'Primary CTA text', 'wptpl' ),
				'section' => 'wptpl_header',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'wptpl_primary_cta_url',
			array(
				'default'           => '#book',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wptpl_primary_cta_url',
			array(
				'label'   => __( 'Primary CTA URL', 'wptpl' ),
				'section' => 'wptpl_header',
				'type'    => 'url',
			)
		);

		// Footer / practice info section.
		$wp_customize->add_section(
			'wptpl_footer',
			array(
				'title' => __( 'Footer / Practice info', 'wptpl' ),
				'panel' => 'wptpl_global',
			)
		);

		/*
		 * Defaults are neutral placeholders, not real copy. Every field is
		 * rendered by footer.php only when it holds a value, so the ones left
		 * empty here simply don't appear until a site fills them in.
		 */
		$footer_fields = array(
			'practice_name' => array(
				'label'   => __( 'Practice name', 'wptpl' ),
				'default' => __( 'Practice Name', 'wptpl' ),
				'type'    => 'text',
			),
			'practitioner'  => array(
				'label'   => __( 'Practitioner + credentials', 'wptpl' ),
				'default' => __( 'Practitioner Name', 'wptpl' ),
				'type'    => 'text',
			),
			'license'       => array(
				'label'   => __( 'License number', 'wptpl' ),
				'default' => '',
				'type'    => 'text',
			),
			'hours'         => array(
				'label'   => __( 'Hours', 'wptpl' ),
				'default' => __( 'Monday – Friday', 'wptpl' ),
				'type'    => 'textarea',
			),
			'modality'      => array(
				'label'   => __( 'Modality', 'wptpl' ),
				'default' => '',
				'type'    => 'text',
			),
			'languages'     => array(
				'label'   => __( 'Languages line (rendered as standalone eyebrow)', 'wptpl' ),
				'default' => '',
				'type'    => 'text',
			),
			'alert_text'    => array(
				'label'   => __( 'Alert bar message (leave empty to hide the bar)', 'wptpl' ),
				'default' => '',
				'type'    => 'textarea',
			),
		);

		foreach ( $footer_fields as $key => $cfg ) {
			$setting = 'wptpl_' . $key;
			$wp_customize->add_setting(
				$setting,
				array(
					'default'           => $cfg['default'],
					'sanitize_callback' => 'textarea' === $cfg['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$setting,
				array(
					'label'   => $cfg['label'],
					'section' => 'wptpl_footer',
					'type'    => $cfg['type'],
				)
			);
		}
	}
);

/**
 * Convenience accessor with the same defaults as the Customizer registration.
 */
function wptpl_setting( string $key, string $default = '' ): string {
	$value = get_theme_mod( 'wptpl_' . $key, $default );
	return is_string( $value ) ? $value : $default;
}
