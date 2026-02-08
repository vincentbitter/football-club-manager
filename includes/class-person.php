<?php

if (! defined('ABSPATH')) {
    exit;
}

abstract class FCManager_Person
{
    protected $id;
    protected $first_name;
    protected $last_name;
    protected $date_of_birth;
    protected $publish_birthday;
    protected $publish_age;

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
        }
        return $this->first_name;
    }

    public function last_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->last_name = $new_value;
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
        }
        return $this->publish_birthday;
    }

    public function publish_age($new_value = null)
    {
        if ($new_value !== null) {
            $this->publish_age = $new_value;
        }
        return $this->publish_age;
    }

    protected function save_title()
    {
        wp_update_post([
            'ID' => $this->id,
            'post_title' => $this->name(),
        ]);
    }
}
