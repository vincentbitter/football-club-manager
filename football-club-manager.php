<?php
/*
 * Plugin Name: Football Club Manager
 * Plugin URI: https://github.com/vincentbitter/football-club-manager
 * Description: Easily create a website for your football club to publish teams, players and fixtures.
 * Version: 0.10.0
 * Requires at least: 6.8
 * Requires PHP: 7.4
 * Author: Vincent Bitter
 * Author URI: https://vincentbitter.nl
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: football-club-manager
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('FCMANAGER_VERSION', '0.10.0');

// Register custom post types
require_once('includes/post-types/team.php');
require_once('includes/post-types/player.php');
require_once('includes/post-types/volunteer.php');
require_once('includes/post-types/match.php');

// Register settings
require_once('includes/class-settings.php');
require_once('includes/settings.php');

// Register administration pages
require_once('admin/page_dashboard.php');
require_once('admin/page_settings.php');

// Register administration menu
function fcmanager_register_administration_menu()
{
    add_menu_page(
        'Football Club Manager',
        'Football Club Manager',
        'edit_posts',
        'fcmanager',
        null,
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCA2NCA2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgYXJpYS1oaWRkZW49InRydWUiIHJvbGU9ImltZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+PHBhdGggZD0iTTYxLjkzNCAzMS45OTJjLjAyMS0uNzEzLjIwOS0xMC45MDQtNS44MjItMTcuNTM4Yy0uMjY4LS41OTMtMS41MzktMi45ODMtNS42NDEtNS45MDRhNDEuOTU5IDQxLjk1OSAwIDAgMC01Ljc3NS0zLjc2M2wtLjAwOC0uMDA0QzQ0LjQzMiA0LjY0NiAzOS40MyAyIDMzLjM1OSAyYy0uNDYxIDAtLjkxNy4wMjctMS4zNjguMDU4VjIuMDVjLTQuNjI5LS4xMDEtOS4yMjcgMS4wOS0xMS45OTggMi4zNDFjLTIuNDU4IDEuMTEtNS4xODcgMi45NzEtNS4zODQgMy4xMTVDMTEuMjA1IDkuNDEgNC43NSAxNy4wNTEgNC4yMzkgMjEuMWMtMi4wNjMgMi42MzctMy43ODcgMTQuNDgyLjAwNCAyMS42OTdjMi42NTggMTAuMDI3IDEyLjY2NCAxNS4wNDUgMTMuNDYgMTUuNDNjLjQ4NC4zMDkgNS45MzcgMy42OCAxMi42MzYgMy42OGMuMjgxIDAgMS45OC4wOTQgMi41ODYuMDk0YzcuMjQxIDAgMTcuOTcxLTUuMTA0IDIwLjIxNy05LjEwMmM2LjE3MS00LjUxNCA5LjM3LTE2LjE0NyA4Ljc5Mi0yMC45MDdNMTcuNzU4IDQ3LjA1NWMtMi44NjktNC42NDEtNC41MDQtMTAuNzA1LTQuODU0LTEyLjA5OGMuOTA4LTEuMzYxIDUuMzg3LTcuOTY1IDcuOTM5LTkuOTUyYzEuNDQ1LjI2NiA3LjQ3OSAxLjM3NCAxMy4xNyAyLjQwNGMuNzE1IDEuODUzIDMuODUyIDEwLjAyOSA0Ljc1IDEzLjE4NWMtLjk5IDEuMTc0LTQuODc5IDUuNzAyLTguNzA4IDkuMjQ4Yy00LjA2NS4wMTktMTAuOTc5LTIuMzI2LTEyLjI5Ny0yLjc4N001My44MjQgMTQuNThjLS4wMTIuNDUtLjExOSAyLjA1LS44ODUgMy44ODdjLTEuNTIxLS43NzctNS4zNDQtMi40NDEtMTAuNTg0LTIuNzIyYy0uNzkzLTEuMTcxLTMuNzc3LTUuMjU0LTguNDktOC4wODZjLjY0NS0xLjI2MiAxLjU0My0yLjgwMSAyLjA2OC0zLjI3Yy4xNy0uMDQ4LjQzNC0uMDkyLjgzNi0uMDkyYzIuNTI3IDAgNi44OTMgMS42NTUgNy4yNzMgMS44MDJjLjQwMy4yMTMgOC4yNTEgNC40MzkgOS43ODIgOC40ODFNMTEuNzczIDM0LjAxMmMtMy40MjMtLjU4NC01LjQ1OC0xLjY0OC02LjA2Ni0yLjAwOGMtMS4yNzMtNC42MTctLjI0OC05LjYwNy0uMDktMTAuMzIyYzEuMjU2LTIuMjQ2IDQuODMyLTcuOTcxIDcuMTkxLTkuMDU4YzIuNDQ1LS40OTkgNS40OTQuMTIxIDYuNzM2LjQyNGMtLjExNyAxLjYxNS0uMzQyIDYuMTI3LjMyNiAxMC44NjJjLTIuNzA2IDIuMTc4LTYuOTg5IDguNDQ3LTguMDk3IDEwLjEwMk0zMS42ODUgMy41M2MuNzY4LjA1NyAxLjg5NS4yMjUgMi42NjcuNDU0Yy0uNzcgMS4wMjQtMS41NTkgMi41NDItMS45MzIgMy4yOTJjLTEuNTcuMjU3LTcuNTMzIDEuMzk3LTEyLjIxMSA0LjQzYy0uOTQzLS4yNS0zLjc5MS0uOTE3LTYuNDg4LS42ODdjLjY2OC0xLjI5MyAxLjY2Ni0yLjI0OSAxLjc3My0yLjM0N2MuMzcxLS4yNjYgNy41MTMtNS4yNjMgMTYuMTkxLTUuMTU1di4wMTNtMTkuMDk2IDM4LjA5M2MtMS4xNy0uMDQ4LTUuNjc4LS4zMDUtMTAuNjIxLTEuNDY2Yy0uOTQ3LTMuMzAyLTQuMDc0LTExLjQ0NC00Ljc4OS0xMy4yOTZhNTU2LjU4NiA1NTYuNTg2IDAgMCAxIDYuOTI4LTkuNjU0YzUuNjg4LjMxMiA5LjY4MiAyLjM4NyAxMC40NTUgMi44MmMzLjI5NSA1LjI5OSA0LjAxOCAxMC43MTEgNC4xMTcgMTEuNjE1Yy0xLjc1IDUuNDQ2LTUuMjExIDkuMTEzLTYuMDkgOS45ODFNMy42NTUgMjguNTE5Yy4wODQgMS4yNjYuMjg3IDIuNTk5LjY1NCAzLjkxN2ExMS43MzggMTEuNzM4IDAgMCAwLS42ODIgMi42NTFhMzMuMDM5IDMzLjAzOSAwIDAgMSAuMDI4LTYuNTY4bTkuNjQ0IDIzLjM1OWMxLjUwOC0xLjQ1MyAzLjM2Ny0yLjg2NyA0LjA4OC0zLjQwMWMxLjYzLjU3NCA4LjMyNCAyLjgzNyAxMi41OTEgMi44MzdjLjcyNy45NzUgMy4xMDQgNC4wMjggNi4wMTggNi4zNjJjLTEuODE0IDEuNzc1LTQuNDM0IDIuNjEzLTQuODk3IDIuNzUyYy04LjEyNy4yMTgtMTYuMDQyLTQuMzUtMTcuOC04LjU1bTIxLjQ2MyA4LjUzOGMuOTIyLS41MzcgMS44ODMtMS4yNDQgMi42NzgtMi4xMzljMS4yOTctLjE3OSA2Ljg2My0xLjEzNyAxMS44OTMtNC44MzJjLjMzMi4wMzYuODc5LjA4IDEuNDkuMDYzYy0zLjAxOCAyLjk1Ny0xMC4zODIgNi4yNi0xNi4wNjEgNi45MDhtMTUuNDI0LTguMzc2YzEuODA3LTQuNzA4IDEuNzMtOC4yNTggMS42NDEtOS4zOTJjLjk5Mi0uOTcyIDQuMzk2LTQuNTk5IDYuMjg1LTEwLjExM2MxLjAxOC4xNyAxLjY4LjQyOSAxLjk5NC41NzRjLjEwOS40LjI5MSAxLjMyNC4xODggMi43MjVjLS43NyA1LjA0My0zLjQyOCAxMi42LTguMDg0IDE1Ljk0MWMtLjQ2OC4yMzktMS4yOTIuMjkxLTIuMDI0LjI2NSIgZmlsbD0iI0ZGRkZGRiI+PC9wYXRoPjwvc3ZnPg==',
        50
    );
    add_submenu_page(
        'fcmanager',
        __('Dashboard', 'football-club-manager'),
        __('Dashboard', 'football-club-manager'),
        'edit_posts',
        'fcmanager',
        'fcmanager_page_dashboard'
    );
    add_submenu_page(
        'fcmanager',
        __('Teams', 'football-club-manager'),
        __('Teams', 'football-club-manager'),
        'edit_posts',
        'edit.php?post_type=fcmanager_team',
        false
    );
    add_submenu_page(
        'fcmanager',
        __('Players', 'football-club-manager'),
        __('Players', 'football-club-manager'),
        'edit_posts',
        'edit.php?post_type=fcmanager_player',
        false
    );
    add_submenu_page(
        'fcmanager',
        __('Volunteers', 'football-club-manager'),
        __('Volunteers', 'football-club-manager'),
        'edit_posts',
        'edit.php?post_type=fcmanager_volunteer',
        false
    );
    add_submenu_page(
        'fcmanager',
        __('Matches', 'football-club-manager'),
        __('Matches', 'football-club-manager'),
        'edit_posts',
        'edit.php?post_type=fcmanager_match',
        false
    );
    add_submenu_page(
        'fcmanager',
        __('Settings', 'football-club-manager'),
        __('Settings', 'football-club-manager'),
        'manage_options',
        'fcmanager_settings',
        'fcmanager_page_settings'
    );
}

// Highlight submenu for child pages
function fcmanager_select_parent_menu($file)
{
    global $submenu_file;
    if (str_starts_with($file, 'edit.php?post_type=fcmanager_')) {
        $submenu_file = $file;
        $file = 'fcmanager';
    }
    return $file;
}

add_filter('parent_file', 'fcmanager_select_parent_menu');

// Disable quick edit button
function fcmanager_disable_quick_edit($actions)
{
    if (str_starts_with(get_post_type(), 'fcmanager_')) {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

add_filter('post_row_actions', 'fcmanager_disable_quick_edit');

// On admin init
function fcmanager_admin_init()
{
    fcmanager_settings_init();
}

add_action('admin_init', 'fcmanager_admin_init');

// On init
function fcmanager_init()
{
    fcmanager_register_team_post_type();
    fcmanager_register_player_post_type();
    fcmanager_register_volunteer_post_type();
    fcmanager_register_match_post_type();

    wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
}

add_action('init', 'fcmanager_init');


// Register block editor assets
function fcmanager_enqueue_block_editor_assets()
{
    $plugin_url = plugin_dir_url(__FILE__);
    $manifest = require __DIR__ . '/build/blocks-manifest.php';

    foreach ($manifest as $block) {
        $editor_script_handle = generate_block_asset_handle($block['name'], 'editorScript');

        wp_localize_script($editor_script_handle, 'FootballClubManager', [
            'pluginUrl' => $plugin_url,
        ]);
        wp_set_script_translations($editor_script_handle, 'football-club-manager', plugin_dir_path(__FILE__) . get_plugin_data(__FILE__)['DomainPath']);
    }
}
add_action('enqueue_block_editor_assets', 'fcmanager_enqueue_block_editor_assets');

// On admin menu
function fcmanager_admin_menu()
{
    fcmanager_register_administration_menu();
}

add_action('admin_menu', 'fcmanager_admin_menu', 10);

// On activation
function fcmanager_activated()
{
    fcmanager_init();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'fcmanager_activated');

// On deactivation
function fcmanager_deactivated()
{
    // Unregister post types before refreshing rewrite rules
    fcmanager_unregister_team_post_type();
    fcmanager_unregister_player_post_type();
    fcmanager_unregister_volunteer_post_type();
    fcmanager_unregister_match_post_type();

    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'fcmanager_deactivated');

// Load blocks
function fcmanager_load_all_blocks()
{
    $blocks_dir = __DIR__ . '/build';
    $block_folders = glob($blocks_dir . '/*', GLOB_ONLYDIR);

    foreach ($block_folders as $block_path) {
        $render_file = $block_path . '/render.php';

        if (file_exists($render_file)) {
            require_once $render_file;
        }
    }
}
fcmanager_load_all_blocks();

// Add customerizer settings
function fcmanager_customize_register($wp_customize)
{
    $wp_customize->add_panel('fcmanager_panel', [
        'title'       => __('Football Club Manager', 'football-club-manager'),
        'capability'     => 'edit_theme_options',
        'priority'    => 30,
    ]);

    fcmanager_customize_register_team_page($wp_customize);
}

add_action('customize_register', 'fcmanager_customize_register');
