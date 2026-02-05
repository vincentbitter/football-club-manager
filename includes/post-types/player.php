<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-settings.php');
require_once(dirname(__FILE__) . '/../class-player.php');

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
                $player = new FCManager_Player($post['id']);
                $meta = get_post_meta($post['id']);
                $meta['_fcmanager_player_date_of_birth'] = array($player->date_of_birth());
                $meta['_fcmanager_player_age'] = array($player->age(false));
                return $meta;
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
    $date_of_birth = get_post_meta($post->ID, '_fcmanager_player_date_of_birth', true);
    $publish_birthday = $post->post_status !== 'auto-draft' ? get_post_meta($post->ID, '_fcmanager_player_publish_birthday', true) === 'true' : FCManager_Settings::instance()->player->publish_birthday_by_default();
    $publish_age = $post->post_status !== 'auto-draft' ? get_post_meta($post->ID, '_fcmanager_player_publish_age', true) === 'true' : FCManager_Settings::instance()->player->publish_age_by_default();
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
                <th><label for="fcmanager_player_date_of_birth"><?php esc_html_e('Date of birth', 'football-club-manager'); ?></label></th>
                <td><input type="date" id="fcmanager_player_date_of_birth" name="fcmanager_player_date_of_birth" value="<?php echo esc_attr($date_of_birth); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_player_publish_birthday"><?php esc_html_e('Publish birthday?', 'football-club-manager'); ?></label></th>
                <td><input type="checkbox" id="fcmanager_player_publish_birthday" name="fcmanager_player_publish_birthday" <?php checked($publish_birthday, true); ?>></td>
            </tr>
            <tr>
                <th><label for="fcmanager_player_publish_age"><?php esc_html_e('Publish age?', 'football-club-manager'); ?></label></th>
                <td><input type="checkbox" id="fcmanager_player_publish_age" name="fcmanager_player_publish_age" <?php checked($publish_age, true); ?>></td>
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
    if (array_key_exists('fcmanager_player_date_of_birth', $_POST))
        update_post_meta($post_id, '_fcmanager_player_date_of_birth', sanitize_text_field(wp_unslash($_POST['fcmanager_player_date_of_birth'])));
    $publish_birthday = array_key_exists('fcmanager_player_publish_birthday', $_POST) ? 'true' : 'false';
    update_post_meta($post_id, '_fcmanager_player_publish_birthday', $publish_birthday);
    $publish_age = array_key_exists('fcmanager_player_publish_age', $_POST) ? 'true' : 'false';
    update_post_meta($post_id, '_fcmanager_player_publish_age', $publish_age);

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

add_action('manage_fcmanager_player_posts_custom_column', 'fcmanager_custom_player_column', 10, 2);

// Allow filtering players via REST API
add_filter('rest_fcmanager_player_query', function ($args, $request) {
    if ($meta_key = $request->get_param('meta_key')) {
        $meta_value = $request->get_param('meta_value');

        if ($meta_key === '_fcmanager_player_date_of_birth') {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $meta_value)) {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_player_date_of_birth',
                    'value' => $meta_value,
                    'compare' => '=',
                    'type' => 'DATE',
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_player_publish_birthday',
                    'value' => 'true',
                    'compare' => '='
                );
            } elseif ($meta_value === 'today') {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_player_date_of_birth',
                    'value' => '-' . wp_date('m-d'),
                    'compare' => 'LIKE'
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_player_publish_birthday',
                    'value' => 'true',
                    'compare' => '='
                );
            }
        } else {
            $args['meta_query'] = array(
                [
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => '=',
                    'type' => 'NUMERIC',
                ],
            );
        }
    }
    return $args;
}, 10, 2);

// Order players by name in REST API
add_filter('rest_fcmanager_player_query', function ($args, $request) {
    $args['orderby'] = 'title';
    $args['order'] = 'ASC';

    return $args;
}, 10, 2);
