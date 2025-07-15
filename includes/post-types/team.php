<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Get all teams
if (! function_exists('fcm_get_teams')) {
    function fcm_get_teams()
    {
        return get_posts(array(
            'post_type' => 'fcm_team',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'suppress_filters' => false,
            'posts_per_page' => -1
        ));
    }
}

// Get team by ID
if (! function_exists('fcm_get_team')) {
    function fcm_get_team($team_id)
    {
        return get_post($team_id);
    }
}

// Register Custom Post Type: Team
if (! function_exists('fcm_register_team_post_type')) {
    function fcm_register_team_post_type()
    {
        register_post_type(
            'fcm_team',
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
}

// Unregister Custom Post Type: Team
if (! function_exists('fcm_unregister_team_post_type')) {
    function fcm_unregister_team_post_type()
    {
        unregister_post_type('fcm_team');
    }
}

function fcm_players_on_team_page($content)
{
    $hide = get_post_meta(get_the_ID(), 'fcm_show_players_block', true) == '0';
    if (get_post_type() === 'fcm_team' && get_theme_mod('fcm_show_players_block', true) && !$hide) {
        $extra_content = do_blocks(("<!-- wp:fcm/team-players /-->"));
        return $content . $extra_content;
    }
    return $content;
}
add_filter('the_content', 'fcm_players_on_team_page');

function fcm_customize_register($wp_customize)
{
    $wp_customize->add_setting('fcm_show_players_block', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ]);


    $wp_customize->add_control('fcm_show_players_block', [
        'label' => __('Players', 'football-club-manager'),
        'section' => 'post_type_single_fcm_team',
        'type' => 'checkbox',
        'priority' => 11
    ]);
}
add_action('customize_register', 'fcm_customize_register');

function fcm_add_players_toggle_meta_box()
{
    add_meta_box(
        'fcm_players_toggle',
        __('Elements', 'football-club-manager'),
        'fcm_render_players_toggle_meta_box',
        'fcm_team',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'fcm_add_players_toggle_meta_box');

function fcm_render_players_toggle_meta_box($post)
{
    $value = get_post_meta($post->ID, 'fcm_show_players_block', true) == '0' ? '0' : '1';
    wp_nonce_field('fcm_players_toggle_nonce', 'fcm_players_toggle_nonce');
?>
    <label>
        <input type="checkbox" name="fcm_show_players_block" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Show players', 'football-club-manager'); ?>
    </label>
<?php
}

function fcm_save_players_toggle_meta($post_id)
{
    if (!isset($_POST['fcm_players_toggle_nonce']) || !wp_verify_nonce($_POST['fcm_players_toggle_nonce'], 'fcm_players_toggle_nonce')) {
        return;
    }

    $value = isset($_POST['fcm_show_players_block']) ? '1' : '0';
    update_post_meta($post_id, 'fcm_show_players_block', $value);
}
add_action('save_post', 'fcm_save_players_toggle_meta');
