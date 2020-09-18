<?php

include_once 'dbh.inc.php';
session_start();

//checks to see if the user actually searched something
if (isset($_POST['login'])) {

    //pulls and hashes the inputs
    $inputUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $inputPassword = mysqli_real_escape_string($conn, $_POST['password']);

    //checks if either of the inputs were empty
    if (empty($inputUsername) || empty($inputPassword)) {

        header("Location: ../login.php?note=emtpyFields");
        exit();
    } else {

        //hashes the password
        $hashedInputPassword = strtoupper(hash('sha256', $inputPassword));
        $inputPassword = "";

        //creates an sql query to pull the hashed password of the inputed username
        $sql =
            "SELECT
                _user.ID AS 'userID',
                _user.UserName AS 'userName',
                _user._PassHash AS 'userPass'
            FROM
                _user
            WHERE 
                _user.Username = '$inputUsername';";

        //pulls the data from the database using the query
        $result = mysqli_query($conn, $sql);

        //gets how many rows of data was pulled
        $resultCheck = mysqli_num_rows($result);

        //checks if there were any results
        if ($resultCheck > 0) {

            //gets the first index from the array of pulled results (there should only be one)
            $pulledData = mysqli_fetch_assoc($result);

            //saves the database hashed password
            $databaseHashedPassword = $pulledData['userPass'];

            if ($hashedInputPassword == $databaseHashedPassword) {

                session_start();

                $_SESSION['userName'] = $pulledData['userName'];
                $_SESSION['userID'] = $pulledData['userID'];

                header("Location: ../index.php");
            } else {

                header("Location: ../login.php?note=badCredentials");
                exit();
            }
        } else {

            header("Location: ../login.php?note=badCredentials");
            exit();
        }
    }
} else {

    header("Location: ../index.php");
    exit();
}
