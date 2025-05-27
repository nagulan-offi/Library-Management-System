<?php
include '../includes/session_check.php';
include '../config/db.php';

$book_id = $_GET['book_id'];
$user_id = $_GET['user_id'];

if ($_SESSION['role'] == 'user' && $_SESSION['user_id'] != $user_id) {
    header('Location: ../index.php');
    exit;
}

// Check if user has already borrowed this book
if ($_SESSION['role'] == 'user') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE user_id = ? AND book_id = ? AND return_date IS NULL");
    $stmt->execute([$user_id, $book_id]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: ../books/view_books.php?error=' . urlencode('You have already borrowed a copy of this book'));
        exit;
    }
}

$stmt = $pdo->prepare("SELECT available_copies FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if ($book && $book['available_copies'] > 0) {
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+7 days'));

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO issued_books (user_id, book_id, issue_date, due_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $book_id, $issue_date, $due_date]);

        $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?");
        $stmt->execute([$book_id]);

        $pdo->commit();
        $redirect = $_SESSION['role'] == 'user' ? '../dashboard/user_dashboard.php' : '../dashboard/librarian_dashboard.php';
        header("Location: $redirect?message=Book issued successfully");
    } catch (PDOException $e) {
        $pdo->rollBack();
        $redirect = $_SESSION['role'] == 'user' ? '../dashboard/user_dashboard.php' : '../dashboard/librarian_dashboard.php';
        header("Location: $redirect?error=" . urlencode($e->getMessage()));
    }
} else {
    $redirect = $_SESSION['role'] == 'user' ? '../books/view_books.php' : '../dashboard/librarian_dashboard.php';
    header("Location: $redirect?error=No copies available");
}
exit;
?>