<?php
include_once 'dbh.inc.php';
require_once 'passwordFunctions.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");

//checks if the send message was pressed
if (isset($_POST['messageSend'])) {

    $messageContent = $_POST['messageEntry'];

    //checks if the message was empty
    if ($messageContent != "") {


        $UserID = $_SESSION['userID'];
        $ChatroomID = $_GET['ChatroomID'];

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

            $passRequired = false;
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

            if ($passRequired)
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
