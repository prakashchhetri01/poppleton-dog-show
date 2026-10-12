<?php
//Start the session toaccess the stored login data
session_start();

//Remove all session variables (like user id, username,)
session_unset();

//Destroy the session completely to log the user out
session_destroy();

//After logging out, send the user back to the login page
header('Location: login.php');
exit; // Stop the script after redirect
?>
