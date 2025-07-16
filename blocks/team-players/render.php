<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('fcmanager_render_team_players_block')) {
    function fcmanager_render_team_players_block($attributes, $content)
    {
        $player_placeholder_img =  plugin_dir_url(dirname(__DIR__, 1)) . 'assets/player.svg';

        $team_id = isset($attributes['teamId']) && $attributes['teamId'] > 0
            ? intval($attributes['teamId'])
            : get_the_ID();

        $team_post = get_post($team_id);

        if ($team_post && $team_post->post_type === 'fcmanager_team') {
            $players = get_posts([
                'post_type' => 'fcmanager_player',
                'meta_key' => '_fcmanager_player_team',
                'meta_value' => $team_id,
                'posts_per_page' => -1,
            ]);

            ob_start();
?>
            <div class="fcmanager-team-players">
                <h2><?php printf(__("Players of %s", 'football-club-manager'), $team_post->post_title); ?></h2>

                <?php if ($players): ?>
                    <ul class="fcmanager-player-list">
                        <?php foreach ($players as $player): ?>
                            <li class="fcmanager-player-card">
                                <figure class="fcmanager-player-photo">
                                    <?php
                                    if (has_post_thumbnail($player->ID)) {
                                        echo get_the_post_thumbnail($player->ID, 'medium');
                                    } else {
                                    ?>
                                        <div class="fcmanager-placeholder" style="
                                            -webkit-mask-image: url(<?php echo $player_placeholder_img; ?>);
                                            mask-image: url(<?php echo $player_placeholder_img; ?>)"></div>
                                    <?php
                                    }
                                    ?>
                                </figure>
                                <strong>
                                    <?php
                                    echo esc_html(get_post_meta($player->ID, '_fcmanager_player_first_name', true)) . ' ' .
                                        esc_html(get_post_meta($player->ID, '_fcmanager_player_last_name', true));
                                    ?>
                                </strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?php _e('No players found for this team.', 'football-club-manager'); ?></p>
                <?php endif; ?>
            </div>
<?php
            return ob_get_clean();
        }
    }

    register_block_type(__DIR__, [
        'render_callback' => 'fcmanager_render_team_players_block',
    ]);
}
