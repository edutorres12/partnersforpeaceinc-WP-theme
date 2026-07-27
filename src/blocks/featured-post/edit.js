import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit() {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<ServerSideRender
				block="wptpl/featured-post"
				EmptyResponsePlaceholder={ () => (
					<p style={ { padding: '2rem', textAlign: 'center' } }>
						{ __(
							'No published posts yet. This card will show the sticky post (or the most recent one) once you publish.',
							'wptpl'
						) }
					</p>
				) }
			/>
		</div>
	);
}
