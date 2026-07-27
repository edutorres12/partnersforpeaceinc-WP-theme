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
	{ label: __( 'Auto (by theme / photo)', 'soywd' ), value: 'auto' },
	{ label: __( 'Clay (accent)', 'soywd' ), value: 'accent' },
	{ label: __( 'Sage (photo)', 'soywd' ), value: 'sage' },
	{ label: __( 'Cream', 'soywd' ), value: 'cream' },
	{ label: __( 'Olive (primary)', 'soywd' ), value: 'primary' },
	{ label: __( 'Outline', 'soywd' ), value: 'outline' },
];

function resolveBtnClass( style, hasImage, isDark ) {
	switch ( style ) {
		case 'accent':
			return 'soywd-btn-accent';
		case 'sage':
			return 'soywd-btn-photo';
		case 'cream':
			return 'soywd-btn bg-cream text-contrast';
		case 'primary':
			return 'soywd-btn-primary';
		case 'outline':
			return 'soywd-btn-outline';
		default:
			if ( hasImage ) {
				return 'soywd-btn-photo';
			}
			if ( isDark ) {
				return 'soywd-btn bg-cream text-contrast';
			}
			return 'soywd-btn-primary';
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
		bgClass = 'bg-cream text-contrast';
	}
	const wrapperClass = `soywd-cta-banner relative text-center py-[6.25rem] px-6 overflow-hidden ${ bgClass }`;
	const blockProps = useBlockProps( { className: wrapperClass } );

	const btnClass = resolveBtnClass( ctaStyle, hasImage, isDark );
	const secBtnClass = resolveBtnClass( secondaryCtaStyle, hasImage, isDark );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Background image', 'soywd' ) }
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
										? __( 'Change image', 'soywd' )
										: __( 'Select image', 'soywd' ) }
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
							{ __( 'Remove', 'soywd' ) }
						</Button>
					) }
					<RangeControl
						label={ __( 'Overlay opacity', 'soywd' ) }
						value={ overlayOpacity }
						min={ 0 }
						max={ 0.9 }
						step={ 0.05 }
						onChange={ ( v ) =>
							setAttributes( { overlayOpacity: v } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Primary CTA', 'soywd' ) } initialOpen>
					<TextControl
						label={ __( 'Button text', 'soywd' ) }
						value={ ctaText }
						onChange={ ( v ) => setAttributes( { ctaText: v } ) }
					/>
					<TextControl
						label={ __( 'Button URL', 'soywd' ) }
						value={ ctaUrl }
						onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
					/>
					<SelectControl
						label={ __( 'Button color', 'soywd' ) }
						value={ ctaStyle }
						options={ STYLE_OPTIONS }
						onChange={ ( v ) => setAttributes( { ctaStyle: v } ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Secondary CTA', 'soywd' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __(
							'Button text (leave empty to hide)',
							'soywd'
						) }
						value={ secondaryCtaText }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaText: v } )
						}
					/>
					<TextControl
						label={ __( 'Button URL', 'soywd' ) }
						value={ secondaryCtaUrl }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaUrl: v } )
						}
					/>
					<SelectControl
						label={ __( 'Button color', 'soywd' ) }
						value={ secondaryCtaStyle }
						options={ STYLE_OPTIONS }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaStyle: v } )
						}
					/>
					<TextControl
						label={ __( 'Text color (hex, optional)', 'soywd' ) }
						value={ secondaryCtaTextColor }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaTextColor: v } )
						}
					/>
					<SelectControl
						label={ __( 'Buttons layout', 'soywd' ) }
						value={ buttonLayout }
						options={ [
							{
								label: __( 'Side by side', 'soywd' ),
								value: 'row',
							},
							{
								label: __( 'Stacked', 'soywd' ),
								value: 'column',
							},
						] }
						onChange={ ( v ) =>
							setAttributes( { buttonLayout: v } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Theme', 'soywd' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Theme', 'soywd' ) }
						value={ theme }
						options={ [
							{
								label: __( 'Dark (closing CTA)', 'soywd' ),
								value: 'dark',
							},
							{
								label: __( 'Light (book directly)', 'soywd' ),
								value: 'light',
							},
						] }
						onChange={ ( v ) => setAttributes( { theme: v } ) }
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Text colors', 'soywd' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: headlineColor,
							onChange: ( v ) =>
								setAttributes( { headlineColor: v || '' } ),
							label: __( 'Headline color', 'soywd' ),
						},
						{
							value: bodyColor,
							onChange: ( v ) =>
								setAttributes( { bodyColor: v || '' } ),
							label: __( 'Text color', 'soywd' ),
						},
						{
							value: eyebrowColor,
							onChange: ( v ) =>
								setAttributes( { eyebrowColor: v || '' } ),
							label: __( 'Eyebrow color', 'soywd' ),
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
						placeholder={ __( 'Eyebrow (optional)', 'soywd' ) }
					/>
					<RichText
						tagName="h2"
						className="max-w-3xl mx-auto"
						style={
							headlineColor ? { color: headlineColor } : undefined
						}
						value={ headline }
						onChange={ ( v ) => setAttributes( { headline: v } ) }
						placeholder={ __( 'Headline…', 'soywd' ) }
					/>
					<RichText
						tagName="p"
						className={ `mt-4 max-w-2xl mx-auto ${
							hasImage || isDark ? 'text-white/70' : 'text-muted'
						}` }
						style={ bodyColor ? { color: bodyColor } : undefined }
						value={ text }
						onChange={ ( v ) => setAttributes( { text: v } ) }
						placeholder={ __( 'Supporting text…', 'soywd' ) }
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
