<?php
include_once 'dbh.inc.php';
session_start();



if (isset($_POST['messageSend'])) {

    $messageContent = $_POST['messageEntry'];

    //checks if the message was empty
    if ($messageContent != "") {


        $UserID = $_SESSION['userID'];
        $ChatroomID = $_GET['chatRoomID'];

        //statement to get the connector between this user and the chatroom the message is being sent to
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



            $sqlInsertMessage =
                "INSERT INTO
                message (SenderID, ChatroomID, Content)
            VALUES
                ('$UserID', '$ChatroomID', '$messageContent');";

            mysqli_query($conn, $sqlInsertMessage);

            header("Location: ../index.php?ChatRoomID=" . $ChatroomID);
        }
    }
}
