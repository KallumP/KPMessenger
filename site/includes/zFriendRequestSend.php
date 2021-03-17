<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

if (isset($_GET['recipientID'])) {

  //gets the data required for the request
  $userID = $_SESSION['userID'];
  $recipientID = mysqli_real_escape_string($conn, $_GET['recipientID']);

  //gets the friend connector between this user and the other user
  $sqlGetFriendConnector =
    "SELECT
      friend.ID
    FROM
      friend
    WHERE
      friend.SenderID = '$userID' AND
      friend.RecipientID = '$recipientID';";
  $FriendConnResultCheck = mysqli_num_rows(mysqli_query($conn, $sqlGetFriendConnector));

  //tries to get a friend requests between this and the other user
  $sqlGetFriendRequest =
    "SELECT
      friendrequest.ID
    FROM
      friendrequest
    WHERE
      (friendrequest.RecipientID = '$recipientID' AND
      friendrequest.SenderID = '$userID') OR
      (friendrequest.RecipientID = '$userID' AND
      friendrequest.SenderID = '$recipientID');";
  $FriendRequestResultCheck = mysqli_num_rows(mysqli_query($conn, $sqlGetFriendRequest));

  //checks if there was a friend connection or a friend request
  if ($FriendConnResultCheck == 0 && $FriendRequestResultCheck == 0) {

    //query to send the friend request
    $sqlSendFriendRequest =
      "INSERT INTO 
        friendrequest (SenderID, RecipientID)
      VALUES
        ('$userID', '$recipientID');";

    mysqli_query($conn, $sqlSendFriendRequest);


    header("Location: ../searchAllUsers.php?search=" . $recipientID . "&note=requestSent");
    exit();
  } else {
    header("Location: ../searchAllUsers.php?search=" . $recipientID . "&note=cantSendRequest");
    exit();
  }
} else {
  header("Location: ../searchAllUsers.php?search=" . $recipientID . "&note=noPost");
  exit();
}
