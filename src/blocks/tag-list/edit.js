import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { items, alignment, variant, pillBorderColor } = attributes;

	const update = ( i, key, value ) => {
		const next = [ ...items ];
		next[ i ] = { ...next[ i ], [ key ]: value };
		setAttributes( { items: next } );
	};
	const remove = ( i ) =>
		setAttributes( { items: items.filter( ( _, idx ) => idx !== i ) } );
	const add = () =>
		setAttributes( { items: [ ...items, { label: '', url: '' } ] } );

	let justify = 'justify-center';
	if ( alignment === 'left' ) {
		justify = 'justify-start';
	} else if ( alignment === 'right' ) {
		justify = 'justify-end';
	}

	const blockProps = useBlockProps( { className: 'soywd-tag-list' } );

	const tagClass =
		variant === 'filled'
			? 'inline-block bg-secondary text-white px-3 py-1 rounded-2xl text-sm border border-secondary'
			: 'inline-block border border-slate-400 text-muted px-3 py-1 rounded-2xl text-sm';

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
				<PanelBody title={ __( 'Style', 'soywd' ) } initialOpen>
					<TextControl
						label={ __(
							'Border color (hex, outline only)',
							'soywd'
						) }
						value={ pillBorderColor }
						onChange={ ( v ) =>
							setAttributes( { pillBorderColor: v } )
						}
					/>
					<SelectControl
						label={ __( 'Variant', 'soywd' ) }
						value={ variant }
						options={ [
							{
								label: __( 'Outline', 'soywd' ),
								value: 'outline',
							},
							{ label: __( 'Filled', 'soywd' ), value: 'filled' },
						] }
						onChange={ ( v ) => setAttributes( { variant: v } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Tags', 'soywd' ) } initialOpen>
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
								label={ `${ __( 'Label', 'soywd' ) } ${
									i + 1
								}` }
								value={ item.label }
								onChange={ ( v ) => update( i, 'label', v ) }
							/>
							<TextControl
								label={ __( 'URL (optional)', 'soywd' ) }
								value={ item.url }
								onChange={ ( v ) => update( i, 'url', v ) }
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => remove( i ) }
							>
								{ __( 'Remove', 'soywd' ) }
							</Button>
						</div>
					) ) }
					<Button variant="primary" onClick={ add }>
						{ __( 'Add tag', 'soywd' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className={ `flex flex-wrap gap-2 ${ justify }` }>
					{ items.map( ( item, i ) => (
						<span
							key={ i }
							className={ tagClass }
							style={
								variant === 'outline' && pillBorderColor
									? { borderColor: pillBorderColor }
									: undefined
							}
						>
							{ item.label || __( '(empty)', 'soywd' ) }
						</span>
					) ) }
				</div>
			</div>
		</>
	);
}
