<?php
include 'dbh.inc.php';
require_once 'passwordFunctions.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, false);
?>
<h1 class='WhiteHeader'>Recent Messages</h1>

<?php
$UserID = $_SESSION['userID'];

//checks if this page was opened properly
if (isset($_POST['ChatroomID'])) {

  $ChatroomID = $_POST['ChatroomID'];

  //gets all the Chatroom id's and names that the user is a part of
  $sqlGetRecentMessages =
    "SELECT 
      chatroom.Name AS 'ChatName',
      chatroom.ID AS 'ChatroomID'
    FROM
      chatroom
    LEFT JOIN connector ON chatroom.ID = connector.ChatroomID
    WHERE 
      connector.UserID = '$UserID'
    ORDER BY 
      chatroom.LastMessageTime DESC;";

  $RecentMessagesResult = mysqli_query($conn, $sqlGetRecentMessages);

  //checks if there was any Chatrooms found with recent messages
  if (mysqli_num_rows($RecentMessagesResult) > 0) {

    //loops through each recent message
    while ($recentMessageRow = mysqli_fetch_assoc($RecentMessagesResult)) {

      $currentChatroomID = $recentMessageRow['ChatroomID'];
      $currentChatroomName = $recentMessageRow['ChatName'];
      $lastMessageID = "";
      $lastMessage = "";
      $messagePreview = "";

      //gets the ID of the last sent message in this chat
      $sqlGetLastMessageID =
        "SELECT
          MAX(ID) as ID
        FROM
          message
        WHERE
          message.ChatroomID = '$currentChatroomID';";

      $lastMessageIDResult = mysqli_query($conn, $sqlGetLastMessageID);

      //checks if an id was found
      if (mysqli_num_rows($lastMessageIDResult) > 0) {

        $lastMessageIDRow = mysqli_fetch_assoc($lastMessageIDResult);

        //saves the id
        $lastMessageID = $lastMessageIDRow['ID'];

        //gets the last sent message using the id
        $sqlGetLastMessage =
          "SELECT 
            message.Content AS 'Content'
          FROM
            message
          WHERE
            message.ID = '$lastMessageID';";

        $lastMessageResult = mysqli_query($conn, $sqlGetLastMessage);

        //checks if there was a message found with that id
        if (mysqli_num_rows($lastMessageResult) > 0) {

          $lastMessageRow = mysqli_fetch_assoc($lastMessageResult);
          $lastMessage = $lastMessageRow['Content'];

          //check if a password is required
          $passwordCheck = RequirePassword($currentChatroomID, $conn);

          //deals with if there was a password required (regardless of if the saved pass was right)
          if ($passwordCheck == "WrongSavedPassword" || $passwordCheck == "NoSavedPassword" || $passwordCheck == "RightSavedPassword")
            $lastMessage  = "Encrypted message";


          //checks if there 
          if ($lastMessage != "") {

            //checks if the message was more than 20 characters long and then saves the preview
            if (strlen($lastMessage) > 20)
              $messagePreview = substr($lastMessage, 0, 40);
            else
              $messagePreview = $lastMessage;
          } else {
            $messagePreview = "No messages yet";
          }
        }
      }


      //gets the read status of the chat
      $sqlReadStatus =
        "SELECT
          connector._Read AS 'Status'
        FROM
          connector
        WHERE
          connector.UserID = '$UserID' AND
          connector.ChatroomID = '$currentChatroomID';";

      $readStatusResult = mysqli_query($conn, $sqlReadStatus);

      //checks if there was a status pulled (there always should be)
      if (mysqli_num_rows($readStatusResult)) {
        $readRow = mysqli_fetch_assoc($readStatusResult);

        //checks if the chat was not read
        if ($readRow['Status'] == 0) {
          //outputs the  Chatroom
          echo "<div class='MessagePrev Unread'>";

          //checks if this recent chat is the same as the currently opened chat
          if ($ChatroomID ==  $currentChatroomID)
            echo "<a class='Current' href=index.php?ChatroomID=" . $currentChatroomID . ">";
          else
            echo "<a href=index.php?ChatroomID=" . $currentChatroomID . ">";

          echo "<h2 class='WhiteHeader'>" . $currentChatroomName . "</h2>";
          echo "<p>" . $messagePreview . "</p>";
          echo "</a>";
          echo "</div>";
        } else {

          //outputs the  Chatroom
          echo "<div class='MessagePrev'>";

          //checks if this recent chat is the same as the currently opened chat
          if ($ChatroomID ==  $currentChatroomID)
            echo "<a class='Current'>";
          else
            echo "<a href=index.php?ChatroomID=" . $currentChatroomID . ">";

          echo "<h2 class='WhiteHeader'>" . $currentChatroomName . "</h2>";
          echo "<p>" . $messagePreview . "</p>";
          echo "</a>";
          echo "</div>";
        }
      }
    }
  }
}
?>