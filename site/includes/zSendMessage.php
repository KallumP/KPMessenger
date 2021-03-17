<?php
include_once 'dbh.inc.php';
require_once 'passwordFunctions.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if the send message was pressed
if (isset($_POST['messageSend'])) {

    $messageContent = mysqli_real_escape_string($conn, $_POST['messageEntry']);

    //checks if the message was empty
    if ($messageContent != "") {


        $UserID = $_SESSION['userID'];
        $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);

        //statement to get the connector between this user and the Chatroom the message is being sent to
        $sqlVerifyChatroomConnector =
            "SELECT
                connector.ID
            FROM
                connector
            WHERE
                connector.UserID = '$UserID' AND 
                connector.ChatroomID = '$ChatroomID';";

        //checks if there was a connector found
        if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

            //check if a password is required
            $passwordCheck = RequirePassword($ChatroomID, $conn);

            //deal with the validation response
            if ($passwordCheck == "WrongSavedPassword") {

                $urlToGoTo = "enterChatPassword.php?ChatroomID=" . $ChatroomID . "?note=changed";
                echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
                exit();
            } else if ($passwordCheck == "NoSavedPassword") {

                $urlToGoTo = "enterChatPassword.php?ChatroomID=" . $ChatroomID;
                echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
                exit();
            } else if ($passwordCheck == "RightSavedPassword")
                $messageContent = EncryptString($messageContent, $_SESSION['ChatroomID_' . $ChatroomID]);

            //gets the time this message was sent
            $sendTime = date("Y-m-d H:i:s");

            //query to insert the new message
            $sqlInsertMessage =
                "INSERT INTO
                    message (SenderID, ChatroomID, Content, TimeSent)
                VALUES
                    ('$UserID', '$ChatroomID', '$messageContent', '$sendTime');";

            mysqli_query($conn, $sqlInsertMessage);


            //updates the Chatroom with the message send time
            $sqlUpdateChatroomTime =
                "UPDATE
                    chatroom
                SET
                    LastMessageTime = '$sendTime'
                WHERE
                    chatroom.ID = '$ChatroomID';";

            mysqli_query($conn, $sqlUpdateChatroomTime);

            //query to set all connected users' read status to false
            $sqlUpdateConnectorReadStatus =
                "UPDATE
                    connector
                SET
                    _Read = 0
                WHERE
                    connector.ChatroomID = '$ChatroomID';";

            mysqli_query($conn, $sqlUpdateConnectorReadStatus);

            header("Location: ../index.php?ChatroomID=" . $ChatroomID);
            exit();
        }
    }
}

header("Location: ../index.php");
exit();
