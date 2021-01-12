<?php
include 'includes/dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
  header("Location: login.php");
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

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script>
    let GetMessages = function() {

      <?php if (isset($_GET['ChatRoomID'])) { ?>
        $('#Messages').load('includes/zLoadMessages.php', {
          ChatroomID: <?php echo $_GET['ChatRoomID'] ?>
        });
      <?php } ?>
    }

    let GetRecentMessages = function() {

      //gets the chat id from the url, and assigns it -1 if there wasn't one
      let URLChatRoomID;

      <?php if (isset($_GET['ChatRoomID'])) { ?>
        URLChatRoomID = <?php echo $_GET['ChatRoomID'] ?>;
      <?php } else { ?>
        URLChatRoomID = -1;
      <?php } ?>

      $("#RecentMessages").load("includes/zLoadRecents.php", {

        ChatroomID: URLChatRoomID
      });
    }

    let GetNotes = function() {
      $("#Banner").load("includes/zLoadNotes.php", {

      });
    }

    let SetChatBoxHeight = function() {

      //http://tutorialshares.com/dynamically-change-div-height-browser-window-resize/

      //in px
      let bannerHeight = 210;

      $('#Messages').css({
        'max-height': ($(window).height() - bannerHeight) + 'px'
      });

      $('#RecentMessages').css({
        'max-height': ($(window).height() - bannerHeight + 150) + 'px'
      });

    }

    //calls the initial ajax (to load up the dynamic parts of the page)
    $(document).ready(function() {

      SetChatBoxHeight();

      GetMessages();
      GetRecentMessages();
      GetNotes();

      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;
    });



    //the timer to pull new messages (short polling every 4 seconds)
    setInterval(function() {

      GetMessages();
      GetRecentMessages();
      GetNotes();

      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;

    }, 4000);

    $(window).resize(function() { // On resize
      SetChatBoxHeight();
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

    <!-- <div class="Content"> -->
    <div class="ChatRoom">

      <div id='Messages' class='Messages'>

      </div>


      <div class="MessageInput">

        <?php
        if (isset($_GET['ChatRoomID'])) {
          //generates the url to send the message with
          $SendMessageTo = "includes/zSendMessage.php?chatRoomID=" . mysqli_real_escape_string($conn, $_GET['ChatRoomID']);
          echo "<form action='$SendMessageTo'  method='POST' autocomplete='off'>"
        ?>

          <input class="messageEntry BorderInputs" type="text" name="messageEntry" placeholder="Type your message here" rows="1" autofocus></textarea>
          <button class="messageSend BorderInputs" type="submit" name="messageSend"> Send </button>
          </form>

        <?php
        }
        ?>
      </div>

      <!-- </div> -->
    </div>
  </div>
</body>