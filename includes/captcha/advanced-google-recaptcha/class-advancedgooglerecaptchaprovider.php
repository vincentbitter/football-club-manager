<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(dirname(__DIR__, 2)) . 'includes/captcha/interface-captchaprovider.php';

class RecaptchaProvider implements CaptchaProvider
{
    public static function name(): string
    {
        return 'Advanced Google reCAPTCHA';
    }

    public static function available(): bool
    {
        return class_exists('WPCaptcha_Functions')
            && method_exists('WPCaptcha_Functions', 'captcha_fields_print')
            && method_exists('WPCaptcha_Functions', 'handle_captcha')
            && method_exists('WPCaptcha_Functions', 'login_enqueue_scripts');
    }

    public function render(): string
    {
        if (!self::available()) {
            return '';
        }

        WPCaptcha_Functions::login_enqueue_scripts();

        ob_start();
        WPCaptcha_Functions::captcha_fields_print();
        return ob_get_clean();
    }

    public function validate($request): bool
    {
        return self::available() && WPCaptcha_Functions::handle_captcha() === true;
    }
}
