<?php
include_once 'dbh.inc.php';
session_start();

//checks if the user has logged in
if (!isset($_SESSION['userID']))
  header("Location: login.php");

//checks if the recipientID was in the url (the id of the user who will be include in this chat)
if (isset($_GET['recipientID'])) {

  //creates the Chatroom
  $sqlCreateChat =
    "INSERT INTO 
      chatroom (Name)
    VALUES
      ('temp');";

  //queries and checks if the query worked
  if ($conn->query($sqlCreateChat) === TRUE)

    //gets the id of the chat
    $ChatroomID = $conn->insert_id;

  $userID = $_SESSION['userID'];
  $recipientID = $_GET['recipientID'];


  //creates the connector for this user (adnim)
  $sqlConnector1 =
    "INSERT INTO 
      connector (UserID, ChatroomID, Admin)
    VALUES
      ('$userID', '$ChatroomID', '1');";
  mysqli_query($conn, $sqlConnector1);

  //creates the connector for other user (not adnim)
  $sqlConnector2 =
    "INSERT INTO 
      connector (UserID, ChatroomID)
    VALUES
      ('$recipientID', '$ChatroomID');";
  mysqli_query($conn, $sqlConnector2);


  //gets the other user's user name
  $sqlGetRecipientName =
    "SELECT
      _user.UserName as 'senderName'
    FROM
      _user
    WHERE
      _user.ID = '$recipientID';";
  $recipientNameResult = mysqli_query($conn, $sqlGetRecipientName);
  $recipientnameResultRow = mysqli_fetch_assoc($recipientNameResult);

  //creates the new chat name
  $userName = $_SESSION['userName'];
  $recipientName = $recipientnameResultRow['senderName'];
  $newChatName = $userName . ", " . $recipientName;

  //updates the Chatroom name
  $sqlChatroomNameUpdate =
    "UPDATE 
      chatroom
    SET 
      chatroom.Name = '$newChatName'
    WHERE
      chatroom.ID = '$ChatroomID';";
  mysqli_query($conn, $sqlChatroomNameUpdate);


  header("Location: ../index.php");
  exit();
} else {

  header("Location: ../index.php");
  exit();
}
