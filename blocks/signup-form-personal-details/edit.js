import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit() {
    const blockProps = useBlockProps({
        className: "fcmanager-personal-details-editor"
    });

    return (
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

                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Initials", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="J.D."
                                readOnly
                            />
                        </label>
                    </div>

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
                        {__("Date of birth", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="date"
                            value="1990-01-01"
                            readOnly
                        />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Gender", "football-club-manager")}
                        <select
                            className="components-select-control__input"
                            disabled
                        >
                            <option>{__("Male", "football-club-manager")}</option>
                        </select>
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Street", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value="Main Street"
                            readOnly
                        />
                    </label>
                </div>
                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("House number", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="123"
                                readOnly
                            />
                        </label>
                    </div>
                    <div className="fcmanager-form-field">
                        <label>
                            {__("House number addition", "football-club-manager")}
                            <input
                                className="components-text-control__input"
                                type="text"
                                value="A"
                                readOnly
                            />
                        </label>
                    </div>
                </div>
                <div className="fcmanager-form-field">
                    <label>
                        {__("Postal code", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value="1234 AB"
                            readOnly
                        />
                    </label>
                </div>
                <div className="fcmanager-form-field">
                    <label>
                        {__("City", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value="Amsterdam"
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
    );
}
