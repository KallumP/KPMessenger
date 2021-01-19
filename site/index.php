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

      <?php if (isset($_GET['ChatroomID'])) { ?>
        $('#Messages').load('includes/zLoadMessages.php', {
          ChatroomID: <?php echo $_GET['ChatroomID'] ?>
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

    let SetChatBoxHeight = function() {

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

      SetChatBoxHeight();

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

      let noPass = true;

      <?php
      if (isset($_GET['ChatroomID'])) {

        $ChatroomID = $_GET['ChatroomID'];
        //check if there is a password required
        $sqlCheckPassword =
          "SELECT
            chatroom.PassHash AS 'PassHash'
          FROM
            chatroom
          WHERE
            chatroom.ID = '$ChatroomID' AND
            NOT chatroom.PassHash = ''";

        $CheckPasswordResult = mysqli_query($conn, $sqlCheckPassword);

        //checks if there was a required password, and a session password is not set
        if (mysqli_num_rows($CheckPasswordResult) > 0 && !isset($_SESSION['ChatroomID_' . $ChatroomID])) {
          echo "noPass = false;";
        }
      }
      ?>

      if (noPass)
        GetMessages();



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
    <div class="ChatRoom">

      <div id='Messages' class='Messages'>

      </div>


      <div class="MessageInput">

        <?php
        if (isset($_GET['ChatroomID'])) {

          $ChatroomID = $_GET['ChatroomID'];
          $UserID = $_SESSION['userID'];

          $chatAccess = false;
          $passAccess = true;

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
          if (mysqli_num_rows(mysqli_query($conn, $sqlUserConnector)) > 0)
            $chatAccess = true;


          //check if there is a password required
          $sqlCheckPassword =
            "SELECT
              chatroom.PassHash AS 'PassHash'
            FROM
              chatroom
            WHERE
              chatroom.ID = '$ChatroomID' AND
              NOT chatroom.PassHash = ''";

          $CheckPasswordResult = mysqli_query($conn, $sqlCheckPassword);

          //checks if there was a required password, and a session password is not set
          if (mysqli_num_rows($CheckPasswordResult) > 0 && !isset($_SESSION['ChatroomID_' . $ChatroomID]))
            $passAccess = false;


          if ($chatAccess && $passAccess) {

            //generates the url to send the message with
            $SendMessageTo = "includes/zSendMessage.php?ChatroomID=" . $_GET['ChatroomID'];

            echo "<form action='$SendMessageTo'  method='POST' autocomplete='off'>";
            echo "<input class='messageEntry BorderInputs' type='text' name='messageEntry' placeholder='Type your message here' rows='1' autofocus></input>";
            echo "<button class='messageSend BorderInputs' type='submit' name='messageSend'> Send </button>";
            echo "</form>";
          } else {
            echo $chatAccess . $passAccess;
          }
        }
        ?>
      </div>
    </div>
  </div>
</body>