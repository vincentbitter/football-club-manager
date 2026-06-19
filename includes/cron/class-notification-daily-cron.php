<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Notification_Daily_Cron
{
    public static function run()
    {
        $time = time();

        $new_signups = self::get_signups_since(self::last_sent_timestamp());
        $counts = array_reduce($new_signups, function ($carry, $signup) {
            $type = $signup->type();
            $carry[$type] = ($carry[$type] ?? 0) + 1;
            return $carry;
        }, []);
        foreach ($counts as $type => $count) {
            self::send_notification($type, $count);
        }

        self::last_sent_timestamp($time);
    }

    /**
     * @param int|null $timestamp
     * @return int
     */
    private static function last_sent_timestamp($timestamp = null): int
    {
        if ($timestamp) {
            update_option('fcmanager_cron_notification_daily_last_timestamp', $timestamp);
            return $timestamp;
        }
        return (int) get_option('fcmanager_cron_notification_daily_last_timestamp', 0);
    }

    /**
     * @return FCManager_Signup[]
     */
    private static function get_signups_since(int $timestamp): array
    {
        return array_map(
            fn($post) => new FCManager_Signup($post),
            get_posts([
                'post_type' => 'fcmanager_signup',
                'posts_per_page' => -1,
                'date_query' => [
                    'after' => gmdate('Y-m-d H:i:s', $timestamp)
                ]
            ])
        );
    }

    private static function send_notification(string $type, int $count): void
    {
        $receivers = get_users([
            'meta_key' => 'fcmanager_notification_signup_' . $type,
            'meta_value' => 'immediately',
        ]);

        foreach ($receivers as $user) {
            $html = self::compose_mail(self::compose_mail_body($type, $count));
            /* translators: 1: number of new signups, 2: signup type */
            wp_mail($user->user_email, sprintf(esc_html__('%1$u New %2$s signups', 'football-club-manager'), $count, strtolower(FCManager_SignupType::esc_html__($type))), $html, ['Content-Type: text/html; charset=UTF-8']);
        }
    }

    private static function compose_mail(string $body): string
    {
        return "<!DOCTYPE html>
        <html><head><style>body {
                font-family: Arial, sans-serif;
                font-size: 13px;
                margin: 20px;
            }</style></head><body>" . $body . "</body></html>";
    }

    private static function compose_mail_body(string $type, int $count): string
    {
        return "<p>" .
            sprintf(
                /* translators: 1: number of new signups, 2: signup type, 3: admin page link ('the admin page') */
                esc_html__('There are %1$u new %2$s signups! Please check the details on %3$s.', 'football-club-manager'),
                $count,
                strtolower(FCManager_SignupType::esc_html__($type)),
                '<a href="' . admin_url('edit.php?post_type=fcmanager_signup') . '">' . esc_html__('the admin page', 'football-club-manager') . '</a>'
            ) . "</p>";
    }
}
