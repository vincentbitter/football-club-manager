<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_page_import()
{
    if (! current_user_can('edit_posts')) {
        return;
    }

    if (! isset($_GET['post_type'])) {
        echo '<div class="notice notice-error"><p>' . esc_html__('No post type provided.', 'football-club-manager') . '</p></div>';
        return;
    }

    $target_class = '';
    switch ($_GET['post_type']) {
        case 'fcmanager_birthday':
            $target_class = FCManager_Birthday::class;
            break;
        default:
            echo '<div class="notice notice-error"><p>' . esc_html__('Unsupported post type for import.', 'football-club-manager') . '</p></div>';
            return;
    }

    $supported_extensions = FCManager_Import_Parser_Resolver::supported_extensions();

    wp_localize_script(
        'fcmanager-import',
        'FCMANAGER_IMPORT_DATA',
        [
            'target_class' => $target_class,
            'fields' => $target_class::get_form_fields(),
            'supported_extensions' => $supported_extensions,
        ]
    );
?>
    <div class="wrap fcm-import">
        <h1><?php esc_html_e('Import', 'football-club-manager'); ?></h1>

        <label id="fcm-dropzone" class="fcm-dropzone" for="fcm-file-input">
            <div class="fcm-dropzone-instructions">
                <p><?php esc_html_e('Drag your file here, or', 'football-club-manager'); ?></p>
                <p class="button button-primary">
                    <?php esc_html_e('Upload a file', 'football-club-manager'); ?>
                </p>
            </div>
            <p class="fcm-dropzone-selected-file"></p>
            <input type="file" id="fcm-file-input" accept="<?php echo esc_attr(implode(', ', array_map(fn($v) => '.' . $v, $supported_extensions))); ?>" hidden>
        </label>

        <div id="fcm-mapping" style="display:none;">
            <h2><?php esc_html_e('Map Columns', 'football-club-manager'); ?></h2>
            <form id="fcm-mapping-form" method="post">
                <?php wp_nonce_field('fcmanager_import', 'fcmanager_nonce'); ?>
                <table class="wp-list-table widefat striped table-view-list">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Field', 'football-club-manager'); ?></th>
                            <th><?php esc_html_e('Map from', 'football-club-manager'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="fcm-mapping-body">
                        <tr id="fcm-mapping-row-template">
                            <td data-content="to_field"></td>
                            <td data-content="from_field"></td>
                            <td data-content="extra"></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e('Import', 'football-club-manager'); ?></button>
                </p>
                <div id="fcmanager-progress" style="display:none;">
                    <div class="fcmanager-progress-bar">
                        <div class="fcmanager-progress-fill"></div>
                        <p class="fcmanager-progress-text">0%</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php
}
