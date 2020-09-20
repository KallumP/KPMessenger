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
  <header>

    <?php include("includes/accountBanner.inc.php"); ?>

    <div class="Actions">
      <a href="searchFriends.php">Friends</a>
      <a href="searchAllUsers.php">Search all Users</a>
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

      for ($i = 0; $i < 1; $i++) {

        $sent = rand(0, 1);

        if ($sent == 1) {
          echo "<div class='SentMessage Message'>";

          echo "<h3>" . $i . " Sent</h3><br>";
          echo "<p>" . $i . " Content</p>";

          echo "</div>";
        } else {
          echo "<div class='RecievedMessage Message'>";

          echo "<h3>" . $i . " Recieved</h3><br>";
          echo "<p>" . $i . " Content</p>";

          echo "</div>";
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

</body>