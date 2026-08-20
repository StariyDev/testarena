<?php
/**
 * 33 Store — Luxury header template part.
 *
 * Overrides Hello Elementor's `template-parts/header.php`.
 * Renders the announcement bar + sticky luxury header.
 *
 * @package 33StoreLuxury
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Luxury_Header::render_top_bar();
Luxury_Header::render_header();
