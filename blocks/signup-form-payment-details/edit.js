import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody } from "@wordpress/components";
import { CheckboxControl } from "@wordpress/components";
import { useState } from "@wordpress/element";

export default function Edit({ attributes, setAttributes }) {
    const { allowedMethods } = attributes;
    const [selectedPaymentMethod, setSelectedPaymentMethod] = useState(allowedMethods.length > 0 ? allowedMethods[0] : "");

    const blockProps = useBlockProps({
        className: "fcmanager-payment-details-editor"
    });

    const paymentMethods = [
        { value: 'direct_debit', label: __('Direct debit', 'football-club-manager') },
        { value: 'no_payment', label: __('No payment needed', 'football-club-manager') }
    ];

    const handleMethodChange = (method, checked) => {
        if (checked) {
            setAttributes({ allowedMethods: [...allowedMethods, method] });
            if (!selectedPaymentMethod) {
                setSelectedPaymentMethod(method);
            }
        } else {
            const newAllowedMethods = allowedMethods.filter(m => m !== method);
            setAttributes({ allowedMethods: newAllowedMethods });
            if (selectedPaymentMethod === method) {
                setSelectedPaymentMethod(newAllowedMethods.length > 0 ? newAllowedMethods[0] : "");
            }
        }
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__("Payment Methods", "football-club-manager")}>
                    {paymentMethods.map(method => (
                        <CheckboxControl
                            key={method.value}
                            label={method.label}
                            checked={allowedMethods.includes(method.value)}
                            onChange={(checked) => handleMethodChange(method.value, checked)}
                        />
                    ))}
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>

                {allowedMethods.length > 1 && (
                    <div className="fcmanager-form-grid fcmanager-form-grid--full">
                        <div className="fcmanager-form-field">
                            <label>
                                {__("Payment method", "football-club-manager")}
                                <select
                                    className="components-text-control__input"
                                    value={selectedPaymentMethod || ''}
                                    onChange={(e) => setSelectedPaymentMethod(e.target.value)}
                                >
                                    {allowedMethods.includes('direct_debit') && (
                                        <option value="direct_debit">{__("Direct debit", "football-club-manager")}</option>
                                    )}
                                    {allowedMethods.includes('no_payment') && (
                                        <option value="no_payment">{__("No payment needed", "football-club-manager")}</option>
                                    )}
                                </select>
                            </label>
                        </div>
                    </div>
                )}

                {allowedMethods.includes('direct_debit') && selectedPaymentMethod === 'direct_debit' && (
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
                )}

                {allowedMethods.includes('no_payment') && selectedPaymentMethod === 'no_payment' && (
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
                )}

            </div>
        </>
    );
}