<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_results_block($attributes, $content)
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
            'date_max' => [
                'key' => '_fcmanager_match_date',
                'value' => wp_date('Y-m-d'),
                'compare' => '<=',
                'type' => 'DATE',
            ],
            'date_min' => [
                'key' => '_fcmanager_match_date',
                'value' => wp_date('Y-m-d', strtotime("-{$numberOfDays} days")),
                'compare' => '>',
                'type' => 'DATE',
            ],
            'goals_for_exists' => [
                'key'     => '_fcmanager_match_goals_for',
                'compare' => 'EXISTS',
            ],
            'goals_for_not_empty' => [
                'key'     => '_fcmanager_match_goals_for',
                'value'   => '',
                'compare' => '!=',
                'type'    => 'NUMERIC',
            ],
            'match_starttime' => [
                'key'  => '_fcmanager_match_starttime',
                'type' => 'TIME',
            ],
        ],
        'orderby' => [
            'date_max'   => 'DESC',
            'match_starttime' => 'DESC',
        ],
        'numberposts' => $numberOfItems
    ]);

    ob_start();
?>
    <div class="fcmanager-results">
        <h2><?php esc_html_e("Results", "football-club-manager") ?></h2>
        <?php if ($matches): ?>
            <table class="fcmanager-matches fcmanager-matches-results">
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <?php
                        $away = get_post_meta($match->ID, '_fcmanager_match_away', true);
                        $opponent = get_post_meta($match->ID, '_fcmanager_match_opponent', true);
                        $goals_for = get_post_meta($match->ID, '_fcmanager_match_goals_for', true);
                        $goals_against = get_post_meta($match->ID, '_fcmanager_match_goals_against', true);
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
                            <td class="fcmanager-match-homescore">
                                <?php
                                echo $away ? esc_html($goals_against) : esc_html($goals_for);
                                ?>
                            </td>
                            <td class="fcmanager-match-separator">-</td>
                            <td class="fcmanager-match-awayscore">
                                <?php
                                echo $away ? esc_html($goals_for) : esc_html($goals_against);
                                ?>
                            </td>
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
    'render_callback' => 'fcmanager_render_results_block',
]);
