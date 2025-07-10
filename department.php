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

function str_replace_once($needle, $replace, $haystack)
{
	// Looks for the first occurence of $needle in $haystack 
	// and replaces it with $replace. 
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		// Nothing found 
		return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}

if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "orderID=") && strstr($_SERVER['HTTP_REFERER'], "edit=1")) {
	$oldID = strstr($_SERVER['HTTP_REFERER'], "orderID=");
	$oldID = substr($oldID, 8, strpos($oldID, "&") - 8);
	$updateOrderSql = "UPDATE orders SET lockedByName=NULL WHERE id='" . $oldID . "'";
	mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
}

/* Get department list */
$selectUserSql = "SELECT id,name FROM users WHERE type NOT LIKE '%Inactive%' AND type != 'Admin' ORDER BY users.name ASC";
$selectUserQuery = mysqli_query($conn, $selectUserSql) or die(mysqli_error($conn));
$departmentList = "";
$department = isset($_POST['department']) ? $_POST['department'] : null;
while ($entry = mysqli_fetch_array($selectUserQuery)) {
	if ($entry[0] == $department || (!isset($department) && $entry[1] == "SalesDept"))
		$departmentList .= "<option value='" . $entry[0] . "' selected='selected'>" . $entry[1] . "</option>";
	else
		$departmentList .= "<option value='" . $entry[0] . "'>" . $entry[1] . "</option>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/OMS.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<script language="javascript" src="oms.js"></script>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>OMS - Department</title>
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
			<div style="width:250px; margin:auto; text-align:right">
				<label>Department <select name="department" style="width:154px"><?php echo $departmentList ?></select></label><br />

				<label>Sort by:
					<select name="sortType" style="width: 154px;">
						<option value="dueDate" <?php if ($_POST['sortType'] ?? '' == "dueDate") echo " selected='selected'" ?>>Order Due Date</option>
						<option value="printDate" <?php if ($_POST['sortType'] ?? '' == "printDate") echo " selected='selected'" ?>>Scheduled to Print Date</option>
						<option value="artDueDate" <?php if ($_POST['sortType'] ?? '' == "artDueDate") echo " selected='selected'" ?>>Art Due Date</option>
						<option value="orderDate" <?php if ($_POST['sortType'] ?? '' == "orderDate") echo " selected='selected'" ?>>Order Date</option>

					</select></label><br />

				<?php
				$t_order = false;
				$t_inquiry = false;
				$t_mock = false;
				$t_ready = false;
				$t_project = false;
				$t_inactive = false;

				$typeArray = $_POST['type'] ?? [];
				$somethingIsChecked = !empty($typeArray);
				if ($somethingIsChecked) {
					$N = count($typeArray);
					for ($i = 0; $i < $N; $i++) {
						if ($typeArray[$i] == 'A')
							$t_order = true;
						else if ($typeArray[$i] == 'B')
							$t_inquiry = true;
						else if ($typeArray[$i] == 'C')
							$t_mock = true;
						else if ($typeArray[$i] == 'D')
							$t_ready = true;
						else if ($typeArray[$i] == 'E')
							$t_project = true;
						else if ($typeArray[$i] == 'F')
							$t_inactive = true;
					}
				}
				?>
				Order<input type="checkbox" name="type[]" value="A" <?php if (!$somethingIsChecked || $t_order) echo "checked=1"; ?> /><br />
				Inquiry/Quote<input type="checkbox" name="type[]" value="B" <?php if ($t_inquiry) echo "checked=1"; ?> /><br />
				Mock-up<input type="checkbox" name="type[]" value="C" <?php if ($t_mock) echo "checked=1"; ?> /><br />
				Ready to Print<input type="checkbox" name="type[]" value="D" <?php if (!$somethingIsChecked || $t_ready) echo "checked=1"; ?> /><br />
				Project<input type="checkbox" name="type[]" value="E" <?php if ($t_project) echo "checked=1"; ?> /><br />
				Inactive<input type="checkbox" name="type[]" value="F" <?php if ($t_inactive) echo "checked=1"; ?> /><br />
				<label>Order Status: <select name="orderType" style="width:154px;">
						<option value="active">Active Orders</option>
						<option value="all" <?php if ($_POST['orderType'] ?? '' == "all") echo " selected='selected'" ?>>All Orders</option>
						<option value="archive" <?php if ($_POST['orderType'] ?? '' == "archive") echo " selected='selected'" ?>>Archived Orders</option>
						<option value="stageBilled" <?php if ($_POST['orderType'] ?? '' == "stageBilled") echo " selected='selected'" ?>>Billed Orders</option>
						<option value="stagePaid" <?php if ($_POST['orderType'] ?? '' == "stagePaid") echo " selected='selected'" ?>>Paid Orders</option>
						<option value="stageComplete" <?php if ($_POST['orderType'] ?? '' == "stageComplete") echo " selected='selected'" ?>>Completed Orders</option>

					</select></label><br /><br />
			</div>
			<input type="submit" value="Display" name='departmentSubmit' style="width:100px;" />
		</div>
	</form>
	<div style="position:relative; top:30px; width:99%; text-align:center">
		<form name="departmentForm" method="post">
			<?php
			/* User moved selected orders to a different department */
			if (isset($_POST['moveDeptTo'])) {
				$count = 0;
				for ($i = 0; $i <= $_POST['highestID']; $i++) {
					if (isset($_POST['check' . $i])) {
						$count++;
						$updateOrderSql = "UPDATE orders SET departmentID=" . $_POST['departmentChangeID'] . " WHERE id=" . $i;
						mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
					}
				}
				echo '<span style="color:darkgreen">Department changed for ' . $count . ' orders.</span><br /><br />';
			}
			if (isset($_POST['sortType'])) {
				$resultsPerPage = 100;
				/* If there were more than 100 matches and the user hit a navigation button... */
				if (isset($_POST['display']) && !isset($_POST['departmentSubmit'])) {
					/* Find which button was pressed and modify the display number appropriately */
					$displayNumber = isset($_POST['display']) ? $_POST['display'] : 0;
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

				foreach ($_POST as $key => $value) {
					if (preg_match('/^page(\d+)$/', $key, $matches)) {
						$desiredPage = (int)$matches[1];
						$displayNumber = ($desiredPage - 1) * 100;
						$endDisplayNumber = $displayNumber + 100;
						if ($endDisplayNumber > (int)$_POST['displayTotal'])
							$endDisplayNumber = (int)$_POST['displayTotal'];
						break;
					}
				}

				if ($displayNumber < 0) {
					$displayNumber = 0;
				}
				$totalResults = isset($_POST['displayTotal']) ? (int)$_POST['displayTotal'] : 0;
				if ($displayNumber >= $totalResults) {
					$displayNumber = max(0, $totalResults - 100);
					$endDisplayNumber = $totalResults;
				}

				$selectOrderClientSql = "SELECT SQL_CALC_FOUND_ROWS name, orders.id as id, projectName, category, type, DATE_FORMAT(" . $_POST['sortType'] . ",'%m/%d/%Y') as sortDate, artFilename, stageOrderGoods, stageGoodsReceived, artistApproved, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid FROM orders INNER JOIN clients ON orders.clientID = clients.id WHERE orders.departmentID = " . $_POST['department'] . " AND orders.visible = 1 ";

				$sortType = $_POST['orderType'];

				if ($sortType == "archive")
					$selectOrderClientSql .= "AND orders.type = 'Archive' ";
				else if ($sortType != 'all')
					$selectOrderClientSql .= "AND orders.type != 'Archive' ";

				if ($sortType == "stageBilled")
					$selectOrderClientSql .= "AND (orders.stageBilled IS NOT NULL AND orders.stageBilled != '') AND (orders.stagePaid IS NULL OR orders.stagePaid = '') ";
				else if ($sortType == "stagePaid")
					$selectOrderClientSql .= "AND orders.stagePaid IS NOT NULL AND orders.stagePaid != '' ";
				else if ($sortType == "stageComplete")
					$selectOrderClientSql .= "AND orders.stageComplete IS NOT NULL AND orders.stageComplete != '' AND (orders.stageBilled = '' OR orders.stageBilled IS NULL) AND (orders.stagePaid IS NULL OR orders.stagePaid = '') ";
				else if ($sortType == "active")
					$selectOrderClientSql .= "AND (orders.stageComplete IS NULL OR orders.stageComplete = '' ) ";

				$orderType = [];
				if ($t_order) $orderType[] = "orders.type ='Order'";
				if ($t_inquiry) $orderType[] = "orders.type ='Inquiry/Quote'";
				if ($t_mock) $orderType[] = "orders.type ='Mock-up'";
				if ($t_ready) $orderType[] = "orders.type ='Ready to Print'";
				if ($t_project) $orderType[] = "orders.type ='Project'";
				if ($t_inactive) $orderType[] = "orders.type ='Inactive'";

				if (!empty($orderType)) {
					$selectOrderClientSql .= "AND (" . implode(" OR ", $orderType) . ") ";
				}

				$selectOrderClientSql .= "ORDER BY " . $_POST['sortType'] . " ASC LIMIT " . $displayNumber . " , 100";
				$selectOrderClientQuery = mysqli_query($conn, $selectOrderClientSql) or die(mysqli_error($conn));
				$selectOrderClientRowSql = "SELECT FOUND_ROWS();";
				$selectOrderClientRowQuery = mysqli_query($conn, $selectOrderClientRowSql) or die(mysqli_error($conn));
				$result = mysqli_fetch_array($selectOrderClientRowQuery);

				$totalResults = (int)$result[0];

				if ($totalResults < 1)
					echo "<center><div>[No Matches]</div></center>";
				else {
					if ($_POST['department'] == 10 && ($t_order || $t_ready)) {
						$dayOfWeek = date("w", time());
						$sunday = time() - (60 * 60 * 24 * $dayOfWeek);
						$saturday = $sunday + (60 * 60 * 24 * 7);
						$dayArray = array();
						$lastRow = 0;
						$rowTwo = "";
						echo "<table class='previewTable'><tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr><tr>";
						for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
							echo "<td>";
							$rowTwo .= "<td>";
							$currentDay = $sunday + (60 * 60 * 24 * $dayOfWeek);
							$toyear = date('Y', $currentDay);
							$tomonth = date('n', $currentDay);
							$tooFar = false;
							while (!$tooFar && ($qresult = mysqli_fetch_array($selectOrderClientQuery))) {
								$type = $qresult['type'];
								if (($type == "Ready to Print" && $t_ready) || ($type == "Order" && $t_order && !$t_ready)) {
									$date = explode("/", $qresult['sortDate']);
									$a_month = $date[0];
									$a_day = $date[1];
									$a_year = $date[2];
									$a_date = mktime(0, 0, 0, $a_month, $a_day, $a_year);

									if ($a_date > $currentDay) {
										mysqli_data_seek($selectOrderClientQuery, $lastRow);
										$lastRow--;
										$tooFar = true;
									} else if ($a_year == $toyear && $a_month == $tomonth && $a_day == date('j', $currentDay)) {
										$selectOrderItemSql = "SELECT SUM(yxs) + SUM(ys) + SUM(ym) + SUM(yl) + SUM(yxl) + SUM(s) + SUM(m) + SUM(l) + SUM(xl) + SUM(xxl) + SUM(xxxl) + SUM(xxxxl) + SUM(misc) AS 'totalCount' FROM orderItems WHERE orderID = " . $qresult['id'] . " AND visible=1";
										$selectOrderItemQuery = mysqli_query($conn, $selectOrderItemSql) or die(mysqli_error($conn));
										$cresult = mysqli_fetch_array($selectOrderItemQuery);

										$cat = $qresult['category'];
										if ($cat == "Screen Print" || $cat == "Logo Ventures")
											echo $qresult["name"] . " - " . $cresult['totalCount'] . "<br/>";
										else if ($cat == "Logo Magnet" || $cat == "LM-Dealer" || $cat == "CMD")
											$rowTwo .= $qresult["name"] . " - " . $cresult['totalCount'] . "<br/>";
									}
								}
								$lastRow++;
							}
							echo "</td>";
							$rowTwo .= "</td>";
						}
						echo "</tr>$rowTwo</tr></table><br/><br/>";
					}

					$sortDesc = "Order Due Date";
					if ($_POST['sortType'] == "printDate")
						$sortDesc = "Scheduled to Print Date";
					else if ($_POST['sortType'] == "artDueDate")
						$sortDesc = "Art Due Date";
					else if ($_POST['sortType'] == "orderDate")
						$sortDesc = "Order Date";

					echo "<center>";
					if ($totalResults > 100)
						echo "Displaying " . ($displayNumber + 1) . " - " . $endDisplayNumber . " of " . $totalResults . " matches<br />";
					else
						echo $totalResults . " matches";
					echo "</center>";
			?>
					<input type='hidden' name='sortType' value='<?php echo $_POST['sortType']; ?>' />
					<?php
					foreach ($typeArray as $key => $value) {
						echo "<input type='hidden' name='type[]' value='" . htmlspecialchars($value) . "'/>";
					}
					?>
					<input type='hidden' name='orderType' value='<?php echo $_POST['orderType']; ?>' />
					<input type='hidden' name='department' value='<?php echo $_POST['department']; ?>' />
					<input type='hidden' name='display' value='<?php echo $displayNumber; ?>' />
					<input type='hidden' name='displayTotal' value='<?php echo $totalResults; ?>' />

					<!-- Pagination Controls -->
					<?php
					// Calculate current page and total pages
					$totalPages = ceil($totalResults / $resultsPerPage);
					$currentPage = floor($displayNumber / $resultsPerPage) + 1;
					$range = 2;
					$startPage = max(1, $currentPage - $range);
					$endPage = min($totalPages, $currentPage + $range);
					?>

					<div style="display:flex;justify-content:space-between">
						<div>
							<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
							<select name="departmentChangeID" style="width:154px; float:right;"><?php echo $departmentList ?></select>
							<input type="submit" name="moveDeptTo" value="Move selected to:" style="float:right;margin-left:10px;" />
						</div>
						<?php if ($totalPages > 1):
							include('pagination.php');
						endif; ?>
					</div>
					<br />

					<table border=1 width=100% cellpadding=1 cellspacing=1>
						<tr>
							<td rowspan=2></td>
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
								<font face=arial size=1>Stages Complete
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
						</tr>
						<?php
						$highestID = 0;
						if (mysqli_num_rows($selectOrderClientQuery) > 0)
							mysqli_data_seek($selectOrderClientQuery, 0);
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
							else if ($category == "CMD")
								$category = "PromoSouth";
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
									<font face=arial size=1><?php echo $artFilename; ?></font>
								</td>
								<td align="center" nowrap><input type="button" value="Edit" onclick='window.open("viewOrder.php?orderID=<?php echo $orderID; ?>&edit=1")' />&nbsp;&nbsp;<input name='editonly' type="button" value="View" onclick="window.open('viewOrder.php?orderID=<?php echo $orderID; ?>')"></td>
							</tr>
						<?php 	} ?>
					</table>
					<br />
					<input type="hidden" name="highestID" value="<?php echo $highestID; ?>" />

					<div style="display:flex;justify-content:space-between">
						<div>
							<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
							<select name="departmentChangeID" style="width:154px; float:right;"><?php echo $departmentList ?></select>
							<input type="submit" name="moveDeptTo" value="Move selected to:" style="float:right;margin-left:10px;" />
						</div>
						<?php if ($totalPages > 1):
							include('pagination.php');
						endif; ?>
					</div>
					<br />
			<?php
				}
			} ?>
		</form>
	</div>
	<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->

</html>