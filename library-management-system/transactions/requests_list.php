<!DOCTYPE html>
<html lang="en" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        html, body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content-area {
            flex: 1 0 auto;
        }
        /* Ensure footer is fixed at the bottom */
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: linear-gradient(to right, #1e3a8a, #5b21b6);
            color: white;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-800 to-purple-600 min-h-screen">
    <?php
    // Start session only if not already active
    if (session_status() !== PHP_SESSION_ACTIVE && !session_id()) {
        session_start();
    }
    ?>
    <nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 sticky top-0 z-10 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Library System</h1>
            <div class="hidden md:flex space-x-6">
                <?php
                if (isset($_SESSION['role'])) {
                    if ($_SESSION['role'] == 'librarian') {
                        echo '
                        <a href="../dashboard/librarian_dashboard.php" class="nav-link font-semibold hover:text-gray-200">Dashboard</a>
                        <a href="../books/view_books.php" class="nav-link font-semibold hover:text-gray-200">Books</a>
                        <a href="../users/manage_users.php" class="nav-link font-semibold hover:text-gray-200">Users</a>
                        <a href="../fines/manage_fines.php" class="nav-link font-semibold hover:text-gray-200">Fines</a>
                        <a href="../reports/report.php" class="nav-link font-semibold hover:text-gray-200">Reports</a>';
                    } else {
                        echo '
                        <a href="../dashboard/user_dashboard.php" class="nav-link font-semibold hover:text-gray-200">Dashboard</a>
                        <a href="../books/view_books.php" class="nav-link font-semibold hover:text-gray-200">Books</a>
                        <a href="../fines/user_fines.php" class="nav-link font-semibold hover:text-gray-200">Fines</a>
                        <a href="../users/profile.php" class="nav-link font-semibold hover:text-gray-200">Profile</a>';
                    }
                    echo '<a href="../logout.php" class="nav-link font-semibold hover:text-gray-200">Logout</a>';
                }
                ?>
            </div>
            <button class="md:hidden focus:outline-none" onclick="toggleMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
        <div id="mobile-menu" class="md:hidden hidden flex-col space-y-2 mt-4">
            <?php
            if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'librarian') {
                    echo '
                    <a href="../dashboard/librarian_dashboard.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Dashboard</a>
                    <a href="../books/view_books.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Books</a>
                    <a href="../users/manage_users.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Users</a>
                    <a href="../fines/manage_fines.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Fines</a>
                    <a href="../reports/report.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Reports</a>';
                } else {
                    echo '
                    <a href="../dashboard/user_dashboard.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Dashboard</a>
                    <a href="../books/view_books.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Books</a>
                    <a href="../fines/user_fines.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Fines</a>
                    <a href="../users/profile.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Profile</a>';
                }
                echo '<a href="../logout.php" class="nav-link font-semibold p-2 bg-blue-700 rounded-lg hover:bg-blue-600">Logout</a>';
            }
            ?>
        </div>
    </nav>
    <div class="container mx-auto p-6 content-area">
    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>