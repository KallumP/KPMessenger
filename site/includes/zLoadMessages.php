<?php
include 'dbh.inc.php';
require_once 'passwordFunctions.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");

//checks if there was a Chatroom posted from the ajax
if (isset($_POST['ChatroomID'])) {

    $passRequired = false;
    $ChatroomID = $_POST['ChatroomID'];
    $UserID = $_SESSION['userID'];

    //check if the user has access to this Chatroom
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

        //check if there is a password required
        $sqlCheckPassword =
            "SELECT
                chatroom.PassHash AS 'PassHash'
            FROM
                chatroom
            WHERE
                chatroom.ID = '$ChatroomID' AND
                NOT chatroom.PassHash = ''";

        $CheckPasswordResult = mysqli_query($conn, $sqlCheckPassword);

        //checks if there was a result (a password is required for this chat)
        if (mysqli_num_rows($CheckPasswordResult) > 0) {

            $passHashRow = mysqli_fetch_assoc($CheckPasswordResult);
            $dbPassHash = $passHashRow['PassHash'];

            if (ValidatePassword($dbPassHash, $ChatroomID))
                $passRequired = true;
        }
    }

    //query to set this user's read status to true
    $sqlUpdateConnectorReadStatus =
        "UPDATE
            connector
        SET
            _Read = 1
        WHERE
            connector.UserID = '$UserID' AND
            connector.ChatroomID = '$ChatroomID';";

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
        echo "<a href='chatSettings.php?ChatroomID=" . $ChatroomID . "'><h1 id='ChatName' class='ChatName WhiteHeader'>" . $chatname .  "</h1></a>";
    }

    //pulls the last 10 messages from this Chatroom
    $sqlAllMessages =
        "SELECT
            message.Content AS 'MessageContent',
            message.SenderID AS 'SenderID',
            message.TimeSent as 'Time'
        FROM
            message
        WHERE
            message.ChatroomID = '$ChatroomID'
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
            $time = date_create($messageRow['Time']);
            $timeNow = new DateTime('now');

            $timeSinceSent = $time->diff($timeNow);

            //https://stackoverflow.com/questions/365191/how-to-get-time-difference-in-minutes-in-php
            $yearsSinceStart = $timeSinceSent->y;
            $monthsSinceStart = $timeSinceSent->m;
            $daysSinceStart = $timeSinceSent->d;
            $hoursSinceStart = $timeSinceSent->h;
            $minutesSinceStart = $timeSinceSent->i;
            $secondsSinceStart = $timeSinceSent->s;

            //if the datetime is not set
            if ($messageRow['Time'] == NULL)
                $formatedTime = " at: unknown...";

            //if its been more than a year
            else if ($yearsSinceStart >= 1)
                $formatedTime = " at: " . date_format($time, "H:i:s d/m/Y");

            //if its been more than a month
            else if ($monthsSinceStart >= 1)
                if ($monthsSinceStart == 1)
                    $formatedTime = ": " . $monthsSinceStart . " month ago";
                else
                    $formatedTime = ": " . $monthsSinceStart . " months ago";

            //if its been more than a day
            else if ($daysSinceStart >= 1)
                if ($daysSinceStart == 1)
                    $formatedTime = ": " . $daysSinceStart . " day ago";
                else
                    $formatedTime = ": " . $daysSinceStart . " days ago";

            //if its been more than an hour
            else if ($hoursSinceStart >= 1)
                if ($hoursSinceStart == 1)
                    $formatedTime = ": " . $hoursSinceStart . " hour ago";
                else
                    $formatedTime = ": " . $hoursSinceStart . " hours ago";

            //if its been more than a min
            else if ($minutesSinceStart >= 1)
                if ($minutesSinceStart == 1)
                    $formatedTime = ": " . $minutesSinceStart . " minute ago";
                else
                    $formatedTime = ": " . $minutesSinceStart . " minutes ago";

            //if its been less than a min
            else
                $formatedTime = ": " . $secondsSinceStart . " seconds ago";


            //checks if the current message was yours
            if ($senderID == $_SESSION['userID']) {

                //outputs the message
                echo "<div class='SentMessage Message'>";

                echo "<p>" .  $message . "</p>";
                echo "<h3 class='WhiteHeader'>Sent by you" .  $formatedTime  . "</h3>";

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
                echo "<h3 class='WhiteHeader'>Sent by " . $senderName .  $formatedTime  . "</h3>";

                echo "</div>";
            }
        }
    } else {

        //there were no messages in the chat
        echo "<h2 class='WhiteHeader'>Looks like there are no messages in this chat.<br>Why not initiate and send one yourself</h3><br><br>";
    }
} else {

    //user had no access
    echo "<h2 class='WhiteHeader'>You don't have access to this chat</h3>";
}
