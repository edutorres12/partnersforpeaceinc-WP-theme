import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const { count } = attributes;
	const blockProps = useBlockProps();
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Grid', 'soywd' ) } initialOpen>
					<RangeControl
						label={ __( 'Number of posts (0 = all)', 'soywd' ) }
						value={ count }
						min={ 0 }
						max={ 24 }
						onChange={ ( v ) => setAttributes( { count: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="soywd/post-grid"
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<p style={ { padding: '2rem', textAlign: 'center' } }>
							{ __(
								'No posts to show yet. Publish some posts and they will appear here automatically.',
								'soywd'
							) }
						</p>
					) }
				/>
			</div>
		</>
	);
}
