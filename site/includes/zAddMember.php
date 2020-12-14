<?php
include 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");

echo "post not passed<br>";

//checks if the page is loaded correctly
if (isset($_GET['ChatroomID'])) {
    echo "chatroom passed<br>";

    if (isset($_POST['UserToAdd'])) {
        echo "toadd passed<br>";

        $UserID = $_SESSION['userID'];
        $ChatroomID = $_GET['ChatroomID'];
        $ToAdd = $_POST['UserToAdd'];

        //statement to get the connector between this user and the chatroom the new user is being added to
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
            echo "user has access to chat<br>";

            //checks if the input was not emtpy
            if ($ToAdd != "") {
                echo "input was not empty<br>";

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
                    echo "user was found<br>";

                    //statement to pull the new users connectoin (to check if there was one)
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
                        echo "member does not exist yet<br>";

                        //statement to add the memeber
                        $sqlAddMember =
                            "INSERT INTO
                                connector (UserID, ChatroomID)
                            VALUES
                                ('$ToAdd', '$ChatroomID');";

                        //adds the member
                        mysqli_query($conn, $sqlAddMember);

                        header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=UserAdded");
                    } else
                        header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=AlreadyAMember");
                } else
                    header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=NotAUser");
            } else
                header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=EmptyInput");
        } else
            header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=NoChatAccess");
    } else
        header("Location: ../chatSettings.php?Note=BadFileAccess&ChatRoomID=" . $ChatroomID);
} else
    header("Location: ../chatSettings.php?Note=BadFileAccess&ChatRoomID=" . $ChatroomID);
