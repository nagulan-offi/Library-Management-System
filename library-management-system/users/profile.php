<?php
include '../includes/session_check.php';
include '../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    if (empty($name) || empty($email)) {
        echo '<p class="text-red-500">Name and email are required!</p>';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?");
        try {
            $stmt->execute([$name, $email, $password, $_SESSION['user_id']]);
            $_SESSION['name'] = $name;
            echo '<p class="text-green-500">Profile updated successfully!</p>';
        } catch (PDOException $e) {
            echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
<form method="POST" class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled class="w-full p-2 border rounded-md bg-gray-100">
    </div>
    <div>
        <label class="block text-sm font-medium">Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full p-2 border rounded-md">
    </div>
    <div>
        <label class="block text-sm font-medium">New Password (leave blank to keep current)</label>
        <input type="password" name="password" class="w-full p-2 border rounded-md">
    </div>
    <button type="submit" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Update Profile</button>
</form>
<?php include '../includes/footer.php'; ?>