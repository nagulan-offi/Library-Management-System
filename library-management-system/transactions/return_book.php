<?php
include '../includes/session_check.php';
include '../config/db.php';

// Debugging: Verify session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    error_log("Session invalid in return_book.php: user_id=" . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'unset') . ", role=" . (isset($_SESSION['role']) ? $_SESSION['role'] : 'unset'));
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['issue_id']) || !is_numeric($_GET['issue_id'])) {
    header('Location: ../dashboard/user_dashboard.php?error=Invalid issue ID');
    exit;
}

$issue_id = (int)$_GET['issue_id'];

if ($_SESSION['role'] == 'user') {
    $stmt = $pdo->prepare("SELECT user_id, book_id FROM issued_books WHERE issue_id = ? AND return_date IS NULL");
    $stmt->execute([$issue_id]);
    $issue = $stmt->fetch();
    if (!$issue || $issue['user_id'] != $_SESSION['user_id']) {
        error_log("Unauthorized return attempt: issue_id=$issue_id, user_id={$_SESSION['user_id']}");
        header('Location: ../dashboard/user_dashboard.php?error=Unauthorized action');
        exit;
    }
} else {
    $stmt = $pdo->prepare("SELECT book_id FROM issued_books WHERE issue_id = ? AND return_date IS NULL");
    $stmt->execute([$issue_id]);
    $issue = $stmt->fetch();
    if (!$issue) {
        header('Location: ../dashboard/librarian_dashboard.php?error=Invalid or already returned');
        exit;
    }
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE issued_books SET return_date = CURDATE() WHERE issue_id = ? AND return_date IS NULL");
    $stmt->execute([$issue_id]);

    if ($stmt->rowCount() == 0) {
        $pdo->rollBack();
        $redirect = $_SESSION['role'] == 'user' ? '../dashboard/user_dashboard.php' : '../dashboard/librarian_dashboard.php';
        header("Location: $redirect?error=Book already returned or invalid issue");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?");
    $stmt->execute([$issue['book_id']]);

    $pdo->commit();
    $redirect = $_SESSION['role'] == 'user' ? '../dashboard/user_dashboard.php' : '../dashboard/librarian_dashboard.php';
    header("Location: $redirect?message=Book returned successfully");
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Return error: " . $e->getMessage());
    $redirect = $_SESSION['role'] == 'user' ? '../dashboard/user_dashboard.php' : '../dashboard/librarian_dashboard.php';
    header("Location: $redirect?error=" . urlencode($e->getMessage()));
}
exit;
?>