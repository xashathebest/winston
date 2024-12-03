<?php
session_start();
require_once '../classes/database.class.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header('Location: ../accounts/login.php');
    exit;
}

// Database connection
$db = new Database(); // Create an instance of Database class
$conn = $db->getConnection();

// Initialize variables
$currentPasswordErr = $newPasswordErr = $confirmPasswordErr = "";
$successMessage = "";
$errorMessage = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Assume the user ID is stored in the session
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM user WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        $errorMessage = "User not found.";
    } else {
        // Validate current password
        if (!password_verify($currentPassword, $user['password'])) {
            $currentPasswordErr = "Current password is incorrect.";
        }

        // Validate new password
        if (empty($newPassword)) {
            $newPasswordErr = "New password is required.";
        } elseif (strlen($newPassword) < 6) {
            $newPasswordErr = "Password must be at least 6 characters long.";
        }

        // Validate confirm password
        if ($newPassword !== $confirmPassword) {
            $confirmPasswordErr = "Passwords do not match.";
        }

        // If no errors, update the password
        if (empty($currentPasswordErr) && empty($newPasswordErr) && empty($confirmPasswordErr)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateStmt = $conn->prepare("UPDATE user SET password = :password WHERE id = :id");
            $updateStmt->execute([
                'password' => $hashedPassword,
                'id' => $userId,
            ]);

            $successMessage = "Password updated successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit Password</h2>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif (!empty($errorMessage)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
            <?php if (!empty($currentPasswordErr)): ?>
                <div class="text-danger"><?= htmlspecialchars($currentPasswordErr) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            <?php if (!empty($newPasswordErr)): ?>
                <div class="text-danger"><?= htmlspecialchars($newPasswordErr) ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            <?php if (!empty($confirmPasswordErr)): ?>
                <div class="text-danger"><?= htmlspecialchars($confirmPasswordErr) ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
        <a href="student.php" class="btn btn-secondary">Back to Schedule</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
