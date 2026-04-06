import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";

export default function Save() {
    return (
        <div {...useBlockProps.save()}>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("First name", "football-club-manager")}
                        <input type="text" name="first_name" required />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("Initials", "football-club-manager")}
                            <input type="text" name="initials" required />
                        </label>
                    </div>

                    <div className="fcmanager-form-field">
                        <label>
                            {__("Middle name", "football-club-manager")}
                            <input type="text" name="middle_name" />
                        </label>
                    </div>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Last name", "football-club-manager")}
                        <input type="text" name="last_name" required />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Date of birth", "football-club-manager")}
                        <input type="date" name="date_of_birth" required />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Gender", "football-club-manager")}
                        <select name="gender" required>
                            <option value="">
                                {__("Select…", "football-club-manager")}
                            </option>
                            <option value="male">
                                {__("Male", "football-club-manager")}
                            </option>
                            <option value="female">
                                {__("Female", "football-club-manager")}
                            </option>
                            <option value="gender neutral">
                                {__("Gender neutral", "football-club-manager")}
                            </option>
                        </select>
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Street", "football-club-manager")}
                        <input type="text" name="street" required />
                    </label>
                </div>
                <div className="fcmanager-form-grid fcmanager-form-grid--double">
                    <div className="fcmanager-form-field">
                        <label>
                            {__("House number", "football-club-manager")}
                            <input type="text" name="house_number" required />
                        </label>
                    </div>

                    <div className="fcmanager-form-field">
                        <label>
                            {__("House number addition", "football-club-manager")}
                            <input type="text" name="house_number_suffix" />
                        </label>
                    </div>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Postal code", "football-club-manager")}
                        <input type="text" name="postal_code" required />
                    </label>
                </div>
                <div className="fcmanager-form-field">
                    <label>
                        {__("City", "football-club-manager")}
                        <input type="text" name="city" required />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--double">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Mobile phone number", "football-club-manager")}
                        <input type="text" name="mobile_phone" />
                    </label>
                </div>

                <div className="fcmanager-form-field">
                    <label>
                        {__("Phone number", "football-club-manager")}
                        <input type="text" name="phone" />
                    </label>
                </div>
            </div>

            <div className="fcmanager-form-grid fcmanager-form-grid--full">
                <div className="fcmanager-form-field">
                    <label>
                        {__("Email address", "football-club-manager")}
                        <input type="email" name="email" required />
                    </label>
                </div>
            </div>

        </div>
    );
}
