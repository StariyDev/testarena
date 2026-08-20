<?php
/**
 * 33 Store — Luxury Monochrome Child Theme
 *
 * Child theme of Hello Elementor. Wires up the luxury design system:
 * fonts, styles, WooCommerce clean-ups, Elementor Kit global settings
 * and the custom header / cart drawer / gallery components.
 *
 * @package 33StoreLuxury
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'LUXURY_THEME_VERSION', '1.0.0' );
define( 'LUXURY_THEME_DIR', get_stylesheet_directory() );
define( 'LUXURY_THEME_URI', get_stylesheet_directory_uri() );

require_once LUXURY_THEME_DIR . '/inc/class-luxury-header.php';
require_once LUXURY_THEME_DIR . '/inc/class-luxury-woocommerce.php';
require_once LUXURY_THEME_DIR . '/inc/class-luxury-elementor-kit.php';

/**
 * Theme setup: text domain, WooCommerce gallery support removal, body class.
 */
function luxury_theme_setup() {
	load_child_theme_textdomain( 'thirtythree-store', LUXURY_THEME_DIR . '/languages' );

	/*
	 * The luxury single-product gallery is a custom vertical stack
	 * (no thumb sliders), so the core WooCommerce gallery features
	 * (zoom / lightbox / slider) are removed at the theme level.
	 */
	remove_theme_support( 'wc-product-gallery-zoom' );
	remove_theme_support( 'wc-product-gallery-lightbox' );
	remove_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'luxury_theme_setup', 20 );

/**
 * Add a global body class used to scope every luxury rule.
 *
 * @param array $classes Body classes.
 * @return array
 */
function luxury_body_class( $classes ) {
	$classes[] = 'luxury-design';
	return $classes;
}
add_filter( 'body_class', 'luxury_body_class' );

/**
 * Enqueue fonts, styles and scripts.
 */
function luxury_enqueue_assets() {
	$fonts_url = luxury_google_fonts_url();

	// Google Fonts: Cormorant Garamond + Montserrat.
	if ( $fonts_url ) {
		wp_enqueue_style( 'luxury-google-fonts', $fonts_url, array(), LUXURY_THEME_VERSION );
	}

	// Child theme stylesheet (design tokens) + full design system.
	wp_enqueue_style( 'thirtythree-store-style', get_stylesheet_uri(), array(), LUXURY_THEME_VERSION );
	wp_enqueue_style( 'luxury-theme', LUXURY_THEME_URI . '/assets/css/luxury-theme.css', array( 'thirtythree-store-style' ), LUXURY_THEME_VERSION );

	// Theme JS.
	wp_enqueue_script( 'luxury-theme', LUXURY_THEME_URI . '/assets/js/luxury-theme.js', array(), LUXURY_THEME_VERSION, true );

	$cart_count = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}

	wp_localize_script(
		'luxury-theme',
		'LUXURY',
		array(
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'wcAjaxUrl'         => function_exists( 'WC' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'nonce'             => wp_create_nonce( 'luxury-theme' ),
			'cartCount'         => $cart_count,
			'wcActive'          => class_exists( 'WooCommerce' ),
			'fragmentsEndpoint' => function_exists( 'WC' ) ? WC_AJAX::get_endpoint( 'get_refreshed_fragments' ) : '',
			'i18n'              => array(
				'addedToBag' => esc_html__( 'Added to bag', 'thirtythree-store' ),
			),
		)
	);

	// Pass WC add-to-cart params for the quick-add flow.
	if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_cart_url' ) ) {
		wp_localize_script(
			'luxury-theme',
			'wc_add_to_cart_params_luxury',
			array(
				'cart_url' => esc_url( wc_get_cart_url() ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'luxury_enqueue_assets', 20 );

/**
 * Build the Google Fonts URL.
 *
 * @return string
 */
function luxury_google_fonts_url() {
	$font_families = array(
		'family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500',
		'family=Montserrat:wght@300;400;500;600;700',
	);

	return 'https://fonts.googleapis.com/css2?' . implode( '&', $font_families ) . '&display=swap';
}

/**
 * Apply the Elementor Kit design system once when the theme is switched on,
 * and flush rewrite rules (product permalinks etc.).
 */
function luxury_theme_switch( $old_theme_name, $old_theme ) {
	Luxury_Elementor_Kit::apply();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'luxury_theme_switch', 10, 2 );

/**
 * Register widgets / sidebars used by the footer.
 */
function luxury_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column 1', 'thirtythree-store' ),
			'id'            => 'luxury-footer-1',
			'description'   => esc_html__( 'Appears in the first footer column.', 'thirtythree-store' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column 2', 'thirtythree-store' ),
			'id'            => 'luxury-footer-2',
			'description'   => esc_html__( 'Appears in the second footer column.', 'thirtythree-store' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'luxury_widgets_init' );

/**
 * WP-CLI integration: `wp luxury apply` / `wp luxury status`.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'luxury apply',
		function () {
			$ok = Luxury_Elementor_Kit::apply();
			if ( $ok ) {
				WP_CLI::success( '33 Store design system applied to the Elementor Kit.' );
			} else {
				WP_CLI::warning( 'Elementor Kit not found. Is Elementor active? Skipped.' );
			}
		}
	);

	WP_CLI::add_command(
		'luxury status',
		function () {
			$kit_id = (int) get_option( 'elementor_active_kit' );
			$applied = get_option( 'luxury_design_system_applied' );

			WP_CLI::line( 'Active Elementor kit ID: ' . ( $kit_id ? (string) $kit_id : 'none' ) );
			WP_CLI::line( 'Design system applied:    ' . ( $applied ? gmdate( 'Y-m-d H:i:s', (int) $applied ) : 'no' ) );
		}
	);
}
