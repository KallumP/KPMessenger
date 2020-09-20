<?php
include 'includes/dbh.inc.php';
?>

<div class="LoginOptions">
    <?php if (isset($_SESSION['userName'])) { ?>

        <?php

        $userID = $_SESSION['userID'];

        //gets all the unanswered friendrequests for this user
        $sqlGetFriendRequests =
            "SELECT
                friendrequest.ID AS 'senderID'
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
            <li> <a href="#">Notifications: <?php $numberOfNotifications ?></a>
                <?php if ($numberOfNotifications > 0) { ?>
                    <ul>
                        <?php while ($friendRequestsRow = mysqli_fetch_assoc($getFriendRequestsResult)) {

                            $senderID = $friendRequestsRow['senderID'];

                            //query to get the name of the friend request sender
                            $sqlGetRequestSenderName =
                                "SELECT
                                    _user.UserName as 'senderName'
                                FROM
                                    _user
                                WHERE
                                    _user.ID = '$senderID';";

                            //queries the database
                            $getSenderNameResult = mysqli_query($conn, $sqlGetRequestSenderName);
                            $friendRequestsSenderNameRow = mysqli_fetch_assoc($getSenderNameResult);

                            //displays the friend request
                            echo "<li> Friend request from: " . $friendRequestsSenderNameRow['senderName'] . "</li>";
                        }

                        ?>
                    </ul>
                <?php } ?>
            </li>
        </ul>
        <a href="includes/zLogout.php">Log out </a>
        <a href="accountOptions.php">Account options: <?php echo $_SESSION['userName'] ?></a>

    <?php } else { ?>

        <?php header("Location: login.php"); ?>

    <?php } ?>
</div>