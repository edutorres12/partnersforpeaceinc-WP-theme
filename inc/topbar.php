<?php
/**
 * Crisis topbar — site-wide alert that sits above the header.
 *
 * The bar reuses the Customizer `crisis_text` setting (the same string the
 * footer renders) so the copy + 988 / Crisis Resources links live in one
 * place. Each page can hide the bar via a sidebar checkbox in the editor.
 *
 * @package soywd
 */

defined( 'ABSPATH' ) || exit;

const SOYWD_TOPBAR_META = '_soywd_show_topbar';

/**
 * Post types that expose the per-post visibility toggle.
 *
 * @return array<int, string>
 */
function soywd_topbar_post_types(): array {
	return array( 'page', 'post' );
}

/**
 * Register the per-post visibility meta. Stored as a string ('1' / '0');
 * an empty value means "never saved" and is treated as visible so existing
 * pages keep the bar without needing to be re-saved.
 */
function soywd_topbar_register_meta(): void {
	foreach ( soywd_topbar_post_types() as $soywd_type ) {
		register_post_meta(
			$soywd_type,
			SOYWD_TOPBAR_META,
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( $soywd_value ) {
					return (bool) $soywd_value ? '1' : '0';
				},
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'soywd_topbar_register_meta' );

/**
 * Register the editor sidebar meta box.
 */
function soywd_topbar_add_meta_box(): void {
	add_meta_box(
		'soywd_topbar',
		__( 'Crisis topbar', 'soywd' ),
		'soywd_topbar_render_meta_box',
		soywd_topbar_post_types(),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'soywd_topbar_add_meta_box' );

/**
 * Render the meta-box checkbox.
 *
 * @param WP_Post $post Post being edited.
 */
function soywd_topbar_render_meta_box( $post ): void {
	$soywd_raw  = get_post_meta( $post->ID, SOYWD_TOPBAR_META, true );
	$soywd_show = ( '0' !== (string) $soywd_raw );
	wp_nonce_field( 'soywd_topbar_meta', 'soywd_topbar_meta_nonce' );
	?>
	<p>
		<label>
			<input type="checkbox" name="soywd_show_topbar" value="1" <?php checked( $soywd_show ); ?> />
			<?php esc_html_e( 'Show the crisis topbar on this page', 'soywd' ); ?>
		</label>
	</p>
	<p class="description" style="margin-top:6px">
		<?php esc_html_e( 'Uncheck to hide the bar here. The message text + link live in Customizer → Footer / Practice info (Crisis disclaimer).', 'soywd' ); ?>
	</p>
	<?php
}

/**
 * Persist the meta-box choice on save.
 *
 * @param int $post_id Post being saved.
 */
function soywd_topbar_save_meta( int $post_id ): void {
	if ( ! isset( $_POST['soywd_topbar_meta_nonce'] ) ) {
		return;
	}
	$soywd_nonce = sanitize_text_field( wp_unslash( $_POST['soywd_topbar_meta_nonce'] ) );
	if ( ! wp_verify_nonce( $soywd_nonce, 'soywd_topbar_meta' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$soywd_show = isset( $_POST['soywd_show_topbar'] ) ? '1' : '0';
	update_post_meta( $post_id, SOYWD_TOPBAR_META, $soywd_show );
}
add_action( 'save_post', 'soywd_topbar_save_meta' );

/**
 * Decide whether to render the topbar in the current request.
 */
function soywd_should_show_topbar(): bool {
	if ( is_singular( soywd_topbar_post_types() ) ) {
		$soywd_raw  = get_post_meta( get_queried_object_id(), SOYWD_TOPBAR_META, true );
		$soywd_show = ( '0' !== (string) $soywd_raw );
	} else {
		$soywd_show = true;
	}
	return (bool) apply_filters( 'soywd_should_show_topbar', $soywd_show );
}

/**
 * Render the topbar markup above the site header.
 */
function soywd_render_topbar(): void {
	if ( ! soywd_should_show_topbar() ) {
		return;
	}
	$soywd_crisis_text = soywd_setting(
		'crisis_text',
		__( 'If you are in crisis, call or text 988 or visit our Crisis Resources page.', 'soywd' )
	);
	if ( '' === $soywd_crisis_text ) {
		return;
	}

	$soywd_crisis_page = get_page_by_path( 'crisis-resources' );
	$soywd_crisis_url  = $soywd_crisis_page ? get_permalink( $soywd_crisis_page ) : home_url( '/crisis-resources/' );

	// Build the link-rich HTML by escaping the raw text first, then upgrading
	// `988` and `Crisis Resources` into anchors — mirrors the footer pattern.
	$soywd_html = esc_html( $soywd_crisis_text );
	$soywd_html = preg_replace(
		'/\b988\b/',
		'<a href="tel:988">988</a>',
		$soywd_html
	);
	$soywd_html = preg_replace(
		'/Crisis Resources/i',
		'<a href="' . esc_url( $soywd_crisis_url ) . '">$0</a>',
		$soywd_html
	);
	?>
	<div class="soywd-topbar" role="region" aria-label="<?php esc_attr_e( 'Crisis resources', 'soywd' ); ?>">
		<div class="soywd-container flex items-center justify-center gap-2 py-2 text-sm text-center">
			<svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path d="M2.697 16.126c-.866 1.5.217 3.374 1.948 3.374h14.71c1.732 0 2.814-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" fill="#facc15" stroke="#000000" stroke-width="1.5" stroke-linejoin="round" />
				<path d="M12 9v3.75M12 15.75h.007v.008H12v-.008z" fill="none" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
			<span><?php echo $soywd_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</div>
	<?php
}
