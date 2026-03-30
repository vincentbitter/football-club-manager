<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../class-settings.php');
require_once(dirname(__FILE__) . '/../class-signup.php');

// Register Custom Post Type: Signup
function fcmanager_register_signup_post_type()
{
    register_post_type(
        'fcmanager_signup',
        array(
            'labels'        => array(
                'name'          => __('Signups', 'football-club-manager'),
                'singular_name' => __('Signup', 'football-club-manager'),
                'add_new_item'     => __('New signup', 'football-club-manager'),
                'edit_item' => __('Edit signup', 'football-club-manager'),
                'not_found' => __('No signups found', 'football-club-manager'),
                'not_found_in_trash' => __('No signups found in the trash', 'football-club-manager'),
                'search_items' => __('Search signup', 'football-club-manager'),
            ),
            'show_ui'       => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports'      => array(''),
        )
    );
}

// Unregister Custom Post Type: Signup
function fcmanager_unregister_signup_post_type()
{
    unregister_post_type('fcmanager_signup');
}

// Add custom meta boxes to signup
function fcmanager_add_signup_meta_boxes()
{
    add_meta_box(
        'fcmanager_signup_personal_details_meta_box',
        __('Personal details', 'football-club-manager'),
        'fcmanager_render_signup_personal_details_meta_box',
        'fcmanager_signup',
        'normal',
        'high'
    );

    add_meta_box(
        'fcmanager_signup_parents_meta_box',
        __('Parent details', 'football-club-manager'),
        'fcmanager_render_signup_parents_meta_box',
        'fcmanager_signup',
        'normal',
        'default'
    );

    add_meta_box(
        'fcmanager_signup_payment_details_meta_box',
        __('Payment details', 'football-club-manager'),
        'fcmanager_render_signup_payment_details_meta_box',
        'fcmanager_signup',
        'normal',
        'default'
    );

    add_meta_box(
        'fcmanager_signup_additional_information_meta_box',
        __('Additional information', 'football-club-manager'),
        'fcmanager_render_signup_additional_information_meta_box',
        'fcmanager_signup',
        'normal',
        'low'
    );
}

add_action('add_meta_boxes', 'fcmanager_add_signup_meta_boxes');


// Render personal details meta box
function fcmanager_render_signup_personal_details_meta_box($post)
{
    // Retrieve current meta values
    $signup = new FCManager_Signup($post);

    // Show form
    wp_nonce_field('fcmanager_save_signup_personal_details_meta_box', 'fcmanager_signup_personal_details_meta_box_nonce');
?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            function set_age() {
                const dateOfBirth = new Date(jQuery(this).val());
                if (isNaN(dateOfBirth.getTime())) {
                    jQuery('#fcmanager_signup_personal_details_age').html('');
                    jQuery('#fcmanager_signup_personal_details_age_container').addClass('hidden');
                    return;
                }
                const today = new Date();
                let age = today.getFullYear() - dateOfBirth.getFullYear();
                const m = today.getMonth() - dateOfBirth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dateOfBirth.getDate())) {
                    age--;
                }

                jQuery('#fcmanager_signup_personal_details_age').html(age);
                jQuery('#fcmanager_signup_personal_details_age_container').removeClass('hidden');

                const requireParentsTillAge = <?php echo esc_js(FCManager_Settings::instance()->signup->require_parents_till_age()); ?>;
                if (requireParentsTillAge) {
                    if (age < requireParentsTillAge) {
                        jQuery('#fcmanager_signup_parents_meta_box').removeClass('closed');
                    } else {
                        jQuery('#fcmanager_signup_parents_meta_box').addClass('closed');
                    }
                }
            }

            const date_of_birth_input = jQuery('#fcmanager_signup_personal_details_date_of_birth');
            date_of_birth_input.change(set_age);
            set_age.call(date_of_birth_input[0]);
        });
    </script>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="fcmanager_signup_personal_details_first_name"><?php esc_html_e('First name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_signup_personal_details_first_name" name="fcmanager_signup_personal_details_first_name" value="<?php echo esc_attr($signup->personal_details()->first_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_initials"><?php esc_html_e('Initials', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_signup_personal_details_initials" name="fcmanager_signup_personal_details_initials" value="<?php echo esc_attr($signup->personal_details()->initials()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_middle_name"><?php esc_html_e('Middle name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_signup_personal_details_middle_name" name="fcmanager_signup_personal_details_middle_name" value="<?php echo esc_attr($signup->personal_details()->middle_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_last_name"><?php esc_html_e('Last name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="fcmanager_signup_personal_details_last_name" name="fcmanager_signup_personal_details_last_name" value="<?php echo esc_attr($signup->personal_details()->last_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_date_of_birth"><?php esc_html_e('Date of birth', 'football-club-manager'); ?></label></th>
                <td><input type="date" id="fcmanager_signup_personal_details_date_of_birth" name="fcmanager_signup_personal_details_date_of_birth" value="<?php echo esc_attr($signup->personal_details()->date_of_birth() != null ? $signup->personal_details()->date_of_birth()->format('Y-m-d') : ''); ?>">
                    <span class="hidden" id="fcmanager_signup_personal_details_age_container"><span class="description"><span id="fcmanager_signup_personal_details_age"></span> <?php esc_html_e('Year', 'football-club-manager'); ?></span></span>
                </td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_gender"><?php esc_html_e('Gender', 'football-club-manager'); ?></label></th>
                <td>
                    <select id="fcmanager_signup_personal_details_gender" name="fcmanager_signup_personal_details_gender">
                        <option value="male" <?php selected($signup->personal_details()->gender(), 'male'); ?>><?php esc_html_e('Male', 'football-club-manager'); ?></option>
                        <option value="female" <?php selected($signup->personal_details()->gender(), 'female'); ?>><?php esc_html_e('Female', 'football-club-manager'); ?></option>
                        <option value="gender neutral" <?php selected($signup->personal_details()->gender(), 'gender neutral'); ?>><?php esc_html_e('Gender neutral', 'football-club-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_mobile_phone_number"><?php esc_html_e('Mobile phone number', 'football-club-manager'); ?></label></th>
                <td><input type="tel" id="fcmanager_signup_personal_details_mobile_phone_number" name="fcmanager_signup_personal_details_mobile_phone_number" value="<?php echo esc_attr($signup->personal_details()->mobile_phone_number()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_phone_number"><?php esc_html_e('Phone number', 'football-club-manager'); ?></label></th>
                <td><input type="tel" id="fcmanager_signup_personal_details_phone_number" name="fcmanager_signup_personal_details_phone_number" value="<?php echo esc_attr($signup->personal_details()->phone_number()); ?>"></td>
            </tr>
            <tr>
                <th><label for="fcmanager_signup_personal_details_email_address"><?php esc_html_e('Email address', 'football-club-manager'); ?></label></th>
                <td><input type="email" id="fcmanager_signup_personal_details_email_address" name="fcmanager_signup_personal_details_email_address" value="<?php echo esc_attr($signup->personal_details()->email_address()); ?>"></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save personal data meta box
function fcmanager_save_signup_personal_details_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_signup')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_signup_personal_details_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_signup_personal_details_meta_box', 'fcmanager_signup_personal_details_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Edit meta values
    $signup = new FCManager_Signup($post_id);
    if (array_key_exists('fcmanager_signup_personal_details_first_name', $_POST))
        $signup->personal_details()->first_name(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_first_name'])));
    if (array_key_exists('fcmanager_signup_personal_details_initials', $_POST))
        $signup->personal_details()->initials(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_initials'])));
    if (array_key_exists('fcmanager_signup_personal_details_middle_name', $_POST))
        $signup->personal_details()->middle_name(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_middle_name'])));
    if (array_key_exists('fcmanager_signup_personal_details_last_name', $_POST))
        $signup->personal_details()->last_name(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_last_name'])));

    if (array_key_exists('fcmanager_signup_personal_details_date_of_birth', $_POST))
        $signup->personal_details()->date_of_birth(new DateTime(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_date_of_birth']))));
    if (array_key_exists('fcmanager_signup_personal_details_gender', $_POST))
        $signup->personal_details()->gender(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_gender'])));

    if (array_key_exists('fcmanager_signup_personal_details_mobile_phone_number', $_POST))
        $signup->personal_details()->mobile_phone_number(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_mobile_phone_number'])));
    if (array_key_exists('fcmanager_signup_personal_details_phone_number', $_POST))
        $signup->personal_details()->phone_number(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_phone_number'])));
    if (array_key_exists('fcmanager_signup_personal_details_email_address', $_POST))
        $signup->personal_details()->email_address(sanitize_text_field(wp_unslash($_POST['fcmanager_signup_personal_details_email_address'])));

    // Save
    remove_action('save_post_fcmanager_signup', 'fcmanager_save_signup_personal_details_meta_box');
    $signup->save();
}

add_action('save_post_fcmanager_signup', 'fcmanager_save_signup_personal_details_meta_box');


// Render parents meta box
function fcmanager_render_signup_parents_meta_box($post)
{
    // Retrieve current meta values
    $signup = new FCManager_Signup($post);

    // Show form
    wp_nonce_field('fcmanager_save_signup_parents_meta_box', 'fcmanager_signup_parents_meta_box_nonce');

    echo '<h3>' . __('Parent/guardian 1', 'football-club-manager') . '</h3>';
    fcmanager_render_signup_parent_meta_box(1, $signup->parent1());
    echo '<h3>' . __('Parent/guardian 2', 'football-club-manager') . '</h3>';
    fcmanager_render_signup_parent_meta_box(2, $signup->parent2());
}

// Render parent meta box
function fcmanager_render_signup_parent_meta_box($position, $parent)
{
    $prefix = 'fcmanager_signup_parent_' . $position . '_';
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'first_name'); ?>"><?php esc_html_e('First name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'first_name'); ?>" name="<?php echo esc_attr($prefix . 'first_name'); ?>" value="<?php echo esc_attr($parent->first_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'middle_name'); ?>"><?php esc_html_e('Middle name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'middle_name'); ?>" name="<?php echo esc_attr($prefix . 'middle_name'); ?>" value="<?php echo esc_attr($parent->middle_name()); ?>"></td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'last_name'); ?>"><?php esc_html_e('Last name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'last_name'); ?>" name="<?php echo esc_attr($prefix . 'last_name'); ?>" value="<?php echo esc_attr($parent->last_name()); ?>"></td>
            </tr>

            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'mobile_phone_number'); ?>"><?php esc_html_e('Mobile phone number', 'football-club-manager'); ?></label></th>
                <td><input type="tel" id="<?php echo esc_attr($prefix . 'mobile_phone_number'); ?>" name="<?php echo esc_attr($prefix . 'mobile_phone_number'); ?>" value="<?php echo esc_attr($parent->mobile_phone_number()); ?>"></td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'phone_number'); ?>"><?php esc_html_e('Phone number', 'football-club-manager'); ?></label></th>
                <td><input type="tel" id="<?php echo esc_attr($prefix . 'phone_number'); ?>" name="<?php echo esc_attr($prefix . 'phone_number'); ?>" value="<?php echo esc_attr($parent->phone_number()); ?>"></td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'email_address'); ?>"><?php esc_html_e('Email address', 'football-club-manager'); ?></label></th>
                <td><input type="email" id="<?php echo esc_attr($prefix . 'email_address'); ?>" name="<?php echo esc_attr($prefix . 'email_address'); ?>" value="<?php echo esc_attr($parent->email_address()); ?>"></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save parents meta box
function fcmanager_save_signup_parents_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_signup')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_signup_parents_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_signup_parents_meta_box', 'fcmanager_signup_parents_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Edit meta values
    $signup = new FCManager_Signup($post_id);
    fcmanager_save_signup_parent_meta_box(1, $signup->parent1());
    fcmanager_save_signup_parent_meta_box(2, $signup->parent2());

    // Save
    remove_action('save_post_fcmanager_signup', 'fcmanager_save_signup_parents_meta_box');
    $signup->save();
}

add_action('save_post_fcmanager_signup', 'fcmanager_save_signup_parents_meta_box');

function fcmanager_save_signup_parent_meta_box($position, $parent)
{
    $prefix = 'fcmanager_signup_parent_' . $position . '_';
    if (array_key_exists($prefix . 'first_name', $_POST))
        $parent->first_name(sanitize_text_field(wp_unslash($_POST[$prefix . 'first_name'])));
    if (array_key_exists($prefix . 'middle_name', $_POST))
        $parent->middle_name(sanitize_text_field(wp_unslash($_POST[$prefix . 'middle_name'])));
    if (array_key_exists($prefix . 'last_name', $_POST))
        $parent->last_name(sanitize_text_field(wp_unslash($_POST[$prefix . 'last_name'])));

    if (array_key_exists($prefix . 'mobile_phone_number', $_POST))
        $parent->mobile_phone_number(sanitize_text_field(wp_unslash($_POST[$prefix . 'mobile_phone_number'])));
    if (array_key_exists($prefix . 'phone_number', $_POST))
        $parent->phone_number(sanitize_text_field(wp_unslash($_POST[$prefix . 'phone_number'])));
    if (array_key_exists($prefix . 'email_address', $_POST))
        $parent->email_address(sanitize_email(wp_unslash($_POST[$prefix . 'email_address'])));
}

// Render payment details meta box
function fcmanager_render_signup_payment_details_meta_box($post)
{
    $prefix = 'fcmanager_signup_payment_details_';

    // Retrieve current meta values
    $signup = new FCManager_Signup($post);

    // Show form
    wp_nonce_field('fcmanager_save_signup_payment_details_meta_box', 'fcmanager_signup_payment_details_meta_box_nonce');
?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            function togglePaymentDetails() {
                const method = jQuery('#<?php echo esc_js($prefix); ?>method').val();
                jQuery('[data-method]').hide();
                jQuery('[data-method="' + method + '"]').show();
            }

            jQuery('#<?php echo esc_js($prefix); ?>method').change(togglePaymentDetails);
            togglePaymentDetails();
        });
    </script>
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="<?php echo esc_attr($prefix . 'method'); ?>"><?php esc_html_e('Payment method', 'football-club-manager'); ?></label></th>
                <td>
                    <select id="<?php echo esc_attr($prefix . 'method'); ?>" name="<?php echo esc_attr($prefix . 'method'); ?>">
                        <option value="direct_debit" <?php selected($signup->payment_details()->method(), 'direct_debit'); ?>><?php esc_html_e('Direct debit', 'football-club-manager'); ?></option>
                        <option value="no_payment" <?php selected($signup->payment_details()->method(), 'no_payment'); ?>><?php esc_html_e('No payment needed', 'football-club-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr data-method="direct_debit">
                <th><label for="<?php echo esc_attr($prefix . 'iban'); ?>"><?php esc_html_e('Bank account (IBAN)', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'iban'); ?>" name="<?php echo esc_attr($prefix . 'iban'); ?>" value="<?php echo esc_attr($signup->payment_details()->iban()); ?>"></td>
            </tr>
            <tr data-method="direct_debit">
                <th><label for="<?php echo esc_attr($prefix . 'account_holder_name'); ?>"><?php esc_html_e('Account holder name', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'account_holder_name'); ?>" name="<?php echo esc_attr($prefix . 'account_holder_name'); ?>" value="<?php echo esc_attr($signup->payment_details()->account_holder_name()); ?>"></td>
            </tr>
            <tr data-method="no_payment">
                <th><label for="<?php echo esc_attr($prefix . 'reason'); ?>"><?php esc_html_e('Reason', 'football-club-manager'); ?></label></th>
                <td><input type="text" id="<?php echo esc_attr($prefix . 'reason'); ?>" name="<?php echo esc_attr($prefix . 'reason'); ?>" value="<?php echo esc_attr($signup->payment_details()->reason()); ?>"></td>
            </tr>
        </tbody>
    </table>
<?php
}

// Save payment details meta box
function fcmanager_save_signup_payment_details_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_signup')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_signup_payment_details_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_signup_payment_details_meta_box', 'fcmanager_signup_payment_details_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    // Edit meta values
    $prefix = 'fcmanager_signup_payment_details_';
    $signup = new FCManager_Signup($post_id);
    if (array_key_exists($prefix . 'method', $_POST))
        $signup->payment_details()->method(sanitize_text_field(wp_unslash($_POST[$prefix . 'method'])));
    if (array_key_exists($prefix . 'iban', $_POST))
        $signup->payment_details()->iban(sanitize_text_field(wp_unslash($_POST[$prefix . 'iban'])));
    if (array_key_exists($prefix . 'account_holder_name', $_POST))
        $signup->payment_details()->account_holder_name(sanitize_text_field(wp_unslash($_POST[$prefix . 'account_holder_name'])));
    if (array_key_exists($prefix . 'reason', $_POST))
        $signup->payment_details()->reason(sanitize_text_field(wp_unslash($_POST[$prefix . 'reason'])));

    // Save
    remove_action('save_post_fcmanager_signup', 'fcmanager_save_signup_payment_details_meta_box');
    $signup->save();
}

add_action('save_post_fcmanager_signup', 'fcmanager_save_signup_payment_details_meta_box');

// Render additional information meta box
function fcmanager_render_signup_additional_information_meta_box($post)
{
    $extra_fields = FCManager_Settings::instance()->signup->extra_fields();
    if (empty($extra_fields)) {
        echo '<p>' . __('No extra fields defined. You can define extra fields in the settings page.', 'football-club-manager') . '</p>';
        return;
    }

    // Retrieve current meta values
    $signup = new FCManager_Signup($post);

    // Show form
    wp_nonce_field('fcmanager_save_signup_additional_information_meta_box', 'fcmanager_signup_additional_information_meta_box_nonce');
?>
    <table class="form-table">
        <tbody>
            <?php foreach ($extra_fields as $index => $field) : ?>
                <tr>
                    <th><label for="<?php echo esc_attr('fcmanager_signup_additional_information[' . $field . ']'); ?>"><?php echo esc_html($field); ?></label></th>
                    <td>
                        <input type="text" id="<?php echo esc_attr('fcmanager_signup_additional_information[' . $field . ']'); ?>" name="<?php echo esc_attr('fcmanager_signup_additional_information[' . $field . ']'); ?>" value="<?php echo esc_attr($signup->additional_information()[$field] ?? ''); ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
}

// Save additional information meta box
function fcmanager_save_signup_additional_information_meta_box($post_id)
{
    // Check post type
    if (get_post_type($post_id) !== 'fcmanager_signup')
        return;

    // Check nonce
    if (!array_key_exists('fcmanager_signup_additional_information_meta_box_nonce', $_POST) || !check_admin_referer('fcmanager_save_signup_additional_information_meta_box', 'fcmanager_signup_additional_information_meta_box_nonce'))
        return;

    // Check permissions
    if (! current_user_can('edit_post', $post_id))
        return;

    $extra_fields = FCManager_Settings::instance()->signup->extra_fields();
    if (empty($extra_fields)) {
        return;
    }

    // Edit meta values
    $signup = new FCManager_Signup($post_id);
    foreach ($extra_fields as $index => $field) {
        if (array_key_exists('fcmanager_signup_additional_information', $_POST) && array_key_exists($field, $_POST['fcmanager_signup_additional_information'])) {
            $signup->additional_information()[$field] = sanitize_text_field(wp_unslash($_POST['fcmanager_signup_additional_information'][$field]));
        }
    }

    // Save
    remove_action('save_post_fcmanager_signup', 'fcmanager_save_signup_additional_information_meta_box');
    $signup->save();
}

add_action('save_post_fcmanager_signup', 'fcmanager_save_signup_additional_information_meta_box');

// Add the custom columns to the signup post type:
function fcmanager_set_custom_edit_signup_columns($columns)
{
    unset($columns['title']);
    $columns['signup_first_name'] = __('First name', 'football-club-manager');
    $columns['signup_last_name'] = __('Last name', 'football-club-manager');

    return $columns;
}

add_filter('manage_fcmanager_signup_posts_columns', 'fcmanager_set_custom_edit_signup_columns');


// Add the data to the custom columns for the signup post type:
function fcmanager_custom_signup_column($column, $post_id)
{
    $signup = new FCManager_Signup($post_id);
    switch ($column) {
        case 'signup_first_name':
            echo esc_html($signup->personal_details()->first_name());
            break;

        case 'signup_last_name':
            echo esc_html($signup->personal_details()->full_last_name());
            break;
    }
}

add_action('manage_fcmanager_signup_posts_custom_column', 'fcmanager_custom_signup_column', 10, 2);

add_filter('post_date_column_status', function ($status, $post) {
    if ($post->post_type === 'fcmanager_signup') {
        return '';
    }
    return $status;
}, 10, 2);
