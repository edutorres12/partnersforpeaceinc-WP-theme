import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { eyebrow, headline, intro, alignment, headingLevel } = attributes;
	const Tag = `h${ headingLevel }`;
	const blockProps = useBlockProps( {
		className: `wptpl-section-header text-${ alignment }`,
	} );

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={ alignment }
					onChange={ ( v ) =>
						setAttributes( { alignment: v || 'center' } )
					}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Heading', 'wptpl' ) } initialOpen>
					<SelectControl
						label={ __( 'Heading level', 'wptpl' ) }
						value={ String( headingLevel ) }
						options={ [
							{ label: 'H1', value: '1' },
							{ label: 'H2', value: '2' },
							{ label: 'H3', value: '3' },
							{ label: 'H4', value: '4' },
						] }
						onChange={ ( v ) =>
							setAttributes( { headingLevel: parseInt( v, 10 ) } )
						}
						help={ __(
							'Use H1 once per page (the main title, on pages with no hero). Sections use H2+.',
							'wptpl'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="p"
					className="wptpl-section-header__eyebrow text-sm uppercase tracking-widest text-muted mb-2"
					value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
					placeholder={ __( 'Eyebrow (optional)', 'wptpl' ) }
				/>
				<RichText
					tagName={ Tag }
					className="wptpl-section-header__headline"
					value={ headline }
					onChange={ ( v ) => setAttributes( { headline: v } ) }
					placeholder={ __( 'Headline…', 'wptpl' ) }
				/>
				<RichText
					tagName="p"
					className="wptpl-section-header__intro text-muted mt-3 max-w-3xl mx-auto"
					value={ intro }
					onChange={ ( v ) => setAttributes( { intro: v } ) }
					placeholder={ __( 'Optional intro text', 'wptpl' ) }
				/>
			</div>
		</>
	);
}
