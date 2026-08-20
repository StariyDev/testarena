<?php
/**
 * 33 Store — Luxury header template part (Elementor "dynamic header" path).
 *
 * Overrides Hello Elementor's `template-parts/dynamic-header.php`
 * so the same luxury header renders when the header/footer experiment
 * is enabled. Elementor Theme Builder locations still take precedence
 * via the parent's `header.php` guard.
 *
 * @package 33StoreLuxury
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Luxury_Header::render_top_bar();
Luxury_Header::render_header();
