<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-settings.php');
require_once(dirname(__FILE__) . '/../class-birthday.php');

// Register Custom Post Type: Birthday
function fcmanager_register_birthday_post_type()
{
    register_post_type(
        'fcmanager_birthday',
        array(
            'labels'        => array(
                'name'          => __('Birthdays', 'football-club-manager'),
                'singular_name' => __('Birthday', 'football-club-manager'),
                'add_new_item'     => __('New birthday', 'football-club-manager'),
                'edit_item' => __('Edit birthday', 'football-club-manager'),
                'not_found' => __('No birthdays found', 'football-club-manager'),
                'not_found_in_trash' => __('No birthdays found in the trash', 'football-club-manager'),
                'search_items' => __('Search birthday', 'football-club-manager'),
            ),
            'show_ui'       => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports'      => array(''),
        )
    );
}

add_action('rest_api_init', function () {
    register_rest_field(
        'fcmanager_birthday',
        'meta',
        array(
            'get_callback' => function ($post) {
                $birthday = new FCManager_Birthday($post['id']);
                $meta = get_post_meta($post['id']);
                $meta['_fcmanager_birthday_date_of_birth'] = array($birthday->date_of_birth());
                $meta['_fcmanager_birthday_age'] = array($birthday->age(false));
                return $meta;
            },
            'schema' => null,
        )
    );
    register_rest_field('fcmanager_birthday', 'title', [
        'get_callback' => function ($post) {
            return get_the_title($post['id']);
        },
        'schema' => [
            'type'    => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
});

// Unregister Custom Post Type: Birthday
function fcmanager_unregister_birthday_post_type()
{
    unregister_post_type('fcmanager_birthday');
}

// Add custom meta boxes to birthday
function fcmanager_add_birthday_meta_boxes()
{
    add_meta_box(
        'fcmanager_birthday_meta_box',
        __('Birthday information', 'football-club-manager'),
        'fcmanager_render_birthday_meta_box',
        'fcmanager_birthday',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'fcmanager_add_birthday_meta_boxes');


// Render birthday meta box
function fcmanager_render_birthday_meta_box($post)
{
    // Retreive current meta values
    $birthday = new FCManager_Birthday($post);

    // Show form
    wp_nonce_field('fcmanager_save_birthday_meta_box', 'fcmanager_birthday_meta_box_nonce');
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="fcmanager_birthday_first_name"><?php esc_html_e('First name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_birthday_first_name" name="fcmanager_birthday_first_name" value="<?php echo esc_attr($birthday->first_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_birthday_last_name"><?php esc_html_e('Last name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_birthday_last_name" name="fcmanager_birthday_last_name" value="<?php echo esc_attr($birthday->last_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_birthday_date_of_birth"><?php esc_html_e('Date of birth', 'football-club-manager'); ?></label></th>
                <td><input type="date" id="fcmanager_birthday_date_of_birth" name="fcmanager_birthday_date_of_birth" value="<?php echo esc_attr($birthday->date_of_birth() != null ? $birthday->date_of_birth()->format('Y-m-d') : ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_birthday_publish_age"><?php esc_html_e('Publish age?', 'football-club-manager'); ?></label></th>
                <td><input type="checkbox" id="fcmanager_birthday_publish_age" name="fcmanager_birthday_publish_age" <?php checked($birthday->publish_age(), true); ?>></td>
            </tr>
        </tbody>
    </table>
<?php
}


// Save birthday meta box
function fcmanager_save_birthday_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_birthday')
        return;

    // Allow skipping meta box save for import tools using wp_insert_post
    if (apply_filters('fcmanager_skip_meta_box_save', false))
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_birthday_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_birthday_meta_box', 'fcmanager_birthday_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save meta values
    if (array_key_exists('fcmanager_birthday_first_name', $_POST))
        update_post_meta($post_id, '_fcmanager_birthday_first_name', sanitize_text_field(wp_unslash($_POST['fcmanager_birthday_first_name'])));
    if (array_key_exists('fcmanager_birthday_last_name', $_POST))
        update_post_meta($post_id, '_fcmanager_birthday_last_name', sanitize_text_field(wp_unslash($_POST['fcmanager_birthday_last_name'])));
    if (array_key_exists('fcmanager_birthday_date_of_birth', $_POST))
        update_post_meta($post_id, '_fcmanager_birthday_date_of_birth', sanitize_text_field(wp_unslash($_POST['fcmanager_birthday_date_of_birth'])));
    update_post_meta($post_id, '_fcmanager_birthday_publish_birthday', 'true');
    $publish_age = array_key_exists('fcmanager_birthday_publish_age', $_POST) ? 'true' : 'false';
    update_post_meta($post_id, '_fcmanager_birthday_publish_age', $publish_age);

    // Update post with new title
    $name = get_post_meta($post_id, '_fcmanager_birthday_first_name', true) . ' ' . get_post_meta($post_id, '_fcmanager_birthday_last_name', true);
    $slug = sanitize_title($name);
    $post_update = array(
        'ID'         => $post_id,
        'post_title' => $name,
        'post_name' => $slug
    );
    remove_action('save_post_fcmanager_birthday', 'fcmanager_save_birthday_meta_box');
    wp_update_post($post_update);
}

add_action('save_post_fcmanager_birthday', 'fcmanager_save_birthday_meta_box');


// Add the custom columns to the birthday post type:
function fcmanager_set_custom_edit_birthday_columns($columns)
{
    unset($columns['title']);
    unset($columns['date']);
    $columns['birthday_first_name'] = __('First name', 'football-club-manager');
    $columns['birthday_last_name'] = __('Last name', 'football-club-manager');
    $columns['birthday_birthday'] = __('Birthday', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_birthday_posts_columns', 'fcmanager_set_custom_edit_birthday_columns');


// Add the data to the custom columns for the birthday post type:
function fcmanager_custom_birthday_column($column, $post_id)
{
    switch ($column) {

        case 'birthday_first_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_birthday_first_name', true));
            break;

        case 'birthday_last_name':
            echo esc_html(get_post_meta($post_id, '_fcmanager_birthday_last_name', true));
            break;

        case 'birthday_birthday':
            echo esc_html(wp_date('j F', strtotime(get_post_meta($post_id, '_fcmanager_birthday_date_of_birth', true))));
            break;
    }
}

add_action('manage_fcmanager_birthday_posts_custom_column', 'fcmanager_custom_birthday_column', 10, 2);

// Allow filtering birthday via REST API
add_filter('rest_fcmanager_birthday_query', function ($args, $request) {
    if ($meta_key = $request->get_param('meta_key')) {
        $meta_value = $request->get_param('meta_value');

        if ($meta_key === '_fcmanager_birthday_date_of_birth') {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $meta_value)) {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_birthday_date_of_birth',
                    'value' => $meta_value,
                    'compare' => '=',
                    'type' => 'DATE',
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_birthday_publish_birthday',
                    'value' => 'true',
                    'compare' => '='
                );
            } elseif ($meta_value === 'today') {
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_birthday_date_of_birth',
                    'value' => '-' . wp_date('m-d'),
                    'compare' => 'LIKE'
                );
                $args['meta_query'][] = array(
                    'key' => '_fcmanager_birthday_publish_birthday',
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

// Order birthdays by name in REST API
add_filter('rest_fcmanager_birthday_query', function ($args, $request) {
    $args['orderby'] = 'title';
    $args['order'] = 'ASC';

    return $args;
}, 10, 2);
