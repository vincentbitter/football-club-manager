<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_additional_information_block($attributes, $content, $block)
{
    $extra_fields = FCManager_Settings::instance()->signup->extra_fields();
    if (empty($extra_fields) || !is_array($extra_fields)) {
        return '';
    }

    $posted = ! empty($_POST) ? wp_unslash($_POST) : [];
    $additional_information = isset($posted['fcmanager_signup_additional_information']) && is_array($posted['fcmanager_signup_additional_information']) ? $posted['fcmanager_signup_additional_information'] : [];

    ob_start();
?>
    <div class="fcmanager-signup-additional-information">
        <?php foreach ($extra_fields as $field) :
            $label = $field['label']; ?>
            <div class="fcmanager-form-grid fcmanager-form-grid--full">
                <div class="fcmanager-form-field">
                    <label>
                        <?php echo esc_html($label); ?>
                        <input
                            type="text"
                            name="<?php echo esc_attr('fcmanager_signup_additional_information[' . $label . ']'); ?>"
                            value="<?php echo esc_attr(sanitize_text_field($additional_information[$label] ?? '')); ?>" />
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_additional_information_block',
]);
