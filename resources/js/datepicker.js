import flatpickr from 'flatpickr';

const config = {
    enableTime: true,
    time_24hr: true,
    dateFormat: 'Y-m-d H:i:S',   // outputs MySQL-ready format directly
    defaultSeconds: 0,
    allowInput: true,
    disableTime: function(timeObj) {
        return timeObj.getHours() >= 1 && timeObj.getHours() <= 6;
    },
};

// Start/end linked pickers (used on both events and weekend forms)
const startEl = document.getElementById('input_start_datetime')
    || document.getElementById('input_start_date');
const endEl   = document.getElementById('input_end_datetime')
    || document.getElementById('input_end_date');

let startPicker, endPicker;

if (startEl) {
    startPicker = flatpickr(startEl, {
        ...config,
        onChange: function(selectedDates) {
            if (endPicker && selectedDates[0]) {
                endPicker.set('minDate', selectedDates[0]);
                if (endPicker.selectedDates[0] && endPicker.selectedDates[0] < selectedDates[0]) {
                    showDateError('End date/time must be after the start date/time.');
                } else {
                    clearDateError();
                }
            }
        },
    });
}

if (endEl) {
    endPicker = flatpickr(endEl, {
        ...config,
        onChange: function(selectedDates) {
            if (startPicker && selectedDates[0]) {
                if (selectedDates[0] < startPicker.selectedDates[0]) {
                    showDateError('End date/time must be after the start date/time.');
                } else {
                    clearDateError();
                }
            }
        },
    });
}

// Apply constraints from existing values on page load
if (startPicker && endPicker) {
    const existingStart = startPicker.selectedDates[0];

    // Only constrain end to be after start, not enforcing the other way around
    if (existingStart) endPicker.set('minDate', existingStart);
}

// Standalone pickers (weekend form only — no cross-field constraints)
[
    'input_candidate_arrival_time',
    'input_sendoff_start_time',
    'input_serenade_arrival_time',
    'input_closing_arrival_time',
    'input_closing_scheduled_start_time',
].forEach(id => {
    const el = document.getElementById(id);
    if (el) flatpickr(el, config);
});

function showDateError(message) {
    const el = document.getElementById('date-validation-message');
    if (el) {
        el.textContent = message;
        el.style.display = 'block';
    }
}

function clearDateError() {
    const el = document.getElementById('date-validation-message');
    if (el) {
        el.textContent = '';
        el.style.display = 'none';
    }
}

const form = startEl ? startEl.closest('form') : null;
if (form) {
    form.addEventListener('submit', function(e) {
        if (startPicker.selectedDates[0] && endPicker.selectedDates[0] &&
            endPicker.selectedDates[0] < startPicker.selectedDates[0]) {
            e.preventDefault();
            showDateError('Please correct the end date/time before saving — it must be after the start date/time.');
            endEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}
