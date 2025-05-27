<?php
include '../includes/session_check.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fine_id'])) {
    $stmt = $pdo->prepare("UPDATE fines SET status = 'paid' WHERE fine_id = ? AND user_id = ?");
    try {
        $stmt->execute([$_POST['fine_id'], $_SESSION['user_id']]);
        echo '<p class="text-green-500">Fine paid successfully!</p>';
    } catch (PDOException $e) {
        echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
    }
}

$stmt = $pdo->prepare("SELECT f.*, b.title FROM fines f JOIN books b ON f.book_id = b.book_id WHERE f.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$fines = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4">Your Fines</h2>
<?php
if ($fines) {
    echo '<table class="w-full border-collapse border">';
    echo '<tr class="bg-gray-200"><th class="border p-2">Book Title</th><th class="border p-2">Due Date</th><th class="border p-2">Return Date</th><th class="border p-2">Amount</th><th class="border p-2">Status</th><th class="border p-2">Action</th></tr>';
    foreach ($fines as $fine) {
        $action = $fine['status'] == 'pending' ? "<form method='POST'><input type='hidden' name='fine_id' value='{$fine['fine_id']}'><button type='submit' class='text-blue-600 hover:underline'>Pay</button></form>" : '';
        $return_date = $fine['return_date'] ?? 'Not returned';
        echo "<tr><td class='border p-2'>{$fine['title']}</td><td class='border p-2'>{$fine['due_date']}</td><td class='border p-2'>$return_date</td><td class='border p-2'>₹{$fine['amount']}</td><td class='border p-2'>{$fine['status']}</td><td class='border p-2'>$action</td></tr>";
    }
    echo '</table>';
} else {
    echo '<p>No fines.</p>';
}
?>
<?php include '../includes/footer.php'; ?>