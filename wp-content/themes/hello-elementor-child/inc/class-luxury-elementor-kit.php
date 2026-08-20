<?php
/**
 * Elementor Kit — Global Fonts & Colors Injection
 *
 * Programmatically writes the 33 Store design system into the active
 * Elementor Kit's global settings (system colors, custom colors and
 * global typography), so every Elementor widget inherits the luxury
 * monochrome tokens by default.
 *
 * Runs automatically on theme switch and on demand via the admin
 * page ("Appearance → 33 Store Design System") or `wp luxury apply`.
 *
 * @package 33StoreLuxury
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Luxury_Elementor_Kit
 */
final class Luxury_Elementor_Kit {

	/**
	 * Design system version marker (bump to force re-application).
	 */
	const SYSTEM_VERSION = '1.0.0';

	/**
	 * Hook everything up.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_page' ) );
		add_action( 'admin_post_luxury_apply_design_system', array( __CLASS__, 'handle_admin_apply' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
	}

	/**
	 * Register the admin settings page.
	 */
	public static function register_admin_page() {
		add_theme_page(
			__( '33 Store Design System', 'thirtythree-store' ),
			__( '33 Store Design System', 'thirtythree-store' ),
			'manage_options',
			'luxury-design-system',
			array( __CLASS__, 'render_admin_page' )
		);
	}

	/**
	 * Admin page assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function admin_assets( $hook ) {
		if ( 'appearance_page_luxury-design-system' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'luxury-admin', get_stylesheet_directory_uri() . '/assets/css/admin.css', array(), LUXURY_THEME_VERSION );
	}

	/**
	 * Handle the "Apply now" form submit.
	 */
	public static function handle_admin_apply() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'thirtythree-store' ) );
		}
		check_admin_referer( 'luxury_apply_design_system' );

		$result = self::apply();

		set_transient(
			'luxury_design_system_notice',
			$result ? 'applied' : 'skipped',
			30
		);

		wp_safe_redirect( admin_url( 'themes.php?page=luxury-design-system' ) );
		exit;
	}

	/**
	 * One-time admin notice after applying.
	 */
	public static function admin_notice() {
		$status = get_transient( 'luxury_design_system_notice' );
		if ( ! $status ) {
			return;
		}
		delete_transient( 'luxury_design_system_notice' );

		if ( 'applied' === $status ) {
			echo '<div class="notice notice-success is-dismissible"><p><strong>33 Store</strong> — ' . esc_html__( 'Luxury design system applied to the Elementor Kit.', 'thirtythree-store' ) . '</p></div>';
		} else {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>33 Store</strong> — ' . esc_html__( 'Elementor Kit not found. Activate Elementor and try again.', 'thirtythree-store' ) . '</p></div>';
		}
	}

	/**
	 * Render the admin page.
	 */
	public static function render_admin_page() {
		$kit_id  = (int) get_option( 'elementor_active_kit' );
		$applied = get_option( 'luxury_design_system_applied' );
		$el_active = did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
		?>
		<div class="wrap luxury-admin">
			<h1><?php esc_html_e( '33 Store — Luxury Monochrome Design System', 'thirtythree-store' ); ?></h1>

			<p class="luxury-admin__lead">
				<?php esc_html_e( 'Injects the global color palette and global typography into the active Elementor Kit so every Elementor widget inherits the luxury monochrome design.', 'thirtythree-store' ); ?>
			</p>

			<table class="widefat striped" style="max-width:720px;">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Elementor', 'thirtythree-store' ); ?></th>
						<td><?php echo $el_active ? '<span class="dashicons dashicons-yes-alt" style="color:#111;"></span> ' . esc_html__( 'Active', 'thirtythree-store' ) : '<span style="color:#a00;">' . esc_html__( 'Not active', 'thirtythree-store' ) . '</span>'; ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Active Kit ID', 'thirtythree-store' ); ?></th>
						<td><?php echo $kit_id ? (int) $kit_id : esc_html__( 'none', 'thirtythree-store' ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Last applied', 'thirtythree-store' ); ?></th>
						<td><?php echo $applied ? esc_html( gmdate( 'Y-m-d H:i:s', (int) $applied ) ) : esc_html__( 'never', 'thirtythree-store' ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'System version', 'thirtythree-store' ); ?></th>
						<td><?php echo esc_html( self::SYSTEM_VERSION ); ?></td>
					</tr>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'What gets applied', 'thirtythree-store' ); ?></h2>
			<ul style="list-style:disc; padding-left:20px; max-width:720px;">
				<li><strong><?php esc_html_e( 'Global colors', 'thirtythree-store' ); ?>:</strong> <?php esc_html_e( 'Primary #111111, Secondary #1A1A1A, Text #111111, Accent #111111.', 'thirtythree-store' ); ?></li>
				<li><strong><?php esc_html_e( 'Custom colors', 'thirtythree-store' ); ?>:</strong> <?php esc_html_e( 'Lux Black, Lux Charcoal, Lux Muted, Lux Light Gray, Lux Offwhite, Lux White.', 'thirtythree-store' ); ?></li>
				<li><strong><?php esc_html_e( 'Global typography', 'thirtythree-store' ); ?>:</strong> <?php esc_html_e( 'Lux Heading (Cormorant Garamond 500), Lux Body (Montserrat 300), Lux Accent (Montserrat 600, uppercase).', 'thirtythree-store' ); ?></li>
				<li><strong><?php esc_html_e( 'System typography', 'thirtythree-store' ); ?>:</strong> <?php esc_html_e( 'H1–H6 → Cormorant Garamond; body → Montserrat; buttons → Montserrat uppercase.', 'thirtythree-store' ); ?></li>
			</ul>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="luxury_apply_design_system" />
				<?php wp_nonce_field( 'luxury_apply_design_system' ); ?>
				<?php submit_button( __( 'Apply design system now', 'thirtythree-store' ), 'primary large', 'apply', true ); ?>
			</form>

			<p class="description" style="max-width:720px;">
				<?php esc_html_e( 'Tip: re-apply after changing themes or if the Kit settings were reset. Manual Elementor Kit settings (Site Identity, Layout) are preserved.', 'thirtythree-store' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Apply the design system to the active Elementor Kit.
	 *
	 * @return bool True when applied, false when no Kit is available.
	 */
	public static function apply() {
		$kit_id = (int) get_option( 'elementor_active_kit' );
		if ( ! $kit_id || ! get_post( $kit_id ) ) {
			return false;
		}

		$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		// Global (system) colors.
		$settings['system_colors'] = self::merge_system_colors(
			isset( $settings['system_colors'] ) ? (array) $settings['system_colors'] : array()
		);

		// Custom color swatches.
		$settings['custom_colors'] = self::merge_named_items(
			isset( $settings['custom_colors'] ) ? (array) $settings['custom_colors'] : array(),
			self::custom_colors()
		);

		// System typography (H1–H6, body, buttons).
		$settings['system_typography'] = self::merge_system_typography(
			isset( $settings['system_typography'] ) ? (array) $settings['system_typography'] : array()
		);

		// Global (custom) typography.
		$settings['custom_typography'] = self::merge_named_items(
			isset( $settings['custom_typography'] ) ? (array) $settings['custom_typography'] : array(),
			self::custom_typography()
		);

		update_post_meta( $kit_id, '_elementor_page_settings', $settings );
		update_option( 'luxury_design_system_applied', time() );
		update_option( 'luxury_design_system_version', self::SYSTEM_VERSION );

		return true;
	}

	/**
	 * System colors: overwrite the four core Elementor colors.
	 *
	 * @param array $current Existing system colors.
	 * @return array
	 */
	private static function merge_system_colors( $current ) {
		$design = array(
			'primary'   => array( 'id' => 'primary', 'title' => __( 'Primary', 'thirtythree-store' ), 'color' => '#111111' ),
			'secondary' => array( 'id' => 'secondary', 'title' => __( 'Secondary', 'thirtythree-store' ), 'color' => '#1A1A1A' ),
			'text'      => array( 'id' => 'text', 'title' => __( 'Text', 'thirtythree-store' ), 'color' => '#111111' ),
			'accent'    => array( 'id' => 'accent', 'title' => __( 'Accent', 'thirtythree-store' ), 'color' => '#111111' ),
		);

		$merged = array();
		foreach ( $current as $item ) {
			if ( ! is_array( $item ) || empty( $item['id'] ) ) {
				continue;
			}
			if ( isset( $design[ $item['id'] ] ) ) {
				$merged[] = $design[ $item['id'] ];
				unset( $design[ $item['id'] ] );
			} else {
				$merged[] = $item;
			}
		}

		return array_merge( $merged, array_values( $design ) );
	}

	/**
	 * Custom color swatches.
	 *
	 * @return array
	 */
	private static function custom_colors() {
		return array(
			array(
				'id'    => 'lux_black',
				'title' => 'Lux Black',
				'color' => '#111111',
			),
			array(
				'id'    => 'lux_charcoal',
				'title' => 'Lux Charcoal',
				'color' => '#1A1A1A',
			),
			array(
				'id'    => 'lux_muted',
				'title' => 'Lux Muted',
				'color' => '#555555',
			),
			array(
				'id'    => 'lux_light_gray',
				'title' => 'Lux Light Gray',
				'color' => '#E5E5E5',
			),
			array(
				'id'    => 'lux_offwhite',
				'title' => 'Lux Offwhite',
				'color' => '#F9F8F6',
			),
			array(
				'id'    => 'lux_white',
				'title' => 'Lux White',
				'color' => '#FFFFFF',
			),
		);
	}

	/**
	 * System typography: headings, body and buttons.
	 *
	 * @param array $current Existing system typography.
	 * @return array
	 */
	private static function merge_system_typography( $current ) {
		$body = array(
			'font_family'    => 'Montserrat',
			'font_size'      => array( 'unit' => 'px', 'size' => 15 ),
			'font_weight'    => '300',
			'text_transform' => 'none',
			'font_style'     => 'normal',
			'text_decoration' => 'none',
			'line_height'    => array( 'unit' => 'em', 'size' => 1.7 ),
			'letter_spacing' => array( 'unit' => 'px', 'size' => 0 ),
			'word_spacing'   => array( 'unit' => 'em', 'size' => 0 ),
		);

		$button = array(
			'font_family'    => 'Montserrat',
			'font_size'      => array( 'unit' => 'px', 'size' => 12 ),
			'font_weight'    => '600',
			'text_transform' => 'uppercase',
			'font_style'     => 'normal',
			'text_decoration' => 'none',
			'line_height'    => array( 'unit' => 'em', 'size' => 1.2 ),
			'letter_spacing' => array( 'unit' => 'px', 'size' => 2 ),
			'word_spacing'   => array( 'unit' => 'em', 'size' => 0 ),
		);

		$design = array(
			'body_text' => $body,
			'h1'        => self::heading_variant( 46, 38, 32 ),
			'h2'        => self::heading_variant( 36, 30, 26 ),
			'h3'        => self::heading_variant( 28, 24, 22 ),
			'h4'        => self::heading_variant( 22, 20, 18 ),
			'h5'        => self::heading_variant( 17, 17, 16 ),
			'h6'        => self::heading_variant( 15, 15, 15 ),
			'button'    => $button,
		);

		$merged = array();
		foreach ( $current as $key => $value ) {
			if ( isset( $design[ $key ] ) ) {
				$merged[ $key ] = array_merge( is_array( $value ) ? $value : array(), $design[ $key ] );
				unset( $design[ $key ] );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return array_merge( $merged, $design );
	}

	/**
	 * Build a heading typography variant.
	 *
	 * @param int $desktop Font size desktop.
	 * @param int $tablet  Font size tablet.
	 * @param int $mobile  Font size mobile.
	 * @return array
	 */
	private static function heading_variant( $desktop, $tablet, $mobile ) {
		return array(
			'font_family'    => 'Cormorant Garamond',
			'font_size'      => array(
				'unit'         => 'px',
				'size'         => $desktop,
				'size_tablet'  => $tablet,
				'size_mobile'  => $mobile,
			),
			'font_weight'    => '500',
			'text_transform' => 'none',
			'font_style'     => 'normal',
			'text_decoration' => 'none',
			'line_height'    => array( 'unit' => 'em', 'size' => 1.2 ),
			'letter_spacing' => array( 'unit' => 'px', 'size' => 1.5 ),
			'word_spacing'   => array( 'unit' => 'em', 'size' => 0 ),
		);
	}

	/**
	 * Global (custom) typography entries.
	 *
	 * @return array
	 */
	private static function custom_typography() {
		return array(
			array(
				'id'              => 'lux_heading',
				'title'           => 'Lux Heading',
				'font_family'     => 'Cormorant Garamond',
				'font_size'       => array( 'unit' => 'px', 'size' => 32 ),
				'font_weight'     => '500',
				'text_transform'  => 'none',
				'font_style'      => 'normal',
				'text_decoration' => 'none',
				'line_height'     => array( 'unit' => 'em', 'size' => 1.2 ),
				'letter_spacing'  => array( 'unit' => 'px', 'size' => 1.5 ),
				'word_spacing'    => array( 'unit' => 'em', 'size' => 0 ),
			),
			array(
				'id'              => 'lux_body',
				'title'           => 'Lux Body',
				'font_family'     => 'Montserrat',
				'font_size'       => array( 'unit' => 'px', 'size' => 14 ),
				'font_weight'     => '300',
				'text_transform'  => 'none',
				'font_style'      => 'normal',
				'text_decoration' => 'none',
				'line_height'     => array( 'unit' => 'em', 'size' => 1.7 ),
				'letter_spacing'  => array( 'unit' => 'px', 'size' => 0 ),
				'word_spacing'    => array( 'unit' => 'em', 'size' => 0 ),
			),
			array(
				'id'              => 'lux_accent',
				'title'           => 'Lux Accent',
				'font_family'     => 'Montserrat',
				'font_size'       => array( 'unit' => 'px', 'size' => 12 ),
				'font_weight'     => '600',
				'text_transform'  => 'uppercase',
				'font_style'      => 'normal',
				'text_decoration' => 'none',
				'line_height'     => array( 'unit' => 'em', 'size' => 1.4 ),
				'letter_spacing'  => array( 'unit' => 'px', 'size' => 2 ),
				'word_spacing'    => array( 'unit' => 'em', 'size' => 0 ),
			),
		);
	}

	/**
	 * Merge named custom items (colors / typography) by title.
	 * Existing items with the same title are updated, the rest are appended.
	 *
	 * @param array $current Existing items.
	 * @param array $design  Design items.
	 * @return array
	 */
	private static function merge_named_items( $current, $design ) {
		$by_title = array();
		foreach ( $design as $item ) {
			$by_title[ strtolower( $item['title'] ) ] = $item;
		}

		$merged = array();
		foreach ( $current as $item ) {
			if ( ! is_array( $item ) || empty( $item['title'] ) ) {
				continue;
			}
			$key = strtolower( $item['title'] );
			if ( isset( $by_title[ $key ] ) ) {
				$merged[] = $by_title[ $key ];
				unset( $by_title[ $key ] );
			} else {
				$merged[] = $item;
			}
		}

		foreach ( $by_title as $item ) {
			$merged[] = $item;
		}

		return $merged;
	}
}

Luxury_Elementor_Kit::init();
