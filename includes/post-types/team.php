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

function fcmanager_players_on_team_page()
{
    $hide = get_post_meta(get_the_ID(), 'fcmanager_show_players_block', true) == '0';
    if (get_theme_mod('fcmanager_show_players_block', true) && !$hide) {
        return "<!-- wp:fcmanager/team-players /-->";
    }
}

function fcmanager_results_on_team_page()
{
    $hide = get_post_meta(get_the_ID(), 'fcmanager_show_results_block', true) == '0';
    if (get_theme_mod('fcmanager_show_results_block', true) && !$hide) {
        return "<!-- wp:fcmanager/team-results /-->";
    }
}

function fcmanager_schedule_on_team_page()
{
    $hide = get_post_meta(get_the_ID(), 'fcmanager_show_schedule_block', true) == '0';
    if (get_theme_mod('fcmanager_show_schedule_block', true) && !$hide) {
        return "<!-- wp:fcmanager/team-schedule /-->";
    }
}

function fcmanager_blocks_on_team_page($content)
{
    if (get_post_type() != 'fcmanager_team')
        return $content;

    $extra_content = fcmanager_players_on_team_page();
    $results = fcmanager_results_on_team_page();
    $schedule = fcmanager_schedule_on_team_page();
    if ($results || $schedule) {
        $extra_content .= "<!-- wp:columns -->"
            . "<div class=\"wp-block-columns\"><!-- wp:column -->"
            . "<div class=\"wp-block-column\">" . $results . "</div>"
            . "<!-- /wp:column -->"
            . "<!-- wp:column -->"
            . "<div class=\"wp-block-column\">" . $schedule . "</div>"
            . "<!-- /wp:column --></div>"
            . "<!-- /wp:columns -->";
    }
    return $content . do_blocks($extra_content);
}
add_filter('the_content', 'fcmanager_blocks_on_team_page');

function fcmanager_customize_register_team_page($wp_customize)
{
    $wp_customize->add_section('fcmanager_team_page', [
        'title' => __('Team Page Settings', 'football-club-manager'),
        'priority' => 10,
        'panel' => 'fcmanager_panel',
    ]);

    $wp_customize->add_setting('fcmanager_show_players_block', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ]);

    $wp_customize->add_control('fcmanager_show_players_block', [
        'label' => __('Show players', 'football-club-manager'),
        'section' => 'fcmanager_team_page',
        'type' => 'checkbox',
        'priority' => 10
    ]);

    $wp_customize->add_setting('fcmanager_show_results_block', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ]);

    $wp_customize->add_control('fcmanager_show_results_block', [
        'label' => __('Show results', 'football-club-manager'),
        'section' => 'fcmanager_team_page',
        'type' => 'checkbox',
        'priority' => 11
    ]);

    $wp_customize->add_setting('fcmanager_show_schedule_block', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ]);

    $wp_customize->add_control('fcmanager_show_schedule_block', [
        'label' => __('Show schedule', 'football-club-manager'),
        'section' => 'fcmanager_team_page',
        'type' => 'checkbox',
        'priority' => 12
    ]);
}

function fcmanager_add_blocks_toggle_meta_box()
{
    add_meta_box(
        'fcmanager_blocks_toggle',
        __('Elements', 'football-club-manager'),
        'fcmanager_render_blocks_toggle_meta_box',
        'fcmanager_team',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'fcmanager_add_blocks_toggle_meta_box');

function fcmanager_render_block_toggle_meta_box($post, $block, $label)
{
    $value = get_post_meta($post->ID, 'fcmanager_show_' . $block . '_block', true) == '0' ? '0' : '1';
?>
    <div>
        <label>
            <input type="checkbox" name="fcmanager_show_<?php echo esc_attr($block); ?>_block" value="1" <?php checked($value, '1'); ?>>
            <?php echo esc_html($label); ?>
        </label>
    </div>
<?php
}

function fcmanager_render_blocks_toggle_meta_box($post)
{
    wp_nonce_field('fcmanager_block_toggle_nonce', 'fcmanager_block_toggle_nonce');

    fcmanager_render_block_toggle_meta_box($post, 'players', __('Show players', 'football-club-manager'));
    fcmanager_render_block_toggle_meta_box($post, 'schedule', __('Show schedule', 'football-club-manager'));
    fcmanager_render_block_toggle_meta_box($post, 'results', __('Show results', 'football-club-manager'));
}

function fcmanager_save_block_toggle_meta_field($post_id, $block)
{
    $value = isset($_POST['fcmanager_show_' . $block . '_block']) ? '1' : '0';
    update_post_meta($post_id, 'fcmanager_show_' . $block . '_block', $value);
}


function fcmanager_save_block_toggle_meta($post_id)
{
    if (!array_key_exists('fcmanager_block_toggle_nonce', $_POST) || !check_admin_referer('fcmanager_block_toggle_nonce', 'fcmanager_block_toggle_nonce'))
        return;

    remove_action('save_post_fcmanager_team', 'fcmanager_save_block_toggle_meta');
    fcmanager_save_block_toggle_meta_field($post_id, 'players');
    fcmanager_save_block_toggle_meta_field($post_id, 'schedule');
    fcmanager_save_block_toggle_meta_field($post_id, 'results');
}
add_action('save_post_fcmanager_team', 'fcmanager_save_block_toggle_meta');

function fcmanager_find_team_by_player($query)
{
    if (! $query->is_search || is_admin()) {
        return;
    }

    $search_term = trim($query->query_vars['s']);
    if (! $search_term) {
        return;
    }

    global $wpdb;

    $terms = preg_split('/\s+/', trim($search_term));

    $team_ids = $wpdb->get_col($wpdb->prepare(
        "
    SELECT pm.meta_value
    FROM $wpdb->posts p
    INNER JOIN $wpdb->postmeta pm
        ON pm.post_id = p.ID
        AND pm.meta_key = '_fcmanager_player_team'
    WHERE p.post_type = 'fcmanager_player'
      AND p.post_status = 'publish'
      AND " . implode(' AND ', array_fill(0, count($terms), 'p.post_title LIKE %s')),
        ...array_map(
            fn($t) => '%' . $wpdb->esc_like($t) . '%',
            $terms
        )
    ));


    if (empty($team_ids)) {
        return;
    }
    $query->set('s', null);
    $query->set('post__in', $team_ids);
}
add_action('pre_get_posts', 'fcmanager_find_team_by_player');
