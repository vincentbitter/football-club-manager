import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Save({ attributes }) {
    const { parent } = attributes;

    const blockProps = useBlockProps.save({
        className: "fcmanager-parent-details-data"
    });

    return (
        <div {...blockProps}>
            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("First name", "football-club-manager")}
                        <input type="text" name={`${parent}_first_name`} {...(parent === 'parent1' ? { required: true } : {})} />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Middle name", "football-club-manager")}
                        <input type="text" name={`${parent}_middle_name`} />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Last name", "football-club-manager")}
                        <input type="text" name={`${parent}_last_name`} {...(parent === 'parent1' ? { required: true } : {})} />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Mobile phone number", "football-club-manager")}
                        <input type="text" name={`${parent}_mobile_phone`} />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Phone number", "football-club-manager")}
                        <input type="text" name={`${parent}_phone`} />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Email address", "football-club-manager")}
                        <input type="email" name={`${parent}_email`} {...(parent === 'parent1' ? { required: true } : {})} />
                    </label>
                </div>
            </div>
        </div>
    );
}