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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script>
        let GetNotes = function() {
            $("#Banner").load("includes/zLoadNotes.php", {
                AccountOptions: "true"
            });
        }

        //calls the initial ajax (to load up the dynamic parts of the page)
        $(document).ready(function() {

            GetNotes();

        });

        //the timer to pull new messages (short polling every 4 seconds)
        setInterval(function() {

            GetNotes();

        }, 4000);
    </script>
</head>

<body>

    <header>

        <div id="Banner" class="AccountBanner">

        </div>

        <div class="Actions">
            <ul>
                <li><a href="index.php">Messages</a></li>
                <li><a href="searchFriends.php">Friends</a></li>
                <li><a href="searchAllUsers.php">Search all Users</a></li>
            </ul>
        </div>

    </header>

    <?php if (isset($_SESSION['userName'])) { ?>

        <div class="AccountOptions">

            <h1>Logged in as: <?php echo $_SESSION['userName']  ?> || ID: <?php echo $_SESSION['userID']  ?></h1>


            <a href="includes/zLogout.php">Log out</a>
        </div>


    <?php } ?>



</body>

<?php  ?>