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

    <div class="Login">

        <div class="Notes">
            <?php
            if (isset($_GET['note'])) {

                $note = $_GET['note'];

                if ($note == "passwordsNotSame")
                    echo "<h3>The password fields were not the same</h3>";
                else if ($note == "notUniqueUsername")
                    echo "<h3>That username was already taken</h3>";
                else if ($note == "emptyFields")
                    echo "<h3>Please fill out all the fields</h3>";
                else if ($note == "noPost")
                    echo "<h3>Create an account from here</h3>";
            }
            ?>
        </div>

        <h1 class='WhiteHeader'>Create an account</h1>

        <form action="includes/zAccountCreate.php" method="POST">

            <!-- <label for="email">Enter your email</label><br>
            <input class="email" type="text" name="email" placeholder="Enter username"><br><br> -->

            <label class='WhiteHeader' for="username">Enter your desired username</label><br>
            <input class="username_txt BorderInputs" type="text" name="username" placeholder="Enter username"><br><br>

            <label class='WhiteHeader' for="username">Enter a password for this account</label><br>
            <input class="password_txt BorderInputs" type="password" name="password" placeholder="Enter password"><br><br>

            <label class='WhiteHeader' for="username">Re-enter the same password</label><br>
            <input class="password_txt BorderInputs" type="password" name="password-validate" placeholder="Enter password"><br><br>

            <button class="login_btn BorderInputs" type="submit" name="login"> Submit </button>

            <a href="login.php">
                <p>Back to log in</p>
            </a>

        </form>
    </div>

</body>