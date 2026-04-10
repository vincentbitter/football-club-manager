<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_personal_details_block($attributes, $content, $block)
{
    $posted = ! empty($_POST) ? wp_unslash($_POST) : [];

    ob_start();
?>
    <div class="fcmanager-personal-details">

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('First name', 'football-club-manager'); ?>
                    <input type="text" name="first_name" value="<?php echo esc_attr(sanitize_text_field($posted['first_name'] ?? '')); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('Initials', 'football-club-manager'); ?>
                        <input type="text" name="initials" value="<?php echo esc_attr(sanitize_text_field($posted['initials'] ?? '')); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('Middle name', 'football-club-manager'); ?>
                        <input type="text" name="middle_name" value="<?php echo esc_attr(sanitize_text_field($posted['middle_name'] ?? '')); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Last name', 'football-club-manager'); ?>
                    <input type="text" name="last_name" value="<?php echo esc_attr(sanitize_text_field($posted['last_name'] ?? '')); ?>" required />
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
                        value="<?php echo esc_attr(sanitize_text_field($posted['date_of_birth'] ?? '')); ?>"
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
                        <option value="male" <?php selected(sanitize_text_field($posted['gender'] ?? ''), 'male'); ?>>
                            <?php esc_html_e('Male', 'football-club-manager'); ?>
                        </option>
                        <option value="female" <?php selected(sanitize_text_field($posted['gender'] ?? ''), 'female'); ?>>
                            <?php esc_html_e('Female', 'football-club-manager'); ?>
                        </option>
                        <option value="gender neutral" <?php selected(sanitize_text_field($posted['gender'] ?? ''), 'gender neutral'); ?>>
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
                    <input type="text" name="street" value="<?php echo esc_attr(sanitize_text_field($posted['street'] ?? '')); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('House number', 'football-club-manager'); ?>
                        <input type="text" name="house_number" value="<?php echo esc_attr(sanitize_text_field($posted['house_number'] ?? '')); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php esc_html_e('House number addition', 'football-club-manager'); ?>
                        <input type="text" name="house_number_addition" value="<?php echo esc_attr(sanitize_text_field($posted['house_number_addition'] ?? '')); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Postal code', 'football-club-manager'); ?>
                    <input type="text" name="postal_code" value="<?php echo esc_attr(sanitize_text_field($posted['postal_code'] ?? '')); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('City', 'football-club-manager'); ?>
                    <input type="text" name="city" value="<?php echo esc_attr(sanitize_text_field($posted['city'] ?? '')); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Mobile phone number', 'football-club-manager'); ?>
                    <input type="text" name="mobile_phone" value="<?php echo esc_attr(sanitize_text_field($posted['mobile_phone'] ?? '')); ?>" />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Phone number', 'football-club-manager'); ?>
                    <input type="text" name="phone" value="<?php echo esc_attr(sanitize_text_field($posted['phone'] ?? '')); ?>" />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Email address', 'football-club-manager'); ?>
                    <input type="email" name="email" value="<?php echo esc_attr(sanitize_text_field($posted['email'] ?? '')); ?>" required />
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
