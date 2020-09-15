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
            <a href="index.php">Back to messages</a>
            <a href="searchAllUsers.php">Search all Users</a>
        </div>

    </header>

    <form action="searchAllUsers.php" method="POST">
        <input class="search_txt" type="text" name="search" placeholder="Enter a username or ID of a friend to search">
        <button class="search_btn" type="submit" name="searchSubmit"> Search </button>
    </form>

    <?php
    if (isset($_POST['searchSubmit'])) {

        $searchInput = mysqli_real_escape_string($conn, $_POST['search']);
        echo $searchInput;
    }

    //display all friends if no post
    //display only search results if post
    ?>


</body>