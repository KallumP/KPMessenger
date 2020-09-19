<?php
include 'dbh.inc.php';

//checks if the user actually clicked the create acccount button
if (isset($_POST['login'])) {

    //gets the inputs from the create account page
    //$inputEmail = mysqli_real_escape_string($conn, 'email');
    $inputUserName = mysqli_real_escape_string($conn, $_POST['username']);
    $inputPass = mysqli_real_escape_string($conn, $_POST['password']);
    $inputPassValidate = mysqli_real_escape_string($conn, $_POST['password-validate']);

    //checks if all the input fields were filled
    if ($inputUserName != "" && $inputPass != "" && $inputPassValidate != "") {

        //a query to pull all usernames that are the same as the input username
        $usernameCheckSQL =
            "SELECT
                _user.UserName as 'username'
            FROM 
                _user
            WHERE
                _user.UserName = '$inputUserName';";

        //checks if the query returns doesn't return any data
        if (mysqli_num_rows(mysqli_query($conn, $usernameCheckSQL)) == 0) {

            //checks if the two password inputs were the same
            if ($inputPass == $inputPassValidate) {

                //saves a hashed version of the password
                $hashedPassword = strtoupper(hash('sha256', $inputPass));

                //a query to insert a new user
                $tableSQL =
                    "INSERT INTO 
                    _user ( UserName, PassHash)
                VALUES  ('$inputUserName', '$hashedPassword');";

                //queries the database
                mysqli_query($conn, $tableSQL);

                //logs the user into the account they just made
                session_start();

                $_SESSION['userName'] = $inputUserName;
                $_SESSION['userID'] = $pulledData['userID'];
                
                header("Location: ../index.php");
            } else
                header("Location: ../createNewAccount.php?note=passwordsNotSame");
        } else
            header("Location: ../createNewAccount.php?note=notUniqueUsername");
    } else
        header("Location: ../createNewAccount.php?note=emptyFields");
} else
    header("Location: ../createNewAccount.php?note=noPost");
