<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, false);

//checks if the page is loaded correctly
if (isset($_POST['ChatroomID'])) {

    $ChatroomID = mysqli_real_escape_string($conn, $_POST['ChatroomID']);

    //check if there is a password is not required
    $sqlCheckPassword =
        "SELECT
            chatroom.PassHash AS 'PassHash'
        FROM
            chatroom
        WHERE
            chatroom.ID = '$ChatroomID' AND
            chatroom.PassHash = ''";
    if (mysqli_num_rows(mysqli_query($conn, $sqlCheckPassword)) > 0) {

        echo "<meta http-equiv='refresh' content='0;url=index.php?ChatroomID=" . $ChatroomID . "'>";
        exit();
    }
}
