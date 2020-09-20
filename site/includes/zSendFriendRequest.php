<?php
include 'dbh.inc.php';
session_start();

if (isset($_SESSION['userID'])) {

  if (isset($_GET['recipientID'])) {

    //gets the data required for the request
    $senderID = $_SESSION['userID'];
    $recipientID = $_GET['recipientID'];

    //query to 
    $sqlSendFriendRequest =
      "INSERT INTO 
        friendrequest (SenderID, RecipientID)
      VALUES
        ('$senderID', '$recipientID');";

    $SendFriendRequestResult = mysqli_query($conn, $sqlSendFriendRequest);

    header("Location: ../searchAllUsers.php?note=requestSent");
  } else
    header("Location: ../searchAllUsers.php?note=noPost");
} else
  header("Location: ../index.php");
