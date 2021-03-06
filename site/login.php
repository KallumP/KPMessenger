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
    <?php

    //checks if the user has logged in
    if (isset($_SESSION['userName'])) {

        header("Location: accountOptions.php");
    } else {
    ?>

        <div class="Login">

            <h1 class='WhiteHeader'> Log into KPMessenger </h1>

            <div class="Notes">
                <?php
                //gets and displays the error message
                if (isset($_GET['note'])) {

                    $note = $_GET['note'];

                    if ($note == "badCredentials")
                        echo "<h3>Either your username or password was incorrect</h3>";
                    else if ($note == "badUser")
                        echo "<h3>That username was wrong";
                    else if ($note == "badPass")
                        echo "<h3>That password was wrong";
                    else if ($note == "emtpyFields")
                        echo "<h3>Please fill out both text boxes";
                }
                ?>
            </div>

            <form action="includes/zLogin.php" method="POST">

                <input class="username_txt BorderInputs" type="text" name="username" placeholder="Enter username"><br><br>
                <input class="password_txt BorderInputs" type="password" name="password" placeholder="Enter password"><br><br>
                <button class="login_btn BorderInputs" type="submit" name="login"> Login </button>

                <a href="createNewAccount.php">
                    <p>Create new account</p>
                </a>
            </form>

        </div>
    <?php
    }
    ?>
</body>