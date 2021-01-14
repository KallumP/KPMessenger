<?php
include_once 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
    header("Location: login.php");

//checks if a friend request id was in the url
if (isset($_GET['requestID'])) {

    $requestID = $_GET['requestID'];
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
}
