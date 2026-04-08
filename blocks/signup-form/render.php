<?php

if (! defined('ABSPATH')) {
    exit;
}

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

function fcmanager_process_signup_form($block): ?FCManager_Signup
{
    $signup = new FCManager_Signup();
    $blocks = fcmanager_get_blocks($block->parsed_block);
    $success = true;

    foreach ($blocks as $inner_block) {
        switch ($inner_block['blockName']) {
            case 'fcmanager/signup-form-personal-details':
                $success &= $signup->personal_details($_POST);
                break;
            case 'fcmanager/signup-form-payment-details':
                $allowed_methods = $inner_block['attrs']['allowedMethods'] ?? [];
                $success &= $signup->payment_details($_POST, $allowed_methods);
                break;
            case 'fcmanager/signup-form-parent-details':
                $require_parents_till_age = FCManager_Settings::instance()->signup->require_parents_till_age();
                if ($require_parents_till_age && $require_parents_till_age > $signup->personal_details()->age()) {
                    $parent = ($inner_block['attrs']['parent'] ?? '') === 'parent2' ? 'parent2' : 'parent1';
                    $success &= $signup->{$parent}($_POST);
                }
                break;
            case 'fcmanager/signup-form-additional-information':
                $success &= $signup->additional_information($_POST);
                break;
        }
    }
    return $success ? $signup : null;
}

function fcmanager_render_signup_form_block($attributes, $content, $block)
{
    $redirectUrl = $attributes['redirectUrl'] ?? '';

    wp_enqueue_script('fcmanager-payment-details-toggle', plugins_url('public/js/signup.js', dirname(__DIR__)), ['jquery'], FCMANAGER_VERSION, true);

    if ($_POST && isset($_POST['fcmanager_nonce'])) {
        if (!wp_verify_nonce($_POST['fcmanager_nonce'], 'fcmanager_signup')) {
            return __('Error occurred while processing the form. Please try again.', 'football-club-manager');
        }

        $signup = fcmanager_process_signup_form($block);

        if (!$signup) {
            return __('Error occurred while processing the form. Please try again.', 'football-club-manager');
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

    ob_start();
?>
    <form class="fcmanager-signup-form" action="" method="post" data-require-parents-till-age="<?php echo esc_attr(FCManager_Settings::instance()->signup->require_parents_till_age()); ?>">

        <?php wp_nonce_field('fcmanager_signup', 'fcmanager_nonce'); ?>

        <?php echo $content; ?>
    </form>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_block',
]);
