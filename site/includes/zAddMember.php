<?php
include 'dbh.inc.php';
include 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if the page is loaded correctly
if (isset($_GET['ChatroomID'])) {

    if (isset($_POST['UserToAdd'])) {

        $UserID = $_SESSION['userID'];
        $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);
        $ToAdd = mysqli_real_escape_string($conn, $_POST['UserToAdd']);

        //statement to get the connector between this user and the Chatroom the new user is being added to (only if the user is admin)
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

            //checks if the input was not emtpy
            if ($ToAdd != "") {

                //statement to pull the user
                $sqlCheckUserExists =
                    "SELECT
                        _user.ID
                    FROM
                        _user
                    WHERE
                        _user.ID = '$ToAdd';";

                //checks if there was a user found
                if (mysqli_num_rows(mysqli_query($conn, $sqlCheckUserExists)) > 0) {

                    //statement to pull the new users connection (to check if there was one)
                    $sqlCheckExistingMember =
                        "SELECT
                            connector.ID
                        FROM
                            connector
                        WHERE
                            connector.UserID = '$ToAdd' AND 
                            connector.ChatroomID = '$ChatroomID';";

                    //checks if there was a connection found
                    if (mysqli_num_rows(mysqli_query($conn, $sqlCheckExistingMember)) == 0) {

                        //statement to add the memeber
                        $sqlAddMember =
                            "INSERT INTO
                                connector (UserID, ChatroomID)
                            VALUES
                                ('$ToAdd', '$ChatroomID');";

                        //adds the member
                        mysqli_query($conn, $sqlAddMember);

                        header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=UserAdded");
                        exit();
                    } else {
                        header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=AlreadyAMember");
                        exit();
                    }
                } else {
                    header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=NotAUser");
                    exit();
                }
            } else {
                header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=EmptyInput");
                exit();
            }
        } else {
            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&note=NoChatAccess");
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
