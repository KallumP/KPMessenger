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

        let GetMembers = function() {

            <?php if (isset($_GET['ChatRoomID'])) { ?>
                $('#ChatMembers').load('includes/zLoadMembers.php', {
                    ChatroomID: <?php echo $_GET['ChatRoomID'] ?>
                });
            <?php } ?>
        }


        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {
            GetNotes();
            GetRecentMessages();
            GetMembers();
        });


        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();
            GetRecentMessages();

        }, 4000);
    </script>
</head>

<body>

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

    <div class="ChatSettings">

        <?php

        //checks if a chat was selected
        if (isset($_GET['ChatRoomID'])) {

            //checks if the chatroom was left empty
            if ($_GET['ChatRoomID'] != "") {

                //check if the current user has access to this chat

                $ChatRoomID = mysqli_real_escape_string($conn, $_GET['ChatRoomID']);

                $sqlGetChatName =
                    "SELECT
                    chatroom.name AS 'ChatName'
                FROM
                    chatroom
                WHERE 
                    chatroom.ID = $ChatRoomID;";

                $ChatNameResult = mysqli_query($conn, $sqlGetChatName);

                //checks if there was a result
                if (mysqli_num_rows($ChatNameResult) > 0) {

                    //saves the row of data
                    $ChatNameRow = mysqli_fetch_assoc($ChatNameResult);

                    echo "<a href='index.php?ChatRoomID=" . $ChatRoomID . "'> Back </a>";
                    echo "<h1>Settings for " . $ChatNameRow['ChatName'] . " </h1>";

                    echo "<form action='includes/zUpdateChatName.php?ChatRoomID=" . $ChatRoomID . "' method='POST' class='ChatName'>";

                    echo "<label for='ChatName'>Chat name:</label><br>";
                    echo "<input class='ChatNameInput BorderInputs' type='text' name='ChatName' value='" . $ChatNameRow['ChatName'] . "'> </input>";
                    echo "<button id='UpdateChatName' class='Send BorderInputs' type='submit' name='submit'> Update </button>";

                    echo "</form>";
                    echo "<br><br><br>";

                    echo "<form action='includes/zAddMember.php?ChatroomID=" . $ChatRoomID . "' method='POST' id='AddMemberForm' class='ChatName'>";

                    echo "<label for='UserToAdd'>Add new members to this chat (use their unique code (found after the #)):</label><br>";
                    echo "<input id='UserToAdd' class='ChatNameInput BorderInputs' type='text' name='UserToAdd'> </input>";
                    echo "<button id='AddMember' class='Send BorderInputs' type='submit' name='submit'> Add </button>";

                    echo "</form>";


                    //checks if there was an error message
                    if (isset($_GET['Note'])) {
                        $note = $_GET['Note'];

                        echo "<div class='Notes'>";
                        if ($note == "UserAdded")
                            echo "<h3>User added successfully</h3>";
                        else if ($note == "UserRemoved")
                            echo "<h3>User removed successfully</h3>";
                        else if ($note == "NotAMember")
                            echo "<h3>That user is not a member of this chat</h3>";
                        else if ($note == "AlreadyAMember")
                            echo "<h3>That user was already a member of this chat</h3>";
                        else if ($note == "NotAUser")
                            echo "<h3>No users in our database had that id</h3>";
                        else if ($note == "EmptyInput")
                            echo "<h3>Your input was empty</h3>";
                        else if ($note == "NoChatAccess")
                            echo "<h3>You don't have access to this chat</h3>";
                        else if ($note == "BadFileAccess")
                            echo "<h3>You need to add members using the interfaces on this page</h3>";
                        echo "</div>";
                    }


                    echo "<div id='ChatMembers' class='ChatMembers'>";

                    echo "</div>";
                }
            } else header("Location: index.php");
        } else header("Location: index.php");

        ?>
    </div>
</body>