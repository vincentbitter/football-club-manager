<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once 'class-person.php';

class FCManager_Volunteer extends FCManager_Person
{
    public function __construct($id_or_post = null)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
            $this->publish_birthday = FCManager_Settings::instance()->volunteer->publish_birthday_by_default();
            $this->publish_age = FCManager_Settings::instance()->volunteer->publish_age_by_default();
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
            $this->first_name = get_post_meta($this->id, '_fcmanager_volunteer_first_name', true);
            $this->last_name = get_post_meta($this->id, '_fcmanager_volunteer_last_name', true);

            $date_of_birth = get_post_meta($this->id, '_fcmanager_volunteer_date_of_birth', true);
            $this->date_of_birth = $date_of_birth ? new DateTime($date_of_birth) : null;

            $this->publish_birthday = get_post_meta($this->id, '_fcmanager_volunteer_publish_birthday', true) === 'true';
            $this->publish_age = get_post_meta($this->id, '_fcmanager_volunteer_publish_age', true) === 'true';
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }
}
