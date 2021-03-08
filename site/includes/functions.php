<?php
function CheckLoggedIn($conn, $includes)
{

    if ($includes == true)
        $urlToGoTo = "../login.php";
    else
        $urlToGoTo = "login.php";


    //checks if there wasn't any logged in variables saved
    if (!isset($_SESSION['userID'])) {

        echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
    } else {

        $userID = $_SESSION['userID'];
        $userName = $_SESSION['userName'];

        //check the database's to make sure a user of that id and name exists
        $sqlGetThisUser =
            "SELECT
                _user.ID
             FROM
                _user
            WHERE
                _user.ID = '$userID' AND
                _user.UserName = '$userName';";

        //checks if the logged in variables didn't correlate to a user in the database
        if (mysqli_num_rows(mysqli_query($conn, $sqlGetThisUser)) == 0) {
            echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
        }
    }
}

function OutputSearchedUser($conn, $searchedID, $searchedName, $userID, $redirect)
{

    //gets the friend connector between this user and the searched user
    $sqlGetFriendConnector =
        "SELECT
            friend.ID
        FROM
            friend
        WHERE
            friend.SenderID = '$userID' AND
            friend.RecipientID = '$searchedID';";
    $FriendConnResultCheck = mysqli_num_rows(mysqli_query($conn, $sqlGetFriendConnector));

    //tries to get a friend request from this user
    $sqlGetFriendRequest =
        "SELECT
            friendrequest.ID
        FROM
            friendrequest
        WHERE
            friendrequest.RecipientID = '$userID' AND
            friendrequest.SenderID = '$searchedID';";
    $FriendRequestResultCheck = mysqli_num_rows(mysqli_query($conn, $sqlGetFriendRequest));

    echo "<div class='UserBox'>";
    echo "<h2 class='WhiteHeader'> Username: " . $searchedName . "# " . $searchedID . "</h2>";

    if ($FriendConnResultCheck == 0)
        if ($FriendRequestResultCheck == 0)
            echo "<a href=includes/zFriendRequestSend.php?recipientID=" . $searchedID . "><p>Send friend request</p></a>";
        else
            echo "<a href='notifications.php'><p>You have a friend request from this user, click here to see it in the notifications page</p></a>";
    else
        echo "<a href=includes/zRemoveFriend.php?toRemoveID=" . $searchedID . "&redir=" . $redirect . "><p class='highRiskLink'>Remove Friend</p></a>";

    echo "<a href=includes/zChatroomCreate.php?recipientID=" . $searchedID . "><p>Create new chat</p></a>";

    OutputCommonChats($conn, $searchedID);

    echo "</div>";
}

function OutputCommonChats($conn, $currentSearchedUserID)
{
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
        echo "<p>This user has no chats</p>";
    }
}
