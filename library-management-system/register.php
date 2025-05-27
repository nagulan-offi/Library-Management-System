<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validate input
    if (empty($username) || empty($password) || empty($name) || empty($email) || !in_array($role, ['user', 'librarian'])) {
        header('Location: index.php?error=' . urlencode('All fields are required and role must be valid'));
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=' . urlencode('Invalid email format'));
        exit;
    }

    try {
        // Check for duplicate username or email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            header('Location: index.php?error=' . urlencode('Username or email already exists'));
            exit;
        }

        // Insert new user with plain-text password
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $role, $name, $email]);

        header('Location: index.php?message=' . urlencode('Registration successful! Please log in.'));
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?error=' . urlencode('Database error: ' . $e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php?error=' . urlencode('Invalid request'));
    exit;
}
?>