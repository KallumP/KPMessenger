<?php
include 'dbh.inc.php';
session_start();

if (!isset($_SESSION['userID']))
    header("Location: ../login.php");

//checks if the page is loaded correctly
if (isset($_GET['ChatroomID']) && isset($_POST['PasswordToAdd'])) {

    $UserID = $_SESSION['userID'];
    $ChatroomID = $_GET['ChatroomID'];
    $inputPassword = $_POST['PasswordToAdd'];

    //statement to get the connector between this user and the Chatroom the new user is being added to (only if the user is admin)
    $sqlVerifyChatroomConnector =
        "SELECT
            connector.ID
        FROM
            connector
        WHERE
            connector.UserID = '$UserID' AND 
            connector.Admin = '1' AND
            connector.ChatroomID = '$ChatroomID';";

    //checks if there was an admin connector found
    if (mysqli_num_rows(mysqli_query($conn, $sqlVerifyChatroomConnector)) > 0) {

        //checks if the input was not emtpy
        if ($inputPassword != "") {

            $hashedPasswordInput = strtoupper(hash('sha256', $inputPassword));

            //add passhash to the database
            $sqlAddPassHash =
                "UPDATE
                    chatroom
                SET
                    chatroom.PassHash = '$hashedPasswordInput'
                WHERE    
                    chatroom.ID = '$ChatroomID';";

            mysqli_query($conn, $sqlAddPassHash);

            //saves the password
            $_SESSION['ChatroomID_' . $ChatroomID] = $inputPassword;



            //query to get all messages from this chat
            $sqlGetMessages =
                "SELECT
                    message.ID AS 'messageID',
                    message.Content AS 'messageContent'
                FROM
                    message
                WHERE 
                    message.ChatroomID = '$ChatroomID';";

            //calls the query
            $AllMessagesResult = mysqli_query($conn, $sqlGetMessages);

            //checks if there were any results
            if (mysqli_num_rows($AllMessagesResult) > 0) {

                //gets the next row of data returned by the query
                while ($messageRow = mysqli_fetch_assoc($AllMessagesResult)) {

                    $encryptionKey = $_SESSION['ChatroomID_' . $ChatroomID];
                    $oldMessage = $messageRow['messageContent'];
                    $messageID = $messageRow['messageID'];

                    $cipher = "AES-128-CTR";
                    $options = 0;
                    $encryption_iv = '1234567891011121';

                    $encryptedMessage = openssl_encrypt($oldMessage, $cipher, $encryptionKey, $options, $encryption_iv);

                    $sqlSaveEncryptedMessage =
                        "UPDATE
                            message
                        SET
                            message.Content = '$encryptedMessage'
                        WHERE
                            message.ID = '$messageID';";
                    mysqli_query($conn, $sqlSaveEncryptedMessage);

                    // echo "Old: " . $oldMessage . " - New: " . $encryptedMessage . "<br>";
                }
            }

            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&Note=PassSuccess");
        } else
            //return to chat settings saying you can't have an empty password
            header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&Note=EmptyPass");
    } else
        //don't have (admin) access to the chat
        header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&Note=NoChatAccess");
} else
    //opened the file wrong
    header("Location: ../chatSettings.php?ChatroomID=" . $ChatroomID . "&Note=BadFileAccess");
