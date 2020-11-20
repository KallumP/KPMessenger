<?php
include_once 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");



//checks if send was pressed
if (isset($_POST['submit'])) {

    $UserID = $_SESSION['userID'];
    $ChatRoomID = mysqli_real_escape_string($conn, $_GET['ChatRoomID']);

    //statement to get the connector between this user and the chatroom the message is being sent to
    $sqlVerifyChatroomConnector =
        "SELECT
            connector.ID
        FROM
            connector
        WHERE
            connector.UserID = '$UserID' AND
            connector.ChatroomID = '$ChatRoomID';";



    //checks if there was a connector found (the user has access to the chat)
    if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

        $newName = $_POST['ChatName'];

        //checks if the name was empty
        if ($newName != "") {

            //query to insert the new message
            $sqlUpdateName =
                "UPDATE 
                    chatroom
                SET 
                    chatroom.Name = '$newName'
                WHERE 
                    chatroom.ID = '$ChatRoomID';";

            mysqli_query($conn, $sqlUpdateName);

            // echo $newName;
            header("Location: ../index.php?ChatRoomID=" . $ChatRoomID);
        }
    }
}
