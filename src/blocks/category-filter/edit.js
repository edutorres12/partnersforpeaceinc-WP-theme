import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit() {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<ServerSideRender
				block="soywd/category-filter"
				EmptyResponsePlaceholder={ () => (
					<p style={ { padding: '1rem', textAlign: 'center' } }>
						{ __(
							'No categories with posts yet. Add categories to your posts and the filter pills will appear here.',
							'soywd'
						) }
					</p>
				) }
			/>
		</div>
	);
}
