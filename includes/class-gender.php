<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Gender
{
    public const MALE = 'male';
    public const FEMALE = 'female';
    public const GENDER_NEUTRAL = 'gender neutral';

    public static function values(): array
    {
        return [
            self::MALE,
            self::FEMALE,
            self::GENDER_NEUTRAL,
        ];
    }

    public static function __(string $value): string
    {
        switch (strtolower($value)) {
            case self::MALE:
                return __('Male', 'football-club-manager');
            case self::FEMALE:
                return __('Female', 'football-club-manager');
            case self::GENDER_NEUTRAL:
                return __('Gender neutral', 'football-club-manager');
            default:
                return $value;
        }
    }

    public static function esc_html_e(string $value): void
    {
        echo esc_html(self::__($value));
    }
}
