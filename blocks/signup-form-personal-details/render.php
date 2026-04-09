<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_personal_details_block($attributes, $content, $block)
{
    ob_start();
?>
    <div class="fcmanager-personal-details">

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('First name', 'football-club-manager'); ?>
                    <input type="text" name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('Initials', 'football-club-manager'); ?>
                        <input type="text" name="initials" value="<?php echo esc_attr($_POST['initials'] ?? ''); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('Middle name', 'football-club-manager'); ?>
                        <input type="text" name="middle_name" value="<?php echo esc_attr($_POST['middle_name'] ?? ''); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Last name', 'football-club-manager'); ?>
                    <input type="text" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Date of birth', 'football-club-manager'); ?>
                    <input
                        class="fcmanager-personal-details-date-of-birth"
                        type="date"
                        name="date_of_birth"
                        value="<?php echo esc_attr($_POST['date_of_birth'] ?? ''); ?>"
                        required />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Gender', 'football-club-manager'); ?>
                    <select name="gender" required>
                        <option value="">
                            <?php esc_html_e('Select…', 'football-club-manager'); ?>
                        </option>
                        <option value="male" <?php selected($_POST['gender'] ?? '', 'male'); ?>>
                            <?php esc_html_e('Male', 'football-club-manager'); ?>
                        </option>
                        <option value="female" <?php selected($_POST['gender'] ?? '', 'female'); ?>>
                            <?php esc_html_e('Female', 'football-club-manager'); ?>
                        </option>
                        <option value="gender neutral" <?php selected($_POST['gender'] ?? '', 'gender neutral'); ?>>
                            <?php esc_html_e('Gender neutral', 'football-club-manager'); ?>
                        </option>
                    </select>
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Street', 'football-club-manager'); ?>
                    <input type="text" name="street" value="<?php echo esc_attr($_POST['street'] ?? ''); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('House number', 'football-club-manager'); ?>
                        <input type="text" name="house_number" value="<?php echo esc_attr($_POST['house_number'] ?? ''); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('House number addition', 'football-club-manager'); ?>
                        <input type="text" name="house_number_addition" value="<?php echo esc_attr($_POST['house_number_addition'] ?? ''); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Postal code', 'football-club-manager'); ?>
                    <input type="text" name="postal_code" value="<?php echo esc_attr($_POST['postal_code'] ?? ''); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('City', 'football-club-manager'); ?>
                    <input type="text" name="city" value="<?php echo esc_attr($_POST['city'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Mobile phone number', 'football-club-manager'); ?>
                    <input type="text" name="mobile_phone" value="<?php echo esc_attr($_POST['mobile_phone'] ?? ''); ?>" />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Phone number', 'football-club-manager'); ?>
                    <input type="text" name="phone" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Email address', 'football-club-manager'); ?>
                    <input type="email" name="email" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

    </div>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_personal_details_block',
]);
