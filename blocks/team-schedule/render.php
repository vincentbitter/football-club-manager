<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_team_schedule_block($attributes, $content)
{
    $referees = fcmanager_get_referees();

    $team_id = isset($attributes['teamId']) && $attributes['teamId'] > 0
        ? intval($attributes['teamId'])
        : get_the_ID();

    $team_post = get_post($team_id);

    if ($team_post && $team_post->post_type === 'fcmanager_team') {
        $matches = get_posts([
            'post_type' => 'fcmanager_match',
            'meta_query' => [
                'team' => [
                    'key'     => '_fcmanager_match_team',
                    'value'   => $team_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ],
                'match_date' => [
                    'key'     => '_fcmanager_match_date',
                    'value'   => wp_date('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
                'goals_for_missing' => [
                    'key'     => '_fcmanager_match_goals_for',
                    'compare' => 'NOT EXISTS',
                ],
                'match_starttime' => [
                    'key'  => '_fcmanager_match_starttime',
                    'type' => 'TIME',
                ],
            ],
            'orderby' => [
                'match_date' => 'ASC',
                'match_starttime' => 'ASC',
            ],
            'posts_per_page' => 20,
        ]);

        ob_start();
?>
        <div class="fcmanager-team-schedule">
            <h2><?php esc_html_e("Upcoming matches", "football-club-manager") ?></h2>
            <?php if ($matches): ?>
                <table class="fcmanager-matches fcmanager-matches-schedule">
                    <thead>
                        <tr>
                            <th colspan="2"><?php esc_html_e("Date/time", "football-club-manager") ?></th>
                            <th colspan="3"><?php esc_html_e("Match", "football-club-manager") ?></th>
                            <th><?php esc_html_e("Referee", "football-club-manager") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <?php
                            $away = get_post_meta($match->ID, '_fcmanager_match_away', true);
                            $opponent = get_post_meta($match->ID, '_fcmanager_match_opponent', true);

                            $referee_obj = array_find($referees, function ($referee) use ($match) {
                                return $referee->ID == get_post_meta($match->ID, '_fcmanager_match_referee', true);
                            });
                            $referee_name = $referee_obj ? $referee_obj->post_title : '';
                            ?>
                            <tr>
                                <td class="fcmanager-match-date">
                                    <?php echo esc_html(get_post_meta($match->ID, '_fcmanager_match_date', true)); ?>
                                </td>
                                <td class="fcmanager-match-time">
                                    <?php echo esc_html(get_post_meta($match->ID, '_fcmanager_match_starttime', true)); ?>
                                </td>
                                <td class="fcmanager-match-hometeam" title="<?php echo $away ? esc_attr($opponent) : esc_attr($team_post->post_title); ?>">
                                    <?php
                                    echo $away ? esc_html($opponent) : esc_html($team_post->post_title);
                                    ?>
                                </td>
                                <td class="fcmanager-match-separator">-</td>
                                <td class="fcmanager-match-awayteam" title="<?php echo $away ? esc_attr($team_post->post_title) : esc_attr($opponent); ?>">
                                    <?php
                                    echo $away ? esc_html($team_post->post_title) : esc_html($opponent);
                                    ?>
                                </td>
                                <td class="fcmanager-match-referee">
                                    <?php echo esc_html($referee_name); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p><?php esc_html_e('No matches found for this team.', 'football-club-manager'); ?></p>
            <?php endif; ?>
        </div>
<?php
        return ob_get_clean();
    }
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_team_schedule_block',
]);
