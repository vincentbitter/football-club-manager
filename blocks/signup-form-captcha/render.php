<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(dirname(__DIR__)) . 'includes/captcha/class-captchaproviderfactory.php');

function fcmanager_render_signup_form_captcha_block($attributes, $content, $block)
{
    $provider = FCManager_CaptchaProviderFactory::get_default_provider();
    if (!$provider) {
        return '';
    }

    return '<div class="fcmanager-signup-form-captcha">' . $provider->render() . '</div>';
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_captcha_block',
]);
