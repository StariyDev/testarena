# 33 Store — Luxury Monochrome Design System

Production child theme for **[33store.masterpc.ge](https://33store.masterpc.ge)** — a luxury fashion
e-commerce store running **WordPress + Elementor Pro + WooCommerce**.

The design system is implemented as a **Hello Elementor child theme** (`hello-elementor-child`)
with a dedicated CSS design-token layer, a WooCommerce override layer, and a programmatic
Elementor Kit (global fonts & colors) injection layer.

---

## What's inside

| Path | Purpose |
| --- | --- |
| `wp-content/themes/hello-elementor-child/` | The installable child theme |
| `wp-content/themes/hello-elementor-child/style.css` | Theme header + design tokens (`--lux-*`) |
| `wp-content/themes/hello-elementor-child/assets/css/luxury-theme.css` | Full component design system |
| `wp-content/themes/hello-elementor-child/assets/js/luxury-theme.js` | Drawer / menu / search / quick-add / image viewer |
| `wp-content/themes/hello-elementor-child/inc/class-luxury-header.php` | Announcement bar, header, footer, overlays |
| `wp-content/themes/hello-elementor-child/inc/class-luxury-woocommerce.php` | WC clean-ups, badges, quick-add, fragments |
| `wp-content/themes/hello-elementor-child/inc/class-luxury-elementor-kit.php` | Global fonts & colors injection + admin page |
| `wp-content/themes/hello-elementor-child/template-parts/` | Overrides of Hello Elementor header/footer parts |
| `wp-content/themes/hello-elementor-child/woocommerce/single-product/product-image.php` | Vertical scrollable gallery (no thumb sliders) |
| `preview/index.html` | **Static design-system preview** (open in browser) |

---

## Design system summary

### A. Colors
```css
--lux-black: #111111;        /* Primary text, buttons, active states */
--lux-charcoal: #1A1A1A;     /* Secondary headings, hover states */
--lux-muted: #555555;        /* Body copy, metadata, secondary links */
--lux-light-gray: #E5E5E5;   /* 1px borders, dividers */
--lux-bg-offwhite: #F9F8F6;  /* Section backgrounds, modal overlays */
--lux-white: #FFFFFF;        /* Primary canvas */
```

### B. Typography
- **Headings H1–H3:** `Cormorant Garamond`, weight 500, `letter-spacing: 1.5px`, `line-height: 1.2`, no transform.
- **Nav / buttons / badges / accent:** `Montserrat` 600, 11–13px, `letter-spacing: 2px`, uppercase.
- **Body & descriptions:** `Montserrat` 300, 14–15px, `line-height: 1.7`, `color: var(--lux-muted)`.

### C. Components
- **Announcement bar** — 36px, black, white uppercase text: `100% AUTHENTIC LUXURY | EXPRESS WORLDWIDE SHIPPING`.
- **Sticky header** — white, 1px bottom border, left-aligned uppercase nav with 1px underline hover,
  minimalist search / account / cart icons with cart-count badge.
- **Cart drawer** — right slide-out panel with mini-cart, subtotal and View Bag / Checkout CTAs.
- **Search overlay & mobile menu** — full-screen minimal overlays.
- **Shop grid** — 3 cols desktop / 2 tablet / 1 mobile; strict `3:4` `object-fit: cover` framing;
  brand (uppercase) + short title + bold price; no star ratings, no heavy borders/shadows;
  hover shows secondary image + quick-add bar.
- **Single product** — sticky vertical gallery (no thumb sliders) + sticky summary:
  Brand (H4 uppercase) → Title (H1 Cormorant) → Price & tax note → minimalist size selector →
  black **ADD TO BAG** CTA → bordered **"Guaranteed 100% Authentic. Verified by in-house brand specialists."** badge.
- **Cart / Checkout / My Account** — stripped default WooCommerce CSS; minimalist underline inputs,
  black CTAs, monochrome tables and notices.

---

## Installation (on the live site)

### Option 1 — WP-CLI / SSH (deploy script)
```bash
cd wp-content/themes/hello-elementor-child
bash bin/build-zip.sh                  # -> dist/hello-elementor-child.zip
bash bin/deploy.sh ssh_user@33store.masterpc.ge /path/to/wordpress
```
The deploy script uploads the zip, activates the theme and applies the Elementor Kit
tokens via `wp luxury apply`.

### Option 2 — Manual
1. Upload `hello-elementor-child/` to `wp-content/themes/` (or install `dist/hello-elementor-child.zip`
   via **Appearance → Themes → Add New → Upload**).
2. Activate **"33 Store – Luxury Monochrome Child"**.
   The Elementor Kit global colors/typography are applied automatically on activation.
3. **Appearance → Menus** → assign menus to *Header* and *Footer* locations.
4. Re-apply tokens any time: **Appearance → 33 Store Design System → Apply**.

---

## Elementor Kit integration

`inc/class-luxury-elementor-kit.php` writes the design system into the active Elementor Kit
(`elementor_active_kit` → `_elementor_page_settings`):

- **Global colors:** Primary `#111111`, Secondary `#1A1A1A`, Text `#111111`, Accent `#111111`.
- **Custom colors:** Lux Black / Lux Charcoal / Lux Muted / Lux Light Gray / Lux Offwhite / Lux White.
- **Custom typography:** *Lux Heading* (Cormorant Garamond 500), *Lux Body* (Montserrat 300),
  *Lux Accent* (Montserrat 600 uppercase).
- **System typography:** H1–H6 → Cormorant Garamond; body → Montserrat; buttons → Montserrat uppercase.

Apply on demand: **Appearance → 33 Store Design System** or `wp luxury apply` (WP-CLI).

### Theme Builder templates (recommended)
Build these in **Elementor Pro → Theme Builder** and the theme CSS will style them automatically:

1. **Single Product template** — two-column layout:
   - *Left:* Product Images (or leave the theme's vertical gallery by not setting images in the template).
   - *Right:* Heading (Brand) → Product Title → Price → Add To Cart → an HTML widget
     containing the authenticity badge markup (see below), or a shortcode.
2. **Product Archive template** — use the **Products** widget; card styling and 3:4 framing
   are provided globally.

Authenticity badge markup for an Elementor HTML widget:
```html
<div class="luxury-authenticity">
  <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M13 2l9 3v7c0 6-3.8 10.4-9 12-5.2-1.6-9-6-9-12V5l9-3Z" stroke="currentColor" stroke-width="1.1"/><path d="M8.5 12.8l3 3 6-6.2" stroke="currentColor" stroke-width="1.1"/></svg>
  <p class="luxury-authenticity__text"><strong>Guaranteed 100% Authentic.</strong> Verified by in-house brand specialists.</p>
</div>
```

---

## WooCommerce overrides (functions)

- Default WC stylesheets removed (`woocommerce_enqueue_styles` → empty).
- Default wrapper `<div>`s and breadcrumbs removed.
- Star ratings removed from loop + summary.
- Gallery scripts (flexslider / zoom / photoswipe) dequeued on product pages.
- `ADD TO BAG` single-product CTA text.
- Quick-add via `wc-ajax=add_to_cart` (capture-phase handler prevents double adds), drawer opens on success.
- Cart fragments keep the header count + drawer in sync.
- Image sizes forced to **3:4** (`woocommerce_thumbnail` 600×800, `woocommerce_single` 900×1200).
- Brand resolves from the `Brand`/`pa_brand` product attribute, falling back to the primary category.

---

## Filters & hooks

| Filter / action | What it controls |
| --- | --- |
| `luxury_announcement_text` | Top-bar text (return `''` to hide). |
| `luxury_header_wordmark` | Text logo fallback. |
| `luxury_tax_note` | Tax line under the price. |
| `luxury_authenticity_title` / `luxury_authenticity_text` | Badge copy. |
| `luxury_single_add_to_cart_text` / `luxury_loop_add_to_cart_text` | CTA labels. |
| `luxury_product_brand` | Resolved brand string. |
| `luxury_footer_socials` / `luxury_footer_help_links` / `luxury_footer_legal_links` | Footer link arrays. |
| `luxury_fallback_menu_links` | Fallback header menu when no menu is assigned. |
| `hello_elementor_header_footer` (parent) | Set `false` to fully disable theme header/footer (Elementor locations take over). |

---

## Responsive audit

- Breakpoints: **1024px** (grid → 2 cols, checkout → 1 col), **1023px** (nav collapses to burger),
  **640px** (grid → 1 col, quick-add always visible).
- `overflow-x: clip` guards on content wrappers prevent horizontal scroll.
- Fluid gutters via `clamp()`; sticky offsets account for the fixed header.
- `prefers-reduced-motion` respected.

---

## Local preview

`preview/index.html` is a static, interactive mock of the design system (header, hero, grid,
single product, drawer, search, mobile menu, footer). Serve the repo root and open it, e.g.:

```bash
python3 -m http.server 8080 --directory /home/user/testarena
# then open http://localhost:8080/preview/
```

---

## License

GPLv2 or later. Fonts (Cormorant Garamond, Montserrat) are loaded from Google Fonts
(OFL-licensed) and are not bundled.
