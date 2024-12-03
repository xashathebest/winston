<?php
session_start();

if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'staff' || $_SESSION['user_role'] === 'student')) {
    header('Location: ../admin_staff/dashboard.php');
    exit;
}

require_once '../classes/database.class.php';
require_once '../classes/user.class.php';

// Database connection
$db = new Database();
$userModel = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Attempt to log in
    $user = $userModel->login($email, $password);

    if ($user) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header('Location: ../admin_staff/dashboard.php');
        } elseif ($user['role'] === 'staff') {
            header('Location: ../admin_staff/dashboard.php');
        } elseif ($user['role'] === 'student') {
            header('Location: ../user/student.php');
        } else {
            header('Location: login.php'); // Fallback in case of an unknown role
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="./another.css" rel="stylesheet" >
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<div class="flex justify-center items-center mt-24">
    <div class="mr-8">
        <!-- Adjust the size of the image using Tailwind classes -->
        <img src="../FRONTENDPHP/login.png" class="photologin h-auto">
    </div>
    <div class="w-1/2">
        <h2 class="text-center text-4xl font-semibold">Login</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-red-500"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control p-2 border rounded w-full" id="email" name="email" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control p-2 border rounded w-full" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-full py-2 darkblue text-white rounded">Login</button>
        </form>
    </div>
</div>




</body>
</html>
