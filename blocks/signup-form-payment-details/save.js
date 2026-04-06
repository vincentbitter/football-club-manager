import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Save() {
    return (
        <div {...useBlockProps.save()}>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Payment method", "football-club-manager")}
                        <select name="method" required>
                            <option value="">
                                {__("Select…", "football-club-manager")}
                            </option>
                            <option value="direct_debit">
                                {__("Direct debit", "football-club-manager")}
                            </option>
                            <option value="no_payment">
                                {__("No payment needed", "football-club-manager")}
                            </option>
                        </select>
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Bank account (IBAN)", "football-club-manager")}
                        <input type="text" name="iban" />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Account holder name", "football-club-manager")}
                        <input type="text" name="account_holder_name" />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Reason", "football-club-manager")}
                        <input type="text" name="reason" />
                    </label>
                </div>
            </div>

        </div>
    );
}