/**
 * Mobile navigation for the primary header menu.
 *
 * The hamburger opens #wptpl-mobile-nav as a full-screen overlay that sits below
 * the header bar and scrolls internally — it never pushes the page, so the
 * document layout (including the 404 cover) is untouched whether the menu is
 * open or closed. Keeps aria-expanded in sync, morphs the icon (is-open), locks
 * background scroll, and closes on Escape or an outside click. Parent items
 * (e.g. Services) collapse by default with a caret disclosure button. The
 * overlay is offset by the live header height, published as `--wptpl-header-h`.
 * Progressive enhancement: with JS off the panel stays hidden and the desktop
 * nav still works above the lg breakpoint.
 */
( function () {
	const toggle = document.getElementById( 'wptpl-nav-toggle' );
	const panel = document.getElementById( 'wptpl-mobile-nav' );

	if ( ! toggle || ! panel ) {
		return;
	}

	const header = document.querySelector( '.wptpl-header' );
	const parents = panel.querySelectorAll( '.menu-item-has-children' );

	// Make the panel itself programmatically focusable (but not a Tab stop) so
	// that on open we can park focus on the container instead of the first link.
	// Focusing a link directly would move focus there WITHOUT a visible ring
	// (:focus-visible only matches keyboard focus), so the first Tab would jump
	// past it — landing the visible ring on the second item. Parking on the
	// container means the user's first Tab lands, visibly, on the first link.
	panel.setAttribute( 'tabindex', '-1' );

	// Publish the header's bottom edge (so the overlay starts exactly there) and
	// the WordPress admin-bar height (0 when logged out, used to pin the sticky
	// header below it). Re-run on open and on resize/orientation change.
	function syncHeaderHeight() {
		const root = document.documentElement;
		const adminBar = document.getElementById( 'wpadminbar' );
		root.style.setProperty(
			'--wptpl-adminbar-h',
			( adminBar ? adminBar.offsetHeight : 0 ) + 'px'
		);
		if ( header ) {
			// Use the header's real on-screen bottom edge so the overlay meets it
			// exactly — this already accounts for the admin bar and any sub-pixel
			// rounding, avoiding a gap that the height-plus-admin-bar arithmetic
			// left. Measured at open time, when the header is at the top.
			const bottom = Math.round( header.getBoundingClientRect().bottom );
			root.style.setProperty( '--wptpl-header-h', bottom + 'px' );
		}
	}

	// Collapse every open submenu so the panel always opens in a tidy, closed
	// state (called when the whole menu closes).
	function collapseSubmenus() {
		parents.forEach( function ( item ) {
			item.classList.remove( 'is-open' );
			const button = item.querySelector( '.wptpl-submenu-toggle' );
			if ( button ) {
				button.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	// Page regions to hide from keyboard/AT while the full-screen overlay is
	// open, so focus and screen-reader browsing can't wander behind it. The
	// header is left alone because it holds the toggle and the panel itself.
	const backgroundRegions = [
		document.querySelector( 'main' ),
		document.querySelector( '.wptpl-footer' ),
	].filter( Boolean );

	// Visible, focusable elements currently inside the panel.
	function getPanelFocusables() {
		return Array.prototype.filter.call(
			panel.querySelectorAll(
				'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
			),
			function ( el ) {
				return null !== el.offsetParent;
			}
		);
	}

	// Keep Tab within the menu. The toggle (the X) sits outside the panel, so
	// it's included at the start of the cycle to stay keyboard-reachable.
	function trapFocus( event ) {
		if ( 'Tab' !== event.key ) {
			return;
		}
		const focusables = getPanelFocusables();
		if ( ! focusables.length ) {
			return;
		}
		const sequence = [ toggle ].concat( focusables );
		const first = sequence[ 0 ];
		const last = sequence[ sequence.length - 1 ];
		if ( event.shiftKey && panel.ownerDocument.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if (
			! event.shiftKey &&
			panel.ownerDocument.activeElement === last
		) {
			event.preventDefault();
			first.focus();
		}
	}

	function setOpen( open ) {
		// Captured before we hide the panel: hiding moves focus to <body>, so we
		// check membership first to know whether to restore focus on close.
		const focusWasInside = panel.contains(
			panel.ownerDocument.activeElement
		);

		if ( open ) {
			syncHeaderHeight();
		}

		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		toggle.setAttribute( 'aria-label', open ? 'Close menu' : 'Open menu' );
		toggle.classList.toggle( 'is-open', open );
		panel.hidden = ! open;
		// The overlay is fixed, so locking the background is correct here (it
		// keeps the page from scrolling behind the menu). The panel keeps its
		// own scroll via overflow-y in CSS.
		document.documentElement.classList.toggle( 'wptpl-nav-open', open );

		// Make the rest of the page inert (non-focusable, hidden from AT) while
		// the overlay is open.
		backgroundRegions.forEach( function ( el ) {
			if ( open ) {
				el.setAttribute( 'inert', '' );
			} else {
				el.removeAttribute( 'inert' );
			}
		} );

		if ( open ) {
			document.addEventListener( 'keydown', trapFocus );
			// Park focus on the panel container. The first Tab then lands, with a
			// visible ring, on the first menu link (About) rather than skipping to
			// the second item.
			panel.focus();
		} else {
			document.removeEventListener( 'keydown', trapFocus );
			collapseSubmenus();
			// Return focus to the toggle if it was left inside the now-hidden
			// panel (e.g. the menu was closed by an outside click).
			if ( focusWasInside ) {
				toggle.focus();
			}
		}
	}

	// Give each parent item a caret button that toggles its children. The parent
	// link is untouched, so tapping the label still navigates to its own page.
	parents.forEach( function ( item, index ) {
		const submenu = item.querySelector( '.sub-menu' );
		const link = item.querySelector( ':scope > a' );

		if ( ! submenu || ! link ) {
			return;
		}

		if ( ! submenu.id ) {
			submenu.id = 'wptpl-submenu-' + index;
		}

		const button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'wptpl-submenu-toggle';
		button.setAttribute( 'aria-expanded', 'false' );
		button.setAttribute( 'aria-controls', submenu.id );
		// Describe the action, not just the section, so the disclosure isn't
		// announced identically to its sibling link (e.g. "Toggle submenu:
		// Services" vs the "Services" link).
		button.setAttribute(
			'aria-label',
			'Toggle submenu: ' + link.textContent.trim()
		);

		button.addEventListener( 'click', function () {
			const open = item.classList.toggle( 'is-open' );
			button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );

		item.insertBefore( button, submenu );
	} );

	syncHeaderHeight();
	window.addEventListener( 'resize', syncHeaderHeight );

	toggle.addEventListener( 'click', function () {
		setOpen( panel.hidden );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && ! panel.hidden ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( panel.hidden ) {
			return;
		}
		if (
			! panel.contains( event.target ) &&
			! toggle.contains( event.target )
		) {
			setOpen( false );
		}
	} );
} )();
