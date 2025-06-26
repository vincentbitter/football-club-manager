<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type: Player
if (! function_exists('fcm_register_player_post_type')) {
    function fcm_register_player_post_type()
    {
        register_post_type(
            'fcm_player',
            array(
                'labels'        => array(
                    'name'          => __('Players', 'football-club-manager'),
                    'singular_name' => __('Player', 'football-club-manager'),
                    'add_new_item'     => __('New player', 'football-club-manager'),
                    'edit_item' => __('Edit player', 'football-club-manager'),
                    'featured_image' => __('Player photo', 'football-club-manager'),
                    'set_featured_image' => __('Set player photo', 'football-club-manager'),
                    'remove_featured_image' => __('Remove player photo', 'football-club-manager'),
                    'use_featured_image' => __('Use as player photo', 'football-club-manager'),
                    'not_found' => __('No players found', 'football-club-manager'),
                    'not_found_in_trash' => __('No players found in the trash', 'football-club-manager'),
                    'search_items' => __('Search player', 'football-club-manager'),
                ),
                'show_ui'       => true,
                'show_in_menu' => false,
                'supports'      => array('thumbnail'),
            )
        );
    }
}

// Unregister Custom Post Type: Player
if (! function_exists('fcm_unregister_player_post_type')) {
    function fcm_unregister_player_post_type()
    {
        unregister_post_type('fcm_player');
    }
}

// Add custom meta boxes to player
if (! function_exists('fcm_add_player_meta_boxes')) {
    function fcm_add_player_meta_boxes()
    {
        add_meta_box(
            'fcm_player_meta_box',
            __('Player information', 'football-club-manager'),
            'fcm_render_player_meta_box',
            'fcm_player',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'fcm_add_player_meta_boxes');
}

// Render player meta box
if (! function_exists('fcm_render_player_meta_box')) {
    function fcm_render_player_meta_box($post)
    {
        // Retreive current meta values
        $first_name = get_post_meta($post->ID, '_fcm_player_first_name', true);
        $last_name = get_post_meta($post->ID, '_fcm_player_last_name', true);
        $team = get_post_meta($post->ID, '_fcm_player_team', true);

        $team_options = fcm_get_teams();

        // Show form
        wp_nonce_field('fcm_player_meta_box_nonce', 'fcm_player_meta_box_nonce');
?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th><label for="fcm_player_first_name"><?php _e('First name', 'football-club-manager'); ?></label></th>
                    <td><input type="text" id="fcm_player_first_name" name="fcm_player_first_name" value="<?php echo esc_attr($first_name); ?>"></td>
                </tr>
                <tr>
                    <th><label for="fcm_player_last_name"><?php _e('Last name', 'football-club-manager'); ?></label></th>
                    <td><input type="text" id="fcm_player_last_name" name="fcm_player_last_name" value="<?php echo esc_attr($last_name); ?>"></td>
                </tr>
                <tr>
                    <th><label for="fcm_player_team"><?php _e('Team', 'football-club-manager'); ?></label></th>
                    <td>
                        <select id="fcm_player_team" name="fcm_player_team">
                            <?php foreach ($team_options as $team_option) : ?>
                                <option value="<?php echo $team_option->ID; ?>" <?php selected($team, $team_option->ID); ?>><?php echo $team_option->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
<?php
    }
}

// Save player meta box
if (! function_exists('fcm_save_player_meta_box')) {
    function fcm_save_player_meta_box($post_id)
    {
        // Check post type
        if (get_post_type($post_id) !== 'fcm_player')
            return;

        // Check nonce
        if (! isset($_POST['fcm_player_meta_box_nonce']) || ! wp_verify_nonce($_POST['fcm_player_meta_box_nonce'], 'fcm_player_meta_box_nonce'))
            return;

        // Check permissions
        if (! current_user_can('edit_post', $post_id))
            return;

        // Save meta values
        if (array_key_exists('fcm_player_first_name', $_POST))
            update_post_meta($post_id, '_fcm_player_first_name', sanitize_text_field($_POST['fcm_player_first_name']));
        if (array_key_exists('fcm_player_last_name', $_POST))
            update_post_meta($post_id, '_fcm_player_last_name', sanitize_text_field($_POST['fcm_player_last_name']));
        if (array_key_exists('fcm_player_team', $_POST))
            update_post_meta($post_id, '_fcm_player_team', sanitize_text_field($_POST['fcm_player_team']));

        // Update post with new title
        $name = get_post_meta($post_id, '_fcm_player_first_name', true) . ' ' . get_post_meta($post_id, '_fcm_player_last_name', true);
        $slug = sanitize_title($name);
        $post_update = array(
            'ID'         => $post_id,
            'post_title' => $name,
            'post_name' => $slug
        );
        remove_action('save_post', 'fcm_save_player_meta_box');
        wp_update_post($post_update);
    }
    add_action('save_post', 'fcm_save_player_meta_box');
}

// Add the custom columns to the player post type:
if (! function_exists('set_custom_edit_player_columns')) {

    function set_custom_edit_player_columns($columns)
    {
        unset($columns['title']);
        unset($columns['date']);
        $columns['player_first_name'] = __('First name', 'football-club-manager');
        $columns['player_last_name'] = __('Last name', 'football-club-manager');
        $columns['team_name'] = __('Team', 'football-club-manager');

        return $columns;
    }
    add_filter('manage_fcm_player_posts_columns', 'set_custom_edit_player_columns');
}

// Add the data to the custom columns for the player post type:
if (! function_exists('custom_player_column')) {
    function custom_player_column($column, $post_id)
    {
        switch ($column) {

            case 'player_first_name':
                echo get_post_meta($post_id, '_fcm_player_first_name', true);
                break;

            case 'player_last_name':
                echo get_post_meta($post_id, '_fcm_player_last_name', true);
                break;

            case 'team_name':
                $team_id = get_post_meta($post_id, '_fcm_player_team', true);
                $team = fcm_get_team($team_id);
                if ($team)
                    echo $team->post_title;
                break;
        }
    }
    add_action('manage_fcm_player_posts_custom_column', 'custom_player_column', 10, 2);
}
