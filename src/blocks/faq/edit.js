import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { items } = attributes;

	const update = ( i, key, value ) => {
		const next = [ ...items ];
		next[ i ] = { ...next[ i ], [ key ]: value };
		setAttributes( { items: next } );
	};
	const remove = ( i ) =>
		setAttributes( { items: items.filter( ( _, idx ) => idx !== i ) } );
	const add = () =>
		setAttributes( {
			items: [ ...items, { question: 'New question', answer: '' } ],
		} );

	const blockProps = useBlockProps( {
		className: 'wptpl-faq wptpl-container-narrow',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'FAQ items', 'wptpl' ) } initialOpen>
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
								label={ `${ __( 'Question', 'wptpl' ) } ${
									i + 1
								}` }
								value={ item.question }
								onChange={ ( v ) => update( i, 'question', v ) }
							/>
							<TextareaControl
								label={ __( 'Answer', 'wptpl' ) }
								value={ item.answer }
								onChange={ ( v ) => update( i, 'answer', v ) }
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
						{ __( 'Add question', 'wptpl' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ items.map( ( item, i ) => (
					<div
						key={ i }
						className="border-b border-slate-200 py-3 flex items-start justify-between gap-4"
					>
						<div>
							<p className="text-lg font-semibold">
								{ item.question }
							</p>
							<p className="text-muted mt-1">{ item.answer }</p>
						</div>
						<span
							className="text-2xl text-muted shrink-0"
							aria-hidden="true"
						>
							+
						</span>
					</div>
				) ) }
			</div>
		</>
	);
}
