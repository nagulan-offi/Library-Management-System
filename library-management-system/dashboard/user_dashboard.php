<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit;
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-3xl font-bold mb-6 text-white fade-in">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

<?php
if (isset($_GET['message'])) {
    echo '<p class="text-green-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['message']) . '</p>';
}
if (isset($_GET['error'])) {
    echo '<p class="text-red-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<!-- Search Books -->
<div class="mb-8 card bg-white p-6 rounded-2xl shadow-xl">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Search Books</h3>
    <form method="GET" action="../books/view_books.php" class="flex space-x-4">
        <input type="text" name="search" placeholder="Search by title, author, or category" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">Search</button>
    </form>
</div>

<!-- Issued Books -->
<div class="mb-8 card bg-white p-6 rounded-2xl shadow-xl">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Your Issued Books</h3>
    <?php
    $stmt = $pdo->prepare("SELECT i.*, b.title FROM issued_books i JOIN books b ON i.book_id = b.book_id WHERE i.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $issued_books = $stmt->fetchAll();
    if ($issued_books) {
        echo '<div class="overflow-x-auto"><table class="w-full border-collapse border border-gray-300">';
        echo '<tr class="bg-gradient-to-r from-blue-100 to-purple-100"><th class="border p-3 text-left">Book Title</th><th class="border p-3 text-left">Issue Date</th><th class="border p-3 text-left">Due Date</th><th class="border p-3 text-left">Status</th><th class="border p-3 text-left">Action</th></tr>';
        foreach ($issued_books as $book) {
            $status = $book['return_date'] ? 'Returned' : 'Issued';
            $action = $book['return_date'] ? '' : "<a href='../transactions/return_book.php?issue_id={$book['issue_id']}' class='btn-primary text-white px-4 py-2 rounded-lg font-semibold'>Return</a>";
            echo "<tr class='table-row'><td class='border p-3'>{$book['title']}</td><td class='border p-3'>{$book['issue_date']}</td><td class='border p-3'>{$book['due_date']}</td><td class='border p-3'>$status</td><td class='border p-3'>$action</td></tr>";
        }
        echo '</table></div>';
    } else {
        echo '<p class="text-gray-600">No books issued.</p>';
    }
    ?>
</div>

<!-- Fines -->
<div class="card bg-white p-6 rounded-2xl shadow-xl">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Your Fines</h3>
    <?php
    $stmt = $pdo->prepare("SELECT f.*, b.title FROM fines f JOIN books b ON f.book_id = b.book_id WHERE f.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $fines = $stmt->fetchAll();
    if ($fines) {
        echo '<div class="overflow-x-auto"><table class="w-full border-collapse border border-gray-300">';
        echo '<tr class="bg-gradient-to-r from-blue-100 to-purple-100"><th class="border p-3 text-left">Book Title</th><th class="border p-3 text-left">Amount</th><th class="border p-3 text-left">Status</th></tr>';
        foreach ($fines as $fine) {
            echo "<tr class='table-row'><td class='border p-3'>{$fine['title']}</td><td class='border p-3'>₹{$fine['amount']}</td><td class='border p-3'>{$fine['status']}</td></tr>";
        }
        echo '</table></div>';
    } else {
        echo '<p class="text-gray-600">No fines.</p>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>