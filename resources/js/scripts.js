//scripts.js

// bind keyboard shortcuts
$('[data-shortcut]').each(function (index) {
    //key combo with spaces
    let combo = $(this).data('shortcut').split('').join(' ');

    // attach to matching anchors
    if ($(this).is('a')) {
        let target = $(this).attr('href');
        Mousetrap.bind(combo, function () {
            window.location = target;
        });
    }
});
// 's' or '/' for "search"
Mousetrap.bind(['s', '/', 's e a r c h'], function() {
    $('#searchfield').focus();
    return false; // to prevent propagation of char into search box
});


// flash auto hide
$(function () {
    $('#flash-msg .alert').not('.alert-danger, .alert-important').delay(6000).slideUp(500);
});
