<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(dirname(__DIR__)) . 'includes/captcha/class-captchaproviderfactory.php');

/**
 * @param array $block
 * @return array(array)
 */
function fcmanager_get_blocks($block): array
{
    $blocks = $block['innerBlocks'] ?? [];

    foreach ($blocks as $inner_block) {
        $blocks = array_merge(
            $blocks,
            fcmanager_get_blocks($inner_block)
        );
    }

    return $blocks;
}

function fcmanager_process_signup_form($block, $attributes, $post_data): ?FCManager_Signup
{
    $signup = new FCManager_Signup();
    $signup->type($attributes['signupType'] ?? FCManager_SignupType::PLAYER);
    $signup->subtype($attributes['signupSubtype'] ?? '');

    $blocks = fcmanager_get_blocks($block->parsed_block);
    $success = true;

    foreach ($blocks as $inner_block) {
        switch ($inner_block['blockName']) {
            case 'fcmanager/signup-form-personal-details':
                $success &= $signup->personal_details($post_data);
                break;
            case 'fcmanager/signup-form-payment-details':
                $allowed_methods = $inner_block['attrs']['allowedMethods'] ?? [];
                $success &= $signup->payment_details($post_data, $allowed_methods);
                break;
            case 'fcmanager/signup-form-parent-details':
                $require_parents_till_age = FCManager_Settings::instance()->signup->require_parents_till_age();
                if ($require_parents_till_age && $require_parents_till_age > $signup->personal_details()->age()) {
                    $parent = ($inner_block['attrs']['parent'] ?? '') === 'parent2' ? 'parent2' : 'parent1';
                    $success &= $signup->{$parent}($post_data);
                }
                break;
            case 'fcmanager/signup-form-additional-information':
                $success &= $signup->additional_information($post_data);
                break;
            case 'fcmanager/signup-form-terms':
                $terms_id = $inner_block['attrs']['id'];
                $success &= isset($post_data[$terms_id]) && $post_data[$terms_id] === 'on';
                break;
            case 'fcmanager/signup-form-captcha':
                $provider = FCManager_CaptchaProviderFactory::get_default_provider();
                if ($provider) {
                    $success &= $provider->validate($post_data);
                }
                break;
        }
    }

    return $success ? $signup : null;
}

function fcmanager_render_signup_form_block($attributes, $content, $block)
{
    $redirectUrl = $attributes['redirectUrl'] ?? '';
    $error_message = '';
    $post_data = ! empty($_POST) ? wp_unslash($_POST) : [];

    wp_enqueue_script('fcmanager-payment-details-toggle', plugins_url('public/js/signup.js', dirname(__DIR__)), ['jquery'], FCMANAGER_VERSION, true);

    if (! empty($post_data) && isset($post_data['fcmanager_nonce'])) {
        if (!wp_verify_nonce(sanitize_text_field($post_data['fcmanager_nonce']), 'fcmanager_signup')) {
            $error_message = __('Error occurred while processing the form. Please try again.', 'football-club-manager');
        } else {
            $signup = fcmanager_process_signup_form($block, $attributes, $post_data);

            if (!$signup) {
                $error_message = __('Error occurred while processing the form. Please try again.', 'football-club-manager');
            } else {
                $signup->save();
                if ($redirectUrl) {
                    wp_redirect($redirectUrl);
                    exit;
                } else {
                    wp_redirect(add_query_arg('signup', 'success', get_permalink()));
                    exit;
                }
            }
        }
    }

    ob_start();
?>
    <form class="fcmanager-signup-form" action="" method="post" data-require-parents-till-age="<?php echo esc_attr(FCManager_Settings::instance()->signup->require_parents_till_age()); ?>">
        <?php if ($error_message): ?>
            <div class="fcmanager-form-error">
                <?php echo esc_html($error_message); ?>
            </div>
        <?php endif; ?>
        <?php wp_nonce_field('fcmanager_signup', 'fcmanager_nonce'); ?>
        <?php echo $content; ?>
    </form>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_block',
]);
