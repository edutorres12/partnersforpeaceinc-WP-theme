import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
	ToggleControl,
	RangeControl,
	SelectControl,
} from '@wordpress/components';

// Static grid classes so Tailwind can detect them during purge.
// Mirror of the lookup table in render.php.
const GRID_COLS = {
	1: 'md:grid-cols-1',
	2: 'md:grid-cols-2',
	3: 'md:grid-cols-3',
	4: 'md:grid-cols-4',
};

// Overlay tint slug → Tailwind bg utility. Mirror of the map in render.php.
const OVERLAY_BG = {
	primary: 'bg-primary',
	secondary: 'bg-secondary',
	accent: 'bg-accent',
	base: 'bg-canvas',
	'on-dark': 'bg-on-dark',
	muted: 'bg-muted',
	'primary-soft': 'bg-primary-soft',
	surface: 'bg-surface',
	white: 'bg-white',
};

// Friendly overlay names — matches the group block's "Overlay: *" styles
// registered in inc/setup.php so authors see the same palette everywhere.
const OVERLAY_COLORS = [
	{ label: 'Secondary (dark)', value: 'secondary' },
	{ label: 'Primary', value: 'primary' },
	{ label: 'Accent', value: 'accent' },
	{ label: 'Base', value: 'base' },
	{ label: 'On dark', value: 'on-dark' },
	{ label: 'Muted', value: 'muted' },
	{ label: 'Primary soft', value: 'primary-soft' },
	{ label: 'Surface', value: 'surface' },
	{ label: 'White', value: 'white' },
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		items,
		heading,
		intro,
		showCta,
		ctaText,
		ctaUrl,
		microcopy,
		backgroundImageUrl,
		overlayOpacity,
		overlayColor,
		usePlaceholder,
	} = attributes;

	const overlayBg = OVERLAY_BG[ overlayColor ] || 'bg-secondary';

	const update = ( i, key, value ) => {
		const next = [ ...items ];
		next[ i ] = { ...next[ i ], [ key ]: value };
		setAttributes( { items: next } );
	};
	const remove = ( i ) =>
		setAttributes( { items: items.filter( ( _, idx ) => idx !== i ) } );
	const add = () =>
		setAttributes( {
			items: [ ...items, { title: 'Step', text: '' } ],
		} );

	// Mirror render.php's photo-variant branching so the editor preview matches
	// the frontend (bordered cards over a dark overlay) instead of the plain
	// fallback. has-image is true when an image is set or the placeholder is on.
	const hasImage = backgroundImageUrl !== '' || usePlaceholder;

	const blockProps = useBlockProps( {
		className: `wptpl-steps text-center${
			hasImage ? ' relative overflow-hidden text-white py-[6.25rem]' : ''
		}`,
	} );

	const count = Math.max( 1, Math.min( 4, items.length || 1 ) );
	const gridCols = GRID_COLS[ count ] || 'md:grid-cols-3';

	const cardClass = hasImage
		? 'relative border border-canvas rounded-lg pt-16 px-6 pb-6 h-full'
		: '';
	const numberClass = hasImage
		? 'w-20 h-20 rounded-full bg-accent text-canvas flex items-center justify-center text-2xl absolute -top-10 left-1/2 -translate-x-1/2'
		: 'w-20 h-20 rounded-full bg-accent text-white flex items-center justify-center text-2xl mx-auto mb-4';
	const titleClass = hasImage ? 'text-canvas' : '';
	const bodyClass = hasImage
		? 'text-canvas/85 mt-2 font-medium'
		: 'text-sm opacity-80 mt-2 font-medium';
	const headingClass = hasImage
		? 'wptpl-steps__heading text-canvas'
		: 'wptpl-steps__heading';
	const introClass = hasImage ? 'mt-3 text-canvas/85' : 'mt-3 text-muted';
	const headerMb = hasImage ? 'mb-20' : 'mb-12';
	const ctaClass = hasImage ? 'wptpl-btn-accent' : 'wptpl-btn-primary';
	const microcopyClass = hasImage
		? 'mt-3 text-sm text-white/70'
		: 'mt-3 text-sm text-muted';

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Section header', 'wptpl' ) }
					initialOpen
				>
					<TextControl
						label={ __( 'Heading', 'wptpl' ) }
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v } ) }
					/>
					<TextControl
						label={ __( 'Intro', 'wptpl' ) }
						value={ intro }
						onChange={ ( v ) => setAttributes( { intro: v } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Steps', 'wptpl' ) } initialOpen>
					{ items.map( ( item, i ) => (
						<div
							key={ i }
							style={ {
								marginBottom: 12,
								padding: 8,
								border: '1px solid #ddd',
							} }
						>
							<TextControl
								label={ `${ __( 'Title', 'wptpl' ) } ${
									i + 1
								}` }
								value={ item.title }
								onChange={ ( v ) => update( i, 'title', v ) }
							/>
							<TextareaControl
								label={ __( 'Description', 'wptpl' ) }
								value={ item.text }
								onChange={ ( v ) => update( i, 'text', v ) }
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => remove( i ) }
							>
								{ __( 'Remove', 'wptpl' ) }
							</Button>
						</div>
					) ) }
					<Button variant="primary" onClick={ add }>
						{ __( 'Add step', 'wptpl' ) }
					</Button>
				</PanelBody>
				<PanelBody
					title={ __( 'Background image', 'wptpl' ) }
					initialOpen={ false }
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									backgroundImageUrl: media.url,
									backgroundImageAlt: media.alt || '',
								} )
							}
							allowedTypes={ [ 'image' ] }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open }>
									{ backgroundImageUrl
										? __( 'Change image', 'wptpl' )
										: __( 'Select image', 'wptpl' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImageUrl && (
						<Button
							isDestructive
							isSmall
							onClick={ () =>
								setAttributes( {
									backgroundImageUrl: '',
									backgroundImageAlt: '',
								} )
							}
							style={ { marginLeft: 8 } }
						>
							{ __( 'Remove', 'wptpl' ) }
						</Button>
					) }
					<SelectControl
						label={ __( 'Overlay color', 'wptpl' ) }
						value={ overlayColor }
						options={ OVERLAY_COLORS }
						onChange={ ( v ) =>
							setAttributes( { overlayColor: v } )
						}
					/>
					<RangeControl
						label={ __( 'Overlay opacity', 'wptpl' ) }
						value={ overlayOpacity }
						min={ 0 }
						max={ 0.9 }
						step={ 0.05 }
						onChange={ ( v ) =>
							setAttributes( { overlayOpacity: v } )
						}
					/>
					<ToggleControl
						label={ __(
							'Use placeholder image when none selected',
							'wptpl'
						) }
						checked={ usePlaceholder }
						onChange={ ( v ) =>
							setAttributes( { usePlaceholder: v } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Call to action', 'wptpl' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show CTA button', 'wptpl' ) }
						checked={ showCta }
						onChange={ ( v ) => setAttributes( { showCta: v } ) }
					/>
					{ showCta && (
						<>
							<TextControl
								label={ __( 'Button text', 'wptpl' ) }
								value={ ctaText }
								onChange={ ( v ) =>
									setAttributes( { ctaText: v } )
								}
							/>
							<TextControl
								label={ __( 'Button URL', 'wptpl' ) }
								value={ ctaUrl }
								onChange={ ( v ) =>
									setAttributes( { ctaUrl: v } )
								}
							/>
							<TextControl
								label={ __( 'Microcopy below CTA', 'wptpl' ) }
								value={ microcopy }
								onChange={ ( v ) =>
									setAttributes( { microcopy: v } )
								}
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ hasImage && (
					<>
						<div
							className={ `absolute inset-0 ${ overlayBg }` }
							aria-hidden="true"
						/>
						{ backgroundImageUrl && (
							<img
								className="absolute inset-0 w-full h-full object-cover"
								src={ backgroundImageUrl }
								alt=""
								aria-hidden="true"
							/>
						) }
						<div
							className={ `absolute inset-0 ${ overlayBg }` }
							style={ { opacity: overlayOpacity } }
							aria-hidden="true"
						/>
					</>
				) }
				<div
					className={ `wptpl-container-md${
						hasImage ? ' relative z-10' : ''
					}` }
				>
					{ ( heading || intro ) && (
						<div className={ headerMb }>
							{ heading && (
								<h2 className={ headingClass }>{ heading }</h2>
							) }
							{ intro && (
								<p className={ introClass }>{ intro }</p>
							) }
						</div>
					) }
					<div className={ `grid gap-6 grid-cols-1 ${ gridCols }` }>
						{ items.map( ( item, i ) => (
							<div key={ i } className={ cardClass }>
								<div
									className={ numberClass }
									style={ {
										fontFamily:
											'Arial, Helvetica, sans-serif',
										fontWeight: 700,
									} }
								>
									{ i + 1 }
								</div>
								{ /* Author-typed HTML (e.g. <br>); rendered so the
								     preview matches the frontend's wp_kses_post output. */ }
								<h3
									className={ titleClass }
									dangerouslySetInnerHTML={ {
										__html: item.title || '',
									} }
								/>
								<p
									className={ bodyClass }
									dangerouslySetInnerHTML={ {
										__html: item.text || '',
									} }
								/>
							</div>
						) ) }
					</div>
					{ showCta && (
						<div className="mt-10">
							<a
								href={ ctaUrl }
								className={ ctaClass }
								onClick={ ( e ) => e.preventDefault() }
							>
								{ ctaText }
							</a>
							{ microcopy && (
								<p className={ microcopyClass }>
									{ microcopy }
								</p>
							) }
						</div>
					) }
				</div>
			</div>
		</>
	);
}
