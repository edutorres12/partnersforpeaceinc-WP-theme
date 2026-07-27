<?php
/**
 * Customizer fields: header CTA + footer practice info.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_panel(
			'soywd_global',
			array(
				'title'    => __( 'Soy Web Development', 'soywd' ),
				'priority' => 30,
			)
		);

		// Header & CTA section.
		$wp_customize->add_section(
			'soywd_header',
			array(
				'title' => __( 'Header & CTA', 'soywd' ),
				'panel' => 'soywd_global',
			)
		);

		$wp_customize->add_setting(
			'soywd_primary_cta_text',
			array(
				'default'           => __( 'Book a free consultation', 'soywd' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'soywd_primary_cta_text',
			array(
				'label'   => __( 'Primary CTA text', 'soywd' ),
				'section' => 'soywd_header',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'soywd_primary_cta_url',
			array(
				'default'           => '#book',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'soywd_primary_cta_url',
			array(
				'label'   => __( 'Primary CTA URL', 'soywd' ),
				'section' => 'soywd_header',
				'type'    => 'url',
			)
		);

		// Footer / practice info section.
		$wp_customize->add_section(
			'soywd_footer',
			array(
				'title' => __( 'Footer / Practice info', 'soywd' ),
				'panel' => 'soywd_global',
			)
		);

		$footer_fields = array(
			'practice_name'      => array(
				'label'   => __( 'Practice name', 'soywd' ),
				'default' => __( 'Seasons of You Therapy', 'soywd' ),
				'type'    => 'text',
			),
			'practitioner'       => array(
				'label'   => __( 'Practitioner + credentials', 'soywd' ),
				'default' => __( 'Helia Ziaee, LMFT', 'soywd' ),
				'type'    => 'text',
			),
			'license'            => array(
				'label'   => __( 'License number', 'soywd' ),
				'default' => __( 'LMFT #103036', 'soywd' ),
				'type'    => 'text',
			),
			'hours'              => array(
				'label'   => __( 'Hours', 'soywd' ),
				'default' => __( 'Monday – Friday', 'soywd' ),
				'type'    => 'textarea',
			),
			'modality'           => array(
				'label'   => __( 'Modality', 'soywd' ),
				'default' => __( 'CBT, ACT, IBCT, MBCT', 'soywd' ),
				'type'    => 'text',
			),
			'languages'          => array(
				'label'   => __( 'Languages line (rendered as standalone eyebrow)', 'soywd' ),
				'default' => __( 'Sessions in English and Farsi', 'soywd' ),
				'type'    => 'text',
			),
			'crisis_text'        => array(
				'label'   => __( 'Crisis disclaimer', 'soywd' ),
				'default' => __( 'If you are in crisis, call or text 988 or visit our Crisis Resources page.', 'soywd' ),
				'type'    => 'textarea',
			),
		);

		foreach ( $footer_fields as $key => $cfg ) {
			$setting = 'soywd_' . $key;
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
					'section' => 'soywd_footer',
					'type'    => $cfg['type'],
				)
			);
		}
	}
);

/**
 * Convenience accessor with the same defaults as the Customizer registration.
 */
function soywd_setting( string $key, string $default = '' ): string {
	$value = get_theme_mod( 'soywd_' . $key, $default );
	return is_string( $value ) ? $value : $default;
}
