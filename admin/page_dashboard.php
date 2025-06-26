<?php
if (! defined('ABSPATH')) {
    exit;
}

require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

if (! function_exists('fcm_page_dashboard')) {
    function fcm_page_dashboard()
    {
        if (is_blog_admin() && current_user_can('edit_posts')) {
            wp_add_dashboard_widget('fcm_dashboard_right_now', __('At a Glance'), 'fcm_dashboard_right_now');
        }

        wp_enqueue_script('dashboard');
?>
        <div class="wrap">
            <h1>Football Club Manager</h1>
            <div id="dashboard-widgets-wrap">
                <?php wp_dashboard(); ?>
            </div><!-- dashboard-widgets-wrap -->
        </div>
    <?php
    }
}

if (!function_exists('')) {
    function fcm_dashboard_right_now()
    {
    ?>
        <div class="main">
            <ul>
                <?php
                // Teams, players and matches
                foreach (array('fcm_team', 'fcm_player', 'fcm_match') as $post_type) {
                    $num_posts = wp_count_posts($post_type);

                    if ($num_posts) {
                        $post_type_object = get_post_type_object($post_type);
                        $text = '%s ' . __($post_type_object->labels->name);
                        $text = sprintf($text, number_format_i18n($num_posts->publish));

                        if ($post_type_object && current_user_can($post_type_object->cap->edit_posts)) {
                            printf('<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text);
                        } else {
                            printf('<li class="%1$s-count"><span>%2$s</span></li>', $post_type, $text);
                        }
                    }
                }
                ?>
            </ul>
        </div>
        <?php

        ob_start();

        $actions = ob_get_clean();

        if (! empty($actions)) :
        ?>
            <div class="sub">
                <?php echo $actions; ?>
            </div>
<?php
        endif;
    }
}
