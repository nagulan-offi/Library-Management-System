<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    exit('Access denied');
}

$stmt = $pdo->prepare("SELECT * FROM issued_books WHERE return_date IS NULL AND due_date < CURDATE()");
$stmt->execute();
$overdue_books = $stmt->fetchAll();

foreach ($overdue_books as $book) {
    $due_date = new DateTime($book['due_date']);
    $today = new DateTime();
    $days_overdue = $today->diff($due_date)->days;
    $fine_amount = $days_overdue * 5.00;

    $check_fine = $pdo->prepare("SELECT * FROM fines WHERE issue_id = ?");
    $check_fine->execute([$book['issue_id']]);
    if (!$check_fine->fetch()) {
        $insert_fine = $pdo->prepare("INSERT INTO fines (user_id, book_id, issue_id, due_date, amount, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $insert_fine->execute([$book['user_id'], $book['book_id'], $book['issue_id'], $book['due_date'], $fine_amount]);
    } else {
        $update_fine = $pdo->prepare("UPDATE fines SET amount = ? WHERE issue_id = ?");
        $update_fine->execute([$fine_amount, $book['issue_id']]);
    }
}
?>