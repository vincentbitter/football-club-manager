jQuery(document).ready(function () {
    jQuery('[data-payment-method] input[required]').attr('was-required', true);

    function togglePaymentDetails(select) {
        const method = jQuery(select).val();
        const container = jQuery(select).closest('form, .postbox');
        container.find('[data-payment-method]').hide();
        container.find('[data-payment-method] input[required]').prop('required', false);
        container.find('[data-payment-method="' + method + '"]').show();
        container.find('[data-payment-method="' + method + '"] input[was-required]').prop('required', true);
    }

    jQuery(document).on('change', '.fcmanager-payment-method-select', function () {
        togglePaymentDetails(this);
    });

    // Initial toggle on page load
    jQuery('.fcmanager-payment-method-select').each(function () {
        togglePaymentDetails(this);
    });
});