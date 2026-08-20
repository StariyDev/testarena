=== 33 Store - Luxury Monochrome Child ===
Contributors: 33store
Tags: e-commerce, luxury, fashion, monochrome, elementor, woocommerce
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Luxury monochrome design system for 33 Store: Elementor Pro + WooCommerce on a Hello Elementor child theme.

== Description ==

A complete luxury monochrome design system for 33 Store (33store.masterpc.ge):

* Cormorant Garamond serif headings + Montserrat uppercase navigation/buttons.
* 36px black announcement bar: "100% AUTHENTIC LUXURY | EXPRESS WORLDWIDE SHIPPING".
* Sticky white header with left-aligned navigation and minimalist search / account / cart icons.
* Slide-out cart drawer, full-screen search overlay and full-screen mobile menu.
* Shop grid: 3 columns desktop / 2 tablet / 1 mobile, strict 3:4 vertical product framing,
  secondary hover image, quick-add trigger, no star ratings.
* Single product: sticky vertical gallery (no thumb sliders) + sticky summary with brand,
  serif title, price & tax note, minimalist size selector, black "ADD TO BAG" CTA and
  the "Guaranteed 100% Authentic" badge.
* WooCommerce global overrides: default styles/scripts stripped, minimalist cart & checkout.
* Programmatic Elementor Kit injection (global colors + global typography).

== Installation ==

1. Upload the `hello-elementor-child` folder to `/wp-content/themes/`, or install the
   built zip (see bin/build-zip.sh).
2. Activate "33 Store - Luxury Monochrome Child" in Appearance > Themes.
   The design system is applied to the Elementor Kit automatically on activation.
3. Assign menus: Appearance > Menus > "Header" (menu-1) and "Footer" (menu-2).
4. Re-apply the Elementor Kit tokens any time via Appearance > 33 Store Design System.

== Frequently Asked Questions ==

= Does it require Elementor Pro? =

Elementor Pro is recommended for Theme Builder templates (single product,
shop archive). The child theme works without it, but global typography
injection (custom_typography) is a Pro feature.

= How do I build the single product template in Elementor Theme Builder? =

Create a Single Product template with a two-column layout: left = Product
Images (or keep the theme gallery), right = Brand (heading), Product Title,
Price, Add To Cart, then the authenticity badge widget. The theme CSS already
provides the sticky behavior and the badge component.

== Changelog ==

= 1.0.0 =
* Initial release.
