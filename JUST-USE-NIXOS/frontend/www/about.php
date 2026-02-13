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

    <div class="mt-8 border-8 border-double border-orange-600 p-4">
        <h1 class="text-7xl font-bold text-center">About Me:</h1>
        <img src="./project-assets/graphics/hash.gif" class="w-[500px] mx-auto">
    </div>
    
</body>
</html>