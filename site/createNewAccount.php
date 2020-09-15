<?php
include 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>KPMessager</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <header>

        <?php include("includes/login.inc.php"); ?>

        <div class="Actions">
            <a href="searchFriends.php">Friends</a>
            <a href="searchAllUsers.php">Search all Users</a>
            <a href="index.php">Back to messages</a>
        </div>

    </header>

    <div class="CreateAccount">

        <form action="includes/zAccountCreate.inc.php" method="POST">
            
            <label for="GuruImage">Image path of the Guru's profile</label><br>
            <input class="username_txt" type="text" name="username" placeholder="Enter username">
            <input class="password_txt" type="password" name="password" placeholder="Enter password">
            <input class="password_txt" type="password" name="password-validate" placeholder="Enter password">
            <button class="login_btn" type="submit" name="login"> Submit </button>

        </form>
    </div>

</body>