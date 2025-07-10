<?php
include('database.php');
include('config.php');
session_start();
if (!isset($_SESSION['initials']))
	header('Location: index2.php');

function getImage($initials)
{
	if ($initials == "")
		return "redsphere.gif";
	else
		return "bluesphere.gif";
}

if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "orderID=") && strstr($_SERVER['HTTP_REFERER'], "edit=1")) {
	$oldID = strstr($_SERVER['HTTP_REFERER'], "orderID=");
	$oldID = substr($oldID, 8, strpos($oldID, "&") - 8);
	$updateOrderSql = "UPDATE orders SET lockedByName=NULL WHERE id='" . $oldID . "'";
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
	<title>OMS - Order Goods</title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>

<body>
	<!-- InstanceBeginEditable name="topbody" -->
	<!-- InstanceEndEditable -->
	<?php include('header.php'); ?>
	<!-- InstanceBeginEditable name="body" -->

	<form name='navi' method="post">
		<div style="position:relative; top:20px; text-align:center; width:99%">
			<fieldset style="text-align:right; width:110px; margin:auto">
				<legend>Categories</legend>
				<label>All <input type="checkbox" name="allCats" <?php if (($_POST['allCats'] ?? '') == 'on' || !isset($_POST['searchButton'])) echo 'checked=1'; ?> onclick='return checkAll();' /></label><br />
				<label>Screen Print <input type="checkbox" name="screenPrint" <?php if ($_POST['screenPrint'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
				<label>C-S-Shirts <input type="checkbox" name="logoVentures" <?php if ($_POST['logoVentures'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
				<label>Promotional <input type="checkbox" name="promotional" <?php if ($_POST['promotional'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
				<label>Embroidery <input type="checkbox" name="embroidery" <?php if ($_POST['embroidery'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
				<label>Signs <input type="checkbox" name="signs" <?php if ($_POST['signs'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
				<label>Other <input type="checkbox" name="other" <?php if ($_POST['other'] ?? '' == 'on') echo 'checked=1'; ?> onclick='return checkOther();' /></label><br />
			</fieldset><br />
			<input type="submit" name="searchButton" value="Search" style="width:100px;" />
		</div>
	</form>
	<div style="position:relative; top:30px; width:99%; text-align:center">
		<?php
		if (isset($_POST['deleteOrders'])) {
			$count = 0;
			for ($i = 0; $i <= $_POST['highestID']; $i++) {
				if (isset($_POST['check' . $i])) {
					$count++;
					$updateOrderSql = "UPDATE orders SET visible=0 WHERE id=" . $i;
					mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
				}
			}
			echo '<span style="color:#FF0000">' . $count . ' orders deleted</span><br /><br />';
		} else if (isset($_POST['markAsOrdered'])) {
			$count = 0;
			for ($i = 0; $i <= $_POST['highestID']; $i++) {
				if (isset($_POST['check' . $i])) {
					$count++;
					$updateOrderSql = "UPDATE orders SET stageOrderGoods = '" . $_SESSION['initials'] . "' WHERE id=" . $i;
					mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
				}
			}
			echo '<span style="color:darkgreen">' . $count . ' orders have had their "Order Goods" attribute marked with the initials: ' . $_SESSION['initials'] . '</span><br /><br />';
		}
		?>
		<?php
		if (isset($_POST['searchButton'])) {
			$selectOrderClientSql = "SELECT projectName, orders.id as id, category, type, DATE_FORMAT(dueDate,'%m/%d/%Y') as sortDate, artFilename, stageOrderGoods, stageGoodsReceived, artistApproved, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid, name FROM orders INNER JOIN clients ON orders.clientID = clients.id WHERE orders.visible = 1 AND (orders.type = 'Order' OR orders.type = 'Ready to Print') AND (stageOrderGoods IS NULL OR stageOrderGoods = '') AND orders.category != 'Logo Magnet' AND orders.category != 'CMD' ";
			$queryString2 = "AND ( ";
			if (isset($_POST['screenPrint']) && $_POST['screenPrint'] == 'on')
				$queryString2 .= "orders.category = 'Screen Print' OR ";
			if (isset($_POST['logoVentures']) && $_POST['logoVentures'] == 'on')
				$queryString2 .= "orders.category = 'Logo Ventures' OR ";
			if (isset($_POST['promotional']) && $_POST['promotional'] == 'on')
				$queryString2 .= "orders.category = 'Promotional' OR ";
			if (isset($_POST['embroidery']) && $_POST['embroidery'] == 'on')
				$queryString2 .= "orders.category = 'Embroidery' OR ";
			if (isset($_POST['other']) && $_POST['other'] == 'on')
				$queryString2 .= "orders.category = 'Other' OR ";

			if ($queryString2 != "AND ( ") {
				$queryString2 .= "FALSE ) ";
				$selectOrderClientSql .= $queryString2;
			}

			$sortType = isset($_POST['filter2']) ? $_POST['filter2'] : '';

			$selectOrderClientSql .= "ORDER BY sortDate ASC ";

			$selectOrderClientQuery = mysqli_query($conn, $selectOrderClientSql) or die(mysqli_error($conn));
			if (mysqli_num_rows($selectOrderClientQuery) < 1)
				echo "[No Matches]";
			else {
				echo mysqli_num_rows($selectOrderClientQuery) . " matches";
		?>

				<br />
				<div style="display:flex;justify-content:space-between">
					<div>
						<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
						<input type="submit" name="markAsOrdered" value="Update selected" style="margin-left:10px;" />
						<input type="button" name="printOrders" value="Print selected" style="margin-left:10px;" onclick="printSelected()" />
					</div>
				</div>
				<br />

				<table border=1 width=100% cellpadding=1 cellspacing=1>
					<tr>
						<td align="center" rowspan=2></td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Client Name
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Project Name
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Category
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Type
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Order Due Date
						</td>
						<td align="center" colspan=8>
							<font face=arial size=1>Stages Completed
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Link to art sample
						</td>
						<td align="center" rowspan=2>
							<font face=arial size=1>Options
						</td>
					</tr>
					<tr style="font-size:7px">
						<td align="center" cellpadding=0 width=30>Order<br>Goods</td>
						<td align="center" cellpadding=0 width=30>Goods<br>Received</td>
						<td align="center" cellpadding=0 width=30>Art<br>Approval</td>
						<td align="center" cellpadding=0 width=30>Order<br>Staging</td>
						<td align="center" cellpadding=0 width=30>Printing/<br>Stitching</td>
						<td align="center" cellpadding=0 width=30>Complete</td>
						<td align="center" cellpadding=0 width=30>Billed</td>
						<td align="center" cellpadding=0 width=30>Paid</td>
					</tr>
					<form name="orderForm" method="post">
						<?php
						$highestID = 0;
						$ids = array();
						while ($result = mysqli_fetch_array($selectOrderClientQuery)) {
							$name = $result['name'];
							$orderID = $result['id'];
							$projectName = $result['projectName'];
							$category = $result['category'];
							$type = $result['type'];
							$dueDate = $result['sortDate'];
							$artFilename = $result['artFilename'];
							$stageOrderGoods = getImage($result['stageOrderGoods']);
							$stageGoodsReceived = getImage($result['stageGoodsReceived']);
							$artistApproved = getImage($result['artistApproved']);
							$stageOrderStaging = getImage($result['stageOrderStaging']);
							$stagePrintingStitching = getImage($result['stagePrintingStitching']);
							$stageComplete = getImage($result['stageComplete']);
							$stageBilled = getImage($result['stageBilled']);
							$stagePaid = getImage($result['stagePaid']);

							if ($highestID < $orderID)
								$highestID = $orderID;

							$ids[] = $orderID;

							if ($artFilename != "")
								$artFilename = "<a href='" . BASE_URL . "files/" . $artFilename . "' target='_blank'><font color=000000>uploaded art</a>";
							else
								$artFilename = "[no&nbsp;uploaded&nbsp;art]";

							if ($category == "Logo Ventures")
								$category = "C-S-Shirts";
						?>
							<tr>
								<td align="center"><input type="checkbox" class='selector' name="check<?php echo $orderID; ?>" onclick="return addToDelete('<?php echo $orderID; ?>');" />
								<td align="center">
									<font face=arial size=1>&nbsp;<?php echo $name; ?>&nbsp;
								</td>
								<td align="center">
									<font face=arial size=1>&nbsp;<?php echo $projectName; ?>&nbsp;
								</td>
								<td align="center">
									<font face=arial size=1>&nbsp;<?php echo $category; ?>&nbsp;
								</td>
								<td align="center">
									<font face=arial size=1>&nbsp;<?php echo $type; ?>&nbsp;
								</td>
								<td align="center">
									<font face=arial size=1>&nbsp;<?php echo $dueDate; ?>&nbsp;
								</td>
								<td align="center"><img src="<?php echo $stageOrderGoods; ?>" style="height:10px; width:10px" alt="Order Goods"></td>
								<td align="center"><img src="<?php echo $stageGoodsReceived; ?>" style="height:10px; width:10px" alt="Goods Received"></td>
								<td align="center"><img src="<?php echo $artistApproved; ?>" style="height:10px; width:10px" alt="Art Approval"></td>
								<td align="center"><img src="<?php echo $stageOrderStaging; ?>" style="height:10px; width:10px" alt="Order Staging"></td>
								<td align="center"><img src="<?php echo $stagePrintingStitching; ?>" style="height:10px; width:10px" alt="Printing/Stitching"></td>
								<td align="center"><img src="<?php echo $stageComplete; ?>" style="height:10px; width:10px" alt="Complete"></td>
								<td align="center"><img src="<?php echo $stageBilled; ?>" style="height:10px; width:10px" alt="Billed"></td>
								<td align="center"><img src="<?php echo $stagePaid; ?>" style="height:10px; width:10px" alt="Paid"></td>
								<td align="center">
									<font face=arial size=1><?php echo $artFilename; ?>
								</td>
								<td align="center" nowrap><input type="button" value="Edit" onclick='window.open("viewOrder.php?orderID=<?php echo $orderID; ?>&edit=1")' />&nbsp;&nbsp;<input name='editonly' type="button" value="View" onclick="window.open('viewOrder.php?orderID=<?php echo $orderID; ?>')"></td>
							</tr>
						<?php 	}
						$ids = urlencode(serialize($ids)); ?>
				</table>
				<input type='hidden' name='salestaxradio' />
				<input type="hidden" name="highestID" value="<?php echo $highestID; ?>" /><br />

				<div style="display:flex;justify-content:space-between">
					<div>
						<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
						<input type="submit" name="markAsOrdered" value="Update selected" style="margin-left:10px;" />
						<input type="button" name="printOrders" value="Print selected" style="margin-left:10px;" onclick="printSelected()" />
					</div>
				</div>
				<br />
				</form>
				</p>


		<?php
			}
		} ?>
	</div>
	<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->

</html>