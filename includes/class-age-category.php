<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class FCManager_AgeCategory
{
    public const YOUTH = 'youth';
    public const SENIOR = 'senior';

    public static function values(): array
    {
        return [
            self::YOUTH,
            self::SENIOR
        ];
    }

    public static function __(string $value): string
    {
        switch (strtolower($value)) {
            case self::YOUTH:
                return __('Youth', 'football-club-manager');
            case self::SENIOR:
                return __('Senior', 'football-club-manager');
            default:
                return $value;
        }
    }

    public static function esc_html_e(string $value): void
    {
        echo esc_html(self::__($value));
    }
}
