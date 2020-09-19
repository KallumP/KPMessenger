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
            <a href="searchFriends.php">Friends</a>
        </div>

    </header>

    <div class="SearchContainer">

        <h1>You can search for new friends here!</h1>
        <form action="searchAllUsers.php" method="POST">
            <?php include("includes/search.inc.php"); ?>
        </form>

        <div class="SearchResults">
            <?php

            //checks if the user has searched something
            if (isset($_POST['searchSubmit'])) {


                $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                $sqlUserSearch =
                    "SELECT
                        _user.UserName as 'userName',
                        _user.ID as 'userID'
                    FROM
                        _user
                    WHERE
                        _user.UserName = '$searchInput' OR _user.ID = '$searchInput';";

                $UserSearchResult = mysqli_query($conn, $sqlUserSearch);

                $UserSearchResultCheck = mysqli_num_rows($UserSearchResult);

                if ($UserSearchResultCheck > 0) {

                    while ($row = mysqli_fetch_assoc($UserSearchResult)) {
                        echo "<div class='FriendBox'>";
                        echo "<h2> Username: " . $row['userName'] . " || User ID: " . $row['userID'] . "</h2>";
                        echo "<a href=includes/zChatroomCreate.php?recipientID=" . $row['userID'] . ">Create new chat</a><br>";
                        echo "<a href=includes/zSendFriendRequest.php?recipientID=" . $row['userID'] . ">Send friend request</a>";
                        echo "</div>";
                    }
                }
            }
            ?>
        </div>
    </div>

</body>