<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_render_signup_form_payment_details_block($attributes, $content, $block)
{
    $allowedMethods = $attributes['allowedMethods'] ?? ['direct_debit', 'no_payment'];

    ob_start();
?>
    <div class="fcmanager-payment-details">
        <?php if (count($allowedMethods) === 1): ?>
            <input type="hidden" name="method" value="<?php echo esc_attr($allowedMethods[0]); ?>" />
        <?php endif; ?>

        <?php if (count($allowedMethods) > 1): ?>
            <div class="fcmanager-form-grid fcmanager-form-grid--full">
                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Payment method', 'football-club-manager'); ?>
                        <select name="method" class="fcmanager-payment-method-select" required>
                            <option value="">
                                <?php _e('Select…', 'football-club-manager'); ?>
                            </option>
                            <?php if (in_array('direct_debit', $allowedMethods)): ?>
                                <option value="direct_debit" <?php selected($_POST['method'] ?? '', 'direct_debit'); ?>>
                                    <?php _e('Direct debit', 'football-club-manager'); ?>
                                </option>
                            <?php endif; ?>
                            <?php if (in_array('no_payment', $allowedMethods)): ?>
                                <option value="no_payment" <?php selected($_POST['method'] ?? '', 'no_payment'); ?>>
                                    <?php _e('No payment needed', 'football-club-manager'); ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('direct_debit', $allowedMethods)): ?>
            <div class="fcmanager-form-grid fcmanager-form-grid--double" data-payment-method="direct_debit">
                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Bank account (IBAN)', 'football-club-manager'); ?>
                        <input type="text" name="iban" value="<?php echo esc_attr($_POST['iban'] ?? ''); ?>" required />
                    </label>
                </div>

                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Account holder name', 'football-club-manager'); ?>
                        <input type="text" name="account_holder_name" value="<?php echo esc_attr($_POST['account_holder_name'] ?? ''); ?>" required />
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('no_payment', $allowedMethods)): ?>
            <div class="fcmanager-form-grid fcmanager-form-grid--full" data-payment-method="no_payment">
                <div class="fcmanager-form-field">
                    <label>
                        <?php _e('Reason', 'football-club-manager'); ?>
                        <input type="text" name="reason" value="<?php echo esc_attr($_POST['reason'] ?? ''); ?>" required />
                    </label>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php

    return ob_get_clean();
}

register_block_type(__DIR__, [
    'render_callback' => 'fcmanager_render_signup_form_payment_details_block',
]);
