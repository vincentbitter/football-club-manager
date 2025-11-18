<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_team_schedule_block($attributes, $content)
{
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
                <table class="fcmanager-matches">
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <?php
                            $away = get_post_meta($match->ID, '_fcmanager_match_away', true);
                            $opponent = get_post_meta($match->ID, '_fcmanager_match_opponent', true);
                            ?>
                            <tr>
                                <td class="fcmanager-match-date">
                                    <?php echo esc_html(get_post_meta($match->ID, '_fcmanager_match_date', true)); ?>
                                </td>
                                <td class="fcmanager-match-time">
                                    <?php echo esc_html(get_post_meta($match->ID, '_fcmanager_match_starttime', true)); ?>
                                </td>
                                <td class="fcmanager-match-hometeam">
                                    <?php
                                    echo $away ? esc_html($opponent) : esc_html($team_post->post_title);
                                    ?>
                                </td>
                                <td class="fcmanager-match-separator">-</td>
                                <td class="fcmanager-match-awayteam">
                                    <?php
                                    echo $away ? esc_html($team_post->post_title) : esc_html($opponent);
                                    ?>
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
