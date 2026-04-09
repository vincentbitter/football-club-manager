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

    ob_start();
?>
    <div class="fcmanager-signup-additional-information">
        <?php foreach ($extra_fields as $field) : ?>
            <div class="fcmanager-form-grid fcmanager-form-grid--full">
                <div class="fcmanager-form-field">
                    <label>
                        <?php echo esc_html($field); ?>
                        <input
                            type="text"
                            name="<?php echo esc_attr('fcmanager_signup_additional_information[' . $field . ']'); ?>"
                            value="<?php echo esc_attr($_POST['fcmanager_signup_additional_information'][$field] ?? ''); ?>" />
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
