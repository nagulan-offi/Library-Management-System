<?php
include '../includes/session_check.php';
include '../config/db.php';

if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $available_copies = (int)$_POST['available_copies'];

    if (empty($title) || empty($author) || empty($category) || empty($isbn) || $available_copies < 0) {
        $error = 'All fields are required, and available copies must be non-negative';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE isbn = ?");
            $stmt->execute([$isbn]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'ISBN already exists';
            } else {
                $stmt = $pdo->prepare("INSERT INTO books (title, author, category, isbn, available_copies) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $category, $isbn, $available_copies]);
                header('Location: view_books.php?message=' . urlencode('Book added successfully'));
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-3xl font-bold mb-6 text-white fade-in">Add New Book</h2>

<div class="card bg-white p-6 rounded-2xl shadow-xl">
    <?php if (isset($error)) : ?>
        <p class="text-red-500 text-center font-medium mb-4"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-gray-700">Title</label>
            <input type="text" name="title" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">Author</label>
            <input type="text" name="author" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">Category</label>
            <input type="text" name="category" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">ISBN</label>
            <input type="text" name="isbn" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">Available Copies</label>
            <input type="number" name="available_copies" min="0" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button type="submit" class="w-full btn-primary text-white p-3 rounded-lg font-semibold">Add Book</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>