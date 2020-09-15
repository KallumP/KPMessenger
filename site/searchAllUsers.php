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
            <a href="index.php">Back to messages</a>
        </div>

    </header>

    <form action="searchAllUsers.php" method="POST">
        <input class="search_txt" type="text" name="search" placeholder="Enter a username or ID to search">
        <button class="search_btn" type="submit" name="searchSubmit"> Search </button>
    </form>

    <?php
    if (isset($_POST['searchSubmit'])) {

        $searchInput = mysqli_real_escape_string($conn, $_POST['search']);
        echo $searchInput;
    }
    ?>
</body>