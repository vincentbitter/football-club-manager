<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Signup
{
    /** @var int */
    private $id;

    /** @var string */
    private $type;
    /** @var string */
    private $subtype;

    /** @var FCManager_Signup_Personal_Details */
    private $personal_details;

    /** @var FCManager_Signup_Parent */
    private $parent1;

    /** @var FCManager_Signup_Parent */
    private $parent2;

    /** @var FCManager_Signup_Payment_Details */
    private $payment_details;

    /** @var FCManager_Signup_Additional_Information */
    private $additional_information;

    public function __construct($id_or_post = null)
    {
        $this->set_id($id_or_post);
        $this->type = get_post_meta($this->id, '_fcmanager_signup_type', true) ?: 'player';
        $this->subtype = get_post_meta($this->id, '_fcmanager_signup_subtype', true) ?: '';

        $this->personal_details = new FCManager_Signup_Personal_Details($this->id);
        $this->parent1 = new FCManager_Signup_Parent($this->id, 1);
        $this->parent2 = new FCManager_Signup_Parent($this->id, 2);
        $this->payment_details = new FCManager_Signup_Payment_Details($this->id);
        $this->additional_information = new FCManager_Signup_Additional_Information($this->id);
    }

    private function set_id($id_or_post)
    {
        if ($id_or_post === null || ($id_or_post instanceof WP_Post && $id_or_post->post_status === 'auto-draft')) {
            $this->id = $id_or_post;
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
        } else {
            throw new InvalidArgumentException('Expected an integer ID or a WP_Post object.');
        }
    }

    public function type($new_value = null)
    {
        if ($new_value !== null && in_array($new_value, ['player', 'volunteer'], true)) {
            $this->type = $new_value;
        }

        return $this->type;
    }

    public function subtype($new_value = null)
    {
        if ($new_value !== null) {
            $this->subtype = sanitize_text_field($new_value);
        }

        return $this->subtype;
    }

    public function personal_details($post = null)
    {
        if ($post) {
            $this->personal_details->from_post_data($post);
            return $this->personal_details->validate();
        }
        return $this->personal_details;
    }

    public function parent1($post = null)
    {
        if ($post) {
            $this->parent1->from_post_data($post);
            return $this->parent1->validate();
        }
        return $this->parent1;
    }

    public function parent2($post = null)
    {
        if ($post) {
            $this->parent2->from_post_data($post);
            return $this->parent2->validate(true);
        }
        return $this->parent2;
    }

    public function payment_details($post = null, $allowed_methods = null)
    {
        if ($post) {
            $this->payment_details->from_post_data($post, $allowed_methods);
            return $this->payment_details->validate();
        }
        return $this->payment_details;
    }

    public function additional_information($post = null)
    {
        if ($post) {
            $this->additional_information->from_post_data($post);
            return true;
        }
        return $this->additional_information;
    }

    public function save()
    {
        if (!$this->id) {
            // Create new post
            $this->id = wp_insert_post([
                'post_type' => 'fcmanager_signup',
                'post_status' => 'publish',
            ]);
        }

        update_post_meta($this->id, '_fcmanager_signup_type', $this->type);
        update_post_meta($this->id, '_fcmanager_signup_subtype', $this->subtype);
        $this->personal_details->save($this->id);
        $this->parent1->save($this->id);
        $this->parent2->save($this->id);
        $this->payment_details->save($this->id);
        $this->additional_information->save($this->id);
    }
}

class FCManager_Signup_Personal_Details
{
    private $first_name;
    private $initials;
    private $middle_name;
    private $last_name;

    private $date_of_birth;
    private $gender;
    private $nationality;

    private $street;
    private $house_number;
    private $house_number_addition;
    private $postal_code;
    private $city;
    private $country;

    private $mobile_phone_number;
    private $phone_number;
    private $emergency_contact_number;
    private $email_address;

    public function __construct($id = null)
    {
        $this->first_name = get_post_meta($id, '_fcmanager_signup_personal_details_first_name', true);
        $this->initials = get_post_meta($id, '_fcmanager_signup_personal_details_initials', true);
        $this->middle_name = get_post_meta($id, '_fcmanager_signup_personal_details_middle_name', true);
        $this->last_name = get_post_meta($id, '_fcmanager_signup_personal_details_last_name', true);

        $date_of_birth = get_post_meta($id, '_fcmanager_signup_personal_details_date_of_birth', true);
        $this->date_of_birth = $date_of_birth ? new DateTime($date_of_birth) : null;

        $this->gender = get_post_meta($id, '_fcmanager_signup_personal_details_gender', true);
        $this->nationality = get_post_meta($id, '_fcmanager_signup_personal_details_nationality', true);

        $this->street = get_post_meta($id, '_fcmanager_signup_personal_details_street', true);
        $this->house_number = get_post_meta($id, '_fcmanager_signup_personal_details_house_number', true);
        $this->house_number_addition = get_post_meta($id, '_fcmanager_signup_personal_details_house_number_addition', true);
        $this->postal_code = get_post_meta($id, '_fcmanager_signup_personal_details_postal_code', true);
        $this->city = get_post_meta($id, '_fcmanager_signup_personal_details_city', true);
        $this->country = get_post_meta($id, '_fcmanager_signup_personal_details_country', true);

        $this->mobile_phone_number = get_post_meta($id, '_fcmanager_signup_personal_details_mobile_phone_number', true);
        $this->phone_number = get_post_meta($id, '_fcmanager_signup_personal_details_phone_number', true);
        $this->emergency_contact_number = get_post_meta($id, '_fcmanager_signup_personal_details_emergency_contact_number', true);
        $this->email_address = get_post_meta($id, '_fcmanager_signup_personal_details_email_address', true);
    }

    public function from_post_data($post)
    {
        $this->first_name = sanitize_text_field($post['first_name'] ?? '');
        $this->initials = sanitize_text_field($post['initials'] ?? '');
        $this->middle_name = sanitize_text_field($post['middle_name'] ?? '');
        $this->last_name = sanitize_text_field($post['last_name'] ?? '');

        $this->date_of_birth = isset($post['date_of_birth']) ? new DateTime($post['date_of_birth']) : null;
        $this->gender = sanitize_text_field($post['gender'] ?? '');
        $this->nationality = sanitize_text_field($post['nationality'] ?? '');

        $this->street = sanitize_text_field($post['street'] ?? '');
        $this->house_number = sanitize_text_field($post['house_number'] ?? '');
        $this->house_number_addition = sanitize_text_field($post['house_number_addition'] ?? '');
        $this->postal_code = sanitize_text_field($post['postal_code'] ?? '');
        $this->city = sanitize_text_field($post['city'] ?? '');
        $this->country = sanitize_text_field($post['country'] ?? '');

        $this->mobile_phone_number = sanitize_text_field($post['mobile_phone'] ?? '');
        $this->phone_number = sanitize_text_field($post['phone'] ?? '');
        $this->emergency_contact_number = sanitize_text_field($post['emergency_contact_number'] ?? '');
        $this->email_address = sanitize_email($post['email'] ?? '');
    }

    public function validate()
    {
        if (
            empty($this->first_name)
            || empty($this->initials)
            || empty($this->last_name)
            || empty($this->gender)
            || empty($this->nationality)
            || empty($this->date_of_birth)
            || empty($this->street)
            || empty($this->house_number)
            || empty($this->postal_code)
            || empty($this->city)
            || empty($this->email_address)
        ) {
            return false;
        }

        if ($this->date_of_birth > new DateTime()) {
            return false;
        }

        if (!in_array($this->gender, ['male', 'female', 'gender neutral'])) {
            return false;
        }

        if (!is_email($this->email_address)) {
            return false;
        }

        if ($this->mobile_phone_number && !preg_match('/^\+?[0-9\s\-]+$/', $this->mobile_phone_number)) {
            return false;
        }

        if ($this->phone_number && !preg_match('/^\+?[0-9\s\-]+$/', $this->phone_number)) {
            return false;
        }

        if ($this->emergency_contact_number && !preg_match('/^\+?[0-9\s\-]+$/', $this->emergency_contact_number)) {
            return false;
        }

        return true;
    }

    public function save($signup_id)
    {
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_first_name', $this->first_name);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_initials', $this->initials);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_middle_name', $this->middle_name);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_last_name', $this->last_name);

        update_post_meta($signup_id, '_fcmanager_signup_personal_details_date_of_birth', $this->date_of_birth ? $this->date_of_birth->format('Y-m-d') : '');
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_gender', $this->gender);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_nationality', $this->nationality);

        update_post_meta($signup_id, '_fcmanager_signup_personal_details_street', $this->street);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_house_number', $this->house_number);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_house_number_addition', $this->house_number_addition);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_postal_code', $this->postal_code);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_city', $this->city);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_country', $this->country);

        update_post_meta($signup_id, '_fcmanager_signup_personal_details_mobile_phone_number', $this->mobile_phone_number);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_phone_number', $this->phone_number);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_emergency_contact_number', $this->emergency_contact_number);
        update_post_meta($signup_id, '_fcmanager_signup_personal_details_email_address', $this->email_address);

        $this->save_title($signup_id);
    }

    protected function save_title($signup_id)
    {
        wp_update_post([
            'ID' => $signup_id,
            'post_title' => $this->name(),
            'post_name' => sanitize_title($this->name())
        ]);
    }

    public function initials($new_value = null)
    {
        if ($new_value !== null) {
            $this->initials = $new_value;
        }
        return $this->initials;
    }

    public function first_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->first_name = $new_value;
        }
        return $this->first_name;
    }

    public function middle_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->middle_name = $new_value;
        }
        return $this->middle_name;
    }

    public function last_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->last_name = $new_value;
        }
        return $this->last_name;
    }

    public function full_last_name()
    {
        return ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

    public function name()
    {
        return $this->initials . ' ' . $this->full_last_name();
    }

    public function date_of_birth($new_value = null)
    {
        if ($new_value !== null) {
            if ($new_value instanceof DateTime) {
                $this->date_of_birth = $new_value;
            } else {
                throw new InvalidArgumentException('Expected a DateTime object.');
            }
        }

        return $this->date_of_birth;
    }

    public function age()
    {
        if (! $this->date_of_birth) {
            return null;
        }

        $today = new DateTime();
        $age = $today->diff($this->date_of_birth)->y;

        return $age;
    }

    public function gender($new_value = null)
    {
        if ($new_value !== null && in_array($new_value, ['male', 'female', 'gender neutral'])) {
            $this->gender = $new_value;
        }
        return $this->gender;
    }

    public function nationality($new_value = null)
    {
        if ($new_value !== null) {
            $this->nationality = $new_value;
        }
        return $this->nationality;
    }

    public function street($new_value = null)
    {
        if ($new_value !== null) {
            $this->street = $new_value;
        }
        return $this->street;
    }

    public function house_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->house_number = $new_value;
        }
        return $this->house_number;
    }

    public function house_number_addition($new_value = null)
    {
        if ($new_value !== null) {
            $this->house_number_addition = $new_value;
        }
        return $this->house_number_addition;
    }

    public function postal_code($new_value = null)
    {
        if ($new_value !== null) {
            $this->postal_code = $new_value;
        }
        return $this->postal_code;
    }

    public function city($new_value = null)
    {
        if ($new_value !== null) {
            $this->city = $new_value;
        }
        return $this->city;
    }

    public function country($new_value = null)
    {
        if ($new_value !== null) {
            $this->country = $new_value;
        }
        return $this->country;
    }

    public function mobile_phone_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->mobile_phone_number = $new_value;
        }
        return $this->mobile_phone_number;
    }

    public function phone_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->phone_number = $new_value;
        }
        return $this->phone_number;
    }

    public function emergency_contact_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->emergency_contact_number = $new_value;
        }
        return $this->emergency_contact_number;
    }

    public function email_address($new_value = null)
    {
        if ($new_value !== null) {
            $this->email_address = $new_value;
        }
        return $this->email_address;
    }
}

class FCManager_Signup_Parent
{
    private $position;
    private $key_prefix;

    private $first_name;
    private $middle_name;
    private $last_name;

    private $mobile_phone_number;
    private $phone_number;
    private $email_address;

    public function __construct($id = null, $position = 1)
    {
        $this->position = $position;
        $this->key_prefix = '_fcmanager_signup_parent_' . $position . '_';

        $this->first_name = get_post_meta($id, $this->key_prefix . 'first_name', true);
        $this->middle_name = get_post_meta($id, $this->key_prefix . 'middle_name', true);
        $this->last_name = get_post_meta($id, $this->key_prefix . 'last_name', true);

        $this->mobile_phone_number = get_post_meta($id, $this->key_prefix . 'mobile_phone_number', true);
        $this->phone_number = get_post_meta($id, $this->key_prefix . 'phone_number', true);
        $this->email_address = get_post_meta($id, $this->key_prefix . 'email_address', true);
    }

    public function save($signup_id)
    {
        update_post_meta($signup_id, $this->key_prefix . 'first_name', $this->first_name);
        update_post_meta($signup_id, $this->key_prefix . 'middle_name', $this->middle_name);
        update_post_meta($signup_id, $this->key_prefix . 'last_name', $this->last_name);

        update_post_meta($signup_id, $this->key_prefix . 'mobile_phone_number', $this->mobile_phone_number);
        update_post_meta($signup_id, $this->key_prefix . 'phone_number', $this->phone_number);
        update_post_meta($signup_id, $this->key_prefix . 'email_address', $this->email_address);
    }

    public function from_post_data($post)
    {
        $prefix = 'parent' . $this->position . '_';
        $this->first_name = sanitize_text_field($post[$prefix . 'first_name'] ?? '');
        $this->middle_name = sanitize_text_field($post[$prefix . 'middle_name'] ?? '');
        $this->last_name = sanitize_text_field($post[$prefix . 'last_name'] ?? '');

        $this->mobile_phone_number = sanitize_text_field($post[$prefix . 'mobile_phone'] ?? '');
        $this->phone_number = sanitize_text_field($post[$prefix . 'phone'] ?? '');
        $this->email_address = sanitize_email($post[$prefix . 'email'] ?? '');
    }

    public function validate($optional = false)
    {
        if (!$optional && (empty($this->first_name) || empty($this->last_name) || empty($this->email_address))) {
            return false;
        }

        if ($this->email_address && !is_email($this->email_address)) {
            return false;
        }

        if ($this->mobile_phone_number && !preg_match('/^\+?[0-9\s\-]+$/', $this->mobile_phone_number)) {
            return false;
        }

        if ($this->phone_number && !preg_match('/^\+?[0-9\s\-]+$/', $this->phone_number)) {
            return false;
        }

        return true;
    }

    public function first_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->first_name = $new_value;
        }
        return $this->first_name;
    }

    public function middle_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->middle_name = $new_value;
        }
        return $this->middle_name;
    }

    public function last_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->last_name = $new_value;
        }
        return $this->last_name;
    }

    public function full_last_name()
    {
        return ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

    public function name()
    {
        return $this->first_name() . ' ' . $this->full_last_name();
    }

    public function mobile_phone_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->mobile_phone_number = $new_value;
        }
        return $this->mobile_phone_number;
    }

    public function phone_number($new_value = null)
    {
        if ($new_value !== null) {
            $this->phone_number = $new_value;
        }
        return $this->phone_number;
    }

    public function email_address($new_value = null)
    {
        if ($new_value !== null) {
            $this->email_address = $new_value;
        }
        return $this->email_address;
    }
}


class FCManager_Signup_Payment_Details
{
    private $method;
    private $reason;
    private $iban;
    private $account_holder_name;

    public function __construct($id = null)
    {
        $this->method = get_post_meta($id, '_fcmanager_signup_payment_details_method', true);
        $this->reason = get_post_meta($id, '_fcmanager_signup_payment_details_reason', true);
        $this->iban = get_post_meta($id, '_fcmanager_signup_payment_details_iban', true);
        $this->account_holder_name = get_post_meta($id, '_fcmanager_signup_payment_details_account_holder_name', true);
    }

    public function save($signup_id)
    {
        update_post_meta($signup_id, '_fcmanager_signup_payment_details_method', $this->method);
        update_post_meta($signup_id, '_fcmanager_signup_payment_details_reason', $this->reason);
        update_post_meta($signup_id, '_fcmanager_signup_payment_details_iban', $this->iban);
        update_post_meta($signup_id, '_fcmanager_signup_payment_details_account_holder_name', $this->account_holder_name);
    }

    public function method($new_value = null)
    {
        if ($new_value !== null) {
            $this->method = $new_value;
        }
        return $this->method;
    }

    public function reason($new_value = null)
    {
        if ($new_value !== null) {
            $this->reason = $new_value;
        }
        return $this->reason;
    }

    public function iban($new_value = null)
    {
        if ($new_value !== null) {
            $this->iban = $new_value;
        }
        return $this->iban;
    }

    public function account_holder_name($new_value = null)
    {
        if ($new_value !== null) {
            $this->account_holder_name = $new_value;
        }
        return $this->account_holder_name;
    }

    public function from_post_data($post, $allowed_methods = null)
    {
        $this->method = (array_key_exists('method', $post) && (! $allowed_methods || in_array($post['method'], $allowed_methods))) ? sanitize_text_field($post['method']) : null;
        $this->iban = sanitize_text_field($post['iban'] ?? '');
        $this->account_holder_name = sanitize_text_field($post['account_holder_name'] ?? '');
        $this->reason = sanitize_text_field($post['reason'] ?? '');
    }

    public function validate()
    {
        if (empty($this->method)) {
            return false;
        }

        if ($this->method === 'direct_debit') {
            if (empty($this->iban) || empty($this->account_holder_name)) {
                return false;
            }
        } elseif ($this->method === 'no_payment') {
            if (empty($this->reason)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }
}

class FCManager_Signup_Additional_Information implements ArrayAccess
{
    private array $data = [];

    public function __construct($id = null)
    {
        $extra_fields_json = get_post_meta($id, '_fcmanager_signup_additional_information', true);
        $this->data = $extra_fields_json ? json_decode($extra_fields_json, true) : [];
    }

    public function save($signup_id)
    {
        update_post_meta($signup_id, '_fcmanager_signup_additional_information', json_encode($this->data));
    }

    public function offsetSet($offset, $value): void
    {
        $valid_keys = FCManager_Settings::instance()->signup->extra_fields();
        if ($offset === null) {
            throw new InvalidArgumentException('Offset cannot be null.');
        } else if (!in_array($offset, $valid_keys)) {
            throw new InvalidArgumentException('Invalid additional information key');
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset): string
    {
        return $this->data[$offset] ?? null;
    }

    public function from_post_data($post)
    {
        $extra_fields = FCManager_Settings::instance()->signup->extra_fields();
        if (empty($extra_fields) || !is_array($extra_fields) || !array_key_exists('fcmanager_signup_additional_information', $post)) {
            return;
        }

        foreach ($extra_fields as $field) {
            if (array_key_exists($field, $post['fcmanager_signup_additional_information'])) {
                $this->data[$field] = sanitize_text_field($post['fcmanager_signup_additional_information'][$field]);
            }
        }
    }
}
