<?php

use function PHPSTORM_META\map;

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-player.php';
require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-volunteer.php';
require_once plugin_dir_path(dirname(__DIR__)) . 'includes/class-birthday.php';

/**
 * Retrieve all players or volunteers whose birthday is today and who have enabled birthday display.
 * Also, include all birthday custom post types.
 * 
 * @param string $post_type 'player', 'volunteer', or 'birthday'
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
    $birthdays = fcmanager_get_birthdays('birthday', 'FCManager_Birthday');

    /** @var FCManager_Person[] */
    $people = [];

    foreach (array_merge($players, $volunteers, $birthdays) as $person) {
        $key = $person->name() . '.' . $person->age();
        $people[$key] = $person;
    }

    $people = array_values($people);

    usort($people, function ($a, $b) {
        $nameCompare = strcmp($a->name(), $b->name());
        return $nameCompare !== 0 ? $nameCompare : $a->age() <=> $b->age();
    });

    ob_start();
?>
    <div class="fcmanager-birthdays">
        <h2><?php
            esc_html_e("Birthdays", 'football-club-manager'); ?></h2>

        <?php if ($people): ?>
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
