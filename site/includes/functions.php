<?php
function CheckLoggedIn($conn, $includes)
{

    if ($includes == true)
        $urlToGoTo = "../login.php";
    else
        $urlToGoTo = "login.php";


    //checks if there wasn't any logged in variables saved
    if (!isset($_SESSION['userID'])) {

        echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
    } else {

        $userID = $_SESSION['userID'];
        $userName = $_SESSION['userName'];

        //check the database's to make sure a user of that id and name exists
        $sqlGetThisUser =
            "SELECT
                _user.ID
             FROM
                _user
            WHERE
                _user.ID = '$userID' AND
                _user.UserName = '$userName';";

        //checks if the logged in variables didn't correlate to a user in the database
        if (mysqli_num_rows(mysqli_query($conn, $sqlGetThisUser)) == 0) {
            echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
        }
    }
}
