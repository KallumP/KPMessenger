<?php


//gets the connector between the member being added and the chatroom being added to
$sqlGetMembers =
    "SELECT
        connector.ID
    FROM
        connector
    WHERE
        connector.ChatroomID = '$ChatRoomID' AND
        connector.UserID = '$memberID';";

$RecentMessagesResult = mysqli_query($conn, $sqlGetRecentMessages);
