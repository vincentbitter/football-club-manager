<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class FCManager_SignupType
{
    public const PLAYER = 'player';
    public const VOLUNTEER = 'volunteer';

    public static function values(): array
    {
        return [
            self::PLAYER,
            self::VOLUNTEER,
        ];
    }

    public static function __(string $value): string
    {
        switch (strtolower($value)) {
            case self::PLAYER:
                return __('Player', 'football-club-manager');
            case self::VOLUNTEER:
                return __('Volunteer', 'football-club-manager');
            default:
                return $value;
        }
    }

    public static function esc_html__(string $value): string
    {
        return esc_html(self::__($value));
    }

    public static function esc_html_e(string $value): void
    {
        echo esc_html(self::__($value));
    }
}
