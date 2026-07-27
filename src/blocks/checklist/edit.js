import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	TextControl,
	Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { items, direction, columns, theme, iconStyle } = attributes;
	const iconGlyph =
		{ check: '✓', plus: '+', dot: '•', none: '' }[ iconStyle ] || '✓';

	const update = ( i, value ) => {
		const next = [ ...items ];
		next[ i ] = { ...next[ i ], text: value };
		setAttributes( { items: next } );
	};
	const remove = ( i ) =>
		setAttributes( { items: items.filter( ( _, idx ) => idx !== i ) } );
	const add = () => setAttributes( { items: [ ...items, { text: '' } ] } );

	const isHorizontal = direction === 'horizontal';
	const isDark = theme === 'dark';

	const wrapperClasses = [
		'soywd-checklist',
		isDark ? 'bg-secondary text-white' : '',
		isDark ? 'py-4 px-6' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const listClasses = isHorizontal
		? 'flex flex-wrap items-center justify-center gap-[0.8rem] md:gap-x-[4.5rem] md:gap-y-2'
		: `grid gap-3 grid-cols-${ columns }`;

	const blockProps = useBlockProps( { className: wrapperClasses } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'soywd' ) } initialOpen>
					<SelectControl
						label={ __( 'Direction', 'soywd' ) }
						value={ direction }
						options={ [
							{
								label: __( 'Vertical (grid)', 'soywd' ),
								value: 'vertical',
							},
							{
								label: __( 'Horizontal (inline)', 'soywd' ),
								value: 'horizontal',
							},
						] }
						onChange={ ( v ) => setAttributes( { direction: v } ) }
					/>
					{ direction === 'vertical' && (
						<RangeControl
							label={ __( 'Columns', 'soywd' ) }
							value={ columns }
							onChange={ ( v ) =>
								setAttributes( { columns: v } )
							}
							min={ 1 }
							max={ 4 }
						/>
					) }
					<SelectControl
						label={ __( 'Theme', 'soywd' ) }
						value={ theme }
						options={ [
							{ label: __( 'Light', 'soywd' ), value: 'light' },
							{
								label: __( 'Dark (trust bar)', 'soywd' ),
								value: 'dark',
							},
						] }
						onChange={ ( v ) => setAttributes( { theme: v } ) }
					/>
					<SelectControl
						label={ __( 'Icon style', 'soywd' ) }
						value={ iconStyle }
						options={ [
							{
								label: __( 'Check (✓)', 'soywd' ),
								value: 'check',
							},
							{ label: __( 'Plus (+)', 'soywd' ), value: 'plus' },
							{ label: __( 'Dot (•)', 'soywd' ), value: 'dot' },
							{ label: __( 'None', 'soywd' ), value: 'none' },
						] }
						onChange={ ( v ) => setAttributes( { iconStyle: v } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Items', 'soywd' ) } initialOpen>
					{ items.map( ( item, i ) => (
						<div key={ i } style={ { marginBottom: 8 } }>
							<TextControl
								label={ `${ __( 'Item', 'soywd' ) } ${
									i + 1
								}` }
								value={ item.text }
								onChange={ ( v ) => update( i, v ) }
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
						{ __( 'Add item', 'soywd' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ul className={ listClasses }>
					{ items.map( ( item, i ) => (
						<li key={ i } className="flex items-start gap-2">
							{ iconGlyph && (
								<span aria-hidden="true" className="opacity-70">
									{ iconGlyph }
								</span>
							) }
							{ /* Author-typed HTML (e.g. <br>); rendered so the
							     preview matches the frontend's wp_kses_post output. */ }
							<span
								className={
									isHorizontal ? 'font-bold' : 'font-medium'
								}
								dangerouslySetInnerHTML={ {
									__html:
										item.text || __( '(empty)', 'soywd' ),
								} }
							/>
						</li>
					) ) }
				</ul>
			</div>
		</>
	);
}
