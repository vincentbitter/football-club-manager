document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fcmanager-notification-signup').forEach(fcmanager_notification_signup_init);
});

function fcmanager_notification_signup_init(container) {
    const select = container.querySelector('select');
    const checkbox = container.querySelector('.fcmanager-notification-signup-include-data');

    select.addEventListener('change', () => fcmanager_notification_signup_toggle_include_data_field(checkbox, select));
    fcmanager_notification_signup_toggle_include_data_field(checkbox, select);
}

function fcmanager_notification_signup_toggle_include_data_field(checkbox, select) {
    checkbox.style.display = (select.value === 'immediately' ? 'block' : 'none');
}