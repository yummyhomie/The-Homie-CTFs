<?php
session_start();
// Generate a unique session identifier for comment isolation
if (!isset($_SESSION['comment_session_id'])) {
    $_SESSION['comment_session_id'] = uniqid('sess_', true);
}

$db = new PDO('sqlite:/var/www/html/database/comments.db');

// Create the comments table 
$db->exec("CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id TEXT,
    username TEXT DEFAULT 'Anonymous',
    comment TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$message = "";

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $comment = trim($_POST['comment'] ?? '');
    
    if (!empty($comment)) {
        // Check if user is logged in
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $username = $_SESSION['username'];
        } else {
            $username = 'Anonymous';
        }
        
        // Insert comment into database with session ID for isolation
        $stmt = $db->prepare("INSERT INTO comments (session_id, username, comment) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['comment_session_id'], $username, $comment]);
        $message = "Comment posted successfully!";
        
        // Clear the comment after posting
        $_POST['comment'] = '';
    } else {
        $message = "Comment cannot be empty.";
    }
}

// Handle comment deletion (only for logged-in users on their own comments)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        // Delete only if the comment belongs to the logged-in user AND is from the same session
        $stmt = $db->prepare("DELETE FROM comments WHERE id = ? AND username = ? AND session_id = ?");
        $stmt->execute([$comment_id, $_SESSION['username'], $_SESSION['comment_session_id']]);
        $message = "Comment deleted successfully!";
    }
}

// Fetch all comments
$stmt = $db->prepare("SELECT * FROM comments WHERE session_id = ? or session_id = 'static_session' ORDER BY timestamp ASC");
$stmt->execute([$_SESSION['comment_session_id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>USE-NIXOS-DOTCOM</title>
    <link rel="stylesheet" href="./tailwind-files/output.css">
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
    
    <!-- MAIN CONTENT -->
    <div class="mt-8">

        <!-- BLOG -->
        <div class="border-8 border-double border-cyan-600 p-2 text-xl text-stone-100 leading-relaxed">
            <img src="/project-assets/graphics/nixos.svg" class="float-left w-40 h-auto mr-4 mb-2" alt="NixOS Logo">

            <p><span class="text-4xl font-bold">JUST USE NIXOS!!!!</span> Need I say more?</p>

            This article is NOT here to criticize other Linux OS's. My journey has seen me from Ubuntu, to Debian, then Gentoo (for 30 seconds) and Arch, to glorious openSUSE & finally NixOS. I will simply recount a few paths I took through linux, and why it led me to NixOS. (And why it you might end up using NixOS too!)<br>

            Context: <br>
            <span class="text-2xl font-bold">1.</span> For context, I refer to something called a <span class="italic text-amber-300">configuration.nix</span> file throughout this article. This file IS the config file for my entire system, everything is declared here. Packages, services, system settings & more. <br>
            <span class="text-2xl font-bold">2.</span> Another phrase I use is *rebuild* or  <span class="italic text-amber-300">sudo nixos-rebuild switch</span>. When I refer to either one of these commands, they're essentially the NixOS equivalent of sudo apt-get install AND systemctl start/enable all bundled together in one command.<br><br> 

            <div class="text-center"><p>--------------------------------------------------------------------------------------------------------------------<p></div><br>

            If there's 1 paragraph you read today, have it be this one. <span class="font-bold text-amber-300">Everything</span> on NixOS is declarative. Programs, services, system settings, you name it!<br><br> 
            <div class="w-[85%] mx-auto"><span class="text-amber-300 font-sans">I DO NOT CARE HOW GOOD YOU ARE AT CONFIGURING YOUR FAVORITE MACHINE. I WANT TO SETUP HYPRLAND IN 20 MINUTES AND PLAY SOME SPACEMARINE 2 <span class="italic text-amber-300 underline decoration-amber-600">BEFORE 8 PM TONIGHT!</span> NIXOS HELPS ME DO JUST THAT.</span></div><br>

            Don't get me wrong, I've had some wonderful vibe-configuring nights where I stayed up till 3-4 am configuring my machine away. Setting up the perfect environment for me. Podcasts were listened to, youtube was watched, and I walked away feeling accomplished & sleep deprived.<br><br>
            BUT, the amount of times I walked away defeated because I broke my system, is immeasurable. Going to bed knowing tomorrow I have to reinstall my system hurts my soul. I HATED that feeling! 
            "Well why didn't backup your system before making changes?" <- Simple answer: <span class="text-amber-300">I never figured out how.</span> Reinstalling hurt so deeply because I felt my efforts in making my perfect config was wasted. I had to start over from zero again.<br> 

            <div class="text-center"><span class="text-4xl font-extrabold">:/</span></div><br>

            Now my life on NixOS goes something like this:<br><br>

            <div class="w-[75%] mx-auto">

            <span class="italic text-orange-300">YO! I just saw a sick video about Hyprland. Lemme try it out!</span><br><br>

            Type in: <span class="inline-block bg-white bg-opacity-20 rounded-md px-2 font-mono">vim /etc/nixos/configuration.nix</span><br>
            Add this line to my config: <span class="inline-block bg-white bg-opacity-20 rounded-md px-2 font-mono">programs.hyprland.enable = true;</span><br>
            Rebuild my system: <span class="inline-block bg-white bg-opacity-20 rounded-md px-2 font-mono">sudo nixos-rebuild switch</span><span class="text-amber-300 font-bold"> **</span><br>
            Type in: <span class="inline-block bg-white bg-opacity-20 rounded-md px-2 font-mono">Hyprland</span> into the console.<br><br> 

            <p class="ml-4 text-[12pt]"><span class="text-amber-300 font-bold">**</span> - Because of the Nix package manager, Hyprland will install with all its required dependencies. Including the kitty terminal! (Hyprlands default terminal)</p><br>

            <span class="italic text-orange-300">Hyprland is sick. What about dwl?</span><br><br>

            Open up config:

            <span class="block bg-white bg-opacity-20 rounded-md px-2 font-mono">
            programs.hyprland.enable = false;<br>

            programs.dwl.enable = true;
            </span><br>

            Then I rebuild: <span class="inline-block bg-white bg-opacity-20 rounded-md px-2">sudo nixos-rebuild switch</span><br><br>

            <span class="italic text-orange-300">Actually, I'm good with Hyprland. Lemme get waybar!</span><br><br>
            <span class="block bg-white bg-opacity-20 rounded-md px-2 font-mono">
            programs.hyprland.enable = true;<br>

            programs.waybar.enable = true;
            </span><br>

            </div>

            And the process continues! Over time, my config grows and grows. Each backup of my <span class="italic text-amber-300">configuration.nix</span> to github makes me feel like I am investing my efforts and time into the future. My time is NOT wasted.<br><br>

            "Okay bro" you might ask, "You have a fancy package manager to install shiz for you, but you still gotta go in and configure everything right?" <span class="font-bold text-amber-300">NOPE!</span>

            Let's take a look at a simple config of mine. <span class="font-bold text-amber-300">VIM!</span>, featuring all sorts of cool plugins and settings I like.<br><br>

            <div class="w-[75%] mx-auto">
                <span class="block bg-white bg-opacity-20 rounded-md px-2 font-mono">
                { pkgs, config, ... }:<br>
                {<br>
                    <div class="ml-4">programs.vim = {</div>
                        <div class="ml-8">enable = true;</div>
                        <div class="ml-8">extraConfig = ''</div>
                            <div class="ml-12">set number</div>
                            <div class="ml-12">set nowrap</div>
                            <div class="ml-12">colorscheme gruvbox</div>
                            <div class="ml-12">set background=dark</div>
                            <div class="ml-12">syntax on</div>
                            <div class="ml-12">set autoindent</div>
                            <div class="ml-12">set smartindent</div>
                        <div class="ml-8">'';</div><br>

                        <div class="ml-8">plugins = with pkgs.vimPlugins; [</div>
                            <div class="ml-12">gruvbox         <span class="ml-[77px]"># For gruvbox theme</span></div>
                            <div class="ml-12">auto-pairs      <span class="ml-[44px]"># Automatically fill in stuff</span></div>
                            <div class="ml-12">nvchad          <span class="ml-[88px]"># Makes VIM feel like an IDE</span></div>
                            <div class="ml-12">ale             <span class="ml-[120px]"># Code/Syntax error catcher</span></div>
                            <div class="ml-12">lightline-vim   <span class="ml-[10px]"># Status Bar</span></div>
                            <div class="ml-12">lightline-ale   <span class="ml-[10px]"># Status Bar addon that works with ALE</span></div>
                            <div class="ml-12">nerdtree        <span class="ml-[64px]"># Directory viewer for projects</span></div>
                        <div class="ml-8">];</div>
                    <div class="ml-4">};</div>
                }
                </span><br>
            </div>

            <div class="text-center"><span class="text-4xl font-extrabold">:)</span></div><br>

            As you can see, the syntax is pretty easy to get used to. And don't worry, I didn't make this up. All these variables and options are available on websites and forums like <span class="italic text-orange-300 underline decoration-orange-300"><a href="https://search.nixos.org/packages">search.nixos.org/packages</a></span> and <span class="italic text-orange-300 underline decoration-orange-300"><a href="https://mynixos.com/">mynixos.com</a></span>. So, with a little bit of time, you can make your own config or copy someone else's. Nix has a <span class="font-bold text-2xl">HUGE</span> community! <br><br>

            Wrapping up, let me leave you with a few words:<br><br> 

                <p class="w-[85%] mx-auto text-2xl/10 font-sans font-semibold text-amber-300">NIXOS HAS BEEN THE GREATEST OS I HAVE USED, LET YOUR SYSTEM WORK FOR YOU. BECAUSE OF NIXOS I SPEND MORE TIME USING & ENJOYING MY MACHINE AND LESS TIME CONFIGURING. ALL PACKAGES ARE WITHIN REACH. <span class="italic">I DO NOT FEAR CONFIGS, I DO NOT FEAR DEPENDENCIES, THEY ARE BELOW ME. I HAVE CONQURED THEM WITH NIX.</span></p><br> 
                
                <p class="w-[85%] mx-auto text-2xl/10 text-center font-sans font-semibold underline decoration-[#5072bb] decoration-2">LINUX SUPREMACY. <span class="text-[#7db6e1]">NIXOS SUPREMACY</span>.</p><br>

            <div class="w-[95%] text-center">Have a wonderful day, take care of yourself. Feel free to leave any questions in the</div>
            <div class="animate-bounce p-4 text-center"><span class="text-4xl text-orange-300">comment section below!</span></div>
	 
        </div>

        <!-- LEAVE A COMMENT -->
        <form method="POST" action="#comments" class="mt-4">
            <div class="col-start-2 row-start-3 border-8 border-double border-amber-600">

                <!-- COMMENT AREA -->
                <textarea 
                    name="comment" 
                    placeholder="Leave a comment... <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'Logged in as ' . htmlspecialchars($_SESSION['username']) : 'Login to change or delete your comments!'; ?>" 
                    class="bg-black p-2 w-full resize-none h-20 text-white"
                    rows="2"
                ><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>

                <div class="text-center p-2">

                    <!-- POST COMMENT BUTTON -->
                    <button type="submit" name="submit_comment" class="px-[8px] hover:text-amber-600">
                        Post Comment
                    </button>

                    <!-- POSTING AS DISPLAY -->
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                        <span class="px-[8px]">Posting as: <span class="font-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                    <?php else: ?>
                        <span class="px-[8px]">Posting as: <span class="font-bold">Anonymous</span></span>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- COMMENT SECTION -->
        <div class="p-2 border-8 border-double border-lime-600 mt-4" id="comments">
            <p class="text-lg mb-4">Comments:</p>

            <!-- COMMENT MESSAGE -->
            <?php if (!empty($message)): ?>
                <div class="mb-4 p-2 bg-gray-900 text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($comments)): ?> <!-- IF EMPTY SHOW THIS -->
                <p class="text-sm text-gray-400">No comments yet.</p>
            <?php else: ?> <!-- ELSE IF NOT EMPTY SHOW THE REST OF THE COMMENTS -->

                <!-- COMMENTS DISPLAY -->
                <?php foreach ($comments as $comment): ?>
                    
                    <!-- COMMENT BLOCK -->
                    <div class="grid grid-rows-[fit,fit] grid-cols-2 bg-white bg-opacity-[0.09] mb-4 p-2">

                        <!-- COMMENT OWNER & TIMESTAMP -->
                        <div class="col-start-1 row-start-1">
                            <span class="font-bold text-sm text-lime-600 pr-2">
                                <?php echo htmlspecialchars($comment['username']); ?>
                            </span>
                        </div>
                        <div class="col-start-2 row-start-1 text-right font-tahoma">
                            <span class="text-sm text-gray-400">
                                <?php 
                                $timestamp = new DateTime($comment['timestamp']);
                                echo $timestamp->format('M j, Y - H:i:s A'); 
                                ?>
                            </span>
                        </div>
                        
                                    
                        <!-- DELETE BUTTON -->
                        <div class="col-start-2 row-start-2 flex justify-end text-sm gap-2" style="z-index: 10; position: relative;">
                            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && $_SESSION['username'] === $comment['username']): ?>
                                <!-- DELETE FORM -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete_comment" class="text-rose-400 hover:text-rose-200 hover:underline hover:underline-offset-1 bg-transparent border-none cursor-pointer p-0 font-inherit text-sm" style="background: none; border: none; cursor: pointer;" onclick="return confirm('Are you sure you want to delete this comment?')">
                                        delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- COMMENTS -->
                        <div class="text-sm col-start-1 row-start-2 col-span-2">
                            <?php echo $comment['comment'];?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="border-8 border-double border-indigo-600 w-[100%] py-2 my-8 flex flex-wrap items-center justify-center gap-2">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/vim.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/hatemac.jpg">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/hatems.jpg">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/linux.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/mozilla.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/drpepper.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/welcome.gif">

        <img class="w-auto h-8" src="./project-assets/graphics/footer/cs.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/css.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/firefox.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/half-life.gif">

        <img class="w-auto h-8" src="./project-assets/graphics/footer/halloween.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/mspaint.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/linux3.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/ctf.gif">

        <img class="w-auto h-8" src="./project-assets/graphics/footer/steam.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/tor.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/www.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/i2p.gif">

        <img class="w-auto h-8" src="./project-assets/graphics/footer/4x3.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/winxp.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/php.gif">
        <img class="w-auto h-8" src="./project-assets/graphics/footer/scp.gif">
    </div>
        
</body>
</html>
