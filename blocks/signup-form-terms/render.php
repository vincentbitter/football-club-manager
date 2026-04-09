<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_terms_block($attributes, $content, $block)
{
    $id = $attributes['id'] ?? '';
    $description = $attributes['description'] ?? __('I agree to the terms and conditions.', 'football-club-manager');

    ob_start();
?>
    <div class="fcmanager-signup-form-terms">
        <label>
            <input
                type="checkbox"
                name="<?php echo esc_attr($id); ?>"
                value="on"
                <?php checked($_POST[$id] ?? '', 'on'); ?>
                required />
            <?php echo wp_kses_post($description); ?>
        </label>
    </div>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_terms_block',
]);
