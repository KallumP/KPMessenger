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

    <h1>You can search for your friends here!</h1>

        <form action="searchFriends.php" method="POST">
            <?php include("includes/search.inc.php"); ?>
        </form>

        <div class="SearchResults">
            <?php

            $userID = $_SESSION['userID'];

            //pulls the users friends
            $sqlAllFriends =
                "SELECT
                    _user.ID as 'userID'
                FROM
                    _user
                    LEFT JOIN friend ON _user.ID = friend.ID
                WHERE
                    friend.SenderID = '$userID';";

            $AllFriendsResult = mysqli_query($conn, $sqlAllFriends);
            $AllFriendsResultCheck = mysqli_num_rows($AllFriendsResult);

            if ($AllFriendsResultCheck > 0) {

                if (isset($_POST['searchSubmit'])) {

                    //gets the user search input
                    $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                    //checks if the user id and the username was the same as the input search 
                    if ($searchInput != $_SESSION['userID'] && $searchInput != $_SESSION['userName']) {

                        while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                            $friendID = $friendsRow['userID'];

                            //pulls the users that were searched for
                            $sqlUserSearch =
                                "SELECT
                               _user.UserName as 'userName',
                               _user.ID as 'userID'
                           FROM
                               _user
                           WHERE
                               _user.UserName = '$searchInput' OR _user.ID = '$searchInput' AND _user.ID = '$friendID';";

                            $UserSearchResult = mysqli_query($conn, $sqlUserSearch);
                            $UserSearchResultCheck = mysqli_num_rows($UserSearchResult);

                            //checks to see if any users were pulled
                            if ($UserSearchResultCheck > 0) {

                                //loops through each searched user
                                while ($UserSearchResultRow = mysqli_fetch_assoc($UserSearchResult)) {

                                    //saves the current searched user id
                                    $currentSearchedUserID = $UserSearchResultRow['userID'];

                                    echo "<div class='FriendBox'>";
                                    echo "<h2> Username: " . $UserSearchResultRow['userName'] . "# " . $UserSearchResultRow['userID'] . "</h2>";
                                    echo "<a href=includes/zChatroomCreate.php?recipientID=" . $UserSearchResultRow['userID'] . ">Create new chat</a><br>";

                                    include("includes/findCommonChats.inc.php");

                                    echo "</div>";
                                }
                            }
                        }
                    }
                } else {


                    while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                        echo "<div class='FriendBox'>";
                        echo "<h2> Username: " . $friendsRow['userName'] . "# " . $friendsRow['userID'] . "</h2>";
                        echo "<a href=includes/zChatroomCreate.php?recipientID=" . $friendsRow['userID'] . ">Create new chat</a><br>";

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