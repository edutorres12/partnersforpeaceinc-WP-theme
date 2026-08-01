/**
 * Team bios as modal dialogs.
 *
 * The bios are written into the page as ordinary sections — one per person,
 * each carrying `wptpl-bio-modal` and an anchor id — and the roster cards above
 * them link to those anchors. That is the whole page with JavaScript off: a
 * grid of cards over a stack of full bios, and the card link jumps to one.
 *
 * This layer promotes each of those sections into a native <dialog>. The
 * browser supplies the focus trap, Escape and the backdrop; what is added here
 * is the close button, focus restoration, the background scroll lock and the
 * accessible name. Nothing is fetched and nothing is removed from the document,
 * so the bios stay in the HTML for crawlers either way.
 *
 * Deep links keep working in both directions: landing on `#therapist-3` opens
 * that bio, and opening one pushes its hash onto the history so the URL is
 * shareable and Back closes the dialog.
 *
 * The `wptpl-modal-ready` class on <html> — set by the inline bootstrap in
 * inc/enqueue.php before first paint — is what hides the bios so the page does
 * not flash its full height before this runs. If anything here throws, the
 * class comes off and the page falls back to the no-JS layout.
 */
( function () {
	const root = document.documentElement;

	function init() {
		const sections = Array.prototype.slice.call(
			document.querySelectorAll( '.wptpl-bio-modal' )
		);

		if ( ! sections.length ) {
			root.classList.remove( 'wptpl-modal-ready' );
			return;
		}

		// The element focus returns to when a dialog closes. Set on open.
		let lastTrigger = null;

		const dialogs = {};

		sections.forEach( function ( section ) {
			const id = section.id;
			if ( ! id ) {
				return;
			}

			const dialog = document.createElement( 'dialog' );
			dialog.className = 'wptpl-bio-dialog';

			// The dialog takes over the id so `#id` links and `:target` still
			// resolve to it; the section keeps a derived one for labelling.
			section.removeAttribute( 'id' );
			dialog.id = id;

			const heading = section.querySelector( 'h1, h2, h3' );
			if ( heading ) {
				heading.id = id + '-name';
				dialog.setAttribute( 'aria-labelledby', heading.id );
			}

			const close = document.createElement( 'button' );
			close.type = 'button';
			close.className = 'wptpl-bio-dialog__close';
			close.setAttribute( 'aria-label', 'Close' );
			close.innerHTML = '<span aria-hidden="true">&times;</span>';

			section.parentNode.insertBefore( dialog, section );
			dialog.appendChild( close );
			dialog.appendChild( section );

			close.addEventListener( 'click', function () {
				dialog.close();
			} );

			// Clicking the backdrop closes. The dialog's own box is the only
			// child that reports a hit, so anything outside it is the backdrop.
			dialog.addEventListener( 'click', function ( event ) {
				if ( event.target === dialog ) {
					dialog.close();
				}
			} );

			dialog.addEventListener( 'close', function () {
				document.body.classList.remove( 'wptpl-modal-open' );
				if ( lastTrigger ) {
					lastTrigger.focus();
					lastTrigger = null;
				}
				// Drop the hash so reloading does not reopen what was closed,
				// without adding another history entry.
				if ( window.location.hash === '#' + dialog.id ) {
					window.history.replaceState(
						null,
						'',
						window.location.pathname + window.location.search
					);
				}
			} );

			dialogs[ id ] = dialog;
		} );

		function open( id, trigger, push ) {
			const dialog = dialogs[ id ];
			if ( ! dialog || dialog.open ) {
				return false;
			}
			lastTrigger = trigger || null;
			document.body.classList.add( 'wptpl-modal-open' );
			dialog.showModal();
			if ( push ) {
				window.history.pushState( null, '', '#' + id );
			}
			return true;
		}

		// Any in-page link pointing at a bio opens it instead of jumping.
		document.addEventListener( 'click', function ( event ) {
			const link = event.target.closest
				? event.target.closest( 'a[href*="#"]' )
				: null;
			if ( ! link ) {
				return;
			}
			const hash = link.hash;
			if ( ! hash || ! dialogs[ hash.slice( 1 ) ] ) {
				return;
			}
			// Leave links to other documents alone.
			if (
				link.pathname !== window.location.pathname ||
				link.host !== window.location.host
			) {
				return;
			}
			if ( open( hash.slice( 1 ), link, true ) ) {
				event.preventDefault();
			}
		} );

		// Back/forward: close what is open, then open whatever the URL names.
		window.addEventListener( 'popstate', function () {
			Object.keys( dialogs ).forEach( function ( id ) {
				if ( dialogs[ id ].open ) {
					dialogs[ id ].close();
				}
			} );
			const id = window.location.hash.slice( 1 );
			if ( id ) {
				open( id, null, false );
			}
		} );

		// Landing directly on a bio.
		const initial = window.location.hash.slice( 1 );
		if ( initial ) {
			open( initial, null, false );
		}
	}

	function boot() {
		try {
			init();
		} catch ( err ) {
			document.body.classList.remove( 'wptpl-modal-open' );
			root.classList.remove( 'wptpl-modal-ready' );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
