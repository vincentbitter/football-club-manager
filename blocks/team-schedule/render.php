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
        $matches = fcmanager_get_upcoming_matches($team_id, 20);

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

            <?php if ($attributes['showSubscribeButtons'] !== false): ?>
                <div class="fcmanager-calendar-buttons">
                    <h3><?php esc_html_e("Add to your calendar", "football-club-manager") ?></h3>
                    <a class="wp-block-button__link" href="<?php echo esc_url(home_url("teams/" . $team_post->post_name . "/matches.ics")); ?>">
                        iPhone / iPad / Mac
                    </a>
                    <a class="wp-block-button__link" href="https://calendar.google.com/calendar/u/0/r?cid=<?php echo esc_url(home_url("teams/" . $team_post->post_name . "/matches.ics")); ?>">
                        Google Calendar (Android)
                    </a>
                    <a class="wp-block-button__link" href="<?php echo esc_url(str_replace('https://', 'webcal://', home_url("teams/" . $team_post->post_name . "/matches.ics"))); ?>">
                        Windows / Outlook Desktop
                    </a>
                    <a class="wp-block-button__link" href="https://outlook.live.com/calendar/0/addfromweb?url=<?php echo esc_url(home_url("teams/" . $team_post->post_name . "/matches.ics")); ?>">
                        Outlook.com
                    </a>
                </div>
            <?php endif; ?>
        </div>
<?php
        return ob_get_clean();
    }
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_team_schedule_block',
]);
