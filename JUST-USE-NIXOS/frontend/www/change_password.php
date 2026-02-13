<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}

$db = new PDO('sqlite:/var/www/html/database/users.db');
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user = $_POST['user'] ?? $_SESSION['username'];
    
    if (empty($new_password)) {
        $message = "New password cannot be empty.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $user]);
        
        $message = "Password changed successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="../tailwind-files/output.css">
</head>
<body class="bg-black text-white font-serif w-[960px] mx-auto p-8">

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
            </div>
        </div>
    </div>

    <h1 class="text-3xl mb-4">Change Password</h1>

    <!-- PASSWORD CHANGE FORM -->
    <div class="mt-8 p-4 border-8 border-double border-yellow-600 w-[50%] mx-auto">
        <form action="change_password.php" method="POST" class="grid grid-cols-[auto,1fr] gap-4">
            
            <label class="flex items-center">New Password:</label>
            <input class="bg-gray-800 p-1" type="password" name="new_password" required>
            
            <label class="flex items-center">Confirm Password:</label>
            <input class="bg-gray-800 p-1" type="password" name="confirm_password" required>

            <div class="col-span-2 text-center">
                <input class="bg-green-600 px-4 py-2 cursor-pointer" type="submit" value="Change Password">
            </div>
        </form>

        <?php if (!empty($message)): ?>
            <div class="mt-4 p-2 bg-gray-900 text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <a href="account.php" class="text-indigo-400 hover:text-indigo-200">← Back to Account</a>
    </div>

</body>
</html>