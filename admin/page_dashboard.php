<?php
if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_add_dashboard_widget($widget_id, $widget_name, $callback, $context = 'normal')
{
    $screen = get_current_screen();
    add_meta_box($widget_id, $widget_name, $callback, $screen, $context);
}

function fcmanager_dashboard()
{
    $screen = get_current_screen();
?>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" class="postbox-container">
            <?php do_meta_boxes($screen->id, 'normal', ''); ?>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <?php do_meta_boxes($screen->id, 'side', ''); ?>
        </div>
        <div id="postbox-container-3" class="postbox-container">
            <?php do_meta_boxes($screen->id, 'column3', ''); ?>
        </div>
        <div id="postbox-container-4" class="postbox-container">
            <?php do_meta_boxes($screen->id, 'column4', ''); ?>
        </div>
    </div>

<?php
    wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
    wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
}

function fcmanager_dashboard_setup()
{
    if (is_blog_admin() && current_user_can('edit_posts')) {
        fcmanager_add_dashboard_widget('fcmanager_dashboard_right_now', __('At a Glance', 'football-club-manager'), 'fcmanager_dashboard_right_now');
    }
}

function fcmanager_page_dashboard()
{
    fcmanager_dashboard_setup();
    wp_enqueue_script('dashboard');
?>
    <div class="wrap">
        <h1>Football Club Manager</h1>
        <div id="dashboard-widgets-wrap">
            <?php fcmanager_dashboard(); ?>
        </div><!-- dashboard-widgets-wrap -->
    </div>
<?php
}

function fcmanager_dashboard_right_now()
{
?>
    <div class="main">
        <ul>
            <?php
            // Teams, players and matches
            foreach (array('fcmanager_team', 'fcmanager_player', 'fcmanager_match') as $post_type) {
                $num_posts = wp_count_posts($post_type);

                if ($num_posts) {
                    $post_type_object = get_post_type_object($post_type);
                    $text = '%s ' . $post_type_object->labels->name;
                    $text = sprintf($text, number_format_i18n($num_posts->publish));

                    if ($post_type_object && current_user_can($post_type_object->cap->edit_posts)) {
                        printf('<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', esc_attr($post_type), esc_html($text));
                    } else {
                        printf('<li class="%1$s-count"><span>%2$s</span></li>', esc_attr($post_type), esc_html($text));
                    }
                }
            }
            ?>
        </ul>
    </div>
<?php
}
