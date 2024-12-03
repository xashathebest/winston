function fetchCourses(departmentId) {
    if (departmentId) {
        $.ajax({
            url: 'get_courses.php',
            type: 'GET',
            data: { department_id: departmentId },
            success: function(data) {
                var courses = JSON.parse(data);
                var courseSelect = $('#course_id');
                courseSelect.empty(); // Clear previous options
                courseSelect.append('<option value="">--Select Course--</option>'); // Default option

                // Append courses to the select dropdown
                courses.forEach(function(course) {
                    courseSelect.append('<option value="' + course.id + '">' + course.course_description + '</option>');
                });
            }
        });
    } else {
        $('#course_id').empty().append('<option value="">--Select Course--</option>');
    }
}


function confirmDelete(sectionId) {
    if (confirm("Are you sure you want to delete this section?")) {
        // If confirmed, redirect to delete section page
        window.location.href = "delete_section.php?id=" + sectionId;
    }
}