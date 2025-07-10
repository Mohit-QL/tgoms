<?php
session_start();
function addSmartQuotes($string)
{
	if (is_null($string) || $string === '') {
		return $string;
	}

	$replace = array(chr(146), chr(148));
	$search = array("'", '"');
	return str_replace($search, $replace, $string);
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

	if (count($tempDate) == 3) {
		$month = $tempDate[0] + 0;
		$daynum = $tempDate[1] + 0;
		$year = $tempDate[2] + 0;
	} else
		$err = true;

	$month   = (($month   <  10) ? '0'  . $month   : $month);
	$daynum  = (($daynum  <  10) ? '0'  . $daynum   : $daynum);

	if (! $err)
		return $year . '-' . $month  . '-' . $daynum;
	else
		return $strDate;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<?php
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$stylesheet = "printStyles.css";

	if (preg_match("/Firefox/i", $agent) && !preg_match("/safari/i", $agent)) {
		$stylesheet = "printStylesMozilla.css";
	} else if (preg_match("/mozilla/i", $agent) && !preg_match("/safari/i", $agent)) {
		$stylesheet = "printStylesMozilla121.css";
	}
	?>
	<link rel="stylesheet" href="<?php echo $stylesheet; ?>" type="text/css" />
	<script type="text/javascript" src="oms.js"></script>
	<title>OMS - Print</title>
</head>

<body>
	<div id="status">
	</div>
	<?php
	include('database.php');

	if (isset($_REQUEST['ids']) && !empty($_REQUEST['ids'])) {
		$ids = explode(',', implode(',', unserialize(urldecode($_REQUEST['ids']))));
		for ($k = 0; $k < sizeof($ids); $k++) {

			unset($orderID, $clientID, $repID, $artistID, $departmentID, $type, $category, $projectName, $shipBlind, $shippingClient, $shippingContact, $shippingAddress, $shippingCity, $shippingState, $shippingZip, $front, $back, $sleeve, $other, $frontDetails, $backDetails, $sleeveDetails, $otherDetails, $orderDate, $artDueDate, $printDate, $dueDate, $specialInstructions, $productionNotes, $artInstructions, $comments, $redAlert, $artType, $artFilename, $artistInProgress, $artistComplete, $artistSentToApprove, $artistRevisions, $artistApproved, $artistSepsDone, $artistProofFilm, $artistHours, $stageOrderGoods, $stageGoodsReceived, $stageOrderStaging, $stagePrintingStitching, $stageComplete, $stageBilled, $stagePaid, $salesTax, $screenNumber, $screenCharge, $dieCharge, $artNumber, $artCharge, $colorCharge, $shippingCharge, $miscCharge, $miscDescription, $deposit, $name, $contact, $address, $city, $state, $zip, $phone, $email, $artistName, $repName, $departmentName, $description, $color, $vendor, $yxs, $ys, $ym, $yl, $yxl, $s, $m, $l, $xl, $xxl, $xxxl, $xxxxl, $misc, $price);

			$pageBreak = "always";
			if ($k + 1 == sizeof($ids))
				$pageBreak = "avoid";

			$selectOrderSql = "SELECT * FROM orders WHERE id='" . $ids[$k] . "'";
			$selectOrderQuery = mysqli_query($conn, $selectOrderSql) or die(mysqli_error($conn));

			$entry = mysqli_fetch_array($selectOrderQuery);

			if (!empty($entry)) {

				$orderID = $ids[$k];
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
				$shippingZip = $entry['shippingZip'];
				$front = $entry['front'];
				$back = $entry['back'];
				$sleeve = $entry['sleeve'];
				$other = addSmartQuotes($entry['other']);
				$frontDetails = addSmartQuotes($entry['frontDetails']);
				$backDetails = addSmartQuotes($entry['backDetails']);
				$sleeveDetails = addSmartQuotes($entry['sleeveDetails']);
				$otherDetails = addSmartQuotes($entry['otherDetails']);
				$orderDate = !empty($entry['orderDate']) ? date("m/d/Y", strtotime($entry['orderDate'])) : null;
				$artDueDate = !empty($entry['artDueDate']) ? date("m/d/Y", strtotime($entry['artDueDate'])) : null;
				$printDate = !empty($entry['printDate']) ? date("m/d/Y", strtotime($entry['printDate'])) : null;
				$dueDate = !empty($entry['dueDate']) ? date("m/d/Y", strtotime($entry['dueDate'])) : null;
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

				/* Grab the client info using the client ID */
				$selectClientSql = "SELECT * FROM clients WHERE id='" . $clientID . "'";
				$selectClientQuery = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
				$entry = mysqli_fetch_array($selectClientQuery);
				$name = addSmartQuotes($entry['name']);
				$contact = addSmartQuotes($entry['contact']);
				$address = addSmartQuotes($entry['address']);
				$city = addSmartQuotes($entry['city']);
				$state = $entry['state'];
				$zip = $entry['zip'];
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

				/* Get the rep's name from the rep ID */
				$selectUserSql = "SELECT name FROM users WHERE id='" . $departmentID . "'";
				$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
				$entry = mysqli_fetch_array($selectUserQuery);
				$departmentName = $entry[0];

				/* Get any order items associated with this order */
				$j = 1;
				$selectOrderItemSql = "SELECT * FROM orderItems WHERE visible = 1 AND orderID='" . $orderID . "' ORDER BY id ASC";
				$selectOrderItemQuery = mysqli_query($conn, $selectOrderItemSql) or die(mysqli_error($conn));
				$totalRows = mysqli_num_rows($selectOrderItemQuery);
				$subtotal = 0;
				while ($entry = mysqli_fetch_array($selectOrderItemQuery)) {
					$sum = 0;
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

					$total[$j] = $quantity[$j] * $price[$j];

					if(empty($entry['quantity']) && $entry['quantity'] == ''){
						$sum = $entry['yxs'] + $entry['ys'] + $entry['ym'] + $entry['yl'] + $entry['yxl'] + $entry['s'] + $entry['m'] + $entry['l'] + $entry['xl'] + $entry['xxl'] + $entry['xxxl'] + $entry['xxxxl'] + $entry['misc'];
						$quantity[$j] = $sum;
						$total[$j] = $sum * $price[$j];
					}

					$subtotal += $total[$j];

					$j++;
				}

				$salesTaxTotal = 0;
				if($salesTax > 0 ){
					$salesTaxTotal = $subtotal * 0.06;
				}
				$screenTotal = 0;
				if($screenNumber){
					$screenTotal = $screenNumber * $screenCharge;
				}
				$artTotal = 0;
				if($artNumber){
					$artTotal = $artNumber * $artCharge;
				}
				$finalTotal = 0;
				$finalTotal = $subtotal + $salesTaxTotal + $screenTotal + $artTotal + $colorCharge + $dieCharge + $shippingCharge + $miscCharge;
				$balance = 0;
				$balance = $finalTotal - $deposit;
	?>
				<div style="page-break-after:<?php echo $pageBreak; ?>; position:relative; height:1000px">
					<form name="mainform<?php echo $k; ?>" id="mainform<?php echo $k; ?>" method="post" enctype="multipart/form-data">

						<div style="width:950px;">

							<div style="float:left;width:33%">
								<div style="width:250px; text-align:left">
									<b>Bill to:</b><br />
									<?php echo $name . "<br />" . $contact . "<br />" . $projectName . "<br />" . $address . "<br />" . $city . ", " . $state . " " . $zip . "<br />" . $phone . "<br />" . $email . "<br />(Order Number: " . $orderID . ")"; ?>
								</div>
							</div>

							<div style="float:left; width:34%; text-align:center;">
								<div style="width:220px; text-align:left">
									<b>Ship to:</b><br />
									<?php echo $shippingClient . "<br />" . $shippingContact . "<br />" . $shippingAddress . "<br />" . $shippingCity . ", " . $shippingState . " " . $shippingZip; ?>
								</div>
							</div>

							<div style="float:left;width:33%; text-align:right">
								<label>Type: <input value="<?php echo $type; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Category: <input value="<?php echo $category; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Rep: <input value="<?php echo $repName; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Artist: <input value="<?php echo $artistName; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Department: <input value="<?php echo $departmentName; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Order date: <input value="<?php echo $orderDate; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Art due: <input value="<?php echo $artDueDate; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Scheduled to print: <input value="<?php echo $printDate; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
								<label>Due date: <input value="<?php echo $dueDate; ?>" style="width:100px; border:none; font-size:12px" /></label><br />
							</div>

						</div>
						<br />
						<div style="width:950px; text-align:center">
							<textarea name='specialInstructions' style="width:32%; height:70px;"><?php echo $specialInstructions ?></textarea> <textarea name='productionNotes' style="width:32%; height:70px;"><?php echo $productionNotes ?></textarea> <textarea name="redAlert" style=" width:32%; height:70px;"><?php echo $redAlert; ?></textarea>
						</div>

						<table border="0" cellpadding="0" cellspacing="0" style="float:left; width:70%; z-index:50;">
							<tr>
								<td align="center" style="width: 55px;">Quantity</td>
								<td align="center" style="width: 150px;">Style/Description</td>
								<td align="center" style="width: 55px;">Color</td>
								<td align="center" style="width: 55px;">Vendor</td>
								<td align="center" style="width: 30px;">YXS</td>
								<td align="center" style="width: 30px;">YS</td>
								<td align="center" style="width: 30px;">YM</td>
								<td align="center" style="width: 30px;">YL</td>

								<td align="center" style="width: 30px;">YXL</td>
								<td align="center" style="width: 30px;">S</td>
								<td align="center" style="width: 30px;">M</td>
								<td align="center" style="width: 30px;">L</td>
								<td align="center" style="width: 30px;">XL</td>
								<td align="center" style="width: 30px;">XXL</td>
								<td align="center" style="width: 40px;">XXXL</td>
								<td align="center" style="width: 40px;">XXXXL</td>
								<td align="center" style="width: 40px;">Misc</td>

								<td align="center" style="width: 40px;">Price</td>
								<td align="center" style="width: 40px;">Total</td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="1quantity" style="width: 55px;" value="<?php echo isset($quantity[1]) ? $quantity[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1description" style="width: 145px;" value="<?php echo isset($description[1]) ? $description[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1color" style="width: 65px;" value="<?php echo isset($color[1]) ? $color[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1vendor" style="width: 65px;" value="<?php echo isset($vendor[1]) ? $vendor[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1yxs" style="width: 25px;" value="<?php echo isset($yxs[1]) ? $yxs[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1ys" style="width: 25px;" value="<?php echo isset($ys[1]) ? $ys[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1ym" style="width: 25px;" value="<?php echo isset($ym[1]) ? $ym[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1yl" style="width: 25px;" value="<?php echo isset($yl[1]) ? $yl[1] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="1yxl" style="width: 25px;" value="<?php echo isset($yxl[1]) ? $yxl[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1s" style="width: 25px;" value="<?php echo isset($s[1]) ? $s[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1m" style="width: 25px;" value="<?php echo isset($m[1]) ? $m[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1l" style="width: 25px;" value="<?php echo isset($l[1]) ? $l[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1xl" style="width: 25px;" value="<?php echo isset($xl[1]) ? $xl[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1xxl" style="width: 25px;" value="<?php echo isset($xxl[1]) ? $xxl[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1xxxl" style="width: 35px;" value="<?php echo isset($xxxl[1]) ? $xxxl[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[1]) ? $xxxxl[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1misc" style="width: 35px;" value="<?php echo isset($misc[1]) ? $misc[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="1price" style="width: 35px;" value="<?php echo isset($price[1]) ? $price[1] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="1total" style="width: 40px;" value="<?php echo isset($total[1]) ? $total[1] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="2quantity" style="width: 55px;" value="<?php echo isset($quantity[2]) ? $quantity[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2description" style="width: 145px;" value="<?php echo isset($description[2]) ? $description[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2color" style="width: 65px;" value="<?php echo isset($color[2]) ? $color[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2vendor" style="width: 65px;" value="<?php echo isset($vendor[2]) ? $vendor[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2yxs" style="width: 25px;" value="<?php echo isset($yxs[2]) ? $yxs[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2ys" style="width: 25px;" value="<?php echo isset($ys[2]) ? $ys[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2ym" style="width: 25px;" value="<?php echo isset($ym[2]) ? $ym[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2yl" style="width: 25px;" value="<?php echo isset($yl[2]) ? $yl[2] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="2yxl" style="width: 25px;" value="<?php echo isset($yxl[2]) ? $yxl[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2s" style="width: 25px;" value="<?php echo isset($s[2]) ? $s[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2m" style="width: 25px;" value="<?php echo isset($m[2]) ? $m[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2l" style="width: 25px;" value="<?php echo isset($l[2]) ? $l[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2xl" style="width: 25px;" value="<?php echo isset($xl[2]) ? $xl[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2xxl" style="width: 25px;" value="<?php echo isset($xxl[2]) ? $xxl[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2xxxl" style="width: 35px;" value="<?php echo isset($xxxl[2]) ? $xxxl[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[2]) ? $xxxxl[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2misc" style="width: 35px;" value="<?php echo isset($misc[2]) ? $misc[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="2price" style="width: 35px;" value="<?php echo isset($price[2]) ? $price[2] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="2total" style="width: 40px;" value="<?php echo isset($total[2]) ? $total[2] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="3quantity" style="width: 55px;" value="<?php echo isset($quantity[3]) ? $quantity[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3description" style="width: 145px;" value="<?php echo isset($description[3]) ? $description[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3color" style="width: 65px;" value="<?php echo isset($color[3]) ? $color[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3vendor" style="width: 65px;" value="<?php echo isset($vendor[3]) ? $vendor[3] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="3yxs" style="width: 25px;" value="<?php echo isset($yxs[3]) ? $yxs[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3ys" style="width: 25px;" value="<?php echo isset($ys[3]) ? $ys[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3ym" style="width: 25px;" value="<?php echo isset($ym[3]) ? $ym[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3yl" style="width: 25px;" value="<?php echo isset($yl[3]) ? $yl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3yxl" style="width: 25px;" value="<?php echo isset($yxl[3]) ? $yxl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3s" style="width: 25px;" value="<?php echo isset($s[3]) ? $s[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3m" style="width: 25px;" value="<?php echo isset($m[3]) ? $m[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3l" style="width: 25px;" value="<?php echo isset($l[3]) ? $l[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3xl" style="width: 25px;" value="<?php echo isset($xl[3]) ? $xl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3xxl" style="width: 25px;" value="<?php echo isset($xxl[3]) ? $xxl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3xxxl" style="width: 35px;" value="<?php echo isset($xxxl[3]) ? $xxxl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[3]) ? $xxxxl[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3misc" style="width: 35px;" value="<?php echo isset($misc[3]) ? $misc[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="3price" style="width: 35px;" value="<?php echo isset($price[3]) ? $price[3] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="3total" style="width: 40px;" value="<?php echo isset($total[3]) ? $total[3] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="4quantity" style="width: 55px;" value="<?php echo isset($quantity[4]) ? $quantity[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4description" style="width: 145px;" value="<?php echo isset($description[4]) ? $description[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4color" style="width: 65px;" value="<?php echo isset($color[4]) ? $color[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4vendor" style="width: 65px;" value="<?php echo isset($vendor[4]) ? $vendor[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4yxs" style="width: 25px;" value="<?php echo isset($yxs[4]) ? $yxs[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4ys" style="width: 25px;" value="<?php echo isset($ys[4]) ? $ys[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4ym" style="width: 25px;" value="<?php echo isset($ym[4]) ? $ym[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4yl" style="width: 25px;" value="<?php echo isset($yl[4]) ? $yl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4yxl" style="width: 25px;" value="<?php echo isset($yxl[4]) ? $yxl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4s" style="width: 25px;" value="<?php echo isset($s[4]) ? $s[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4m" style="width: 25px;" value="<?php echo isset($m[4]) ? $m[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4l" style="width: 25px;" value="<?php echo isset($l[4]) ? $l[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4xl" style="width: 25px;" value="<?php echo isset($xl[4]) ? $xl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4xxl" style="width: 25px;" value="<?php echo isset($xxl[4]) ? $xxl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4xxxl" style="width: 35px;" value="<?php echo isset($xxxl[4]) ? $xxxl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[4]) ? $xxxxl[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4misc" style="width: 35px;" value="<?php echo isset($misc[4]) ? $misc[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="4price" style="width: 35px;" value="<?php echo isset($price[4]) ? $price[4] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="4total" style="width: 40px;" value="<?php echo isset($total[4]) ? $total[4] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="5quantity" style="width: 55px;" value="<?php echo isset($quantity[5]) ? $quantity[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5description" style="width: 145px;" value="<?php echo isset($description[5]) ? $description[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5color" style="width: 65px;" value="<?php echo isset($color[5]) ? $color[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5vendor" style="width: 65px;" value="<?php echo isset($vendor[5]) ? $vendor[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5yxs" style="width: 25px;" value="<?php echo isset($yxs[5]) ? $yxs[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5ys" style="width: 25px;" value="<?php echo isset($ys[5]) ? $ys[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5ym" style="width: 25px;" value="<?php echo isset($ym[5]) ? $ym[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5yl" style="width: 25px;" value="<?php echo isset($yl[5]) ? $yl[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5yxl" style="width: 25px;" value="<?php echo isset($yxl[5]) ? $yxl[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5s" style="width: 25px;" value="<?php echo isset($s[5]) ? $s[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5m" style="width: 25px;" value="<?php echo isset($m[5]) ? $m[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5l" style="width: 25px;" value="<?php echo isset($l[5]) ? $l[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5xl" style="width: 25px;" value="<?php echo isset($xl[5]) ? $xl[5] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="5xxl" style="width: 25px;" value="<?php echo isset($xxl[5]) ? $xxl[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5xxxl" style="width: 35px;" value="<?php echo isset($xxxl[5]) ? $xxxl[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[5]) ? $xxxxl[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5misc" style="width: 35px;" value="<?php echo isset($misc[5]) ? $misc[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="5price" style="width: 35px;" value="<?php echo isset($price[5]) ? $price[5] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="5total" style="width: 40px;" value="<?php echo isset($total[5]) ? $total[5] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="6quantity" style="width: 55px;" value="<?php echo isset($quantity[6]) ? $quantity[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6description" style="width: 145px;" value="<?php echo isset($description[6]) ? $description[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6color" style="width: 65px;" value="<?php echo isset($color[6]) ? $color[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6vendor" style="width: 65px;" value="<?php echo isset($vendor[6]) ? $vendor[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6yxs" style="width: 25px;" value="<?php echo isset($yxs[6]) ? $yxs[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6ys" style="width: 25px;" value="<?php echo isset($ys[6]) ? $ys[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6ym" style="width: 25px;" value="<?php echo isset($ym[6]) ? $ym[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6yl" style="width: 25px;" value="<?php echo isset($yl[6]) ? $yl[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6yxl" style="width: 25px;" value="<?php echo isset($yxl[6]) ? $yxl[6] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="6s" style="width: 25px;" value="<?php echo isset($s[6]) ? $s[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6m" style="width: 25px;" value="<?php echo isset($m[6]) ? $m[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6l" style="width: 25px;" value="<?php echo isset($l[6]) ? $l[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6xl" style="width: 25px;" value="<?php echo isset($xl[6]) ? $xl[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6xxl" style="width: 25px;" value="<?php echo isset($xxl[6]) ? $xxl[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6xxxl" style="width: 35px;" value="<?php echo isset($xxxl[6]) ? $xxxl[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[6]) ? $xxxxl[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6misc" style="width: 35px;" value="<?php echo isset($misc[6]) ? $misc[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="6price" style="width: 35px;" value="<?php echo isset($price[6]) ? $price[6] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="6total" style="width: 40px;" value="<?php echo isset($total[6]) ? $total[6] : ''; ?>" /></td>

							</tr>
							<tr>


								<td align="center"><input class="tabletextprint" name="7quantity" style="width: 55px;" value="<?php echo isset($quantity[7]) ? $quantity[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7description" style="width: 145px;" value="<?php echo isset($description[7]) ? $description[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7color" style="width: 65px;" value="<?php echo isset($color[7]) ? $color[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7vendor" style="width: 65px;" value="<?php echo isset($vendor[7]) ? $vendor[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7yxs" style="width: 25px;" value="<?php echo isset($yxs[7]) ? $yxs[7] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="7ys" style="width: 25px;" value="<?php echo isset($ys[7]) ? $ys[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7ym" style="width: 25px;" value="<?php echo isset($ym[7]) ? $ym[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7yl" style="width: 25px;" value="<?php echo isset($yl[7]) ? $yl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7yxl" style="width: 25px;" value="<?php echo isset($yxl[7]) ? $yxl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7s" style="width: 25px;" value="<?php echo isset($s[7]) ? $s[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7m" style="width: 25px;" value="<?php echo isset($m[7]) ? $m[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7l" style="width: 25px;" value="<?php echo isset($l[7]) ? $l[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7xl" style="width: 25px;" value="<?php echo isset($xl[7]) ? $xl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7xxl" style="width: 25px;" value="<?php echo isset($xxl[7]) ? $xxl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7xxxl" style="width: 35px;" value="<?php echo isset($xxxl[7]) ? $xxxl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[7]) ? $xxxxl[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7misc" style="width: 35px;" value="<?php echo isset($misc[7]) ? $misc[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="7price" style="width: 35px;" value="<?php echo isset($price[7]) ? $price[7] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="7total" style="width: 40px;" value="<?php echo isset($total[7]) ? $total[7] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="8quantity" style="width: 55px;" value="<?php echo isset($quantity[8]) ? $quantity[8] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="8description" style="width: 145px;" value="<?php echo isset($description[8]) ? $description[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8color" style="width: 65px;" value="<?php echo isset($color[8]) ? $color[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8vendor" style="width: 65px;" value="<?php echo isset($vendor[8]) ? $vendor[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8yxs" style="width: 25px;" value="<?php echo isset($yxs[8]) ? $yxs[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8ys" style="width: 25px;" value="<?php echo isset($ys[8]) ? $ys[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8ym" style="width: 25px;" value="<?php echo isset($ym[8]) ? $ym[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8yl" style="width: 25px;" value="<?php echo isset($yl[8]) ? $yl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8yxl" style="width: 25px;" value="<?php echo isset($yxl[8]) ? $yxl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8s" style="width: 25px;" value="<?php echo isset($s[8]) ? $s[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8m" style="width: 25px;" value="<?php echo isset($m[8]) ? $m[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8l" style="width: 25px;" value="<?php echo isset($l[8]) ? $l[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8xl" style="width: 25px;" value="<?php echo isset($xl[8]) ? $xl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8xxl" style="width: 25px;" value="<?php echo isset($xxl[8]) ? $xxl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8xxxl" style="width: 35px;" value="<?php echo isset($xxxl[8]) ? $xxxl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[8]) ? $xxxxl[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8misc" style="width: 35px;" value="<?php echo isset($misc[8]) ? $misc[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="8price" style="width: 35px;" value="<?php echo isset($price[8]) ? $price[8] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="8total" style="width: 40px;" value="<?php echo isset($total[8]) ? $total[8] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="9quantity" style="width: 55px;" value="<?php echo isset($quantity[9]) ? $quantity[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9description" style="width: 145px;" value="<?php echo isset($description[9]) ? $description[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9color" style="width: 65px;" value="<?php echo isset($color[9]) ? $color[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9vendor" style="width: 65px;" value="<?php echo isset($vendor[9]) ? $vendor[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9yxs" style="width: 25px;" value="<?php echo isset($yxs[9]) ? $yxs[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9ys" style="width: 25px;" value="<?php echo isset($ys[9]) ? $ys[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9ym" style="width: 25px;" value="<?php echo isset($ym[9]) ? $ym[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9yl" style="width: 25px;" value="<?php echo isset($yl[9]) ? $yl[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9yxl" style="width: 25px;" value="<?php echo isset($yxl[9]) ? $yxl[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9s" style="width: 25px;" value="<?php echo isset($s[9]) ? $s[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9m" style="width: 25px;" value="<?php echo isset($m[9]) ? $m[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9l" style="width: 25px;" value="<?php echo isset($l[9]) ? $l[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9xl" style="width: 25px;" value="<?php echo isset($xl[9]) ? $xl[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9xxl" style="width: 25px;" value="<?php echo isset($xxl[9]) ? $xxl[9] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="9xxxl" style="width: 35px;" value="<?php echo isset($xxxl[9]) ? $xxxl[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[9]) ? $xxxxl[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9misc" style="width: 35px;" value="<?php echo isset($misc[9]) ? $misc[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="9price" style="width: 35px;" value="<?php echo isset($price[9]) ? $price[9] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="9total" style="width: 40px;" value="<?php echo isset($total[9]) ? $total[9] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="10quantity" style="width: 55px;" value="<?php echo isset($quantity[10]) ? $quantity[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10description" style="width: 145px;" value="<?php echo isset($description[10]) ? $description[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10color" style="width: 65px;" value="<?php echo isset($color[10]) ? $color[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10vendor" style="width: 65px;" value="<?php echo isset($vendor[10]) ? $vendor[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10yxs" style="width: 25px;" value="<?php echo isset($yxs[10]) ? $yxs[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10ys" style="width: 25px;" value="<?php echo isset($ys[10]) ? $ys[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10ym" style="width: 25px;" value="<?php echo isset($ym[10]) ? $ym[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10yl" style="width: 25px;" value="<?php echo isset($yl[10]) ? $yl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10yxl" style="width: 25px;" value="<?php echo isset($yxl[10]) ? $yxl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10s" style="width: 25px;" value="<?php echo isset($s[10]) ? $s[10] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="10m" style="width: 25px;" value="<?php echo isset($m[10]) ? $m[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10l" style="width: 25px;" value="<?php echo isset($l[10]) ? $l[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10xl" style="width: 25px;" value="<?php echo isset($xl[10]) ? $xl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10xxl" style="width: 25px;" value="<?php echo isset($xxl[10]) ? $xxl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10xxxl" style="width: 35px;" value="<?php echo isset($xxxl[10]) ? $xxxl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[10]) ? $xxxxl[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10misc" style="width: 35px;" value="<?php echo isset($misc[10]) ? $misc[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="10price" style="width: 35px;" value="<?php echo isset($price[10]) ? $price[10] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="10total" style="width: 40px;" value="<?php echo isset($total[10]) ? $total[10] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="11quantity" style="width: 55px;" value="<?php echo isset($quantity[11]) ? $quantity[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11description" style="width: 145px;" value="<?php echo isset($description[11]) ? $description[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11color" style="width: 65px;" value="<?php echo isset($color[11]) ? $color[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11vendor" style="width: 65px;" value="<?php echo isset($vendor[11]) ? $vendor[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11yxs" style="width: 25px;" value="<?php echo isset($yxs[11]) ? $yxs[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11ys" style="width: 25px;" value="<?php echo isset($ys[11]) ? $ys[11] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="11ym" style="width: 25px;" value="<?php echo isset($ym[11]) ? $ym[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11yl" style="width: 25px;" value="<?php echo isset($yl[11]) ? $yl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11yxl" style="width: 25px;" value="<?php echo isset($yxl[11]) ? $yxl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11s" style="width: 25px;" value="<?php echo isset($s[11]) ? $s[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11m" style="width: 25px;" value="<?php echo isset($m[11]) ? $m[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11l" style="width: 25px;" value="<?php echo isset($l[11]) ? $l[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11xl" style="width: 25px;" value="<?php echo isset($xl[11]) ? $xl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11xxl" style="width: 25px;" value="<?php echo isset($xxl[11]) ? $xxl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11xxxl" style="width: 35px;" value="<?php echo isset($xxxl[11]) ? $xxxl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[11]) ? $xxxxl[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11misc" style="width: 35px;" value="<?php echo isset($misc[11]) ? $misc[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="11price" style="width: 35px;" value="<?php echo isset($price[11]) ? $price[11] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="11total" style="width: 40px;" value="<?php echo isset($total[11]) ? $total[11] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="12quantity" style="width: 55px;" value="<?php echo isset($quantity[12]) ? $quantity[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12description" style="width: 145px;" value="<?php echo isset($description[12]) ? $description[12] : ''; ?>" /></td>

								<td align="center"><input class="tabletextprint" name="12color" style="width: 65px;" value="<?php echo isset($color[12]) ? $color[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12vendor" style="width: 65px;" value="<?php echo isset($vendor[12]) ? $vendor[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12yxs" style="width: 25px;" value="<?php echo isset($yxs[12]) ? $yxs[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12ys" style="width: 25px;" value="<?php echo isset($ys[12]) ? $ys[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12ym" style="width: 25px;" value="<?php echo isset($ym[12]) ? $ym[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12yl" style="width: 25px;" value="<?php echo isset($yl[12]) ? $yl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12yxl" style="width: 25px;" value="<?php echo isset($yxl[12]) ? $yxl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12s" style="width: 25px;" value="<?php echo isset($s[12]) ? $s[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12m" style="width: 25px;" value="<?php echo isset($m[12]) ? $m[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12l" style="width: 25px;" value="<?php echo isset($l[12]) ? $l[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12xl" style="width: 25px;" value="<?php echo isset($xl[12]) ? $xl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12xxl" style="width: 25px;" value="<?php echo isset($xxl[12]) ? $xxl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12xxxl" style="width: 35px;" value="<?php echo isset($xxxl[12]) ? $xxxl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[12]) ? $xxxxl[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12misc" style="width: 35px;" value="<?php echo isset($misc[12]) ? $misc[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="12price" style="width: 35px;" value="<?php echo isset($price[12]) ? $price[12] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="12total" style="width: 40px;" value="<?php echo isset($total[12]) ? $total[12] : ''; ?>" /></td>

							</tr>
							<tr>

								<td align="center"><input class="tabletextprint" name="13quantity" style="width: 55px;" value="<?php echo isset($quantity[13]) ? $quantity[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13description" style="width: 145px;" value="<?php echo isset($description[13]) ? $description[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13color" style="width: 65px;" value="<?php echo isset($color[13]) ? $color[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13vendor" style="width: 65px;" value="<?php echo isset($vendor[13]) ? $vendor[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13yxs" style="width: 25px;" value="<?php echo isset($yxs[13]) ? $yxs[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13ys" style="width: 25px;" value="<?php echo isset($ys[13]) ? $ys[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13ym" style="width: 25px;" value="<?php echo isset($ym[13]) ? $ym[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13yl" style="width: 25px;" value="<?php echo isset($yl[13]) ? $yl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13yxl" style="width: 25px;" value="<?php echo isset($yxl[13]) ? $yxl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13s" style="width: 25px;" value="<?php echo isset($s[13]) ? $s[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13m" style="width: 25px;" value="<?php echo isset($m[13]) ? $m[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13l" style="width: 25px;" value="<?php echo isset($l[13]) ? $l[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13xl" style="width: 25px;" value="<?php echo isset($xl[13]) ? $xl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13xxl" style="width: 25px;" value="<?php echo isset($xxl[13]) ? $xxl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13xxxl" style="width: 35px;" value="<?php echo isset($xxxl[13]) ? $xxxl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[13]) ? $xxxxl[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13misc" style="width: 35px;" value="<?php echo isset($misc[13]) ? $misc[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletextprint" name="13price" style="width: 35px;" value="<?php echo isset($price[13]) ? $price[13] : ''; ?>" /></td>
								<td align="center"><input class="tabletotaltext2" name="13total" style="width: 40px;" value="<?php echo isset($total[13]) ? $total[13] : ''; ?>" /></td>

							</tr>

							<?php if ($totalRows > 13) {
								for ($k = 14; $k < $totalRows + 1; $k++) { ?>
									<tr>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>quantity" style="width: 55px;" value="<?php echo isset($quantity[$k]) ? $quantity[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>description" style="width: 145px;" value="<?php echo isset($description[$k]) ? $description[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>color" style="width: 65px;" value="<?php echo isset($color[$k]) ? $color[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>vendor" style="width: 65px;" value="<?php echo isset($vendor[$k]) ? $vendor[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>yxs" style="width: 25px;" value="<?php echo isset($yxs[$k]) ? $yxs[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>ys" style="width: 25px;" value="<?php echo isset($ys[$k]) ? $ys[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>ym" style="width: 25px;" value="<?php echo isset($ym[$k]) ? $ym[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>yl" style="width: 25px;" value="<?php echo isset($yl[$k]) ? $yl[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>yxl" style="width: 25px;" value="<?php echo isset($yxl[$k]) ? $yxl[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>s" style="width: 25px;" value="<?php echo isset($s[$k]) ? $s[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>m" style="width: 25px;" value="<?php echo isset($m[$k]) ? $m[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>l" style="width: 25px;" value="<?php echo isset($l[$k]) ? $l[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>xl" style="width: 25px;" value="<?php echo isset($xl[$k]) ? $xl[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>xxl" style="width: 25px;" value="<?php echo isset($xxl[$k]) ? $xxl[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>xxxl" style="width: 35px;" value="<?php echo isset($xxxl[$k]) ? $xxxl[$k] : ''; ?>" onchange="maketotals();"></td>

										<td align="center"><input class="tabletextprint" name="<?= $k ?>xxxxl" style="width: 35px;" value="<?php echo isset($xxxxl[$k]) ? $xxxxl[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>misc" style="width: 35px;" value="<?php echo isset($misc[$k]) ? $misc[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletextprint" name="<?= $k ?>price" style="width: 35px;" value="<?php echo isset($price[$k]) ? $price[$k] : ''; ?>" onchange="maketotals();"></td>
										<td align="center"><input class="tabletotaltext2" name="<?= $k ?>total" style="width: 40px;" value="<?php echo isset($total[$k]) ? $total[$k] : ''; ?>" /></td>
									</tr>
							<?php }
							} ?>

						</table>

						<div style="width:950px;">

							<div style="float:right; width:210px; text-align:right">
								<label>Sub-total <input class='tabletotaltext' name="subtotal" style="width: 40px;" value="<?php echo $subtotal; ?>" /></label><br />
								<label>Sales Tax</label> <input class='tabletotaltext' name="salesTax" style="width: 40px;" value="<?php echo $salesTaxTotal; ?>" /><br />
								Screens: <input class='tabletotaltext' name="screenNumber" style="width: 20px;" value="<?php echo $screenNumber; ?>" /> x $<input class='tabletotaltext' name="screenCharge" style="width: 30px;" value="<?php echo $screenCharge; ?>" />=<input class='tabletotaltext' name="screenTotal" style="width: 40px;" value="<?php echo $screenTotal; ?>" /><br />
								<label>Die Charge<input class='tabletotaltext' name="dieCharge" style="width: 40px;" value="<?php echo $dieCharge; ?>" /></label><br />
								Art: <input class='tabletotaltext' name="artNumber" style="width: 20px;" value="<?php echo $artNumber; ?>" /> x $<input class='tabletotaltext' name="artCharge" style="width: 30px;" value="<?php echo $artCharge; ?>" />=<input class='tabletotaltext' name="artTotal" style="width: 40px;" value="<?php echo $artTotal; ?>" /><br />
								<label>Color Charge <input class='tabletotaltext' name="colorCharge" style="width: 40px;" value="<?php echo $colorCharge; ?>" /></label><br />
								<label>Shipping <input class='tabletotaltext' name="shippingCharge" style="width: 40px;" value="<?php echo $shippingCharge; ?>" /></label><br />
								<input class='tabletotaltext' name="miscDescription" style="width: 60px; text-align: right;" value="<?php echo $miscDescription; ?>" /> <input class='tabletotaltext' name="miscCharge" style="width: 40px;" value="<?php echo $miscCharge; ?>" /><br />
								<label>Total <input class='tabletotaltext' name="total" style="width: 40px;" value="<?php echo $finalTotal; ?>" /></label><br />
								<label>Deposit <input class='tabletotaltext' name="deposit" style="width: 40px;" value="<?php echo $deposit; ?>" /></label><br />
								<label>Balance <input class='tabletotaltext' name="balance" style="width: 40px;" value="<?php echo $balance; ?>" /></label><br /><br /><br /><br /><br />
							</div>

							<fieldset style="float:right; width:160px">
								<legend>Stage</legend>
								<label>Order Goods<input class="initialButton" onclick="initialButton('stageOrderGoods<?php echo "," . $_SESSION['initials'] ?>')" name="stageOrderGoods" value="<?php echo $stageOrderGoods; ?>" /></label><br />
								<label>Goods Received<input class="initialButton" onclick="initialButton('stageGoodsReceived<?php echo "," . $_SESSION['initials'] ?>')" name="stageGoodsReceived" value="<?php echo $stageGoodsReceived; ?>" /></label><br />
								<label>Art Approval<input class="initialButton" onclick="initialButton('artistApproved<?php echo "," . $_SESSION['initials'] ?>')" name="artistApproved" value="<?php echo $artistApproved; ?>" /></label><br />
								<label>Order Staging<input class="initialButton" onclick="initialButton('stageOrderStaging<?php echo "," . $_SESSION['initials'] ?>')" name="stageOrderStaging" value="<?php echo $stageOrderStaging; ?>" /></label><br />
								<label>Printing/Stitching<input class="initialButton" onclick="initialButton('stagePrintingStitching<?php echo "," . $_SESSION['initials'] ?>')" name="stagePrintingStitching" value="<?php echo $stagePrintingStitching; ?>" /></label><br />
								<label>Complete<input class="initialButton" onclick="initialButton('stageComplete<?php echo "," . $_SESSION['initials'] ?>')" name="stageComplete" value="<?php echo $stageComplete; ?>" /></label><br />
								<label>Billed<input class="initialButton" onclick="initialButton('stageBilled<?php echo "," . $_SESSION['initials'] ?>')" name="stageBilled" value="<?php echo $stageBilled; ?>" /></label><br />
								<label>Paid<input class="initialButton" onclick="initialButton('stagePaid<?php echo "," . $_SESSION['initials'] ?>')" name="stagePaid" value="<?php echo $stagePaid; ?>" /></label><br />
							</fieldset>

							<div style="width:540px; float:left">

								<textarea name="artInstructions" style="width: 540px; height: 70px; margin-top:5px"><?php echo $artInstructions ?></textarea>

								<div style="width:540px; height:54px; margin-bottom:4px">
									<div class="initialDiv" style="margin-right:3px;">In Progress<input class="initialButton" name="artistInProgress" style="width: 50px; margin:1px 0px;" value="<?php echo $artistInProgress ?>" onclick="initialButton('artistInProgress<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Complete<input class="initialButton" name="artistComplete" style="width: 50px; margin:1px 0px;" value="<?php echo $artistComplete ?>" onclick="initialButton('artistComplete<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Sent/Client<input class="initialButton" name="artistSentToApprove" style="width: 50px; margin:1px 0px;" value="<?php echo $artistSentToApprove ?>" onclick="initialButton('artistSentToApprove<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Revisions<input class="initialButton" name="artistRevisions" style="width: 50px; margin:1px 0px;" value="<?php echo $artistRevisions ?>" onclick="initialButton('artistRevisions<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Approved<input class="initialButton" name="artistApproved2" style="width: 50px; margin:1px 0px;" value="<?php echo $artistApproved ?>" onclick="initialButton('artistApproved<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Seps Done<input class="initialButton" name="artistSepsDone" style="width: 50px; margin:1px 0px;" value="<?php echo $artistSepsDone ?>" onclick="initialButton('artistSepsDone<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin:0px -3px;">Proof Film<input class="initialButton" name="artistProofFilm" style="width: 50px; margin:1px 0px;" value="<?php echo $artistProofFilm ?>" onclick="initialButton('artistProofFilm<?php echo "," . $_SESSION['initials'] ?>')" /></div>
									<div class="initialDiv" style="margin-left:1px">Hours<input class="text" name="artistHours" style="width: 50px; height:18px; text-align:center; margin:1px 0px;" value="<?php echo $artistHours ?>" /></div>
								</div>

								<textarea name="comments" style="width: 540px; height: 40px;"><?php echo $comments ?></textarea>

								<div style="text-align:right;float:right">
									<label>Front &nbsp;<input class="text" name="front" style="width: 100px;" value="<?php echo $front; ?>" /></label>
									&nbsp;&nbsp;&nbsp;<input class="text" name="frontDetails" style="width: 240px;" value="<?php echo $frontDetails; ?>" /><br />

									<label>Back &nbsp;<input class="text" name="back" style="width: 100px;" value="<?php echo $back; ?>" /></label>
									&nbsp;&nbsp;&nbsp;<input class="text" name="backDetails" style="width: 240px;" value="<?php echo $backDetails; ?>" /><br />

									<label>Sleeve &nbsp;<input class="text" name="sleeve" style="width: 100px;" value="<?php echo $sleeve; ?>" /></label>
									&nbsp;&nbsp;&nbsp;<input class="text" name="sleeveDetails" style="width: 240px;" value="<?php echo $sleeveDetails; ?>" /><br />

									<label>Other &nbsp;<input class="text" name="other" style="width: 100px;" value="<?php echo $other; ?>" /></label>
									&nbsp;&nbsp;&nbsp;<input class="text" name="otherDetails" style="width: 240px" value="<?php echo $otherDetails; ?>" /><br /><br /><input type='hidden' name='salestaxradio' />
								</div>
								<?php
								if ($artFilename != "") { ?>
									<div style="float:left; width:102px; height:102px;">
										<img src='<?php echo "files/" . $artFilename; ?>' width="102px" height="102px">
									</div>
								<?php } ?>
							</div>

						</div>
					</form>
					<?php echo '<script type="text/javascript"> maketotalswith("mainform' . $k . '");</script>'; ?>
				</div>
	<?php }
		}
	} ?>
	<script type="text/javascript">
		//var status = document.getElementById("status");
		//status.style.visibility = 'hidden';
		window.print();
		setTimeout("status.style.visibility = 'visible'", 20);
	</script>
</body>

</html>