import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";

export default function Save({ attributes }) {
    const blockProps = useBlockProps.save({
        className: "wp-block-button__link fcmanager-signup-form-button",
        type: "submit"
    });
    return (
        <button {...blockProps}>
            <RichText.Content value={attributes.text} />
        </button>
    );
}
