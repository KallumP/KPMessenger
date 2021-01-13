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

    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 2px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #000000;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #ffffff;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555555;
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script>
        let GetNotes = function() {
            $("#Banner").load("includes/zLoadNotes.php", {
                AccountOptions: "true"
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
                'max-height': ($(window).height() - bannerHeight + 150) + 'px'
            });

            $('#RecentMessages').css({
                'max-height': ($(window).height() - bannerHeight + 150) + 'px'
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

        <?php if (isset($_SESSION['userName'])) { ?>

            <div class="Content">
                <div class="AccountOptions">

                    <div class="AccountNameAndID CenterObjects">
                        <h1 class='WhiteHeader'>Logged in as: <?php echo $_SESSION['userName']  ?> # ID: <?php echo $_SESSION['userID']  ?></h1>
                    </div>

                    <div class="Logout CenterObjects">
                        <a class='highRiskLink' href="includes/zLogout.php">Log out</a>
                    </div>
                </div>
            </div>

        <?php } ?>



    </div>
</body>

<?php  ?>