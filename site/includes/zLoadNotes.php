<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");

$userID = $_SESSION['userID'];

//gets all the friendrequests for this user
$sqlGetFriendRequests =
    "SELECT
        friendrequest.ID AS 'requestID',
        friendrequest.SenderID AS 'senderID'
    FROM 
        friendrequest
    WHERE
        friendrequest.RecipientID = '$userID';";

//queries the database
$getFriendRequestsResult = mysqli_query($conn, $sqlGetFriendRequests);

//gets how many notifications there were
$numberOfNotifications = 0;
$numberOfNotifications += mysqli_num_rows($getFriendRequestsResult);
?>
<ul>

    <?php

    //gets the current page (incase its one of the banner items)
    $currentPage = "other";
    if (isset($_POST["CurrentPage"]))
        $currentPage = $_POST["CurrentPage"];

    //if there were any notifications
    if ($numberOfNotifications > 0) {

        //output the notifications item as "unread"
        echo "<li id='NotificationTab'> <a class='Unread' href='notifications.php'>Notifications: " . $numberOfNotifications  . "</a></li>";
    } else {

        //if the current page was the notes page
        if ($currentPage == "notes")

            //outputs the notifications item as "current"
            echo "<li id='NotificationTab'> <a class='Current' href='#'>Notifications: " . $numberOfNotifications  . "</a></li>";
        else

            //outputs the notifications item
            echo "<li id='NotificationTab'> <a href='notifications.php'>Notifications: " . $numberOfNotifications  . "</a></li>";
    }

    //checks if the current page was the account options
    if ($currentPage == "accountOptions")
        echo "<li><a class='Current'>Account options: " . $_SESSION['userName'] . "</a></li>";
    else
        echo "<li><a href='accountOptions.php'>Account options: " . $_SESSION['userName'] . "</a></li>";
    ?>

    <li><a href="includes/zLogout.php">Log out </a></li>
</ul>