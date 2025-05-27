<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

$book_id = $_GET['book_id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $isbn = $_POST['isbn'];
    $available_copies = $_POST['available_copies'];

    if (empty($title) || empty($author) || empty($category) || empty($isbn) || empty($available_copies)) {
        echo '<p class="text-red-500">All fields are required!</p>';
    } else {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category = ?, isbn = ?, available_copies = ? WHERE book_id = ?");
        try {
            $stmt->execute([$title, $author, $category, $isbn, $available_copies, $book_id]);
            echo '<p class="text-green-500">Book updated successfully!</p>';
        } catch (PDOException $e) {
            echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4">Edit Book</h2>
<form method="POST" class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Author</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Category</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($book['category']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">ISBN</label>
        <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Available Copies</label>
        <input type="number" name="available_copies" value="<?php echo $book['available_copies']; ?>" required class="w-full p-2 border rounded-md" min="0">
    </div>
    <button type="submit" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Update Book</button>
</form>
<?php include '../includes/footer.php'; ?>