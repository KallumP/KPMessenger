<?php
include 'includes/dbh.inc.php';
require_once 'includes/passwordFunctions.php';
include 'includes/functions.php';
session_start();

CheckLoggedIn($conn, false);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>KPMessenger</title>
  <link href="style.css" rel="stylesheet" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script>
    let GetMessages = function() {

      <?php if (isset($_GET['ChatroomID'])) { ?>
        $('#Messages').load('includes/zLoadMessages.php', {
          ChatroomID: <?php echo $_GET['ChatroomID']; ?>
        });
      <?php } ?>

    }

    let GetRecentMessages = function() {

      //gets the chat id from the url, and assigns it -1 if there wasn't one
      let URLChatroomID;

      <?php if (isset($_GET['ChatroomID'])) { ?>
        URLChatroomID = <?php echo $_GET['ChatroomID'] ?>;
      <?php } else { ?>
        URLChatroomID = -1;
      <?php } ?>

      $("#RecentMessages").load("includes/zLoadRecents.php", {

        ChatroomID: URLChatroomID
      });
    }

    let GetNotes = function() {
      $("#Banner").load("includes/zLoadNotes.php", {

      });
    }

    let SetDivHeights = function() {

      //http://tutorialshares.com/dynamically-change-div-height-browser-window-resize/

      //in px
      let bannerHeight = 210;

      $('#Messages').css({
        'max-height': ($(window).height() - bannerHeight) + 'px'
      });

      $('#RecentMessages').css({
        'max-height': ($(window).height() - bannerHeight + 150) + 'px',
        'height': ($(window).height() - bannerHeight + 150) + 'px'
      });

    }

    //calls the initial ajax (to load up the dynamic parts of the page)
    $(document).ready(function() {

      SetDivHeights();

      GetRecentMessages();
      GetNotes();
      GetMessages();


      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;
    });



    //the timer to pull new messages (short polling every 4 seconds)
    setInterval(function() {

      GetRecentMessages();
      GetNotes();
      GetMessages();


      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;

    }, 4000);

    $(window).resize(function() { // On resize
      SetDivHeights();
    });
  </script>
</head>

<body>

  <div class=Container>
    <header>

      <div id="Banner" class="AccountBanner">

      </div>

      <div class="Actions">
        <ul>
          <li><a class="Current">Messages</a></li>
          <li><a href="searchFriends.php">Friends</a></li>
          <li><a href="searchAllUsers.php">Search all Users</a></li>
        </ul>
      </div>

    </header>


    <div id="RecentMessages" class="RecentMessages">

    </div>
    <div class="ChatRoom">

      <div id='Messages' class='Messages'>

      </div>


      <div class="MessageInput">

        <?php
        if (isset($_GET['ChatroomID'])) {

          $ChatroomID = mysqli_real_escape_string($conn, $_GET['ChatroomID']);
          $UserID = $_SESSION['userID'];

          //check if the user has access to this Chatroom
          $sqlUserConnector =
            "SELECT 
              connector.ID
            FROM 
              connector
            WHERE
              connector.UserID = '$UserID' AND 
              connector.ChatroomID = '$ChatroomID';";

          //if the user has access to this chat (the query returned a connector)
          if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector)) > 0) {


            $passwordCheck = RequirePassword($ChatroomID, $conn);

            //deal with the validation response
            if ($passwordCheck == "WrongSavedPassword") {

              $urlToGoTo = "enterChatPassword.php?ChatroomID=" . $ChatroomID . "?Note=changed";
              echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
              exit();
            } else if ($passwordCheck == "NoSavedPassword") {

              $urlToGoTo = "enterChatPassword.php?ChatroomID=" . $ChatroomID;
              echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
              exit();
            } else {

              $SendMessageTo = "includes/zSendMessage.php?ChatroomID=" . $ChatroomID;

              echo "<form action='$SendMessageTo'  method='POST' autocomplete='off'>";
              echo "<input class='messageEntry BorderInputs' type='text' name='messageEntry' placeholder='Type your message here' rows='1' autofocus></input>";
              echo "<button class='messageSend BorderInputs' type='submit' name='messageSend'> Send </button>";
              echo "</form>";
            }
          }
        }
        ?>
      </div>
    </div>
  </div>
</body>