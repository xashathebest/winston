window.addEventListener('DOMContentLoaded', function() {
    // Bar chart for User Counts
    var ctx = document.getElementById('userCountsChart').getContext('2d');
    var userCountsChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Staff', 'Admin', 'Student'],
            datasets: [{
                label: 'User Counts',
                data: [
                    document.getElementById('staffCount').value,
                    document.getElementById('adminCount').value,
                    document.getElementById('studentCount').value
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                borderColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pie chart for Sections
    var ctx = document.getElementById('sectionsChart').getContext('2d');
    var sectionsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Sections'],
            datasets: [{
                data: [document.getElementById('sectionCount').value],
                backgroundColor: ['#f6c23e'],
                borderColor: ['#f6c23e'],
                borderWidth: 1
            }]
        }
    });

    // Pie chart for Departments
    var ctx = document.getElementById('departmentsChart').getContext('2d');
    var departmentsChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Total Departments'],
            datasets: [{
                data: [document.getElementById('departmentCount').value],
                backgroundColor: ['#e74a3b'],
                borderColor: ['#e74a3b'],
                borderWidth: 1
            }]
        }
    });

    // Pie chart for Courses
    var ctx = document.getElementById('coursesChart').getContext('2d');
    var coursesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Total Courses'],
            datasets: [{
                data: [document.getElementById('courseCount').value],
                backgroundColor: ['#36b9cc'],
                borderColor: ['#36b9cc'],
                borderWidth: 1
            }]
        }
    });
});
