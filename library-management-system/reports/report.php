<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

$report_type = $_GET['report_type'] ?? 'issued_books';

if ($report_type == 'issued_books') {
    $stmt = $pdo->prepare("SELECT i.*, b.title, u.name FROM issued_books i JOIN books b ON i.book_id = b.book_id JOIN users u ON i.user_id = u.user_id");
    $stmt->execute();
    $data = $stmt->fetchAll();
    $title = 'Issued Books Report';
} elseif ($report_type == 'overdue_books') {
    $stmt = $pdo->prepare("SELECT i.*, b.title, u.name FROM issued_books i JOIN books b ON i.book_id = b.book_id JOIN users u ON i.user_id = u.user_id WHERE i.return_date IS NULL AND i.due_date < CURDATE()");
    $stmt->execute();
    $data = $stmt->fetchAll();
    $title = 'Overdue Books Report';
} elseif ($report_type == 'fines') {
    $stmt = $pdo->prepare("SELECT f.*, b.title, u.name FROM fines f JOIN books b ON f.book_id = b.book_id JOIN users u ON f.user_id = u.user_id");
    $stmt->execute();
    $data = $stmt->fetchAll();
    $title = 'Fine Collections Report';
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4"><?php echo $title; ?></h2>
<div class="mb-6">
    <a href="?report_type=issued_books" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 mr-2">Issued Books</a>
    <a href="?report_type=overdue_books" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 mr-2">Overdue Books</a>
    <a href="?report_type=fines" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Fines</a>
</div>
<?php
if ($data) {
    echo '<table class="w-full border-collapse border">';
    if ($report_type == 'fines') {
        echo '<tr class="bg-gray-200"><th class="border p-2">User</th><th class="border p-2">Book</th><th class="border p-2">Due Date</th><th class="border p-2">Return Date</th><th class="border p-2">Amount</th><th class="border p-2">Status</th></tr>';
        foreach ($data as $row) {
            $return_date = $row['return_date'] ?? 'Not returned';
            echo "<tr><td class='border p-2'>{$row['name']}</td><td class='border p-2'>{$row['title']}</td><td class='border p-2'>{$row['due_date']}</td><td class='border p-2'>$return_date</td><td class='border p-2'>₹{$row['amount']}</td><td class='border p-2'>{$row['status']}</td></tr>";
        }
    } else {
        echo '<tr class="bg-gray-200"><th class="border p-2">User</th><th class="border p-2">Book</th><th class="border p-2">Issue Date</th><th class="border p-2">Due Date</th><th class="border p-2">Return Date</th></tr>';
        foreach ($data as $row) {
            $return_date = $row['return_date'] ?? 'Not returned';
            echo "<tr><td class='border p-2'>{$row['name']}</td><td class='border p-2'>{$row['title']}</td><td class='border p-2'>{$row['issue_date']}</td><td class='border p-2'>{$row['due_date']}</td><td class='border p-2'>$return_date</td></tr>";
        }
    }
    echo '</table>';
} else {
    echo '<p>No data available.</p>';
}
?>
<?php include '../includes/footer.php'; ?>