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

if (strstr($_SERVER['HTTP_REFERER'], "orderID=") && strstr($_SERVER['HTTP_REFERER'], "edit=1")) {
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
	<title>OMS - Sort</title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>

<body>
	<!-- InstanceBeginEditable name="topbody" -->
	<!-- InstanceEndEditable -->
	<?php include('header.php'); ?>
	<!-- InstanceBeginEditable name="body" -->
	<form method="post">

		<div style="position:relative; top:20px; text-align:center; width:99%">
			<?php
			if (isset($_POST['deleteOrders'])) {
				$count = 0;
				for ($i = 0; $i < $_POST['highestID']; $i++) {
					if (isset($_POST['check' . $i])) {
						$count++;
						$updateOrderSql = "UPDATE orders SET visible=0 WHERE id=" . $i;
						mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
					}
				}
				echo '<span style="color:#FF0000;">' . $count . ' orders deleted</span><br /><br />';
			}
			?>
			<div style="width:360px; margin:auto; text-align:right">
				<label>Narrow results by Category:
					<select name="category" style="width: 200px; text-align: right;">
						<option value="All">All Categories</option>
						<option value="Screen Print" <?php if ($_POST['category'] == "Screen Print") echo "selected=1" ?>>Screen Print</option>
						<option value="Logo Magnet" <?php if ($_POST['category'] == "Logo Magnet") echo "selected=1" ?>>Logo Magnet</option>
						<option value="LM-Dealer" <?php if ($_POST['category'] == "LM-Dealer") echo "selected=1" ?>>LM-Dealer</option>
						<option value="C-S-Shirts" <?php if ($_POST['category'] == "C-S-Shirts") echo "selected=1" ?>>C-S-Shirts</option>

						<option value="CMD" <?php if ($_POST['category'] == "CMD") echo "selected=1" ?>>CMD</option>
						<option value="Promotional" <?php if ($_POST['category'] == "Promotional") echo "selected=1" ?>>Promotional</option>
						<option value="Embroidery" <?php if ($_POST['category'] == "Embroidery") echo "selected=1" ?>>Embroidery</option>
						<option value="Other" <?php if ($_POST['category'] == "Other") echo "selected=1" ?>>Other</option>
					</select></label><br />

				<label>Narrow results by Type:
					<select name="type" style="width: 200px; text-align: right;">
						<option value="Order">Order</option>
						<option value="Inquiry/Quote" <?php if ($_POST['type'] == "Inquiry/Quote") echo "selected=1" ?>>Inquiry/Quote</option>
						<option value="Mock-up" <?php if ($_POST['type'] == "Mock-up") echo "selected=1" ?>>Mock-up</option>
						<option value="Ready to Print" <?php if ($_POST['type'] == "Ready to Print") echo "selected=1" ?>>Ready to Print</option>
						<option value="Project" <?php if ($_POST['type'] == "Project") echo "selected=1" ?>>Project</option>
						<option value="Inactive" <?php if ($_POST['type'] == "Inactive") echo "selected=1" ?>>Inactive</option>
						<option value="Other" <?php if ($_POST['type'] == "Other") echo "selected=1" ?>>Other</option>
						<option value="All" <?php if ($_POST['type'] == "All") echo "selected=1" ?>>All Types</option>
					</select></label><br />

				<label>Sort by:
					<select name="sortType" style="width: 200px; text-align: right;">
						<option value="dueDate" <?php if ($_POST['sortType'] == "dueDate") echo "selected=1" ?>>Order Due Date</option>
						<option value="printDate" <?php if ($_POST['sortType'] == "printDate") echo "selected=1" ?>>Scheduled to Print Date</option>
						<option value="artDueDate" <?php if ($_POST['sortType'] == "artDueDate") echo "selected=1" ?>>Art Due Date</option>
						<option value="orderDate" <?php if ($_POST['type'] == "orderDate") echo "selected=1" ?>>Order Date</option>
					</select></label><br /><br />
			</div>
			<input type=submit name="sortSubmit" value="Sort" style="width:100px;" />
		</div>

		<div style="position:relative; top:30px; width:99%; text-align:center">
			<?php
			if (isset($_POST['type'])) {
				/* If there were more than 100 matches and the user hit a navigation button... */
				if (isset($_POST['display']) && !isset($_POST['sortSubmit'])) {
					/* Find which button was pressed and modify the display number appropriately */
					$displayNumber = $_POST['display'];
					if (isset($_POST['firstResults']))
						$displayNumber = 0;
					else if (isset($_POST['previousResults']))
						$displayNumber -= 100;
					else if (isset($_POST['nextResults']))
						$displayNumber += 100;
					else if (isset($_POST['lastResults']))
						$displayNumber = $_POST['displayTotal'] - ($_POST['displayTotal'] % 100);

					$endDisplayNumber = $displayNumber + 100;
					if ($endDisplayNumber > $_POST['displayTotal'])
						$endDisplayNumber = $_POST['displayTotal'];
				} else {
					$displayNumber = 0;
					$endDisplayNumber = 100;
				}

				$category = "category IS NOT NULL";
				if ($_POST['category'] == "C-S-Shirts")
					$category = "category = 'Logo Ventures' ";
				else if ($_POST['category'] != "All")
					$category = "category = '" . $_POST['category'] . "' ";

				$type = "type != 'Archive' ";
				if ($_POST['type'] != "All")
					$type = "type = '" . $_POST['type'] . "' ";

				$selectOrderClientSql = "SELECT SQL_CALC_FOUND_ROWS name, orders.id as id, projectName, category, type, DATE_FORMAT(" . $_POST['sortType'] . ",'%m/%d/%Y') as sortDate, artFilename, stageOrderGoods, stageGoodsReceived, artistApproved, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid  FROM orders INNER JOIN clients ON orders.clientID = clients.id WHERE orders.visible = 1 AND (orders.stageComplete IS NULL OR orders.stageComplete = '') AND orders." . $category . " AND orders." . $type . " ORDER BY orders." . $_POST['sortType'] . " ASC LIMIT " . $displayNumber . " , 100";
				$selectOrderClientQuery = mysqli_query($conn, $selectOrderClientSql) or die(mysqli_error($conn));
				$selectOrderClientRowSql = "SELECT FOUND_ROWS();";
				$selectOrderClientRowQuery = mysqli_query($conn, $selectOrderClientRowSql) or die(mysqli_error($conn));
				$numResult = mysqli_fetch_array($selectOrderClientRowQuery);
				if ($numResult[0] < 1)
					echo "[No Matches]";
				else {
					$sortDesc = "Order Due Date";
					if ($_POST['sortType'] == "printDate")
						$sortDesc = "Scheduled to Print Date";
					else if ($_POST['sortType'] == "artDueDate")
						$sortDesc = "Art Due Date";
					else if ($_POST['sortType'] == "orderDate")
						$sortDesc = "Order Date";

					if ($numResult[0] > 100)
						echo "Displaying " . ($displayNumber + 1) . " - " . $endDisplayNumber . " of " . $numResult[0] . " matches <br />";
					else
						echo $numResult[0] . " matches";
			?>
					<input type='hidden' name='display' value='<?php echo $displayNumber; ?>' />
					<input type='hidden' name='displayTotal' value='<?php echo $numResult[0]; ?>' />
					<?php if ($displayNumber != 0) { ?>
						<input type='submit' name='firstResults' value='<<' style='float:left' />
						<input type='submit' name='previousResults' value='<' style='float:left' />
					<?php }
					if ($displayNumber < $numResult[0] - 100) { ?>
						<input type='submit' name='lastResults' value='>>' style='float:right' />
						<input type='submit' name='nextResults' value='>' style='float:right' />
					<?php } ?>
					<br /><br />
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
								<font face=arial size=1><?php echo $sortDesc; ?>
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
						<?php
						$highestID = 0;
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

							if ($artFilename != "")
								$artFilename = "<a href='" . BASE_URL . "files/" . $artFilename . "' target='_blank'><font color=000000>uploaded art</a>";
							else
								$artFilename = "[no&nbsp;uploaded&nbsp;art]";

							if ($category == "Logo Ventures")
								$category = "C-S-Shirts";
						?>
							<tr>
								<td align="center"><input type="checkbox" class='selector' name="check<?php echo $orderID; ?>" /></td>
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
						<?php 	} ?>
					</table>
					<input type="hidden" name="highestID" value="<?php echo $highestID; ?>" />
					<input type="hidden" name="ids" value="<?php echo $ids; ?>"><br />
					<div id="deleteDiv" style="position:fixed; left:10px; bottom:10px; width:200px; height:75px; visibility:hidden; background-color:#CCCCCC; border:3px solid #000000; padding:4px">
						Are you sure you want to delete these <span id="numberToDelete">9</span> orders?<br /><br />
						<input type="button" value="No" onclick="document.getElementById('deleteDiv').style.visibility='hidden';" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="deleteOrders" value="Yes" />
					</div>
					<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
					<?php if ($_SESSION['initials'] == 'TT') { ?><input type="button" name="deleteButton" style="float:right; margin-right:5px" value="Delete selected orders" onclick="deleteMarkedOrders();" /><?php } ?>
					<input type="button" name="printOrders" value="Print selected" style="float:right" onclick="printSelected()" /><br /><br />
	</form>
<?php
				}
			} ?>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->

</html>