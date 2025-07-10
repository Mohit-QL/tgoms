<?php
// Define the base URL based on the server environment
$baseURL = ($_SERVER['SERVER_NAME'] == 'localhost') ? '/tgoms/' : '';

define('BASE_URL', $baseURL);
?>