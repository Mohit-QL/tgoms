<?php
include('database.php');
$clientID = $_REQUEST['clientID'];
$selectClientSql = "SELECT * FROM clients WHERE id=" . $clientID;
$selectClientQuery = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
$result = mysqli_fetch_array($selectClientQuery);
echo $result['name'] . "_,__" . $result['contact'] . "_,__" . $result['address'] . "_,__" . $result['city'] . "_,__" . $result['state'] . "_,__" . $result['zip'] . "_,__" . $result['phone'] . "_,__" . $result['email'] . "_,__" . $clientID;
