<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once 'class-person.php';

class FCManager_Player extends FCManager_Person
{
    private $team_id;

    public function __construct($id_or_post = null)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
            $this->publish_birthday = FCManager_Settings::instance()->player->publish_birthday_by_default();
            $this->publish_age = FCManager_Settings::instance()->player->publish_age_by_default();
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
            $this->first_name = get_post_meta($this->id, '_fcmanager_player_first_name', true);
            $this->last_name = get_post_meta($this->id, '_fcmanager_player_last_name', true);

            $date_of_birth = get_post_meta($this->id, '_fcmanager_player_date_of_birth', true);
            $this->date_of_birth = $date_of_birth ? new DateTime($date_of_birth) : null;

            $this->publish_birthday = get_post_meta($this->id, '_fcmanager_player_publish_birthday', true) === 'true';
            $this->publish_age = get_post_meta($this->id, '_fcmanager_player_publish_age', true) === 'true';
            $this->team_id = get_post_meta($this->id, '_fcmanager_player_team', true);
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }

    public function team_id($new_value = null)
    {
        if ($new_value !== null) {
            $this->team_id = $new_value;
        }
        return $this->team_id;
    }
}
