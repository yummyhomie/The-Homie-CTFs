<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Page</title>
    <link rel="stylesheet" href="../tailwind-files/output.css">
</head>
<body class="bg-black text-white font-serif w-[960px] mx-auto p-8">

    <!-- HEADER -->
    <div class="border-8 border-double border-indigo-600 mt-8 p-2">

        <!-- HEADER GRAPHIC -->
        <img class="w-[100%] mx-auto" src="./project-assets/graphics/fire.gif" alt="JUST USE NIXOS">

        <!-- INTRO & NAVIGATION -->
        <div class="grid grid-cols-2 items-center">
        
            <p class="font-tahoma text-xl">a blog by eleedee</p>

            <div class="font-tahoma text-xl flex justify-end gap-4">
                <a href="./index.php" class="hover:text-indigo-600">home</a>
                <a href="./about.php" class="hover:text-indigo-600">about</a>
                <a href="./contact.php" class="hover:text-indigo-600">contact</a>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                    <a href="./account.php" class="hover:text-indigo-600">account</a>
                <?php else: ?>
                    <a href="./login.php" class="hover:text-indigo-600">login</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'Admin'): ?>
                    <a href="./admin.php" class="hover:text-red-600 text-red-400">admin</a>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <div class="mt-8 p-4 border-8 border-double border-yellow-600 w-[50%] mx-auto">

        <h1 class="text-3xl mb-4 text-center">Welcome, <span class="font-extrabold font-mono"><?= htmlspecialchars($_SESSION['username']) ?></span>!</h1>

        <p class="text-xl mb-4 ">This is your account page. Change your password or logout from here!</p>
    
        <!-- ACCOUNT ACTIONS -->
        <div class="flex justify-center items-center gap-4">
            <a href="change_password.php" class="bg-blue-600 px-4 py-2 rounded inline-block hover:bg-blue-700">Change Password</a>

            <form action="logout.php" method="POST" class="col-start-2 row-start-1">
                <button type="submit" class="bg-red-600 px-4 py-2 rounded">Logout</button>
            </form>
        </div>
    </div>

</body>
</html>
