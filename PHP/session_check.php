<?php
// Start the session
session_start();
if (!isset($_SESSION['email'])) {
    $display='style= "display:none"';
    $button='style= "display:flex"';
}
else{
    $display='style= "display:flex"';
    $button='style= "display:none"';
}
?>