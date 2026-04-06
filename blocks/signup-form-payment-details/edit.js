import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit() {
    const blockProps = useBlockProps({
        className: "fcmanager-payment-details-editor"
    });

    return (
        <div {...blockProps}>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Payment method", "football-club-manager")}
                        <select
                            className="components-select-control__input"
                            disabled
                        >
                            <option>{__("Direct debit", "football-club-manager")}</option>
                        </select>
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Bank account (IBAN)", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value="NL91ABNA0417164300"
                            readOnly
                        />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Account holder name", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value="John Doe"
                            readOnly
                        />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Reason", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            value={__("Volunteer", "football-club-manager")}
                            readOnly
                        />
                    </label>
                </div>
            </div>

        </div>
    );
}