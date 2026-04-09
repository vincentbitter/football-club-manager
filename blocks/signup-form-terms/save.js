import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";

export default function Save({ attributes }) {
    const { id, description } = attributes;

    const blockProps = useBlockProps.save({
        className: "fcmanager-signup-form-terms"
    });

    return (
        <div {...blockProps}>
            <label>
                <input
                    type="checkbox"
                    name={id}
                    required
                />
                <RichText.Content value={description} />
            </label>
        </div>
    );
}