<?php
include('database.php');
include('config.php');
session_start();
$error = '';
if (isset($_POST['login'])) {
	$login = mysqli_real_escape_string($conn, $_POST['login']);
	$pass  = md5($_POST['pass']);
	$selectUserSql = "SELECT initials, type FROM users WHERE name='$login' AND password='$pass'";
	$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
	if (mysqli_num_rows($selectUserQuery) == 0)
		$error = "ERROR: Invalid username/password";
	else {
		$row = mysqli_fetch_array($selectUserQuery);
		$initials = $row[0];
		$_SESSION['initials'] = $initials;
		$_SESSION['username'] = $login;

		$types = explode(",", $row[1]);
		for ($i = 0; $i < sizeof($types); $i++)
			$_SESSION[$types[$i]] = $i;

		if (isset($_SESSION['Inactive'])) {
			$error = "ERROR: This username has been disabled";
			session_destroy();
			unset($_SESSION['initials']);
			unset($_SESSION['username']);
		}
	}
} else if (isset($_REQUEST['logout'])) {
	/* Free all files marked as edited by this user */
	$username = isset($_SESSION['username']) ? mysqli_real_escape_string($conn, $_SESSION['username']) : '';
	$updateOrderSql = "UPDATE orders SET lockedByName = NULL WHERE lockedByName = '$username'";
	mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
	session_destroy();
	unset($_SESSION['initials']);
	unset($_SESSION['username']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/OMS.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<script language="javascript" src="oms.js"></script>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>Welcome to OMS</title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>

<body>
	<!-- InstanceBeginEditable name="topbody" -->
	<?php
	if (!isset($_SESSION['initials'])) {
	?>

		<table style="border:0; width:100%; height:100%; position:relative; top:200px;">
			<tr>
				<td align="center" style="vertical-align:middle">
					<img src="oms.gif" /><br />
					<span style="color:#FF0000"><?php echo $error; ?></span>
					<form action="index2.php" method="post">
						<table border=0>
							<tr>
								<td align=left>
									<font face=arial size=2>Username
								</td>
								<td align=right><input name="login" value="" size=20>
									<font face=arial size=2>
								</td>
							</tr>
							<tr>
								<td align=left>
									<font face=arial size=2>Password
								</td>
								<td align=right><input name="pass" value="" size=20 type="password">
									<font face=arial size=2>
								</td>
							</tr>
						</table>
						<input type=submit name='justloggedin' value="Log In" style="font-family: Arial;text-decoration: none;background-color: #FFFFFF;border: 1px solid;" />
					</form>
				</td>
			</tr>
		</table>
	<?php } else { ?>
		<!-- InstanceEndEditable -->
		<?php include('header.php'); ?>
		<!-- InstanceBeginEditable name="body" -->
	<?php } ?>
	<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->

</html>