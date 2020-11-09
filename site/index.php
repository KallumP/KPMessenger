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

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script>

    let LoadMessages = function() {

      $('#Messages').load('includes/zLoadMessages.php', {
        ChatroomID: <?php echo $_GET['ChatRoomID'] ?>,
      });


      $("#RecentMessages").load("includes/zLoadRecents.php", {

      });
    }

    //scrolls to the bottom of the message box on page load
    $(document).ready(function() {

      LoadMessages()

      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;
    });

    //the timer to pull new messages (short polling every 4 seconds)
    setInterval(function() {

      LoadMessages()

      let scroll = document.getElementById('Messages');
      scroll.scrollTop = scroll.scrollHeight;

    }, 4000);
    
  </script>
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


    <div id="RecentMessages" class="RecentMessages Border">

    </div>


    <div class="ChatRoom">

      <div id='Messages' class='Messages'>

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