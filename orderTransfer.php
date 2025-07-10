<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<body>
	<?PHP
	/* This file parses old order files into the new OMS order database */

	/* Connect to the database */
	include('database.php');
	/* Loop through the existing files */
	for ($i = 2941; $i < 2943; $i++) {
		if ($i < 1000)
			$filename = "orders/000" . $i . ".order";
		else
			$filename = "orders/00" . $i . ".order";

		if (file_exists($filename)) {
			$file_handle = fopen($filename, "r");
			echo $filename . ": ";

			/* Get client info first. */
			$name = getValue($file_handle);
			echo $name . "<br />";
			$contact = getValue($file_handle);
			$projectName = getValue($file_handle);
			$address = getValue($file_handle);
			$city = getValue($file_handle);
			$state = getValue($file_handle);
			$zip = getValue($file_handle);
			$phone = stripOneFromPhone(getValue($file_handle));
			$email = getValue($file_handle);

			/* If the client doesn't exist already (name, contact, and address are the same), create it. */
			$selectClientSql = "SELECT id FROM clients WHERE name = " . $name . " AND contact = " . $contact . " AND address = " . $address . " AND phone = " . $phone . " AND email = " . $email;
			$clientsMatching  = mysqli_query($conn, $selectClientSql) or die(mysqli_error($conn));
			if (mysqli_num_rows($clientsMatching) > 0) {
				$clientIDArray = mysqli_fetch_array($clientsMatching);
				$clientID = $clientIDArray[0];
			} else {
				$insertClientSql = "INSERT INTO clients (name, contact, address, city, state, zip, phone, email) VALUES(" . $name . "," . $contact . "," . $address . "," . $city . "," . $state . "," . $zip . "," . $phone . "," . $email . " ) ";
				mysqli_query($conn, $insertClientSql) or die(mysqli_error($conn));
				$clientID = "'" . mysqli_insert_id($conn) . "'";
			}

			/* If an art file exists for this order, record the filename */
			if (!file_exists("files/00" . $i . ".jpg"))
				$artFilename = "NULL";
			else
				$artFilename = "'00" . $i . ".jpg'";

			$shippingClient = getValue($file_handle);
			$shippingContact = getValue($file_handle);
			$trash = getValue($file_handle);
			$shippingAddress = getValue($file_handle);
			$shippingCity = getValue($file_handle);
			$shippingState = getValue($file_handle);
			$shippingZip = getValue($file_handle);
			$trash = getValue($file_handle);
			$trash = getValue($file_handle);
			$type = getValue($file_handle);
			$category = getValue($file_handle);

			/* Check the rep name to get ID. If the user no longer exists, create an inactive placeholder account. */
			$repName = getValue($file_handle);
			$selectUserSql = "SELECT id FROM users WHERE name = " . $repName;
			$checkRepID  = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
			if (mysqli_num_rows($checkRepID) > 0) {
				$repIDArray = mysqli_fetch_array($checkRepID);
				$repID = $repIDArray[0];
			} else {
				$insertUserSql = "INSERT INTO users (name, type, initials, password) VALUES(" . $repName . ",'Inactive','0000','" . md5('aasdfajkl') . "')";
				mysqli_query($conn, $insertUserSql) or die(mysqli_error($conn));
				$repID = mysqli_insert_id($conn);
			}

			$orderDate = sqlDate($file_handle);
			$artDueDate = sqlDate($file_handle);
			$printDate = sqlDate($file_handle);
			$dueDate = sqlDate($file_handle);
			$trash = getValue($file_handle);

			$specialInstructions = getValue($file_handle);
			$productionNotes = getValue($file_handle);

			$front = getValue($file_handle);
			$frontDetails = getValue($file_handle);
			$back = getValue($file_handle);
			$backDetails = getValue($file_handle);
			$sleeve = getValue($file_handle);
			$sleeveDetails = getValue($file_handle);
			$other = getValue($file_handle);
			$otherDetails = getValue($file_handle);

			$artType = getValue($file_handle);

			/* Check the artist name to get ID. If the user no longer exists, default to 'SalesDept' (id: 11). */
			$artName = getValue($file_handle);
			$selectUserSql = "SELECT id FROM users WHERE name = " . $artName;
			$checkArtID  = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
			if (mysqli_num_rows($checkArtID) > 0) {
				$artistIDArray = mysqli_fetch_array($checkArtID);
				$artistID = $artistIDArray[0];
			} else {
				$insertUserSql = "INSERT INTO users (name, type, initials, password) VALUES(" . $artName . ",'Inactive','0000','" . md5('aasdfajkl') . "')";
				mysqli_query($conn, $insertUserSql) or die(mysqli_error($conn));
				$artistID = mysqli_insert_id($conn);
			}

			$trash = getValue($file_handle);
			$artInstructions = getValue($file_handle);

			$artistInProgress = getValue($file_handle);
			$artistComplete = getValue($file_handle);
			$artistSentToApprove = getValue($file_handle);
			$artistRevisions = getValue($file_handle);
			$artistApproved = getValue($file_handle);
			$artistSepsDone = getValue($file_handle);
			$artistProofFilm = getValue($file_handle);
			$artistHours = getValue($file_handle);

			$comments = getValue($file_handle);
			$redAlert = getValue($file_handle);

			$sog = getValue($file_handle);
			$sgr = getValue($file_handle);
			$trash = getValue($file_handle);
			$sos = getValue($file_handle);
			$sps = getValue($file_handle);
			$sc = getValue($file_handle);
			$sb = getValue($file_handle);
			$sp = getValue($file_handle);

			$stageOrderGoods = getValue($file_handle);
			$stageGoodsReceived = getValue($file_handle);
			$trash = getValue($file_handle);
			$stageOrderStaging = getValue($file_handle);
			$stagePrintingStitching = getValue($file_handle);
			$stageComplete = getValue($file_handle);
			$stageBilled = getValue($file_handle);
			$stagePaid = getValue($file_handle);

			if ($sog != "NULL" && $stageOrderGoods == "NULL")
				$stageOrderGoods = "'SD'";
			if ($sgr != "NULL" && $stageGoodsReceived == "NULL")
				$stageGoodsReceived = "'SD'";
			if ($sos != "NULL" && $stageOrderStaging == "NULL")
				$stageOrderStaging = "'SD'";
			if ($sps != "NULL" && $stagePrintingStitching == "NULL")
				$stagePrintingStitching = "'SD'";
			if ($sc != "NULL" && $stageComplete == "NULL")
				$stageComplete = "'SD'";
			if ($sb != "NULL" && $stageBilled == "NULL")
				$stageBilled = "'SD'";
			if ($sp != "NULL" && $stagePaid == "NULL")
				$stagePaid = "'SD'";

			$trash = getValue($file_handle);
			$salesTax = getValue($file_handle);
			if ($salesTax == 'NULL')
				$salesTax = 0;
			$trash = getValue($file_handle);
			$screenCharge = getValue($file_handle);
			if ($screenCharge == 'NULL')
				$screenCharge = 0;
			$dieCharge = getValue($file_handle);
			if ($dieCharge == 'NULL')
				$dieCharge = 0;
			$artCharge = getValue($file_handle);
			if ($artCharge == 'NULL')
				$artCharge = 0;
			$colorCharge = getValue($file_handle);
			if ($colorCharge == 'NULL')
				$colorCharge = 0;
			$shippingCharge = getValue($file_handle);
			if ($shippingCharge == 'NULL')
				$shippingCharge = 0;
			$miscDescription = getValue($file_handle);
			$miscCharge = getValue($file_handle);
			if ($miscCharge == 'NULL')
				$miscCharge = 0;
			$trash = getValue($file_handle);
			$deposit = getValue($file_handle);
			if ($deposit == 'NULL')
				$deposit = 0;
			$trash = getValue($file_handle);

			/* Insert the new order record. */
			$insertOrderSql = "INSERT INTO orders (clientID, repID, artistID, departmentID, projectName, type, category, shippingClient, shippingContact, shippingAddress, shippingCity, shippingState, shippingZip, front, back, sleeve, other, frontDetails, backDetails, sleeveDetails, otherDetails, orderDate, artDueDate, printDate, dueDate, specialInstructions, productionNotes, artInstructions, comments, redAlert, artType, artFilename, artistInProgress, artistComplete, artistSentToApprove, artistRevisions, artistApproved, artistSepsDone, artistProofFilm, artistHours, stageOrderGoods, stageGoodsReceived, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid, salesTax, screenCharge, dieCharge, artCharge, colorCharge, shippingCharge, miscCharge, miscDescription, deposit) VALUES(" . $clientID . "," . $repID . "," . $artistID . "," . $artistID . "," . $projectName . "," . $type . "," . $category . "," . $shippingClient . "," . $shippingContact . "," . $shippingAddress . "," . $shippingCity . "," . $shippingState . "," . $shippingZip . "," . $front . "," . $back . "," . $sleeve . "," . $other . "," . $frontDetails . "," . $backDetails . "," . $sleeveDetails . "," . $otherDetails . "," . $orderDate . "," . $artDueDate . "," . $printDate . "," . $dueDate . "," . $specialInstructions . "," . $productionNotes . "," . $artInstructions . "," . $comments . "," . $redAlert . "," . $artType . "," . $artFilename . "," . $artistInProgress . "," . $artistComplete . "," . $artistSentToApprove . "," . $artistRevisions . "," . $artistApproved . "," . $artistSepsDone . "," . $artistProofFilm . "," . $artistHours . "," . $stageOrderGoods . "," . $stageGoodsReceived . "," . $stageOrderStaging . "," . $stagePrintingStitching . "," . $stageComplete . "," . $stageBilled . "," . $stagePaid . "," . $salesTax . "," . $screenCharge . "," . $dieCharge . "," . $artCharge . "," . $colorCharge . "," . $shippingCharge . "," . $miscCharge . "," . $miscDescription . "," . $deposit . ")";
			mysqli_query($conn, $insertOrderSql) or die(mysqli_error($conn));
			$orderID = $i;

			/* Add an order entry for each complete order. */
			$orders = array();
			for ($j = 0; $j < 13; $j++) {
				$quantity = getValue($file_handle);
				$description = getValue($file_handle);
				$color = getValue($file_handle);
				$yxs = getValue($file_handle);
				$ys = getValue($file_handle);
				$ym = getValue($file_handle);
				$yl = getValue($file_handle);
				$yxl = getValue($file_handle);
				$s = getValue($file_handle);
				$m = getValue($file_handle);
				$l = getValue($file_handle);
				$xl = getValue($file_handle);
				$xxl = getValue($file_handle);
				$xxxl = getValue($file_handle);
				$xxxxl = getValue($file_handle);
				$price = getValue($file_handle);
				$trash = getValue($file_handle);
				/* Save each into an array as the string ready to be inserted into mySQL, then add misc
			   and vendor afterwards (since they're placed at the bottom of the .order file */
				if ($quantity != "NULL" || $description != "NULL")
					$orders[] = "INSERT INTO orderItems (orderID, quantity, description, color, yxs, ys, ym, yl, yxl, s, m, l, xl, xxl, xxxl, xxxxl, price, vendor, misc) VALUES(" . $orderID . "," . $quantity . "," . $description . "," . $color . "," . $yxs . "," . $ys . "," . $ym . "," . $yl . "," . $yxl . "," . $s . "," . $m . "," . $l . "," . $xl . "," . $xxl . "," . $xxxl . "," . $xxxxl . "," . $price . ",";
			}

			/* Tack the vendors on to the end of the queries which had a quantity */
			for ($j = 0; $j < 13; $j++) {
				$vendor = getValue($file_handle);
				if (isset($orders[$j])) {
					$orders[$j] .= $vendor . ",";
				}
			}

			/* Add on any misc and submit a query for each qualifying order */
			for ($j = 0; $j < 13; $j++) {
				$misc = getValue($file_handle);
				if (isset($orders[$j])) {
					$orders[$j] .= $misc . ")";
					mysqli_query($conn, $orders[$j]) or die(mysqli_error($conn));
				}
			}

			fclose($file_handle);
		}
	}

	function getValue($fh)
	{
		include('database.php');

		$line = fgets($fh);
		if (strlen($line) == 0)
			$line = fgets($fh);

		//echo "<em>".$line."</em><br/>";
		$parts = explode(':', $line);
		if (sizeof($parts) < 2 || trim($parts[1]) == "")
			return "NULL";

		$value = mysqli_real_escape_string($conn, trim($parts[1]));
		for ($k = 2; $k < sizeof($parts); $k++)
			$value .= ":" . mysqli_real_escape_string($conn, trim($parts[$k]));

		/* Check the next line to see if it's part of this entry (via a carriage return) */
		$moreLines = true;
		while ($moreLines && !feof($fh)) {
			$fileOffset = ftell($fh);
			$line = fgets($fh);
			if (strpos($line, ":") === false)
				$value .= "\n" . mysqli_real_escape_string($conn, trim($line));
			else {
				fseek($fh, $fileOffset);
				$moreLines = false;
			}
		}

		//echo $value."<br />";
		return "'" . $value . "'";
	}

	function stripOneFromPhone($pn)
	{
		$pns = explode("-", $pn);
		if ($pns[0] == "'1") {
			$pn2 = "'" . $pns[1];
			for ($pni = 2; $pni < sizeof($pns); $pni++)
				$pn2 .= "-" . $pns[$pni];

			echo $pn . " => " . $pn2 . "<br>";
			return $pn2;
		}
		return $pn;
	}

	/* This is ghetto-assembled from a function found online. Just saving time here... */
	function sqlDate($fh)
	{
		/** Variable local bool $err
		 * @var bool $err Default error state
		 * @name $err
		 *
		 * @abstract Default error state
		 *
		 * @access private
		 * @static
		 * @since v1.0
		 *
		 **/
		$line = fgets($fh);
		if (strlen($line) == 0)
			$line = fgets($fh);

		$parts = explode(':', $line);
		if (sizeof($parts) < 2 || trim($parts[1]) == "")
			return "NULL";

		$value = addslashes(trim($parts[1]));
		for ($v = 2; $v < sizeof($parts); $v++)
			$value .= ":" . addslashes(trim($parts[$v]));
		$strDate = $value;
		$err = false;

		// We will be doing many levels of error checking
		// and will need to bale at any time,
		// so we do this...
		if ((strlen($strDate) >= 8) && (strlen($strDate) <= 10)) {
			/** Variable local array $tempDate
			 * @var array $tempDate Holds tore apart string as array
			 * @name $tempDate
			 *
			 * @abstract Holds tore apart string as array
			 *
			 * @access private
			 * @static
			 * @since v1.0
			 *
			 **/
			$tempDate = explode('/', $strDate);

			// See if we got what we thought we should
			if (count($tempDate) == 3) {
				$month = $tempDate[0] + 0;
				$daynum = $tempDate[1] + 0;
				$year = $tempDate[2] + 0;
			} else
				$err = true;
		} else
			$err = true;

		$month   = (($month   <  10) ? '0'  . $month   : $month);
		// prepend ZERO, maybe
		$daynum  = (($daynum  <  10) ? '0'  . $daynum   : $daynum);
		// prepend ZERO, maybe

		if (! $err)
			// mm/dd/yyyy
			return "'" . $year . '-' . $month  . '-' . $daynum . "'";
		else
			return "NULL";
	}      // AmerDate2SqlDateTime ()
	?>

</body>

</html>