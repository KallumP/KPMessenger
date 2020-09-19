<?php
include 'includes/dbh.inc.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>KPMessenger</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <header>

        <?php include("includes/accountBanner.inc.php"); ?>

        <div class="Actions">
            <a href="index.php">Back to messages</a>
            <a href="searchAllUsers.php">Search all Users</a>
        </div>

    </header>

    <div class="SearchContainer">
        <form action="searchFriends.php" method="POST">
            <?php include("includes/search.inc.php"); ?>
        </form>

        <div class="SearchResults">
            <?php
            if (isset($_POST['searchSubmit'])) {

                $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                echo "<div class='FriendBox'>";
                echo "<h2> Username: " . $searchInput . "</h2>";
                echo "<a href=includes/zChatroomCreate.php?senderID=1&recipientID=2>Create new chat</a>";
                echo "</div>";
            } else {

                echo "<div class='FriendBox'>";
                echo "<h2> Username: Test Friend</h2>";
                echo "<a href=includes/zChatroomCreate.php?senderID=1&recipientID=2>Create new chat</a>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

</body>