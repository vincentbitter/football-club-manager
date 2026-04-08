import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, SelectControl } from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
    const { parent } = attributes;

    const blockProps = useBlockProps({
        className: "fcmanager-parent-details-editor"
    });

    const parentOptions = [
        { value: 'parent1', label: __('Parent 1', 'football-club-manager') },
        { value: 'parent2', label: __('Parent 2', 'football-club-manager') }
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={__("Parent Selection", "football-club-manager")}>
                    <SelectControl
                        label={__("Select Parent", "football-club-manager")}
                        value={parent}
                        options={parentOptions}
                        onChange={(value) => setAttributes({ parent: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div className="fcmanager-form-grid fcmanager-form-grid--full">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("First name", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="John"
                                readOnly
                            />
                        </label>
                    </div>
                </div>

                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Middle name", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="van"
                                readOnly
                            />
                        </label>
                    </div>

                    <div className="fcmanager-form-field">
                        <label>
                            {__("Last name", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="Doe"
                                readOnly
                            />
                        </label>
                    </div>
                </div>

                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Mobile phone number", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="tel"
                                value="0612345678"
                                readOnly
                            />
                        </label>
                    </div>

                    <div className="fcmanager-form-field">
                        <label>
                            {__("Phone number", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="tel"
                                value="0781234567"
                                readOnly
                            />
                        </label>
                    </div>
                </div>

                <div className="fcmanager-form-grid fcmanager-form-grid--full">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Email address", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="email"
                                value="john@example.com"
                                readOnly
                            />
                        </label>
                    </div>
                </div>
            </div>
        </>
    );
}