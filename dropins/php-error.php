<?php
/**
 * Branded fatal-error / 500 page (self-contained).
 *
 * WordPress loads this file (when copied to `wp-content/php-error.php`) instead
 * of its plain "There has been a critical error on this website" screen. It runs
 * AFTER a fatal PHP error, so WordPress, the active theme, and its stylesheet are
 * NOT available — everything here must be self-contained: inline CSS, no theme
 * functions, no external assets required to render. Brand fonts load from Google
 * Fonts if reachable, but the page is fully styled without them.
 *
 * The same file also works as an Apache/Nginx `ErrorDocument 500` target for
 * server-level failures that never reach PHP. See dropins/README.md to install.
 *
 * Colors mirror the theme tokens (theme.json / tailwind.config.js):
 *   cream #e3ded4 · sage #7a8461 · olive #2f3328 · clay #8f6a4f · taupe #5c5648
 *
 * @package soywd
 */

if ( ! headers_sent() ) {
	http_response_code( 500 );
	header( 'Content-Type: text/html; charset=utf-8' );
	// Ask well-behaved clients to retry shortly rather than hammering a down site.
	header( 'Retry-After: 120' );
}
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>We&rsquo;ll be right back &middot; Seasons of You Therapy</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@600;700;800&family=Quicksand:wght@400;500&display=swap" rel="stylesheet">
	<style>
		:root {
			--cream: #e3ded4;
			--sage: #7a8461;
			--olive: #2f3328;
			--clay: #8f6a4f;
			--clay-deep: #73513c;
			--taupe: #5c5648;
			--cream-light: #e6ded3;
		}
		* { box-sizing: border-box; }
		html, body { height: 100%; margin: 0; }
		body {
			background-color: var(--cream);
			color: var(--olive);
			font-family: 'Quicksand', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
			-webkit-font-smoothing: antialiased;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 1.5rem;
			line-height: 1.6;
		}
		.wrap {
			width: 100%;
			max-width: 34rem;
			text-align: center;
		}
		.eyebrow {
			font-family: 'Urbanist', ui-sans-serif, system-ui, sans-serif;
			font-size: 0.75rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.18em;
			color: var(--taupe);
			margin: 0 0 1rem;
		}
		.code {
			font-family: 'Urbanist', ui-sans-serif, system-ui, sans-serif;
			font-weight: 800;
			font-size: clamp(4.5rem, 16vw, 8rem);
			line-height: 1;
			letter-spacing: -0.02em;
			color: var(--sage);
			margin: 0 0 1rem;
		}
		h1 {
			font-family: 'Urbanist', ui-sans-serif, system-ui, sans-serif;
			font-weight: 700;
			font-size: clamp(1.6rem, 4vw, 2.25rem);
			line-height: 1.2;
			color: var(--sage);
			margin: 0 0 1.25rem;
		}
		p.lead {
			font-size: 1.05rem;
			color: var(--taupe);
			margin: 0 auto 2rem;
			max-width: 30rem;
		}
		.btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border-radius: 0.375rem;
			padding: 0.875rem 1.75rem;
			font-family: 'Urbanist', ui-sans-serif, system-ui, sans-serif;
			font-size: 0.9rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.2em;
			text-decoration: none;
			background-color: var(--clay);
			color: var(--cream-light);
			box-shadow: 0 2px 5px rgba(47, 51, 40, 0.18);
			transition: background-color 200ms ease-out, box-shadow 200ms ease-out;
		}
		.btn:hover, .btn:focus {
			background-color: var(--clay-deep);
			box-shadow: 0 5px 12px rgba(47, 51, 40, 0.22);
		}
		.note {
			margin-top: 2.5rem;
			font-size: 0.85rem;
			color: var(--taupe);
			opacity: 0.85;
		}
	</style>
</head>
<body>
	<main class="wrap">
		<p class="eyebrow">Seasons of You Therapy</p>
		<p class="code" aria-hidden="true">500</p>
		<h1>We&rsquo;ll be right back</h1>
		<p class="lead">
			Something went wrong on our end. Please try again in a few minutes.
		</p>
		<a class="btn" href="/">Try again</a>
		<p class="note">Thanks for your patience.</p>
	</main>
</body>
</html>
