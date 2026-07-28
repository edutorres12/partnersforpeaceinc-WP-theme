/**
 * Blog hub category filter.
 *
 * Wires wptpl/category-filter pills to wptpl/post-grid cards, all on the same
 * page with no reload:
 *   - "All": show the featured hero (wptpl/featured-post's section) and every
 *     grid card except the featured one (it already shows as the hero).
 *   - A category: hide the featured hero and show only the grid cards in that
 *     category — including the featured post if it belongs there (its grid item
 *     is hidden by default, so this is where it reappears).
 *
 * The featured section is matched by `.wptpl-featured-section` (add that class
 * to the featured section group). No-ops on pages without the filter.
 */
( function () {
	function init() {
		const filter = document.querySelector( '.wptpl-cat-filter' );
		if ( ! filter ) {
			return;
		}

		const featuredSection = document.querySelector(
			'.wptpl-featured-section'
		);
		const items = Array.prototype.slice.call(
			document.querySelectorAll( '.wptpl-post-grid__item' )
		);

		function apply( slug ) {
			const isAll = 'all' === slug;

			if ( featuredSection ) {
				featuredSection.hidden = ! isAll;
			}

			items.forEach( function ( item ) {
				const cats = ( item.getAttribute( 'data-categories' ) || '' )
					.split( ' ' )
					.filter( Boolean );
				const isFeatured = '1' === item.getAttribute( 'data-featured' );
				const show = isAll ? ! isFeatured : cats.indexOf( slug ) !== -1;
				item.hidden = ! show;
			} );
		}

		const reduce = window.matchMedia(
			'(prefers-reduced-motion: reduce)'
		).matches;

		// Scroll the strip so the given pill is centred (clamped at the ends).
		// No-op on desktop, where the row doesn't overflow. The custom scrollbar
		// follows automatically via its own scroll listener.
		function centerPill( pill, smooth ) {
			const fRect = filter.getBoundingClientRect();
			const pRect = pill.getBoundingClientRect();
			const pillLeft = pRect.left - fRect.left + filter.scrollLeft;
			const target =
				pillLeft - ( filter.clientWidth - pill.clientWidth ) / 2;
			const max = filter.scrollWidth - filter.clientWidth;
			filter.scrollTo( {
				left: Math.max( 0, Math.min( target, max ) ),
				behavior: smooth && ! reduce ? 'smooth' : 'auto',
			} );
		}

		filter.addEventListener( 'click', function ( event ) {
			const pill = event.target.closest( '[data-filter]' );
			if ( ! pill || ! filter.contains( pill ) ) {
				return;
			}

			filter
				.querySelectorAll( '[data-filter]' )
				.forEach( function ( button ) {
					const active = button === pill;
					button.classList.toggle( 'is-active', active );
					button.setAttribute(
						'aria-pressed',
						active ? 'true' : 'false'
					);
				} );

			apply( pill.getAttribute( 'data-filter' ) );
			centerPill( pill, true );
		} );

		// Bring the initially-active pill into view (it can start off-screen to
		// the right on a phone).
		const initialActive = filter.querySelector( '[data-filter].is-active' );
		if ( initialActive ) {
			centerPill( initialActive, false );
		}
	}

	/**
	 * Custom, always-visible scrollbar for the category filter.
	 *
	 * On mobile the filter is a horizontal scroll strip (see tailwind.css) with
	 * the native scrollbar hidden. Native mobile scrollbars are overlay — they
	 * vanish at rest — so this draws a persistent bar under the row: a track
	 * with a thumb that reflects the scroll position and can be dragged.
	 * Progressive enhancement: with JS off the row still scrolls natively. The
	 * bar hides itself whenever the row doesn't overflow (e.g. desktop).
	 */
	function initScrollbar() {
		const filter = document.querySelector( '.wptpl-cat-filter' );
		if ( ! filter ) {
			return;
		}

		const bar = document.createElement( 'div' );
		bar.className = 'wptpl-cat-scrollbar';
		const track = document.createElement( 'div' );
		track.className = 'wptpl-cat-scrollbar__track';
		const thumb = document.createElement( 'div' );
		thumb.className = 'wptpl-cat-scrollbar__thumb';
		track.appendChild( thumb );
		bar.appendChild( track );
		filter.after( bar );

		function sync() {
			const ratio = filter.clientWidth / filter.scrollWidth;
			if ( ratio >= 1 ) {
				bar.style.display = 'none';
				return;
			}
			bar.style.display = '';
			const trackW = track.clientWidth;
			const thumbW = Math.max( 28, ratio * trackW );
			const maxScroll = filter.scrollWidth - filter.clientWidth;
			const maxThumb = trackW - thumbW;
			const left =
				maxScroll > 0
					? ( filter.scrollLeft / maxScroll ) * maxThumb
					: 0;
			thumb.style.width = thumbW + 'px';
			thumb.style.transform = 'translateX(' + left + 'px)';
		}

		filter.addEventListener( 'scroll', sync, { passive: true } );
		window.addEventListener( 'resize', sync );

		let dragging = false;
		let startX = 0;
		let startScroll = 0;

		thumb.addEventListener( 'pointerdown', function ( event ) {
			dragging = true;
			startX = event.clientX;
			startScroll = filter.scrollLeft;
			thumb.setPointerCapture( event.pointerId );
			event.preventDefault();
		} );
		thumb.addEventListener( 'pointermove', function ( event ) {
			if ( ! dragging ) {
				return;
			}
			const trackW = track.clientWidth;
			const ratio = filter.clientWidth / filter.scrollWidth;
			const thumbW = Math.max( 28, ratio * trackW );
			const maxThumb = trackW - thumbW;
			const maxScroll = filter.scrollWidth - filter.clientWidth;
			const dx = event.clientX - startX;
			filter.scrollLeft =
				startScroll +
				( maxThumb > 0 ? ( dx / maxThumb ) * maxScroll : 0 );
		} );
		thumb.addEventListener( 'pointerup', function () {
			dragging = false;
		} );
		thumb.addEventListener( 'pointercancel', function () {
			dragging = false;
		} );

		sync();
	}

	function start() {
		init();
		initScrollbar();
	}

	if ( 'loading' !== document.readyState ) {
		start();
	} else {
		document.addEventListener( 'DOMContentLoaded', start );
	}
} )();
