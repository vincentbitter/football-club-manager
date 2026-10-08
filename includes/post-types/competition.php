<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-competition.php');

// Register Custom Post Type: Competition
function fcmanager_register_competition_post_type()
{
    register_post_type(
        'fcmanager_comp',
        array(
            'labels'        => array(
                'name'          => __('Competitions', 'football-club-manager'),
                'singular_name' => __('Competition', 'football-club-manager'),
                'add_new_item'     => __('New competition', 'football-club-manager'),
                'edit_item' => __('Edit competition', 'football-club-manager'),
                'not_found' => __('No competitions found', 'football-club-manager'),
                'not_found_in_trash' => __('No competitions found in the trash', 'football-club-manager'),
                'search_items' => __('Search competition', 'football-club-manager'),
            ),
            'show_ui'       => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports'      => array('title'),
        )
    );
}

// Unregister Custom Post Type: Competition
function fcmanager_unregister_competition_post_type()
{
    unregister_post_type('fcmanager_comp');
}

// Add custom meta boxes to competition
function fcmanager_add_competition_meta_boxes()
{
    add_meta_box(
        'fcmanager_comp_meta_box',
        __('Competition information', 'football-club-manager'),
        'fcmanager_render_competition_meta_box',
        'fcmanager_comp',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fcmanager_add_competition_meta_boxes');

// Render competition meta box
function fcmanager_render_competition_meta_box($post)
{
    // Retrieve current meta values
    $competition = new FCManager_Competition($post);

    // Show form
    wp_nonce_field('fcmanager_save_competition_meta_box', 'fcmanager_comp_meta_box_nonce');
?>
    <table style="width: 464px;" role="presentation">
        <thead>
            <tr>
                <th><label for="fcmanager_comp_start_date"><?php esc_attr_e('Start date', 'football-club-manager'); ?></label></th>
                <th><label for="fcmanager_comp_end_date"><?php esc_attr_e('End date', 'football-club-manager'); ?></label></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input style="width: 100%;" type="date" id="fcmanager_comp_start_date" name="fcmanager_comp_start_date" value="<?php echo esc_attr($competition->start_date() != null ? $competition->start_date()->format('Y-m-d') : ''); ?>"></td>
                <td><input style="width: 100%;" type="date" id="fcmanager_comp_end_date" name="fcmanager_comp_end_date" value="<?php echo esc_attr($competition->end_date() != null ? $competition->end_date()->format('Y-m-d') : ''); ?>"></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save competition meta box
function fcmanager_save_competition_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_comp')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_comp_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_competition_meta_box', 'fcmanager_comp_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save changes
    $competition = new FCManager_Competition($post_id);

    $competition->start_date(isset($_POST['fcmanager_comp_start_date']) ? new DateTime(wp_unslash($_POST['fcmanager_comp_start_date'])) : null);
    $competition->end_date(isset($_POST['fcmanager_comp_end_date']) ? new DateTime(wp_unslash($_POST['fcmanager_comp_end_date'])) : null);

    remove_action('save_post_fcmanager_comp', 'fcmanager_save_competition_meta_box');
    $competition->save();
}

add_action('save_post_fcmanager_comp', 'fcmanager_save_competition_meta_box');


// Add the custom columns to the competition post type:
function fcmanager_set_custom_edit_competition_columns($columns)
{
    unset($columns['date']);
    $columns['comp_start_date'] = __('Start date', 'football-club-manager');
    $columns['comp_end_date'] = __('End date', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_comp_posts_columns', 'fcmanager_set_custom_edit_competition_columns');


// Add the data to the custom columns for the competition post type:
function fcmanager_custom_competition_column($column, $post_id)
{
    $competition = new FCManager_Competition($post_id);
    switch ($column) {

        case 'comp_start_date':
            echo esc_html($competition->start_date() != null ? $competition->start_date()->format('Y-m-d') : '');
            break;

        case 'comp_end_date':
            echo esc_html($competition->end_date() != null ? $competition->end_date()->format('Y-m-d') : '');
            break;
    }
}

add_action('manage_fcmanager_comp_posts_custom_column', 'fcmanager_custom_competition_column', 10, 2);
