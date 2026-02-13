<?php
session_start();
$db = new PDO('sqlite:/var/www/html/database/users.db');

// Create table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
)");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($_POST['register'])) {
        // Handle Registration
        if (empty($username) || empty($password)) {
            $message = "Username and password required.";
        } else {
            // Check if username already exists
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = "Username already taken.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hashed]);
                $message = "Registered successfully! You can now log in.";
            }
        }
    } elseif (isset($_POST['login'])) {
        // Handle Login
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stored_password = $user['password'];
            $login_successful = false;
            
            // First, try to verify as a hashed password
            if (password_verify($password, $stored_password)) {
                $login_successful = true;
            }
            // If hash verification fails, try plaintext comparison
            elseif ($password === $stored_password) {
                $login_successful = true;
            }
            
            if ($login_successful) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['username'];
                header("location: account.php");
                exit;
            } else {
                $message = "Invalid credentials.";
            }
        } else {
            $message = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>USE-NIXOS-DOTCOM</title>
    <link rel="stylesheet" href="../tailwind-files/output.css">
</head>
<body class="w-[960px] mx-auto text-white bg-black font-serif">   
    
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

    <!-- LOGIN -->
    <div class="mt-8 p-4 border-8 border-double border-green-600 w-[50%] mx-auto">

        <!-- LOGIN FORM -->
        <form action="login.php" method="POST" class="grid grid-cols-[auto,1fr] grid-rows-[auto,auto,auto] gap-4">
            
            <!-- TITLES -->
            <label class="col-start-1 row-start-1 flex items-center justify-center">Username:</label>
            <label class="flex items-center justify-center">Password:</label>

            <!-- USERNAME INPUT -->
            <input class="col-start-2 row-start-1 bg-gray-800 p-1" type="text" name="username">
            
            <!-- PASSWORD INPUT -->
            <input class="bg-gray-800 p-1" type="password" name="password">

            <!-- REGISTER/LOGIN BUTTONS -->
            <div class="col-span-2 grid grid-cols-2 gap-8 text-center h-10 mx-8">
                <input class="bg-blue-600" type="submit" name="login" value="Login">
                <input class="bg-red-600" type="submit" name="register" value="Register">
            </div>
        </form>

        <!-- MESSAGE -->
        <?php if (!empty($message)): ?>
            <div class="mt-4 p-2 bg-gray-900 text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

    </div>

    
</body>
</html>