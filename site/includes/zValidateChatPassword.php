<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if this script was opened properly
if (isset($_POST['passwordSend'])) {

    $ChatroomID = $_GET['ChatroomID'];
    $inputPassword = $_POST['passwordEntry'];
    $hashedInput = strtoupper(hash('sha256', $inputPassword));

    //query to get any result from this chatroom with that that password
    $sqlCheckHashedPass =
        "SELECT
            chatroom.ID
        FROM
            chatroom
        WHERE
            chatroom.ID = '$ChatroomID' AND
            chatroom.PassHash = '$hashedInput';";

    //checks if that hashed passsword was found
    if (mysqli_num_rows(mysqli_query($conn, $sqlCheckHashedPass))) {

        //sets the session variable for this chat
        $_SESSION['ChatroomID_' . $ChatroomID] = $inputPassword;

        header("Location: ../index.php?ChatroomID=" . $ChatroomID);
    } else {

        header("Location: ../enterChatPassword.php?ChatroomID=" . $ChatroomID . "&Note=wrong");
    }
}
