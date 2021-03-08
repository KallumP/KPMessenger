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

        let SetDivHeights = function() {

            //http://tutorialshares.com/dynamically-change-div-height-browser-window-resize/

            //in px
            let bannerHeight = 210;
            let heightToSet = ($(window).height() - bannerHeight + 150) + 'px';

            $('#RecentMessages').css({
                'max-height': heightToSet,
                'height': heightToSet
            });

            $('#Content').css({
                'max-height': heightToSet,
                'height': heightToSet
            });

        }

        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {

            GetNotes();
            GetRecentMessages();
            SetDivHeights();

        });

        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();
            GetRecentMessages();

        }, 4000);

        // On resize
        $(window).resize(function() {
            SetDivHeights();
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

        <div id="Content" class="Content Border">
            <div class="SearchContainer">

                <?php
                if (isset($_GET['note'])) {
                    $note = $_GET['note'];

                    echo "<div class='Notes'>";
                    if ($note == "friendRemoveSuccess")
                        echo "<h3>That friend has been removed successfully</h3>";
                    else if ($note == "friendNotFriend")
                        echo "<h3>That user was not your friend</h3>";
                    else if ($note == "userDoesntExist")
                        echo "<h3>That user does not exist</h3>";
                    else if ($note == "requestSent")
                        echo "<h3>Friend request sent</h3>";
                    else if ($note == "noPost")
                        echo "<h3>Please make requests using the links below</h3>";
                    else if ($note == "cantSearchForSelf")
                        echo "<h3>You can't search for yourself</h3>";
                    echo "</div>";
                }
                ?>

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

                        //checks that the user searched something
                        if (isset($_POST['searchSubmit'])) {

                            $searchInput = mysqli_real_escape_string($conn, $_POST['search']);

                            //checks if the user id or the username was the same as the input search 
                            if ($searchInput != $_SESSION['userID'] && $searchInput != $_SESSION['userName']) {

                                while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                                    //checks if the current friend is the one being searched for
                                    if ($friendsRow['friendID'] == $searchInput || $friendsRow['friendName'] == $searchInput) {

                                        OutputSearchedUser($conn, $friendsRow['friendID'], $friendsRow['friendName'], $userID, "searchFriends");
                                    }
                                }
                            } else {
                                echo "<p>You Can't search for yourself!</p>";
                            }
                        } else {

                            while ($friendsRow = mysqli_fetch_assoc($AllFriendsResult)) {

                                OutputSearchedUser($conn, $friendsRow['friendID'], $friendsRow['friendName'], $userID, "searchFriends");
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