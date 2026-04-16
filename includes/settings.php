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

function fcmanager_field_textarea_callback($args)
{
    $options = get_option('fcmanager_options');
?>
    <textarea id="<?php echo esc_attr($args['label_for']); ?>"
        name="fcmanager_options[<?php echo esc_attr($args['label_for']); ?>]"
        rows="5" cols="50"><?php echo isset($options[$args['label_for']]) ? esc_textarea($options[$args['label_for']]) : ''; ?></textarea>
    <?php if (isset($args['description'])) : ?>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
    <?php endif; ?>
<?php
}

function fcmanager_field_number_callback($args)
{
    $options = get_option('fcmanager_options');
?>
    <input type="number" id="<?php echo esc_attr($args['label_for']); ?>"
        name="fcmanager_options[<?php echo esc_attr($args['label_for']); ?>]"
        value="<?php echo isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>">
    <?php if (isset($args['description'])) : ?>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
    <?php endif; ?>
<?php
}

function fcmanager_field_select_callback($args)
{
    $options = get_option('fcmanager_options');
    $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
?>
    <select id="<?php echo esc_attr($args['label_for']); ?>"
        name="fcmanager_options[<?php echo esc_attr($args['label_for']); ?>]">
        <?php foreach ($args['options'] as $option_value => $option_label) : ?>
            <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
                <?php echo esc_html($option_label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($args['description'])) : ?>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
    <?php endif; ?>
<?php
}

function fcmanager_options_sanitize_callback($input)
{
    $input['fcmanager_player_publish_birthday_by_default'] = $input['fcmanager_player_publish_birthday_by_default'] == 1 ? 1 : 0;
    $input['fcmanager_player_publish_age_by_default'] = $input['fcmanager_player_publish_age_by_default'] == 1 ? 1 : 0;
    $input['fcmanager_volunteer_publish_birthday_by_default'] = $input['fcmanager_volunteer_publish_birthday_by_default'] == 1 ? 1 : 0;
    $input['fcmanager_volunteer_publish_age_by_default'] = $input['fcmanager_volunteer_publish_age_by_default'] == 1 ? 1 : 0;
    $input['fcmanager_signup_extra_fields'] = sanitize_textarea_field($input['fcmanager_signup_extra_fields']);
    $input['fcmanager_signup_require_parents_till_age'] = is_numeric($input['fcmanager_signup_require_parents_till_age']) ? (int) $input['fcmanager_signup_require_parents_till_age'] : "";
    $input['fcmanager_signup_captcha_provider'] = sanitize_text_field($input['fcmanager_signup_captcha_provider']);
    $input['fcmanager_birthday_publish_age_by_default'] = $input['fcmanager_birthday_publish_age_by_default'] == 1 ? 1 : 0;

    return $input;
}

function fcmanager_settings_init()
{
    // Register a new setting for Football Club Manager.
    register_setting('fcmanager', 'fcmanager_options', 'fcmanager_options_sanitize_callback');

    // Register player settings section in the Football Club Manager page.
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

    // Register volunteer settings section in the Football Club Manager page.
    add_settings_section(
        'fcmanager_section_volunteer_settings',
        __('Volunteer Settings', 'football-club-manager'),
        null,
        'fcmanager'
    );

    // Register "Publish birthday by default" toggle: fcmanager > fcmanager_section_volunteer_settings > fcmanager_volunteer_publish_birthday_by_default.
    add_settings_field(
        'fcmanager_volunteer_publish_birthday_by_default',
        __('Publish birthday by default', 'football-club-manager'),
        'fcmanager_field_toggle_callback',
        'fcmanager',
        'fcmanager_section_volunteer_settings',
        array('label_for' => 'fcmanager_volunteer_publish_birthday_by_default')
    );

    // Register "Publish age by default" toggle: fcmanager > fcmanager_section_volunteer_settings > fcmanager_volunteer_publish_age_by_default.
    add_settings_field(
        'fcmanager_volunteer_publish_age_by_default',
        __('Publish age by default', 'football-club-manager'),
        'fcmanager_field_toggle_callback',
        'fcmanager',
        'fcmanager_section_volunteer_settings',
        array('label_for' => 'fcmanager_volunteer_publish_age_by_default')
    );

    // Register signup settings section in the Football Club Manager page.
    add_settings_section(
        'fcmanager_section_signup_settings',
        __('Signup Settings', 'football-club-manager'),
        null,
        'fcmanager'
    );

    // Register "Extra fields" field: fcmanager > fcmanager_section_signup_settings > fcmanager_signup_extra_fields.
    add_settings_field(
        'fcmanager_signup_extra_fields',
        __('Extra Fields', 'football-club-manager'),
        'fcmanager_field_textarea_callback',
        'fcmanager',
        'fcmanager_section_signup_settings',
        array(
            'label_for' => 'fcmanager_signup_extra_fields',
            'description' => __('Define extra fields for the signup form. One field per line. These fields will be added to the "Additional Information" section of the signup form.', 'football-club-manager')
        )
    );

    // Register "Require parents till age" field: fcmanager > fcmanager_section_signup_settings > fcmanager_signup_require_parents_till_age.
    add_settings_field(
        'fcmanager_signup_require_parents_till_age',
        __('Require parents till age', 'football-club-manager'),
        'fcmanager_field_number_callback',
        'fcmanager',
        'fcmanager_section_signup_settings',
        array(
            'label_for' => 'fcmanager_signup_require_parents_till_age',
            'description' => __('Set the age until which parents are required for the signup form.', 'football-club-manager')
        )
    );

    // Register "Captcha provider" field: fcmanager > fcmanager_section_signup_settings > fcmanager_signup_captcha_provider.
    add_settings_field(
        'fcmanager_signup_captcha_provider',
        __('Captcha provider', 'football-club-manager'),
        'fcmanager_field_select_callback',
        'fcmanager',
        'fcmanager_section_signup_settings',
        array(
            'label_for' => 'fcmanager_signup_captcha_provider',
            'description' => __('Select the captcha provider to use for the signup form.', 'football-club-manager'),
            'options' => array_merge(['' => __('None', 'football-club-manager')], array_combine(FCManager_CaptchaProviderFactory::get_providers(), FCManager_CaptchaProviderFactory::get_providers()))
        )
    );

    // Register birthday settings section in the Football Club Manager page.
    add_settings_section(
        'fcmanager_section_birthday_settings',
        __('Birthday Settings', 'football-club-manager'),
        null,
        'fcmanager'
    );

    // Register "Publish age by default" toggle: fcmanager > fcmanager_section_birthday_settings > fcmanager_birthday_publish_age_by_default.
    add_settings_field(
        'fcmanager_birthday_publish_age_by_default',
        __('Publish age by default', 'football-club-manager'),
        'fcmanager_field_toggle_callback',
        'fcmanager',
        'fcmanager_section_birthday_settings',
        array('label_for' => 'fcmanager_birthday_publish_age_by_default')
    );
}
