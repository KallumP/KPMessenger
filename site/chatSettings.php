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

        let GetMembers = function() {
            $("#ChatMembers").load("includes/zLoadMembers.php", {

            });
        }


        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {

            GetNotes();

            //sets up the event listner for the add member button
            $("#AddMember").click(function(e) {
                $.ajax({
                    type: "POST",
                    url: "includes/zAddMembers",
                    data: {
                        ChatRoomID: <?php echo $_GET['ChatRoomID'] ?>,
                        UserToAdd: document.getElementById("MemberToAdd").innerHTML
                    }
                });
            });

        });

        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();

        }, 4000);
    </script>
</head>

<body>
    <div class="ChatSettings">

        <?php

        //checks if a chat was selected
        if (isset($_GET['ChatRoomID'])) {

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

                //(only show the add members part if current user is admin)
                echo "<label for='MemberToAdd'>Add new members to this chat (use their unique code (found after the #)):</label><br>";
                echo "<input id='MemberToAdd' class='ChatNameInput BorderInputs' type='text' name='MemberToAdd'> </input>";
                echo "<button id='AddMember' class='Send BorderInputs' type='submit' name='submit'> Add </button>";


                echo "<div id='ChatMembers' class='ChatMembers'>";

                include "includes/zLoadMembers.php";

                echo "</div>";
            }
        }

        ?>
    </div>
</body>