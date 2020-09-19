<?php
//query to pull all chatrooms that the current searched user is a part of
$sqlChatCheck =
  "SELECT
    chatroom.ID as 'chatID'
  FROM
    chatRoom
  LEFT JOIN connector ON chatRoom.ID = connector.ChatRoomID
  WHERE
    connector.ID = '$currentSearchedUserID';";

$ChatResult = mysqli_query($conn, $sqlChatCheck);
$ChatResultCheck = mysqli_num_rows($ChatResult);

//checks if there were any chats that the searched user was a part of
if ($ChatResultCheck > 0) {

  while ($ChatResultRow = mysqli_fetch_assoc($ChatResult)) {

    //saves the users's id
    $userID = $_SESSION['userID'];

    //saves the current chat id of the current searched user
    $currentSearchedUserCurrentChatID = $ChatResultRow['chatID'];

    //query to pull all chatrooms that the current searched user is a part of that the current searched user is also a part of
    $sqlCommonChatCheck =
      "SELECT
        chatroom.ID as 'chatID',
        chatroom.Name as 'chatName'
      FROM
        chatroom
      LEFT JOIN connector ON chatRoom.ID = connector.ChatRoomID
      WHERE
        connector.ID = '$userID' AND chatroom.ID = $currentSearchedUserCurrentChatID;";

    $CommonChatResult = mysqli_query($conn, $sqlCommonChatCheck);
    $CommonChatResultCheck = mysqli_num_rows($CommonChatResult);

    //checks if there were any common chats
    if ($ChatResultCheck > 0) {

      //loops through each common chat
      while ($ChatResultRow = mysqli_fetch_assoc($ChatResult)) {

        echo "<a href=index.php?chatID=" . $ChatResultRow['chatID'] . ">Join chat: " . $ChatResultRow['chatName'] . "</a><br>";
      }
    } else {

      echo "No common chats left";
    }
  }
} else {
  echo "That user has no chats";
}
