<?php
session_start();
include('database.php');
include('config.php');
$showOrderSql = "SHOW COLUMNS FROM orders LIKE 'pmethod'";
$checkColumn = mysqli_query($conn, $showOrderSql);
if (mysqli_num_rows($checkColumn) == 0) {
	$alterOrderSql = "ALTER TABLE orders ADD pmethod VARCHAR(50)";
	mysqli_query($conn, $alterOrderSql) or die(mysqli_error($conn));
}

$showOrderSql = "SHOW COLUMNS FROM orders LIKE 'poNo'";
$checkColumn = mysqli_query($conn, $showOrderSql);
if (mysqli_num_rows($checkColumn) == 0) {
	$alterOrderSql = "ALTER TABLE orders ADD poNo VARCHAR(50)";
	mysqli_query($conn, $alterOrderSql) or die(mysqli_error($conn));
}

$description = [];
$color = [];
$vendor = [];
$yxs = [];
$ys = [];
$ym = [];
$yl = [];
$yxl = [];
$s = [];
$m = [];
$l = [];
$xl = [];
$xxl = [];
$xxxl = [];
$xxxxl = [];
$misc = [];
$price = [];
$artistName = '';
$repID = '';
$artistID = '';
$departmentID = '';
$shipBlind = 0;
$category = '';
$type = '';
$front = '';
$back = '';
$sleeve = '';
$salesTax = 0;
$totalRows = 0;

if (!isset($_SESSION['initials']))
	header('Location: index2.php');

function addSmartQuotes($string)
{
	if (is_null($string) || $string === '') {
		return $string;
	}

	$replace = ['’', '“'];
	$search = ["'", '"'];
	return str_replace($search, $replace, $string);
}

function getPostValue($key, $default = '')
{
	return isset($_POST[$key]) && !empty($_POST[$key]) ? $_POST[$key] : $default;
}

function getArtFilenameForID($ids)
{
	if ($ids < 1000)
		$fn = "000" . $ids . ".jpeg";
	else if ($ids < 10000)
		$fn = "00" . $ids . ".jpeg";
	else if ($ids < 100000)
		$fn = "0" . $ids . ".jpeg";
	else
		$fn = $ids . ".jpeg";

	return $fn;
}

function sqlDate($strDate)
{
	$tempDate = explode('/', $strDate);
	$err = false;

	if (count($tempDate) == 3) {
		$month = $tempDate[0] + 0;
		$daynum = $tempDate[1] + 0;
		$year = $tempDate[2] + 0;
	} else
		$err = true;

	$month   = (($month   <  10) ? '0'  . $month   : $month);
	$daynum  = (($daynum  <  10) ? '0'  . $daynum   : $daynum);

	if (!$err)
		return $year . '-' . $month  . '-' . $daynum;
	else
		return $strDate;
}

if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "orderID=") && strstr($_SERVER['HTTP_REFERER'], "edit=1")) {
	$oldID = strstr($_SERVER['HTTP_REFERER'], "orderID=");
	$oldID = substr($oldID, 8, strpos($oldID, "&") - 8);
	$updateOrderSql = "UPDATE orders SET lockedByName=NULL WHERE id='" . mysqli_real_escape_string($conn, $oldID) . "'";
	mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/OMS.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<script language="javascript" src="oms.js"></script>
	<!-- InstanceBeginEditable name="doctitle" -->
	<script language="javascript" src="CalendarPopup.js"></script>
	<script language="javascript">
		var cal = new CalendarPopup("testdiv1");
		var calx = new CalendarPopup("testdiv2");
		var calxx = new CalendarPopup("testdiv3");
		var calxxx = new CalendarPopup("testdiv4");
		document.write(getCalendarStyles());
	</script>
	<script type="text/javascript">
		function getInfoForClient(clientID) {
			var xmlHttpReq = false;
			var self = this;
			// Mozilla/Safari
			if (window.XMLHttpRequest)
				self.xmlHttpReq = new XMLHttpRequest();
			// IE
			else if (window.ActiveXObject)
				self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			self.xmlHttpReq.open('POST', 'findClient.php?clientID=' + clientID, true);
			self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			self.xmlHttpReq.onreadystatechange = function() {
				if (self.xmlHttpReq.readyState == 4)
					updateOrder(self.xmlHttpReq.responseText);
			}
			self.xmlHttpReq.send(null);
		}

		function updateOrder(str) {
			var parts = str.split("_,__");
			document.forms['mainform'].elements['name'].value = parts[0];
			document.forms['mainform'].elements['contact'].value = parts[1];
			document.forms['mainform'].elements['address'].value = parts[2];
			document.forms['mainform'].elements['city'].value = parts[3];
			document.forms['mainform'].elements['state'].value = parts[4];
			document.forms['mainform'].elements['zip'].value = parts[5];
			document.forms['mainform'].elements['phone'].value = parts[6];
			document.forms['mainform'].elements['email'].value = parts[7];
			document.forms['mainform'].elements['clientID'].value = parts[8];
			alert(parts[8]);
		}

		function checkValidSubmit() {
			var missingFields = new Array();
			var typeEntered = false;
			var categoryEntered = false;

			/* Check for a type entry */
			for (var x = 0; x < document.forms['mainform'].elements['type'].length; x++) {
				if (document.forms['mainform'].elements['type'][x].checked)
					typeEntered = true;
			}

			/* Check for a category entry */
			for (var x = 0; x < document.forms['mainform'].elements['category'].length; x++) {
				if (document.forms['mainform'].elements['category'][x].checked)
					categoryEntered = true;
			}

			if (!typeEntered)
				missingFields.push('Type');
			if (!categoryEntered)
				missingFields.push('Category');
			if (document.forms['mainform'].elements['artDueDate'].value == '')
				missingFields.push('Art Due Date');
			if (document.forms['mainform'].elements['dueDate'].value == '')
				missingFields.push('Due Date');
			missingFieldsString = "<ul>";

			for (var x = 0; x < missingFields.length; x++)
				missingFieldsString += "<li>" + missingFields[x] + "</li>";

			if (missingFieldsString != '<ul>') {
				document.getElementById('submitErrorMessage').innerHTML = "This order is missing:" + missingFieldsString + "</ul>";
				document.getElementById('submitErrorMessage').style.visibility = 'visible';
				return false;
			} else
				return true;
		}

		var categoryIsMagnet = false;
		/* Category is about to change. Auto-initialize 'Order Goods' box if Logo Magnet or PromoSouth was chosen. */
		function categoryChange(isaMagnet) {
			categoryChosen = true;
			if (isaMagnet) {
				categoryIsMagnet = true;
				document.forms['mainform'].elements['stageOrderGoods'].value = "N/A";
				document.forms['mainform'].elements['stageOrderGoods_'].value = "N/A";
				document.forms['mainform'].elements['stageOrderStaging'].value = "N/A";
				document.forms['mainform'].elements['stageOrderStaging_'].value = "N/A";
				document.forms['mainform'].elements['stageGoodsReceived'].value = "N/A";
				document.forms['mainform'].elements['stageGoodsReceived_'].value = "N/A";
			} else if (categoryIsMagnet) {
				categoryIsMagnet = false;
				document.forms['mainform'].elements['stageOrderGoods'].value = " ";
				document.forms['mainform'].elements['stageOrderGoods_'].value = "";
				document.forms['mainform'].elements['stageOrderStaging'].value = " ";
				document.forms['mainform'].elements['stageOrderStaging_'].value = "";
				document.forms['mainform'].elements['stageGoodsReceived'].value = " ";
				document.forms['mainform'].elements['stageGoodsReceived_'].value = "";
			}
		}
	</script>
	<title><?php
			if (isset($_REQUEST['orderID']))
				echo ("View Order: " . $_REQUEST['orderID']);
			else
				echo ("New Order"); ?></title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>

<body>
	<!-- InstanceBeginEditable name="topbody" -->
	<!-- InstanceEndEditable -->
	<?php include('header.php'); ?>
	<!-- InstanceBeginEditable name="body" -->
	<?php
	if (isset($_POST['addOrder'])) {

		$orderID = getPostValue('orderID', null);
		$clientID = getPostValue('clientID', null);
		$name = addSmartQuotes(getPostValue('name'));
		$contact = addSmartQuotes(getPostValue('contact'));
		$address = addSmartQuotes(getPostValue('address'));
		$city = addSmartQuotes(getPostValue('city'));
		$state = getPostValue('state');
		$zip = trim(getPostValue('zip'));
		$phone = addSmartQuotes(getPostValue('phone'));
		$email = addSmartQuotes(getPostValue('email'));
		$repID = getPostValue('repID', 0);
		$artistID = getPostValue('artistID', 0);
		$departmentID = getPostValue('departmentID', 0);
		$type = getPostValue('type', 'Order');
		$category = getPostValue('category', 'Screen Print');
		$projectName = addSmartQuotes(getPostValue('projectName'));
		$shippingClient = addSmartQuotes(getPostValue('shippingClient'));
		$shippingContact = addSmartQuotes(getPostValue('shippingContact'));
		$shippingAddress = addSmartQuotes(getPostValue('shippingAddress'));
		$shippingCity = addSmartQuotes(getPostValue('shippingCity'));
		$shippingState = getPostValue('shippingState');
		$shippingZip = trim(getPostValue('shippingZip'));

		$front = getPostValue('front', 'No Print');
		$back = getPostValue('back', 'No Print');
		$sleeve = getPostValue('sleeve', 'No Print');
		$other = addSmartQuotes(getPostValue('other'));

		$frontDetails = addSmartQuotes(getPostValue('frontDetails'));
		$backDetails = addSmartQuotes(getPostValue('backDetails'));
		$sleeveDetails = addSmartQuotes(getPostValue('sleeveDetails'));
		$otherDetails = addSmartQuotes(getPostValue('otherDetails'));

		$orderDate = getPostValue('orderDate') ? "'" . sqlDate(getPostValue('orderDate')) . "'" : 'NULL';
		$artDueDate = getPostValue('artDueDate') ? "'" . sqlDate(getPostValue('artDueDate')) . "'" : 'NULL';
		$printDate = getPostValue('printDate') ? "'" . sqlDate(getPostValue('printDate')) . "'" : 'NULL';
		$dueDate = getPostValue('dueDate') ? "'" . sqlDate(getPostValue('dueDate')) . "'" : 'NULL';

		$specialInstructions = addSmartQuotes(getPostValue('specialInstructions'));
		$productionNotes = addSmartQuotes(getPostValue('productionNotes'));
		$artInstructions = addSmartQuotes(getPostValue('artInstructions'));
		$comments = addSmartQuotes(getPostValue('comments'));
		$redAlert = addSmartQuotes(getPostValue('redAlert'));
		$artType = getPostValue('artType', 'New');
		$artFilename = getPostValue('artFilename');

		$salesTax = getPostValue('salesTax', 0.00);
		$screenNumber = getPostValue('screenNumber', 0);
		$screenCharge = getPostValue('screenCharge', 0.00);

		$dieCharge = getPostValue('dieCharge', 0.00);
		$artCharge = getPostValue('artCharge', 0.00);
		$colorCharge = getPostValue('colorCharge', 0.00);
		$shippingCharge = getPostValue('shippingCharge', 0.00);
		$miscCharge = getPostValue('miscCharge', 0.00);
		$deposit = getPostValue('deposit', 0.00);

		$miscDescription = addSmartQuotes(getPostValue('miscDescription'));
		$pmethod = getPostValue('pmethod');
		$poNo = getPostValue('poNo');

		$artistInProgress = $_POST['artistInProgress_'];
		$artistComplete = $_POST['artistComplete_'];
		$artistSentToApprove = $_POST['artistSentToApprove_'];
		$artistRevisions = $_POST['artistRevisions_'];
		$artistApproved = $_POST['artistApproved_'];
		$artistSepsDone = $_POST['artistSepsDone_'];
		$artistProofFilm = $_POST['artistProofFilm_'];
		$artistHours = $_POST['artistHours'];
		$stageOrderGoods = $_POST['stageOrderGoods_'];
		$stageGoodsReceived = $_POST['stageGoodsReceived_'];
		$stageOrderStaging = $_POST['stageOrderStaging_'];
		$stagePrintingStitching = $_POST['stagePrintingStitching_'];
		$stageComplete = $_POST['stageComplete_'];
		$stageBilled = $_POST['stageBilled_'];
		$stagePaid = $_POST['stagePaid_'];
		$artNumber = isset($_POST['artNumber']) && !empty($_POST['artNumber']) ? $_POST['artNumber'] : 0;

		if (isset($_POST['shipBlind']) && $_POST['shipBlind'] == 'on')
			$shipBlind = 1;
		else
			$shipBlind = 0;

		if (isset($_POST['saveClient']) && $_POST['saveClient'] == 'on')
			$saveClient = 1;
		else
			$saveClient = 0;

		/* If client doesn't already exist, create one */
		if ($clientID == -1) {
			$insertClientSql = "INSERT INTO clients (name, contact, address, city, state, zip, phone, email, visible) VALUES('" . $name . "','" . $contact . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "','" . $phone . "','" . $email . "','" . $saveClient . "' ) ";
			mysqli_query($conn, $insertClientSql) or die(mysqli_error($conn));
			$clientID = mysqli_insert_id($conn);
		} else {
			$selectClientSql = "SELECT name, contact, address, phone, email, city, state, zip FROM clients WHERE id =" . $clientID;
			$selectClientQuery = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
			$result = mysqli_fetch_array($selectClientQuery);
			if ($result['name'] != $name || $result['contact'] != $contact || $result['address'] != $address || $result['phone'] != $phone || $result['email'] != $email || $result['city'] != $city || $result['state'] != $state || $result['zip'] != $zip) {
				echo "Order successfully updated. Client information has changed.";
				$insertClientSql = "INSERT INTO clients (name, contact, address, city, state, zip, phone, email, visible) VALUES('" . $name . "','" . $contact . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "','" . $phone . "','" . $email . "','" . $saveClient . "' ) ";
				mysqli_query($conn, $insertClientSql) or die(mysqli_error($conn));
				$clientID = mysqli_insert_id($conn);
			}
		}

		/* Add a new order */
		if ($_POST['addOrder'] == "Add Order" || isset($_POST['saveAsNewOrder'])) {
			$insertOrderSql = "INSERT INTO orders (pmethod, poNo, clientID, repID, artistID, departmentID, projectName, type, category, shippingClient, shippingContact, shippingAddress, shippingCity, shippingState, shippingZip, front, back, sleeve, other, frontDetails, backDetails, sleeveDetails, otherDetails, orderDate, artDueDate, printDate, dueDate, specialInstructions, productionNotes, artInstructions, comments, redAlert, artType, artFilename, artistInProgress, artistComplete, artistSentToApprove, artistRevisions, artistApproved, artistSepsDone, artistProofFilm, artistHours, stageOrderGoods, stageGoodsReceived, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid, salesTax, screenCharge, dieCharge, artCharge, artNumber, screenNumber, colorCharge, shippingCharge, miscCharge, miscDescription, deposit, shipBlind) VALUES('" . $pmethod . "', '" . $poNo . "', " . $clientID . ", " . $repID . ", " . $artistID . ", " . $departmentID . ", '" . $projectName . "', '" . $type . "', '" . $category . "', '" . $shippingClient . "', '" . $shippingContact . "', '" . $shippingAddress . "', '" . $shippingCity . "', '" . $shippingState . "', '" . $shippingZip . "', '" . $front . "', '" . $back . "', '" . $sleeve . "', '" . $other . "', '" . $frontDetails . "', '" . $backDetails . "', '" . $sleeveDetails . "', '" . $otherDetails . "', " . ($orderDate !== 'NULL' ? $orderDate : 'NULL') . ", " . ($artDueDate !== 'NULL' ? $artDueDate : 'NULL') . ", " . ($printDate !== 'NULL' ? $printDate : 'NULL') . ", " . ($dueDate !== 'NULL' ? $dueDate : 'NULL') . ", '" . $specialInstructions . "', '" . $productionNotes . "', '" . $artInstructions . "', '" . $comments . "', '" . $redAlert . "', '" . $artType . "', '" . $artFilename . "', '" . $artistInProgress . "', '" . $artistComplete . "', '" . $artistSentToApprove . "', '" . $artistRevisions . "', '" . $artistApproved . "', '" . $artistSepsDone . "', '" . $artistProofFilm . "', '" . $artistHours . "', '" . $stageOrderGoods . "', '" . $stageGoodsReceived . "', '" . $stageOrderStaging . "', '" . $stagePrintingStitching . "', '" . $stageComplete . "', '" . $stageBilled . "', '" . $stagePaid . "', " . $salesTax . ", " . $screenCharge . ", " . $dieCharge . ", " . $artCharge . ", " . $artNumber . ", " . $screenNumber . ", " . $colorCharge . ", " . $shippingCharge . ", " . $miscCharge . ", '" . $miscDescription . "', " . $deposit . ", " . $shipBlind . ")";
			mysqli_query($conn, $insertOrderSql) or die(mysqli_error($conn));

			$orderID = mysqli_insert_id($conn);
			/* If art file was specified, update the order with the art filename */
			if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
				$artFilename = getArtFilenameForID(mysqli_insert_id($conn));
				if ($_FILES["file"]["size"] > 0) {
					$updateOrderSql = "UPDATE orders SET artFilename = '" . $artFilename . "' WHERE id=" . mysqli_insert_id($conn);
					mysqli_query($conn, $updateOrderSql);
				}
			}
			echo '<div style="color:darkgreen;text-align:center;margin:auto">Order has been successfully added.</div>';
		} else {
			if ($_FILES["file"]["size"] > 0 && isset($_REQUEST['orderID']))
				$artFilename = getArtFilenameForID($_POST['orderID']);

			$updateOrderSql = "UPDATE orders SET clientID='" . $clientID . "', repID='" . $repID . "', artistID='" . $artistID . "', departmentID='" . $departmentID . "', type='" . $type . "', category='" . $category . "', projectName='" . $projectName . "', shipBlind='" . $shipBlind . "', shippingContact='" . $shippingContact . "', shippingClient='" . $shippingClient . "', shippingAddress='" . $shippingAddress . "', shippingCity='" . $shippingCity . "', shippingState='" . $shippingState . "', shippingZip='" . $shippingZip . "', front='" . $front . "', back='" . $back . "', sleeve='" . $sleeve . "', other='" . $other . "', frontDetails='" . $frontDetails . "', backDetails='" . $backDetails . "', sleeveDetails='" . $sleeveDetails . "', otherDetails='" . $otherDetails . "', orderDate=" . $orderDate . ", artDueDate=" . $artDueDate . ", printDate=" . $printDate . ", dueDate=" . $dueDate . ", specialInstructions='" . $specialInstructions . "', productionNotes='" . $productionNotes . "', artInstructions='" . $artInstructions . "', comments='" . $comments . "', redAlert='" . $redAlert . "', artType='" . $artType . "', artFilename='" . $artFilename . "', artistInProgress='" . $artistInProgress . "', artistComplete='" . $artistComplete . "', artistSentToApprove='" . $artistSentToApprove . "', artistRevisions='" . $artistRevisions . "', artistApproved='" . $artistApproved . "', artistSepsDone='" . $artistSepsDone . "', artistProofFilm='" . $artistProofFilm . "', artistHours='" . $artistHours . "', stageOrderGoods='" . $stageOrderGoods . "', stageGoodsReceived='" . $stageGoodsReceived . "', stageOrderStaging='" . $stageOrderStaging . "', stagePrintingStitching='" . $stagePrintingStitching . "', stageComplete='" . $stageComplete . "', stageBilled='" . $stageBilled . "', stagePaid='" . $stagePaid . "', salesTax='" . $salesTax . "', screenNumber='" . $screenNumber . "', screenCharge='" . $screenCharge . "', dieCharge='" . $dieCharge . "', artNumber='" . $artNumber . "', artCharge='" . $artCharge . "', colorCharge='" . $colorCharge . "', shippingCharge='" . $shippingCharge . "', miscCharge='" . $miscCharge . "', miscDescription='" . $miscDescription . "', deposit='" . $deposit . "', pmethod='" . $pmethod . "', poNo='" . $poNo . "', lockedByName = NULL WHERE id=" . $_POST['orderID'];
			mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
			echo '<div style="color:darkgreen;text-align:center;margin:auto">Order has been successfully edited.</div>';
		}

		/* Upload the file if one was specified and move it to the proper location */
		if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
			if ($_FILES["file"]["size"] > 0) {
				if (($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg")) {
					if ($_FILES["file"]["error"] > 0)
						echo "Error uploading file: " . $_FILES["file"]["error"] . "<br />";
					else {
						echo "Upload: " . $_FILES["file"]["name"] . "<br />";
						echo "Type: " . $_FILES["file"]["type"] . "<br />";
						echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
						echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
						move_uploaded_file($_FILES["file"]["tmp_name"], "files/" . $artFilename);
						echo "Stored in: " . "files/" . $artFilename;
					}
				} else
					echo "Valid formats for artwork are .gif and .jpeg. Artwork was not saved. <br />";
			}
		}

		/* Add any order items */
		$updateOrderItemSql = "UPDATE orderItems SET visible = 0 WHERE orderID = " . $orderID;
		mysqli_query($conn, $updateOrderItemSql) or die(mysqli_error($conn));
		for ($i = 1; $i < 50; $i++) {
			// Prepare an array with the required data
			$fields = [
				'quantity' => $_POST[$i . 'quantity'] ?? NULL,
				'description' => addSmartQuotes($_POST[$i . 'description'] ?? ''),
				'color' => addSmartQuotes($_POST[$i . 'color'] ?? ''),
				'vendor' => addSmartQuotes($_POST[$i . 'vendor'] ?? ''),
				'yxs' => $_POST[$i . 'yxs'] ?? NULL,
				'ys' => $_POST[$i . 'ys'] ?? NULL,
				'ym' => $_POST[$i . 'ym'] ?? NULL,
				'yl' => $_POST[$i . 'yl'] ?? NULL,
				'yxl' => $_POST[$i . 'yxl'] ?? NULL,
				's' => $_POST[$i . 's'] ?? NULL,
				'm' => $_POST[$i . 'm'] ?? NULL,
				'l' => $_POST[$i . 'l'] ?? NULL,
				'xl' => $_POST[$i . 'xl'] ?? NULL,
				'xxl' => $_POST[$i . 'xxl'] ?? NULL,
				'xxxl' => $_POST[$i . 'xxxl'] ?? NULL,
				'xxxxl' => $_POST[$i . 'xxxxl'] ?? NULL,
				'misc' => $_POST[$i . 'misc'] ?? NULL,
				'price' => $_POST[$i . 'price'] ?? NULL
			];

			// Check if any non-empty values are in the array, except quantity
			$nonEmptyValues = array_filter($fields, function ($value) {
				return !empty($value) && $value !== 0;
			});

			// If there are valid fields to insert
			if (!empty($nonEmptyValues)) {
				// Prepare the SQL statement with placeholders
				$stmt = $conn->prepare("
					INSERT INTO orderItems (orderID, quantity, description, color, vendor, yxs, ys, ym, yl, yxl, s, m, l, xl, xxl, xxxl, xxxxl, misc, price) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
				");

				// Bind the parameters dynamically
				$stmt->bind_param(
					"iisssiiiiiiiiiiiiis",
					$orderID,
					$fields['quantity'],
					$fields['description'],
					$fields['color'],
					$fields['vendor'],
					$fields['yxs'],
					$fields['ys'],
					$fields['ym'],
					$fields['yl'],
					$fields['yxl'],
					$fields['s'],
					$fields['m'],
					$fields['l'],
					$fields['xl'],
					$fields['xxl'],
					$fields['xxxl'],
					$fields['xxxxl'],
					$fields['misc'],
					$fields['price']
				);

				// Execute the prepared statement
				$stmt->execute();
			}
		}
	} else {
		if (!isset($_REQUEST['edit'])) { ?>
			<div style="border:#666666 groove; background-color:#000000; color:#000000; width:170px; height:80px; right:0px; top:0px; position:fixed; text-align:center; z-index:50000;">
				<h3 style="color:#CCCCCC">View Mode</h3>
				<span style="color:#CCCCCC; margin:3px">Any changes you make will <b>not</b> be saved.</span>
			</div>
		<?php
		}

		/* If an orderID was sent in, pull all info on that order and prep it in variables */
		if (isset($_REQUEST['orderID'])) {
			$orderID = $_REQUEST['orderID'];
			$selectOrderSql = "SELECT * FROM orders WHERE id='" . $_REQUEST['orderID'] . "'";
			$selectOrderQuery = mysqli_query($conn, $selectOrderSql) or die(mysqli_error($conn));
			if (mysqli_num_rows($selectOrderQuery) < 1)
				die("Error: Order ID (#" . $_REQUEST['orderID'] . ") was not found");

			$entry = mysqli_fetch_array($selectOrderQuery);
			if (isset($_REQUEST['edit'])) {
				if (!empty($entry['lockedByName']) && strlen($entry['lockedByName']) > 0) {
					die("<span style='color:#FF0000'>Access denied. " . $entry['lockedByName'] . " is currently editing this order.</span>");
				} else {
					$updateOrderSql = "UPDATE orders SET lockedByName='" . $_SESSION['username'] . "' WHERE id='" . $_REQUEST['orderID'] . "'";
					mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
				}
			}

			$clientID = $entry['clientID'];
			$repID = $entry['repID'];
			$artistID = $entry['artistID'];
			$departmentID = $entry['departmentID'];
			$type = $entry['type'];
			$category = $entry['category'];
			$projectName = addSmartQuotes($entry['projectName']);
			$shipBlind = $entry['shipBlind'];
			$shippingClient = addSmartQuotes($entry['shippingClient']);
			$shippingContact = addSmartQuotes($entry['shippingContact']);
			$shippingAddress = addSmartQuotes($entry['shippingAddress']);
			$shippingCity = addSmartQuotes($entry['shippingCity']);
			$shippingState = $entry['shippingState'];
			$shippingZip = isset($entry['shippingZip']) ? trim($entry['shippingZip']) : '';
			$front = $entry['front'];
			$back = $entry['back'];
			$sleeve = $entry['sleeve'];
			$other = addSmartQuotes($entry['other']);
			$frontDetails = addSmartQuotes($entry['frontDetails']);
			$backDetails = addSmartQuotes($entry['backDetails']);
			$sleeveDetails = addSmartQuotes($entry['sleeveDetails']);
			$otherDetails = addSmartQuotes($entry['otherDetails']);
			if ($entry['orderDate'] == '0000-00-00' || $entry['orderDate'] == '')
				$orderDate = '';
			else
				$orderDate = date("m/d/Y", strtotime($entry['orderDate']));
			if ($entry['artDueDate'] == '0000-00-00' || $entry['artDueDate'] == '')
				$artDueDate = '';
			else
				$artDueDate = date("m/d/Y", strtotime($entry['artDueDate']));
			if ($entry['printDate'] == '0000-00-00' || $entry['printDate'] == '')
				$printDate = '';
			else
				$printDate = date("m/d/Y", strtotime($entry['printDate']));
			if ($entry['dueDate'] == '0000-00-00' || $entry['dueDate'] == '')
				$dueDate = '';
			else
				$dueDate = date("m/d/Y", strtotime($entry['dueDate']));
			$specialInstructions = addSmartQuotes($entry['specialInstructions']);
			$productionNotes = addSmartQuotes($entry['productionNotes']);
			$artInstructions = addSmartQuotes($entry['artInstructions']);
			$comments = addSmartQuotes($entry['comments']);
			$redAlert = addSmartQuotes($entry['redAlert']);
			$artType = $entry['artType'];
			$artFilename = $entry['artFilename'];
			$artistInProgress = $entry['artistInProgress'];
			$artistComplete = $entry['artistComplete'];
			$artistSentToApprove = $entry['artistSentToApprove'];
			$artistRevisions = $entry['artistRevisions'];
			$artistApproved = $entry['artistApproved'];
			$artistSepsDone = $entry['artistSepsDone'];
			$artistProofFilm = $entry['artistProofFilm'];
			$artistHours = $entry['artistHours'];
			$stageOrderGoods = $entry['stageOrderGoods'];
			$stageGoodsReceived = $entry['stageGoodsReceived'];
			$stageOrderStaging = $entry['stageOrderStaging'];
			$stagePrintingStitching = $entry['stagePrintingStitching'];
			$stageComplete = $entry['stageComplete'];
			$stageBilled = $entry['stageBilled'];
			$stagePaid = $entry['stagePaid'];
			$salesTax = $entry['salesTax'];
			$screenNumber = $entry['screenNumber'];
			$screenCharge = $entry['screenCharge'];
			$dieCharge = $entry['dieCharge'];
			$artNumber = $entry['artNumber'];
			$artCharge = $entry['artCharge'];
			$colorCharge = $entry['colorCharge'];
			$shippingCharge = $entry['shippingCharge'];
			$miscCharge = $entry['miscCharge'];
			$miscDescription = addSmartQuotes($entry['miscDescription']);
			$deposit = $entry['deposit'];
			$pmethod = $entry['pmethod'];
			$poNo = $entry['poNo'];

			/* Grab the client info using the client ID */
			$selectClientSql = "SELECT * FROM clients WHERE id='" . $clientID . "'";
			$selectClientQuery = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
			$entry = mysqli_fetch_array($selectClientQuery);
			$name = addSmartQuotes($entry['name']);
			$contact = addSmartQuotes($entry['contact']);
			$address = addSmartQuotes($entry['address']);
			$city = addSmartQuotes($entry['city']);
			$state = $entry['state'];
			$zip = isset($entry['zip']) ? trim($entry['zip']) : '';
			$phone = addSmartQuotes($entry['phone']);
			$email = addSmartQuotes($entry['email']);

			/* Get the artist's name from the artist ID */
			$selectUserSql = "SELECT name FROM users WHERE id='" . $artistID . "'";
			$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
			$entry = mysqli_fetch_array($selectUserQuery);
			$artistName = $entry[0];

			/* Get the rep's name from the rep ID */
			$selectUserSql = "SELECT name FROM users WHERE id='" . $repID . "'";
			$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
			$entry = mysqli_fetch_array($selectUserQuery);
			$repName = $entry[0];

			/* Get the department's name from the department ID */
			$selectUserSql = "SELECT name FROM users WHERE id='" . $departmentID . "'";
			$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
			$entry = mysqli_fetch_array($selectUserQuery);
			$departmentName = $entry[0];

			/* Get any order items associated with this order */
			$j = 1;
			$selectOrderItemSql = "SELECT * FROM orderItems WHERE orderID='" . $orderID . "' AND visible=1 ORDER BY id ASC";
			$selectOrderItemQuery = mysqli_query($conn, $selectOrderItemSql) or die(mysqli_error($conn));
			$totalRows = mysqli_num_rows($selectOrderItemQuery);

			while ($entry = mysqli_fetch_array($selectOrderItemQuery)) {
				$quantity[$j] = addSmartQuotes($entry['quantity']);
				$description[$j] = addSmartQuotes($entry['description']);
				$color[$j] = addSmartQuotes($entry['color']);
				$vendor[$j] = addSmartQuotes($entry['vendor']);
				$yxs[$j] = $entry['yxs'];
				$ys[$j] = $entry['ys'];
				$ym[$j] = $entry['ym'];
				$yl[$j] = $entry['yl'];
				$yxl[$j] = $entry['yxl'];
				$s[$j] = $entry['s'];
				$m[$j] = $entry['m'];
				$l[$j] = $entry['l'];
				$xl[$j] = $entry['xl'];
				$xxl[$j] = $entry['xxl'];
				$xxxl[$j] = $entry['xxxl'];
				$xxxxl[$j] = $entry['xxxxl'];
				$misc[$j] = $entry['misc'];
				$price[$j] = $entry['price'];
				$j++;
			}
		} else {
			/* Set default rep and department */
			$departmentName = "SalesDept";
			$repName = "Maria";
			$clientID = -1;

			/* Get client list */
			$selectClientSql = "SELECT id, name, contact FROM clients WHERE visible=1 ORDER BY clients.name ASC";
			$selectClientQuery = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
			$clientList = "";
			while ($entry = mysqli_fetch_array($selectClientQuery))
				$clientList .= "<option value=" . $entry['id'] . ">" . $entry['name'] . " - " . $entry['contact'] . "</option>";
		}

		/* Get Rep name list */
		$selectUserSql = "SELECT id, name FROM users WHERE (type LIKE '%Rep%') OR (users.id = '" . $repID . "' AND type NOT LIKE '%Inactive%') ORDER BY users.name ASC";
		$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
		$repList = "";
		while ($entry = mysqli_fetch_array($selectUserQuery)) {
			if ($entry['name'] == $repName)
				$repList .= "<option value=" . $entry['id'] . " selected='selected'>" . $entry['name'] . "</option>";
			else
				$repList .= "<option value=" . $entry['id'] . ">" . $entry['name'] . "</option>";
		}

		/* Get Artist name list */
		$selectUserSql = "SELECT id,name FROM users WHERE (type LIKE '%Artist%') OR (users.id = '" . $artistID . "' AND type NOT LIKE '%Inactive%') ORDER BY users.name ASC";
		$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
		$artistList = "";
		while ($entry = mysqli_fetch_array($selectUserQuery)) {
			if ($entry['name'] == $artistName)
				$artistList .= "<option value=" . $entry['id'] . " selected='selected'>" . $entry['name'] . "</option>";
			else
				$artistList .= "<option value=" . $entry['id'] . ">" . $entry['name'] . "</option>";
		}

		/* Get department list */
		$selectUserSql = "SELECT id,name FROM users WHERE type NOT LIKE '%Inactive%' AND type != 'Admin' OR users.id = '" . $departmentID . "' ORDER BY users.name ASC";
		$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
		$departmentList = "";
		while ($entry = mysqli_fetch_array($selectUserQuery)) {
			if ($entry['name'] == $departmentName)
				$departmentList .= "<option value=" . $entry['id'] . " selected='selected'>" . $entry['name'] . "</option>";
			else
				$departmentList .= "<option value=" . $entry['id'] . ">" . $entry['name'] . "</option>";
		}
		?>
		<form id="mainform" name="mainform" method="post" enctype="multipart/form-data" onsubmit="return checkValidSubmit();">

			<fieldset style="width:320px; position:absolute; top:105px">
				<legend>Bill to</legend>
				<?php if (isset($orderID)) { ?><span style='float:left; font-size:.8em'>Order Number: <?php echo $orderID; ?></span><?php } ?>
				<label>Save Info <input name='saveClient' type="checkbox" /></label><br />
				<?php if (isset($clientList)) { ?><select name='changeclient' onchange="getInfoForClient(this.options[this.selectedIndex].value)" style="width: 232px;"><?php echo $clientList; ?></select><?php } ?><br />
				<label>Client: <input class="text" name='name' value="<?php echo isset($name) ? $name : ''; ?>" style="width: 230px;" /></label><br />
				<label>Contact: <input class="text" name='contact' value="<?php echo isset($contact) ? $contact : ''; ?>" style="width: 230px;" /></label><br />
				<label>Project Name: <input class="text" name="projectName" value="<?php echo isset($projectName) ? $projectName : ''; ?>" style="width: 230px;" /></label><br />
				<label>Address: <input class="text" name="address" value="<?php echo isset($address) ? $address : ''; ?>" style="width: 230px;" /></label><br />
				<label>City, State, Zip: <input class="text" name="city" value="<?php echo isset($city) ? $city : ''; ?>" style="width: 136px;" /></label>
				<input class="text" name="state" value="<?php echo isset($state) ? $state : ''; ?>" style="width: 30px; margin:2px 0px;" />
				<input class="text" name="zip" value="<?php echo isset($zip) ? $zip : ''; ?>" style="width: 50px;" /><br />
				<label>Phone or Fax: <input class="text" name="phone" value="<?php echo isset($phone) ? $phone : ''; ?>" style="width: 230px;" /></label><br />
				<label>E-mail: <input class="text" name="email" value="<?php echo isset($email) ? $email : ''; ?>" style="width: 230px;" /></label><br />
			</fieldset>

			<fieldset style="width:320px; position:absolute; top:105px; left:354px">
				<legend>Ship to</legend>
				<label>Ship Blind <input name="shipBlind" id="shipBlind" type="checkbox" <?php if ($shipBlind) echo "checked=1"; ?> /></label><br />
				<label>Same as Bill to <input name="samebilling" type="checkbox" onclick="sameshipdisplay();" /></label><br />
				<label>Client: <input class='text' name='shippingClient' value='<?php echo isset($shippingClient) ? $shippingClient : ''; ?>' style="width: 230px;" /></label><br />
				<label>Contact: <input class="text" name="shippingContact" value="<?php echo isset($shippingContact) ? $shippingContact : ''; ?>" style="width: 230px;" /></label><br />
				<label>Address: <input class="text" name="shippingAddress" value="<?php echo $shippingAddress ?? ''; ?>" style="width: 230px;" /></label><br />
				<label>City, State, Zip: <input class="text" name="shippingCity" value="<?php echo $shippingCity ?? ''; ?>" style="width: 136px;" /></label>
				<input class="text" name="shippingState" value="<?php echo $shippingState ?? ''; ?>" style="width: 30px; margin:2px 0px;" />
				<input class="text" name="shippingZip" value="<?php echo $shippingZip ?? ''; ?>" style="width: 50px;" /><br />
			</fieldset>

			<div style="width:335px; position:absolute; top:268px; left:358px;">
				<span style="color:#FF0000; font-weight:bold">Red Alert</span>
				<textarea name="redAlert" style="width:335px; height:47px;"><?php echo $redAlert ?? ''; ?></textarea>
			</div>

			<fieldset style="width:105px; position:absolute; top:105px; left:700px">
				<legend>Type</legend>
				<label>Order <input name="type" value="Order" type="radio" onclick='typeChosen = true;' <?php if ($type == "Order") echo "checked='yes'"; ?> /></label><br />
				<label>Inquiry/Quote <input name="type" value="Inquiry/Quote" type="radio" onclick='typeChosen = true;' <?php if ($type == "Inquiry/Quote") echo "checked='yes'"; ?> /></label><br />
				<label>Mock-up <input name="type" value="Mock-up" type="radio" onclick='typeChosen = true;' <?php if ($type == "Mock-up") echo "checked='yes'"; ?> /></label><br />
				<label>Ready to Print <input name="type" value="Ready to Print" type="radio" onclick='typeChosen = true;' <?php if ($type == "Ready to Print") echo "checked='yes'"; ?> /></label><br />
				<label>Project <input name="type" value="Project" type="radio" onclick='typeChosen = true;' <?php if ($type == "Project") echo "checked='yes'"; ?> /></label><br />
				<label>Inactive <input name="type" value="Inactive" type="radio" onclick='typeChosen = true;' <?php if ($type == "Inactive") echo "checked='yes'"; ?> /></label><br />
				<label>Other <input name="type" value="Other" type="radio" onclick='typeChosen = true;' <?php if ($type == "Other") echo "checked='yes'"; ?> /></label><br />
			</fieldset>
			<?php
			$str = 'MHM,HP-FB500,SST,Cameo,Outsource,Other';
			$ary = explode(',', $str);
			?>
			<fieldset style="width:105px; position:absolute; top:270px; left:700px">
				<legend>Prod. Method</legend>
				<select name="pmethod">
					<option value="<?php echo $pmethod ?? ''; ?>"><?php echo $pmethod ?? ''; ?></option>
					<?php
					foreach ($ary as $drop) {
						echo "<option value=$drop>$drop</option>";
					}
					?>
				</select>
			</fieldset>
			<fieldset style="width:200px; position:absolute; top:325px; left:700px">
				<legend>Purchase Number</legend>
				<textarea name="poNo" rows="1" cols="22"><?php echo isset($poNo) ? htmlspecialchars($poNo) : ''; ?></textarea>
			</fieldset>




			<fieldset style="width:105px; position:absolute; top:105px; left:831px">
				<legend>Category</legend>
				<label>Screen Print <input name="category" value="Screen Print" type="radio" onclick='categoryChange(false)' <?php if ($category == "Screen Print") echo "checked='yes'"; ?> /></label><br />
				<label>Logo Magnet <input name="category" value="Logo Magnet" type="radio" onclick='categoryChange(true)' <?php if ($category == "Logo Magnet") echo "checked='yes'"; ?> /></label><br />
				<label>LM-Dealer <input name="category" value="LM-Dealer" type="radio" onclick='categoryChange(false)' <?php if ($category == "LM-Dealer") echo "checked='yes'"; ?> /></label><br />
				<label>C-S-Shirts <input name="category" value="Logo Ventures" type="radio" onclick='categoryChange(false)' <?php if ($category == "Logo Ventures") echo "checked='yes'"; ?> /></label><br />
				<label>PromoSouth <input name="category" value="CMD" type="radio" onclick="categoryChange(true); document.forms['mainform'].elements['shipBlind'].checked = 'yes';" <?php if ($category == "CMD") echo "checked='yes'"; ?> /></label><br />
				<label>Promotional <input name="category" value="Promotional" type="radio" onclick='categoryChange(false)' <?php if ($category == "Promotional") echo "checked='yes'"; ?> /></label><br />
				<label>Embroidery <input name="category" value="Embroidery" type="radio" onclick='categoryChange(false)' <?php if ($category == "Embroidery") echo "checked='yes'"; ?> /></label><br />
				<label>Signs <input name="category" value="Signs" type="radio" onclick='categoryChange(false)' <?php if ($category == "Signs") echo "checked='yes'"; ?> /></label><br />
				<label>Other <input name="category" value="Other" type="radio" onclick='categoryChange(false)' <?php if ($category == "Other") echo "checked='yes'"; ?> /></label><br />
			</fieldset>

			<div style="position:absolute; top:25px;">
				<div style="width:1000px; position:absolute; top:360px;">
					<label>Rep: <select name="repID" style="width:80px"><?php echo $repList ?? ''; ?></select></label>
					&nbsp;&nbsp;&nbsp;
					<label>Order date: <input class="text" name="orderDate" value="<?php if (isset($_REQUEST['orderID'])) echo $orderDate;
																					else echo date('m/d/Y'); ?>" onchange="ValidateDate('orderDate');" onkeyup="DateChange('orderDate');" size="10" /></label>
					<img src='calendar.png' border=0 onclick="cal.select(document.forms['mainform'].elements['orderDate'],'anchor1','MM/dd/yyyy'); return false;" name="anchor1" id="anchor1" />
					&nbsp;&nbsp;&nbsp;
					<label>Art due: <input class="text" name="artDueDate" value="<?php echo $artDueDate ?? ''; ?>" onchange="ValidateDate('artDueDate');" onkeyup="DateChange('artDueDate');" size="10" /></label>
					<img src='calendar.png' border=0 onclick="calx.select(document.forms['mainform'].elements['artDueDate'],'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2" />
					&nbsp;&nbsp;&nbsp;
					<label>Scheduled to print: <input class="text" size="10" name="printDate" value="<?php echo $printDate ?? ''; ?>" onchange="ValidateDate('printDate');" onkeyup="DateChange('printDate');" /></label>
					<img src='calendar.png' border=0 onclick="calxx.select(document.forms['mainform'].printDate,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3" />
					&nbsp;&nbsp;&nbsp;
					<label>Due date: <input class="text" size="10" name="dueDate" value="<?php echo $dueDate ?? ''; ?>" onchange="ValidateDate('dueDate');" onkeyup="DateChange('dueDate');" /></label>
					<img src='calendar.png' border=0 onclick="calxxx.select(document.forms['mainform'].dueDate,'anchor4','MM/dd/yyyy'); return false;" NAME="anchor4" ID="anchor4" />
				</div>

				<div style="width:1000px; position:absolute; top:0px; z-index:100;">
					<div id="testdiv1" name="testdiv1" STYLE="position:absolute; background-color: #ffffff;"></div>
					<div id="testdiv2" name="testdiv2" STYLE="position:absolute; background-color: #ffffff;"></div>
					<div id="testdiv3" name="testdiv3" STYLE="position:absolute; background-color: #ffffff;"></div>
					<div id="testdiv4" name="testdiv4" STYLE="position:absolute; background-color: #ffffff;"></div>
				</div>

				<div style="width:465px; position:absolute; top:410px;">
					<label>Special Instructions:<textarea name='specialInstructions' style="width:465px; height:70px;"><?php echo $specialInstructions ?? ''; ?></textarea></label>
				</div>

				<div style="width:465px; position:absolute; top:410px; left:490px">
					<label>Production Notes:<textarea name='productionNotes' style="width:465px; height:70px;"><?php echo $productionNotes ?? ''; ?></textarea></label>
				</div>


				<table border=0 cellpadding=0 cellspacing=0 style="position:absolute; top:510px; z-index:50;" id="dynamicTable">
					<tr>
						<td align="center">
							<font style="width: 40px;"></font>
						</td>
						<td align="center">
							<font style="width: 55px;">Quantity</font>
						</td>
						<td align="center">
							<font style="width: 150px;">Style/Description</font>
						</td>
						<td align="center">
							<font style="width: 55px;">Color</font>
						</td>
						<td align="center">
							<font style="width: 55px;">Vendor</font>
						</td>
						<td align="center">
							<font style="width: 30px;">YXS</font>
						</td>
						<td align="center">
							<font style="width: 30px;">YS</font>
						</td>
						<td align="center">
							<font style="width: 30px;">YM</font>
						</td>
						<td align="center">
							<font style="width: 30px;">YL</font>
						</td>

						<td align="center">
							<font style="width: 30px;">YXL</font>
						</td>
						<td align="center">
							<font style="width: 30px;">S</font>
						</td>
						<td align="center">
							<font style="width: 30px;">M</font>
						</td>
						<td align="center">
							<font style="width: 30px;">L</font>
						</td>
						<td align="center">
							<font style="width: 30px;">XL</font>
						</td>
						<td align="center">
							<font style="width: 30px;">XXL</font>
						</td>
						<td align="center">
							<font style="width: 40px;">XXXL</font>
						</td>
						<td align="center">
							<font style="width: 40px;">XXXXL</font>
						</td>
						<td align="center">
							<font style="width: 40px;">Misc</font>
						</td>

						<td align="center">
							<font style="width: 40px;">Price</font>
						</td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="1quantity" style="width: 55px;" value="<?php echo isset($quantity[1]) ? $quantity[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1description" style="width: 145px;" value='<?php echo isset($description[1]) ? $description[1] : ''; ?>' onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1color" style="width: 65px;" value="<?php echo isset($color[1]) ? $color[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1vendor" style="width: 65px;" value="<?php echo isset($vendor[1]) ? $vendor[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1yxs" style="width: 25px;" value="<?php echo isset($yxs[1]) ? $yxs[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1ys" style="width: 25px;" value="<?php echo isset($ys[1]) ? $ys[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1ym" style="width: 25px;" value="<?php echo isset($ym[1]) ? $ym[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1yl" style="width: 25px;" value="<?php echo isset($yl[1]) ? $yl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1yxl" style="width: 25px;" value="<?php echo isset($yxl[1]) ? $yxl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1s" style="width: 25px;" value="<?php echo isset($s[1]) ? $s[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1m" style="width: 25px;" value="<?php echo isset($m[1]) ? $m[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1l" style="width: 25px;" value="<?php echo isset($l[1]) ? $l[1] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="1xl" style="width: 25px;" value="<?php echo isset($xl[1]) ? $xl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1xxl" style="width: 25px;" value="<?php echo isset($xxl[1]) ? $xxl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1xxxl" style="width: 35px;" value="<?php echo isset($xxxl[1]) ? $xxxl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[1]) ? $xxxxl[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1misc" style="width: 35px;" value="<?php echo isset($misc[1]) ? $misc[1] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="1price" style="width: 35px;" value="<?php echo isset($price[1]) ? $price[1] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="2quantity" style="width: 55px;" value="<?php echo isset($quantity[2]) ? $quantity[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2description" style="width: 145px;" value="<?php echo isset($description[2]) ? $description[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2color" style="width: 65px;" value="<?php echo isset($color[2]) ? $color[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2vendor" style="width: 65px;" value="<?php echo isset($vendor[2]) ? $vendor[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2yxs" style="width: 25px;" value="<?php echo isset($yxs[2]) ? $yxs[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2ys" style="width: 25px;" value="<?php echo isset($ys[2]) ? $ys[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2ym" style="width: 25px;" value="<?php echo isset($ym[2]) ? $ym[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2yl" style="width: 25px;" value="<?php echo isset($yl[2]) ? $yl[2] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="2yxl" style="width: 25px;" value="<?php echo isset($yxl[2]) ? $yxl[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2s" style="width: 25px;" value="<?php echo isset($s[2]) ? $s[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2m" style="width: 25px;" value="<?php echo isset($m[2]) ? $m[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2l" style="width: 25px;" value="<?php echo isset($l[2]) ? $l[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2xl" style="width: 25px;" value="<?php echo isset($xl[2]) ? $xl[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2xxl" style="width: 25px;" value="<?php echo isset($xxl[2]) ? $xxl[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2xxxl" style="width: 35px;" value="<?php echo isset($xxxl[2]) ? $xxxl[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[2]) ? $xxxxl[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2misc" style="width: 35px;" value="<?php echo isset($misc[2]) ? $misc[2] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="2price" style="width: 35px;" value="<?php echo isset($price[2]) ? $price[2] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="3quantity" style="width: 55px;" value="<?php echo isset($quantity[3]) ? $quantity[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3description" style="width: 145px;" value="<?php echo isset($description[3]) ? $description[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3color" style="width: 65px;" value="<?php echo isset($color[3]) ? $color[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3vendor" style="width: 65px;" value="<?php echo isset($vendor[3]) ? $vendor[3] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="3yxs" style="width: 25px;" value="<?php echo isset($yxs[3]) ? $yxs[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3ys" style="width: 25px;" value="<?php echo isset($ys[3]) ? $ys[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3ym" style="width: 25px;" value="<?php echo isset($ym[3]) ? $ym[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3yl" style="width: 25px;" value="<?php echo isset($yl[3]) ? $yl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3yxl" style="width: 25px;" value="<?php echo isset($yxl[3]) ? $yxl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3s" style="width: 25px;" value="<?php echo isset($s[3]) ? $s[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3m" style="width: 25px;" value="<?php echo isset($m[3]) ? $m[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3l" style="width: 25px;" value="<?php echo isset($l[3]) ? $l[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3xl" style="width: 25px;" value="<?php echo isset($xl[3]) ? $xl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3xxl" style="width: 25px;" value="<?php echo isset($xxl[3]) ? $xxl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3xxxl" style="width: 35px;" value="<?php echo isset($xxxl[3]) ? $xxxl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[3]) ? $xxxxl[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3misc" style="width: 35px;" value="<?php echo isset($misc[3]) ? $misc[3] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="3price" style="width: 35px;" value="<?php echo isset($price[3]) ? $price[3] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="4quantity" style="width: 55px;" value="<?php echo isset($quantity[4]) ? $quantity[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4description" style="width: 145px;" value="<?php echo isset($description[4]) ? $description[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4color" style="width: 65px;" value="<?php echo isset($color[4]) ? $color[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4vendor" style="width: 65px;" value="<?php echo isset($vendor[4]) ? $vendor[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4yxs" style="width: 25px;" value="<?php echo isset($yxs[4]) ? $yxs[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4ys" style="width: 25px;" value="<?php echo isset($ys[4]) ? $ys[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4ym" style="width: 25px;" value="<?php echo isset($ym[4]) ? $ym[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4yl" style="width: 25px;" value="<?php echo isset($yl[4]) ? $yl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4yxl" style="width: 25px;" value="<?php echo isset($yxl[4]) ? $yxl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4s" style="width: 25px;" value="<?php echo isset($s[4]) ? $s[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4m" style="width: 25px;" value="<?php echo isset($m[4]) ? $m[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4l" style="width: 25px;" value="<?php echo isset($l[4]) ? $l[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4xl" style="width: 25px;" value="<?php echo isset($xl[4]) ? $xl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4xxl" style="width: 25px;" value="<?php echo isset($xxl[4]) ? $xxl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4xxxl" style="width: 35px;" value="<?php echo isset($xxxl[4]) ? $xxxl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[4]) ? $xxxxl[4] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="4misc" style="width: 35px;" value="<?php echo isset($misc[4]) ? $misc[4] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="4price" style="width: 35px;" value="<?php echo isset($price[4]) ? $price[4] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="5quantity" style="width: 55px;" value="<?php echo isset($quantity[5]) ? $quantity[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5description" style="width: 145px;" value="<?php echo isset($description[5]) ? $description[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5color" style="width: 65px;" value="<?php echo isset($color[5]) ? $color[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5vendor" style="width: 65px;" value="<?php echo isset($vendor[5]) ? $vendor[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5yxs" style="width: 25px;" value="<?php echo isset($yxs[5]) ? $yxs[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5ys" style="width: 25px;" value="<?php echo isset($ys[5]) ? $ys[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5ym" style="width: 25px;" value="<?php echo isset($ym[5]) ? $ym[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5yl" style="width: 25px;" value="<?php echo isset($yl[5]) ? $yl[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5yxl" style="width: 25px;" value="<?php echo isset($yxl[5]) ? $yxl[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5s" style="width: 25px;" value="<?php echo isset($s[5]) ? $s[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5m" style="width: 25px;" value="<?php echo isset($m[5]) ? $m[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5l" style="width: 25px;" value="<?php echo isset($l[5]) ? $l[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5xl" style="width: 25px;" value="<?php echo isset($xl[5]) ? $xl[5] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="5xxl" style="width: 25px;" value="<?php echo isset($xxl[5]) ? $xxl[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5xxxl" style="width: 35px;" value="<?php echo isset($xxxl[5]) ? $xxxl[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[5]) ? $xxxxl[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5misc" style="width: 35px;" value="<?php echo isset($misc[5]) ? $misc[5] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="5price" style="width: 35px;" value="<?php echo isset($price[5]) ? $price[5] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="6quantity" style="width: 55px;" value="<?php echo isset($quantity[6]) ? $quantity[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6description" style="width: 145px;" value="<?php echo isset($description[6]) ? $description[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6color" style="width: 65px;" value="<?php echo isset($color[6]) ? $color[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6vendor" style="width: 65px;" value="<?php echo isset($vendor[6]) ? $vendor[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6yxs" style="width: 25px;" value="<?php echo isset($yxs[6]) ? $yxs[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6ys" style="width: 25px;" value="<?php echo isset($ys[6]) ? $ys[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6ym" style="width: 25px;" value="<?php echo isset($ym[6]) ? $ym[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6yl" style="width: 25px;" value="<?php echo isset($yl[6]) ? $yl[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6yxl" style="width: 25px;" value="<?php echo isset($yxl[6]) ? $yxl[6] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="6s" style="width: 25px;" value="<?php echo isset($s[6]) ? $s[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6m" style="width: 25px;" value="<?php echo isset($m[6]) ? $m[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6l" style="width: 25px;" value="<?php echo isset($l[6]) ? $l[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6xl" style="width: 25px;" value="<?php echo isset($xl[6]) ? $xl[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6xxl" style="width: 25px;" value="<?php echo isset($xxl[6]) ? $xxl[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6xxxl" style="width: 35px;" value="<?php echo isset($xxxl[6]) ? $xxxl[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[6]) ? $xxxxl[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6misc" style="width: 35px;" value="<?php echo isset($misc[6]) ? $misc[6] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="6price" style="width: 35px;" value="<?php echo isset($price[6]) ? $price[6] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="7quantity" style="width: 55px;" value="<?php echo isset($quantity[7]) ? $quantity[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7description" style="width: 145px;" value="<?php echo isset($description[7]) ? $description[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7color" style="width: 65px;" value="<?php echo isset($color[7]) ? $color[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7vendor" style="width: 65px;" value="<?php echo isset($vendor[7]) ? $vendor[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7yxs" style="width: 25px;" value="<?php echo isset($yxs[7]) ? $yxs[7] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="7ys" style="width: 25px;" value="<?php echo isset($ys[7]) ? $ys[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7ym" style="width: 25px;" value="<?php echo isset($ym[7]) ? $ym[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7yl" style="width: 25px;" value="<?php echo isset($yl[7]) ? $yl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7yxl" style="width: 25px;" value="<?php echo isset($yxl[7]) ? $yxl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7s" style="width: 25px;" value="<?php echo isset($s[7]) ? $s[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7m" style="width: 25px;" value="<?php echo isset($m[7]) ? $m[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7l" style="width: 25px;" value="<?php echo isset($l[7]) ? $l[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7xl" style="width: 25px;" value="<?php echo isset($xl[7]) ? $xl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7xxl" style="width: 25px;" value="<?php echo isset($xxl[7]) ? $xxl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7xxxl" style="width: 35px;" value="<?php echo isset($xxxl[7]) ? $xxxl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[7]) ? $xxxxl[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7misc" style="width: 35px;" value="<?php echo isset($misc[7]) ? $misc[7] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="7price" style="width: 35px;" value="<?php echo isset($price[7]) ? $price[7] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="8quantity" style="width: 55px;" value="<?php echo isset($quantity[8]) ? $quantity[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8description" style="width: 145px;" value="<?php echo isset($description[8]) ? $description[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8color" style="width: 65px;" value="<?php echo isset($color[8]) ? $color[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8vendor" style="width: 65px;" value="<?php echo isset($vendor[8]) ? $vendor[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8yxs" style="width: 25px;" value="<?php echo isset($yxs[8]) ? $yxs[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8ys" style="width: 25px;" value="<?php echo isset($ys[8]) ? $ys[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8ym" style="width: 25px;" value="<?php echo isset($ym[8]) ? $ym[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8yl" style="width: 25px;" value="<?php echo isset($yl[8]) ? $yl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8yxl" style="width: 25px;" value="<?php echo isset($yxl[8]) ? $yxl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8s" style="width: 25px;" value="<?php echo isset($s[8]) ? $s[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8m" style="width: 25px;" value="<?php echo isset($m[8]) ? $m[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8l" style="width: 25px;" value="<?php echo isset($l[8]) ? $l[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8xl" style="width: 25px;" value="<?php echo isset($xl[8]) ? $xl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8xxl" style="width: 25px;" value="<?php echo isset($xxl[8]) ? $xxl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8xxxl" style="width: 35px;" value="<?php echo isset($xxxl[8]) ? $xxxl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[8]) ? $xxxxl[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8misc" style="width: 35px;" value="<?php echo isset($misc[8]) ? $misc[8] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="8price" style="width: 35px;" value="<?php echo isset($price[8]) ? $price[8] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="9quantity" style="width: 55px;" value="<?php echo isset($quantity[9]) ? $quantity[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9description" style="width: 145px;" value="<?php echo isset($description[9]) ? $description[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9color" style="width: 65px;" value="<?php echo isset($color[9]) ? $color[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9vendor" style="width: 65px;" value="<?php echo isset($vendor[9]) ? $vendor[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9yxs" style="width: 25px;" value="<?php echo isset($yxs[9]) ? $yxs[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9ys" style="width: 25px;" value="<?php echo isset($ys[9]) ? $ys[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9ym" style="width: 25px;" value="<?php echo isset($ym[9]) ? $ym[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9yl" style="width: 25px;" value="<?php echo isset($yl[9]) ? $yl[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9yxl" style="width: 25px;" value="<?php echo isset($yxl[9]) ? $yxl[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9s" style="width: 25px;" value="<?php echo isset($s[9]) ? $s[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9m" style="width: 25px;" value="<?php echo isset($m[9]) ? $m[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9l" style="width: 25px;" value="<?php echo isset($l[9]) ? $l[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9xl" style="width: 25px;" value="<?php echo isset($xl[9]) ? $xl[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9xxl" style="width: 25px;" value="<?php echo isset($xxl[9]) ? $xxl[9] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="9xxxl" style="width: 35px;" value="<?php echo isset($xxxl[9]) ? $xxxl[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[9]) ? $xxxxl[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9misc" style="width: 35px;" value="<?php echo isset($misc[9]) ? $misc[9] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="9price" style="width: 35px;" value="<?php echo isset($price[9]) ? $price[9] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="10quantity" style="width: 55px;" value="<?php echo isset($quantity[10]) ? $quantity[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10description" style="width: 145px;" value="<?php echo isset($description[10]) ? $description[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10color" style="width: 65px;" value="<?php echo isset($color[10]) ? $color[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10vendor" style="width: 65px;" value="<?php echo isset($vendor[10]) ? $vendor[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10yxs" style="width: 25px;" value="<?php echo isset($yxs[10]) ? $yxs[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10ys" style="width: 25px;" value="<?php echo isset($ys[10]) ? $ys[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10ym" style="width: 25px;" value="<?php echo isset($ym[10]) ? $ym[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10yl" style="width: 25px;" value="<?php echo isset($yl[10]) ? $yl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10yxl" style="width: 25px;" value="<?php echo isset($yxl[10]) ? $yxl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10s" style="width: 25px;" value="<?php echo isset($s[10]) ? $s[10] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="10m" style="width: 25px;" value="<?php echo isset($m[10]) ? $m[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10l" style="width: 25px;" value="<?php echo isset($l[10]) ? $l[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10xl" style="width: 25px;" value="<?php echo isset($xl[10]) ? $xl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10xxl" style="width: 25px;" value="<?php echo isset($xxl[10]) ? $xxl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10xxxl" style="width: 35px;" value="<?php echo isset($xxxl[10]) ? $xxxl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[10]) ? $xxxxl[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10misc" style="width: 35px;" value="<?php echo isset($misc[10]) ? $misc[10] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="10price" style="width: 35px;" value="<?php echo isset($price[10]) ? $price[10] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="11quantity" style="width: 55px;" value="<?php echo isset($quantity[11]) ? $quantity[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11description" style="width: 145px;" value="<?php echo isset($description[11]) ? $description[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11color" style="width: 65px;" value="<?php echo isset($color[11]) ? $color[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11vendor" style="width: 65px;" value="<?php echo isset($vendor[11]) ? $vendor[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11yxs" style="width: 25px;" value="<?php echo isset($yxs[11]) ? $yxs[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11ys" style="width: 25px;" value="<?php echo isset($ys[11]) ? $ys[11] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="11ym" style="width: 25px;" value="<?php echo isset($ym[11]) ? $ym[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11yl" style="width: 25px;" value="<?php echo isset($yl[11]) ? $yl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11yxl" style="width: 25px;" value="<?php echo isset($yxl[11]) ? $yxl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11s" style="width: 25px;" value="<?php echo isset($s[11]) ? $s[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11m" style="width: 25px;" value="<?php echo isset($m[11]) ? $m[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11l" style="width: 25px;" value="<?php echo isset($l[11]) ? $l[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11xl" style="width: 25px;" value="<?php echo isset($xl[11]) ? $xl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11xxl" style="width: 25px;" value="<?php echo isset($xxl[11]) ? $xxl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11xxxl" style="width: 35px;" value="<?php echo isset($xxxl[11]) ? $xxxl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[11]) ? $xxxxl[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11misc" style="width: 35px;" value="<?php echo isset($misc[11]) ? $misc[11] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="11price" style="width: 35px;" value="<?php echo isset($price[11]) ? $price[11] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"></td>
						<td align="center"><input class="tabletext" name="12quantity" style="width: 55px;" value="<?php echo isset($quantity[12]) ? $quantity[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12description" style="width: 145px;" value="<?php echo isset($description[12]) ? $description[12] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="12color" style="width: 65px;" value="<?php echo isset($color[12]) ? $color[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12vendor" style="width: 65px;" value="<?php echo isset($vendor[12]) ? $vendor[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12yxs" style="width: 25px;" value="<?php echo isset($yxs[12]) ? $yxs[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12ys" style="width: 25px;" value="<?php echo isset($ys[12]) ? $ys[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12ym" style="width: 25px;" value="<?php echo isset($ym[12]) ? $ym[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12yl" style="width: 25px;" value="<?php echo isset($yl[12]) ? $yl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12yxl" style="width: 25px;" value="<?php echo isset($yxl[12]) ? $yxl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12s" style="width: 25px;" value="<?php echo isset($s[12]) ? $s[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12m" style="width: 25px;" value="<?php echo isset($m[12]) ? $m[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12l" style="width: 25px;" value="<?php echo isset($l[12]) ? $l[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12xl" style="width: 25px;" value="<?php echo isset($xl[12]) ? $xl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12xxl" style="width: 25px;" value="<?php echo isset($xxl[12]) ? $xxl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12xxxl" style="width: 35px;" value="<?php echo isset($xxxl[12]) ? $xxxl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[12]) ? $xxxxl[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12misc" style="width: 35px;" value="<?php echo isset($misc[12]) ? $misc[12] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="12price" style="width: 35px;" value="<?php echo isset($price[12]) ? $price[12] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<tr>

						<td align="center"><button type="button" id="addRow" onclick="addRow1()">+</button></td>
						<td align="center"><input class="tabletext" name="13quantity" style="width: 55px;" value="<?php echo isset($quantity[13]) ? $quantity[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13description" style="width: 145px;" value="<?php echo isset($description[13]) ? $description[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13color" style="width: 65px;" value="<?php echo isset($color[13]) ? $color[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13vendor" style="width: 65px;" value="<?php echo isset($vendor[13]) ? $vendor[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13yxs" style="width: 25px;" value="<?php echo isset($yxs[13]) ? $yxs[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13ys" style="width: 25px;" value="<?php echo isset($ys[13]) ? $ys[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13ym" style="width: 25px;" value="<?php echo isset($ym[13]) ? $ym[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13yl" style="width: 25px;" value="<?php echo isset($yl[13]) ? $yl[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13yxl" style="width: 25px;" value="<?php echo isset($yxl[13]) ? $yxl[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13s" style="width: 25px;" value="<?php echo isset($s[13]) ? $s[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13m" style="width: 25px;" value="<?php echo isset($m[13]) ? $m[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13l" style="width: 25px;" value="<?php echo isset($l[13]) ? $l[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13xl" style="width: 25px;" value="<?php echo isset($xl[13]) ? $xl[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13xxl" style="width: 25px;" value="<?php echo isset($xxl[13]) ? $xxl[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13xxxl" style="width: 35px;" value="<?php echo isset($xxxl[13]) ? $xxxl[13] : ''; ?>" onchange="maketotals();"></td>

						<td align="center"><input class="tabletext" name="13xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[13]) ? $xxxxl[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13misc" style="width: 35px;" value="<?php echo isset($misc[13]) ? $misc[13] : ''; ?>" onchange="maketotals();"></td>
						<td align="center"><input class="tabletext" name="13price" style="width: 35px;" value="<?php echo isset($price[13]) ? $price[13] : ''; ?>" onchange="maketotals();"></td>

					</tr>
					<?php if (isset($_REQUEST['orderID']) && ($totalRows > 13)) {
						for ($k = 14; $k < $totalRows + 1; $k++) { ?>
							<tr>
								<td align="center"><button type="button" onclick="removeRow(this)">-</button></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>quantity" style="width: 55px;" value="<?php echo isset($quantity[$k]) ? $quantity[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>description" style="width: 145px;" value="<?php echo isset($description[$k]) ? $description[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>color" style="width: 65px;" value="<?php echo isset($color[$k]) ? $color[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>vendor" style="width: 65px;" value="<?php echo isset($vendor[$k]) ? $vendor[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>yxs" style="width: 25px;" value="<?php echo isset($yxs[$k]) ? $yxs[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>ys" style="width: 25px;" value="<?php echo isset($ys[$k]) ? $ys[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>ym" style="width: 25px;" value="<?php echo isset($ym[$k]) ? $ym[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>yl" style="width: 25px;" value="<?php echo isset($yl[$k]) ? $yl[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>yxl" style="width: 25px;" value="<?php echo isset($yxl[$k]) ? $yxl[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>s" style="width: 25px;" value="<?php echo isset($s[$k]) ? $s[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>m" style="width: 25px;" value="<?php echo isset($m[$k]) ? $m[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>l" style="width: 25px;" value="<?php echo isset($l[$k]) ? $l[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>xl" style="width: 25px;" value="<?php echo isset($xl[$k]) ? $xl[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>xxl" style="width: 25px;" value="<?php echo isset($xxl[$k]) ? $xxl[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>xxxl" style="width: 35px;" value="<?php echo isset($xxxl[$k]) ? $xxxl[$k] : ''; ?>" onchange="maketotals();"></td>

								<td align="center"><input class="tabletext" name="<?= $k ?>xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[$k]) ? $xxxxl[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>misc" style="width: 35px;" value="<?php echo isset($misc[$k]) ? $misc[$k] : ''; ?>" onchange="maketotals();"></td>
								<td align="center"><input class="tabletext" name="<?= $k ?>price" style="width: 35px;" value="<?php echo isset($price[$k]) ? $price[$k] : ''; ?>" onchange="maketotals();"></td>
							</tr>
					<?php }
					} ?>
				</table>

				<div id="totalContainer" style="position:absolute; top:510px; left:765px; width:210px; text-align:right">
					<span style="position:relative; left:-10px">Total</span><br />
					<input class="tabletotaltext" name="1total" id="1total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="2total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="3total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="4total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="5total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="6total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="7total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="8total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="9total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="10total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="11total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="12total" style="width: 40px;" value=""><br />
					<input class="tabletotaltext" name="13total" style="width: 40px;" value=""><br />
					<?php if (isset($_REQUEST['orderID']) && ($totalRows > 13)) {
						for ($k = 14; $k < $totalRows + 1; $k++) { ?>
							<input class="tabletotaltext" name="<?= $k ?>total" style="width: 40px;" value=""><br />
					<?php }
					} ?>
					<label>Sub-total <input class='tabletotaltext' name="subtotal" style="width: 40px;" value=""></label><br />
					<label><input class='tabletotaltext' name="salestaxradio" value="salestaxradio" type=checkbox <?php if ($salesTax > 0) echo 'checked="checked"' ?> onclick="maketotals();">&nbsp;Sales Tax</label> <input class='tabletotaltext' name="salesTax" style="width: 40px;" value="<?php echo $salesTax ?? ''; ?>" onchange="maketotals();"><br />
					Screens: <input class='tabletotaltext' name="screenNumber" style="width: 20px;" value="<?php echo $screenNumber ?? ''; ?>" onchange="maketotals();" /> x <input class='tabletotaltext' name="screenCharge" style="width: 30px;" value="<?php echo $screenCharge ?? ''; ?>" onchange="maketotals();">= $<input class='tabletotaltext' name="screenTotal" style="width: 40px;" value="" onchange="maketotals();"><br />
					<label>Die Charge</font><input class='tabletotaltext' name="dieCharge" style="width: 40px;" value="<?php echo $dieCharge ?? ''; ?>" onchange="maketotals();"></label><br />
					Art: <input class='tabletotaltext' name="artNumber" style="width: 20px;" value="<?php echo $artNumber ?? ''; ?>" onchange="maketotals();"> x <input class='tabletotaltext' name="artCharge" style="width: 30px;" value="<?php echo $artCharge ?? ''; ?>" onchange="maketotals();">= $<input class='tabletotaltext' name="artTotal" style="width: 40px;" value="" onchange="maketotals();"><br />
					<label>Color Charge <input class='tabletotaltext' name="colorCharge" style="width: 40px;" value="<?php echo $colorCharge ?? ''; ?>" onchange="maketotals();"></label><br />
					<label>Shipping <input class='tabletotaltext' name="shippingCharge" style="width: 40px;" value="<?php echo $shippingCharge ?? ''; ?>" onchange="maketotals();"></label><br />
					<input class='tabletotaltext' name="miscDescription" style="width: 60px; text-align: right;" value="<?php echo $miscDescription ?? ''; ?>"> <input class='tabletotaltext' name="miscCharge" style="width: 40px;" value="<?php echo $miscCharge ?? ''; ?>" onchange="maketotals();"><br />
					<label>Total <input class='tabletotaltext' name="total" style="width: 40px;" value=""></label><br />
					<label>Deposit <input class='tabletotaltext' name="deposit" style="width: 40px;" value="<?php echo $deposit ?? ''; ?>" onchange="maketotals();"></label><br />
					<label>Balance <input class='tabletotaltext' name="balance" style="width: 40px;" value=""></label><br /><br /><br />
					<?php if (isset($_REQUEST['edit'])) {
						if ($_REQUEST['edit'] == 1) {
							echo "<input type='button' style='width:100px' value='Print Order' onclick='printOrderByID(" . $_REQUEST['orderID'] . ")' /><br /><br />";

							$buttonString = "Save Changes";
						} else
							$buttonString = "Add Order"; ?>
						<input type='hidden' name="orderID" value="<?php echo $orderID ?? ''; ?>" />
						<input type='submit' name="addOrder" style='width:100px' value="<?php echo $buttonString; ?>" /><br />
						<div id='submitErrorMessage' class='alertBox' style='text-align:left; margin-left:auto; width:130px; position:relative; bottom:130px; visibility:hidden'></div>
					<?php } ?>
				</div>


				<?php $addHight = $totalRows > 13 ? ($totalRows - 13) * 23 : 0; ?>
				<div style="position:absolute; top:<?= 850 + $addHight ?>px; width:410px; text-align:right;" id="frontDiv">
					<label>Front &nbsp;<select name="front" style="width: 100px;">
							<option>No Print</option>
							<option <?php if ($front == 'Left Chest') echo "selected='selected'" ?>>Left Chest</option>
							<option <?php if ($front == 'Right Chest') echo "selected='selected'" ?>>Right Chest</option>
							<option <?php if ($front == 'Center Chest') echo "selected='selected'" ?>>Center Chest</option>
							<option <?php if ($front == 'Full Chest') echo "selected='selected'" ?>>Full Chest</option>
						</select></label>
					&nbsp;&nbsp;&nbsp;<input class="text" name="frontDetails" style="width: 240px;" value="<?php echo $frontDetails ?? ''; ?>" /><br />

					<label>Back &nbsp;<select name="back" style="width: 100px;">
							<option>No Print</option>
							<option <?php if ($back == 'Full Back') echo "selected='selected'" ?>>Full Back</option>
							<option <?php if ($back == 'Small Back') echo "selected='selected'" ?>>Small Back</option>
							<option <?php if ($back == 'Tag') echo "selected='selected'" ?>>Tag</option>
						</select></label>
					&nbsp;&nbsp;&nbsp;<input class="text" name="backDetails" style="width: 240px;" value="<?php echo $backDetails ?? ''; ?>" /><br />

					<label>Sleeve &nbsp;<select name="sleeve" style="width: 100px;">
							<option>No Print</option>
							<option <?php if ($sleeve == 'Left') echo "selected='selected'" ?>>Left</option>
							<option <?php if ($sleeve == 'Right') echo "selected='selected'" ?>>Right</option>
							<option <?php if ($sleeve == 'Both') echo "selected='selected'" ?>>Both</option>
						</select></label>
					&nbsp;&nbsp;&nbsp;<input class="text" name="sleeveDetails" style="width: 240px;" value="<?php echo $sleeveDetails ?? ''; ?>" /><br />

					<label>Other &nbsp;<input class="text" name="other" style="width: 100px;" value="<?php echo $other ?? ''; ?>" /></label>
					&nbsp;&nbsp;&nbsp;<input class="text" name="otherDetails" style="width: 240px" value="<?php echo $otherDetails ?? ''; ?>" /><br />
				</div>

				<?php if (isset($artFilename) && $artFilename != "") { ?>
					<div style="position:absolute; top:<?= 850 + $addHight ?>px; left:450px" id="imageDiv">
						<a href="<?php echo "files/" . $artFilename; ?>" target="_blank"><img src="<?php echo "files/" . $artFilename; ?>" width="100px" height="100px" /></a>
					</div>
				<?php } ?>

				<fieldset style="position:absolute; top:<?= 850 + $addHight ?>px; left:586px; width:150px;" id="stageDiv">
					<legend>Stage</legend>
					<label>Order Goods<input class="initialButton" type="button" onclick="initialButton('stageOrderGoods<?php echo "," . $_SESSION['initials'] ?>')" name="stageOrderGoods" value="<?php echo $stageOrderGoods ?? ''; ?>" /></label><br />
					<label>Goods Received<input class="initialButton" type="button" onclick="initialButton('stageGoodsReceived<?php echo "," . $_SESSION['initials'] ?>')" name="stageGoodsReceived" value="<?php echo $stageGoodsReceived ?? ''; ?>" /></label><br />
					<label>Art Approval<input class="initialButton" type="button" onclick="initialButton('artistApproved<?php echo "," . $_SESSION['initials'] ?>')" name="artistApproved" value="<?php echo $artistApproved ?? ''; ?>" /></label><br />
					<label>Order Staging<input class="initialButton" type="button" onclick="initialButton('stageOrderStaging<?php echo "," . $_SESSION['initials'] ?>')" name="stageOrderStaging" value="<?php echo $stageOrderStaging ?? ''; ?>" /></label><br />
					<label>Printing/Stitching<input class="initialButton" type="button" onclick="initialButton('stagePrintingStitching<?php echo "," . $_SESSION['initials'] ?>')" name="stagePrintingStitching" value="<?php echo $stagePrintingStitching ?? ''; ?>" /></label><br />
					<label>Complete<input class="initialButton" type="button" onclick="initialButton('stageComplete<?php echo "," . $_SESSION['initials'] ?>')" name="stageComplete" value="<?php echo $stageComplete ?? ''; ?>" /></label><br />
					<label>Billed<input class="initialButton" type="button" onclick="initialButton('stageBilled<?php echo "," . $_SESSION['initials'] ?>')" name="stageBilled" value="<?php echo $stageBilled ?? ''; ?>" /></label><br />
					<label>Paid<input class="initialButton" type="button" onclick="initialButton('stagePaid<?php echo "," . $_SESSION['initials'] ?>')" name="stagePaid" value="<?php echo $stagePaid ?? ''; ?>" /></label><br />
				</fieldset>

				<div style="position:absolute; top:<?= 1100 + $addHight ?>px; left:586px;" id="departmentDiv">
					<label>Department <select name="departmentID" style="width:105px"><?php echo $departmentList ?></select></label>
					<?php if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == 1) { ?>
						<br /><br />
						<div class='alertBox' id='duplicateAlert' style='position:absolute; top:-75px; left:0px; width: 120px, height 140px; visibility:hidden;'>
							You are not updating the existing order; you are producing a new order that is a duplicate of this order. Is this what you want to do?<br /><br />
							<input type='button' onclick='document.getElementById("duplicateAlert").style.visibility = "hidden"; document.getElementById("duplicateCheckbox").checked = false;' value="No" style="float:left" ; /><input type='button' onclick="document.getElementById('duplicateAlert').style.visibility = 'hidden';" style="float:right" value="Yes" />
						</div>
						<label><input type='checkbox' name='saveAsNewOrder' id='duplicateCheckbox' onclick="if( this.checked ) document.getElementById('duplicateAlert').style.visibility = 'visible';" />Produce Duplicate Order</label>
					<?php } ?>
				</div>

				<div style="position:absolute; top:<?= 960 + $addHight ?>px; width:560px" id="artCommentDiv">
					<div style="margin-bottom:7px">
						<label style="margin-left:3px">Artwork <select name="artType" style="width: 80px;">
								<option>New</option>
								<option <?php if (isset($artType) && $artType == 'Repeat') echo "selected='selected'" ?>>Repeat</option>
								<option <?php if (isset($artType) && $artType == 'Revision') echo "selected='selected'" ?>>Revision</option>
							</select></label>
						<label style="margin:0px 16px">Artist <select name="artistID"><?php echo $artistList ?></select></label>
						<input type="file" name="file" style="height: 22px; width: 220px; margin:0px;"><br />
					</div>

					<label>Art Instructions:<textarea name="artInstructions" style="width: 560px; height: 70px"><?php echo $artInstructions ?? ''; ?></textarea></label>

					<div style="width:560px; height:80px; margin-top:5px">
						<div class="initialDiv" style="width:65px; margin-right:3px;">In Progress<input class="initialButton" type="button" name="artistInProgress" style="width: 65px; margin:2px 0px;" value="<?php echo $artistInProgress ?? ''; ?>" onclick="initialButton('artistInProgress<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Complete<input class="initialButton" type="button" name="artistComplete" style="width: 65px; margin:2px 0px;" value="<?php echo $artistComplete ?? ''; ?>" onclick="initialButton('artistComplete<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Sent/Client<input class="initialButton" type="button" name="artistSentToApprove" style="width: 65px; margin:2px 0px;" value="<?php echo $artistSentToApprove ?? ''; ?>" onclick="initialButton('artistSentToApprove<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Revisions<input class="initialButton" type="button" name="artistRevisions" style="width: 65px; margin:2px 0px;" value="<?php echo $artistRevisions ?? ''; ?>" onclick="initialButton('artistRevisions<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Approved<input class="initialButton" type="button" name="artistApproved2" style="width: 65px; margin:2px 0px;" value="<?php echo $artistApproved ?? ''; ?>" onclick="initialButton('artistApproved<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Seps Done<input class="initialButton" type="button" name="artistSepsDone" style="width: 65px; margin:2px 0px;" value="<?php echo $artistSepsDone ?? ''; ?>" onclick="initialButton('artistSepsDone<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv">Proof Film<input class="initialButton" type="button" name="artistProofFilm" style="width: 65px; margin:2px 0px;" value="<?php echo $artistProofFilm ?? ''; ?>" onclick="initialButton('artistProofFilm<?php echo "," . $_SESSION['initials'] ?>')" /></div>
						<div class="initialDiv" style="margin-left:2px">Hours<input class="text" name="artistHours" style="width: 65px; height:18px; text-align:center" value="<?php echo $artistHours ?? ''; ?>" /></div>
					</div>

					<label style="position:relative; top:-39px;">Comments:<textarea name="comments" style="width: 560px; height: 40px;"><?php echo $comments ?? ''; ?></textarea></label><?php } ?>
				</div>
				<input type='hidden' name='clientID' value='<?php echo $clientID ?? ''; ?>' />
				<input type='hidden' name="artFilename" value='<?php echo $artFilename ?? ''; ?>' />
				<input type='hidden' name='artistInProgress_' value='<?php echo $artistInProgress ?? ''; ?>' />
				<input type='hidden' name='artistComplete_' value='<?php echo $artistComplete ?? ''; ?>' />
				<input type='hidden' name='artistSentToApprove_' value='<?php echo $artistSentToApprove ?? ''; ?>' />
				<input type='hidden' name='artistRevisions_' value='<?php echo $artistRevisions ?? ''; ?>' />
				<input type='hidden' name='artistApproved_' value='<?php echo $artistApproved ?? ''; ?>' />
				<input type='hidden' name='artistSepsDone_' value='<?php echo $artistSepsDone ?? ''; ?>' />
				<input type='hidden' name='artistProofFilm_' value='<?php echo $artistProofFilm ?? ''; ?>' />
				<input type='hidden' name='stageOrderGoods_' value='<?php echo $stageOrderGoods ?? ''; ?>' />
				<input type='hidden' name='stageGoodsReceived_' value='<?php echo $stageGoodsReceived ?? ''; ?>' />
				<input type='hidden' name='stageOrderStaging_' value='<?php echo $stageOrderStaging ?? ''; ?>' />
				<input type='hidden' name='stagePrintingStitching_' value='<?php echo $stagePrintingStitching ?? ''; ?>' />
				<input type='hidden' name='stageComplete_' value='<?php echo $stageComplete ?? ''; ?>' />
				<input type='hidden' name='stageBilled_' value='<?php echo $stageBilled ?? ''; ?>' />
				<input type='hidden' name='stagePaid_' value='<?php echo $stagePaid ?? ''; ?>' />
		</form>
		<script type='text/javascript'>
			window.addEventListener('load', () => {
				localStorage.setItem('orderItems', 14);
			});
			maketotals();
		</script>

		</div>
		<!-- InstanceEndEditable -->

		<script>
			let rowCount = <?php echo $totalRows > 13 ? $totalRows + 1 : 14; ?>; // Start new rows after 13
			let i = 1;

			function addRow1() {
				const table = document.getElementById('dynamicTable');
				const row = table.insertRow();
				row.innerHTML = `
					<td align="center"><button type="button" onclick="removeRow(this)">-</button></td>
					<td align="center"><input class="tabletext" name="${rowCount}quantity" style="width: 55px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}description" style="width: 145px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}color" style="width: 65px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}vendor" style="width: 65px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}yxs" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}ys" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}ym" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}yl" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}yxl" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}s" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}m" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}l" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}xl" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}xxl" style="width: 25px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}xxxl" style="width: 35px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}xxxxl" style="width: 35px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}misc" style="width: 35px;" onchange="maketotals();"></td>
					<td align="center"><input class="tabletext" name="${rowCount}price" style="width: 35px;" onchange="maketotals();"></td>
				`;

				addTotalRow(rowCount, rowCount);
				localStorage.setItem('orderItems', 14 + i);
				rowCount++;
				i++;
				adjustTopValue(20, 3);
			}

			function addTotalRow(rowCount, number) {
				var container = document.getElementById('totalContainer');
				var inputs = container.getElementsByTagName('input');

				var lastInput = null;
				for (var i = inputs.length - 1; i >= 0; i--) {
					var name = inputs[i].name;
					if (name && /^(\d+)total$/.test(name)) {
						lastInput = inputs[i];
						break;
					}
				}

				var lastInputNumber = 0;
				if (lastInput) {
					var lastInputName = lastInput.name;
					lastInputNumber = parseInt(lastInputName.replace('total', '')) || 0;
				}

				var newInputName = number + 'total';

				var newInput = document.createElement('input');
				newInput.type = 'text';
				newInput.className = 'tabletotaltext';
				newInput.name = newInputName;
				newInput.style.width = '40px';

				var lineBreak = document.createElement('br');

				if (lastInput) {
					lastInput.parentNode.insertBefore(newInput, lastInput.nextSibling);
					lastInput.parentNode.insertBefore(lineBreak, lastInput.nextSibling);
				} else {
					console.log("somthing want's wrong");
				}
			}

			function adjustTopValue(change, gap) {
				const ids = ['frontDiv', 'stageDiv', 'departmentDiv', 'artCommentDiv', 'imageDiv'];
				ids.forEach(id => {
					const element = document.getElementById(id);
					if (element) {
						const currentTop = parseInt(element.style.top) || 0;
						element.style.top = (currentTop + change + gap) + 'px';
					}
				});
			}

			function removeRow(button) {
				const row = button.closest('tr');
				const rowNumber = row.querySelector('input').name.replace(/\D/g, '');
				var ordercount = localStorage.getItem('orderItems');
				localStorage.setItem('orderItems', ordercount - 1);
				row.remove();

				removeTotalRow(rowNumber);
				adjustTopValue(-20, -3);
				maketotals();
			}

			function removeTotalRow(rowNumber) {
				const totalInput = document.querySelector(`input[name="${rowNumber}total"]`);
				if (totalInput) {
					const brElement = totalInput.nextElementSibling;
					totalInput.remove();
					if (brElement && brElement.tagName.toLowerCase() === 'br') {
						brElement.remove();
					}
				}
			}
		</script>

</body>
<!-- InstanceEnd -->

</html>