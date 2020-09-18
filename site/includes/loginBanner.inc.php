<?php
include 'includes/dbh.inc.php';
?>

<div class="LoginOptions">
    <?php if (isset($_SESSION['userName'])) { ?>

        <a href="includes/zLogout.php">Log out </a>
        <a href="accountOptions.php">Account options</a>

    <?php } else { ?>

        <a href="createNewAccount.php">Create new account</a>
        <a href="login.php">Login</a>

    <?php } ?>
</div>