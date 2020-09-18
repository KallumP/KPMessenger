<?php
include 'includes/dbh.inc.php';
?>

<div class="LoginOptions">
    <?php if (isset($_SESSION['userName'])) { ?>

        <a href="includes/zLogout.php">Log out </a>
        <a href="accountOptions.php">Account options</a>

    <?php } else { ?>

        <?php header("Location: login.php"); ?>

    <?php } ?>
</div>