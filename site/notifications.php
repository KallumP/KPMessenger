<?php
include 'includes/dbh.inc.php';
include 'includes/functions.php';
session_start();

CheckLoggedIn($conn, false);
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
                CurrentPage: "notes"
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
            SetChatBoxHeight();

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
                    <li><a href="searchFriends.php">Friends</a></li>
                    <li><a href="searchAllUsers.php">Search all Users</a></li>
                </ul>
            </div>

        </header>

        <div id="RecentMessages" class="RecentMessages">

        </div>

        <div class="Content">

            <div class="Notifications">

                <h1 class='WhiteHeader title'>Notifications </h1>

                <div class='FriendRequests'>
                    <h2 class='WhiteHeader'>Friend Requests </h2>

                    <?php

                    $userID = $_SESSION['userID'];

                    //gets all the friendrequests for this user
                    $sqlGetFriendRequests =
                        "SELECT
                            friendrequest.ID AS 'requestID',
                            friendrequest.SenderID AS 'senderID'
                        FROM 
                            friendrequest
                        WHERE
                            friendrequest.RecipientID = '$userID';";

                    //queries the database
                    $getFriendRequestsResult = mysqli_query($conn, $sqlGetFriendRequests);

                    //If there were friendrequests
                    if (mysqli_num_rows($getFriendRequestsResult) > 0) {

                        //echo friend requests title

                        //loops through each friend request
                        while ($friendRequestsRow = mysqli_fetch_assoc($getFriendRequestsResult)) {

                            //saves the neccessary data for this friend request
                            $senderID = $friendRequestsRow['senderID'];
                            $requestID = $friendRequestsRow['requestID'];

                            //query to get the name of the friend request sender
                            $sqlGetRequestSenderName =
                                "SELECT
                                _user.UserName as 'senderName'
                            FROM
                                _user
                            WHERE
                                _user.ID = '$senderID';";

                            //queries the database
                            $getSenderNameResult = mysqli_query($conn, $sqlGetRequestSenderName);
                            $friendRequestsSenderNameRow = mysqli_fetch_assoc($getSenderNameResult);

                            //displays the friend request
                            echo "<div class='Request'>";
                            echo "<p>Friend request from: " . $friendRequestsSenderNameRow['senderName'] . "</p>";
                            echo "<a href='includes/zFriendRequestAccept.php?requestID=" . $requestID . "'><p>Accept</p></a>";
                            echo "<a href='includes/zFriendRequestDeny.php?requestID=" . $requestID . "'><p>Deny</p></a>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>:( You don't have any friend requests... Just like real life eh?</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>