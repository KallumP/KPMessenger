<?php
ob_start();
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

        let CheckIfPassStillRequired = function() {
            //gets the chat id from the url, and assigns it -1 if there wasn't one
            let URLChatroomID;

            <?php if (isset($_GET['ChatroomID'])) { ?>
                URLChatroomID = <?php echo $_GET['ChatroomID'] ?>;
            <?php } else { ?>
                URLChatroomID = -1;
            <?php } ?>

            $("#Redirect").load("includes/zCheckIfPassRequired.php", {

                ChatroomID: URLChatroomID
            });
        }

        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {

            GetNotes();
            GetRecentMessages();
            SetChatBoxHeight();
            CheckIfPassStillRequired();

        });


        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();
            GetRecentMessages();
            CheckIfPassStillRequired();

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
                    <li><a class="Current">Messages</a></li>
                    <li><a href="searchFriends.php">Friends</a></li>
                    <li><a href="searchAllUsers.php">Search all Users</a></li>
                </ul>
            </div>

        </header>

        <div id="RecentMessages" class="RecentMessages">

        </div>

        <div class="Content">
            <div class='ChatPassword'>
                <?php
                if (isset($_GET['ChatroomID'])) {

                    if (isset($_GET['Note'])) {
                        $note = $_GET['Note'];

                        echo "<div class='Notes'>";
                        if ($note == "wrong")
                            echo "<h3>That password was wrong</h3>";
                        else if ($note == "changed")
                            echo "<h3>The password for this chat has changed</h3>";
                        else if ($note == "PassAlreadySet")
                            echo "<h3>The password was already set <br> (maybe just before you tried to set it)</h3>";
                        echo "</div>";

                        echo "<br><br><br>";
                    }

                    $ChatroomID = $_GET['ChatroomID'];
                ?>


                    <form action='includes/zValidateChatPassword.php?ChatroomID=<?php echo $ChatroomID; ?>' method="POST" autocomplete="off">
                        <label class='WhiteHeader' for='passwordEntry'>This chat needs a password to enter</label><br>
                        <input class='passwordEntry BorderInputs' type="password" name='passwordEntry' placeholder='Enter password' rows='1' autofocus></input>
                        <button class='passwordSubmit BorderInputs' type='submit' name='passwordSend'> Unlock </button>
                    </form>
            </div>

            <div id="Redirect">
            </div>

        <?php
                } else {

                    header("Location: index.php");
                    ob_end_flush();
                    exit();
                }

        ?>
        </div>
    </div>
</body>

<?php
ob_end_flush();
