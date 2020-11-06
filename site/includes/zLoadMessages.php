<?php

// echo "ajax loads <br>";

include 'dbh.inc.php';
session_start();
// echo "databse and session started<br>";


//checks if there was a chatroom and a user posted from the ajax
if (isset($_POST['ChatroomID']) && isset($_POST['UserID'])) {

    // echo "post verified<br>";

    $ChatroomID = $_POST['ChatroomID'];
    //  mysqli_real_escape_string($conn, $_GET['ChatRoomID']);
    $UserID = $_POST['UserID'];

    //check if the user has access to this chatroom
    $sqlUserConnector =
        "SELECT 
            connector.ID
        FROM  
            connector
        WHERE
            connector.UserID = '$UserID';";

    //if the user has access to this chat (the query returned a connector)
    if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector))) {

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
            echo "<h1 class='ChatName'>" . $chatname .  "</h1>";
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
            DESC;";

        $AllMessagesResult = mysqli_query($conn, $sqlAllMessages);
        $AllMessagesResultCheck = mysqli_num_rows($AllMessagesResult);

        //checks if there were any messages
        if ($AllMessagesResultCheck > 0) {

            //---------
            //stores all the messages in an array
            while ($messageRow = mysqli_fetch_assoc($AllMessagesResult))
                $messages[] = $messageRow;

            //reverses the array (so that the newests message is at the bottom)
            $messages = array_reverse($messages, true);

            foreach ($messages as $messageRow) {
                //---------end

                $senderID = $messageRow['SenderID'];
                $message = wordwrap($messageRow['MessageContent'], 70, "<br>");

                //checks if the current message was yours
                if ($senderID == $_SESSION['userID']) {

                    echo "<div class='SentMessage Message'>";

                    echo "<p>" .  $message . "</p>";
                    echo "<h3> Sent by you</h3>";

                    echo "</div>";
                } else {


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
        echo "<h2>You don't have access to this chat</h3>";
    }
} else {
    echo "post not working";
}
