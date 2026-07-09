<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class FCManager_TeamGender
{
    public const MALE = 'male';
    public const FEMALE = 'female';
    public const MIXED = 'mixed';

    public static function values(): array
    {
        return [
            self::MALE,
            self::FEMALE,
            self::MIXED,
        ];
    }

    public static function __(string $value): string
    {
        switch (strtolower($value)) {
            case self::MALE:
                return __('Male', 'football-club-manager');
            case self::FEMALE:
                return __('Female', 'football-club-manager');
            case self::MIXED:
                return __('Mixed', 'football-club-manager');
            default:
                return $value;
        }
    }

    public static function esc_html_e(string $value): void
    {
        echo esc_html(self::__($value));
    }
}
