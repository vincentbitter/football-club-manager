<?php

use function PHPSTORM_META\map;

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-player.php';
require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-volunteer.php';

/**
 * Retrieve all players or volunteers whose birthday is today and who have enabled birthday display.
 *
 * @param string $post_type 'player' or 'volunteer'
 * @return WP_Post[] List of posts matching the criteria.
 */
function fcmanager_get_birthdays($post_type, $class)
{
    $posts = get_posts([
        'post_type' => 'fcmanager_' . $post_type,
        'meta_query' => [
            [
                'key' => '_fcmanager_' . $post_type . '_date_of_birth',
                'value' => '-' . wp_date('m-d'),
                'compare' => 'LIKE'
            ],
            [
                'key' => '_fcmanager_' . $post_type . '_publish_birthday',
                'value' => 'true',
                'compare' => '='
            ]
        ],
        'orderby' => 'title',
        'order' => 'ASC',
        'posts_per_page' => -1,
    ]);

    return array_map(fn($post) => new $class($post), $posts);
}

function fcmanager_render_birthdays_block($attributes, $content)
{
    $players = fcmanager_get_birthdays('player', 'FCManager_Player');
    $volunteers = fcmanager_get_birthdays('volunteer', 'FCManager_Volunteer');

    /** @var FCManager_Person[] */
    $people = array_merge($players, $volunteers);

    ob_start();
?>
    <div class="fcmanager-birthdays">
        <h2><?php
            esc_html_e("Birthdays", 'football-club-manager'); ?></h2>

        <?php if ($players): ?>
            <ul class="fcmanager-people-name-list">
                <?php foreach ($people as $person): ?>
                    <li class="fcmanager-player-item">
                        <?php echo esc_html($person->name()); ?>
                        <?php $age = $person->age();
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
