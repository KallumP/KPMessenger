<?php
include 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");

//checks if the page is loaded correctly
if (isset($_GET['ChatRoomID']) && isset($_GET['UserToMakeAdmin'])) {

    $UserID = $_SESSION['userID'];
    $ChatRoomID = $_GET['ChatRoomID'];
    $ToAdmin = $_GET['UserToMakeAdmin'];

    //statement to get the connector between this user and the chatroom the new user is being added to
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

        //checks if the input was not emtpy
        if ($ToAdmin != "") {

            //statement to pull the user
            $sqlCheckUserExists =
                "SELECT
                    _user.ID
                FROM
                    _user
                WHERE
                    _user.ID = '$ToAdmin';";

            //checks if there was a user found
            if (mysqli_num_rows(mysqli_query($conn, $sqlCheckUserExists)) > 0) {

                //statement to pull the member to make admin's connection (to check if there was one)
                $sqlCheckExistingMember =
                    "SELECT
                        connector.ID
                    FROM
                        connector
                    WHERE
                        connector.UserID = '$ToAdmin' AND 
                        connector.ChatroomID = '$ChatRoomID';";

                //checks if there was a connection found
                if (mysqli_num_rows(mysqli_query($conn, $sqlCheckExistingMember)) != 0) {

                    //statement to admin the member
                    $sqlMakeAdmin =
                        "UPDATE
                            connector
                        SET
                            connector.Admin='1'
                        WHERE
                            connector.UserID = '$ToAdmin';";

                    //adds the member
                    mysqli_query($conn, $sqlMakeAdmin);

                    header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=MadeAdminSuccess");
                } else
                    header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=NotAMember");
            } else
                header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=NotAUser");
        } else
            header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=EmptyInput");
    } else
        header("Location: ../chatSettings.php?ChatRoomID=" . $ChatRoomID . "&Note=NoChatAccess");
} else
    header("Location: ../chatSettings.php?Note=BadFileAccess&ChatRoomID=" . $ChatRoomID);
