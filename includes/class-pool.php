<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Pool
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $competition_id;
    /** @var string */
    protected $division;
    /** @var string */
    protected $name;

    public function __construct($id_or_post = null)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post ? $id_or_post->ID : null;
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
            $this->division = get_post_meta($this->id, '_fcmanager_pool_division', true);
            $this->name = get_post_meta($this->id, '_fcmanager_pool_name', true);

            $this->competition_id = $id_or_post->post_parent;
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }

    public function id()
    {
        return $this->id;
    }


    public function division($new_value = null)
    {
        if ($new_value !== null) {
            $this->division = $new_value;
        }
        return $this->division;
    }

    public function name($new_value = null)
    {
        if ($new_value !== null) {
            $this->name = $new_value;
        }
        return $this->name;
    }

    public function competition_id($new_value = null)
    {
        if ($new_value !== null) {
            $this->competition_id = (int) $new_value;
        }
        return $this->competition_id;
    }

    public function to_string()
    {
        return $this->division() . ' ' . $this->name();
    }

    private function save_title()
    {
        $name = $this->to_string();
        wp_update_post([
            'ID' => $this->id,
            'post_title' => $name,
            'post_name' => sanitize_title($name),
        ]);
    }

    public function validate()
    {
        if (!($this->competition_id() > 0)) {
            return false;
        }

        if (empty($this->division())) {
            return false;
        }

        if (empty($this->name())) {
            return false;
        }

        return true;
    }

    public function save()
    {
        if (!$this->validate()) {
            throw new InvalidArgumentException(esc_html__('Invalid pool details.', 'football-club-manager'));
        }

        if (!$this->id) {
            $this->id = wp_insert_post([
                'post_type' => 'fcmanager_pool',
                'post_status' => 'publish',
                'post_parent' => $this->competition_id()
            ]);
        } else {
            wp_update_post([
                'ID'          => $this->id,
                'post_status' => 'publish',
                'post_parent' => $this->competition_id(),
            ]);
        }

        update_post_meta($this->id, '_fcmanager_pool_division', $this->division());
        update_post_meta($this->id, '_fcmanager_pool_name', $this->name());

        $this->save_title();
    }
}
