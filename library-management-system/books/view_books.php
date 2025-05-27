<?php
include '../includes/session_check.php';
include '../config/db.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$books = $stmt->fetchAll();

// Fetch books already borrowed by the user
$borrowed_books = [];
if ($_SESSION['role'] == 'user') {
    $stmt = $pdo->prepare("SELECT book_id FROM issued_books WHERE user_id = ? AND return_date IS NULL");
    $stmt->execute([$_SESSION['user_id']]);
    $borrowed_books = array_column($stmt->fetchAll(), 'book_id');
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-3xl font-bold mb-6 text-white fade-in">Available Books</h2>

<?php
if (isset($_GET['message'])) {
    echo '<p class="text-green-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['message']) . '</p>';
}
if (isset($_GET['error'])) {
    echo '<p class="text-red-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<div class="mb-8 card bg-white p-6 rounded-2xl shadow-xl">
    <form method="GET" class="flex space-x-4">
        <input type="text" name="search" placeholder="Search by title, author, or category" value="<?php echo htmlspecialchars($search); ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">Search</button>
    </form>
</div>

<div class="card bg-white p-6 rounded-2xl shadow-xl">
    <?php
    if ($books) {
        echo '<div class="overflow-x-auto"><table class="w-full border-collapse border border-gray-300">';
        echo '<tr class="bg-gradient-to-r from-blue-100 to-purple-100"><th class="border p-3 text-left">Title</th><th class="border p-3 text-left">Author</th><th class="border p-3 text-left">Category</th><th class="border p-3 text-left">ISBN</th><th class="border p-3 text-left">Available Copies</th><th class="border p-3 text-left">Action</th></tr>';
        foreach ($books as $book) {
            $action = '';
            if ($_SESSION['role'] == 'user') {
                if (in_array($book['book_id'], $borrowed_books)) {
                    $action = "<span class='text-gray-500'>Already Borrowed</span>";
                } elseif ($book['available_copies'] > 0) {
                    $action = "<a href='../transactions/issue_book.php?book_id={$book['book_id']}&user_id={$_SESSION['user_id']}' class='btn-primary inline-block text-white px-4 py-2 rounded-lg font-semibold text-center'>Borrow</a>";
                } else {
                    $action = "<span class='text-gray-500'>Not Available</span>";
                }
            } elseif ($_SESSION['role'] == 'librarian') {
                $action = "<a href='edit_book.php?book_id={$book['book_id']}' class='btn-primary text-white px-4 py-2 rounded-lg font-semibold mr-2'>Edit</a><a href='delete_book.php?book_id={$book['book_id']}' class='bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
            }
            echo "<tr class='table-row'><td class='border p-3'>{$book['title']}</td><td class='border p-3'>{$book['author']}</td><td class='border p-3'>{$book['category']}</td><td class='border p-3'>{$book['isbn']}</td><td class='border p-3'>{$book['available_copies']}</td><td class='border p-3'>$action</td></tr>";
        }
        echo '</table></div>';
    } else {
        echo '<p class="text-gray-600">No books found.</p>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>