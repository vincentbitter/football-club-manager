<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type: Player
function fcmanager_register_player_post_type()
{
    register_post_type(
        'fcmanager_player',
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
            'show_in_rest' => true,
            'supports'      => array('thumbnail'),
        )
    );
}

add_action('rest_api_init', function () {
    register_rest_field(
        'fcmanager_player',
        'meta',
        array(
            'get_callback' => function ($post) {
                return get_post_meta($post['id']);
            },
            'schema' => null,
        )
    );
    register_rest_field('fcmanager_player', 'title', [
        'get_callback' => function ($post) {
            return get_the_title($post['id']);
        },
        'schema' => [
            'type'    => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
    register_rest_field('fcmanager_player', 'photo', [
        'get_callback' => function ($post) {
            $img_id = get_post_thumbnail_id($post['id']);
            if (!$img_id) return null;
            $img_src = wp_get_attachment_image_src($img_id, 'medium');
            return $img_src ? $img_src[0] : null;
        },
        'schema' => [
            'type' => 'string',
            'format' => 'uri',
            'context' => ['view', 'edit'],
        ],
    ]);
});

// Unregister Custom Post Type: Player
function fcmanager_unregister_player_post_type()
{
    unregister_post_type('fcmanager_player');
}

// Add custom meta boxes to player
function fcmanager_add_player_meta_boxes()
{
    add_meta_box(
        'fcmanager_player_meta_box',
        __('Player information', 'football-club-manager'),
        'fcmanager_render_player_meta_box',
        'fcmanager_player',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'fcmanager_add_player_meta_boxes');


// Render player meta box
function fcmanager_render_player_meta_box($post)
{
    // Retreive current meta values
    $first_name = get_post_meta($post->ID, '_fcmanager_player_first_name', true);
    $last_name = get_post_meta($post->ID, '_fcmanager_player_last_name', true);
    $team = get_post_meta($post->ID, '_fcmanager_player_team', true);

    $team_options = fcmanager_get_teams();

    // Show form
    wp_nonce_field('fcmanager_save_player_meta_box', 'fcmanager_player_meta_box_nonce');
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="fcmanager_player_first_name"><?php esc_html_e('First name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_player_first_name" name="fcmanager_player_first_name" value="<?php echo esc_attr($first_name); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_player_last_name"><?php esc_html_e('Last name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_player_last_name" name="fcmanager_player_last_name" value="<?php echo esc_attr($last_name); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_player_team"><?php esc_html_e('Team', 'football-club-manager'); ?></label></th>
                <td>
                    <select id="fcmanager_player_team" name="fcmanager_player_team">
                        <?php foreach ($team_options as $team_option) : ?>
                            <option value="<?php echo esc_attr($team_option->ID); ?>" <?php selected($team, $team_option->ID); ?>><?php echo esc_html($team_option->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}


// Save player meta box
function fcmanager_save_player_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_player')
        return;

    // Allow skipping meta box save for import tools using wp_insert_post
    if (apply_filters('fcmanager_skip_meta_box_save', false))
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_player_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_player_meta_box', 'fcmanager_player_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save meta values
    if (array_key_exists('fcmanager_player_first_name', $_POST))
        update_post_meta($post_id, '_fcmanager_player_first_name', sanitize_text_field(wp_unslash($_POST['fcmanager_player_first_name'])));
    if (array_key_exists('fcmanager_player_last_name', $_POST))
        update_post_meta($post_id, '_fcmanager_player_last_name', sanitize_text_field(wp_unslash($_POST['fcmanager_player_last_name'])));
    if (array_key_exists('fcmanager_player_team', $_POST))
        update_post_meta($post_id, '_fcmanager_player_team', sanitize_text_field(wp_unslash($_POST['fcmanager_player_team'])));

    // Update post with new title
    $name = get_post_meta($post_id, '_fcmanager_player_first_name', true) . ' ' . get_post_meta($post_id, '_fcmanager_player_last_name', true);
    $slug = sanitize_title($name);
    $post_update = array(
        'ID'         => $post_id,
        'post_title' => $name,
        'post_name' => $slug
    );
    remove_action('save_post_fcmanager_player', 'fcmanager_save_player_meta_box');
    wp_update_post($post_update);
}

add_action('save_post_fcmanager_player', 'fcmanager_save_player_meta_box');


// Add the custom columns to the player post type:
function fcmanager_set_custom_edit_player_columns($columns)
{
    unset($columns['title']);
    unset($columns['date']);
    $columns['player_first_name'] = __('First name', 'football-club-manager');
    $columns['player_last_name'] = __('Last name', 'football-club-manager');
    $columns['team_name'] = __('Team', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_player_posts_columns', 'fcmanager_set_custom_edit_player_columns');


// Add the data to the custom columns for the player post type:
function fcmanager_custom_player_column($column, $post_id)
{
    switch ($column) {

        case 'player_first_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_player_first_name', true));
            break;

        case 'player_last_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_player_last_name', true));
            break;

        case 'team_name':
            $team_id = get_post_meta($post_id, '_fcmanager_player_team', true);
            $team = fcmanager_get_team($team_id);
            if ($team)
                echo esc_html($team->post_title);
            break;
    }
}

add_action('manage_fcmanager_player_posts_custom_column', 'custom_player_column', 10, 2);

// Allow filtering players via REST API
add_filter('rest_fcmanager_player_query', function ($args, $request) {
    if ($meta_key = $request->get_param('meta_key')) {
        $args['meta_key'] = $meta_key;
        $args['meta_value'] = $request->get_param('meta_value');
    }
    return $args;
}, 10, 2);
