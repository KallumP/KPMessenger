<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");

//checks if there was a chatroom and a user posted from the ajax
if (isset($_POST['ChatroomID'])) {

    $ChatroomID = $_POST['ChatroomID'];
    $UserID = $_SESSION['userID'];

    //check if the user has access to this chatroom
    $sqlUserConnector =
        "SELECT 
            connector.ID
        FROM  
            connector
        WHERE
            connector.UserID = '$UserID' AND 
            connector.ChatroomID = '$ChatroomID';";

    //if the user has access to this chat (the query returned a connector)
    if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector))) {

        //query to set this user's read status to true
        $sqlUpdateConnectorReadStatus =
            "UPDATE
                connector
            SET
                _Read = 1
            WHERE
                connector.UserID = '$UserID' AND
                connector.ChatRoomID = '$ChatroomID';";

        mysqli_query($conn, $sqlUpdateConnectorReadStatus);

        $sqlChatName =
            "SELECT
              chatroom.Name AS 'Name'
            FROM
              chatroom
            WHERE
              chatroom.ID = '$ChatroomID'";

        $ChatNameResult = mysqli_query($conn, $sqlChatName);

        //checks if there were any messages
        if (mysqli_num_rows($ChatNameResult) > 0) {
            $chatname = mysqli_fetch_assoc($ChatNameResult)['Name'];
            echo "<a href='chatSettings.php?ChatRoomID=" . $ChatroomID . "'><h1 id='ChatName' class='ChatName'>" . $chatname .  "</h1></a>";
        }

        //pulls the last 10 messages from this chatroom
        $sqlAllMessages =
            "SELECT
              message.Content AS 'MessageContent',
              message.SenderID as 'SenderID'
            FROM
              message
            WHERE
              message.ChatRoomID = '$ChatroomID'
            ORDER BY
              message.ID
            DESC
            LIMIT
              10;";

        $AllMessagesResult = mysqli_query($conn, $sqlAllMessages);
        $AllMessagesResultCheck = mysqli_num_rows($AllMessagesResult);

        //checks if there were any messages
        if ($AllMessagesResultCheck > 0) {

            //stores all the messages in an array
            while ($messageRow = mysqli_fetch_assoc($AllMessagesResult))
                $messages[] = $messageRow;

            //reverses the array (so that the newests message is at the bottom)
            $messages = array_reverse($messages, true);

            foreach ($messages as $messageRow) {


                $senderID = $messageRow['SenderID'];
                $message = wordwrap($messageRow['MessageContent'], 70, "<br>");

                //checks if the current message was yours
                if ($senderID == $_SESSION['userID']) {

                    //outputs the message
                    echo "<div class='SentMessage Message'>";

                    echo "<p>" .  $message . "</p>";
                    echo "<h3> Sent by you</h3>";

                    echo "</div>";
                } else {

                    //gets the user name of the sender
                    $sqlGetSender =
                        "SELECT
                            _user.Username AS 'SenderName'
                        FROM
                            _user
                        WHERE
                            _user.ID = '$senderID';";

                    $GetSenderResult = mysqli_query($conn, $sqlGetSender);
                    $GetSenderResultCheck = mysqli_num_rows($GetSenderResult);

                    $senderName = "";

                    if ($GetSenderResultCheck > 0)
                        $senderName = mysqli_fetch_assoc($GetSenderResult)['SenderName'];
                    else
                        $senderName = "Unknown User";

                    //outputs the message
                    echo "<div class='RecievedMessage Message'>";

                    echo "<p>" .  $message . "</p>";
                    echo "<h3>Sent by " . $senderName . "</h3>";

                    echo "</div>";
                }
            }
        } else {

            //there were no messages in the chat
            echo "<h2>Looks like there are no messages in this chat.<br>Why not initiate and send one yourself</h3><br><br>";
        }
    } else {

        //user had no access
        echo "<h2>You don't have access to this chat</h3>";
    }
}
