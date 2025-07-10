<?php
if ($_POST) {
	if (isset($_POST['filterCity_fix']) && ($_POST['filterCity_fix'] != "")) {
	}
}

if (isset($_GET['zmp']) && $_GET['zmp'] != "") { ?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<title>OMS Control Panel</title>
	</head>

	<body>
		<style>
			.centered {
				position: fixed;
				top: 50%;
				left: 50%;
				margin-top: -20px;
				margin-left: -100px;
			}
		</style>
		<?php
		$fup = base64_decode($_GET['zmp']);
		$fup = explode(";", $fup);

		$fup2 = [];
		foreach ($fup as $sz) {
			$sz = explode(",", $sz);
			if (isset($sz[1])) {
				$key = $sz[0];
				$str = $sz[1];
				$fup2[$key] = $str;
			}
		}

		unset($fup2['searchSubmit']);
		unset($fup2['filterCity_select']);
		unset($fup2['filterCity_fix']);

		$fupLINES = "";

		foreach ($fup2 as $k => $s) {
			if ($k !== "") {
				$s = $s !== null ? addslashes($s) : '';
				$marker = ':';
				if ($k === "LFC") {
					$k = '';
					$s = '';
					$marker = '';
				}
				$fupLINES .= "\\n$k" . $marker . "   $s";
			}
		}

		?>

		<div class=centered>
		</div>
		<script>
			<?php
			$yolo = "No Match Found\\n" . "\\n" . "Filters,\\n" . $fupLINES;
			echo "alert('$yolo');";
			$fwdP = '';
			foreach ($fup2 as $k => $s) {
				if ($k != "") {
					$s = addslashes($s);

					$fwdP .= "$k=$s&";
				}
			}
			?>
			window.location = "<?php echo BASE_URL; ?>control.php?<?php echo $fwdP; ?>form=1";
		</script>

	</body>

	</html>

<?php
	exit;
}

// ini_set("memory_limit", "-1");
// ob_start("ob_gzhandler");

function ob_html_compress($buf)
{
	return str_replace(array("\n", "\r", "\t"), '', $buf);
}

include('database.php');
include('config.php');
session_start();
if (!isset($_SESSION['initials']))
	header('Location: index2.php');


if (isset($_POST['searchSubmit'])) {
	include('database.php');
	include('config.php');

	if ($_POST['orderType'] != "All") {
		$selectClientOrderSql = "SELECT `clients`.name, `clients`.contact, `clients`.address, `clients`.phone,`clients`.email, orders.orderDate, `clients`.id, orders.id, orders.repID, `orders`.type,`clients`.state,`clients`.city
        ,`orders`.category, `clients`.zip FROM clients ";
		$selectClientOrderSql .= "JOIN orders ON clients.id = orders.clientID WHERE orders.category = '" . $_POST['orderType'] . "' AND clients.email IS NOT NULL ";
		$selectClientOrderSql .= " AND clients.email != ''  ";
	} else {
		$selectClientOrderSql = "SELECT `clients`.name, `clients`.contact, `clients`.address, `clients`.phone,`clients`.email, ";
		$selectClientOrderSql .= "orders.orderDate, `clients`.id, orders.id, orders.repID, `orders`.type,`clients`.state,`clients`.city,`orders`.category, `clients`.zip
         FROM clients JOIN orders ON clients.id = orders.clientID WHERE clients.email IS NOT NULL ";
		$selectClientOrderSql .= " AND clients.email != ''  ";
	}

	$sortYear2 = $_POST['filterYear2'];
	$sortYear = $_POST['filterYear'];

	$sortMonth = $_POST['filterMonth'];
	$sortMonth2 = $_POST['filterMonth2'];

	$sortType = 'orderDate';

	if ($sortMonth != "any")
		$selectClientOrderSql .= "	AND (MONTH(orders.$sortType) BETWEEN '$sortMonth' AND '$sortMonth2') 	";
	if ($sortYear != "any")
		if (!is_numeric($yearDONT)) {
			$selectClientOrderSql .= "	AND (YEAR(orders.$sortType) BETWEEN '$sortYear' AND '$sortYear2') 	";
		}

	$selectClientOrderSql .= "  GROUP BY clients.email, clients.name ";

	$selectClientOrderSql .= " ORDER BY orders.orderDate DESC";

	$selectClientOrderQuery = mysqli_query($conn, $selectClientOrderSql) or die(mysqli_error($conn));

	$tsv  = array();
	$html = array();

	while ($row = mysqli_fetch_array($selectClientOrderQuery, MYSQLI_NUM)) {

		$newRow['name'] = $row[0];
		$newRow['contact'] = $row[1];
		$newRow['address'] = $row[2];
		$newRow['phone'] = $row[3];
		$newRow['email'] = $row[4];
		$newRow['orderDate'] = $row[5];
		$newRow['clientsID'] = $row[6];
		$newRow['ordersID'] = $row[7];
		$newRow['ordersTYPE'] = $row[9];
		$newRow['state'] = $row[10];
		$newRow['city'] = $row[11];
		$newRow['category'] = $row[12];
		$newRow['zip'] = $row[13];

		$selectUserSql = 'SELECT name FROM users WHERE id = "' . $row[8] . '"';
		$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
		while ($rowF = mysqli_fetch_array($selectUserQuery, MYSQLI_NUM)) {
			$newRow['repsID'] = $rowF[0];
		}

		$rowdebug['orderDate'] = $row[5];
		$rowdebug['name'] = $row[0];
		$rowdebug['ordersType'] = $row[9];
		$rowdebug['category'] = $row[12];


		$filterSTATUSarray = [];

		$rowSetsDEBUG[] = $rowdebug;

		$rowSetsDEBUGtwo[] = $row[5] . ' , ' . $row[9] . ' , ' . $row[12];

		$row = $newRow;
		$aidset = isset($row[7]) ? $row[7] : null;

		$filterSTATUS = 1;

		$checkreprow = $newRow['state'];
		$checkrep = $_POST['filterState'];
		if ($checkrep == "All") {
			$checkrep = '';
		}
		if ($checkrep != "") {
			if ("$checkreprow" == "$checkrep") {
				$filterSTATUSarray['state'] = 1;
			} else {
				$filterSTATUSarray['state'] = 0;
			}
		}

		$checkreprow = $newRow['city'];
		$checkrep = $_POST['filterCity'];

		if ($checkrep == "All") {
			$checkrep = '';
		}
		if ($checkrep != "") {
			if ("$checkreprow" == "$checkrep") {
				$filterSTATUSarray['city'] = 1;
				echo "STATUS city set:  " . $filterSTATUSarray['city'] . "<br/>";
			} else {
				$filterSTATUSarray['city'] = 0;
			}
		}

		$checkreprow = $newRow['repsID'];
		$checkrep = $_POST['filterRep'];
		if ($checkrep == "All") {
			$checkrep = '';
		}
		if ($checkrep != "") {
			if ($checkreprow != "") {
				if ("$checkreprow" == "$checkrep") {
					$filterSTATUSarray['rep'] = 1;
				} else {
					$filterSTATUSarray['rep'] = 0;
				}
			}
		}

		$checktyperow = $newRow['ordersTYPE'];
		$checktype = $_POST['filterType'];
		if ($checktype == "All") {
			$checktype = '';
		}
		if ($checktype != "") {
			if ("$checktyperow" != "") {
				if ("$checktyperow" == "$checktype") {
					$filterSTATUSarray['type'] = 1;
				} else {
					$filterSTATUSarray['type'] = 0;
				}
			}
		}


		if (is_array($filterSTATUSarray)) {

			foreach ($filterSTATUSarray as $fstr) {
				if ($fstr == "0") {
					$filterSTATUS = 0;
				}
			}
		}

		if ($filterSTATUS == "1") {

			$rowSets[] = $row;

			$rowMagicA[0] = 'Customer Name';
			$rowMagicA[1] = 'Contact Name';
			$rowMagicA[2] = 'Address';
			$rowMagicA[3] = 'Phone Number';
			$rowMagicA[4] = 'Email Address';
			$rowMagicA[5] = 'Date';

			$rowMagic['name'] = $newRow['name'];
			$rowMagic['contact'] = $newRow['contact'];
			$rowMagic['address'] = $newRow['address'] . ', ' . $newRow['city'] . ', ' . $newRow['state'] . ', ' . $newRow['zip'];
			$rowMagic['phone'] = $newRow['phone'];
			$rowMagic['email'] = $newRow['email'];
			$rowMagic['date'] = $newRow['orderDate'];

			$rowMagic_print = $rowMagic;
			$rowMagicA_print = $rowMagicA;

			$rowMagicTRIGGER = "";

			if ($rowMagicTRIGGER == "") {
				$html[] = "<tr><td>" . implode("</td><td>", $rowMagicA_print) .              "</td></tr>";
				$tsv[]  = implode("\t", $rowMagicA_print);
				$rowMagicTRIGGER = 1;
			}

			$tsv[]  = implode("\t", $rowMagic_print);
			$html[] = "<tr><td>" . implode("</td><td>", $rowMagic_print) .              "</td></tr>";
		}
	}

	$tsv = implode("\r\n", $tsv);
	$html = "<table border=1>" . implode("\r\n", $html) . "</table>";

	if ($_POST['filterRep'] != "") {
		if ($_POST['filterRep'] != "All") {
			$ext = "." . strtoupper($_POST['filterRep']) . ".xls";
		} else {
			$ext = ".xls";
		}
	}

	$fileName = str_replace(" ", "", $_POST['orderType'] . $ext);

	if ($tsv != "") {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName");
		echo $tsv;
	} else {

		foreach ($_POST as $zk => $zs) {
			$zARY[$zk] = $zs;
		}
		foreach ($zARY as $zk => $zs) {
			$zSTR .= "$zk,$zs;";
		}

		$zmpcoded = base64_encode($zSTR);

		header("Location: control.php?zmp=$zmpcoded");
	}
} else {

	function addSmartQuotes($string)
	{
		if (is_null($string) || $string === '') {
			return $string;
		}

		$replace = array(chr(146), chr(148));
		$search = array("'", '"');
		return str_replace($search, $replace, $string);
	}

	if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "orderID=") && strstr($_SERVER['HTTP_REFERER'], "edit=1")) {
		$oldID = strstr($_SERVER['HTTP_REFERER'], "orderID=");
		$oldID = substr($oldID, 8, strpos($oldID, "&") - 8);
		$updateOrderSql = "UPDATE orders SET lockedByName=NULL WHERE id='" . $oldID . "'";
		mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
	}
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<script language="javascript" src="oms.js"></script>
		<title>OMS Control Panel</title>
	</head>

	<body>
		<?php include('header.php'); ?>

		<script type="text/javascript">
			var numToDelete = 0;

			function addToDelete(id) {
				if (document.forms['mainForm'].elements['check' + id].checked)
					numToDelete++;
				else
					numToDelete--;
				return true;
			}

			function deleteMarkedOrders() {
				var deleteDiv = document.getElementById('deleteDiv');
				deleteDiv.style.visibility = 'visible';
				document.getElementById('numberToDelete').innerHTML = numToDelete;
			}

			function editClientWithInfo(id, name, contact, address, city, state, zip, phone, email) {
				var clientView = document.getElementById('clientView');
				document.forms['clientForm'].elements['id'].value = id;
				document.forms['clientForm'].elements['name'].value = name;
				document.forms['clientForm'].elements['contact'].value = contact;
				document.forms['clientForm'].elements['address'].value = address;
				document.forms['clientForm'].elements['city'].value = city;
				document.forms['clientForm'].elements['state'].value = state;
				document.forms['clientForm'].elements['zip'].value = zip;
				document.forms['clientForm'].elements['phone'].value = phone;
				document.forms['clientForm'].elements['email'].value = email;
				clientView.style.visibility = 'visible';
			}

			function submitEditOfClient() {
				var clientView = document.getElementById('clientView');
				var id = document.forms['clientForm'].elements['id'].value;
				var name = document.forms['clientForm'].elements['name'].value;
				var contact = document.forms['clientForm'].elements['contact'].value;
				var address = document.forms['clientForm'].elements['address'].value;
				var city = document.forms['clientForm'].elements['city'].value;
				var state = document.forms['clientForm'].elements['state'].value;
				var zip = document.forms['clientForm'].elements['zip'].value;
				var phone = document.forms['clientForm'].elements['phone'].value;
				var email = document.forms['clientForm'].elements['email'].value;
				var submitString = "editClient.php?id=" + id + "&name=" + name + "&contact=" + contact + "&address=" + address + "&city=" + city + "&state=" + state + "&zip=" + zip + "&phone=" + phone + "&email=" + email;

				var xmlHttpReq = false;
				var self = this;
				// Mozilla/Safari
				if (window.XMLHttpRequest)
					self.xmlHttpReq = new XMLHttpRequest();
				// IE
				else if (window.ActiveXObject)
					self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
				self.xmlHttpReq.open('POST', submitString, true);
				self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				self.xmlHttpReq.onreadystatechange = function() {
					if (self.xmlHttpReq.readyState == 4)
						clientUpdated(self.xmlHttpReq.responseText);
				}
				self.xmlHttpReq.send(null);
			}

			function clientUpdated(str) {
				var clientView = document.getElementById('clientView');
				var clientOkView = document.getElementById('clientOkView');
				var clientUpdateMessage = document.getElementById('clientUpdateMessage');

				clientUpdateMessage.innerHTML = "Client has been updated.<br>Changes will appear next time you view the client. " + str;
				clientOkView.style.visibility = 'visible';
				clientView.style.visibility = 'hidden';
			}
		</script>
		<div id='clientView' style="position:fixed; padding:2px; top:200px; left:200px; width:350px; height:180px; border:4px solid #000000; text-align:right; background-color:#FAFAFF; visibility:hidden; z-index:300">
			<form name='clientForm'>
				<input type='hidden' name="id" />
				<label>Client: <input class="text" name='name' value="" style="width: 230px;" /></label><br />
				<label>Contact: <input class="text" name='contact' value="" style="width: 230px;" /></label><br />
				<label>Address: <input class="text" name="address" value="" style="width: 230px;" /></label><br />
				<label>City, State, Zip: <input class="text" name="city" value="" style="width: 136px;" />
					<input class="text" name="state" value="" style="width: 30px; margin:2px 0px;" />
					<input class="text" name="zip" value="" style="width: 50px;" /></label><br />
				<label>Phone or Fax: <input class="text" name="phone" value="" style="width: 230px;" /></label><br />
				<label>E-mail: <input class="text" name="email" value="" style="width: 230px;" /></label><br />
				<input type="button" value="Cancel" onclick="document.getElementById('clientView').style.visibility='hidden';" />
				<input type="button" value="Submit" onclick="submitEditOfClient()" />
			</form>
		</div>
		<div id='clientOkView' style="position:fixed; padding:4px; top:240px; left:200px; width:350px; height:100px; border:4px solid #000000; text-align:center; background-color:#FAFAFF; visibility:hidden; z-index:301">
			<span style="color:darkgreen" id='clientUpdateMessage'></span><br /><br />
			<input type="button" value="Ok" onclick="document.getElementById('clientOkView').style.visibility='hidden';" />
		</div>
		<div style="position:relative; top:20px; text-align:center; width:99%">
			<form name='mainForm' method="post">
				<!-- EDIT USERS -->
				<?php
				if (isset($_POST['editUsers']) || isset($_POST['resetPassword']) || isset($_POST['newUserAdd'])) {

					if (!isset($_POST['editUser']) || isset($_POST['resetPassword']) || isset($_POST['newUserAdd'])) {
						$selectUserSql = "SELECT name, type FROM users";
						$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
						$adminString = "";
						$artistString = "";
						$crewString = "";
						$repString = "";
						$inactiveString = "";
						while ($result = mysqli_fetch_array($selectUserQuery)) {
							if (strpos($result['type'], 'Admin') !== FALSE)
								$adminString .= "<option>" . $result['name'] . "</option>";
							if (strpos($result['type'], 'Artist') !== FALSE)
								$artistString .= "<option>" . $result['name'] . "</option>";
							if (strpos($result['type'], 'Crew') !== FALSE)
								$crewString .= "<option>" . $result['name'] . "</option>";
							if (strpos($result['type'], 'Rep') !== FALSE)
								$repString .= "<option>" . $result['name'] . "</option>";
							if (strpos($result['type'], 'Inactive') !== FALSE)
								$inactiveString .= "<option>" . $result['name'] . "</option>";
						}
						$fullString = '<select name="editUser">
								<optgroup label="Admin">' . $adminString . '</optgroup>
								<optgroup label="Artist">' . $artistString . '</optgroup>
								<optgroup label="Crew">' . $crewString . '</optgroup>
								<optgroup label="Rep">' . $repString . '</optgroup>
								<optgroup label="Inactive">' . $inactiveString . '</optgroup>
								</select><br /><br /><input type="submit" name="editUsers" value="Edit User"/>
								<br /><br /><input type="submit" name="resetPassword" value="Reset Password" />';

						/* Reset password */
						if (isset($_POST['resetPassword'])) {
							$updateUserSql = "UPDATE users SET password = MD5('password') WHERE name='" . $_POST['editUser'] . "'";
							mysqli_query($conn, $updateUserSql) or die(mysqli_error($conn));
							$message = ['status' => 'success', 'message' => $_POST['editUser'] . "'s password has been reset to 'password'"];
						} else if (isset($_POST['modUser'])) {
							$modUserString = "";
							if (isset($_POST['modAdmin']))
								$modUserString .= "Admin,";
							if (isset($_POST['modArtist']))
								$modUserString .= "Artist,";
							if (isset($_POST['modCrew']))
								$modUserString .= "Crew,";
							if (isset($_POST['modRep']))
								$modUserString .= "Rep,";
							if (isset($_POST['modInactive']))
								$modUserString .= "Inactive,";
							$modUserString = trim($modUserString, ",");

							$updateUserSql = "UPDATE users SET type='" . $modUserString . "' WHERE name='" . $_POST['modUser'] . "'";
							mysqli_query($conn, $updateUserSql) or die(mysqli_error($conn));
							$message = ['status' => 'success', 'message' => $_POST['modUser'] . "'s type has been successfully modified"];
						} else if (isset($_POST['newUserName'])) {
							$newName = trim($_POST['newUserName']);
							$newInitials = trim($_POST['newUserInitials']);
							if (empty($newName) || empty($newInitials)) {
								$message = ['status' => 'error', 'message' => "Error: 'Name' and 'Initials' fields are required."];
							} else {
								$newType = "";
								if (isset($_POST['newAdmin'])) $newType .= "Admin,";
								if (isset($_POST['newArtist'])) $newType .= "Artist,";
								if (isset($_POST['newCrew'])) $newType .= "Crew,";
								if (isset($_POST['newRep'])) $newType .= "Rep,";
								$newType = trim($newType, ",");

								if ($newType == "") $newType = "Crew";

								$insertUserSql = "INSERT INTO users (name, initials, type) VALUES ('" . $newName . "','" . $newInitials . "','" . $newType . "')";
								mysqli_query($conn, $insertUserSql) or die(mysqli_error($conn));
								$message = ['status' => 'success', 'message' => $newName . " (" . $newInitials . ") has been created with default password of 'password'"];
							}
						}
					} else {
						$name = $_POST['editUser'];
						$selectUserSql = "SELECT type FROM users WHERE name='" . $name . "'";
						$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
						$result = mysqli_fetch_array($selectUserQuery);
						$fullString = '<span style="font-weight:bold; font-size:1.3em">' . $name . '</span><br /><div style="width:100%; text-align:right">';

						if (strpos($result[0], 'Admin') !== FALSE)
							$fullString .= '<label>Admin <input type="checkbox" name="modAdmin" checked="1" /></label><br />';
						else
							$fullString .= '<label>Admin <input type="checkbox" name="modAdmin" /></label><br />';
						if (strpos($result[0], 'Artist') !== FALSE)
							$fullString .= '<label>Artist <input type="checkbox" name="modArtist" checked="1" /></label><br />';
						else
							$fullString .= '<label>Artist <input type="checkbox" name="modArtist" /></label><br />';
						if (strpos($result[0], 'Crew') !== FALSE)
							$fullString .= '<label>Crew <input type="checkbox" name="modCrew" checked="1" /></label><br />';
						else
							$fullString .= '<label>Crew <input type="checkbox" name="modCrew" /></label><br />';
						if (strpos($result[0], 'Rep') !== FALSE)
							$fullString .= '<label>Rep <input type="checkbox" name="modRep" checked="1" /></label><br />';
						else
							$fullString .= '<label>Rep <input type="checkbox" name="modRep" /></label><br />';
						if (strpos($result[0], 'Inactive') !== FALSE)
							$fullString .= '<label>Inactive <input type="checkbox" name="modInactive" checked="1" /></label><br />';
						else
							$fullString .= '<label>Inactive <input type="checkbox" name="modInactive" /></label><br />';

						$fullString .= '</div><br /><input type="hidden" name="modUser" value=' . $name . ' /><input type="submit" name="editUsers" value="Modify User"/>';
					}
				?>
					<span style="color:<?= isset($message['status']) && $message['status'] == 'success' ? 'darkgreen' : '#FF0000'; ?>"><?php echo $message['message'] ?? ''; ?></span><br /><br />
					<fieldset style="text-align:right; width:130px; margin:auto">
						<legend>Add User</legend>
						<label>Name <input name="newUserName" style="width:80px; text-align:center" /></label><br />
						<label>Initials <input name="newUserInitials" style="width:40px; text-align:center" maxlength="4" /></label><br />
						<label>Admin <input type="checkbox" name="newAdmin" /></label><br />
						<label>Artist <input type="checkbox" name="newArtist" /></label><br />
						<label>Crew <input type="checkbox" name="newCrew" /></label><br />
						<label>Rep <input type="checkbox" name="newRep" /></label><br /><br />
						<div style="width:100%; text-align:center"><input type="submit" name="newUserAdd" value="Add User" /></div>
					</fieldset><br /><br />

					<fieldset style="width:130px; margin:auto; text-align:center">
						<legend>Edit User</legend>
						<?php echo $fullString; ?>
					</fieldset>
				<?php } else if (isset($_POST['editClients'])) {
				?>
					<!-- EDIT CLIENT LIST -->
					<input type="submit" value="A-D" name="editClients" />&nbsp;<input type="submit" value="E-H" name="editClients" />&nbsp;<input type="submit" value="I-L" name="editClients" />&nbsp;<input type="submit" value="M-P" name="editClients" />&nbsp;<input type="submit" value="Q-T" name="editClients" />&nbsp;<input type="submit" value="U-Z" name="editClients" />
					<table border=1 width=100% cellpadding=1 cellspacing=1>
						<tr>
							<td align="center"> </td>
							<td align="center">Client Name</td>
							<td align="center">Contact</td>
							<td align="center">Address</td>
							<td align="center">City</td>
							<td align="center">State</td>
							<td align="center">Zip</td>
							<td align="center">Phone</td>
							<td align="center">Email</td>
						</tr>
						<?php
						$modifierString = " ( UPPER(SUBSTRING(name,1,1)) NOT BETWEEN 'A' and 'Z' OR UPPER(SUBSTRING(name,1,1)) BETWEEN 'A' and 'D') ";
						if ($_POST['editClients'] == "E-H")
							$modifierString = " UPPER(SUBSTRING(name,1,1)) BETWEEN 'E' and 'H' ";
						else if ($_POST['editClients'] == "I-L")
							$modifierString = " UPPER(SUBSTRING(name,1,1)) BETWEEN 'I' and 'L' ";
						else if ($_POST['editClients'] == "M-P")
							$modifierString = " UPPER(SUBSTRING(name,1,1)) BETWEEN 'M' and 'P' ";
						else if ($_POST['editClients'] == "Q-T")
							$modifierString = " UPPER(SUBSTRING(name,1,1)) BETWEEN 'Q' and 'T' ";
						else if ($_POST['editClients'] == "U-Z")
							$modifierString = " UPPER(SUBSTRING(name,1,1)) BETWEEN 'U' and 'Z' ";

						$selectUserSql = "SELECT id, name, contact, address, city, state, zip, phone, email FROM clients WHERE visible = 1 AND " . $modifierString . " ORDER BY name ASC";
						$electUserQuery = mysqli_query($conn, $selectUserSql);
						$highestID = 0;
						$count = 0;

						while ($result = mysqli_fetch_array($electUserQuery)) {
							if ($highestID < $result['id'])
								$highestID = $result['id'];

							$id = addSmartQuotes($result['id']);
							$name = addSmartQuotes($result['name']);
							$contact = addSmartQuotes($result['contact']);
							$address = addSmartQuotes($result['address']);
							$city = addSmartQuotes($result['city']);
							$state = addSmartQuotes($result['state']);
							$zip = addSmartQuotes($result['zip']);
							$phone = addSmartQuotes($result['phone']);
							$email = addSmartQuotes($result['email']);
						?>
							<!-- EDIT CLIENT LIST action -->
							<tr>
								<td align="center"><input type="checkbox" name="check<?php echo $result['id'] ?>" onclick="return addToDelete('<?php echo $result['id']; ?>');" /></td>
								<td align="center"><?php echo wordwrap($result['name'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['contact'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['address'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['city'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['state'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['zip'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['phone'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><?php echo wordwrap($result['email'] ?? '', 15, " ", 1); ?></td>
								<td align="center"><input type="button" value="Edit" onclick="editClientWithInfo(<?php echo "'" . $id . "','" . $name . "','" . $contact . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "','" . $phone . "','" . $email . "'"; ?>);" /></td>
							</tr>
						<?php } ?>
					</table>
					<input type="hidden" name="highestID" value="<?php echo $highestID; ?>" />
					<div id="deleteDiv" style="position:fixed; left:10px; bottom:10px; width:200px; height:75px; visibility:hidden; background-color:#CCCCCC; border:3px solid #000000; padding:4px">
						Are you sure you want to delete these <span id="numberToDelete">9</span> clients?<br /><br />
						<input type="button" value="No" onclick="document.getElementById('deleteDiv').style.visibility='hidden';" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete" value="Yes" />
					</div>
					<br />
					<input type="button" name="deleteButton" value="Delete marked clients" onclick="deleteMarkedOrders();" />
			</form>
		<?php
				} else if (isset($_POST['collateEmails'])) {
					$colVal = $_POST['collateEmails'];
		?>
			<label>Narrow results by Category:
				<select name="collateEmails" style="width: 200px;" onchange='this.form.submit()'>
					<option value="All">All Categories</option>
					<option value="Screen Print" <?php if ($colVal == "Screen Print") echo "selected=1" ?>>Screen Print</option>
					<option value="Logo Magnet" <?php if ($colVal == "Logo Magnet") echo "selected=1" ?>>Logo Magnet</option>
					<option value="LM-Dealer" <?php if ($colVal == "LM-Dealer") echo "selected=1" ?>>LM-Dealer</option>
					<option value="C-S-Shirts" <?php if ($colVal == "C-S-Shirts") echo "selected=1" ?>>C-S-Shirts</option>

					<option value="CMD" <?php if ($colVal == "CMD") echo "selected=1" ?>>CMD</option>
					<option value="Promotional" <?php if ($colVal == "Promotional") echo "selected=1" ?>>Promotional</option>
					<option value="Embroidery" <?php if ($colVal == "Embroidery") echo "selected=1" ?>>Embroidery</option>
					<option value="Signs" <?php if ($colVal == "Signs") echo "selected=1" ?>>Signs</option>
					<option value="Other" <?php if ($colVal == "Other") echo "selected=1" ?>>Other</option>
				</select></label><br /><br />
			<?php

					if ($colVal == "C-S-Shirts")
						$colVal = "Logo Ventures";

					if ($colVal == 'All' || $colVal == "Collate Email Addresses")
						$selectUserSql = "SELECT email FROM clients WHERE visible=1 GROUP BY email";
					else
						$selectUserSql = "SELECT c.email FROM clients AS c INNER JOIN orders AS o ON c.id=o.clientID WHERE c.visible=1 AND o.category='" . $colVal . "' GROUP BY c.email";

					$selectUserQuery = mysqli_query($conn, $selectUserSql);

					$i = 0;
					while ($result = mysqli_fetch_array($selectUserQuery)) {
						$cEmail = $result['email'] ?? '';
						if ($cEmail !== '' && strstr($cEmail, '@') && !strstr($cEmail, '�') && !strstr($cEmail, '(') && !strstr($cEmail, '<') && !strstr($cEmail, '>'))
							echo $result['email'] . ", ";
					}
				} else if (isset($_POST['highestID'])) {
					$total = 0;
					for ($i = 0; $i <= $_POST['highestID']; $i++) {
						if (isset($_POST['check' . $i])) {
							$total++;
							$updateClientSql = "UPDATE clients SET visible = 0 WHERE id=" . $i;
							mysqli_query($conn, $updateClientSql);
						}
					}

					echo "Deleted " . $total . " clients";

					/** CHANGE PASSWORD */
				} else if (isset($_POST['changePassword']) || isset($_POST['pass1'])) {
					if (isset($_POST['changePW'])) {
						if (isset($_POST['pass1']) && !empty($_POST['pass1'])) {
							$message = ['status' => 'success', 'message' => "Your password has successfully been changed"];
							if ($_POST['pass1'] == $_POST['pass2']) {
								$updateUserSql = "UPDATE users SET password = MD5('" . $_POST['pass1'] . "') WHERE initials='" . $_SESSION['initials'] . "'";
								mysqli_query($conn, $updateUserSql) or die(mysqli_error($conn));
							} else {
								$message = ['status' => 'error', 'message' => "ERROR: Your password fields did not match"];
							}
						} else {
							$message = ['status' => 'error', 'message' => "ERROR: Please fill in both password fields"];
						}
					} ?>
			<span style="color:<?= isset($message['status']) && $message['status'] == 'success' ? 'darkgreen' : '#FF0000'; ?>"><?php echo $message['message'] ?? ''; ?></span>
			<br />
			<label>New Password<br /><input type="password" name="pass1" /></label><br /><br />
			<label>Retype Password<br /><input type="password" name="pass2" /></label><br /><br />
			<input type="submit" name="changePW" value="Change Password" />
			<?php } else {
					if (isset($_SESSION['Admin'])) { ?>

				<!-- DEFAULT -->
				<input type="submit" value="Edit Users" name="editUsers" style="width:150px;" /><br /><br /> <?php } ?>
			<input type="submit" value="Edit Client List" name="editClients" style="width:150px;" /><br /><br />
			<input type="submit" value="Collate Email Addresses" name="collateEmails" style="width:150px;" /><br /><br />
			<input type="submit" value="Change Password" name="changePassword" style="width:150px;" /><br /><br />

			<form action="control.php" id="searchForm" name="searchForm" method="post" target="_blank">



				<?php
					$states['All'] = 'All';
					$states['AL'] = 'Alabama';
					$states['AK'] = 'Alaska';
					$states['AZ'] = 'Arizona';
					$states['AR'] = 'Arkansas';
					$states['CA'] = 'California';
					$states['CO'] = 'Colorado';
					$states['CT'] = 'Connecticut';
					$states['DE'] = 'Delaware';
					$states['FL'] = 'Florida';
					$states['GA'] = 'Georgia';
					$states['HI'] = 'Hawaii';
					$states['ID'] = 'Idaho';
					$states['IL'] = 'Illinois';
					$states['IN'] = 'Indiana';
					$states['IA'] = 'Iowa';
					$states['KS'] = 'Kansas';
					$states['KY'] = 'Kentucky';
					$states['LA'] = 'Louisiana';
					$states['ME'] = 'Maine';
					$states['MD'] = 'Maryland';
					$states['MA'] = 'Massachusetts';
					$states['MI'] = 'Michigan';
					$states['MN'] = 'Minnesota';
					$states['MS'] = 'Mississippi';
					$states['MO'] = 'Missouri';
					$states['MT'] = 'Montana';
					$states['NE'] = 'Nebraska';
					$states['NV'] = 'Nevada';
					$states['NH'] = 'New Hapshire';
					$states['NJ'] = 'New Jersey';
					$states['NM'] = 'New Mexico';
					$states['NY'] = 'New York';
					$states['NC'] = 'North Carolina';
					$states['ND'] = 'North Dakota';
					$states['OH'] = 'Ohio';
					$states['OK'] = 'Oklahoma';
					$states['OR'] = 'Oregon';
					$states['PA'] = 'Pennsylvania';
					$states['RI'] = 'Rhode Island';
					$states['SC'] = 'South Carolina';
					$states['SD'] = 'South Dakota';
					$states['TN'] = 'Tennessee';
					$states['TX'] = 'Texas';
					$states['UT'] = 'Utah';
					$states['VT'] = 'Vermont';
					$states['VA'] = 'Virginia';
					$states['WA'] = 'Washington';
					$states['WV'] = 'West Virginia';
					$states['WI'] = 'Wisconsin';
					$states['WY'] = 'Wyoming';
					$states['DC'] = 'Washington DC';

					foreach ($states as $kode => $stay) {
						$statesBACKWARDS["$stay"] = "$kode";
					}
				?>

				<?php
					$getform = '';
					if (isset($_GET['form']) && ($_GET['form'] == "1")) {
						$getform = $_GET;
					}
				?>

				<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:
				<select id="filterState" name="filterState" style="width:154px;margin-right:0px"><!--onChange="process2(this)">-->
					<?php
					foreach ($states as $statecode => $statename) {
						$sstr = '';
						if (isset($getform['filterState']) && ($getform['filterState'] == "$statecode")) {
							$sstr = 'selected';
						}

					?>
						<option value="<?php echo $statecode; ?>" <?php echo $sstr; ?>><?php echo $statename; ?></option>
					<?php
						if ($sstr != "") {
							$kodestated = $statecode;
						}
					} ?>
				</select>
				<br />

				<div id="pleasewaitforcitydata" style="display:block;">
					<br />
					<br />
					<center>
						<table border=0>
							<tr>
								<td><img src="load.gif" alt="[loading]"></td>
								<td>&nbsp;</td>
								<td>
									<h3> Please Wait . . . .</h3>
								</td>
							</tr>
						</table>
					</center>
				</div>
				<?php
					if (!file_exists("statecityUSonly.csv")) {
						echo "<span style='color:#FF0000'>ERROR: File not found.</span>\n";
						exit;
					}
					$effpee = fopen("statecityUSonly.csv", "r");
					if ($effpee == FALSE) {
						echo "<span style='color:#FF0000'>ERROR: Check feed file.</span>\n";
						exit;
					}
					while (($datas = fgetcsv($effpee, 8192, ",")) !== FALSE) {
						$state = $datas['0'];
						$citydata["$state"][] = $datas[2];
					}
				?>

				<?php
					foreach ($states as $statecode => $statename) { ?>
					<div id="<?php echo $statecode; ?>" style="display:none;">
						<?php if ($statename != "All") { ?>
							<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (<?php echo $statename; ?>) City:
							<select name="filterCity_select" id="filterCity_select" style="width:154px;margin-right:0px" onChange="process1(this)">
								<option value="All">All</option>
								<?php
								$echome = '';
								if (isset($citydata[$statecode]) && is_array($citydata[$statecode])) {
									foreach ($citydata[$statecode] as $cityname) {
										$sstr = '';
										if (isset($getform['filterCity']) && ($getform['filterCity'] == "$cityname")) {
											$sstr = 'selected';
											$mycitystring = $cityname;
										}
										$echome .= "<option value=\"$cityname\"  $sstr>$cityname,$statecode</option>";
									}
								}
								echo $echome;
								?>
							</select>
							<br />
					<?php }
						echo '</div>';
					} ?>
					<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
					<script>
						$('#filterState').change(function() {
							HideContent('AL');
							HideContent('AK');
							HideContent('AZ');
							HideContent('AR');
							HideContent('CA');
							HideContent('CO');
							HideContent('CT');
							HideContent('DE');
							HideContent('FL');
							HideContent('GA');
							HideContent('HI');
							HideContent('ID');
							HideContent('IL');
							HideContent('IN');
							HideContent('IA');
							HideContent('KS');
							HideContent('KY');
							HideContent('LA');
							HideContent('ME');
							HideContent('MD');
							HideContent('MA');
							HideContent('MI');
							HideContent('MN');
							HideContent('MS');
							HideContent('MO');
							HideContent('MT');
							HideContent('NE');
							HideContent('NV');
							HideContent('NH');
							HideContent('NJ');
							HideContent('NM');
							HideContent('NY');
							HideContent('NC');
							HideContent('ND');
							HideContent('OH');
							HideContent('OK');
							HideContent('OR');
							HideContent('PA');
							HideContent('RI');
							HideContent('SC');
							HideContent('SD');
							HideContent('TN');
							HideContent('TX');
							HideContent('UT');
							HideContent('VT');
							HideContent('VA');
							HideContent('WA');
							HideContent('WV');
							HideContent('WI');
							HideContent('WY');
							HideContent('DC');

							ShowContent($(this).val());
							document.getElementById($(this).val()).value = (document.getElementById("filterCity_select").value);
							document.getElementById("filterCity").value = 'All';

						});

						function process1(showed) {
							document.getElementById("filterCity").value = showed.value;
						}

						function process2(showed) {}
					</script>
					<script type="text/javascript" language="JavaScript">
						function HideContent(d) {
							document.getElementById(d).style.display = "none";
						}

						function ShowContent(d) {
							document.getElementById(d).style.display = "block";
						}

						function ReverseDisplay(d) {
							if (document.getElementById(d).style.display == "none") {
								document.getElementById(d).style.display = "block";
							} else {
								document.getElementById(d).style.display = "none";
							}
						}
					</script>

					<?php

					if (isset($getform['filterState']) && ($getform['filterState'] != "All")) {
						if ($getform['filterState'] != "") {
							if ($getform['filterState'] != "all") {
							}
						}
					}
					?>



					<script>
						HideContent('pleasewaitforcitydata')
					</script>

					<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:
					<select name="filterRep" style="width:154px;margin-right:0px">
						<?php
						$selectUserNameSql = 'SELECT name FROM users';
						$selectUserNameQuery = mysqli_query($conn, $selectUserNameSql) or die(mysqli_error($conn));
						$reps['please choose'] = 'All';
						while ($rep = mysqli_fetch_array($selectUserNameQuery, MYSQLI_NUM)) {
							$reps[$rep['0']] = $rep['0'];
						}
						if (isset($reps) && is_array($reps)) {
							foreach ($reps as $rep) {
								$sstr = '';
								if (isset($getform['filterRep']) && ($getform['filterRep'] == "$rep")) {
									$sstr = 'selected';
								}
						?>
								<option value='<?php echo $rep; ?>' <?php echo ' ' . $sstr; ?>><?php echo $rep; ?></option>
						<?php
								$sstr = '';
							}
						}
						$reps = array();
						?>
					</select>
					<br />

					<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Type:
					<select name="filterType" style="width:154px;margin-right:0px">
						<?php
						$selectOrderTypeSql = 'SELECT type FROM orders';
						$selectOrderTypeQuery = mysqli_query($conn, $selectOrderTypeSql) or die(mysqli_error($conn));
						$reps['please choose'] = 'All';
						while ($rep = mysqli_fetch_array($selectOrderTypeQuery, MYSQLI_NUM)) {
							$reps[$rep['0']] = $rep['0'];
						}
						if (isset($reps) && is_array($reps)) {
							foreach ($reps as $rep) {
								if ($rep != "") {
									$sstr = '';
									if (isset($getform['filterType']) && ($getform['filterType'] == "$rep")) { /*die("ahhh");*/
										$sstr = 'selected';
									}

						?>
									<option value="<?php echo $rep; ?>" <?php echo $sstr; ?>><?php echo $rep; ?></option>
						<?php
									$sstr = '';
								}
							}
						}
						$reps = array();
						?>
					</select>
					<br />
					<?php
					$reps['All'] = 'All';
					$reps['LM-Dealer'] = 'LM-Dealer';
					$reps['Screen Print'] = 'Screen Print';
					$reps['Logo Magnet'] = 'Logo Magnet';
					$reps['Logo Ventures'] = 'Logo Ventures';
					$reps['CMD'] = 'CMD';
					$reps['Promotional'] = 'Promotional';
					$reps['Embroidery'] = 'Embroidery';
					$reps['Signs'] = 'Signs';
					$reps['Other'] = 'Other';
					?>

					<br /> Category:
					<select name="orderType" style="width:154px;margin-right:0px">
						<?php
						if (isset($reps) && is_array($reps)) {
							foreach ($reps as $rep) {
								if ($rep != "") {
									$sstr = '';
									if (isset($getform['orderType']) && ($getform['orderType'] == "$rep")) {
										$sstr = 'selected';
									}

						?>
									<option value="<?php echo $rep; ?>" <?php echo $sstr; ?>><?php echo $rep; ?></option>
						<?php
									$sstr = '';
								}
							}
						}
						?>
					</select>


					<br />


					<br />
					<label>Start Date: <select name="filterMonth" style="width:92px;margin-right:0px">
							<option value="any">Any Month</option>
							<option value="01" <?php if (($getform['filterMonth'] ?? '') == "01") echo "selected"; ?>>January</option>
							<option value="02" <?php if (($getform['filterMonth'] ?? '') == "02") echo "selected"; ?>>February</option>
							<option value="03" <?php if (($getform['filterMonth'] ?? '') == "03") echo "selected"; ?>>March</option>
							<option value="04" <?php if (($getform['filterMonth'] ?? '') == "04") echo "selected"; ?>>April</option>
							<option value="05" <?php if (($getform['filterMonth'] ?? '') == "05") echo "selected"; ?>>May</option>
							<option value="06" <?php if (($getform['filterMonth'] ?? '') == "06") echo "selected"; ?>>June</option>
							<option value="07" <?php if (($getform['filterMonth'] ?? '') == "07") echo "selected"; ?>>July</option>
							<option value="08" <?php if (($getform['filterMonth'] ?? '') == "08") echo "selected"; ?>>August</option>
							<option value="09" <?php if (($getform['filterMonth'] ?? '') == "09") echo "selected"; ?>>September</option>
							<option value="10" <?php if (($getform['filterMonth'] ?? '') == "10") echo "selected"; ?>>October</option>
							<option value="11" <?php if (($getform['filterMonth'] ?? '') == "11") echo "selected"; ?>>November</option>
							<option value="12" <?php if (($getform['filterMonth'] ?? '') == "12") echo "selected"; ?>>December</option>
						</select></label>

					<select name="filterYear" style="width:60px;margin-left:0px">
						<option value="any">Any</option>
						<?php
						$currentYear = date("Y");
						while ($currentYear > 2006) {
							echo "<option value='$currentYear'";
							if (isset($getform['filterYear']) && ($getform['filterYear'] == $currentYear))
								echo " selected";
							echo ">$currentYear</option>\n";
							$currentYear--;
						}
						?>
					</select><br />


					<label>End Date: <select name="filterMonth2" style="width:92px;margin-right:0px">
							<option value="any">Any Month</option>
							<option value="01" <?php if (($getform['filterMonth2'] ?? '') == "01") echo "selected"; ?>>January</option>
							<option value="02" <?php if (($getform['filterMonth2'] ?? '') == "02") echo "selected"; ?>>February</option>
							<option value="03" <?php if (($getform['filterMonth2'] ?? '') == "03") echo "selected"; ?>>March</option>
							<option value="04" <?php if (($getform['filterMonth2'] ?? '') == "04") echo "selected"; ?>>April</option>
							<option value="05" <?php if (($getform['filterMonth2'] ?? '') == "05") echo "selected"; ?>>May</option>
							<option value="06" <?php if (($getform['filterMonth2'] ?? '') == "06") echo "selected"; ?>>June</option>
							<option value="07" <?php if (($getform['filterMonth2'] ?? '') == "07") echo "selected"; ?>>July</option>
							<option value="08" <?php if (($getform['filterMonth2'] ?? '') == "08") echo "selected"; ?>>August</option>
							<option value="09" <?php if (($getform['filterMonth2'] ?? '') == "09") echo "selected"; ?>>September</option>
							<option value="10" <?php if (($getform['filterMonth2'] ?? '') == "10") echo "selected"; ?>>October</option>
							<option value="11" <?php if (($getform['filterMonth2'] ?? '') == "11") echo "selected"; ?>>November</option>
							<option value="12" <?php if (($getform['filterMonth2'] ?? '') == "12") echo "selected"; ?>>December</option>
						</select></label>

					<select name="filterYear2" style="width:60px;margin-left:0px">
						<option value="any">Any</option>
						<?php
						$currentYear = date("Y");
						while ($currentYear > 2006) {
							echo "<option value='$currentYear'";
							if (isset($getform['filterYear2']) && ($getform['filterYear2'] == $currentYear))
								echo " selected";
							echo ">$currentYear</option>\n";
							$currentYear--;
						}
						?>
					</select><br /><br />

					<input type="hidden" id="filterCity" name="filterCity" value="" />

					<input type="submit" value="Download Client Emails" name="searchSubmit" style="width:154px">
			</form>
			<br />
			<input type="button" onclick="window.location='<?php echo BASE_URL; ?>control.php'" value="Reset Filters">

			<script>
				document.getElementById('<?php echo $kodestated ?? ''; ?>').style.display = "block";
				document.getElementById('filterCity').value = '<?php echo $getform['filterCity'] ?? ''; ?>';
			</script>





		<?php
				}

				if (isset($_GET['zmp']) && ($_GET['zmp'] != "")) { ?> <script>
				alert('No Match Found');
			</script> <?php } ?>

		</div>

		<!-- InstanceEndEditable -->
	</body>
	<!-- InstanceEnd -->

	</html>
<?php
}
// ob_end_flush();
?>