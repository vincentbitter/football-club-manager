<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../includes/import/class-mapping-file.php';

class FCManager_Import_Storage
{
    protected static function base_dir()
    {
        $dir = sys_get_temp_dir() . '/fcm-imports';
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
        return $dir;
    }

    protected static function filename($user_id, $guid, $extension)
    {
        return self::base_dir() . "/{$user_id}-{$guid}.{$extension}";
    }

    public static function store($user_id, $guid, $file_tmp_name, $extension, $target_class, $mapping_rules)
    {
        global $wp_filesystem;
        WP_Filesystem();
        $wp_filesystem->move($file_tmp_name, self::filename($user_id, $guid, $extension));

        $mapping = new FCManager_Import_Mapping_File($extension, $target_class, $mapping_rules);
        file_put_contents(self::filename($user_id, $guid, 'mapping'), json_encode($mapping));

        return $extension;
    }

    public static function get_import_filename($user_id, $guid)
    {
        $mapping = self::load_mapping($user_id, $guid);
        return self::filename($user_id, $guid, $mapping->extension());
    }

    private static function load_mapping($user_id, $guid)
    {
        $filename = self::filename($user_id, $guid, 'mapping');
        $file_content = json_decode(file_get_contents($filename), true);
        return FCManager_Import_Mapping_File::jsonDeserialize($file_content);
    }

    public static function load_target_class($user_id, $guid)
    {
        return self::load_mapping($user_id, $guid)->target_class();
    }

    public static function load_mapping_rules($user_id, $guid)
    {
        return self::load_mapping($user_id, $guid)->mapping();
    }
}
