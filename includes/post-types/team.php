<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Get all teams
function fcmanager_get_teams()
{
    return get_posts(array(
        'post_type' => 'fcmanager_team',
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'posts_per_page' => -1
    ));
}


// Get team by ID
function fcmanager_get_team($team_id)
{
    return get_post($team_id);
}


// Register Custom Post Type: Team
function fcmanager_register_team_post_type()
{
    register_post_type(
        'fcmanager_team',
        array(
            'rest_base' => 'teams',
            'rewrite'   => array('slug' => 'teams'),
            'labels'    => array(
                'name'          => __('Teams', 'football-club-manager'),
                'singular_name' => __('Team', 'football-club-manager'),
                'add_new_item'     => __('New team', 'football-club-manager'),
                'edit_item' => __('Edit team', 'football-club-manager'),
                'featured_image' => __('Team photo', 'football-club-manager'),
                'set_featured_image' => __('Set team photo', 'football-club-manager'),
                'remove_featured_image' => __('Remove team photo', 'football-club-manager'),
                'use_featured_image' => __('Use as team photo', 'football-club-manager'),
                'not_found' => __('No teams found', 'football-club-manager'),
                'not_found_in_trash' => __('No teams found in the trash', 'football-club-manager'),
                'search_items' => __('Search team', 'football-club-manager'),
            ),
            'public'    => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'show_in_admin_bar' => true,
            'supports'  => array('title', 'thumbnail', 'editor'),
        )
    );
}


// Unregister Custom Post Type: Team
function fcmanager_unregister_team_post_type()
{
    unregister_post_type('fcmanager_team');
}

function fcmanager_players_on_team_page($content)
{
    $hide = get_post_meta(get_the_ID(), 'fcmanager_show_players_block', true) == '0';
    if (get_post_type() === 'fcmanager_team' && get_theme_mod('fcmanager_show_players_block', true) && !$hide) {
        $extra_content = do_blocks(("<!-- wp:fcmanager/team-players /-->"));
        return $content . $extra_content;
    }
    return $content;
}
add_filter('the_content', 'fcmanager_players_on_team_page');

function fcmanager_customize_register($wp_customize)
{
    $wp_customize->add_setting('fcmanager_show_players_block', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ]);


    $wp_customize->add_control('fcmanager_show_players_block', [
        'label' => __('Players', 'football-club-manager'),
        'section' => 'post_type_single_fcmanager_team',
        'type' => 'checkbox',
        'priority' => 11
    ]);
}
add_action('customize_register', 'fcmanager_customize_register');

function fcmanager_add_players_toggle_meta_box()
{
    add_meta_box(
        'fcmanager_players_toggle',
        __('Elements', 'football-club-manager'),
        'fcmanager_render_players_toggle_meta_box',
        'fcmanager_team',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'fcmanager_add_players_toggle_meta_box');

function fcmanager_render_players_toggle_meta_box($post)
{
    $value = get_post_meta($post->ID, 'fcmanager_show_players_block', true) == '0' ? '0' : '1';
    wp_nonce_field('fcmanager_players_toggle_nonce', 'fcmanager_players_toggle_nonce');
?>
    <label>
        <input type="checkbox" name="fcmanager_show_players_block" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Show players', 'football-club-manager'); ?>
    </label>
<?php
}

function fcmanager_save_players_toggle_meta($post_id)
{
    if (!isset($_POST['fcmanager_players_toggle_nonce']) || !wp_verify_nonce($_POST['fcmanager_players_toggle_nonce'], 'fcmanager_players_toggle_nonce')) {
        return;
    }

    $value = isset($_POST['fcmanager_show_players_block']) ? '1' : '0';
    update_post_meta($post_id, 'fcmanager_show_players_block', $value);
}
add_action('save_post', 'fcmanager_save_players_toggle_meta');
