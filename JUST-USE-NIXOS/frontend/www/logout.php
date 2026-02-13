<?php
session_start();
session_unset();     // Unset all session variables
session_destroy();   // Destroy the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
    <meta http-equiv="refresh" content="1;url=login.php">
    <link rel="stylesheet" href="../tailwind-files/output.css">
</head>
<body class="bg-black text-white font-serif w-[960px] mx-auto p-8 flex flex-col items-center justify-center min-h-screen">

    <h1 class="text-3xl text-green-400 mb-4">You have been logged out.</h1>
    <p class="text-xl">Redirecting to <a href="login.php" class="underline text-blue-400">login page</a></p>
    
    <div class="mt-4 rounded-lg"><img src="./project-assets/graphics/rate-my-setup.png"></div>

    <h4 class="text-4xl text-orange-400 mt-4">rate my setup</h4>

</body>
</html>
