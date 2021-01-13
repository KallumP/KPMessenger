<?php
//query to pull all Chatroom IDs that the current searched user is a part of
$sqlChatCheck =
  "SELECT
    chatroom.ID as 'ChatroomID'
  FROM
    chatroom
  LEFT JOIN connector ON chatroom.ID = connector.ChatroomID
  WHERE
    connector.UserID = '$currentSearchedUserID';";

$ChatResult = mysqli_query($conn, $sqlChatCheck);
$ChatResultCheck = mysqli_num_rows($ChatResult);

//checks if there were any chats that the searched user was a part of
if ($ChatResultCheck > 0) {

  $commonchat = false;

  while ($ChatResultRow = mysqli_fetch_assoc($ChatResult)) {

    //saves the users's id
    $userID = $_SESSION['userID'];

    //saves the current chat id of the current searched user
    $currentSearchedUserCurrentChatroomID = $ChatResultRow['ChatroomID'];

    //query to pull all Chatrooms that the current searched user is a part of that the user is also a part of
    $sqlCommonChatCheck =
      "SELECT
        chatroom.ID as 'ChatroomID',
        chatroom.Name as 'chatName'
      FROM
        chatroom
      LEFT JOIN connector ON chatroom.ID = connector.ChatroomID
      WHERE
        connector.UserID = '$userID' AND chatroom.ID = '$currentSearchedUserCurrentChatroomID';";

    $CommonChatResult = mysqli_query($conn, $sqlCommonChatCheck);
    $CommonChatResultCheck = mysqli_num_rows($CommonChatResult);

    //checks if there were any common chats
    if ($CommonChatResultCheck > 0) {

      //loops through each common chat
      while ($CommonChatResultRow = mysqli_fetch_assoc($CommonChatResult)) {
        $commonchat = true;
        echo "<a href=index.php?ChatroomID=" . $CommonChatResultRow['ChatroomID'] . "><p>Open chat: " . $CommonChatResultRow['chatName'] . "</p></a>";
      }
    }
  }
  if (!$commonchat) {

    echo "<p>No common chats</p>";
  }
} else {
  echo "This user has no chats";
}
