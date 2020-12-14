<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: login.php");


if (isset($_POST['ChatroomID'])) {

    echo "<h2>Memers of this chat: </h2>";


    $ChatroomID = mysqli_real_escape_string($conn, $_POST['ChatroomID']);

    //gets all the chatroom id's and names that the user is a part of
    $sqlGetMembers =
        "SELECT 
        _user.UserName AS 'UserName',
        _user.ID AS 'UserID'
    FROM
        _user
    LEFT JOIN 
        connector ON _user.ID = connector.UserID
    WHERE 
        connector.ChatroomID = '$ChatroomID';";

    $GetMembersResult = mysqli_query($conn, $sqlGetMembers);

    //checks if there was any users found connected to this chat
    if (mysqli_num_rows($GetMembersResult) > 0) {

        //loops through each recent message
        while ($ChatMembersRow = mysqli_fetch_assoc($GetMembersResult)) {

            $UserName = $ChatMembersRow['UserName'];
            $UserID = $ChatMembersRow['UserID'];

            //have a link to remove / make admin

            //outputs the  member
            echo "<div class='ConectedUser'>";
            echo "<p>" . $UserName . "#" . $UserID . "<a href='includes/zRemoveUser.php?UserToRemoveID=" . $UserID . "&ChatroomID=" . $ChatroomID . "'>Remove User</a><a href='#'>Make Admin</a></p>";
            echo "</div>";

            //includes/zRemoveUser.php?UserID=" . $UserID . "&ChatroomID=" . $ChatroomID
            //includes/zSetAdmin.php?UserID=" . $UserID . "
        }
    }
}
