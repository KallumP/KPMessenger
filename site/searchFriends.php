<?php
include 'includes/dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>KPMessenger</title>
    <link href="style.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script>
        let GetNotes = function() {
            $("#Banner").load("includes/zLoadNotes.php", {

            });
        }

        let GetRecentMessages = function() {

            //gets the chat id from the url, and assigns it -1 if there wasn't one
            let URLChatroomID;

            <?php if (isset($_GET['ChatroomID'])) { ?>
                URLChatroomID = <?php echo $_GET['ChatroomID'] ?>;
            <?php } else { ?>
                URLChatroomID = -1;
            <?php } ?>

            $("#RecentMessages").load("includes/zLoadRecents.php", {

                ChatroomID: URLChatroomID
            });
        }

        let SetChatBoxHeight = function() {

            //http://tutorialshares.com/dynamically-change-div-height-browser-window-resize/

            //in px
            let bannerHeight = 210;

            $('#RecentMessages').css({
                'max-height': ($(window).height() - bannerHeight + 150) + 'px',
                'height': ($(window).height() - bannerHeight + 150) + 'px'
            });

        }

        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {

            GetNotes();
            GetRecentMessages();

        });

        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();
            GetRecentMessages();

        }, 4000);

        $(window).resize(function() { // On resize
            SetChatBoxHeight();
        });
    </script>
</head>

<body>
    <div class=Container>
        <header>

            <div id="Banner" class="AccountBanner">

            </div>

            <div class="Actions">
                <ul>
                    <li><a href="index.php">Messages</a></li>
                    <li><a class="Current">Friends</a></li>
                    <li><a href="searchAllUsers.php">Search all Users</a></li>
                </ul>
            </div>

        </header>

        <div id="RecentMessages" class="RecentMessages">

        </div>
        <div class="Content">
            <div class="SearchContainer">

                <h1 class='WhiteHeader'>You can search for your friends here!</h1>

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

                                    echo "<div class='UserBox'>";
                                    echo "<h2 class='WhiteHeader'> Username: " . $friendName . "# " . $friendID . "</h2>";
                                    echo "<a href=includes/zChatroomCreate.php?recipientID=" . $friendID . "><p>Create new chat</p></a><br>";

                                    include("includes/zLoadCommonChats.php");

                                    echo "</div>";
                                }
                            }
                        } else {

                            //loops through each of the pulled friends
                            while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                                $friendID = $friendsRow['friendID'];
                                $friendName = $friendsRow['friendName'];

                                $currentSearchedUserID = $friendID;

                                echo "<div class='UserBox'>";
                                echo "<h2 class='WhiteHeader'> Username: " . $friendsRow['friendName'] . "# " . $friendsRow['friendID'] . "</h2>";
                                echo "<a href=includes/zChatroomCreate.php?recipientID=" . $friendsRow['friendID'] . "><p>Create new chat</p></a>";

                                include("includes/zLoadCommonChats.php");

                                echo "</div>";
                            }
                        }
                    } else {

                        echo "<p>You don't have any friends yet...</p>";
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</body>