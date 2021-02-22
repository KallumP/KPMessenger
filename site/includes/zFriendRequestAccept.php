<?php
include_once 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//checks if a friend request id was in the url
if (isset($_GET['requestID'])) {

  $requestID = mysqli_real_escape_string($conn, $_GET['requestID']);
  $userID = $_SESSION['userID'];

  //query to get the senderid
  //(makes sure that the current user is who is request was sent to)
  $sqlFriendRequest =
    "SELECT
      friendrequest.SenderID AS 'senderID'
    FROM
      friendrequest
    WHERE
      friendrequest.ID = '$requestID' AND 
      friendrequest.RecipientID = '$userID';";

  $friendRequestresult = mysqli_query($conn, $sqlFriendRequest);

  //checks if there were any results
  if (mysqli_num_rows($friendRequestresult) > 0) {

    $friendRequstRow = mysqli_fetch_assoc($friendRequestresult);

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

    //creates the connector for this user
    $sqlConnector1 =
      "INSERT INTO 
        connector (UserID, ChatroomID, Admin)
      VALUES
        ('$userID', '$ChatroomID', '1');";
    mysqli_query($conn, $sqlConnector1);

    //creates the connector for the user who sent the friend request
    $sqlConnector2 =
      "INSERT INTO 
        connector (UserID, ChatroomID, Admin)
      VALUES
        ('$senderID', '$ChatroomID', '1');";
    mysqli_query($conn, $sqlConnector2);


    //gets the sender's user name
    $sqlGetSenderName =
      "SELECT
        _user.UserName as 'senderName'
      FROM
        _user
      WHERE
        _user.ID = '$senderID';";
    $senderNameResult = mysqli_query($conn, $sqlGetSenderName);
    $sendernameResultRow = mysqli_fetch_assoc($senderNameResult);

    //creates the new chat name
    $userName = $_SESSION['userName'];
    $senderName = $sendernameResultRow['senderName'];
    $newChatName = $senderName . ", " . $userName;

    //updates the Chatroom name
    $sqlChatroomNameUpdate =
      "UPDATE 
        chatroom
      SET 
        chatroom.Name = '$newChatName'
      WHERE
        chatroom.ID = '$ChatroomID';";
    mysqli_query($conn, $sqlChatroomNameUpdate);


    //deletes the friend request
    $sqlDeleteFriendRequest =
      "DELETE FROM
        friendrequest
      WHERE
        friendrequest.ID='$requestID';";
    mysqli_query($conn, $sqlDeleteFriendRequest);

    header("Location: ../index.php");
    exit();
  } else {
    header("Location: ../index.php");
    exit();
  }
}
