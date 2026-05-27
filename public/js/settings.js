document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fcmanager-structured-list').forEach(fcmanager_settings_structured_list_init);
});

function createStructuredListTable() {

}

function fcmanager_settings_structured_list_init(container) {
    const hiddenInput = container.querySelector('input[type="hidden"]');
    let rows = JSON.parse(hiddenInput.value || '[]');

    const table = document.createElement('table');
    table.className = 'fcmanager-structured-list__table widefat';

    const tbody = table.createTBody();
    container.insertBefore(table, hiddenInput);

    const columns = JSON.parse(container.dataset.columns);
    rows.forEach(row => fcmanager_settings_add_row(tbody, columns, row, hiddenInput));
    fcmanager_settings_create_add_button(container)
        .addEventListener('click', () => fcmanager_settings_add_row(tbody, columns, null, hiddenInput));
}

function fcmanager_settings_add_row(tbody, columns, data, hiddenInput) {
    const tr = tbody.insertRow();
    columns.forEach(col =>
        fcmanager_settings_create_column_in_row(tr, col.options, data ? data[col.key] : '')
            .addEventListener('change', () => fcmanager_settings_sync_to_hidden(columns, hiddenInput, tbody))
    );
    fcmanager_settings_create_remove_button_for_row(tr).addEventListener('click', () => {
        tr.remove();
        fcmanager_settings_sync_to_hidden(columns, hiddenInput, tbody);
    });
    fcmanager_settings_sync_to_hidden(columns, hiddenInput, tbody);
}

function fcmanager_settings_create_column_in_row(tr, options, data) {
    const td = tr.insertCell();
    let input;

    if (options) {
        input = document.createElement('select');
        Object.entries(options).forEach(([val, label]) => {
            const option = new Option(label, val);
            if (data === val) option.selected = true;
            input.appendChild(option);
        });
    } else {
        input = document.createElement('input');
        input.type = 'text';
        input.value = data;
    }

    td.appendChild(input);

    return input;
}

function fcmanager_settings_create_remove_button_for_row(tr) {
    const removeTd = tr.insertCell();
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'button-link fcmanager-structured-list__remove';
    removeBtn.textContent = '✕';
    removeTd.appendChild(removeBtn);

    return removeBtn;
}

function fcmanager_settings_create_add_button(container) {
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'button fcmanager-structured-list__add';
    addButton.textContent = container.dataset.addLabel || '+ ' + __('Add Row', 'football-club-manager');
    const descriptionNode = container.querySelector('p.description');
    if (descriptionNode) {
        container.insertBefore(addButton, descriptionNode);
    } else {
        container.appendChild(addButton);
    }
    return addButton;
}

function fcmanager_settings_sync_to_hidden(columns, hiddenInput, tbody) {
    const data = Array.from(tbody.rows).map(tr => {
        const row = {};
        columns.forEach((col, i) => {
            row[col.key] = tr.cells[i].querySelector('input, select').value;
        });
        return row;
    });
    hiddenInput.value = JSON.stringify(data);
}