<?php
include 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");


//checks if the page is loaded correctly
if (isset($_GET['ChatroomID'])) {


    $UserID = $_SESSION['userID'];
    $ChatroomID = $_GET['ChatroomID'];

    //checks if the input  and Chatroomid was not emtpy
    if ($ChatroomID != "") {

        //statement to get the admin connector between this user and the Chatroom the new user is being added to
        $sqlVerifyChatroomConnector =
            "SELECT
                connector.ID
            FROM
                connector
            WHERE
                connector.UserID = '$UserID' AND 
                connector.Admin = '1' AND
                connector.ChatroomID = '$ChatroomID';";

        //checks if there was a connector found
        if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

            //query to reset the password
            $sqlRemovePassword =
                "UPDATE
                    chatroom
                SET
                    chatroom.PassHash = ''
                WHERE
                    chatroom.ID = $ChatroomID;";

            //calls the query
            mysqli_query($conn, $sqlRemovePassword);

            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID);
        }
    }
}


//remove password
