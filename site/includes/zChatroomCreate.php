<?php
include_once 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (isset($_SESSION['userID'])) {

  //checks if the sender was 
  if (isset($_GET['recipientID'])) {

    //creates the chatroom
    $sqlCreateChat =
      "INSERT INTO 
        chatroom (Name)
      VALUES
        ('temp');";


    //queries and checks if the query worked
    if ($conn->query($sqlCreateChat) === TRUE)

      //gets the id of the chat
      $chatID = $conn->insert_id;

    $userID = $_SESSION['userID'];
    $recipientID = $_GET['recipientID'];

    //creates the connector for user 1
    $sqlConnector1 =
      "INSERT INTO 
        connector (UserID, ChatroomID)
      VALUES
        ('$userID', '$chatID' );";
    mysqli_query($conn, $sqlConnector1);

    //creates the connector for user 2
    $sqlConnector2 =
      "INSERT INTO 
        connector (UserID, ChatroomID)
      VALUES
        ('$recipientID', '$chatID' );";
    mysqli_query($conn, $sqlConnector2);

    //creates the connector for user 2
    $sqlChatroomNameUpdate =
      "UPDATE 
        chatroom
      SET 
        chatroom.Name = '$chatID'
      WHERE
        chatroom.ID = '$chatID';";
    mysqli_query($conn, $sqlChatroomNameUpdate);

    //checks if a friend request id was passed
    if (isset($_GET['friendRequestID'])) {
      $requstID = $_GET['friendRequestID'];

      //deletes the friend request
      $sqlDeleteFriendRequest =
        "DELETE FROM
          friendrequest
        WHERE
          friendrequest.ID='$requstID';";

      mysqli_query($conn, $sqlDeleteFriendRequest);
    }
    //alter the chatroom name to the chatid

    header("Location: ../index.php");
    exit();
  } else {

    header("Location: ../index.php");
    exit();
  }
} else {

  header("Location: ../index.php");
  exit();
}
