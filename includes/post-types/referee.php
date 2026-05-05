<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-referee.php');

// Get all referees
function fcmanager_get_referees()
{
    return get_posts(array(
        'post_type' => 'fcmanager_referee',
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'posts_per_page' => -1
    ));
}

// Register Custom Post Type: Referee
function fcmanager_register_referee_post_type()
{
    register_post_type(
        'fcmanager_referee',
        array(
            'labels'        => array(
                'name'          => __('Referees', 'football-club-manager'),
                'singular_name' => __('Referee', 'football-club-manager'),
                'add_new_item'     => __('New referee', 'football-club-manager'),
                'edit_item' => __('Edit referee', 'football-club-manager'),
                'featured_image' => __('Referee photo', 'football-club-manager'),
                'set_featured_image' => __('Set referee photo', 'football-club-manager'),
                'remove_featured_image' => __('Remove referee photo', 'football-club-manager'),
                'use_featured_image' => __('Use as referee photo', 'football-club-manager'),
                'not_found' => __('No referees found', 'football-club-manager'),
                'not_found_in_trash' => __('No referees found in the trash', 'football-club-manager'),
                'search_items' => __('Search referee', 'football-club-manager'),
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
        'fcmanager_referee',
        'meta',
        array(
            'get_callback' => function ($post) {
                $referee = new FCManager_Referee($post['id']);
                $meta = get_post_meta($post['id']);
                $meta['_fcmanager_referee_date_of_birth'] = array($referee->date_of_birth());
                $meta['_fcmanager_referee_age'] = array($referee->age(false));
                return $meta;
            },
            'schema' => null,
        )
    );
    register_rest_field('fcmanager_referee', 'title', [
        'get_callback' => function ($post) {
            return get_the_title($post['id']);
        },
        'schema' => [
            'type'    => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
    register_rest_field('fcmanager_referee', 'photo', [
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

// Unregister Custom Post Type: Referee
function fcmanager_unregister_referee_post_type()
{
    unregister_post_type('fcmanager_referee');
}

// Add custom meta boxes to referee
function fcmanager_add_referee_meta_boxes()
{
    add_meta_box(
        'fcmanager_referee_meta_box',
        __('Referee information', 'football-club-manager'),
        'fcmanager_render_referee_meta_box',
        'fcmanager_referee',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'fcmanager_add_referee_meta_boxes');


// Render referee meta box
function fcmanager_render_referee_meta_box($post)
{
    // Retreive current meta values
    $referee = new FCManager_Referee($post);

    // Show form
    wp_nonce_field('fcmanager_save_referee_meta_box', 'fcmanager_referee_meta_box_nonce');
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="fcmanager_referee_first_name"><?php esc_html_e('First name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_referee_first_name" name="fcmanager_referee_first_name" value="<?php echo esc_attr($referee->first_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_referee_last_name"><?php esc_html_e('Last name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_referee_last_name" name="fcmanager_referee_last_name" value="<?php echo esc_attr($referee->last_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_referee_date_of_birth"><?php esc_html_e('Date of birth', 'football-club-manager'); ?></label></th>
                <td><input type="date" id="fcmanager_referee_date_of_birth" name="fcmanager_referee_date_of_birth" value="<?php echo esc_attr($referee->date_of_birth() != null ? $referee->date_of_birth()->format('Y-m-d') : ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_referee_publish_birthday"><?php esc_html_e('Publish birthday?', 'football-club-manager'); ?></label></th>
                <td><input type="checkbox" id="fcmanager_referee_publish_birthday" name="fcmanager_referee_publish_birthday" <?php checked($referee->publish_birthday(), true); ?>></td>
            </tr>
            <tr>
                <th><label for="fcmanager_referee_publish_age"><?php esc_html_e('Publish age?', 'football-club-manager'); ?></label></th>
                <td><input type="checkbox" id="fcmanager_referee_publish_age" name="fcmanager_referee_publish_age" <?php checked($referee->publish_age(), true); ?>></td>
            </tr>
        </tbody>
    </table>
<?php
}


// Save referee meta box
function fcmanager_save_referee_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_referee')
        return;

    // Allow skipping meta box save for import tools using wp_insert_post
    if (apply_filters('fcmanager_skip_meta_box_save', false))
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_referee_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_referee_meta_box', 'fcmanager_referee_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save meta values
    if (array_key_exists('fcmanager_referee_first_name', $_POST))
        update_post_meta($post_id, '_fcmanager_referee_first_name', sanitize_text_field(wp_unslash($_POST['fcmanager_referee_first_name'])));
    if (array_key_exists('fcmanager_referee_last_name', $_POST))
        update_post_meta($post_id, '_fcmanager_referee_last_name', sanitize_text_field(wp_unslash($_POST['fcmanager_referee_last_name'])));
    if (array_key_exists('fcmanager_referee_date_of_birth', $_POST))
        update_post_meta($post_id, '_fcmanager_referee_date_of_birth', sanitize_text_field(wp_unslash($_POST['fcmanager_referee_date_of_birth'])));
    $publish_birthday = array_key_exists('fcmanager_referee_publish_birthday', $_POST) ? 'true' : 'false';
    update_post_meta($post_id, '_fcmanager_referee_publish_birthday', $publish_birthday);
    $publish_age = array_key_exists('fcmanager_referee_publish_age', $_POST) ? 'true' : 'false';
    update_post_meta($post_id, '_fcmanager_referee_publish_age', $publish_age);

    // Update post with new title
    $name = get_post_meta($post_id, '_fcmanager_referee_first_name', true) . ' ' . get_post_meta($post_id, '_fcmanager_referee_last_name', true);
    $slug = sanitize_title($name);
    $post_update = array(
        'ID'         => $post_id,
        'post_title' => $name,
        'post_name' => $slug
    );
    remove_action('save_post_fcmanager_referee', 'fcmanager_save_referee_meta_box');
    wp_update_post($post_update);
}

add_action('save_post_fcmanager_referee', 'fcmanager_save_referee_meta_box');


// Add the custom columns to the referee post type:
function fcmanager_set_custom_edit_referee_columns($columns)
{
    unset($columns['title']);
    unset($columns['date']);
    $columns['referee_first_name'] = __('First name', 'football-club-manager');
    $columns['referee_last_name'] = __('Last name', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_referee_posts_columns', 'fcmanager_set_custom_edit_referee_columns');


// Add the data to the custom columns for the referee post type:
function fcmanager_custom_referee_column($column, $post_id)
{
    switch ($column) {

        case 'referee_first_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_referee_first_name', true));
            break;

        case 'referee_last_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_referee_last_name', true));
            break;
    }
}

add_action('manage_fcmanager_referee_posts_custom_column', 'fcmanager_custom_referee_column', 10, 2);

// Allow filtering referees via REST API
add_filter('rest_fcmanager_referee_query', function ($args, $request) {
    if ($meta_key = $request->get_param('meta_key')) {
        $meta_value = $request->get_param('meta_value');

        if ($meta_key === '_fcmanager_referee_date_of_birth') {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $meta_value)) {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_referee_date_of_birth',
                    'value' => $meta_value,
                    'compare' => '=',
                    'type' => 'DATE',
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_referee_publish_birthday',
                    'value' => 'true',
                    'compare' => '='
                );
            } elseif ($meta_value === 'today') {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_referee_date_of_birth',
                    'value' => '-' . wp_date('m-d'),
                    'compare' => 'LIKE'
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_referee_publish_birthday',
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

// Order referees by name in REST API
add_filter('rest_fcmanager_referee_query', function ($args, $request) {
    $args['orderby'] = 'title';
    $args['order'] = 'ASC';

    return $args;
}, 10, 2);
