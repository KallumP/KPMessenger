<?php
include 'includes/dbh.inc.php';
require_once 'includes/passwordFunctions.php';
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

        $(window).resize(function() { // On resize
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
                    <li><a class="Current">Messages</a></li>
                    <li><a href="searchFriends.php">Friends</a></li>
                    <li><a href="searchAllUsers.php">Search all Users</a></li>
                </ul>
            </div>

        </header>

        <div id="RecentMessages" class="RecentMessages">

        </div>

        <div id="Content" class="Content">
            <div class="ChatSettings">

                <?php

                //checks if a chat was selected
                if (isset($_GET['ChatroomID'])) {

                    //checks if the Chatroom was left empty
                    if ($_GET['ChatroomID'] != "") {

                        $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);
                        $UserID = $_SESSION['userID'];

                        //check if the user has access to this Chatroom and gets the admin status at the same time
                        $sqlUserConnector =
                            "SELECT 
                                connector.Admin as 'AdminStatus'
                            FROM  
                                connector
                            WHERE
                                connector.UserID = '$UserID' AND 
                                connector.ChatroomID = '$ChatroomID';";

                        //turns result into an array of results
                        $userConnectorResult = mysqli_query($conn, $sqlUserConnector);

                        //if the user has access to this chat (the query returned a connector)
                        if (mysqli_num_rows($userConnectorResult)) {

                            //check if a password is required
                            $passwordCheck = RequirePassword($ChatroomID, $conn);

                            $noteToAdd = "ChatroomID=" . $ChatroomID . "&";

                            if (isset($_GET['note'])) {
                                if ($_GET['note'] == "PassAlreadySet") {
                                    $noteToAdd .= "note=PassAlreadySet";
                                }
                            } else if ($passwordCheck == "WrongSavedPassword") {
                                $noteToAdd .= "note=changed&";
                            }


                            //deal with the validation response
                            if ($passwordCheck == "WrongSavedPassword" || $passwordCheck == "NoSavedPassword") {

                                $urlToGoTo = "enterChatPassword.php?" . $noteToAdd;
                                echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
                                exit();
                            }

                            //gets the admin status from the array of results
                            $adminStatus = mysqli_fetch_assoc($userConnectorResult)['AdminStatus'];

                            $sqlGetChatName =
                                "SELECT
                                    chatroom.name AS 'ChatName',
                                    chatroom.PassHash AS 'PassHash'
                                FROM
                                    chatroom
                                WHERE 
                                    chatroom.ID = $ChatroomID;";

                            $ChatNameResult = mysqli_query($conn, $sqlGetChatName);

                            //checks if there was a result
                            if (mysqli_num_rows($ChatNameResult) > 0) {

                                //saves the row of data
                                $ChatNameRow = mysqli_fetch_assoc($ChatNameResult);

                                $chatName = $ChatNameRow['ChatName'];
                                $passHash = $ChatNameRow['PassHash'];

                                //the name of the chat
                                echo "<a href='index.php?ChatroomID=" . $ChatroomID . "'> Back </a>";
                                echo "<h1 class='WhiteHeader'>Settings for " . $chatName  . " </h1>";

                                //checks if there was an error message
                                if (isset($_GET['note'])) {
                                    $note = $_GET['note'];

                                    echo "<div class='Notes'>";
                                    if ($note == "UserAdded")
                                        echo "<h3>User added successfully</h3>";
                                    else if ($note == "UserRemoved")
                                        echo "<h3>User removed successfully</h3>";
                                    else if ($note == "PassSuccess")
                                        echo "<h3>Password added successfully (all messages has been encrypted)</h3>";
                                    else if ($note == "NotAMember")
                                        echo "<h3>That user is not a member of this chat</h3>";
                                    else if ($note == "AlreadyAMember")
                                        echo "<h3>That user was already a member of this chat</h3>";
                                    else if ($note == "NotAUser")
                                        echo "<h3>No users in our database had that id</h3>";
                                    else if ($note == "NotAFriend")
                                        echo "<h3>That user is not currently your friend</h3>";
                                    else if ($note == "EmptyInput")
                                        echo "<h3>Your input was empty</h3>";
                                    else if ($note == "NoChatAccess")
                                        echo "<h3>You don't have the rights do that</h3>";
                                    else if ($note == "BadFileAccess")
                                        echo "<h3>You need to add members using the interfaces on this page</h3>";
                                    echo "</div>";
                                }


                                if ($adminStatus == 1) {

                                    //the input to change the chat name
                                    echo "<form action='includes/zUpdateChatName.php?ChatroomID=" . $ChatroomID . "' method='POST' class='ChatName'>";
                                    echo "<label class='WhiteHeader' for='ChatName'>Chat name:</label><br>";
                                    echo "<input class='BorderInputs' type='text' name='ChatName' value='" . $ChatNameRow['ChatName'] . "'> </input>";
                                    echo "<button id='UpdateChatName' class='BorderInputs' type='submit' name='submit'> Update </button>";
                                    echo "</form>";
                                    echo "<br><br><br>";
                                }

                                if ($adminStatus == 1) {
                                    if ($passHash == "") {

                                        //the input to add a password
                                        echo "<form action='includes/zAddPassword.php?ChatroomID=" . $ChatroomID . "' method='POST' id='AddMemberForm' class='ChatName'>";
                                        echo "<label class='WhiteHeader' for='PasswordToAdd'>Add a password to this chat (Password strength is all up to you):</label><br>";
                                        echo "<input id='PasswordToAdd' class='BorderInputs' type='text' name='PasswordToAdd'> </input>";
                                        echo "<button id='AddPassword' class='BorderInputs' type='submit' name='submit'>Add</button>";
                                        echo "</form>";
                                    } else {
                                        echo "<div class='CenterObjects'>";
                                        echo "<a class='highRiskLink' href='includes/zRemovePassword.php?ChatroomID=" . $ChatroomID . "'>Remove password</a>";
                                        echo "</div>";
                                    }
                                }
                                echo "<br><br><br>";
                                echo "<br><br><br>";

                                if ($adminStatus == 1) {

                                    //the input to add new members
                                    echo "<form action='includes/zAddMember.php?ChatroomID=" . $ChatroomID . "' method='POST' id='AddMemberForm' class='ChatName'>";
                                    echo "<label class='WhiteHeader' for='UserToAdd'>Add new members to this chat (use their unique code (found after the #)):</label><br>";
                                    echo "<input id='UserToAdd' class='BorderInputs' type='text' name='UserToAdd'> </input>";
                                    echo "<button id='AddMember' class='BorderInputs' type='submit' name='submit'> Add </button>";
                                    echo "</form>";
                                }

                                //all the members of the chat
                                echo "<div class='ChatMembers'>";
                                OutputMembers($conn, $ChatroomID, $adminStatus);
                                echo "</div>";

                                //leave chat button
                                echo "<div class='LeaveChat CenterObjects'>";
                                echo "<a class='highRiskLink' href='includes/zLeaveChat.php?ChatroomID=" . $ChatroomID . "'>Leave Chat</a>";
                                echo "</div>";
                            }
                        } else echo "<meta http-equiv='refresh' content='0;url=index.php'>";
                    } else echo "<meta http-equiv='refresh' content='0;url=index.php'>";
                } else echo "<meta http-equiv='refresh' content='0;url=index.php'>";
                ?>
            </div>
        </div>
    </div>
</body>