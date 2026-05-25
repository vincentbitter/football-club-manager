<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../includes/import/class-storage.php';
require_once plugin_dir_path(__FILE__) . '../../includes/import/class-parser-resolver.php';
require_once plugin_dir_path(__FILE__) . '../../includes/import/class-mapping-rule.php';

function fcmanager_endpoint_import_upload()
{
    check_ajax_referer('fcmanager_import', 'fcmanager_nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('no_permission');
    }

    if (!isset($_POST['target_class']) || !class_exists(sanitize_text_field(wp_unslash($_POST['target_class'])))) {
        wp_send_json_error('No target class provided.');
    }

    if (!isset($_FILES['file']) || empty($_FILES['file']['tmp_name']) || empty($_FILES['file']['name'])) {
        wp_send_json_error('No file provided.');
    }

    if (!isset($_POST['mapping']) || empty($_POST['mapping'])) {
        wp_send_json_error('No mapping rules provided.');
    }

    $mapping_input = json_decode(sanitize_text_field(wp_unslash($_POST['mapping'])), true);
    if (!is_array($mapping_input) || empty($mapping_input) || empty(array_filter($mapping_input, fn($r) => !empty($r['from'])))) {
        wp_send_json_error('No mapping rules provided.');
    }

    $user_id = get_current_user_id();
    $guid = wp_generate_uuid4();

    $mapping_rules = [];
    foreach ($mapping_input as $rule) {
        $mapping_rules[] = FCManager_Import_Mapping_Rule::jsonDeserialize($rule);
    }

    $filename = sanitize_file_name(wp_unslash($_FILES['file']['name']));
    $tmp_filename = $_FILES['file']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $parser = FCManager_Import_Parser_Resolver::resolve($tmp_filename, $extension);
    $rows = $parser->get_total_rows();

    FCManager_Import_Storage::store($user_id, $guid, $tmp_filename, $extension, sanitize_text_field(wp_unslash($_POST['target_class'])), $mapping_rules);

    wp_send_json_success([
        'guid' => $guid,
        'total' => $rows
    ]);
}

add_action('wp_ajax_fcmanager_import_upload', 'fcmanager_endpoint_import_upload');
