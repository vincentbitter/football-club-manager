<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once('advanced-google-recaptcha/class-advancedgooglerecaptchaprovider.php');

class FCManager_CaptchaProviderFactory
{
    /** @var CaptchaProvider[] $providers */
    private static array $providers = [RecaptchaProvider::class];

    /**
     * Get the default captcha provider as set on the settings page.
     *
     * @return CaptchaProvider|null
     */
    public static function get_default_provider(): ?CaptchaProvider
    {
        $captcha_provider = FCManager_Settings::instance()->signup->captcha_provider();
        return self::get_provider($captcha_provider);
    }

    /**
     * Get a captcha provider by name.
     *
     * @param string $name
     * @return CaptchaProvider|null
     */
    public static function get_provider($name): ?CaptchaProvider
    {
        foreach (self::$providers as $provider) {
            if ($provider::name() === $name && $provider::available()) {
                return new $provider();
            }
        }

        return null;
    }

    /**
     * Get the names of all available captcha providers.
     * 
     * @return string[]
     */
    public static function get_providers(): array
    {

        /** @var string[] $provider_names */
        $provider_names = [];

        foreach (self::$providers as $provider) {
            if ($provider::available()) {
                $provider_names[] = $provider::name();
            }
        }

        return $provider_names;
    }
}
