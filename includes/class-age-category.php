<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class FCManager_AgeCategory
{
    public const YOUTH = 'youth';
    public const SENIORS = 'seniors';

    public static function values(): array
    {
        return [
            self::YOUTH,
            self::SENIORS
        ];
    }

    public static function __(string $value): string
    {
        switch (strtolower($value)) {
            case self::YOUTH:
                return __('Youth', 'football-club-manager');
            case self::SENIORS:
                return __('Seniors', 'football-club-manager');
            default:
                return $value;
        }
    }

    public static function esc_html_e(string $value): void
    {
        echo esc_html(self::__($value));
    }
}
