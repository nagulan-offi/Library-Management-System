<?php
include '../includes/session_check.php';
include '../config/db.php';

if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header('Location: view_books.php?error=' . urlencode('Invalid book ID'));
    exit;
}

$book_id = (int)$_GET['book_id'];

// Check if book has active (non-returned) issued records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE book_id = ? AND return_date IS NULL");
$stmt->execute([$book_id]);
if ($stmt->fetchColumn() > 0) {
    header('Location: view_books.php?error=' . urlencode('Cannot delete book with active borrows'));
    exit;
}

try {
    // Delete the book (returned records won't block due to foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);

    if ($stmt->rowCount() > 0) {
        header('Location: view_books.php?message=' . urlencode('Book deleted successfully'));
    } else {
        header('Location: view_books.php?error=' . urlencode('Book not found'));
    }
} catch (PDOException $e) {
    header('Location: view_books.php?error=' . urlencode('Database error: ' . $e->getMessage()));
}
exit;
?>