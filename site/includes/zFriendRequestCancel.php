<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

if (isset($_GET['recipientID'])) {

    //gets the data required for the request
    $senderID = $_SESSION['userID'];
    $recipientID = mysqli_real_escape_string($conn, $_GET['recipientID']);

    //query to send the friend request
    $sqlCancelFriendRequest =
        "DELETE 
            friendrequest 
        FROM
            friendrequest
        WHERE
            friendrequest.SenderID = '$senderID' AND
            friendrequest.RecipientID = '$recipientID';";

    mysqli_query($conn, $sqlCancelFriendRequest);

    header("Location: ../searchAllUsers.php?search=" . $recipientID . "&note=requestCancel");
    exit();
} else {
    header("Location: ../searchAllUsers.php?search=" . $recipientID . "&note=noPost");
    exit();
}
