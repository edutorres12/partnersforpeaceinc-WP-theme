import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import edit from './edit';
import './style.css';
import './editor.css';

registerBlockType( metadata.name, {
	edit,
	save: () => null,
} );
