<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-3xl font-bold mb-6 text-white fade-in">Librarian Dashboard</h2>

<?php
if (isset($_GET['message'])) {
    echo '<p class="text-green-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['message']) . '</p>';
}
if (isset($_GET['error'])) {
    echo '<p class="text-red-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<!-- Actions -->
<div class="mb-8">
    <a href="../books/add_book.php" class="btn-primary text-white px-4 py-2 rounded-lg font-semibold">Add New Book</a>
    <a href="../books/view_books.php" class="btn-primary text-white px-4 py-2 rounded-lg font-semibold ml-4">Manage Books</a>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php
    // Total copies of all books
    $total_copies = $pdo->query("SELECT SUM(available_copies) FROM books")->fetchColumn();
    $total_copies = $total_copies ? $total_copies : 0; // Handle NULL case
    $issued_books = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE return_date IS NULL")->fetchColumn();
    $overdue_books = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE return_date IS NULL AND due_date < CURDATE()")->fetchColumn();
    ?>
    <div class="card bg-white p-6 rounded-2xl shadow-xl text-center">
        <h3 class="text-lg font-semibold text-gray-800">Total Book Copies</h3>
        <p class="text-3xl font-bold text-blue-600"><?php echo $total_copies; ?></p>
    </div>
    <div class="card bg-white p-6 rounded-2xl shadow-xl text-center">
        <h3 class="text-lg font-semibold text-gray-800">Issued Books</h3>
        <p class="text-3xl font-bold text-purple-600"><?php echo $issued_books; ?></p>
    </div>
    <div class="card bg-white p-6 rounded-2xl shadow-xl text-center">
        <h3 class="text-lg font-semibold text-gray-800">Overdue Books</h3>
        <p class="text-3xl font-bold text-blue-600"><?php echo $overdue_books; ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>  