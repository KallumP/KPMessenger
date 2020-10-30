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

    <div class="RecentMessages">
      <h1>Recent Messages</h1>
      <?php
      for ($i = 0; $i < 9; $i++) {

        echo "<div class='MessagePrev'>";
        echo "<a href=index.php?ChatRoomID=" . $i . ">";
        echo "<h1>" . $i . " Sender</h1>";
        echo "<p>" . $i . " Preview</p>";
        echo "</a>";
        echo "</div>";
      }
      ?>
    </div>

    <div class="ChatRoom">

      <div class="Messages">
        <?php

        //checks if there was a chatroom that was selected
        if (isset($_GET['ChatRoomID'])) {


          $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatRoomID']);

          //pulls the user's friend's ids
          $sqlAllMessages =
            "SELECT
              message.Content AS 'MessageContent',
              message.SenderID as 'SenderID'
            FROM
              message
            WHERE
              message.ChatRoomID = '$ChatroomID';";

          $AllMessagesResult = mysqli_query($conn, $sqlAllMessages);
          $AllMessagesResultCheck = mysqli_num_rows($AllMessagesResult);

          //checks if there were any messages
          if ($AllMessagesResultCheck > 0) {

            //loops through all the messages

            while ($messageRow = mysqli_fetch_assoc($AllMessagesResult)) {


              $senderID = $messageRow['SenderID'];


              //checks if the current message was yours
              if ($senderID == $_SESSION['userID']) {

                echo "<div class='SentMessage Message'>";

                echo "<h3> Sent by you</h3><br>";
                echo "<p>" .  $messageRow['MessageContent'] . "</p>";

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

                echo "<h3>Sent by " . $senderName . "</h3><br>";
                echo "<p>" .  $messageRow['MessageContent'] . "</p>";

                echo "</div>";
              }
            }
          }
        }
        ?>
      </div>

      <div class="MessageInput">
        <form action="index.php" method="POST">
          <input class="messageEntry" type="text" name="messageEntry" placeholder="Type your message here">
          <button class="messageSend" type="submit" name="messageSend"> Send </button>
        </form>
      </div>

    </div>
  </div>
</body>