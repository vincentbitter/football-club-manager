<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-player.php';

function fcmanager_render_birthdays_block($attributes, $content)
{
    $players = get_posts([
        'post_type' => 'fcmanager_player',
        'meta_query' => [
            [
                'key' => '_fcmanager_player_date_of_birth',
                'value' => '-' . wp_date('m-d'),
                'compare' => 'LIKE'
            ],
            [
                'key' => '_fcmanager_player_publish_birthday',
                'value' => 'true',
                'compare' => '='
            ]
        ],
        'orderby' => 'title',
        'order' => 'ASC',
        'posts_per_page' => -1,
    ]);

    ob_start();
?>
    <div class="fcmanager-birthdays">
        <h2><?php
            esc_html_e("Birthdays", 'football-club-manager'); ?></h2>

        <?php if ($players): ?>
            <ul class="fcmanager-player-name-list">
                <?php foreach ($players as $player): ?>
                    <?php $player_obj = new FCManager_Player($player); ?>
                    <li class="fcmanager-player-item">
                        <?php echo esc_html($player_obj->name()); ?>
                        <?php $age = $player_obj->age();
                        if ($age !== null) echo ' (' . esc_html($age) . ')'; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?php esc_html_e('No birthdays today.', 'football-club-manager'); ?></p>
        <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_birthdays_block',
]);
