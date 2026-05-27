const { __ } = wp.i18n;

document.addEventListener('DOMContentLoaded', fcmanager_init_ui);

function fcmanager_init_ui() {
    const dropzone = document.getElementById('fcm-dropzone');
    const fileInput = document.getElementById('fcm-file-input');

    if (!dropzone || !fileInput) return;

    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        fcmanager_handle_file(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', e => {
        fcmanager_handle_file(e.target.files[0]);
    });
}

function fcmanager_set_selected_file(file) {
    window.fcmanager_selected_file = file;

    const selectedFileText = document.querySelector('.fcm-dropzone-selected-file');
    const instructions = document.querySelector('.fcm-dropzone-instructions');

    selectedFileText.textContent = file?.name;
    selectedFileText.style.display = file ? 'block' : 'none';
    instructions.style.display = file ? 'none' : 'block';
}

function fcmanager_handle_file(file) {
    if (!file) return;

    const mappingContainer = document.getElementById('fcm-mapping');

    mappingContainer.style.display = 'none';

    const extension = file.name.split('.').pop().toLowerCase();
    if (!window.FCMANAGER_IMPORT_DATA.supported_extensions.includes(extension)) {
        alert(__('File type not supported', 'football-club-manager'));
        fcmanager_set_selected_file(null);
        return;
    }

    fcmanager_set_selected_file(file);

    switch (extension) {
        case 'csv':
            fcmanager_parse_csv(file);
            break;
    }
}

function fcmanager_parse_csv(file) {
    Papa.parse(file, {
        header: false,
        preview: 10,
        skipEmptyLines: true,
        complete: function (results) {
            const rows = results.data;
            if (rows.length < 2) {
                alert(__('File is empty', 'football-club-manager'));
                fcmanager_set_selected_file(null);
                return;
            }

            const headers = rows[0];
            const samples = rows.slice(1);

            fcmanager_build_mapping_ui(headers, samples);
        }
    });
}

function fcmanager_build_mapping_ui(headers, samples) {
    const mappingContainer = document.getElementById('fcm-mapping');
    const mappingRows = document.getElementById('fcm-mapping-body');
    const mappingForm = document.getElementById('fcm-mapping-form');

    const template = document.getElementById('fcm-mapping-row-template');
    template.style.display = 'none';

    // Clear old rows
    [...mappingRows.querySelectorAll('tr')].forEach(tr => tr !== template && tr.remove());

    // Build rows
    FCMANAGER_IMPORT_DATA.fields.forEach(field => {
        const row = template.cloneNode(true);
        row.style.display = 'table-row';

        // Label
        const label = document.createElement('label');
        label.textContent = field.label;
        label.setAttribute('for', field.key);
        row.querySelector('[data-content="to_field"]').appendChild(label);

        // Select
        const select = document.createElement('select');
        select.id = field.key;
        select.name = field.key;

        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = '';
        select.appendChild(empty);

        headers.forEach((h, index) => {
            const opt = document.createElement('option');
            opt.value = index;
            opt.textContent = h;

            const example = samples.find(r => r[index] !== '')?.[index] ?? '';
            if (example) {
                opt.textContent += ` (${example})`;
                opt.dataset.example = example;
            }

            select.appendChild(opt);
        });

        row.querySelector('[data-content="from_field"]').appendChild(select);

        // Boolean extra field
        if (field.type === 'boolean') {
            const extraCell = row.querySelector('[data-content="extra"]');

            const boolLabel = document.createElement('label');
            boolLabel.textContent = __('Must equal value:', 'football-club-manager');
            boolLabel.style.visibility = 'hidden';

            const boolInput = document.createElement('input');
            boolInput.type = 'text';
            boolInput.name = field.key + '_true_value';

            boolLabel.appendChild(boolInput);
            extraCell.appendChild(boolLabel);

            select.addEventListener('change', () => {
                boolLabel.style.visibility = select.value ? 'visible' : 'hidden';
                boolInput.value = select.selectedOptions[0].dataset.example || 'true';
            });
        }

        mappingRows.appendChild(row);
    });

    // Submit handler
    mappingForm.addEventListener('submit', fcmanager_submit_import);

    mappingContainer.style.display = 'block';
}

async function fcmanager_submit_import(e) {
    e.preventDefault();
    const mappingForm = e.target;

    const mappingRules = fcmanager_collect_mapping_rules(mappingForm);

    fcmanager_toggle_import_ui(false);

    let uploadResult;
    try {
        uploadResult = await fcmanager_upload_import(mappingRules);

        if (!uploadResult) {
            throw new Error();
        }
    } catch (e) {
        fcmanager_update_progress(0, 0, 0, true);
        return;
    }

    fcmanager_start_processing(uploadResult.guid, uploadResult.total);
}

function fcmanager_collect_mapping_rules(form) {
    const rules = [];

    FCMANAGER_IMPORT_DATA.fields.forEach(field => {
        const from = form.querySelector(`[name="${field.key}"]`).value;
        const boolEquals = form.querySelector(`[name="${field.key}_true_value"]`)?.value || '';

        rules.push({
            from,
            to: field.key,
            bool_equals: boolEquals
        });
    });

    return rules;
}

function fcmanager_upload_import(mappingRules) {
    return new Promise((resolve, reject) => {

        const formData = new FormData();
        formData.append('action', 'fcmanager_import_upload');
        formData.append('fcmanager_nonce', document.querySelector('[name="fcmanager_nonce"]').value);
        formData.append('target_class', window.FCMANAGER_IMPORT_DATA.target_class);
        formData.append('file', window.fcmanager_selected_file);
        formData.append('mapping', JSON.stringify(mappingRules));

        const xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                fcmanager_update_progress((e.loaded / e.total), 0, 1);
            }
        });

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    fcmanager_update_progress(100, 0, 1);

                    if (response.success) {
                        resolve({
                            guid: response.data.guid,
                            total: response.data.total
                        });
                    } else {
                        reject(response);
                    }
                } catch (e) {
                    reject();
                }
            } else {
                reject(xhr.statusText);
            }
        };

        xhr.onerror = () => reject();
        xhr.send(formData);
    });
}

async function fcmanager_start_processing(guid, total) {
    let offset = 0;

    while (offset < total) {
        const result = await fcmanager_process_chunk(guid, offset);

        offset = result.next_offset;

        fcmanager_update_progress(100, offset, total);

        if (result.done) break;
    }
}

async function fcmanager_process_chunk(guid, offset) {
    const formData = new FormData();
    formData.append('action', 'fcmanager_import_process');
    formData.append('fcmanager_nonce', document.querySelector('[name="fcmanager_nonce"]').value);
    formData.append('guid', guid);
    formData.append('offset', offset);

    const response = await fetch(ajaxurl, {
        method: 'POST',
        body: formData
    }).then(r => r.json()).catch(e => fcmanager_update_progress(0, 0, 0, true));

    return response.data;
}

function fcmanager_update_progress(upload, done, total, failed) {
    document.getElementById('fcmanager-progress').style.display = 'block';

    const percentUpload = Math.round(upload * 0.2);
    const percentProcessed = Math.round((done / total) * 80);
    let percent = percentUpload + percentProcessed;

    const bar = document.querySelector('.fcmanager-progress-fill');
    const text = document.querySelector('.fcmanager-progress-text');

    if (failed) {
        percent = 100;
        bar.classList.add('fcmanager-progress-failed');
    }
    else
        bar.classList.remove('fcmanager-progress-failed');

    bar.style.width = percent + '%';
    text.textContent = percent + '%';

    if (percent >= 100 || failed) {
        if (failed)
            text.textContent = __('Import failed!', 'football-club-manager');
        else
            text.textContent = __('Import complete!', 'football-club-manager');
        fcmanager_toggle_import_ui(true);
    }
}

function fcmanager_toggle_import_ui(enabled) {
    document.querySelectorAll('.fcm-import button, select, input').forEach(el => el.disabled = !enabled);
}