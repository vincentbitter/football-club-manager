<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_field_toggle_callback($args)
{
    $options = get_option('fcmanager_options');
?>
    <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>"
        name="fcmanager_options[<?php echo esc_attr($args['label_for']); ?>]"
        value="1" <?php checked(
                        1,
                        (isset($options[$args['label_for']]) ? $options[$args['label_for']] : 0)
                    ); ?>>
<?php
}


function fcmanager_options_sanitize_callback($input)
{
    $input['fcmanager_player_publish_birthday_by_default'] = $input['fcmanager_player_publish_birthday_by_default'] == 1 ? 1 : 0;
    $input['fcmanager_player_publish_age_by_default'] = $input['fcmanager_player_publish_age_by_default'] == 1 ? 1 : 0;

    return $input;
}

function fcmanager_settings_init()
{
    // Register a new setting for Football Club Manager.
    register_setting('fcmanager', 'fcmanager_options', 'fcmanager_options_sanitize_callback');

    // Register a new section in the Football Club Manager page.
    add_settings_section(
        'fcmanager_section_player_settings',
        __('Player Settings', 'football-club-manager'),
        null,
        'fcmanager'
    );

    // Register "Publish birthday by default" toggle: fcmanager > fcmanager_section_player_settings > fcmanager_player_publish_birthday_by_default.
    add_settings_field(
        'fcmanager_player_publish_birthday_by_default',
        __('Publish birthday by default', 'football-club-manager'),
        'fcmanager_field_toggle_callback',
        'fcmanager',
        'fcmanager_section_player_settings',
        array('label_for' => 'fcmanager_player_publish_birthday_by_default')
    );

    // Register "Publish age by default" toggle: fcmanager > fcmanager_section_player_settings > fcmanager_player_publish_age_by_default.
    add_settings_field(
        'fcmanager_player_publish_age_by_default',
        __('Publish age by default', 'football-club-manager'),
        'fcmanager_field_toggle_callback',
        'fcmanager',
        'fcmanager_section_player_settings',
        array('label_for' => 'fcmanager_player_publish_age_by_default')
    );
}
