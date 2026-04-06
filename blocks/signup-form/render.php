<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_block($attributes, $content, $block)
{
    $redirectUrl = $attributes['redirectUrl'] ?? '';

    wp_enqueue_script('fcmanager-payment-details-toggle', plugins_url('public/js/signup.js', dirname(__DIR__)), ['jquery'], FCMANAGER_VERSION, true);

    if ($_POST && isset($_POST['fcmanager_nonce'])) {
        if (!wp_verify_nonce($_POST['fcmanager_nonce'], 'fcmanager_signup')) {
            return __('Error occurred while processing the form. Please try again.', 'football-club-manager');
        }

        $signup = new FCManager_Signup();
        $success = true;

        $inner_blocks = $block->parsed_block['innerBlocks'];
        foreach ($inner_blocks as $inner_block) {
            if ($inner_block['blockName'] === 'fcmanager/signup-form-personal-details') {
                $success &= $signup->personal_details($_POST);
            } elseif ($inner_block['blockName'] === 'fcmanager/signup-form-payment-details') {
                $allowed_methods = $inner_block['attrs']['allowedMethods'];
                $success &= $signup->payment_details($_POST, $allowed_methods);
            }
        }

        if (!$success) {
            return __('Error occurred while processing the form. Please try again.', 'football-club-manager');
        } else {
            $signup->save();
            if ($redirectUrl) {
                wp_redirect($redirectUrl);
                exit;
            }
        }
    }

    ob_start();
?>
    <form class="fcmanager-signup-form" action="" method="post">

        <?php wp_nonce_field('fcmanager_signup', 'fcmanager_nonce'); ?>

        <?php echo $content; ?>

        <button type="submit">
            <?php echo __('Sign Up', 'football-club-manager'); ?>
    </form>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_block',
]);
