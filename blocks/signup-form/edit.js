import { __ } from "@wordpress/i18n";
import { PanelBody } from "@wordpress/components";
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
	URLInput,
} from "@wordpress/block-editor";
import { BaseControl } from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
	const { redirectUrl } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Form Settings", "fcmanager")}>
					<BaseControl label={__("Redirect URL", "fcmanager")}>
						<div className="fcmanager-urlinput-wrapper">
							<URLInput
								value={redirectUrl}
								onChange={(value) => setAttributes({ redirectUrl: value })}
								placeholder={__("Search or enter a URL…", "fcmanager")}
							/>
						</div>
					</BaseControl>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				<form class="fcmanager-signup-form">
					<InnerBlocks
						template={
							[
								["fcmanager/signup-form-personal-details"],
								[
									"core/columns",
									{ className: "fcmanager-parent-details-data" },
									[
										[
											"core/column",
											{},
											[
												["fcmanager/signup-form-parent-details", { parent: "parent1" }]
											]
										],
										[
											"core/column",
											{},
											[
												["fcmanager/signup-form-parent-details", { parent: "parent2" }]
											]
										]
									]
								],
								["fcmanager/signup-form-payment-details"],
							]
						}
					/>
				</form>
			</div>
		</>
	);
}
