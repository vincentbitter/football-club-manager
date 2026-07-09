<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once 'class-age-category.php';
require_once 'class-team-gender.php';

class FCManager_Team
{
    protected $id;
    protected $name;
    protected $gender;
    protected $age_category;

    public function __construct($id_or_post = null)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
            $this->gender(FCManager_TeamGender::MALE);
            $this->age_category(FCManager_AgeCategory::SENIORS);
            return;
        }

        if (is_int($id_or_post)) {
            $id_or_post = get_post($id_or_post);
            if (!$id_or_post) {
                throw new InvalidArgumentException('Invalid post ID.');
            }
        }

        if ($id_or_post instanceof WP_Post) {
            $this->id = $id_or_post->ID;
            $this->name = $id_or_post->post_title;
            $this->age_category = get_post_meta($this->id, '_fcmanager_team_age_category', true);
            $this->gender = get_post_meta($this->id, '_fcmanager_team_gender', true);
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }

    public function name($new_value = null)
    {
        if ($new_value !== null) {
            $this->name = $new_value;
        }
        return $this->name;
    }

    public function gender($new_value = null)
    {
        if ($new_value !== null && in_array($new_value, FCManager_TeamGender::values(), true)) {
            $this->gender = $new_value;
        }
        return $this->gender;
    }

    public function age_category($new_value = null)
    {
        if ($new_value !== null && in_array($new_value, FCManager_AgeCategory::values(), true)) {
            $this->age_category = $new_value;
        }
        return $this->age_category;
    }

    private function save_title()
    {
        wp_update_post([
            'ID' => $this->id,
            'post_title' => $this->name(),
            'post_name' => sanitize_title($this->name()),
        ]);
    }

    public function save()
    {
        if (!$this->id) {
            $this->id = wp_insert_post([
                'post_type' => 'fcmanager_team',
                'post_status' => 'publish',
            ]);
        }

        update_post_meta($this->id, '_fcmanager_team_age_category', $this->age_category());
        update_post_meta($this->id, '_fcmanager_team_gender', $this->gender());

        $this->save_title();
    }
}
