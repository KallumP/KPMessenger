<?php
include 'includes/dbh.inc.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>KPMessenger</title>
  <link href="style.css" rel="stylesheet" />

  <style>
    /* width */
    ::-webkit-scrollbar {
      width: 2px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #000000;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #ffffff;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #555555;
    }
  </style>
</head>

<body>

  <div class=Container>
    <header>

      <?php include("includes/accountBanner.inc.php"); ?>

      <div class="Actions">
        <ul>
          <li><a href="searchFriends.php">Friends</a></li>
          <li><a href="searchAllUsers.php">Search all Users</a></li>
        </ul>
      </div>

    </header>

    <div class="RecentMessages Border">
      <h1>Recent Messages</h1>
      <?php

      $UserID = $_SESSION['userID'];

      //gets all the chatroom id's and names that the user is a part of
      $sqlGetRecentMessages =
        "SELECT 
            chatroom.Name AS 'ChatName',
            chatroom.ID AS 'ChatID'
          FROM
            chatroom
          LEFT JOIN connector ON chatroom.ID = connector.ChatroomID
          WHERE 
            connector.UserID = '$UserID'
          ORDER BY 
            chatroom.LastMessageTime DESC;";

      $RecentMessagesResult = mysqli_query($conn, $sqlGetRecentMessages);

      //checks if there was any chatrooms found with recent messages
      if (mysqli_num_rows($RecentMessagesResult) > 0) {

        //loops through each recent message
        while ($recentMessageRow = mysqli_fetch_assoc($RecentMessagesResult)) {

          $currentChat = $recentMessageRow['ChatID'];
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
              message.ChatRoomID = '$currentChat';";

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

              //checks if there 
              if ($lastMessage != "") {

                //checks if the message was more than 20 characters long and then saves the preview
                if (strlen($lastMessage) > 20)
                  $messagePreview = substr($lastMessage, 0, 20);
                else
                  $messagePreview = $lastMessage;
              } else {
                $messagePreview = "No messages yet";
              }
            }
          }

          //outputs the  chatroom
          echo "<div class='MessagePrev'>";
          echo "<a href=index.php?ChatRoomID=" . $recentMessageRow['ChatID'] . ">";
          echo "<h2>" . $recentMessageRow['ChatName'] . "</h2>";
          echo "<p>" . $messagePreview . "</p>";
          echo "</a>";
          echo "</div>";
        }
      }

      ?>
    </div>

    <div class="ChatRoom">

      <?php

      //checks if there was a chatroom that was selected (from the url)
      if (isset($_GET['ChatRoomID'])) {

        $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatRoomID']);
        $UserID = $_SESSION['userID'];

        //check if the user has access to this chatroom
        $sqlUserConnector =
          "SELECT 
              connector.ID
            FROM  
              connector
            WHERE
              connector.UserID = '$UserID';";

        //if the user has access to this chat (the query returned a connector)
        if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector))) {

          $sqlChatName =
            "SELECT
              chatroom.Name AS 'Name'
            FROM
              chatroom
            WHERE
              chatroom.ID = '$ChatroomID'";

          $ChatNameResult = mysqli_query($conn, $sqlChatName);

          //checks if there were any messages
          if (mysqli_num_rows($ChatNameResult) > 0) {
            $chatname = mysqli_fetch_assoc($ChatNameResult)['Name'];
            echo "<h1 class='ChatName'>" . $chatname .  "</h1>";
          }
          echo "<div class='Messages'>";

          //pulls the messages from this chatroom
          $sqlAllMessages =
            "SELECT
              message.Content AS 'MessageContent',
              message.SenderID as 'SenderID'
            FROM
              message
            WHERE
              message.ChatRoomID = '$ChatroomID'
            ORDER BY
              message.ID;";

          $AllMessagesResult = mysqli_query($conn, $sqlAllMessages);
          $AllMessagesResultCheck = mysqli_num_rows($AllMessagesResult);

          //checks if there were any messages
          if ($AllMessagesResultCheck > 0) {

            //loops through all the messages
            while ($messageRow = mysqli_fetch_assoc($AllMessagesResult)) {

              $senderID = $messageRow['SenderID'];
              $message = wordwrap($messageRow['MessageContent'], 70, "<br>");

              //checks if the current message was yours
              if ($senderID == $_SESSION['userID']) {

                echo "<div class='SentMessage Message'>";

                echo "<p>" .  $message . "</p>";
                echo "<h3> Sent by you</h3>";

                echo "</div>";
              } else {


                $sqlGetSender =
                  "SELECT
                    _user.Username AS 'SenderName'
                  FROM
                    _user
                  WHERE
                    _user.ID = '$senderID';";

                $GetSenderResult = mysqli_query($conn, $sqlGetSender);
                $GetSenderResultCheck = mysqli_num_rows($GetSenderResult);

                $senderName = "";
                if ($GetSenderResultCheck > 0)
                  $senderName = mysqli_fetch_assoc($GetSenderResult)['SenderName'];

                else
                  $senderName = "Unknown User";

                echo "<div class='RecievedMessage Message'>";

                echo "<p>" .  $message . "</p>";
                echo "<h3>Sent by " . $senderName . "</h3>";

                echo "</div>";
              }
            }
          } else {

            //there were no messages in the chat

            echo "<h2>Looks like there are no messages in this chat.<br>Why not initiate and send one yourself</h3><br><br>";
          }
        } else {
          echo "<h2>You don't have access to this chat</h3>";
        }
      }
      ?>
    </div>

    <div class="MessageInput">

      <?php
      if (isset($_GET['ChatRoomID'])) {
        //generates the url to send the message with
        $SendMessageTo = "includes/zSendMessage.php?chatRoomID=" . mysqli_real_escape_string($conn, $_GET['ChatRoomID']);
        echo "<form action='$SendMessageTo'  method='POST'>"
      ?>

        <input class="messageEntry BorderInputs" type="text" name="messageEntry" placeholder="Type your message here">
        <button class="messageSend BorderInputs" type="submit" name="messageSend"> Send </button>
        </form>
      <?php
      }
      ?>
    </div>


  </div>
  </div>
</body>