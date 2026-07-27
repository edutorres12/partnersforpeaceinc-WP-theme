import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
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

export default function Edit( { attributes, setAttributes } ) {
	const {
		eyebrow,
		title,
		subtitle,
		ctaText,
		ctaUrl,
		secondaryCtaText,
		secondaryCtaUrl,
		microcopy,
		headingLevel,
		layout,
		alignment,
		imageUrl,
		imageAlt,
		backgroundImageUrl,
		overlayOpacity,
		overlayColor,
		titleColor,
		subtitleColor,
	} = attributes;

	const isSplit = layout === 'split';
	const hasBg = !! backgroundImageUrl;
	const blockProps = useBlockProps( {
		className: `soywd-hero text-${ alignment }`,
	} );

	let btnAlignClass = 'justify-start';
	if ( alignment === 'center' ) {
		btnAlignClass = 'justify-center';
	} else if ( alignment === 'right' ) {
		btnAlignClass = 'justify-end';
	}

	const textCol = (
		<div className="py-[6.25rem] px-6">
			<RichText
				tagName="p"
				className="text-sm uppercase tracking-widest text-muted mb-2"
				value={ eyebrow }
				onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
				placeholder={ __( 'Eyebrow (optional)', 'soywd' ) }
			/>
			<RichText
				tagName="h1"
				className={ `mb-4 ${ titleColor ? '' : 'text-primary' }` }
				style={ titleColor ? { color: titleColor } : undefined }
				value={ title }
				onChange={ ( v ) => setAttributes( { title: v } ) }
				placeholder={ __( 'Hero title…', 'soywd' ) }
			/>
			<RichText
				tagName="p"
				className={ `text-lg md:text-xl mb-6 ${
					subtitleColor ? '' : 'text-muted'
				}` }
				style={ subtitleColor ? { color: subtitleColor } : undefined }
				value={ subtitle }
				onChange={ ( v ) => setAttributes( { subtitle: v } ) }
				placeholder={ __( 'Subtitle…', 'soywd' ) }
			/>
			<div className="flex gap-2 flex-wrap">
				{ ctaText && (
					<a
						href={ ctaUrl }
						className="soywd-btn-accent"
						onClick={ ( e ) => e.preventDefault() }
					>
						{ ctaText }
					</a>
				) }
				{ secondaryCtaText && (
					<a
						href={ secondaryCtaUrl || '#' }
						className="inline-flex items-center justify-center border border-contrast text-contrast rounded-md px-4 py-2 font-semibold"
						onClick={ ( e ) => e.preventDefault() }
					>
						{ secondaryCtaText }
					</a>
				) }
			</div>
			{ microcopy && (
				<p className="mt-3 text-sm text-muted">{ microcopy }</p>
			) }
		</div>
	);

	const imageCol = (
		<div className="bg-slate-100 min-h-[440px] flex items-center justify-center">
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ ( media ) =>
						setAttributes( {
							imageUrl: media.url,
							imageAlt: media.alt || '',
						} )
					}
					allowedTypes={ [ 'image' ] }
					render={ ( { open } ) =>
						imageUrl ? (
							<button
								type="button"
								onClick={ open }
								style={ {
									padding: 0,
									border: 'none',
									background: 'none',
									cursor: 'pointer',
								} }
							>
								<img
									src={ imageUrl }
									alt={ imageAlt }
									style={ {
										display: 'block',
										width: '100%',
									} }
								/>
							</button>
						) : (
							<Button variant="secondary" onClick={ open }>
								{ __( 'Select hero image', 'soywd' ) }
							</Button>
						)
					}
				/>
			</MediaUploadCheck>
		</div>
	);

	const overlayPreview = (
		<div
			className={ `soywd-hero relative overflow-hidden text-${ alignment }` }
			style={ {
				backgroundImage: `url(${ backgroundImageUrl })`,
				backgroundSize: 'cover',
				backgroundPosition: 'center',
			} }
		>
			<div
				className={ `absolute inset-0 ${
					overlayColor ? '' : 'bg-secondary'
				}` }
				style={
					overlayColor
						? {
								backgroundColor: overlayColor,
								opacity: overlayOpacity,
						  }
						: { opacity: overlayOpacity }
				}
				aria-hidden="true"
			/>
			<div className="relative z-10 flex items-center min-h-[60vh] py-[6.25rem]">
				<div className="soywd-container">
					<RichText
						tagName="p"
						className="soywd-eyebrow text-cream/80 mb-3"
						value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						placeholder={ __( 'Eyebrow (optional)', 'soywd' ) }
					/>
					<RichText
						tagName="h1"
						className={ `mb-6 ${ titleColor ? '' : 'text-cream' }` }
						style={ titleColor ? { color: titleColor } : undefined }
						value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						placeholder={ __( 'Hero title…', 'soywd' ) }
					/>
					<RichText
						tagName="p"
						className={ `mb-8 max-w-xl ${
							alignment === 'center' ? 'mx-auto' : ''
						} ${ subtitleColor ? '' : 'text-cream/85' }` }
						style={
							subtitleColor ? { color: subtitleColor } : undefined
						}
						value={ subtitle }
						onChange={ ( v ) => setAttributes( { subtitle: v } ) }
						placeholder={ __( 'Subtitle…', 'soywd' ) }
					/>
					<div
						className={ `flex gap-3 flex-wrap ${ btnAlignClass }` }
					>
						{ ctaText && (
							<a
								href={ ctaUrl }
								className="soywd-btn-accent"
								onClick={ ( e ) => e.preventDefault() }
							>
								{ ctaText }
							</a>
						) }
						{ secondaryCtaText && (
							<a
								href={ secondaryCtaUrl || '#' }
								className="soywd-btn-outline"
								onClick={ ( e ) => e.preventDefault() }
							>
								{ secondaryCtaText }
							</a>
						) }
					</div>
					{ microcopy && (
						<p className="mt-4 text-sm text-cream/70">
							{ microcopy }
						</p>
					) }
				</div>
			</div>
		</div>
	);

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={ alignment }
					onChange={ ( v ) =>
						setAttributes( { alignment: v || 'left' } )
					}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'soywd' ) } initialOpen>
					<SelectControl
						label={ __( 'Layout', 'soywd' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Split (text + image)', 'soywd' ),
								value: 'split',
							},
							{
								label: __( 'Centered (text only)', 'soywd' ),
								value: 'centered',
							},
						] }
						onChange={ ( v ) => setAttributes( { layout: v } ) }
						help={ __(
							'Add a background image below to use the full-bleed overlay hero instead.',
							'soywd'
						) }
					/>
					<SelectControl
						label={ __( 'Heading level', 'soywd' ) }
						value={ String( headingLevel ) }
						options={ [
							{ label: 'H1', value: '1' },
							{ label: 'H2', value: '2' },
						] }
						onChange={ ( v ) =>
							setAttributes( { headingLevel: parseInt( v, 10 ) } )
						}
						help={ __(
							'Use H1 once per page. Set H2 when the page already has another H1.',
							'soywd'
						) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Background image + overlay', 'soywd' ) }
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
								<Button
									variant="secondary"
									onClick={ open }
									style={ { marginBottom: '8px' } }
								>
									{ backgroundImageUrl
										? __( 'Replace background', 'soywd' )
										: __( 'Select background', 'soywd' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImageUrl && (
						<>
							<Button
								variant="link"
								isDestructive
								onClick={ () =>
									setAttributes( {
										backgroundImageUrl: '',
										backgroundImageAlt: '',
									} )
								}
							>
								{ __( 'Remove background', 'soywd' ) }
							</Button>
							<RangeControl
								label={ __( 'Overlay opacity', 'soywd' ) }
								value={ overlayOpacity }
								onChange={ ( v ) =>
									setAttributes( {
										overlayOpacity:
											v === undefined ? 0.5 : v,
									} )
								}
								min={ 0 }
								max={ 0.9 }
								step={ 0.05 }
							/>
						</>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Primary CTA', 'soywd' ) } initialOpen>
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
				</PanelBody>
				<PanelBody
					title={ __( 'Secondary CTA', 'soywd' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Text', 'soywd' ) }
						value={ secondaryCtaText }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaText: v } )
						}
					/>
					<TextControl
						label={ __( 'URL', 'soywd' ) }
						value={ secondaryCtaUrl }
						onChange={ ( v ) =>
							setAttributes( { secondaryCtaUrl: v } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Microcopy', 'soywd' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Below buttons', 'soywd' ) }
						value={ microcopy }
						onChange={ ( v ) => setAttributes( { microcopy: v } ) }
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Colors', 'soywd' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: overlayColor,
							onChange: ( v ) =>
								setAttributes( { overlayColor: v || '' } ),
							label: __( 'Overlay color', 'soywd' ),
						},
						{
							value: titleColor,
							onChange: ( v ) =>
								setAttributes( { titleColor: v || '' } ),
							label: __( 'Title color', 'soywd' ),
						},
						{
							value: subtitleColor,
							onChange: ( v ) =>
								setAttributes( { subtitleColor: v || '' } ),
							label: __( 'Subtitle color', 'soywd' ),
						},
					] }
				/>
			</InspectorControls>

			{ hasBg ? (
				overlayPreview
			) : (
				<div { ...blockProps }>
					{ isSplit ? (
						<div className="grid md:grid-cols-2 gap-0 items-stretch">
							{ textCol }
							{ imageCol }
						</div>
					) : (
						<div className="max-w-3xl mx-auto">{ textCol }</div>
					) }
				</div>
			) }
		</>
	);
}
