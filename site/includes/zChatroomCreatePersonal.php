<?php
include_once 'dbh.inc.php';
require_once 'functions.php';
session_start();

CheckLoggedIn($conn, true);

//creates the Chatroom
$sqlCreateChat =
    "INSERT INTO 
      chatroom (Name)
    VALUES
      ('Just you');";

//queries and checks if the query worked
if ($conn->query($sqlCreateChat) === TRUE) {

    //gets the id of the chat
    $ChatroomID = $conn->insert_id;
    $userID = $_SESSION['userID'];

    //creates the connector for this user (adnim)
    $sqlConnector1 =
        "INSERT INTO 
      connector (UserID, ChatroomID, Admin)
    VALUES
      ('$userID', '$ChatroomID', '1');";
    mysqli_query($conn, $sqlConnector1);

    header("Location: ../index.php?ChatroomID=" .  $ChatroomID);
    exit();
} else {

    header("Location: ../accountOptions.php?Note=CantMakeChat");
    exit();
}
