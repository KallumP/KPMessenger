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
            let URLChatRoomID;

            <?php if (isset($_GET['ChatRoomID'])) { ?>
                URLChatRoomID = <?php echo $_GET['ChatRoomID'] ?>;
            <?php } else { ?>
                URLChatRoomID = -1;
            <?php } ?>

            $("#RecentMessages").load("includes/zLoadRecents.php", {

                ChatroomID: URLChatRoomID
            });
        }

        let SetChatBoxHeight = function() {

            //http://tutorialshares.com/dynamically-change-div-height-browser-window-resize/

            //in px
            let bannerHeight = 210;

            $('#Messages').css({
                'max-height': ($(window).height() - bannerHeight) + 'px'
            });

            $('#RecentMessages').css({
                'max-height': ($(window).height() - bannerHeight + 150) + 'px'
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
                    <li><a href="searchFriends.php">Friends</a></li>
                    <li><a class="Current">Search all Users</a></li>
                </ul>
            </div>

        </header>

        <div id="RecentMessages" class="RecentMessages">

        </div>

        <div class="Content">
            <div class="SearchContainer">

                <h1 class='WhiteHeader'>You can search for new friends here!</h1>
                <form action="searchAllUsers.php" method="POST">
                    <?php include("includes/search.inc.php"); ?>
                </form>

                <div class="SearchResults">
                    <?php

                    //checks if the user has searched something
                    if (isset($_POST['searchSubmit'])) {

                        //gets the user search input
                        $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                        //checks if the user id and the username was the same as the input search 
                        if ($searchInput != $_SESSION['userID'] && $searchInput != $_SESSION['userName']) {

                            //pulls the users that were searched for
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

                            //checks to see if any users were pulled
                            if ($UserSearchResultCheck > 0) {

                                //loops through each searched user
                                while ($UserSearchResultRow = mysqli_fetch_assoc($UserSearchResult)) {

                                    //saves the current searched user id
                                    $currentSearchedUserID = $UserSearchResultRow['userID'];

                                    echo "<div class='UserBox'>";
                                    echo "<h2 class='WhiteHeader'> Username: " . $UserSearchResultRow['userName'] . "# " . $UserSearchResultRow['userID'] . "</h2>";
                                    echo "<a href=includes/zFriendRequestSend.php?recipientID=" . $UserSearchResultRow['userID'] . "><p>Send friend request</p></a>";
                                    echo "<a href=includes/zChatroomCreate.php?recipientID=" . $UserSearchResultRow['userID'] . "><p>Create new chat</p></a>";

                                    include("includes/findCommonChats.inc.php");

                                    echo "</div>";
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>