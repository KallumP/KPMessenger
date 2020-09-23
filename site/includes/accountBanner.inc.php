<?php
include 'includes/dbh.inc.php';
?>

<div class="AccountBanner">
    <?php if (isset($_SESSION['userName'])) { ?>

        <?php

        $userID = $_SESSION['userID'];

        //gets all the unanswered friendrequests for this user
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
            <li> <a href="#">Notifications: <?php echo $numberOfNotifications ?></a>
                <?php if ($numberOfNotifications > 0) { ?>
                    <ul>
                        <?php while ($friendRequestsRow = mysqli_fetch_assoc($getFriendRequestsResult)) {

                            $senderID = $friendRequestsRow['senderID'];
                            $requestID = $friendRequestsRow['requestID'];

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
                            echo "<li><a href='includes/zFriendRequestAccept.php?requestID=" . $requestID . "'> Friend request from: " . $friendRequestsSenderNameRow['senderName'] . "</a></li>";
                        }

                        ?>
                    </ul>
                <?php } ?>
            </li>

            <li><a href="accountOptions.php">Account options: <?php echo $_SESSION['userName'] ?></a></li>
            <li><a href="includes/zLogout.php">Log out </a></li>
        </ul>

    <?php } else { ?>

        <?php header("Location: login.php"); ?>

    <?php } ?>
</div>