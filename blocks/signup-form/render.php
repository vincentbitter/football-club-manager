<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_block($attributes, $content, $block)
{
    $redirectUrl = $attributes['redirectUrl'] ?? '';

    if ($_POST) {
        $inner_blocks = $block->parsed_block['innerBlocks'];

        if ($redirectUrl) {
            wp_redirect($redirectUrl);
            exit;
        }
    }

    ob_start();
?>
    <form class="fcmanager-signup-form" action="" method="post">

        <?php wp_nonce_field('fcmanager_signup', 'fcmanager_nonce'); ?>

        <?php echo $content; ?>

    </form>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_block',
]);
