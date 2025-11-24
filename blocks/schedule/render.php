<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_schedule_block($attributes, $content)
{
    $teams = fcmanager_get_teams();

    $numberOfItems = isset($attributes['numberOfItems']) && $attributes['numberOfItems'] > 0
        ? intval($attributes['numberOfItems'])
        : 20;

    $numberOfDays = isset($attributes['numberOfDays']) && $attributes['numberOfDays'] > 0
        ? intval($attributes['numberOfDays'])
        : 14;

    $matches = get_posts([
        'post_type' => 'fcmanager_match',
        'meta_query' => [
            'date_min' => [
                'key' => '_fcmanager_match_date',
                'value' => wp_date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ],
            'date_max' => [
                'key' => '_fcmanager_match_date',
                'value' => wp_date('Y-m-d', strtotime("+{$numberOfDays} days")),
                'compare' => '<',
                'type' => 'DATE',
            ],
            'goals_for_not_exists' => [
                'key'     => '_fcmanager_match_goals_for',
                'compare' => 'NOT EXISTS',
            ],
            'match_starttime' => [
                'key'  => '_fcmanager_match_starttime',
                'type' => 'TIME',
            ],
        ],
        'orderby' => [
            'date_min'   => 'ASC',
            'match_starttime' => 'ASC',
        ],
        'numberposts' => $numberOfItems
    ]);

    ob_start();
?>
    <div class="fcmanager-schedule">
        <h2><?php esc_html_e("Upcoming matches", "football-club-manager") ?></h2>
        <?php if ($matches): ?>
            <table class="fcmanager-matches fcmanager-matches-schedule">
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <?php
                        $away = get_post_meta($match->ID, '_fcmanager_match_away', true);
                        $opponent = get_post_meta($match->ID, '_fcmanager_match_opponent', true);
                        $team_obj = array_find($teams, function ($team) use ($match) {
                            return $team->ID == get_post_meta($match->ID, '_fcmanager_match_team', true);
                        });
                        if (!$team_obj) continue;
                        $team_name = $team_obj->post_title;
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
                                echo $away ? esc_html($opponent) : esc_html($team_name);
                                ?>
                            </td>
                            <td class="fcmanager-match-separator">-</td>
                            <td class="fcmanager-match-awayteam">
                                <?php
                                echo $away ? esc_html($team_name) : esc_html($opponent);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><?php esc_html_e('No matches found.', 'football-club-manager'); ?></p>
        <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_schedule_block',
]);
