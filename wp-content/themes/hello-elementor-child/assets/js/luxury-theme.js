/**
 * 33 Store — Luxury Monochrome theme JS
 *
 * Vanilla ES6. Powers:
 *  - slide-out cart drawer
 *  - full-screen mobile menu & search overlay
 *  - sticky header state
 *  - AJAX quick-add on the shop grid (WooCommerce-aware, with a
 *    graceful "preview" fallback so the static demo works too)
 *  - lightweight product image viewer (replaces Photoswipe)
 *
 * @package 33StoreLuxury
 */
(function () {
	'use strict';

	var CFG = window.LUXURY || {};
	var body = document.body;
	var drawer = null;
	var menu = null;
	var search = null;
	var galleryViewer = null;
	var toast = null;
	var lastFocused = null;

	/* ----------------------------------------------------------
	 * Helpers
	 * -------------------------------------------------------- */
	function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
	function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

	function lockScroll(lock) {
		if (lock) {
			body.classList.add('luxury-scroll-lock');
		} else {
			body.classList.remove('luxury-scroll-lock');
		}
	}

	function setOverlayState(el, open, opener) {
		if (!el) { return; }
		if (open) {
			lastFocused = opener || document.activeElement;
			el.classList.add('is-open');
			el.setAttribute('aria-hidden', 'false');
			lockScroll(true);
			var auto = qs('[data-luxury-search-input]', el);
			if (auto) {
				window.setTimeout(function () { auto.focus(); }, 120);
			}
		} else {
			el.classList.remove('is-open');
			el.setAttribute('aria-hidden', 'true');
			if (!qs('.luxury-drawer.is-open') && !qs('.luxury-menu.is-open') && !qs('.luxury-search.is-open')) {
				lockScroll(false);
			}
			if (lastFocused && lastFocused.focus) {
				lastFocused.focus();
			}
		}
	}

	function closeAll() {
		setOverlayState(drawer, false);
		setOverlayState(menu, false);
		setOverlayState(search, false);
	}

	function updateCount(count) {
		var n = parseInt(count, 10) || 0;
		qsa('[data-luxury-cart-count]').forEach(function (el) {
			el.textContent = String(n);
			el.classList.toggle('is-empty', n === 0);
		});
	}

	function showToast(message) {
		if (!toast) {
			toast = document.createElement('div');
			toast.className = 'luxury-toast';
			toast.setAttribute('role', 'status');
			body.appendChild(toast);
		}
		toast.textContent = message || '';
		toast.classList.add('is-visible');
		window.clearTimeout(showToast._t);
		showToast._t = window.setTimeout(function () {
			toast.classList.remove('is-visible');
		}, 2600);
	}

	/* ----------------------------------------------------------
	 * Fragment refresh (WooCommerce)
	 * -------------------------------------------------------- */
	function refreshFragments() {
		if (!CFG.fragmentsEndpoint) { return; }
		fetch(CFG.fragmentsEndpoint, { credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (!data || !data.fragments) { return; }
				Object.keys(data.fragments).forEach(function (selector) {
					var html = data.fragments[selector];
					qsa(selector).forEach(function (el) {
						el.outerHTML = html;
					});
				});
			})
			.catch(function () { /* silent */ });
	}

	function triggerWooEvent(eventName, payload) {
		if (typeof window.jQuery === 'function') {
			window.jQuery(document.body).trigger(eventName, payload);
		}
	}

	/* ----------------------------------------------------------
	 * Quick add (shop grid)
	 * -------------------------------------------------------- */
	function quickAdd(button) {
		var productId = button.getAttribute('data-product_id');
		var quantity = button.getAttribute('data-quantity') || '1';

		// Preview / no-WooCommerce fallback: simulate success.
		if (!CFG.wcActive || !CFG.wcAjaxUrl) {
			var current = parseInt(qs('[data-luxury-cart-count]').textContent, 10) || 0;
			updateCount(current + parseInt(quantity, 10) || 1);
			openDrawer(button);
			showToast(CFG.i18n ? CFG.i18n.addedToBag : 'Added to bag');
			return;
		}

		var params = new URLSearchParams();
		params.set('product_id', productId);
		params.set('quantity', quantity);

		fetch(CFG.wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'), {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: params.toString()
		})
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data && data.fragments) {
					Object.keys(data.fragments).forEach(function (selector) {
						qsa(selector).forEach(function (el) { el.outerHTML = data.fragments[selector]; });
					});
					triggerWooEvent('added_to_cart', [data.fragments, data.cart_hash, button]);
				} else {
					refreshFragments();
				}
				openDrawer(button);
				showToast(CFG.i18n ? CFG.i18n.addedToBag : 'Added to bag');
			})
			.catch(function () {
				// Fall back to the native WooCommerce flow.
				window.location.href = button.href || (CFG.wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'));
			});
	}

	/* ----------------------------------------------------------
	 * Drawer / menu / search openers
	 * -------------------------------------------------------- */
	function openDrawer(opener) {
		closeAll();
		setOverlayState(drawer, true, opener);
	}

	document.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-luxury-drawer-open]');
		if (opener) { openDrawer(opener); return; }

		if (e.target.closest('[data-luxury-drawer-close]')) { closeAll(); return; }

		if (e.target.closest('[data-luxury-menu-open]')) {
			closeAll();
			setOverlayState(menu, true, e.target.closest('[data-luxury-menu-open]'));
			return;
		}
		if (e.target.closest('[data-luxury-menu-close]')) { closeAll(); return; }

		if (e.target.closest('[data-luxury-search-open]')) {
			closeAll();
			setOverlayState(search, true, e.target.closest('[data-luxury-search-open]'));
			return;
		}
		if (e.target.closest('[data-luxury-search-close]')) { closeAll(); return; }
	}, false);

	/* Quick add — capture phase so it wins over WooCommerce's
	 * delegated bubble-phase handler (prevents double adds). */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-luxury-quick-add]');
		if (!btn) { return; }
		if (btn.getAttribute('data-luxury-quick-add') !== '1') { return; }

		e.preventDefault();
		e.stopPropagation();
		quickAdd(btn);
	}, true);

	/* ----------------------------------------------------------
	 * Keyboard
	 * -------------------------------------------------------- */
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' || e.key === 'Esc') {
			closeAll();
			closeViewer();
		}
	});

	/* ----------------------------------------------------------
	 * Sticky header state
	 * -------------------------------------------------------- */
	var header = qs('[data-luxury-header]');
	var ticking = false;

	function onScroll() {
		if (header) {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		}
		ticking = false;
	}

	window.addEventListener('scroll', function () {
		if (!ticking) {
			window.requestAnimationFrame(onScroll);
			ticking = true;
		}
	}, { passive: true });

	/* ----------------------------------------------------------
	 * Product image viewer (replaces Photoswipe)
	 * -------------------------------------------------------- */
	function openViewer(src, alt) {
		if (!src) { return; }
		if (!galleryViewer) {
			galleryViewer = document.createElement('div');
			galleryViewer.className = 'luxury-viewer';
			galleryViewer.innerHTML =
				'<div class="luxury-viewer__backdrop" data-luxury-viewer-close></div>' +
				'<figure class="luxury-viewer__frame">' +
				'<button type="button" class="luxury-viewer__close" aria-label="Close image viewer" data-luxury-viewer-close>&times;</button>' +
				'<img src="" alt="" />' +
				'</figure>';
			body.appendChild(galleryViewer);
		}
		var img = qs('img', galleryViewer);
		img.src = src;
		img.alt = alt || '';
		galleryViewer.classList.add('is-open');
		lockScroll(true);
	}

	function closeViewer() {
		if (galleryViewer) {
			galleryViewer.classList.remove('is-open');
			lockScroll(false);
		}
	}

	document.addEventListener('click', function (e) {
		if (e.target.closest('[data-luxury-viewer-close]')) { closeViewer(); return; }
		if (e.target.closest('[data-luxury-gallery-link]')) {
			var link = e.target.closest('[data-luxury-gallery-link]');
			if (link && link.href) {
				e.preventDefault();
				var imgEl = qs('img', link);
				openViewer(link.href, imgEl ? imgEl.alt : '');
			}
		}
	}, false);

	/* ----------------------------------------------------------
	 * Init
	 * -------------------------------------------------------- */
	function init() {
		drawer = qs('[data-luxury-drawer]');
		menu = qs('[data-luxury-menu]');
		search = qs('[data-luxury-search]');

		if (typeof CFG.cartCount === 'number') {
			updateCount(CFG.cartCount);
		}

		onScroll();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
