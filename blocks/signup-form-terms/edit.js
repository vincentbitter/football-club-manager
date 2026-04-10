import { __ } from "@wordpress/i18n";
import {
    useBlockProps,
    RichText
} from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
    const { id, description } = attributes;

    if (!id) {
        setAttributes({ id: `terms_agreement_${Math.random().toString(36).substring(2, 11)}` });
    }

    const blockProps = useBlockProps({
        className: "fcmanager-signup-form-terms"
    });

    return (
        <>
            <div {...blockProps}>
                <label>
                    <input
                        type="checkbox"
                        checked
                        readOnly
                    />
                    <RichText
                        tagName="span"
                        value={description}
                        onChange={(value) => setAttributes({ description: value })}
                        placeholder={__("I agree to the terms and conditions.", "football-club-manager")}
                    />
                </label>
            </div>
        </>
    );
}