<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if the page is loaded correctly
if (isset($_GET['ChatroomID'])) {

    if (isset($_GET['UserToRemoveID'])) {

        $UserID = $_SESSION['userID'];
        $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);
        $ToRemove = mysqli_real_escape_string($conn, $_GET['UserToRemoveID']);

        //checks if the input  and Chatroomid was not emtpy
        if ($ToRemove != "" && $ChatroomID != "") {

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

                //statement to remove the memeber
                $sqlRemoveMember =
                    "DELETE FROM
                        connector
                    WHERE
                        connector.UserID = '$ToRemove' AND
                        connector.ChatroomID = '$ChatroomID';";

                //removes the member
                mysqli_query($conn, $sqlRemoveMember);

                header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=UserRemoved");
                exit();
            } else {
                header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=NoChatAccess");
                exit();
            }
        } else {
            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=EmptyInput");
            exit();
        }
    } else {
        header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=BadFileAccess");
        exit();
    }
} else {
    header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=BadFileAccess");
    exit();
}
