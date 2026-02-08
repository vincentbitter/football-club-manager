<?php
if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_page_settings()
{

    if (! current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?php esc_html_e('Settings', 'football-club-manager'); ?></h1>
        <div class="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">
                <div class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="card">
                            <form action="options.php" method="post">
                                <?php
                                settings_fields('fcmanager');
                                do_settings_sections('fcmanager');
                                submit_button(__('Save settings', 'football-club-manager'));
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
