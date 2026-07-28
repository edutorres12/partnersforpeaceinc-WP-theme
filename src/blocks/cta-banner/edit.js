import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	PanelColorSettings,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	RangeControl,
	Button,
} from '@wordpress/components';

const STYLE_OPTIONS = [
	{ label: __( 'Auto (by theme / photo)', 'wptpl' ), value: 'auto' },
	{ label: __( 'Accent (accent)', 'wptpl' ), value: 'accent' },
	{ label: __( 'Primary (photo)', 'wptpl' ), value: 'soft' },
	{ label: __( 'Canvas', 'wptpl' ), value: 'canvas' },
	{ label: __( 'Secondary (primary)', 'wptpl' ), value: 'primary' },
	{ label: __( 'Outline', 'wptpl' ), value: 'outline' },
];

function resolveBtnClass( style, hasImage, isDark ) {
	switch ( style ) {
		case 'accent':
			return 'wptpl-btn-accent';
		case 'soft':
			return 'wptpl-btn-photo';
		case 'canvas':
			return 'wptpl-btn bg-canvas text-contrast';
		case 'primary':
			return 'wptpl-btn-primary';
		case 'outline':
			return 'wptpl-btn-outline';
		default:
			if ( hasImage ) {
				return 'wptpl-btn-photo';
			}
			if ( isDark ) {
				return 'wptpl-btn bg-canvas text-contrast';
			}
			return 'wptpl-btn-primary';
	}
}

export default function Edit( { attributes, setAttributes } ) {
	const {
		eyebrow,
		headline,
		text,
		ctaText,
		ctaUrl,
		ctaStyle,
		secondaryCtaText,
		secondaryCtaUrl,
		secondaryCtaStyle,
		secondaryCtaTextColor,
		buttonLayout,
		theme,
		headlineColor,
		bodyColor,
		eyebrowColor,
		backgroundImageUrl,
		backgroundImageAlt,
		overlayOpacity,
	} = attributes;
	const isDark = theme === 'dark';
	const hasImage = !! backgroundImageUrl;
	const hasSecondary = !! secondaryCtaText;

	let bgClass;
	if ( hasImage ) {
		bgClass = 'text-white';
	} else if ( isDark ) {
		bgClass = 'bg-secondary text-white';
	} else {
		bgClass = 'bg-canvas text-contrast';
	}
	const wrapperClass = `wptpl-cta-banner relative text-center py-[6.25rem] px-6 overflow-hidden ${ bgClass }`;
	const blockProps = useBlockProps( { className: wrapperClass } );

	const btnClass = resolveBtnClass( ctaStyle, hasImage, isDark );
	const secBtnClass = resolveBtnClass( secondaryCtaStyle, hasImage, isDark );

	return (
		<>
			<InspectorControls>
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
				</PanelBody>
				<PanelBody title={ __( 'Primary CTA', 'wptpl' ) } initialOpen>
					<TextControl
						label={ __( 'Button text', 'wptpl' ) }
						value={ ctaText }
						onChange={ ( v ) => setAttributes( { ctaText: v } ) }
					/>
					<TextControl
						label={ __( 'Button URL', 'wptpl' ) }
						value={ ctaUrl }
						onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
					/>
					<SelectControl
						label={ __( 'Button color', 'wptpl' ) }
						value={ ctaStyle }
						options={ STYLE_OPTIONS }
						onChange={ ( v ) => setAttributes( { ctaStyle: v } ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Secondary CTA', 'wptpl' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __(
							'Button text (leave empty to hide)',
							'wptpl'
						) }
						value={ secondaryCtaText }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaText: v } )
						}
					/>
					<TextControl
						label={ __( 'Button URL', 'wptpl' ) }
						value={ secondaryCtaUrl }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaUrl: v } )
						}
					/>
					<SelectControl
						label={ __( 'Button color', 'wptpl' ) }
						value={ secondaryCtaStyle }
						options={ STYLE_OPTIONS }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaStyle: v } )
						}
					/>
					<TextControl
						label={ __( 'Text color (hex, optional)', 'wptpl' ) }
						value={ secondaryCtaTextColor }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaTextColor: v } )
						}
					/>
					<SelectControl
						label={ __( 'Buttons layout', 'wptpl' ) }
						value={ buttonLayout }
						options={ [
							{
								label: __( 'Side by side', 'wptpl' ),
								value: 'row',
							},
							{
								label: __( 'Stacked', 'wptpl' ),
								value: 'column',
							},
						] }
						onChange={ ( v ) =>
							setAttributes( { buttonLayout: v } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Theme', 'wptpl' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Theme', 'wptpl' ) }
						value={ theme }
						options={ [
							{
								label: __( 'Dark (closing CTA)', 'wptpl' ),
								value: 'dark',
							},
							{
								label: __( 'Light (book directly)', 'wptpl' ),
								value: 'light',
							},
						] }
						onChange={ ( v ) => setAttributes( { theme: v } ) }
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Text colors', 'wptpl' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: headlineColor,
							onChange: ( v ) =>
								setAttributes( { headlineColor: v || '' } ),
							label: __( 'Headline color', 'wptpl' ),
						},
						{
							value: bodyColor,
							onChange: ( v ) =>
								setAttributes( { bodyColor: v || '' } ),
							label: __( 'Text color', 'wptpl' ),
						},
						{
							value: eyebrowColor,
							onChange: ( v ) =>
								setAttributes( { eyebrowColor: v || '' } ),
							label: __( 'Eyebrow color', 'wptpl' ),
						},
					] }
				/>
			</InspectorControls>

			<div { ...blockProps }>
				{ hasImage && (
					<>
						<img
							src={ backgroundImageUrl }
							alt={ backgroundImageAlt }
							className="absolute inset-0 w-full h-full object-cover"
							aria-hidden="true"
						/>
						<div
							className="absolute inset-0 bg-secondary"
							style={ { opacity: overlayOpacity } }
							aria-hidden="true"
						></div>
					</>
				) }
				<div className="relative z-10">
					<RichText
						tagName="p"
						className={ `text-xs uppercase tracking-widest mb-3 ${
							hasImage || isDark ? 'text-white/70' : 'text-muted'
						}` }
						style={
							eyebrowColor ? { color: eyebrowColor } : undefined
						}
						value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						placeholder={ __( 'Eyebrow (optional)', 'wptpl' ) }
					/>
					<RichText
						tagName="h2"
						className="max-w-3xl mx-auto"
						style={
							headlineColor ? { color: headlineColor } : undefined
						}
						value={ headline }
						onChange={ ( v ) => setAttributes( { headline: v } ) }
						placeholder={ __( 'Headline…', 'wptpl' ) }
					/>
					<RichText
						tagName="p"
						className={ `mt-4 max-w-2xl mx-auto ${
							hasImage || isDark ? 'text-white/70' : 'text-muted'
						}` }
						style={ bodyColor ? { color: bodyColor } : undefined }
						value={ text }
						onChange={ ( v ) => setAttributes( { text: v } ) }
						placeholder={ __( 'Supporting text…', 'wptpl' ) }
					/>
					<div
						className={ `mt-6 flex gap-3 justify-center ${
							buttonLayout === 'column'
								? 'flex-col items-center'
								: 'flex-wrap'
						}` }
					>
						<a
							href={ ctaUrl }
							className={ btnClass }
							onClick={ ( e ) => e.preventDefault() }
						>
							{ ctaText }
						</a>
						{ hasSecondary && (
							<a
								href={ secondaryCtaUrl }
								className={ secBtnClass }
								style={
									secondaryCtaTextColor
										? { color: secondaryCtaTextColor }
										: undefined
								}
								onClick={ ( e ) => e.preventDefault() }
							>
								{ secondaryCtaText }
							</a>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
