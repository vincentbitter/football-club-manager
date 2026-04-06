import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Save({ attributes }) {
    const { allowedMethods } = attributes;

    return (
        <div {...useBlockProps.save()}>
            {allowedMethods.length === 1 && (
                <input type="hidden" name="method" value={allowedMethods[0]} />
            )}
            {allowedMethods.length > 1 && (
                <div className="fcmanager-form-grid fcmanager-form-grid--full">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Payment method", "football-club-manager")}
                            <select name="method" className="fcmanager-payment-method-select" required>
                                <option value="">
                                    {__("Select…", "football-club-manager")}
                                </option>
                                {allowedMethods.includes('direct_debit') && (
                                    <option value="direct_debit">
                                        {__("Direct debit", "football-club-manager")}
                                    </option>
                                )}
                                {allowedMethods.includes('no_payment') && (
                                    <option value="no_payment">
                                        {__("No payment needed", "football-club-manager")}
                                    </option>
                                )}
                            </select>
                        </label>
                    </div>
                </div>)
            }

            {allowedMethods.includes('direct_debit') && (
                <div className="fcmanager-form-grid fcmanager-form-grid--double" data-payment-method="direct_debit">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Bank account (IBAN)", "football-club-manager")}
                            <input type="text" name="iban" required />
                        </label>
                    </div>

                    <div className="fcmanager-form-field">
                        <label>
                            {__("Account holder name", "football-club-manager")}
                            <input type="text" name="account_holder_name" required />
                        </label>
                    </div>
                </div>
            )}

            {allowedMethods.includes('no_payment') && (
                <div className="fcmanager-form-grid fcmanager-form-grid--full" data-payment-method="no_payment">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Reason", "football-club-manager")}
                            <input type="text" name="reason" required />
                        </label>
                    </div>
                </div>
            )}

        </div>
    );
}