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
                    <?php _e('First name', 'football-club-manager'); ?>
                    <input type="text" name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Initials', 'football-club-manager'); ?>
                        <input type="text" name="initials" value="<?php echo esc_attr($_POST['initials'] ?? ''); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Middle name', 'football-club-manager'); ?>
                        <input type="text" name="middle_name" value="<?php echo esc_attr($_POST['middle_name'] ?? ''); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Last name', 'football-club-manager'); ?>
                    <input type="text" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Date of birth', 'football-club-manager'); ?>
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
                    <?php _e('Gender', 'football-club-manager'); ?>
                    <select name="gender" required>
                        <option value="">
                            <?php _e('Select…', 'football-club-manager'); ?>
                        </option>
                        <option value="male" <?php selected($_POST['gender'] ?? '', 'male'); ?>>
                            <?php _e('Male', 'football-club-manager'); ?>
                        </option>
                        <option value="female" <?php selected($_POST['gender'] ?? '', 'female'); ?>>
                            <?php _e('Female', 'football-club-manager'); ?>
                        </option>
                        <option value="gender neutral" <?php selected($_POST['gender'] ?? '', 'gender neutral'); ?>>
                            <?php _e('Gender neutral', 'football-club-manager'); ?>
                        </option>
                    </select>
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Street', 'football-club-manager'); ?>
                    <input type="text" name="street" value="<?php echo esc_attr($_POST['street'] ?? ''); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-grid fcmanager-form-grid--double">
                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('House number', 'football-club-manager'); ?>
                        <input type="text" name="house_number" value="<?php echo esc_attr($_POST['house_number'] ?? ''); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('House number addition', 'football-club-manager'); ?>
                        <input type="text" name="house_number_suffix" value="<?php echo esc_attr($_POST['house_number_suffix'] ?? ''); ?>" />
                    </label>
                </div>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Postal code', 'football-club-manager'); ?>
                    <input type="text" name="postal_code" value="<?php echo esc_attr($_POST['postal_code'] ?? ''); ?>" required />
                </label>
            </div>
            <div class="fcmanager-form-field">
                <label>
                    <?php _e('City', 'football-club-manager'); ?>
                    <input type="text" name="city" value="<?php echo esc_attr($_POST['city'] ?? ''); ?>" required />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Mobile phone number', 'football-club-manager'); ?>
                    <input type="text" name="mobile_phone" value="<?php echo esc_attr($_POST['mobile_phone'] ?? ''); ?>" />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Phone number', 'football-club-manager'); ?>
                    <input type="text" name="phone" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php _e('Email address', 'football-club-manager'); ?>
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
