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
  $sqlSendFriendRequest =
    "INSERT INTO 
      friendrequest (SenderID, RecipientID)
    VALUES
      ('$senderID', '$recipientID');";

  $SendFriendRequestResult = mysqli_query($conn, $sqlSendFriendRequest);

  header("Location: ../searchAllUsers.php?note=requestSent");
  exit();
} else {
  header("Location: ../searchAllUsers.php?note=noPost");
  exit();
}
