<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type: Match
function fcmanager_register_match_post_type()
{
    register_post_type(
        'fcmanager_match',
        array(
            'labels'        => array(
                'name'          => __('Matches', 'football-club-manager'),
                'singular_name' => __('Match', 'football-club-manager'),
                'add_new_item'     => __('New match', 'football-club-manager'),
                'edit_item' => __('Edit match', 'football-club-manager'),
                'not_found' => __('No matches found', 'football-club-manager'),
                'not_found_in_trash' => __('No matches found in the trash', 'football-club-manager'),
                'search_items' => __('Search match', 'football-club-manager'),
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
        'fcmanager_match',
        'meta',
        array(
            'get_callback' => function ($post) {
                return get_post_meta($post['id']);
            },
            'schema' => null,
        )
    );
});

// Unregister Custom Post Type: Match
function fcmanager_unregister_match_post_type()
{
    unregister_post_type('fcmanager_match');
}

// Add custom meta boxes to match
function fcmanager_add_match_meta_boxes()
{
    add_meta_box(
        'fcmanager_match_meta_box',
        __('Match information', 'football-club-manager'),
        'fcmanager_render_match_meta_box',
        'fcmanager_match',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fcmanager_add_match_meta_boxes');

// Render match meta box
function fcmanager_render_match_meta_box($post)
{
    // Retreive current meta values
    $date = get_post_meta($post->ID, '_fcmanager_match_date', true);
    $starttime = get_post_meta($post->ID, '_fcmanager_match_starttime', true);
    $endtime = get_post_meta($post->ID, '_fcmanager_match_endtime', true);
    $team = get_post_meta($post->ID, '_fcmanager_match_team', true);
    $opponent = get_post_meta($post->ID, '_fcmanager_match_opponent', true);
    $away = intval(get_post_meta($post->ID, '_fcmanager_match_away', true));
    $goals_for = get_post_meta($post->ID, '_fcmanager_match_goals_for', true);
    $goals_against = get_post_meta($post->ID, '_fcmanager_match_goals_against', true);
    $goals_for_final = get_post_meta($post->ID, '_fcmanager_match_goals_for_final', true);
    $goals_against_final = get_post_meta($post->ID, '_fcmanager_match_goals_against_final', true);

    if ($away !== 0 && $away !== 1)
        $away = 0;

    $team_options = fcmanager_get_teams();

    // Show form
    wp_nonce_field('fcmanager_save_match_meta_box', 'fcmanager_match_meta_box_nonce');
?>
    <table role="presentation">
        <thead>
            <tr>
                <th style="width: 150px;"><label for="fcmanager_match_date"><?php esc_attr_e('Date', 'football-club-manager'); ?></label></th>
                <th style="width: 150px;"><label for="fcmanager_match_starttime"><?php esc_attr_e('From', 'football-club-manager'); ?></label></th>
                <th style="width: 150px;"><label for="fcmanager_match_endtime"><?php esc_attr_e('Till', 'football-club-manager'); ?></label></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input style="width: 100%;" type="date" id="fcmanager_match_date" name="fcmanager_match_date" value="<?php echo esc_attr($date); ?>"></td>
                <td><input style="width: 100%;" type="time" id="fcmanager_match_starttime" name="fcmanager_match_starttime" value="<?php echo esc_attr($starttime); ?>"></td>
                <td><input style="width: 100%;" type="time" id="fcmanager_match_endtime" name="fcmanager_match_endtime" value="<?php echo esc_attr($endtime); ?>"></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 464px; margin: 20px 0;" role="presentation">
        <tbody>
            <tr>
                <td style="text-align: right;">
                    <label for="fcmanager_match_home">
                        <input type="radio" id="fcmanager_match_home" name="fcmanager_match_away" value="0" <?php checked($away, 0); ?>>
                        <?php esc_html_e('Home game', 'football-club-manager'); ?>
                    </label>
                </td>
                <td style="width: 16px"></td>
                <td>
                    <label for="fcmanager_match_away">
                        <input type="radio" id="fcmanager_match_away" name="fcmanager_match_away" value="1" <?php checked($away, 1); ?>>
                        <?php esc_html_e('Away game', 'football-club-manager'); ?>
                    </label>
                </td>

        </tbody>
    </table>
    <table role="presentation">
        <thead>
            <tr>
                <th style="width: 150px; text-align: right;"><label for="fcmanager_match_team"><?php esc_html_e('Team', 'football-club-manager'); ?></label></th>
                <th style="width: 63px; text-align: right;"><label for="fcmanager_match_goals_for"><?php esc_html_e('Goals', 'football-club-manager'); ?></label></th>
                <th style="width: 16px;"></th>
                <th style="width: 63px; text-align: left;"><label for="fcmanager_match_goals_against"><?php esc_html_e('Goals', 'football-club-manager'); ?></label></th>
                <th style="width: 150px; text-align: left;"><label for="fcmanager_match_opponent"><?php esc_html_e('Opponent', 'football-club-manager'); ?></label></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: right">
                    <select style="width: 100%" id="fcmanager_match_team" name="fcmanager_match_team">
                        <?php foreach ($team_options as $team_option) : ?>
                            <option value="<?php echo esc_attr($team_option->ID); ?>" <?php selected($team, $team_option->ID); ?>><?php echo esc_html($team_option->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="text-align: right;"><input style="width: 100%" type="number" id="fcmanager_match_goals_for" name="fcmanager_match_goals_for" value="<?php echo esc_attr($goals_for); ?>"></td>
                <td style="text-align: center;">-</td>
                <td><input style="width: 100%" type="number" id="fcmanager_match_goals_against" name="fcmanager_match_goals_against" value="<?php echo esc_attr($goals_against); ?>"></td>
                <td><input style="width: 100%" type="text" id="fcmanager_match_opponent" name="fcmanager_match_opponent" value="<?php echo esc_attr($opponent); ?>"></td>
            </tr>
            <tr>
                <td style="text-align: right;"><?php esc_html_e('Final score', 'football-club-manager'); ?></td>
                <td style="text-align: right;"><input style="width: 100%" type="number" id="fcmanager_match_goals_for_final" name="fcmanager_match_goals_for_final" value="<?php echo esc_attr($goals_for_final); ?>"></td>
                <td style="text-align: center;">-</td>
                <td><input style="width: 100%;" type="number" id="fcmanager_match_goals_against_final" name="fcmanager_match_goals_against_final" value="<?php echo esc_attr($goals_against_final); ?>"></td>
                <td></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save match meta box
function fcmanager_save_match_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_match')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_match_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_match_meta_box', 'fcmanager_match_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Save meta values
    if (array_key_exists('fcmanager_match_date', $_POST))
        update_post_meta($post_id, '_fcmanager_match_date', sanitize_text_field(wp_unslash($_POST['fcmanager_match_date'])));
    if (array_key_exists('fcmanager_match_starttime', $_POST))
        update_post_meta($post_id, '_fcmanager_match_starttime', sanitize_text_field(wp_unslash($_POST['fcmanager_match_starttime'])));
    if (array_key_exists('fcmanager_match_endtime', $_POST))
        update_post_meta($post_id, '_fcmanager_match_endtime', sanitize_text_field(wp_unslash($_POST['fcmanager_match_endtime'])));
    if (array_key_exists('fcmanager_match_team', $_POST))
        update_post_meta($post_id, '_fcmanager_match_team', sanitize_text_field(wp_unslash($_POST['fcmanager_match_team'])));
    if (array_key_exists('fcmanager_match_opponent', $_POST))
        update_post_meta($post_id, '_fcmanager_match_opponent', sanitize_text_field(wp_unslash($_POST['fcmanager_match_opponent'])));
    if (array_key_exists('fcmanager_match_away', $_POST))
        update_post_meta($post_id, '_fcmanager_match_away', intval(wp_unslash($_POST['fcmanager_match_away'])));
    if (array_key_exists('fcmanager_match_goals_for', $_POST))
        update_post_meta($post_id, '_fcmanager_match_goals_for', sanitize_text_field(wp_unslash($_POST['fcmanager_match_goals_for'])));
    if (array_key_exists('fcmanager_match_goals_against', $_POST))
        update_post_meta($post_id, '_fcmanager_match_goals_against', sanitize_text_field(wp_unslash($_POST['fcmanager_match_goals_against'])));
    if (array_key_exists('fcmanager_match_goals_for_final', $_POST))
        update_post_meta($post_id, '_fcmanager_match_goals_for_final', sanitize_text_field(wp_unslash($_POST['fcmanager_match_goals_for_final'])));
    if (array_key_exists('fcmanager_match_goals_against_final', $_POST))
        update_post_meta($post_id, '_fcmanager_match_goals_against_final', sanitize_text_field(wp_unslash($_POST['fcmanager_match_goals_against_final'])));

    // Update post with new title
    $home_team = fcmanager_get_home_team_name($post_id);
    $away_team = fcmanager_get_away_team_name($post_id);

    $datetime = get_post_meta($post_id, '_fcmanager_match_date', true) . ' ' . get_post_meta($post_id, '_fcmanager_match_starttime', true);
    $title = $datetime . ' ' . $home_team . ' - ' . $away_team;
    $slug = sanitize_title($title);
    $post_update = array(
        'ID'         => $post_id,
        'post_title' => $title,
        'post_name' => $slug
    );
    remove_action('save_post_fcmanager_match', 'fcmanager_save_match_meta_box');
    wp_update_post($post_update);
}

add_action('save_post_fcmanager_match', 'fcmanager_save_match_meta_box');


function fcmanager_get_home_team_name($post_id)
{
    if (!get_post_meta($post_id, '_fcmanager_match_away', true)) {
        $team = fcmanager_get_team(get_post_meta($post_id, '_fcmanager_match_team', true));
        return $team != null ? $team->post_title : '';
    }
    return get_post_meta($post_id, '_fcmanager_match_opponent', true);
}


function fcmanager_get_away_team_name($post_id)
{
    if (get_post_meta($post_id, '_fcmanager_match_away', true)) {
        $team = fcmanager_get_team(get_post_meta($post_id, '_fcmanager_match_team', true));
        return $team != null ? $team->post_title : '';
    }
    return get_post_meta($post_id, '_fcmanager_match_opponent', true);
}


// Add the custom columns to the match post type:
function fcmanager_set_custom_edit_match_columns($columns)
{
    unset($columns['title']);
    unset($columns['date']);
    $columns['match_datetime'] = __('Date/time', 'football-club-manager');
    $columns['match_home_team'] = __('Home team', 'football-club-manager');
    $columns['match_away_team'] = __('Away team', 'football-club-manager');
    $columns['match_result'] = __('Result', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_match_posts_columns', 'fcmanager_set_custom_edit_match_columns');


// Add the data to the custom columns for the match post type:
function fcmanager_custom_match_column($column, $post_id)
{
    switch ($column) {

        case 'match_datetime':
            echo esc_html(get_post_meta($post_id, '_fcmanager_match_date', true) . ' ' . get_post_meta($post_id, '_fcmanager_match_starttime', true));
            break;

        case 'match_home_team':
            echo esc_html(fcmanager_get_home_team_name($post_id));
            break;

        case 'match_away_team':
            echo esc_html(fcmanager_get_away_team_name($post_id));
            break;

        case 'match_result':
            $goals_for = get_post_meta($post_id, '_fcmanager_match_goals_for', true);
            $goals_against = get_post_meta($post_id, '_fcmanager_match_goals_against', true);
            $away = get_post_meta($post_id, '_fcmanager_match_away', true);

            if ($away)
                echo esc_html($goals_against . ' - ' . $goals_for);
            else
                echo esc_html($goals_for . ' - ' . $goals_against);

            $goals_against_final = get_post_meta($post_id, '_fcmanager_match_goals_against_final', true);
            $goals_for_final = get_post_meta($post_id, '_fcmanager_match_goals_for_final', true);

            if ($goals_against_final > 0 || $goals_for_final > 0) {
                if ($goals_against_final > $goals_against || $goals_for_final > $goals_for) {
                    echo esc_html(' (' . $goals_for_final . ' - ' . $goals_against_final . ')');
                }
            }
            break;
    }
}

add_action('manage_fcmanager_match_posts_custom_column', 'fcmanager_custom_match_column', 10, 2);

// Allow filtering matches via REST API
add_filter('rest_fcmanager_match_query', function ($args, $request) {
    $compare = $request->get_param('meta_compare');
    if (!in_array($compare, array('=', '!=', '>', '>=', '<', '<=')))
        $compare = '=';

    $type = $request->get_param('meta_type');
    if (!in_array($type, array('NUMERIC', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED')))
        $type = 'NUMERIC';

    $args['meta_query'] = array();
    if ($meta_key = $request->get_param('meta_key')) {
        $args['meta_query'][] = array(
            'key' => $meta_key,
            'value' => $request->get_param('meta_value'),
            'compare' => $compare,
            'type' => $type,
        );
    }

    if ($request->get_param('upcoming') === 'true') {
        $args['meta_query'][] = array(
            'key' => '_fcmanager_match_date',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE',
        );
        $args['meta_query'][] = array(
            'key' => '_fcmanager_match_goals_for',
            'compare' => 'NOT EXISTS',
        );
    } else if ($request->get_param('results') === 'true') {
        $args['meta_query'][] = array(
            'key' => '_fcmanager_match_date',
            'value' => date('Y-m-d'),
            'compare' => '<=',
            'type' => 'DATE',
        );
        $args['meta_query'][] = array(
            'key' => '_fcmanager_match_goals_for',
            'compare' => 'EXISTS',
        );
        $args['meta_query'][] = array(
            'key' => '_fcmanager_match_goals_for',
            'compare' => '!=',
            'value' => '',
            'type' => 'NUMERIC',
        );
    }
    return $args;
}, 10, 2);

// Order matches by date in REST API
add_filter('rest_fcmanager_match_query', function ($args, $request) {
    $args['meta_query']['match_date'] = array(
        'key'   => '_fcmanager_match_date',
        'type'  => 'DATE',
    );
    $args['meta_query']['match_starttime'] = array(
        'key'   => '_fcmanager_match_starttime',
        'type'  => 'TIME',
    );

    $args['orderby'] = array(
        'match_date' => 'ASC',
        'match_starttime' => 'ASC',
    );

    if ($request->get_param('results') === 'true') {
        $args['orderby'] = array(
            'match_date' => 'DESC',
            'match_starttime' => 'DESC',
        );
    }

    return $args;
}, 10, 2);
