<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: ../login.php");

//checks if the page is loaded correctly
if (isset($_GET['ChatroomID']) && isset($_POST['PasswordToAdd'])) {

    $UserID = $_SESSION['userID'];
    $ChatroomID = $_GET['ChatroomID'];
    $ToAdd = $_POST['PasswordToAdd'];

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

    //checks if there was an admin connector found
    if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

        //checks if the input was not emtpy
        if ($ToAdd != "") {

            $hashedPasswordInput = strtoupper(hash('sha256', $ToAdd));

            //add passhash to the database
            $sqlAddPassHash =
                "UPDATE
                    chatroom
                SET
                    chatroom.PassHash = '$hashedPasswordInput'
                WHERE    
                    chatroom.ID = '$ChatroomID';";

            mysqli_query($conn, $sqlAddPassHash);

            //saves the password
            $_SESSION['ChatroomID_' . $ChatroomID] = $inputPassword;

            //encrypt all messages

            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&Note=PassSuccess");
        } else
            //return to chat settings saying you can't have an empty password
            header("Location: ../chatSettings.php?Note=EmptyPass");
    } else
        //don't have (admin) access to the chat
        header("Location: ../chatSettings.php?Note=NoChatAccess");
} else
    //opened the file wrong
    header("Location: ../chatSettings.php?Note=BadFileAccess");
