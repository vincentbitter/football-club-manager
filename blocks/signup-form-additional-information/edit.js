import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit() {

    const blockProps = useBlockProps({
        className: "fcmanager-signup-additional-information"
    });

    return (
        <div {...blockProps}>
            <div class="fcmanager-form-grid fcmanager-form-grid--full">
                <div class="fcmanager-form-field">
                    <label>
                        {__("How did you hear about us? (example)", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            text="Social media"
                            readOnly />
                    </label>
                </div>
                <div class="fcmanager-form-field">
                    <label>
                        {__("Have you played for another football club? (example)", "football-club-manager")}
                        <input
                            className="components-text-control__input"
                            type="text"
                            text="Yes"
                            readOnly />
                    </label>
                </div>
            </div>
        </div>
    );
}
