import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
	ToggleControl,
	RangeControl,
	Button,
} from '@wordpress/components';

const tagsToText = ( tags ) =>
	( tags || [] )
		.map( ( t ) => ( typeof t === 'string' ? t : t.label || '' ) )
		.join( '\n' );

const textToTags = ( value ) =>
	value
		.split( '\n' )
		.map( ( line ) => line.trim() )
		.filter( Boolean );

export default function Edit( { attributes, setAttributes } ) {
	const {
		icon,
		iconImageUrl,
		eyebrow,
		title,
		text,
		titleRight,
		textRight,
		imageUrl,
		imageAlt,
		showImage,
		tags,
		ctaText,
		ctaUrl,
		ctaStyle,
		centered,
		layout,
		bordered,
		transparent,
		halfWidthCentered,
		imageOverlayColor,
		imageOverlayOpacity,
		backgroundImageUrl,
		titleColor,
		headingLevel,
	} = attributes;

	const renderImage = showImage !== false && !! imageUrl;
	const isBilingual = layout === 'bilingual';

	// Mirror render.php: when the user sets a text color (named slug or custom
	// hex), drop the default `text-muted` on the body so it inherits the
	// chosen color. Without this the editor preview shows muted body copy
	// against a dark custom background — effectively invisible.
	const hasUserText = !! (
		attributes.textColor || attributes.style?.color?.text
	);
	const bodyClass = hasUserText || transparent ? '' : 'text-muted';

	const hasBgImage = !! backgroundImageUrl;

	// A bilingual card needs more room for its two language columns, so it
	// centers at a wider reading width than the 50% half-card width.
	const centeredClass = isBilingual
		? 'soywd-card-bilingual-centered'
		: 'soywd-card-half-centered';

	const cardClasses = [
		'soywd-feature-card',
		'rounded-[14px]',
		'overflow-hidden',
		bordered ? 'border border-muted/25' : '',
		! transparent && ! hasBgImage ? 'bg-surface' : '',
		! transparent && ! hasBgImage ? 'shadow' : '',
		centered ? 'text-center' : '',
		halfWidthCentered ? centeredClass : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const blockProps = useBlockProps( {
		className: cardClasses,
		style: hasBgImage
			? {
					backgroundImage: `url(${ backgroundImageUrl })`,
					backgroundSize: 'cover',
					backgroundPosition: 'center',
					backgroundRepeat: 'no-repeat',
			  }
			: undefined,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Image', 'soywd' ) } initialOpen>
					<ToggleControl
						label={ __( 'Show image', 'soywd' ) }
						help={ __(
							'Turn off for text-only cards (no placeholder).',
							'soywd'
						) }
						checked={ showImage !== false }
						onChange={ ( v ) => setAttributes( { showImage: v } ) }
					/>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									imageUrl: media.url,
									imageAlt: media.alt || '',
								} )
							}
							allowedTypes={ [ 'image' ] }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open }>
									{ imageUrl
										? __( 'Change image', 'soywd' )
										: __( 'Select image', 'soywd' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ imageUrl && (
						<Button
							isDestructive
							isSmall
							onClick={ () =>
								setAttributes( { imageUrl: '', imageAlt: '' } )
							}
							style={ { marginLeft: 8 } }
						>
							{ __( 'Remove', 'soywd' ) }
						</Button>
					) }
					<SelectControl
						label={ __( 'Overlay color', 'soywd' ) }
						value={ imageOverlayColor }
						options={ [
							{ label: __( 'None', 'soywd' ), value: '' },
							{
								label: __( 'Accent (clay)', 'soywd' ),
								value: 'accent',
							},
							{
								label: __( 'Primary (olive)', 'soywd' ),
								value: 'primary',
							},
							{
								label: __( 'Secondary (dark brown)', 'soywd' ),
								value: 'secondary',
							},
						] }
						onChange={ ( v ) =>
							setAttributes( { imageOverlayColor: v } )
						}
					/>
					{ imageOverlayColor && (
						<RangeControl
							label={ __( 'Overlay opacity', 'soywd' ) }
							value={ imageOverlayOpacity }
							min={ 0 }
							max={ 0.7 }
							step={ 0.05 }
							onChange={ ( v ) =>
								setAttributes( { imageOverlayOpacity: v } )
							}
						/>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Icon', 'soywd' ) }
					initialOpen={ false }
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( { iconImageUrl: media.url } )
							}
							allowedTypes={ [ 'image' ] }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open }>
									{ iconImageUrl
										? __( 'Change icon SVG', 'soywd' )
										: __( 'Select icon SVG', 'soywd' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ iconImageUrl && (
						<Button
							isDestructive
							isSmall
							onClick={ () =>
								setAttributes( { iconImageUrl: '' } )
							}
							style={ { marginLeft: 8 } }
						>
							{ __( 'Remove', 'soywd' ) }
						</Button>
					) }
					<TextControl
						label={ __( 'Or emoji character', 'soywd' ) }
						value={ icon }
						onChange={ ( v ) => setAttributes( { icon: v } ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Tags', 'soywd' ) }
					initialOpen={ false }
				>
					<TextareaControl
						label={ __( 'Tags (one per line)', 'soywd' ) }
						help={ __(
							'Subtopic pills shown below the text. Leave empty for none.',
							'soywd'
						) }
						value={ tagsToText( tags ) }
						onChange={ ( v ) =>
							setAttributes( { tags: textToTags( v ) } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'CTA', 'soywd' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Text', 'soywd' ) }
						value={ ctaText }
						onChange={ ( v ) => setAttributes( { ctaText: v } ) }
					/>
					<TextControl
						label={ __( 'URL', 'soywd' ) }
						value={ ctaUrl }
						onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
					/>
					<SelectControl
						label={ __( 'Style', 'soywd' ) }
						value={ ctaStyle }
						options={ [
							{ label: __( 'Button', 'soywd' ), value: 'button' },
							{
								label: __( 'Arrow link', 'soywd' ),
								value: 'arrow',
							},
						] }
						onChange={ ( v ) => setAttributes( { ctaStyle: v } ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Card style', 'soywd' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Layout', 'soywd' ) }
						value={ layout }
						options={ [
							{
								label: __(
									'Vertical (icon above title)',
									'soywd'
								),
								value: 'vertical',
							},
							{
								label: __(
									'Horizontal header (icon beside title)',
									'soywd'
								),
								value: 'horizontal-header',
							},
							{
								label: __(
									'Bilingual (two columns + divider)',
									'soywd'
								),
								value: 'bilingual',
							},
						] }
						onChange={ ( v ) => setAttributes( { layout: v } ) }
					/>
					<SelectControl
						label={ __( 'Heading level', 'soywd' ) }
						value={ String( headingLevel ) }
						options={ [
							{ label: 'H2', value: '2' },
							{ label: 'H3', value: '3' },
							{ label: 'H4', value: '4' },
						] }
						onChange={ ( v ) =>
							setAttributes( {
								headingLevel: parseInt( v, 10 ),
							} )
						}
						help={ __(
							'Match the section: H3 under a section H2, etc. Avoid skipping levels.',
							'soywd'
						) }
					/>
					<ToggleControl
						label={ __( 'Center content', 'soywd' ) }
						checked={ centered }
						onChange={ ( v ) => setAttributes( { centered: v } ) }
					/>
					<ToggleControl
						label={ __( 'Bordered', 'soywd' ) }
						checked={ bordered }
						onChange={ ( v ) => setAttributes( { bordered: v } ) }
					/>
					<ToggleControl
						label={ __(
							'Transparent (inherit parent background)',
							'soywd'
						) }
						checked={ transparent }
						onChange={ ( v ) =>
							setAttributes( { transparent: v } )
						}
					/>
					<ToggleControl
						label={ __( 'Half width (centered)', 'soywd' ) }
						help={ __(
							'Match a 2-column card width and center it. Use for a lone card on its own row.',
							'soywd'
						) }
						checked={ halfWidthCentered }
						onChange={ ( v ) =>
							setAttributes( { halfWidthCentered: v } )
						}
					/>
					<p
						style={ {
							marginTop: 16,
							marginBottom: 8,
							fontSize: 11,
							textTransform: 'uppercase',
							letterSpacing: '0.05em',
							opacity: 0.7,
						} }
					>
						{ __( 'Background texture', 'soywd' ) }
					</p>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									backgroundImageUrl: media.url,
								} )
							}
							allowedTypes={ [ 'image' ] }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open }>
									{ backgroundImageUrl
										? __( 'Change texture', 'soywd' )
										: __( 'Select texture', 'soywd' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImageUrl && (
						<Button
							isDestructive
							isSmall
							onClick={ () =>
								setAttributes( { backgroundImageUrl: '' } )
							}
							style={ { marginLeft: 8 } }
						>
							{ __( 'Remove', 'soywd' ) }
						</Button>
					) }
					<TextControl
						label={ __( 'Title color (hex)', 'soywd' ) }
						help={ __( 'Optional. e.g. #5e5646', 'soywd' ) }
						value={ titleColor || '' }
						onChange={ ( v ) => setAttributes( { titleColor: v } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ renderImage && (
					<div className="p-3 pb-0">
						<div className="relative overflow-hidden rounded-[14px]">
							<img
								src={ imageUrl }
								alt={ imageAlt }
								className="w-full h-48 object-cover"
							/>
							{ imageOverlayColor && (
								<div
									className={ `absolute inset-0 has-${ imageOverlayColor }-background-color has-background` }
									style={ { opacity: imageOverlayOpacity } }
									aria-hidden="true"
								/>
							) }
						</div>
					</div>
				) }
				<div className="p-6">
					{ isBilingual ? (
						<div className="grid md:grid-cols-2">
							<div className="text-left md:pr-8">
								<RichText
									tagName="h3"
									className="mb-2"
									value={ title }
									onChange={ ( v ) =>
										setAttributes( { title: v } )
									}
									placeholder={ __(
										'Title (left)…',
										'soywd'
									) }
									style={
										titleColor
											? { color: titleColor }
											: undefined
									}
								/>
								<RichText
									tagName="p"
									className={ bodyClass }
									value={ text }
									onChange={ ( v ) =>
										setAttributes( { text: v } )
									}
									placeholder={ __(
										'Text (left)…',
										'soywd'
									) }
								/>
							</div>
							<div
								dir="rtl"
								className="text-left mt-6 pt-6 border-t border-current md:mt-0 md:pt-0 md:border-t-0 md:border-l md:pl-8"
							>
								<RichText
									tagName="h3"
									className="mb-2"
									value={ titleRight }
									onChange={ ( v ) =>
										setAttributes( { titleRight: v } )
									}
									placeholder={ __(
										'Title (right)…',
										'soywd'
									) }
									style={
										titleColor
											? { color: titleColor }
											: undefined
									}
								/>
								<RichText
									tagName="p"
									className={ bodyClass }
									value={ textRight }
									onChange={ ( v ) =>
										setAttributes( { textRight: v } )
									}
									placeholder={ __(
										'Text (right)…',
										'soywd'
									) }
								/>
							</div>
						</div>
					) : (
						<>
							{ iconImageUrl && ! renderImage && (
								<div
									className={ `text-accent mb-4 ${
										centered ? 'flex justify-center' : ''
									}` }
									aria-hidden="true"
								>
									<img
										src={ iconImageUrl }
										alt=""
										className="w-12 h-12"
									/>
								</div>
							) }
							{ ! iconImageUrl && icon && ! renderImage && (
								<div
									className="text-3xl mb-3"
									aria-hidden="true"
								>
									{ icon }
								</div>
							) }
							{ eyebrow !== undefined && (
								<RichText
									tagName="p"
									className="soywd-eyebrow mb-2"
									value={ eyebrow }
									onChange={ ( v ) =>
										setAttributes( { eyebrow: v } )
									}
									placeholder={ __(
										'Eyebrow (optional)',
										'soywd'
									) }
								/>
							) }
							<RichText
								tagName="h3"
								className="mb-2"
								value={ title }
								onChange={ ( v ) =>
									setAttributes( { title: v } )
								}
								placeholder={ __( 'Card title…', 'soywd' ) }
								style={
									titleColor
										? { color: titleColor }
										: undefined
								}
							/>
							<RichText
								tagName="p"
								className={ bodyClass }
								value={ text }
								onChange={ ( v ) =>
									setAttributes( { text: v } )
								}
								placeholder={ __( 'Card text…', 'soywd' ) }
							/>
						</>
					) }
					{ tags && tags.length > 0 && (
						<div
							className={ `flex flex-wrap gap-2 mt-4 ${
								centered || isBilingual
									? 'justify-center'
									: 'justify-start'
							}` }
						>
							{ tags.map( ( t, i ) => {
								const label =
									typeof t === 'string' ? t : t.label || '';
								return (
									<span
										key={ i }
										className="inline-block border border-current px-4 py-1.5 rounded-full text-xs tracking-normal"
									>
										{ label }
									</span>
								);
							} ) }
						</div>
					) }
					{ ctaText && (
						<div
							className={
								isBilingual ? 'mt-4 text-center' : 'mt-4'
							}
						>
							{ ctaStyle === 'button' ? (
								<a
									href={ ctaUrl || '#' }
									className="soywd-btn-primary"
									onClick={ ( e ) => e.preventDefault() }
								>
									{ ctaText }
								</a>
							) : (
								<a
									href={ ctaUrl || '#' }
									className="text-xs uppercase tracking-widest font-semibold"
									onClick={ ( e ) => e.preventDefault() }
								>
									{ ctaText } →
								</a>
							) }
						</div>
					) }
				</div>
			</div>
		</>
	);
}
