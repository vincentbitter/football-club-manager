<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../../admin/page_print_signup.php');

class FCManager_Notification_Immediately_Cron
{
    public static function run()
    {
        $time = time();

        if (!self::try_get_lock($time)) {
            return;
        }

        $new_signups = self::get_signups_since(self::last_sent_timestamp());
        foreach ($new_signups as $signup) {
            self::send_notification($signup);
        }

        self::last_sent_timestamp($time);
        self::release_lock();
    }

    /**
     * @return bool Succeeded to get lock
     */
    private static function try_get_lock(int $timestamp): bool
    {
        $lock = get_option('fcmanager_cron_notification_immediately_in_progress', 0);
        if ($lock > $timestamp - 30 * 60) {
            return false;
        }

        update_option('fcmanager_cron_notification_immediately_in_progress', $timestamp);
        return true;
    }

    private static function release_lock(): void
    {
        delete_option('fcmanager_cron_notification_immediately_in_progress');
    }

    /**
     * @param int|null $timestamp
     * @return int
     */
    private static function last_sent_timestamp($timestamp = null): int
    {
        if ($timestamp) {
            update_option('fcmanager_cron_notification_immediately_last_timestamp', $timestamp);
            return $timestamp;
        }
        return (int) get_option('fcmanager_cron_notification_immediately_last_timestamp', 0);
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
                    'after' => wp_date('Y-m-d H:i:s', $timestamp)
                ]
            ])
        );
    }

    private static function send_notification(FCManager_Signup $signup)
    {
        $receivers = get_users([
            'meta_key' => 'fcmanager_notification_signup_' . $signup->type(),
            'meta_value' => 'immediately',
        ]);

        foreach ($receivers as $user) {
            $include = get_user_meta($user->ID, 'fcmanager_notification_signup_' . $signup->type() . '_include_data', true) === 'true';
            $html = self::compose_mail($include ? self::compose_mail_body_with_data($signup) : self::compose_mail_body_without_data($signup));
            wp_mail($user->user_email, esc_html__('New signup', 'football-club-manager'), $html, ['Content-Type: text/html; charset=UTF-8']);
        }
    }

    private static function compose_mail(string $body): string
    {
        return "<!DOCTYPE html>
            <html><head><style>body {
                font-family: Arial, sans-serif;
                font-size: 13px;
                margin: 20px !important;
            }</style>" . fcmanager_page_print_get_style() . "</head><body>" . $body . "</body></html>";
    }

    private static function compose_mail_body_with_data(FCManager_Signup $signup): string
    {
        return
            /* translators: admin page link ('the admin page') */
            "<p>" . sprintf(esc_html__('There is a new signup! Please check the details below or on %s.', 'football-club-manager'), '<a href="' . admin_url('post.php?action=edit&post=' . $signup->id()) . '">' . esc_html__('the admin page', 'football-club-manager') . '</a>') . "</p>" .
            fcmanager_page_print_get_body($signup);
    }

    private static function compose_mail_body_without_data(FCManager_Signup $signup): string
    {
        /* translators: admin page link ('the admin page') */
        return "<p>" . sprintf(esc_html__('There is a new signup! Please check the details on %s.', 'football-club-manager'), '<a href="' . admin_url('post.php?action=edit&post=' . $signup->id()) . '">' . esc_html__('the admin page', 'football-club-manager') . '</a>') . "</p>";
    }
}
