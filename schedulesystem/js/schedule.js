$(document).ready(function() {
    $('.add-schedule').click(function() {
        var day = $(this).data('day');
        var newSchedule = $('#schedule-group-' + day + ' .schedule-entry:first').clone();
        $('#schedule-group-' + day).append(newSchedule);
    });
});

