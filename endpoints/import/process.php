<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../includes/import/class-parser-resolver.php';
require_once plugin_dir_path(__FILE__) . '../../includes/import/class-processor.php';
require_once plugin_dir_path(__FILE__) . '../../includes/import/class-storage.php';

function fcmanager_endpoint_import_process()
{
    check_ajax_referer('fcmanager_import', 'fcmanager_nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('no_permission');
    }

    if (!isset($_POST['guid'])) {
        wp_send_json_error('No guid provided.');
    }

    if (!isset($_POST['offset'])) {
        wp_send_json_error('No offset provided.');
    }


    $guid = sanitize_text_field(wp_unslash($_POST['guid']));
    $offset = intval($_POST['offset']);
    $user_id = get_current_user_id();

    $filename = FCManager_Import_Storage::get_import_filename($user_id, $guid);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $parser = FCManager_Import_Parser_Resolver::resolve($filename, $extension);
    $target_class = FCManager_Import_Storage::load_target_class($user_id, $guid);
    $mapping = FCManager_Import_Storage::load_mapping_rules($user_id, $guid);

    $processor = new FCManager_Import_Processor($parser, $target_class, $mapping);
    $processed = $processor->run_chunk($offset);

    wp_send_json_success([
        'offset' => $offset,
        'processed' => $processed,
        'next_offset' => $offset + $processed,
    ]);
}

add_action('wp_ajax_fcmanager_import_process', 'fcmanager_endpoint_import_process');
