<?php
// Start output buffering
ob_start();

session_start();
include('database.php');
include('config.php');

if (!isset($_SESSION['initials'])) {
    header('Location: index2.php');
    exit;
}

// Clear buffer
ob_end_clean();

if (isset($_POST['downloadEmails']) || isset($_GET['force_download'])) {
    if (isset($_SESSION['email_list_data']) && !empty($_SESSION['email_list_data'])) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="email-list' . '.txt"');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $_SESSION['email_list_data'];
        exit;
    } else {
        die("No email data available for download. Please generate the list first.");
    }
}

header('Location: control.php');
exit;
