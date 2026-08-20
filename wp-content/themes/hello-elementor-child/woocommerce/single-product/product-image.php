<?php
/**
 * 33 Store — Vertical scrollable product gallery.
 *
 * Overrides WooCommerce's default single-product/product-image.php.
 * Renders every product image in a strict 3:4 vertical stack
 * (no flexslider thumb slider, no photoswipe) with a lightweight
 * full-size viewer handled by assets/js/luxury-theme.js.
 *
 * @package 33StoreLuxury
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product ) {
	return;
}

$main_id = (int) $product->get_image_id();

$image_ids = array();
if ( $main_id ) {
	$image_ids[] = $main_id;
}

foreach ( (array) $product->get_gallery_image_ids() as $gallery_id ) {
	$gallery_id = (int) $gallery_id;
	if ( $gallery_id && ! in_array( $gallery_id, $image_ids, true ) ) {
		$image_ids[] = $gallery_id;
	}
}
?>
<div class="woocommerce-product-gallery luxury-gallery" data-luxury-gallery>

	<?php if ( ! empty( $image_ids ) ) : ?>

		<?php foreach ( $image_ids as $index => $attachment_id ) : ?>
			<?php
			$full = wp_get_attachment_image_src( $attachment_id, 'full' );
			$alt  = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
			if ( '' === $alt ) {
				$alt = $product->get_name() . ' — ' . ( $index + 1 );
			}
			?>
			<figure class="luxury-gallery__item">
				<a href="<?php echo esc_url( $full ? $full[0] : '' ); ?>"
					data-luxury-gallery-link
					aria-label="<?php echo esc_attr( sprintf( __( 'View product image %d of %d', 'thirtythree-store' ), (int) $index + 1, count( $image_ids ) ) ); ?>">
					<?php
					echo wp_get_attachment_image(
						$attachment_id,
						'woocommerce_single',
						false,
						array(
							'class'         => 'luxury-gallery__img',
							'alt'           => $alt,
							'loading'       => 0 === $index ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $index ? 'high' : 'auto',
							'decoding'      => 'async',
						)
					);
					?>
				</a>
			</figure>
		<?php endforeach; ?>

	<?php else : ?>

		<?php echo wc_placeholder_img( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<?php endif; ?>

</div>
