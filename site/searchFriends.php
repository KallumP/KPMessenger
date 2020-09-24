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
            <ul>
                <li><a href="index.php">Back to messages</a></li>
                <li><a href="searchAllUsers.php">Search all Users</a></li>
            </ul>
        </div>

    </header>

    <div class="SearchContainer">

        <h1>You can search for your friends here!</h1>

        <form action="searchFriends.php" method="POST">
            <?php include("includes/search.inc.php"); ?>
        </form>

        <div class="SearchResults">

            <?php

            $userID = $_SESSION['userID'];

            //pulls the user's friend's ids
            $sqlAllFriends =
                "SELECT
                   friend.RecipientID AS 'friendID',
                   _user.UserName as 'friendName'
                FROM
                    friend
                LEFT JOIN
                    _user ON friend.RecipientID = _user.ID
                WHERE
                    friend.SenderID = '$userID';";

            $AllFriendsResult = mysqli_query($conn, $sqlAllFriends);
            $AllFriendsResultCheck = mysqli_num_rows($AllFriendsResult);

            //checks if there were any friends
            if ($AllFriendsResultCheck > 0) {

                if (isset($_POST['searchSubmit'])) {

                    //gets the user search input
                    $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                    //checks if the user id or the username was the same as the input search 
                    if ($searchInput != $_SESSION['userID'] || $searchInput != $_SESSION['userName']) {

                        while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                            $friendID = $friendsRow['friendID'];
                            $friendName = $friendsRow['friendName'];

                            $currentSearchedUserID = $friendID;

                            echo "<div class='FriendBox'>";
                            echo "<h2> Username: " . $friendName . "# " . $friendID . "</h2>";
                            echo "<a href=includes/zChatroomCreate.php?recipientID=" . $friendID . ">Create new chat</a><br>";

                            include("includes/findCommonChats.inc.php");

                            echo "</div>";
                        }
                    }
                } else {

                    //loops through each of the pulled friends
                    while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                        $friendID = $friendsRow['friendID'];
                        $friendName = $friendsRow['friendName'];

                        $currentSearchedUserID = $friendID;
                        
                        echo "<div class='FriendBox'>";
                        echo "<h2> Username: " . $friendsRow['friendName'] . "# " . $friendsRow['friendID'] . "</h2>";
                        echo "<a href=includes/zChatroomCreate.php?recipientID=" . $friendsRow['friendID'] . ">Create new chat</a><br>";

                        include("includes/findCommonChats.inc.php");

                        echo "</div>";
                    }
                }
            } else {

                echo "<p>You don't have any friends yet...</p>";
            }

            ?>
        </div>
    </div>

</body>