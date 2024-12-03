$(document).ready(function() {
    $('#department_id').change(function() {
        var departmentId = $(this).val();
        if (departmentId) {
            $.ajax({
                url: 'fetch_courses.php', // URL to fetch_courses.php
                method: 'GET',
                data: { department_id: departmentId },
                success: function(data) {
                    $('#course_id').html(data); // Populate the course dropdown
                }
            });
        } else {
            $('#course_id').html('<option value="">Select a course</option>');
        }
    });
});

$(document).ready(function() {
    $('#course_id').change(function() {
        var courseId = $(this).val();
        if (courseId) {
            $.ajax({
                url: 'fetch_sections.php',
                method: 'GET',
                data: { course_id: courseId },
                success: function(data) {
                    $('#section_id').html(data);
                }
            });
        } else {
            $('#section_id').html('<option value="">Select a section</option>');
        }
    });
});