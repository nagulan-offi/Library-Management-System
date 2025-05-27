<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-600 to-pink-500">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md card fade-in">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800" id="form-title">Library Management</h1>
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800" id="form-title">Login</h2>
        <?php
        session_start();
        include 'config/db.php';

        // Display success/error messages
        if (isset($_GET['message'])) {
            echo '<p class="text-green-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['message']) . '</p>';
        }
        if (isset($_GET['error'])) {
            echo '<p class="text-red-500 text-center font-medium mb-4">' . htmlspecialchars($_GET['error']) . '</p>';
        }

        // Handle login
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                echo '<p class="text-red-500 text-center font-medium">Please fill in all fields!</p>';
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch();

                    if ($user && $password === $user['password']) {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['name'] = $user['name'];
                        if ($user['role'] == 'librarian') {
                            header('Location: dashboard/librarian_dashboard.php');
                        } else {
                            header('Location: dashboard/user_dashboard.php');
                        }
                        exit;
                    } else {
                        echo '<p class="text-red-500 text-center font-medium">Invalid username or password!</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-red-500 text-center font-medium">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
            }
        }
        ?>
        <!-- Login Form -->
        <div id="login-form" class="form-container">
            <form method="POST" class="space-y-5">
                <input type="hidden" name="login" value="1">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="w-full btn-primary text-white p-3 rounded-lg font-semibold">Login</button>
            </form>
            <p class="text-center mt-4 text-gray-600">Don't have an account? <a href="#" onclick="toggleForm('register')" class="text-blue-600 font-semibold hover:underline">Sign Up</a></p>
        </div>

        <!-- Registration Form -->
        <div id="register-form" class="form-container hidden">
            <form method="POST" action="register.php" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Name</label>
                    <input type="text" name="name" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Role</label>
                    <select name="role" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="user">User</option>
                        <option value="librarian">Librarian</option>
                    </select>
                </div>
                <button type="submit" class="w-full btn-primary text-white p-3 rounded-lg font-semibold">Register</button>
            </form>
            <p class="text-center mt-4 text-gray-600">Already have an account? <a href="#" onclick="toggleForm('login')" class="text-blue-600 font-semibold hover:underline">Log In</a></p>
        </div>
    </div>

    <script>
        function toggleForm(form) {
            document.getElementById('login-form').classList.toggle('hidden', form !== 'login');
            document.getElementById('register-form').classList.toggle('hidden', form !== 'register');
            document.getElementById('form-title').textContent = form === 'login' ? 'Login' : 'Register';
        }
    </script>
</body>
</html>