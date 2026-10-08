<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Competition
{
    protected $id;
    protected $name;
    protected $start_date;
    protected $end_date;

    public function __construct($id_or_post = null)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
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

            $start_date = get_post_meta($this->id, '_fcmanager_comp_start_date', true);
            $this->start_date = $start_date ? new DateTime($start_date) : null;

            $end_date = get_post_meta($this->id, '_fcmanager_comp_end_date', true);
            $this->end_date = $end_date ? new DateTime($end_date) : null;
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

    public function start_date($new_value = null)
    {
        if ($new_value !== null) {
            if ($new_value instanceof DateTime) {
                $this->start_date = $new_value;
            } else {
                throw new InvalidArgumentException('Expected a DateTime object.');
            }
        }

        return $this->start_date;
    }

    public function end_date($new_value = null)
    {
        if ($new_value !== null) {
            if ($new_value instanceof DateTime) {
                $this->end_date = $new_value;
            } else {
                throw new InvalidArgumentException('Expected a DateTime object.');
            }
        }

        return $this->end_date;
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

        update_post_meta($this->id, '_fcmanager_comp_start_date', $this->start_date() ? $this->start_date()->format('Y-m-d') : '');
        update_post_meta($this->id, '_fcmanager_comp_end_date', $this->end_date() ? $this->end_date()->format('Y-m-d') : '');

        $this->save_title();
    }
}
