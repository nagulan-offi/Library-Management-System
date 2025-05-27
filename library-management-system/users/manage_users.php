<?php
include '../includes/session_check.php';
include '../config/db.php';
if ($_SESSION['role'] != 'librarian') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (empty($username) || empty($password) || empty($role) || empty($name) || empty($email)) {
        echo '<p class="text-red-500">All fields are required!</p>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, name, email) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $password, $role, $name, $email]);
            echo '<p class="text-green-500">User added successfully!</p>';
        } catch (PDOException $e) {
            echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4">Manage Users</h2>

<!-- Add User Form -->
<form method="POST" class="space-y-4 mb-6">
    <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Role</label>
        <select name="role" required class="w-full p-2 border rounded-md">
            <option value="user">User</option>
            <option value="librarian">Librarian</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Name</label>
        <input type="text" name="name" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" required class="w-full p-2 border rounded-md">
    </div>
    <button type="submit" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Add User</button>
</form>

<!-- Users List -->
<h3 class="text-xl font-semibold mb-2">Users</h3>
<?php
if ($users) {
    echo '<table class="w-full border-collapse border">';
    echo '<tr class="bg-gray-200"><th class="border p-2">Username</th><th class="border p-2">Name</th><th class="border p-2">Email</th><th class="border p-2">Role</th><th class="border p-2">Action</th></tr>';
    foreach ($users as $user) {
        echo "<tr><td class='border p-2'>{$user['username']}</td><td class='border p-2'>{$user['name']}</td><td class='border p-2'>{$user['email']}</td><td class='border p-2'>{$user['role']}</td>";
        echo "<td class='border p-2'><a href='manage_users.php?delete_id={$user['user_id']}' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a></td></tr>";
    }
    echo '</table>';
} else {
    echo '<p>No users found.</p>';
}

if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    try {
        $stmt->execute([$_GET['delete_id']]);
        header('Location: manage_users.php?message=User deleted successfully');
    } catch (PDOException $e) {
        echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
    }
}
?>
<?php include '../includes/footer.php'; ?>