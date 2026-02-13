<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}

// Check if user is Admin (case-sensitive for security)
if ($_SESSION['username'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

// Connect to databases
$users_db = new PDO('sqlite:/var/www/html/database/users.db');
$comments_db = new PDO('sqlite:/var/www/html/database/comments.db');

$message = "";

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $users_db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = "User deleted successfully!";
    }
    
    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $stmt = $comments_db->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        $message = "Comment deleted successfully!";
    }
    
    if (isset($_POST['reset_password'])) {
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $users_db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);
        $message = "Password reset successfully!";
    }
}

// Fetch all users
$stmt = $users_db->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all comments
$stmt = $comments_db->query("SELECT * FROM comments ORDER BY timestamp DESC");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$user_count = count($users);
$comment_count = count($comments);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel / JUST-USE-NIXOS</title>
    <link rel="stylesheet" href="../tailwind-files/output.css">
</head>
<body class="w-[960px] mx-auto text-white bg-black font-serif">

    <!-- HEADER -->
    <div class="border-8 border-double border-indigo-600 mt-8 p-2">
        <img class="w-[100%] mx-auto" src="./project-assets/graphics/fire.gif" alt="JUST USE NIXOS">
        <div class="grid grid-cols-2 items-center">
            <p class="font-tahoma text-xl">a blog by eleedee</p>
            <div class="font-tahoma text-xl flex justify-end gap-4">
                <a href="./index.php" class="hover:text-indigo-600">home</a>
                <a href="./about.php" class="hover:text-indigo-600">about</a>
                <a href="./contact.php" class="hover:text-indigo-600">contact</a>
                <a href="./account.php" class="hover:text-indigo-600">account</a>
                <a href="./admin.php" class="text-red-400 hover:text-red-600 hover:underline hover:decoration-2 hover:decoration-red-600">admin</a>
            </div>
        </div>
    </div>

    <!-- FLAG -->
    <div class="my-12 p-4 border-8 border-double border-orange-600">
        <h1 class="text-3xl font-extrabold mb-4 text-center text-orange-400 ">FLAG</h1>
        <p class="text-center my-8 font-mono font-extrabold text-2xl mx-auto animate-bounce duration-700">flag{Y0_G0_D0WNL04D_N1X05_R1H6T_N0W_1M_S3R10U$_H0M13!!}</p>
        <p class="text-sm text-gray-400 text-center">Congrats! You made it :)</p>
    </div>

    <!-- WELCOME -->
    <div class="mt-4 p-4 border-8 border-double border-red-600">
        <h1 class="text-3xl mb-4 text-center text-red-400">ADMIN PANEL</h1>
        <p class="text-center text-lg">Welcome, <span class="font-bold text-red-300"><?= htmlspecialchars($_SESSION['username']) ?></span>!</p>
        <p class="text-center text-sm text-gray-400">You have administrative privileges on this system.</p>
    </div>

    <!-- USERS -->
    <div class="mt-4 p-4 border-4 border-yellow-600 grid grid-cols-2">
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-400"><?= $user_count ?></div>
            <div class="text-sm">Total Users</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-400"><?= $comment_count ?></div>
            <div class="text-sm">Total Comments</div>
        </div>
    </div>

    <!-- USER MANAGEMENT -->
    <div class="mt-4 p-4 border-8 border-double border-cyan-600">
        <h2 class="text-2xl mb-4 text-cyan-400">User Management</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-600 p-2">ID</th>
                        <th class="border border-gray-600 p-2">Username</th>
                        <th class="border border-gray-600 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-800">
                        <td class="border border-gray-600 p-2 text-center"><?= htmlspecialchars($user['id']) ?></td>
                        <td class="border border-gray-600 p-2"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="border border-gray-600 p-2 text-center">
                            <?php if ($user['username'] !== 'Admin'): ?>
                                <form method="POST" class="inline-block mr-2">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user" 
                                            onclick="return confirm('Delete user <?= htmlspecialchars($user['username']) ?>?')"
                                            class="bg-red-600 hover:bg-red-700 px-2 py-1 rounded text-sm">
                                        Delete
                                    </button>
                                </form>
                                
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="text" name="new_password" placeholder="New password" 
                                           class="bg-gray-800 p-1 text-sm w-32 mr-2" required>
                                    <button type="submit" name="reset_password" 
                                            class="bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded text-sm">
                                        Reset
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-500 text-sm">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SYSTEM INFO FOR FUN -->
    <div class="mt-4 p-4 border-8 border-double border-purple-600">
        <h2 class="text-2xl mb-4 text-purple-400">System Information</h2>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <strong>Server:</strong> <?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?>
            </div>
            <div>
                <strong>PHP Version:</strong> <?= phpversion() ?>
            </div>
            <div>
                <strong>Document Root:</strong> <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') ?>
            </div>
            <div>
                <strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="mt-4 p-2 border-4 border-red-600 text-center">
        <p class="text-red-400">Administrative Access Only</p>
        <p class="text-sm text-gray-400">If you're not supposed to be here please proceed to <span class="underline">snap your laptop in half.</span></p>
    </div>

</body>
</html>