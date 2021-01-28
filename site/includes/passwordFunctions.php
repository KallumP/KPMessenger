<?php
include_once 'dbh.inc.php';

function RequirePassword($ChatroomID, $conn)
{

    //check if there is a password required
    $sqlCheckPassword =
        "SELECT
            chatroom.PassHash AS 'PassHash'
        FROM
            chatroom
        WHERE
            chatroom.ID = '$ChatroomID' AND
            NOT chatroom.PassHash = ''";

    $CheckPasswordResult = mysqli_query($conn, $sqlCheckPassword);

    //checks if there was a result (a password is required for this chat)
    if (mysqli_num_rows($CheckPasswordResult) > 0) {

        $passHashRow = mysqli_fetch_assoc($CheckPasswordResult);
        $dbPassHash = $passHashRow['PassHash'];

        if (ValidatePassword($dbPassHash, $ChatroomID))
            return true;
    } else
        return false;
}


function ValidatePassword($dbPassHash, $ChatroomID)
{
    $passChange = false;

    //check if there is was a password session variable for this chat (user has already entered a password)
    if (isset($_SESSION['ChatroomID_' . $ChatroomID])) {

        $localPassHash = strtoupper(hash('sha256', $_SESSION['ChatroomID_' . $ChatroomID]));

        //checks if the saved password is not valid
        if ($localPassHash != $dbPassHash) {

            //removes the saved password (because it is wrong)
            unset($_SESSION['ChatroomID_' . $ChatroomID]);

            $passChange = true;
        }
    }

    //if there isn't a password saved (nothing was entered, or the saved one is no longer valid)
    if (!isset($_SESSION['ChatroomID_' . $ChatroomID])) {

        $urlToGoTo = "enterChatPassword.php?ChatroomID=" . $ChatroomID;
        if ($passChange)
            $urlToGoTo .= "?Note=changed";

        echo "<meta http-equiv='refresh' content='0;url=" . $urlToGoTo . "'>";
    } else {
        return true;
    }
}

function EncryptString($string, $key)
{

    $cipher = "AES-128-CTR";
    $options = 0;
    $encryption_iv = '1234567891011121';

    return openssl_encrypt($string, $cipher, $key, $options, $encryption_iv);
}

function DecryptString($string, $key)
{

    $cipher = "AES-128-CTR";
    $options = 0;
    $decryption_iv = '1234567891011121';

    return openssl_decrypt($string, $cipher, $key, $options, $decryption_iv);
}
