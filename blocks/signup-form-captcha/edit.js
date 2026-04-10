import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit() {
    const blockProps = useBlockProps({
        className: "fcmanager-signup-form-captcha"
    });

    return (
        <div {...blockProps}>
            <div className="fcmanager-signup-form-captcha">
                <label>
                    <div style={{ padding: '10px', border: '1px dashed #ccc', background: '#f9f9f9' }}>
                        {__("Captcha will be displayed here on the frontend", "football-club-manager")}
                    </div>
                </label>
            </div>
        </div>
    );
}