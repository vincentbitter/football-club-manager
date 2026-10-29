<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-competition.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

/*
 * Get all competitions
 * 
 * @return FCManager_Competition[]
 */
function fcmanager_get_competitions($include = [])
{
    $posts = get_posts(array(
        'post_type' => 'fcmanager_comp',
        'post_status' => array('draft', 'publish'),
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'posts_per_page' => -1,
    ));

    $competitions = array_map(fn($post) => new FCManager_Competition($post), $posts);

    $competition_ids = array_map(fn($comp) => $comp->id(), $competitions);
    $missing = array_diff($include, $competition_ids);

    if (count($missing) > 0) {
        $missing_competitions = [];
        foreach ($missing as $id) {
            try {
                $missing_competitions[] = new FCManager_Competition($id);
            } catch (Exception $e) {
                continue;
            }
        }
        $competitions = array_merge($competitions, $missing_competitions);
    }

    return $competitions;
}

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
            'supports'      => array(''),
        )
    );
}

// Unregister Custom Post Type: Competition
function fcmanager_unregister_competition_post_type()
{
    unregister_post_type('fcmanager_comp');
}

// Add custom meta boxes to competition
function fcmanager_add_competition_meta_boxes($post_type, $post)
{;
    add_meta_box(
        'fcmanager_comp_meta_box',
        __('Competition information', 'football-club-manager'),
        'fcmanager_render_competition_meta_box',
        'fcmanager_comp',
        'normal',
        'high'
    );

    if ($post->post_status !== 'auto-draft') {
        add_meta_box(
            'fcmanager_pools',
            __('Pools', 'football-club-manager'),
            'fcmanager_render_pools_meta_box',
            'fcmanager_comp',
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'fcmanager_add_competition_meta_boxes', 10, 2);

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
                <th><label for="fcmanager_comp_name"><?php esc_attr_e('Name', 'football-club-manager'); ?></label></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input style="width: 100%;" type="text" id="fcmanager_comp_name" name="fcmanager_comp_name" value="<?php echo esc_attr($competition->name()); ?>"></td>
            </tr>
        </tbody>
    </table>
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

    $competition->name(sanitize_text_field(wp_unslash($_POST['fcmanager_comp_name'])));
    $competition->start_date(isset($_POST['fcmanager_comp_start_date']) ? new DateTime(wp_unslash($_POST['fcmanager_comp_start_date'])) : null);
    $competition->end_date(isset($_POST['fcmanager_comp_end_date']) ? new DateTime(wp_unslash($_POST['fcmanager_comp_end_date'])) : null);

    remove_action('save_post_fcmanager_comp', 'fcmanager_save_competition_meta_box');
    $competition->save();
}

add_action('save_post_fcmanager_comp', 'fcmanager_save_competition_meta_box');

class FCManager_Pools_List_Table extends WP_List_Table
{
    private $post_id = null;
    private $post_type = null;

    public function __construct($post_id)
    {
        $this->post_type = get_post_type_object('fcmanager_pool');

        parent::__construct(array(
            'singular' => $this->post_type->labels->singular_name,
            'plural'   => $this->post_type->labels->name,
            'ajax'     => false,
            'screen'   => 'fcmanager_pools',
        ));
        $this->post_id = $post_id;
    }

    public function get_columns()
    {
        return [
            'title' => __('Name', 'football-club-manager'),
        ];
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'title':
                return $item->post_title;
        }

        return '';
    }

    public function prepare_items()
    {
        $this->items = get_children([
            'post_parent' => $this->post_id,
            'post_type'   => 'fcmanager_pool',
            'post_status' => 'any',
            'numberposts' => -1,
            'orderby'     => 'title',
        ]);

        $this->_column_headers = [
            $this->get_columns(),
            [],
            [],
        ];
    }

    function column_title($item)
    {
        $return_url = admin_url(
            sprintf(
                'post.php?post=%d&action=edit',
                $this->post_id
            )
        );

        $actions = array(
            'edit'      => sprintf('<a href="%s">' . __('Edit', 'football-club-manager') . '</a>', get_edit_post_link($item->ID)),
            'delete'      => sprintf('<a href="%s">' . __('Delete', 'football-club-manager') . '</a>', get_delete_post_link($item->ID) . '&redirect=' . urlencode($return_url)),
        );

        return sprintf('%1$s %2$s', $item->post_title, $this->row_actions($actions));
    }

    function display_tablenav($which)
    {
        if ($which == "top") {
            echo '<div class="tablenav ' . esc_attr($which) . '">';
            echo '<a href="' . esc_url(admin_url('post-new.php?post_type=fcmanager_pool&post_parent=' . $this->post_id)) . '" class="button">' .
                esc_html($this->post_type->labels->add_new) . '</a>';
            echo '<br class="clear" /></div>';
        }
    }
}

function fcmanager_render_pools_meta_box($post)
{
    $table = new FCManager_Pools_List_Table($post->ID);

    $table->prepare_items();
    $table->display();
}

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

function fcmanager_delete_competition($post_id)
{
    $pools = get_children([
        'post_parent' => $post_id,
        'post_type'   => 'fcmanager_pool',
        'post_status' => 'any',
        'numberposts' => -1,
        'orderby'     => 'title',
    ]);

    foreach ($pools as $pool) {
        wp_delete_post($pool->ID, true);
    }
}
add_action('delete_post', 'fcmanager_delete_competition', 10, 1);
