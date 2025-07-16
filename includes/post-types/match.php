<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type: Match
if (! function_exists('fcm_register_match_post_type')) {
    function fcm_register_match_post_type()
    {
        register_post_type(
            'fcm_match',
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
                'supports'      => array(''),
            )
        );
    }
}

// Unregister Custom Post Type: Match
if (! function_exists('fcm_unregister_match_post_type')) {
    function fcm_unregister_match_post_type()
    {
        unregister_post_type('fcm_match');
    }
}

// Add custom meta boxes to match
if (! function_exists('fcm_add_match_meta_boxes')) {
    function fcm_add_match_meta_boxes()
    {
        add_meta_box(
            'fcm_match_meta_box',
            __('Match information', 'football-club-manager'),
            'fcm_render_match_meta_box',
            'fcm_match',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'fcm_add_match_meta_boxes');
}

// Render match meta box
if (! function_exists('fcm_render_match_meta_box')) {
    function fcm_render_match_meta_box($post)
    {
        // Retreive current meta values
        $date = get_post_meta($post->ID, '_fcm_match_date', true);
        $starttime = get_post_meta($post->ID, '_fcm_match_starttime', true);
        $endtime = get_post_meta($post->ID, '_fcm_match_endtime', true);
        $team = get_post_meta($post->ID, '_fcm_match_team', true);
        $opponent = get_post_meta($post->ID, '_fcm_match_opponent', true);
        $away = intval(get_post_meta($post->ID, '_fcm_match_away', true));
        $goals_for = get_post_meta($post->ID, '_fcm_match_goals_for', true);
        $goals_against = get_post_meta($post->ID, '_fcm_match_goals_against', true);
        $goals_for_final = get_post_meta($post->ID, '_fcm_match_goals_for_final', true);
        $goals_against_final = get_post_meta($post->ID, '_fcm_match_goals_against_final', true);

        if ($away !== 0 && $away !== 1)
            $away = 0;

        $team_options = fcm_get_teams();

        // Show form
        wp_nonce_field('fcm_save_match_meta_box', 'fcm_match_meta_box_nonce');
?>
        <table role="presentation">
            <thead>
                <tr>
                    <th style="width: 150px;"><label for="fcm_match_date"><?php esc_attr_e('Date', 'football-club-manager'); ?></label></th>
                    <th style="width: 150px;"><label for="fcm_match_starttime"><?php esc_attr_e('From', 'football-club-manager'); ?></label></th>
                    <th style="width: 150px;"><label for="fcm_match_endtime"><?php esc_attr_e('Till', 'football-club-manager'); ?></label></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input style="width: 100%;" type="date" id="fcm_match_date" name="fcm_match_date" value="<?php echo esc_attr($date); ?>"></td>
                    <td><input style="width: 100%;" type="time" id="fcm_match_starttime" name="fcm_match_starttime" value="<?php echo esc_attr($starttime); ?>"></td>
                    <td><input style="width: 100%;" type="time" id="fcm_match_endtime" name="fcm_match_endtime" value="<?php echo esc_attr($endtime); ?>"></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 464px; margin: 20px 0;" role="presentation">
            <tbody>
                <tr>
                    <td style="text-align: right;">
                        <label for="fcm_match_home">
                            <input type="radio" id="fcm_match_home" name="fcm_match_away" value="0" <?php checked($away, 0); ?>>
                            <?php esc_html_e('Home game', 'football-club-manager'); ?>
                        </label>
                    </td>
                    <td style="width: 16px"></td>
                    <td>
                        <label for="fcm_match_away">
                            <input type="radio" id="fcm_match_away" name="fcm_match_away" value="1" <?php checked($away, 1); ?>>
                            <?php esc_html_e('Away game', 'football-club-manager'); ?>
                        </label>
                    </td>

            </tbody>
        </table>
        <table role="presentation">
            <thead>
                <tr>
                    <th style="width: 150px; text-align: right;"><label for="fcm_match_team"><?php esc_html_e('Team', 'football-club-manager'); ?></label></th>
                    <th style="width: 63px; text-align: right;"><label for="fcm_match_goals_for"><?php esc_html_e('Goals', 'football-club-manager'); ?></label></th>
                    <th style="width: 16px;"></th>
                    <th style="width: 63px; text-align: left;"><label for="fcm_match_goals_against"><?php esc_html_e('Goals', 'football-club-manager'); ?></label></th>
                    <th style="width: 150px; text-align: left;"><label for="fcm_match_opponent"><?php esc_html_e('Opponent', 'football-club-manager'); ?></label></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: right">
                        <select style="width: 100%" id="fcm_match_team" name="fcm_match_team">
                            <?php foreach ($team_options as $team_option) : ?>
                                <option value="<?php echo esc_attr($team_option->ID); ?>" <?php selected($team, $team_option->ID); ?>><?php echo esc_html($team_option->post_title); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="text-align: right;"><input style="width: 100%" type="number" id="fcm_match_goals_for" name="fcm_match_goals_for" value="<?php echo esc_attr($goals_for); ?>"></td>
                    <td style="text-align: center;">-</td>
                    <td><input style="width: 100%" type="number" id="fcm_match_goals_against" name="fcm_match_goals_against" value="<?php echo esc_attr($goals_against); ?>"></td>
                    <td><input style="width: 100%" type="text" id="fcm_match_opponent" name="fcm_match_opponent" value="<?php echo esc_attr($opponent); ?>"></td>
                </tr>
                <tr>
                    <td style="text-align: right;"><?php esc_html_e('Final score', 'football-club-manager'); ?></td>
                    <td style="text-align: right;"><input style="width: 100%" type="number" id="fcm_match_goals_for_final" name="fcm_match_goals_for_final" value="<?php echo esc_attr($goals_for_final); ?>"></td>
                    <td style="text-align: center;">-</td>
                    <td><input style="width: 100%;" type="number" id="fcm_match_goals_against_final" name="fcm_match_goals_against_final" value="<?php echo esc_attr($goals_against_final); ?>"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
<?php
    }
}

// Save match meta box
if (! function_exists('fcm_save_match_meta_box')) {
    function fcm_save_match_meta_box($post_id)
    {
        // Check post type
        if (get_post_type($post_id) !== 'fcm_match')
            return;

        // Check nonce
        if (! check_admin_referer('fcm_save_match_meta_box', 'fcm_match_meta_box_nonce'))
            return;

        // Check permissions
        if (! current_user_can('edit_post', $post_id))
            return;

        // Save meta values
        if (array_key_exists('fcm_match_date', $_POST))
            update_post_meta($post_id, '_fcm_match_date', sanitize_text_field(wp_unslash($_POST['fcm_match_date'])));
        if (array_key_exists('fcm_match_starttime', $_POST))
            update_post_meta($post_id, '_fcm_match_starttime', sanitize_text_field(wp_unslash($_POST['fcm_match_starttime'])));
        if (array_key_exists('fcm_match_endtime', $_POST))
            update_post_meta($post_id, '_fcm_match_endtime', sanitize_text_field(wp_unslash($_POST['fcm_match_endtime'])));
        if (array_key_exists('fcm_match_team', $_POST))
            update_post_meta($post_id, '_fcm_match_team', sanitize_text_field(wp_unslash($_POST['fcm_match_team'])));
        if (array_key_exists('fcm_match_opponent', $_POST))
            update_post_meta($post_id, '_fcm_match_opponent', sanitize_text_field(wp_unslash($_POST['fcm_match_opponent'])));
        if (array_key_exists('fcm_match_away', $_POST))
            update_post_meta($post_id, '_fcm_match_away', intval(wp_unslash($_POST['fcm_match_away'])));
        if (array_key_exists('fcm_match_goals_for', $_POST))
            update_post_meta($post_id, '_fcm_match_goals_for', sanitize_text_field(wp_unslash($_POST['fcm_match_goals_for'])));
        if (array_key_exists('fcm_match_goals_against', $_POST))
            update_post_meta($post_id, '_fcm_match_goals_against', sanitize_text_field(wp_unslash($_POST['fcm_match_goals_against'])));
        if (array_key_exists('fcm_match_goals_for_final', $_POST))
            update_post_meta($post_id, '_fcm_match_goals_for_final', sanitize_text_field(wp_unslash($_POST['fcm_match_goals_for_final'])));
        if (array_key_exists('fcm_match_goals_against_final', $_POST))
            update_post_meta($post_id, '_fcm_match_goals_against_final', sanitize_text_field(wp_unslash($_POST['fcm_match_goals_against_final'])));

        // Update post with new title
        $home_team = get_home_team_name($post_id);
        $away_team = get_away_team_name($post_id);

        $datetime = get_post_meta($post_id, '_fcm_match_date', true) . ' ' . get_post_meta($post_id, '_fcm_match_starttime', true);
        $title = $datetime . ' ' . $home_team . ' - ' . $away_team;
        $slug = sanitize_title($title);
        $post_update = array(
            'ID'         => $post_id,
            'post_title' => $title,
            'post_name' => $slug
        );
        remove_action('save_post_fcm_match', 'fcm_save_match_meta_box');
        wp_update_post($post_update);
    }
    add_action('save_post_fcm_match', 'fcm_save_match_meta_box');
}

if (! function_exists('get_home_team_name')) {
    function get_home_team_name($post_id)
    {
        if (!get_post_meta($post_id, '_fcm_match_away', true)) {
            $team = fcm_get_team(get_post_meta($post_id, '_fcm_match_team', true));
            return $team != null ? $team->post_title : '';
        }
        return get_post_meta($post_id, '_fcm_match_opponent', true);
    }
}

if (! function_exists('get_away_team_name')) {
    function get_away_team_name($post_id)
    {
        if (get_post_meta($post_id, '_fcm_match_away', true)) {
            $team = fcm_get_team(get_post_meta($post_id, '_fcm_match_team', true));
            return $team != null ? $team->post_title : '';
        }
        return get_post_meta($post_id, '_fcm_match_opponent', true);
    }
}

if (! function_exists('set_custom_edit_match_columns')) {
    // Add the custom columns to the match post type:
    function set_custom_edit_match_columns($columns)
    {
        unset($columns['title']);
        unset($columns['date']);
        $columns['match_datetime'] = __('Date/time', 'football-club-manager');
        $columns['match_home_team'] = __('Home team', 'football-club-manager');
        $columns['match_away_team'] = __('Away team', 'football-club-manager');
        $columns['match_result'] = __('Result', 'football-club-manager');

        return $columns;
    }
    add_filter('manage_fcm_match_posts_columns', 'set_custom_edit_match_columns');
}

if (! function_exists('custom_match_column')) {
    // Add the data to the custom columns for the match post type:
    function custom_match_column($column, $post_id)
    {
        switch ($column) {

            case 'match_datetime':
                echo esc_html(get_post_meta($post_id, '_fcm_match_date', true) . ' ' . get_post_meta($post_id, '_fcm_match_starttime', true));
                break;

            case 'match_home_team':
                echo esc_html(get_home_team_name($post_id));
                break;

            case 'match_away_team':
                echo esc_html(get_away_team_name($post_id));
                break;

            case 'match_result':
                $goals_for = get_post_meta($post_id, '_fcm_match_goals_for', true);
                $goals_against = get_post_meta($post_id, '_fcm_match_goals_against', true);
                $away = get_post_meta($post_id, '_fcm_match_away', true);

                if ($away)
                    echo esc_html($goals_against . ' - ' . $goals_for);
                else
                    echo esc_html($goals_for . ' - ' . $goals_against);

                $goals_against_final = get_post_meta($post_id, '_fcm_match_goals_against_final', true);
                $goals_for_final = get_post_meta($post_id, '_fcm_match_goals_for_final', true);

                if ($goals_against_final > 0 || $goals_for_final > 0) {
                    if ($goals_against_final > $goals_against || $goals_for_final > $goals_for) {
                        echo esc_html(' (' . $goals_for_final . ' - ' . $goals_against_final . ')');
                    }
                }
                break;
        }
    }
    add_action('manage_fcm_match_posts_custom_column', 'custom_match_column', 10, 2);
}
