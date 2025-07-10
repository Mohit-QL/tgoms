<?php
include('database.php');
$id = $_REQUEST['id'];
$setString = "name = '" . addslashes($_REQUEST['name']) . "', contact = '" . addslashes($_REQUEST['contact']) . "', address = '" . addslashes($_REQUEST['address']) . "', city = '" . addslashes($_REQUEST['city']) . "', state = '" . $_REQUEST['state'] . "', zip = '" . $_REQUEST['zip'] . "', phone = '" . addslashes($_REQUEST['phone']) . "', email = '" . addslashes($_REQUEST['email']) . "'";
$updateClientSql = "UPDATE clients SET " . $setString . " WHERE id=" . $id;
mysqli_query($conn, $updateClientSql) or die(mysqli_error($conn));
