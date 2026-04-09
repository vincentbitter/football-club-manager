<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_parent_details_block($attributes, $content, $block)
{
    $parent = $attributes['parent'] ?? 'parent1';
    $required = $parent === 'parent1';

    ob_start();
?>
    <div class="fcmanager-parent-details-data">
        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('First name', 'football-club-manager'); ?>
                    <input type="text" name="<?php echo esc_attr($parent); ?>_first_name" value="<?php echo esc_attr($_POST[$parent . '_first_name'] ?? ''); ?>" <?php if ($required) echo 'required'; ?> />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Middle name', 'football-club-manager'); ?>
                    <input type="text" name="<?php echo esc_attr($parent); ?>_middle_name" value="<?php echo esc_attr($_POST[$parent . '_middle_name'] ?? ''); ?>" />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Last name', 'football-club-manager'); ?>
                    <input type="text" name="<?php echo esc_attr($parent); ?>_last_name" value="<?php echo esc_attr($_POST[$parent . '_last_name'] ?? ''); ?>" <?php if ($required) echo 'required'; ?> />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--double">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Mobile phone number', 'football-club-manager'); ?>
                    <input type="text" name="<?php echo esc_attr($parent); ?>_mobile_phone" value="<?php echo esc_attr($_POST[$parent . '_mobile_phone'] ?? ''); ?>" />
                </label>
            </div>

            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Phone number', 'football-club-manager'); ?>
                    <input type="text" name="<?php echo esc_attr($parent); ?>_phone" value="<?php echo esc_attr($_POST[$parent . '_phone'] ?? ''); ?>" />
                </label>
            </div>
        </div>

        <div class="fcmanager-form-grid fcmanager-form-grid--full">
            <div class="fcmanager-form-field">
                <label>
                    <?php esc_html_e('Email address', 'football-club-manager'); ?>
                    <input type="email" name="<?php echo esc_attr($parent); ?>_email" value="<?php echo esc_attr($_POST[$parent . '_email'] ?? ''); ?>" <?php if ($required) echo 'required'; ?> />
                </label>
            </div>
        </div>
    </div>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_parent_details_block',
]);
