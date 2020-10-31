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

    <div class="RecentMessages Border">
      <h1>Recent Messages</h1>
      <?php
      for ($i = 1; $i < 9; $i++) {

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


          if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector))) {


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


                //checks if the current message was yours
                if ($senderID == $_SESSION['userID']) {

                  echo "<div class='SentMessage Message Border'>";

                  echo "<p>" .  $messageRow['MessageContent'] . "</p>";
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


                  echo "<div class='RecievedMessage Message Border'>";

                  echo "<p>" .  $messageRow['MessageContent'] . "</p>";
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

        ?>
          <div class="MessageInput Border">

            <?php
            //generates the url to send the message with
            $SendMessageTo = "includes/zSendMessage.php?chatRoomID=" . mysqli_real_escape_string($conn, $_GET['ChatRoomID']);
            echo "<form action='$SendMessageTo'  method='POST'>"
            ?>

            <input class="messageEntry BorderInputs" type="text" name="messageEntry" placeholder="Type your message here">
            <button class="messageSend BorderInputs" type="submit" name="messageSend"> Send </button>
            </form>
          </div>
        <?php
        }
        ?>
      </div>



    </div>
  </div>
</body>