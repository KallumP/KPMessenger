<?php
include_once 'dbh.inc.php';
session_start();

//checks to see if the user has logged in
if (isset($_SESSION['userID'])) {

  //checks if a friend request was sent
  if (isset($_GET['requestID'])) {

    $requestID = $_GET['requestID'];
    $userID = $_SESSION['userID'];

    //query to get the sender and recipient
    //(makes sure that the current user is who is accepting the request)
    $sqlFriendRequest =
      "SELECT
        friendrequest.SenderID AS 'senderID'
      FROM
        friendrequest
      WHERE
        friendrequest.ID = '$requestID' AND friendrequest.RecipientID = '$userID';";

    $result = mysqli_query($conn, $sqlFriendRequest);

    //checks if there were any results
    if (mysqli_num_rows($result) > 0) {

      $friendRequstRow = mysqli_fetch_assoc($result);

      $senderID = $friendRequstRow['senderID'];

      //creates the senders friend relation
      $sqlFriendRelation1 =
        "INSERT INTO 
          friend (SenderID, RecipientID)
        VALUES
          ('$senderID', '$userID');";

      mysqli_query($conn, $sqlFriendRelation1);

      //creates the recipients friend relation
      $sqlFriendRelation2 =
        "INSERT INTO 
          friend (SenderID, RecipientID)
        VALUES
          ('$userID', '$senderID');";

      mysqli_query($conn, $sqlFriendRelation2);


      //goes to the page that creates the chatroom and sends across the friend request sender's id
      header("Location: zChatroomCreate.php?recipientID=$senderID&friendRequestID=$requestID");
    } else {
      header("Location: ../index.php");
      exit();
    }
  }
} else {

  header("Location: ../index.php");
  exit();
}
