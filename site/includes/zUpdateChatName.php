<?php
include_once 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if send was pressed
if (isset($_POST['submit'])) {

    $UserID = $_SESSION['userID'];
    $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);

    //statement to get the admin connector between this user and the Chatroom the message is being sent to
    $sqlVerifyChatroomConnector =
        "SELECT
            connector.ID
        FROM
            connector
        WHERE
            connector.UserID = '$UserID' AND
            connector.ChatroomID = '$ChatroomID' AND
            connector.Admin = '1';";

    //checks if there was an admin connector found (the user has access to the chat)
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
                    chatroom.ID = '$ChatroomID';";

            mysqli_query($conn, $sqlUpdateName);

            header("Location: ../index.php?ChatroomID=" . $ChatroomID);
        }
    }
}
