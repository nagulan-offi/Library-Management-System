<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fine_id = $_POST['fine_id'];
    $action = $_POST['action'];
    $amount = $_POST['amount'] ?? null;

    if ($action == 'waive') {
        $stmt = $pdo->prepare("UPDATE fines SET status = 'waived' WHERE fine_id = ?");
        $stmt->execute([$fine_id]);
        echo '<p class="text-green-500">Fine waived successfully!</p>';
    } elseif ($action == 'adjust' && !empty($amount)) {
        $stmt = $pdo->prepare("UPDATE fines SET amount = ? WHERE fine_id = ?");
        $stmt->execute([$amount, $fine_id]);
        echo '<p class="text-green-500">Fine adjusted successfully!</p>';
    }
}

$stmt = $pdo->prepare("SELECT f.*, b.title, u.name FROM fines f JOIN books b ON f.book_id = b.book_id JOIN users u ON f.user_id = u.user_id");
$stmt->execute();
$fines = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4">Manage Fines</h2>
<?php
if ($fines) {
    echo '<table class="w-full border-collapse border">';
    echo '<tr class="bg-gray-200"><th class="border p-2">User</th><th class="border p-2">Book</th><th class="border p-2">Due Date</th><th class="border p-2">Return Date</th><th class="border p-2">Amount</th><th class="border p-2">Status</th><th class="border p-2">Action</th></tr>';
    foreach ($fines as $fine) {
        $return_date = $fine['return_date'] ?? 'Not returned';
        echo "<tr><td class='border p-2'>{$fine['name']}</td><td class='border p-2'>{$fine['title']}</td><td class='border p-2'>{$fine['due_date']}</td><td class='border p-2'>$return_date</td><td class='border p-2'>₹{$fine['amount']}</td><td class='border p-2'>{$fine['status']}</td>";
        echo "<td class='border p-2'>";
        if ($fine['status'] == 'pending') {
            echo "<form method='POST' class='inline'><input type='hidden' name='fine_id' value='{$fine['fine_id']}'><input type='number' name='amount' value='{$fine['amount']}' class='w-20 p-1 border rounded-md' step='0.01'><input type='hidden' name='action' value='adjust'><button type='submit' class='text-blue-600 hover:underline'>Adjust</button></form> ";
            echo "<form method='POST' class='inline'><input type='hidden' name='fine_id' value='{$fine['fine_id']}'><input type='hidden' name='action' value='waive'><button type='submit' class='text-red-600 hover:underline'>Waive</button></form>";
        }
        echo "</td></tr>";
    }
    echo '</table>';
} else {
    echo '<p>No fines.</p>';
}
?>
<?php include '../includes/footer.php'; ?>