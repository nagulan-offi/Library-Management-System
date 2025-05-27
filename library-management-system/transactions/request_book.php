<?php
include '../includes/session_check.php';
include '../config/db.php';

if ($_SESSION['role'] == 'librarian' && isset($_GET['request_id']) && isset($_GET['action'])) {
    $request_id = $_GET['request_id'];
    $action = $_GET['action'];

    $stmt = $pdo->prepare("UPDATE book_requests SET status = ? WHERE request_id = ?");
    $stmt->execute([$action, $request_id]);
    header('Location: requests_list.php?message=Request ' . $action . ' successfully');
    exit;
}

if ($_SESSION['role'] == 'user' && isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $request_date = date('Y-m-d');

    $stmt = $pdo->prepare("SELECT * FROM book_requests WHERE user_id = ? AND book_id = ? AND status = 'pending'");
    $stmt->execute([$_SESSION['user_id'], $book_id]);
    if ($stmt->fetch()) {
        header('Location: ../books/view_books.php?error=Request already pending');
    } else {
        $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        if ($book['available_copies'] > 0) {
            $stmt = $pdo->prepare("INSERT INTO book_requests (user_id, book_id, request_date) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$_SESSION['user_id'], $book_id, $request_date]);
                header('Location: ../books/view_books.php?message=Book requested successfully');
            } catch (PDOException $e) {
                header('Location: ../books/view_books.php?error=' . urlencode($e->getMessage()));
            }
        } else {
            header('Location: ../books/view_books.php?error=No copies available');
        }
    }
}
exit;
?>