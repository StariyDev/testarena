<?php
/**
 * WooCommerce Global Overrides
 *
 * Strips default WooCommerce styles/scripts, removes wrapper markup and
 * star ratings, and injects the luxury single-product blocks
 * (brand, tax note, authenticity badge) plus the quick-add loop support.
 *
 * @package 33StoreLuxury
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Luxury_WooCommerce
 */
final class Luxury_WooCommerce {

	/**
	 * Hook everything up.
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		/* --- Strip default WooCommerce CSS --- */
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

		/* --- Remove default wrapper markup & breadcrumbs --- */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		/* --- Star ratings: gone from loop and summary --- */
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );

		/* --- Disable the default gallery scripts on product pages --- */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'deregister_gallery_scripts' ), 30 );

		/* --- Loop: secondary hover image + brand name --- */
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'render_loop_hover_image' ), 11 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'render_loop_brand' ), 20 );

		/* --- Loop button: quick-add attributes --- */
		add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'quick_add_link' ), 10, 3 );

		/* --- Single product summary blocks --- */
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_brand' ), 4 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_tax_note' ), 12 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_authenticity_badge' ), 40 );

		/* --- CTA labels --- */
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'single_add_to_cart_text' ), 10, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( __CLASS__, 'loop_add_to_cart_text' ), 10, 2 );

		/* --- Cart fragments: keep the header count in sync --- */
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'cart_count_fragment' ) );

		/* --- Product card classes --- */
		add_filter( 'woocommerce_post_class', array( __CLASS__, 'product_card_class' ), 10, 2 );

		/* --- Image sizes: strict 3:4 vertical framing --- */
		add_filter( 'woocommerce_get_image_size_woocommerce_thumbnail', array( __CLASS__, 'thumbnail_size' ) );
		add_filter( 'woocommerce_get_image_size_woocommerce_single', array( __CLASS__, 'single_size' ) );
	}

	/**
	 * Dequeue default gallery assets (flexslider, zoom, photoswipe).
	 */
	public static function deregister_gallery_scripts() {
		if ( ! is_product() ) {
			return;
		}

		$scripts = array(
			'wc-single-product',
			'flexslider',
			'zoom',
			'photoswipe',
			'photoswipe-ui-default',
		);

		foreach ( $scripts as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}

	/**
	 * Secondary hover image in the product loop.
	 *
	 * @return void
	 */
	public static function render_loop_hover_image() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$gallery_ids = $product->get_gallery_image_ids();
		if ( empty( $gallery_ids ) ) {
			return;
		}

		$hover_id = absint( $gallery_ids[0] );

		echo wp_get_attachment_image(
			$hover_id,
			'woocommerce_thumbnail',
			false,
			array(
				'class'   => 'luxury-loop-hover',
				'alt'     => '',
				'loading' => 'lazy',
			)
		);
	}

	/**
	 * Brand name in the product loop (attribute 'Brand' first, else category).
	 *
	 * @return void
	 */
	public static function render_loop_brand() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$brand = luxury_get_product_brand( $product );
		if ( '' === $brand ) {
			return;
		}

		echo '<span class="luxury-product-brand">' . esc_html( $brand ) . '</span>';
	}

	/**
	 * Brand name on the single product (above the title).
	 *
	 * @return void
	 */
	public static function render_brand() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$brand = luxury_get_product_brand( $product );
		if ( '' === $brand ) {
			return;
		}

		echo '<h4 class="luxury-single-brand">' . esc_html( $brand ) . '</h4>';
	}

	/**
	 * Tax details note under the price.
	 *
	 * @return void
	 */
	public static function render_tax_note() {
		$note = apply_filters( 'luxury_tax_note', __( 'Price includes VAT · Taxes calculated at checkout', 'thirtythree-store' ) );
		if ( '' === $note ) {
			return;
		}
		echo '<p class="luxury-tax-note">' . esc_html( $note ) . '</p>';
	}

	/**
	 * Authenticity badge block below the add-to-cart button.
	 *
	 * @return void
	 */
	public static function render_authenticity_badge() {
		$title = apply_filters( 'luxury_authenticity_title', __( 'Guaranteed 100% Authentic.', 'thirtythree-store' ) );
		$body  = apply_filters( 'luxury_authenticity_text', __( 'Verified by in-house brand specialists.', 'thirtythree-store' ) );
		?>
		<div class="luxury-authenticity">
			<svg width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true" focusable="false">
				<path d="M13 2l9 3v7c0 6-3.8 10.4-9 12-5.2-1.6-9-6-9-12V5l9-3Z" stroke="currentColor" stroke-width="1.1"/>
				<path d="M8.5 12.8l3 3 6-6.2" stroke="currentColor" stroke-width="1.1"/>
			</svg>
			<p class="luxury-authenticity__text">
				<strong><?php echo esc_html( $title ); ?></strong>
				<?php echo esc_html( $body ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Single product CTA label.
	 *
	 * @param string    $text    Default text.
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function single_add_to_cart_text( $text, $product ) {
		return apply_filters( 'luxury_single_add_to_cart_text', __( 'ADD TO BAG', 'thirtythree-store' ) );
	}

	/**
	 * Loop CTA label.
	 *
	 * @param string    $text    Default text.
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function loop_add_to_cart_text( $text, $product ) {
		if ( $product && 'simple' === $product->get_type() && $product->is_purchasable() && $product->is_in_stock() ) {
			return apply_filters( 'luxury_loop_add_to_cart_text', __( 'ADD TO BAG', 'thirtythree-store' ) );
		}
		return $text;
	}

	/**
	 * Quick-add data attributes on loop buttons.
	 *
	 * @param string     $html    Button HTML.
	 * @param WC_Product $product Product.
	 * @param array      $args    Args.
	 * @return string
	 */
	public static function quick_add_link( $html, $product, $args ) {
		if ( ! $product ) {
			return $html;
		}

		$type = $product->get_type();

		// Only simple (in-stock, purchasable) products quick-add via AJAX.
		$quick = ( 'simple' === $type && $product->is_purchasable() && $product->is_in_stock() ) ? '1' : '0';

		$html = str_replace(
			'class="',
			'class="luxury-loop-button ',
			$html
		);

		return str_replace(
			'<a ',
			'<a data-luxury-quick-add="' . esc_attr( $quick ) . '" data-luxury-product-type="' . esc_attr( $type ) . '" ',
			$html
		);
	}

	/**
	 * Fragment for the header cart count.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public static function cart_count_fragment( $fragments ) {
		$count = Luxury_Header::cart_count();

		$fragments['span.luxury-cart-count'] = sprintf(
			'<span class="luxury-cart-count%s" data-luxury-cart-count>%d</span>',
			$count ? '' : ' is-empty',
			(int) $count
		);

		return $fragments;
	}

	/**
	 * Product card CSS classes.
	 *
	 * @param array      $classes Classes.
	 * @param WC_Product $product Product.
	 * @return array
	 */
	public static function product_card_class( $classes, $product ) {
		$classes[] = 'luxury-product-card';
		return $classes;
	}

	/**
	 * Loop thumbnail: 3:4 crop (600 x 800).
	 *
	 * @param array $size Size.
	 * @return array
	 */
	public static function thumbnail_size( $size ) {
		return array(
			'width'  => 600,
			'height' => 800,
			'crop'   => 1,
		);
	}

	/**
	 * Single product image: 3:4 crop (900 x 1200).
	 *
	 * @param array $size Size.
	 * @return array
	 */
	public static function single_size( $size ) {
		return array(
			'width'  => 900,
			'height' => 1200,
			'crop'   => 1,
		);
	}
}

Luxury_WooCommerce::init();

/**
 * Resolve the product "brand" string.
 *
 * Checks the product attribute "Brand" (or pa_brand taxonomy) first,
 * then falls back to the primary product category.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function luxury_get_product_brand( $product ) {
	$brand = '';

	if ( $product ) {
		foreach ( $product->get_attributes() as $attribute ) {
			$name = strtolower( $attribute->get_name() );
			if ( in_array( $name, array( 'brand', 'pa_brand' ), true ) ) {
				if ( $attribute->is_taxonomy() ) {
					$terms = $attribute->get_terms();
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$brand = $terms[0]->name;
						break;
					}
				} else {
					$options = $attribute->get_options();
					if ( ! empty( $options ) ) {
						$brand = (string) $options[0];
						break;
					}
				}
			}
		}

		if ( '' === $brand ) {
			$terms = get_the_terms( $product->get_id(), 'product_cat' );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) && isset( $terms[0]->name ) && 'uncategorized' !== strtolower( $terms[0]->slug ) ) {
				$brand = $terms[0]->name;
			}
		}
	}

	return apply_filters( 'luxury_product_brand', $brand, $product );
}
