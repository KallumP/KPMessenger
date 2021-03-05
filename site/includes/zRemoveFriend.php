<?php
include 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

if (isset($_GET['toRemoveID'])) {

    $UserID = $_SESSION['userID'];
    $ToRemove = $_GET['toRemoveID'];

    //query to get the friend connection 
    $sqlCheckFriend =
        "SELECT
            friend.ID
        FROM
            friend
        WHERE
            friend.SenderID = '$UserID' AND
            friend.RecipientID = '$ToRemove';";

    //checsk if there was a friend connection
    if (mysqli_num_rows(mysqli_query($conn, $sqlCheckFriend)) > 0) {


        //query to delete the two friend connectors
        $sqlRemoveFriend =
            "DELETE FROM
            friend
        WHERE
            (friend.SenderID = '$UserID' AND
            friend.RecipientID = '$ToRemove') OR
            (friend.SenderID = '$ToRemove' AND
            friend.RecipientID = '$UserID');";

        mysqli_query($conn, $sqlRemoveFriend);

        header("Location: ../searchFriends.php?Note=friendRemoveSuccess");
        exit();
    } else {
        header("Location: ../searchFriends.php?Note=friendNotFriend");
        exit();
    }
} else {
    header("Location: ../searchFriends.php?Note=userDoesntExist");
    exit();
}
