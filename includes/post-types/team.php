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
