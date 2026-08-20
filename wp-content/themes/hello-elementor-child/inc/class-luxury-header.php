<?php
/**
 * Luxury Header, Footer & Overlay Components
 *
 * Renders the announcement bar, sticky main header, mobile menu,
 * search overlay, slide-out cart drawer and the footer.
 *
 * @package 33StoreLuxury
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Luxury_Header
 */
final class Luxury_Header {

	/**
	 * Hook everything up.
	 */
	public static function init() {
		add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'menu_link_attributes' ), 10, 2 );
	}

	/**
	 * Announcement bar text (filterable).
	 *
	 * @return string
	 */
	public static function announcement_text() {
		return apply_filters(
			'luxury_announcement_text',
			esc_html__( '100% AUTHENTIC LUXURY | EXPRESS WORLDWIDE SHIPPING', 'thirtythree-store' )
		);
	}

	/**
	 * Announcement bar markup.
	 */
	public static function render_top_bar() {
		$text = self::announcement_text();
		if ( '' === $text ) {
			return;
		}
		?>
		<div class="luxury-topbar" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'thirtythree-store' ); ?>">
			<div class="luxury-topbar__inner">
				<span class="luxury-topbar__text"><?php echo esc_html( $text ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Header wordmark (filterable).
	 *
	 * @return string
	 */
	public static function wordmark() {
		$name = get_bloginfo( 'name' );
		return apply_filters( 'luxury_header_wordmark', $name ? $name : '33 Store' );
	}

	/**
	 * Main header markup: burger, logo, nav, action icons.
	 */
	public static function render_header() {
		?>
		<header id="luxury-header" class="luxury-header" data-luxury-header>
			<div class="luxury-header__inner">
				<button type="button" class="luxury-header__burger" aria-label="<?php esc_attr_e( 'Open menu', 'thirtythree-store' ); ?>" aria-expanded="false" data-luxury-menu-open>
					<svg width="22" height="14" viewBox="0 0 22 14" fill="none" aria-hidden="true" focusable="false">
						<path d="M0 1h22M0 7h22M0 13h22" stroke="currentColor" stroke-width="1.2"/>
					</svg>
				</button>

				<a class="luxury-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Home', 'thirtythree-store' ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<span class="luxury-header__logo-text"><?php echo esc_html( self::wordmark() ); ?></span>
					<?php endif; ?>
				</a>

				<nav class="luxury-header__nav" aria-label="<?php esc_attr_e( 'Main menu', 'thirtythree-store' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'container'      => false,
							'menu_class'     => 'luxury-nav',
							'fallback_cb'    => 'luxury_fallback_menu',
							'depth'          => 2,
						)
					);
					?>
				</nav>

				<div class="luxury-header__actions">
					<button type="button" class="luxury-header__action" aria-label="<?php esc_attr_e( 'Search', 'thirtythree-store' ); ?>" data-luxury-search-open>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
							<circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.2"/>
							<path d="M12.5 12.5L17 17" stroke="currentColor" stroke-width="1.2"/>
						</svg>
					</button>

					<a class="luxury-header__action" href="<?php echo esc_url( luxury_account_url() ); ?>" aria-label="<?php esc_attr_e( 'My account', 'thirtythree-store' ); ?>">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
							<circle cx="9" cy="5.5" r="3.25" stroke="currentColor" stroke-width="1.2"/>
							<path d="M3 16.5c.6-4 3-6 6-6s5.4 2 6 6" stroke="currentColor" stroke-width="1.2"/>
						</svg>
					</a>

					<button type="button" class="luxury-header__action" aria-label="<?php esc_attr_e( 'Shopping bag', 'thirtythree-store' ); ?>" data-luxury-drawer-open>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
							<path d="M3.5 6h11l-.9 9.2a1.5 1.5 0 0 1-1.5 1.3H5.9a1.5 1.5 0 0 1-1.5-1.3L3.5 6Z" stroke="currentColor" stroke-width="1.2"/>
							<path d="M6 6V4.75a3 3 0 0 1 6 0V6" stroke="currentColor" stroke-width="1.2"/>
						</svg>
						<span class="luxury-cart-count<?php echo self::cart_count() ? '' : ' is-empty'; ?>" data-luxury-cart-count><?php echo esc_html( self::cart_count() ); ?></span>
					</button>
				</div>
			</div>
		</header>
		<?php
	}

	/**
	 * Cart contents count.
	 *
	 * @return int
	 */
	public static function cart_count() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return 0;
		}
		return (int) WC()->cart->get_cart_contents_count();
	}

	/**
	 * Full-screen mobile menu.
	 */
	public static function render_mobile_menu() {
		?>
		<div class="luxury-menu" aria-hidden="true" data-luxury-menu>
			<div class="luxury-menu__head">
				<span class="luxury-header__logo-text"><?php echo esc_html( self::wordmark() ); ?></span>
				<button type="button" class="luxury-menu__close" aria-label="<?php esc_attr_e( 'Close menu', 'thirtythree-store' ); ?>" data-luxury-menu-close>
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
						<path d="M2 2l14 14M16 2 2 16" stroke="currentColor" stroke-width="1.2"/>
					</svg>
				</button>
			</div>
			<nav class="luxury-menu__nav" aria-label="<?php esc_attr_e( 'Mobile menu', 'thirtythree-store' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'container'      => false,
						'menu_class'     => '',
						'fallback_cb'    => 'luxury_fallback_menu',
						'depth'          => 2,
					)
				);
				?>
			</nav>
			<div class="luxury-menu__footer">
				<a href="<?php echo esc_url( luxury_account_url() ); ?>"><?php esc_html_e( 'Account', 'thirtythree-store' ); ?></a>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Bag', 'thirtythree-store' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Full-screen search overlay.
	 */
	public static function render_search() {
		?>
		<div class="luxury-search" role="search" aria-hidden="true" data-luxury-search>
			<div class="luxury-search__head">
				<span class="luxury-search__title"><?php esc_html_e( 'Search', 'thirtythree-store' ); ?></span>
				<button type="button" class="luxury-search__close" aria-label="<?php esc_attr_e( 'Close search', 'thirtythree-store' ); ?>" data-luxury-search-close>
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
						<path d="M2 2l14 14M16 2 2 16" stroke="currentColor" stroke-width="1.2"/>
					</svg>
				</button>
			</div>
			<form class="luxury-search__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search products…', 'thirtythree-store' ); ?>" aria-label="<?php esc_attr_e( 'Search for products', 'thirtythree-store' ); ?>" autocomplete="off" data-luxury-search-input />
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product" />
				<?php endif; ?>
				<button type="submit"><?php esc_html_e( 'Search', 'thirtythree-store' ); ?></button>
			</form>
			<p class="luxury-search__hint"><?php esc_html_e( 'Enter a brand, style or product name', 'thirtythree-store' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Slide-out cart drawer (renders the WooCommerce mini-cart inside).
	 */
	public static function render_cart_drawer() {
		?>
		<div class="luxury-drawer" aria-hidden="true" data-luxury-drawer>
			<div class="luxury-drawer__backdrop" data-luxury-drawer-close></div>
			<aside class="luxury-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping bag', 'thirtythree-store' ); ?>">
				<div class="luxury-drawer__head">
					<h3 class="luxury-drawer__title"><?php esc_html_e( 'Your Bag', 'thirtythree-store' ); ?></h3>
					<button type="button" class="luxury-drawer__close" aria-label="<?php esc_attr_e( 'Close bag', 'thirtythree-store' ); ?>" data-luxury-drawer-close>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
							<path d="M2 2l14 14M16 2 2 16" stroke="currentColor" stroke-width="1.2"/>
						</svg>
					</button>
				</div>
				<div class="luxury-drawer__body">
					<?php
					if ( class_exists( 'WooCommerce' ) && function_exists( 'woocommerce_mini_cart' ) ) {
						woocommerce_mini_cart();
					} else {
						echo '<p class="woocommerce-mini-cart__empty-message">' . esc_html__( 'Your bag is empty.', 'thirtythree-store' ) . '</p>';
					}
					?>
				</div>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<p class="luxury-drawer__note"><?php esc_html_e( 'Free express shipping on orders over $300', 'thirtythree-store' ); ?></p>
				<?php endif; ?>
			</aside>
		</div>
		<?php
	}

	/**
	 * Footer markup.
	 */
	public static function render_footer() {
		$socials   = apply_filters( 'luxury_footer_socials', array() );
		$help_links = apply_filters(
			'luxury_footer_help_links',
			array(
				'#contact'  => __( 'Contact Us', 'thirtythree-store' ),
				'#shipping' => __( 'Shipping & Delivery', 'thirtythree-store' ),
				'#returns'  => __( 'Returns & Exchanges', 'thirtythree-store' ),
				'#care'     => __( 'Product Care', 'thirtythree-store' ),
			)
		);
		$legal_links = apply_filters(
			'luxury_footer_legal_links',
			array(
				'#privacy' => __( 'Privacy Policy', 'thirtythree-store' ),
				'#terms'   => __( 'Terms & Conditions', 'thirtythree-store' ),
			)
		);
		?>
		<footer id="luxury-footer" class="luxury-footer">
			<div class="luxury-footer__inner">
				<div class="luxury-footer__brand">
					<div class="luxury-footer__logo">
						<?php if ( has_custom_logo() ) : ?>
							<?php the_custom_logo(); ?>
						<?php else : ?>
							<?php echo esc_html( self::wordmark() ); ?>
						<?php endif; ?>
					</div>
					<p><?php echo esc_html( apply_filters( 'luxury_footer_tagline', __( 'Curated luxury fashion. Guaranteed 100% authentic, delivered worldwide.', 'thirtythree-store' ) ) ); ?></p>
					<?php if ( $socials ) : ?>
						<div class="luxury-footer__social">
							<?php foreach ( $socials as $label => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>">
									<?php echo esc_html( strtoupper( substr( $label, 0, 1 ) ) ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="luxury-footer__col">
					<?php if ( is_active_sidebar( 'luxury-footer-1' ) ) : ?>
						<?php dynamic_sidebar( 'luxury-footer-1' ); ?>
					<?php else : ?>
						<h4><?php esc_html_e( 'Customer Care', 'thirtythree-store' ); ?></h4>
						<ul>
							<?php foreach ( $help_links as $url => $label ) : ?>
								<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="luxury-footer__col">
					<?php if ( is_active_sidebar( 'luxury-footer-2' ) ) : ?>
						<?php dynamic_sidebar( 'luxury-footer-2' ); ?>
					<?php else : ?>
						<h4><?php esc_html_e( 'Legal', 'thirtythree-store' ); ?></h4>
						<ul>
							<?php foreach ( $legal_links as $url => $label ) : ?>
								<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="luxury-footer__col">
					<h4><?php esc_html_e( 'Follow', 'thirtythree-store' ); ?></h4>
					<ul>
						<?php
						$footer_menu = wp_nav_menu(
							array(
								'theme_location' => 'menu-2',
								'container'      => false,
								'fallback_cb'    => false,
								'echo'           => false,
								'depth'          => 1,
							)
						);
						if ( $footer_menu ) {
							echo $footer_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '<li><a href="#">Instagram</a></li>';
							echo '<li><a href="#">Facebook</a></li>';
							echo '<li><a href="#">Pinterest</a></li>';
						}
						?>
					</ul>
				</div>
			</div>

			<div class="luxury-footer__bottom">
				<div class="luxury-footer__bottom-inner">
					<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'thirtythree-store' ); ?></span>
					<span class="luxury-footer__payments"><?php esc_html_e( 'VISA · MASTERCARD · AMEX · PAYPAL', 'thirtythree-store' ); ?></span>
				</div>
			</div>
		</footer>

		<?php
		// Overlays (only rendered when the theme header/footer are in use).
		self::render_mobile_menu();
		self::render_search();
		self::render_cart_drawer();
	}

	/**
	 * Fallback menu when no menu is assigned to the location.
	 */
	public static function fallback_menu() {
		$links = apply_filters(
			'luxury_fallback_menu_links',
			array(
				home_url( '/' ) => __( 'Home', 'thirtythree-store' ),
			)
		);
		echo '<ul class="luxury-nav">';
		foreach ( $links as $url => $label ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 * Add micro-formatting attributes to nav links (nothing needed by default).
	 *
	 * @param array    $atts Link attributes.
	 * @param WP_Post  $item Menu item.
	 * @return array
	 */
	public static function menu_link_attributes( $atts, $item ) {
		return $atts;
	}
}

/**
 * Account URL helper (WooCommerce-aware).
 *
 * @return string
 */
function luxury_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) && wc_get_page_permalink( 'myaccount' ) ) {
		return wc_get_page_permalink( 'myaccount' );
	}
	return wp_login_url();
}

/**
 * Fallback menu callback used by wp_nav_menu().
 */
function luxury_fallback_menu() {
	Luxury_Header::fallback_menu();
}
