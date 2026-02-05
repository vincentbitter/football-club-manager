<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Player
{
    private $id;
    private $first_name;
    private $last_name;
    private $date_of_birth;
    private $publish_birthday;
    private $publish_age;
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

    public function age($only_if_published = true)
    {
        if (!$this->date_of_birth) {
            return null;
        }

        // If the user cannot edit posts, only show the age if it's published.
        if (!current_user_can('edit_posts'))
            $only_if_published = true;

        // Hide age if not published
        if ($only_if_published && !$this->publish_age) {
            return null;
        }

        if ($this->date_of_birth->format('Y') <= 1900) {
            return null; // Invalid date of birth
        }
        $today = new DateTime();
        $age = $today->diff($this->date_of_birth)->y;

        return $age;
    }

    public function first_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->first_name = $new_value;
            update_post_meta($this->id, '_fcmanager_player_first_name', trim($new_value));
            $this->save_title();
        }
        return $this->first_name;
    }

    public function last_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->last_name = $new_value;
            update_post_meta($this->id, '_fcmanager_player_last_name', trim($new_value));
            $this->save_title();
        }
        return $this->last_name;
    }

    public function name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function date_of_birth($new_value = null)
    {
        if ($new_value !== null) {
            if ($new_value instanceof DateTime) {
                $this->date_of_birth = $new_value;
                update_post_meta($this->id, '_fcmanager_player_date_of_birth', $new_value->format('Y-m-d'));
            } else {
                throw new InvalidArgumentException('Expected a DateTime object.');
            }
        }

        if ($this->publish_birthday === false && !current_user_can('edit_posts')) {
            return null;
        }

        if ($this->publish_age === false && !current_user_can('edit_posts')) {
            $new_date = clone $this->date_of_birth;
            $new_date->setDate(1900, $new_date->format('m'), $new_date->format('d'));
            return $new_date;
        }

        return $this->date_of_birth;
    }

    public function publish_birthday($new_value = null)
    {
        if ($new_value !== null) {
            $this->publish_birthday = $new_value;
            update_post_meta($this->id, '_fcmanager_player_publish_birthday', $new_value ? 'true' : 'false');
        }
        return $this->publish_birthday;
    }

    public function publish_age($new_value = null)
    {
        if ($new_value !== null) {
            $this->publish_age = $new_value;
            update_post_meta($this->id, '_fcmanager_player_publish_age', $new_value ? 'true' : 'false');
        }
        return $this->publish_age;
    }

    public function team_id($new_value = null)
    {
        if ($new_value !== null) {
            $this->team_id = $new_value;
            update_post_meta($this->id, '_fcmanager_player_team', $new_value);
        }
        return $this->team_id;
    }

    private function save_title()
    {
        wp_update_post([
            'ID' => $this->id,
            'post_title' => $this->name(),
        ]);
    }
}
