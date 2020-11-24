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

    if (isset($_GET['UserToRemoveID'])) {
        echo "toremove passed<br>";

        $UserID = $_SESSION['userID'];
        $ChatroomID = $_GET['ChatroomID'];
        $ToRemove = $_GET['UserToRemoveID'];

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
            if ($ToRemove != "") {
                echo "input was not empty<br>";

                //statement to pull the user
                $sqlCheckUserExists =
                    "SELECT
                        _user.ID
                    FROM
                        _user
                    WHERE
                        _user.ID = '$ToRemove';";

                //checks if there was a user found
                if (mysqli_num_rows(mysqli_query($conn, $sqlCheckUserExists)) > 0) {
                    echo "user was found<br>";

                    //statement to pull the users to remove's connection (to check if there was one)
                    $sqlCheckExistingMember =
                        "SELECT
                            connector.ID
                        FROM
                            connector
                        WHERE
                            connector.UserID = '$ToRemove' AND 
                            connector.ChatroomID = '$ChatroomID';";

                    //checks if there was a connection found
                    if (mysqli_num_rows(mysqli_query($conn, $sqlCheckExistingMember)) != 0) {
                        echo "user is a member<br>";

                        //statement to add the memeber
                        $sqlAddMember =
                            "DELETE FROM
                                connector
                            WHERE
                                connector.UserID = '$ToRemove' AND
                                connector.ChatroomID = '$ChatroomID';";

                        //adds the member
                        mysqli_query($conn, $sqlAddMember);

                        header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=UserRemoved");
                    } else
                        header("Location: ../chatSettings.php?ChatRoomID=" . $ChatroomID . "&Note=NotAMember");
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
