import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: "wp-block-button__link fcmanager-signup-form-button"
    });

    return (
        <div {...blockProps}>
            <RichText
                tagName="span"
                value={attributes.text}
                onChange={(text) => setAttributes({ text })}
                placeholder={__("Sign up", "football-club-manager")}
            />
        </div>
    );
}
