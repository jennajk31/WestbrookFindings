import { Button } from '@woocommerce/blocks-checkout';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

import { __ } from '@wordpress/i18n'; 

export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Block options', 'delivery_date')}>
					Options for the block go here.
				</PanelBody>
			</InspectorControls>
			<div className={ 'example-fields' }> 
                <Button class="wc_avatax_validate_address button">Validate Address</Button>
			</div>
		</div>
	);
};