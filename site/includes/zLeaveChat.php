<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: ../login.php");

if (isset($_GET['ChatroomID'])) {

    $UserID = $_SESSION['userID'];
    $ChatroomID = $_GET['ChatroomID'];

    //query to delete this user from the specified Chatroom
    $sqlDeleteMemberConnector =
        "DELETE FROM
            connector
        WHERE
            connector.UserID = '$UserID' AND
            connector.ChatroomID = '$ChatroomID';";

    //deletes the user
    mysqli_query($conn, $sqlDeleteMemberConnector);



    //query to get all members from the Chatroom
    $sqlGetAllChatMembers =
        "SELECT
            connector.ID
        FROM
            connector
        WHERE
            connector.ChatroomID = '$ChatroomID';";

    //checks if there were no members left
    if (mysqli_num_rows(mysqli_query($conn, $sqlGetAllChatMembers)) <= 0) {

        //query to delete this Chatroom
        $sqlDeleteChatroom =
            "DELETE FROM
                chatroom
            WHERE
                chatroom.ID = '$ChatroomID';";

        //deletes the Chatroom
        mysqli_query($conn, $sqlDeleteChatroom);
    } else {

        //query to get all admin connectors from this chat
        $sqlGetChatAdmins =
            "SELECT
                connector.ID
            FROM
                connector
            WHERE
                connector.ChatroomID = '$ChatroomID' AND
                connector.Admin = '1';";

        //checks if there were no admin members left
        if (mysqli_num_rows(mysqli_query($conn, $sqlGetChatAdmins)) <= 0) {

            //query to set the oldest (min connector.id) connector to admin
            $sqlMakeNextMemberAdmin =
                "UPDATE
                    connector
                SET
                    connector.Admin = '1'
                WHERE
                    connector.ID = (
                        SELECT 
                            MIN(connector.ID) 
                        FROM
                            connector
                        WHERE
                            connector.ChatroomID = '$ChatroomID');";

            //https://stackoverflow.com/questions/7604893/how-do-i-select-an-entire-row-which-has-the-largest-id-in-the-table

            //sets the next user to admin
            mysqli_query($conn, $sqlMakeNextMemberAdmin);
        }
    }
}

header("Location: ../index.php");
