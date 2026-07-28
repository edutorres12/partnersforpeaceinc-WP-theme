<?php
/**
 * Alert bar — site-wide notice rendered at the top of the footer.
 *
 * The copy comes from the Customizer `alert_text` setting and ships empty, so
 * the bar is invisible until a site writes a message. Each page can hide it via
 * a sidebar checkbox in the editor.
 *
 * @package wptpl
 */

defined( 'ABSPATH' ) || exit;

const WPTPL_TOPBAR_META = '_wptpl_show_topbar';

/**
 * Post types that expose the per-post visibility toggle.
 *
 * @return array<int, string>
 */
function wptpl_topbar_post_types(): array {
	return array( 'page', 'post' );
}

/**
 * Register the per-post visibility meta. Stored as a string ('1' / '0');
 * an empty value means "never saved" and is treated as visible so existing
 * pages keep the bar without needing to be re-saved.
 */
function wptpl_topbar_register_meta(): void {
	foreach ( wptpl_topbar_post_types() as $wptpl_type ) {
		register_post_meta(
			$wptpl_type,
			WPTPL_TOPBAR_META,
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( $wptpl_value ) {
					return (bool) $wptpl_value ? '1' : '0';
				},
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'wptpl_topbar_register_meta' );

/**
 * Register the editor sidebar meta box.
 */
function wptpl_topbar_add_meta_box(): void {
	add_meta_box(
		'wptpl_topbar',
		__( 'Alert bar', 'wptpl' ),
		'wptpl_topbar_render_meta_box',
		wptpl_topbar_post_types(),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'wptpl_topbar_add_meta_box' );

/**
 * Render the meta-box checkbox.
 *
 * @param WP_Post $post Post being edited.
 */
function wptpl_topbar_render_meta_box( $post ): void {
	$wptpl_raw  = get_post_meta( $post->ID, WPTPL_TOPBAR_META, true );
	$wptpl_show = ( '0' !== (string) $wptpl_raw );
	wp_nonce_field( 'wptpl_topbar_meta', 'wptpl_topbar_meta_nonce' );
	?>
	<p>
		<label>
			<input type="checkbox" name="wptpl_show_topbar" value="1" <?php checked( $wptpl_show ); ?> />
			<?php esc_html_e( 'Show the alert bar on this page', 'wptpl' ); ?>
		</label>
	</p>
	<p class="description" style="margin-top:6px">
		<?php esc_html_e( 'Uncheck to hide the bar here. The message text lives in Customizer → Footer / Practice info (Alert bar message).', 'wptpl' ); ?>
	</p>
	<?php
}

/**
 * Persist the meta-box choice on save.
 *
 * @param int $post_id Post being saved.
 */
function wptpl_topbar_save_meta( int $post_id ): void {
	if ( ! isset( $_POST['wptpl_topbar_meta_nonce'] ) ) {
		return;
	}
	$wptpl_nonce = sanitize_text_field( wp_unslash( $_POST['wptpl_topbar_meta_nonce'] ) );
	if ( ! wp_verify_nonce( $wptpl_nonce, 'wptpl_topbar_meta' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$wptpl_show = isset( $_POST['wptpl_show_topbar'] ) ? '1' : '0';
	update_post_meta( $post_id, WPTPL_TOPBAR_META, $wptpl_show );
}
add_action( 'save_post', 'wptpl_topbar_save_meta' );

/**
 * Decide whether to render the topbar in the current request.
 */
function wptpl_should_show_topbar(): bool {
	if ( is_singular( wptpl_topbar_post_types() ) ) {
		$wptpl_raw  = get_post_meta( get_queried_object_id(), WPTPL_TOPBAR_META, true );
		$wptpl_show = ( '0' !== (string) $wptpl_raw );
	} else {
		$wptpl_show = true;
	}
	return (bool) apply_filters( 'wptpl_should_show_topbar', $wptpl_show );
}

/**
 * Render the alert bar markup at the top of the footer.
 */
function wptpl_render_topbar(): void {
	if ( ! wptpl_should_show_topbar() ) {
		return;
	}
	$wptpl_alert_text = wptpl_setting( 'alert_text' );
	if ( '' === $wptpl_alert_text ) {
		return;
	}
	?>
	<div class="wptpl-topbar" role="region" aria-label="<?php esc_attr_e( 'Site notice', 'wptpl' ); ?>">
		<div class="wptpl-container flex items-center justify-center gap-2 py-2 text-sm text-center">
			<span><?php echo esc_html( $wptpl_alert_text ); ?></span>
		</div>
	</div>
	<?php
}
