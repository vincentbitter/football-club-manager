<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-pool.php');

// Register Custom Post Type: Pool
function fcmanager_register_pool_post_type()
{
    register_post_type(
        'fcmanager_pool',
        array(
            'labels'        => array(
                'name'          => __('Pools', 'football-club-manager'),
                'singular_name' => __('Pool', 'football-club-manager'),
                'add_new_item'     => __('New pool', 'football-club-manager'),
                'edit_item' => __('Edit pool', 'football-club-manager'),
                'not_found' => __('No pools found', 'football-club-manager'),
                'not_found_in_trash' => __('No pools found in the trash', 'football-club-manager'),
                'search_items' => __('Search pool', 'football-club-manager'),
            ),
            'show_ui'       => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports'      => array(''),
        )
    );
}

// Unregister Custom Post Type: Pool
function fcmanager_unregister_pool_post_type()
{
    unregister_post_type('fcmanager_pool');
}

// Add custom meta boxes to pool
function fcmanager_add_pool_meta_boxes()
{
    add_meta_box(
        'fcmanager_pool_meta_box',
        __('Pool information', 'football-club-manager'),
        'fcmanager_render_pool_meta_box',
        'fcmanager_pool',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fcmanager_add_pool_meta_boxes');

// Render pool meta box
function fcmanager_render_pool_meta_box($post)
{
    // Retrieve current meta values
    $pool = new FCManager_Pool($post);
    if ((!$pool->id() || $post->post_status === 'auto-draft') && isset($_GET['post_parent'])) {
        $pool->competition_id($_GET['post_parent']);
    }

    $competitions = fcmanager_get_competitions([$pool->competition_id()]);

    // Show form
    wp_nonce_field('fcmanager_save_pool_meta_box', 'fcmanager_pool_meta_box_nonce');
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="fcmanager_pool_competition"><?php esc_attr_e('Competition', 'football-club-manager'); ?></label></th>
                <td>
                    <select id="fcmanager_pool_competition" name="fcmanager_pool_competition" style="width: 100%;">
                        <?php foreach ($competitions as $competition) : ?>
                            <option value="<?php echo esc_attr($competition->id()); ?>" <?php selected($pool->competition_id(), $competition->id()); ?>><?php echo esc_html($competition->to_string()); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="fcmanager_pool_division"><?php esc_html_e('Division', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_pool_division" name="fcmanager_pool_division" value="<?php echo esc_attr($pool->division()); ?>" required style="width: 100%;"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_pool_name"><?php esc_html_e('Pool', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_pool_name" name="fcmanager_pool_name" value="<?php echo esc_attr($pool->name()); ?>" required style="width: 100%;"></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save pool meta box
function fcmanager_save_pool_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_pool')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_pool_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_pool_meta_box', 'fcmanager_pool_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save changes
    $pool = new FCManager_Pool($post_id);

    $pool->competition_id(sanitize_text_field(wp_unslash($_POST['fcmanager_pool_competition'])));
    $pool->division(sanitize_text_field(wp_unslash($_POST['fcmanager_pool_division'])));
    $pool->name(sanitize_text_field(wp_unslash($_POST['fcmanager_pool_name'])));

    remove_action('save_post_fcmanager_pool', 'fcmanager_save_pool_meta_box');
    $pool->save();
}

add_action('save_post_fcmanager_pool', 'fcmanager_save_pool_meta_box');

function fcmanager_pool_redirect_post_location($location, $post_id)
{

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'fcmanager_pool' || !$post->post_parent) {
        return $location;
    }

    return admin_url(
        sprintf(
            'post.php?post=%d&action=edit',
            $post->post_parent
        )
    );
}
add_filter('redirect_post_location', 'fcmanager_pool_redirect_post_location', 10, 2);
