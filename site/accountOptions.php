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


    <?php if (isset($_SESSION['userName'])) { ?>

        <div class="AccountOptions">

            <h1>Logged in as: <?php echo $_SESSION['userName']  ?> || ID: <?php echo $_SESSION['userID']  ?></h1>


            <a href="includes/zLogout.php">Log out</a>
        </div>


    <?php } ?>



</body>

<?php  ?>