<?php
include 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");


//checks if the page is loaded correctly
if (isset($_GET['ChatRoomID'])) {

    if (isset($_GET['UserToRemoveID'])) {

        $UserID = $_SESSION['userID'];
        $ChatRoomID = $_GET['ChatRoomID'];
        $ToRemove = $_GET['UserToRemoveID'];

        //checks if the input  and chatroomid was not emtpy
        if ($ToRemove != "" && $ChatRoomID != "") {

            //statement to get the admin connector between this user and the chatroom the new user is being added to
            $sqlVerifyChatroomConnector =
                "SELECT
                    connector.ID
                FROM
                    connector
                WHERE
                    connector.UserID = '$UserID' AND 
                    connector.Admin = '1' AND
                    connector.ChatroomID = '$ChatRoomID';";

            //checks if there was a connector found
            if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

                //statement to remove the memeber
                $sqlRemoveMember =
                    "DELETE FROM
                        connector
                    WHERE
                        connector.UserID = '$ToRemove' AND
                        connector.ChatroomID = '$ChatRoomID';";

                //removes the member
                mysqli_query($conn, $sqlRemoveMember);

                header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=UserRemoved");
            } else
                header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=NoChatAccess");
        } else
            header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=EmptyInput");
    } else
        header("Location: ../chatSettings.php?Note=BadFileAccess&ChatRoomID=" . $ChatRoomID);
} else
    header("Location: ../chatSettings.php?Note=BadFileAccess&ChatRoomID=" . $ChatRoomID);
