<?php
include 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>KPMessager</title>
  <link href="style.css" rel="stylesheet" />
</head>

<body>
  <header>

    <div class="Login">
      <form action="includes/zLogin.inc.php" method="POST">
        <a href="NewAccount.php">Create new account</a>
        <input class="username_txt" type="text" name="username" placeholder="Enter username">
        <input class="password_txt" type="password" name="password" placeholder="Enter password">
        <button class="login_btn" type="submit" name="login"> Login </button>
      </form>
    </div>

    <div class="Actions">
      <a href="Friends.php">Friends</a>
      <a href="AddFriends.php">Add new friends</a>
    </div>

  </header>

  <div class="RecentMessages">
    <?php
    for ($i = 0; $i < 10; $i++) {
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
      echo "<div class='SentMessage Message'>";

      echo "<h3>0 Sent</h3><br>";
      echo "<p>0 Content</p><br>";

      echo "</div>";


      echo "<div class='RecievedMessage Message'>";

      echo "<h3>1 Recieved</h3><br>";
      echo "<p>1 Content</p><br>";

      echo "</div>";


      echo "<div class='SentMessage Message'>";

      echo "<h3>2 Sent</h3><br>";
      echo "<p>2 Content</p><br>";

      echo "</div>";


      echo "<div class='SentMessage Message'>";

      echo "<h3>3 Sent</h3><br>";
      echo "<p>3 Content</p><br>";

      echo "</div>";
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