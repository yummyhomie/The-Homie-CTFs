# JUST-USE-NIXOS

## Description
YOOO JUST USE NIXOS ALREADY! IT ONLY MAKES SENSE! 

There's a locked down admin portal somewhere. Can you get access?

## Building & Executing
`docker-compose up`

## Challenge Writeup
This challenge seeks to exploit a CSRF (Cross-Site Request Forgery) vulnerability. TL:DR, by using the comment section, you can reset the admin account password using specially crafted request. The flag is located inside the admin portal once you log in. 

### Step 1. 
Create an account. From here you get access to a *change_password.php* page. Using inspect element or capturing the request traffic, you can piece together the form values and whatnot. 

### Step 2. 
Go to the comment section, try to change your own password. Test around to see if you can reset OTHER users passwords.

### Step 3. 
In the comment section, use one of the commands below to change the admin account password. 

### Step 4. 
Access the admin account reveals an *admin* page! The flag is there. 

### Commands that so far work to complete challenge
```
<form id="csrf" action="change_password.php" method="POST">
    <input type="hidden" name="user" value="Admin">
    <input type="hidden" name="new_password" value="hacked123">
    <input type="hidden" name="confirm_password" value="hacked123">
</form>
<script>document.getElementById('csrf').submit();</script>
```

## Files
**index.php** -> Where all the "magic" happens. Vulnerable comment section is here.<br>
**change_password.php** -> Where the password resets happen. Users can get hints from here to craft their requests to interact with the server.<br>
**/database** -> Where users, passwords and comments are stored, respectively. Uses SQLite to read/write to it.<br>
**/project-assets** -> Nothing important here, images and fonts for website "beautification."<br>
**/tailwind-files** -> Absolutely not necessary, but I used a CSS framework for this website. These are the files for it.<br>

## Flag
`flag{Y0_G0_D0WNL04D_N1X05_R1H6T_N0W_1M_S3R10U$_H0M13!!}`