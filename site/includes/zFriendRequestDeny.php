<?php
include_once 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if a friend request id was in the url
if (isset($_GET['requestID'])) {

    $requestID = mysqli_real_escape_string($conn, $_GET['requestID']);
    $userID = $_SESSION['userID'];

    //query to delete the friend request that was passed in that was for this user
    $sqlDeleteFriendRequest =
        "DELETE FROM
            friendrequest
        WHERE
            friendrequest.ID = '$requestID' AND
            friendrequest.RecipientID = '$userID';";

    mysqli_query($conn, $sqlDeleteFriendRequest);

    header("Location: ../notifications.php");
    exit();
}
