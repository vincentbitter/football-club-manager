<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once 'class-person.php';

class FCManager_Birthday extends FCManager_Person
{
    public function __construct($id_or_post = null)
    {

        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
            $this->publish_birthday = true;
            $this->publish_age = FCManager_Settings::instance()->birthday->publish_age_by_default();
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
            $this->first_name = get_post_meta($this->id, '_fcmanager_birthday_first_name', true);
            $this->last_name = get_post_meta($this->id, '_fcmanager_birthday_last_name', true);

            $date_of_birth = get_post_meta($this->id, '_fcmanager_birthday_date_of_birth', true);
            $this->date_of_birth = $date_of_birth ? new DateTime($date_of_birth) : null;

            $this->publish_birthday = get_post_meta($this->id, '_fcmanager_birthday_publish_birthday', true) === 'true';
            $this->publish_age = get_post_meta($this->id, '_fcmanager_birthday_publish_age', true) === 'true';
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }

    public static function get_form_fields()
    {
        return array_values(array_filter(parent::get_form_fields(), fn($f) => $f['key'] !== 'publish_birthday'));
    }

    public function save()
    {
        if (!$this->id) {
            $this->id = wp_insert_post([
                'post_type' => 'fcmanager_birthday',
                'post_status' => 'publish',
            ]);
        }

        update_post_meta($this->id, '_fcmanager_birthday_first_name', $this->first_name());
        update_post_meta($this->id, '_fcmanager_birthday_last_name', $this->last_name());
        update_post_meta($this->id, '_fcmanager_birthday_date_of_birth', $this->date_of_birth() ? $this->date_of_birth()->format('Y-m-d') : '');
        update_post_meta($this->id, '_fcmanager_birthday_publish_birthday', $this->publish_birthday ? 'true' : 'false');
        update_post_meta($this->id, '_fcmanager_birthday_publish_age', $this->publish_age ? 'true' : 'false');

        $this->save_title();
    }
}
