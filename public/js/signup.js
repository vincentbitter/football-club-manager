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

jQuery(document).ready(function () {
    jQuery('.fcmanager-parent-details-data input[required]').attr('was-required', true);

    function set_age(input) {
        input = jQuery(input);
        const age_field_container = input.parents('form').find('.fcmanager-personal-details-age-container');
        const age_field = input.parents('form').find('.fcmanager-personal-details-age');
        const parents_fields = input.parents('form').find('.fcmanager-parent-details-data');
        const parents_meta_box = input.parents('form').find('.fcmanager-parent-details-meta-box');

        const dateOfBirth = new Date(input.val());
        if (isNaN(dateOfBirth.getTime())) {
            age_field.html('');
            age_field_container.addClass('hidden');
            parents_meta_box.toggleClass('closed', true);
            parents_fields.hide();
            parents_fields.find('input[required]').prop('required', false);

            return;
        }
        const today = new Date();
        let age = today.getFullYear() - dateOfBirth.getFullYear();
        const m = today.getMonth() - dateOfBirth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dateOfBirth.getDate())) {
            age--;
        }

        age_field.html(age);
        age_field_container.removeClass('hidden');

        const requireParentsTillAge = input.parents('[data-require-parents-till-age]').data('require-parents-till-age');
        if (requireParentsTillAge) {
            parents_meta_box.toggleClass('closed', age >= requireParentsTillAge);
            parents_fields.toggle(age < requireParentsTillAge);
            parents_fields.find('input[was-required]').prop('required', age < requireParentsTillAge);

        }
    }

    const date_of_birth_inputs = jQuery('.fcmanager-personal-details-date-of-birth');
    date_of_birth_inputs.change(function () { set_age(this); });
    date_of_birth_inputs.each(function () {
        set_age(this);
    });
});