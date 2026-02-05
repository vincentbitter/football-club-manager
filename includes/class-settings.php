<?php

if (! defined('ABSPATH')) {
    exit;
}

abstract class FCManager_Settings_Base
{
    /**
     * Get the value of an option, or update it if a new value is provided.
     */
    protected function get_or_update($option_name, $new_value = null)
    {
        if ($new_value !== null) {
            $options = get_option('fcmanager_options');
            $options[$option_name] = $new_value;
            update_option('fcmanager_options', $options);
        }
        return get_option('fcmanager_options')[$option_name];
    }

    /**
     * Get the boolean value of an option, or update it if a new value is provided.
     */
    protected function get_or_update_boolean($option_name, $new_value = null)
    {
        return $this->get_or_update($option_name, ($new_value !== null) ? $new_value === '1' || $new_value === 'true' : null) === '1';
    }
}

class FCManager_Settings
{
    protected static $_instance = null;
    /** @var FCManager_Player_Settings */
    public $player;

    /**
     * This method will create and return the only instance of this class.
     *
     * @return FCManager_Settings Returns an instance of the class
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->player = new FCManager_Player_Settings();
    }
}

class FCManager_Player_Settings extends FCManager_Settings_Base
{
    public function publish_birthday_by_default($newValue = null)
    {
        return $this->get_or_update_boolean('fcmanager_player_publish_birthday_by_default');
    }

    public function publish_age_by_default($newValue = null)
    {
        return $this->get_or_update_boolean('fcmanager_player_publish_age_by_default');
    }
}
