<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_teams_block($attributes, $content)
{
    $columns = isset($attributes['columns']) && $attributes['columns'] > 0
        ? intval($attributes['columns'])
        : 1;

    $gender_filter = ['relation' => 'OR'];
    foreach ($attributes['genders'] as $gender) {
        $gender_filter[] = [
            'key'     => '_fcmanager_team_gender',
            'value'   => $gender,
            'compare' => '=',
        ];
    }

    $age_category_filter = ['relation' => 'OR'];
    foreach ($attributes['ageCategories'] as $age_category) {
        $age_category_filter[] = [
            'key'     => '_fcmanager_team_age_category',
            'value'   => $age_category,
            'compare' => '=',
        ];
    }

    $teams = [];
    if (count($gender_filter) > 1 && count($age_category_filter) > 1) {
        $teams = get_posts([
            'post_type' => 'fcmanager_team',
            'meta_query' => [
                $gender_filter,
                $age_category_filter,
            ],
            'orderby' => [
                'title' => 'ASC'
            ],
            'posts_per_page' => -1,
        ]);
    }

    $teams = fcmanager_sort_teams($teams, fn($team) => $team->post_title);

    $items_per_column = count($teams) / $columns;

    ob_start();
?>
    <div class="fcmanager-teams-block" <?php echo get_block_wrapper_attributes(); ?>>
        <div class="wp-block-columns">
            <?php for ($i = 0; $i < $columns; $i++) : ?>
                <div class="wp-block-column">
                    <ul>
                        <?php for ($j = (int)ceil($i * $items_per_column); $j < ceil(($i + 1) * $items_per_column) && $j < count($teams); $j++) : ?>
                            <li>
                                <a href="#"><?php esc_html_e($teams[$j]->post_title); ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endfor; ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_teams_block',
]);
