<div id="sidebar">
<?php
    if ($_SESSION['user_role'] === 'admin') {
        ?>
        <h3 class="text-center">Admin Panel</h3>
    <?php
        } else {
            ?>
        <h3 class="text-center">Staff Panel</h3>     
        <?php 
        }
    ?>

    <a href="dashboard.php">Dashboard</a>
    <a href="admin.php">Manage Users</a>
    <a href="courses.php">Manage Courses</a>
    <a href="departments.php">Manage Departments</a>
    <a href="sections.php">Manage Sections</a>
    <a href="schedules.php">Manage Schedules</a>
    <a href="logout.php">Logout</a>
</div>