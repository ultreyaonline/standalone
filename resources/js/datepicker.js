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
            }
        },
    });
}

if (endEl) {
    endPicker = flatpickr(endEl, {
        ...config,
        onChange: function(selectedDates) {
            if (startPicker && selectedDates[0]) {
                startPicker.set('maxDate', selectedDates[0]);
            }
        },
    });
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
