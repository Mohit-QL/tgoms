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
	<title>OMS - Search</title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<!-- InstanceEndEditable -->
</head>

<body>
	<!-- InstanceBeginEditable name="topbody" -->
	<!-- InstanceEndEditable -->
	<?php include('header.php'); ?>
	<!-- InstanceBeginEditable name="body" -->
	<form name="searchForm" method="post">
		<div style="position:relative; top:20px; text-align:center; width:99%">
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
				echo '<span style="color:darkgreen">' . $count . ' orders deleted</span><br /><br />';
			}
			?>
			<div style="width:250px; margin:auto; text-align:right">
				<label>Search: <input name="searchString" style="width: 150px; margin-right:2px" class="text" value="<?php echo $_POST['searchString'] ?? ''; ?>"></label><br />
				<label>Search by: <select name="filter" style="width: 154px; text-align: left;">
						<option value="name">Client Name</option>
						<option value="contact" <?php if (($_POST['filter'] ?? '') == "contact") echo "selected=1" ?>>Contact Name</option>
						<option value="email" <?php if (($_POST['filter'] ?? '') == "email") echo "selected=1" ?>>Email Address</option>
						<option value="projectName" <?php if (($_POST['filter'] ?? '') == "projectName") echo "selected=1" ?>>Project Name</option>
						<option value="orderNumber" <?php if (($_POST['filter'] ?? '') == "orderNumber") echo "selected=1" ?>>Order Number</option>
						<option value="missingValue" <?php if (($_POST['filter'] ?? '') == "missingValue") echo "selected=1" ?>>Missing Value</option>
						<option value="purchaseNumber" <?php if (($_POST['filter'] ?? '') == "purchaseNumber") echo "selected=1" ?>>Purchase Order Number</option>
						<option value="keyword" <?php if (($_POST['filter'] ?? '') == "keyword") echo "selected=1" ?>>Keyword</option>
					</select></label><br />

				<label>Order Status: <select name="filter2" style="width:154px;">
						<option value="all">All Orders</option>
						<option value="active" <?php if (($_POST['filter2'] ?? '') == "active") echo "selected=1" ?>>Active Orders</option>
						<option value="archive" <?php if (($_POST['filter2'] ?? '') == "archive") echo "selected=1" ?>>Archived Orders</option>
						<option value="stageComplete" <?php if (($_POST['filter2'] ?? '') == "stageComplete") echo "selected=1" ?>>Completed Orders</option>
						<option value="stageBilled" <?php if (($_POST['filter2'] ?? '') == "stageBilled") echo "selected=1" ?>>Billed Orders</option>
						<option value="stagePaid" <?php if (($_POST['filter2'] ?? '') == "stagePaid") echo "selected=1" ?>>Paid Orders</option>
					</select></label><br />

				<!-- <label>Display Order:
					<select name="sort_order" style="width:154px;">
						<option value="ASC" <?php if (($_POST['sort_order'] ?? '') == "ASC") echo "selected"; ?>>Ascending</option>
						<option value="DESC" <?php if (($_POST['sort_order'] ?? '') == "DESC") echo "selected"; ?>>Descending</option>
					</select></label><br /> -->

				<br />
				<label>Start Date: <select name="filterMonth" style="width:92px;margin-right:0px">
						<option value="any">Any Month</option>
						<option value="01" <?php if (($_POST['filterMonth'] ?? '') == "01") echo "selected=1" ?>>January</option>
						<option value="02" <?php if (($_POST['filterMonth'] ?? '') == "02") echo "selected=1" ?>>February</option>
						<option value="03" <?php if (($_POST['filterMonth'] ?? '') == "03") echo "selected=1" ?>>March</option>
						<option value="04" <?php if (($_POST['filterMonth'] ?? '') == "04") echo "selected=1" ?>>April</option>
						<option value="05" <?php if (($_POST['filterMonth'] ?? '') == "05") echo "selected=1" ?>>May</option>
						<option value="06" <?php if (($_POST['filterMonth'] ?? '') == "06") echo "selected=1" ?>>June</option>
						<option value="07" <?php if (($_POST['filterMonth'] ?? '') == "07") echo "selected=1" ?>>July</option>
						<option value="08" <?php if (($_POST['filterMonth'] ?? '') == "08") echo "selected=1" ?>>August</option>
						<option value="09" <?php if (($_POST['filterMonth'] ?? '') == "09") echo "selected=1" ?>>September</option>
						<option value="10" <?php if (($_POST['filterMonth'] ?? '') == "10") echo "selected=1" ?>>October</option>
						<option value="11" <?php if (($_POST['filterMonth'] ?? '') == "11") echo "selected=1" ?>>November</option>
						<option value="12" <?php if (($_POST['filterMonth'] ?? '') == "12") echo "selected=1" ?>>December</option>
					</select></label>

				<select name="filterYear" style="width:60px;margin-left:0px">
					<option value="any">Any</option>
					<?php
					$currentYear = date("Y");
					while ($currentYear > 2006) {
						echo "<option value='$currentYear'";
						if (isset($_POST['filterYear']) && ($_POST['filterYear'] == $currentYear))
							echo " selected=1";
						echo ">$currentYear</option>\n";
						$currentYear--;
					}
					?>
				</select><br />


				<label>End Date: <select name="filterMonth2" style="width:92px;margin-right:0px">
						<option value="any">Any Month</option>
						<option value="01" <?php if (($_POST['filterMonth2'] ?? '') == "01") echo "selected=1" ?>>January</option>
						<option value="02" <?php if (($_POST['filterMonth2'] ?? '') == "02") echo "selected=1" ?>>February</option>
						<option value="03" <?php if (($_POST['filterMonth2'] ?? '') == "03") echo "selected=1" ?>>March</option>
						<option value="04" <?php if (($_POST['filterMonth2'] ?? '') == "04") echo "selected=1" ?>>April</option>
						<option value="05" <?php if (($_POST['filterMonth2'] ?? '') == "05") echo "selected=1" ?>>May</option>
						<option value="06" <?php if (($_POST['filterMonth2'] ?? '') == "06") echo "selected=1" ?>>June</option>
						<option value="07" <?php if (($_POST['filterMonth2'] ?? '') == "07") echo "selected=1" ?>>July</option>
						<option value="08" <?php if (($_POST['filterMonth2'] ?? '') == "08") echo "selected=1" ?>>August</option>
						<option value="09" <?php if (($_POST['filterMonth2'] ?? '') == "09") echo "selected=1" ?>>September</option>
						<option value="10" <?php if (($_POST['filterMonth2'] ?? '') == "10") echo "selected=1" ?>>October</option>
						<option value="11" <?php if (($_POST['filterMonth2'] ?? '') == "11") echo "selected=1" ?>>November</option>
						<option value="12" <?php if (($_POST['filterMonth2'] ?? '') == "12") echo "selected=1" ?>>December</option>

					</select></label>

				<select name="filterYear2" style="width:60px;margin-left:0px">
					<option value="any">Any</option>
					<?php
					$currentYear = date("Y");
					while ($currentYear > 2006) {
						echo "<option value='$currentYear'";
						if (isset($_POST['filterYear2']) && ($_POST['filterYear2'] == $currentYear))
							echo " selected=1";
						echo ">$currentYear</option>\n";
						$currentYear--;
					}
					?>
				</select><br /><br />

			</div>
			<input type="submit" value="Display Search Results" name='searchSubmit' style="width:154px">
		</div>
		<div style="position:relative; top:30px; width:99%; text-align:center">
			<?php
			if (isset($_POST['searchString'])) {
				$resultsPerPage = 100;
				if (isset($_POST['display']) && !isset($_POST['searchSubmit'])) {
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

				$selectOrderClientSql = "SELECT SQL_CALC_FOUND_ROWS projectName, orders.id as id, category, type, DATE_FORMAT(dueDate,'%m/%d/%Y') as sortDate, artFilename, stageOrderGoods, stageGoodsReceived, artistApproved, stageOrderStaging, stagePrintingStitching, stageComplete, stageBilled, stagePaid, name FROM orders INNER JOIN clients ON orders.clientID = clients.id WHERE orders.visible = 1 ";
				if (!empty($_POST['filter'])) {

					//$sortOrder = $_POST['sort_order'];

					switch ($_POST['filter']) {
						case "name":
							$selectOrderClientSql .= "AND clients.name LIKE '%" . mysqli_real_escape_string($conn, $_POST['searchString']) . "%' ";
							//$orderBy = "clients.name $sortOrder";
							break;

						case "contact":
							$selectOrderClientSql .= "AND clients.contact LIKE '%" . mysqli_real_escape_string($conn, $_POST['searchString']) . "%' ";
							//$orderBy = "clients.contact $sortOrder";
							break;

						case "email":
							$selectOrderClientSql .= "AND clients.email LIKE '%" . mysqli_real_escape_string($conn, $_POST['searchString']) . "%' ";
							//$orderBy = "clients.email $sortOrder";
							break;

						case "projectName":
							$selectOrderClientSql .= "AND orders.projectName LIKE '%" . mysqli_real_escape_string($conn, $_POST['searchString']) . "%' ";
							//$orderBy = "orders.projectName $sortOrder";
							break;

						case "orderNumber":
							$selectOrderClientSql .= "AND orders.id = " . (int)$_POST['searchString'] . " ";
							//$orderBy = "orders.id $sortOrder";
							break;

						case "missingValue":
							$selectOrderClientSql .= "AND (orders.type = '' OR orders.category = '' OR orders.artDueDate IS NULL OR artDueDate = '0000-00-00' OR dueDate IS NULL OR dueDate = '0000-00-00') ";
							//$orderBy = "orders.id $sortOrder";
							break;

						case "keyword":
							$searchString = trim($_POST['searchString']);

							$dateInput = false;

							if (DateTime::createFromFormat('m/d/Y', $searchString) !== false) {
								$dateInput = DateTime::createFromFormat('m/d/Y', $searchString);
							} elseif (DateTime::createFromFormat('Y-m-d', $searchString) !== false) {
								$dateInput = DateTime::createFromFormat('Y-m-d', $searchString);
							}

							if ($dateInput) {
								$formattedDate = $dateInput->format('Y-m-d');
								$selectOrderClientSql .= "
															AND (
																DATE(orders.orderDate) = '$formattedDate' OR
																DATE(orders.artDueDate) = '$formattedDate' OR
																DATE(orders.printDate) = '$formattedDate' OR
																DATE(orders.dueDate) = '$formattedDate'
															)
														";
							} else {
								$searchStringEscaped = mysqli_real_escape_string($conn, $searchString);
								$selectOrderClientSql .= "
															AND (
																clients.name LIKE '%$searchStringEscaped%' OR
																clients.contact LIKE '%$searchStringEscaped%' OR
																clients.email LIKE '%$searchStringEscaped%' OR
																orders.projectName LIKE '%$searchStringEscaped%' OR
																orders.type LIKE '%$searchStringEscaped%' OR
																orders.category LIKE '%$searchStringEscaped%' OR
																orders.poNo LIKE '%$searchStringEscaped%' OR
																orders.id LIKE '%$searchStringEscaped%' 

															)
														";
							}
							break;


						case "purchaseNumber":
							$selectOrderClientSql .= "AND orders.poNo LIKE '%" . mysqli_real_escape_string($conn, $_POST['searchString']) . "%' ";
							break;
					}
				}

				$sortType = $_POST['filter2'];

				if (!empty($_POST['filter2'])) {
					switch ($_POST['filter2']) {
						case "archive":
							$selectOrderClientSql .= "AND orders.type = 'Archive' ";
							break;
						case "active":
							$selectOrderClientSql .= "AND orders.type != 'Archive' AND orders.type != 'Inactive' 
											 AND ((orders.stageComplete IS NULL OR orders.stageComplete = '') 
											 OR (orders.stageBilled = '' OR orders.stageBilled IS NULL) 
											 OR (orders.stagePaid IS NULL OR orders.stagePaid = '')) ";
							break;
						case "stageBilled":
							$selectOrderClientSql .= "AND (orders.stageBilled IS NOT NULL AND orders.stageBilled != '') 
											 AND (orders.stagePaid IS NULL OR orders.stagePaid = '') ";
							break;
						case "stagePaid":
							$selectOrderClientSql .= "AND orders.stagePaid IS NOT NULL AND orders.stagePaid != '' ";
							break;
						case "stageComplete":
							$selectOrderClientSql .= "AND orders.stageComplete IS NOT NULL AND orders.stageComplete != '' 
											 AND (orders.stageBilled = '' OR orders.stageBilled IS NULL) 
											 AND (orders.stagePaid IS NULL OR orders.stagePaid = '') ";
							break;
					}
				}

				if (!empty($_POST['filterMonth']) && $_POST['filterMonth'] != "any") {
					$sortMonth = (int)$_POST['filterMonth'];
					$sortMonth2 = (int)$_POST['filterMonth2'];
					$selectOrderClientSql .= "AND (MONTH(orders.dueDate) BETWEEN '$sortMonth' AND '$sortMonth2') ";
				}

				if (!empty($_POST['filterYear']) && $_POST['filterYear'] != "any") {
					$sortYear = (int)$_POST['filterYear'];
					$sortYear2 = (int)$_POST['filterYear2'];
					$selectOrderClientSql .= "AND (YEAR(orders.dueDate) BETWEEN '$sortYear' AND '$sortYear2') ";
				}

				$selectOrderClientSql .= "ORDER BY dueDate ASC LIMIT " . $displayNumber . " , 100";
				//$selectOrderClientSql .= "ORDER BY $orderBy LIMIT " . $displayNumber . " , 100";


				$selectOrderClientQuery = mysqli_query($conn,  $selectOrderClientSql) or die($selectOrderClientSql . '<br/><br/><br/>' . mysqli_error($conn));
				$selectOrderClientRowSql = "SELECT FOUND_ROWS();";
				$selectOrderClientRowQuery = mysqli_query($conn, $selectOrderClientRowSql) or die(mysqli_error($conn));
				$result = mysqli_fetch_array($selectOrderClientRowQuery);

				$totalResults = (int)$result[0];

				if ($totalResults < 1)
					echo "<center>[No Matches]</center>";
				else {
					echo "<center>";
					if ($totalResults > 100)
						echo "Displaying " . ($displayNumber + 1) . " - " . $endDisplayNumber . " of " . $totalResults . " matches<br/>";
					else
						echo $totalResults . " matches";
					echo "</center>";
			?>
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
							<?php if ($_SESSION['initials'] == 'TT') { ?><input type="button" name="deleteButton" style="margin-left:10px;" value="Delete selected orders" onclick="deleteMarkedOrders();" /><?php } ?>
							<input type="button" name="printOrders" value="Print selected" style="margin-left:10px;" onclick="printSelected()" />
						</div>
						<?php if ($totalPages > 1):
							include('pagination.php');
						endif; ?>
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
									<font face=arial size=1><?php echo $artFilename; ?>
								</td>
								<td align="center" nowrap><input type="button" value="Edit" onclick='window.open("viewOrder.php?orderID=<?php echo $orderID; ?>&edit=1")' />&nbsp;&nbsp;<input name='editonly' type="button" value="View" onclick="window.open('viewOrder.php?orderID=<?php echo $orderID; ?>')"></td>
							</tr>
						<?php 	}
						$ids = urlencode(serialize($ids)); ?>
					</table>
					<input type="hidden" name="highestID" value="<?php echo $highestID; ?>" />
					<input type="hidden" name="ids" value="<?php echo $ids; ?>"><br />
					<div id="deleteDiv" style="position:fixed; left:10px; bottom:10px; width:200px; height:75px; visibility:hidden; background-color:#CCCCCC; border:3px solid #000000; padding:4px">
						Are you sure you want to delete these <span id="numberToDelete">9</span> orders?<br /><br />
						<input type="button" value="No" onclick="document.getElementById('deleteDiv').style.visibility='hidden';" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="deleteOrders" value="Yes" />
					</div>

					<div style="display:flex;justify-content:space-between">
						<div>
							<input type="button" name="selectAllMarks" value="Select all" style="float:left" onclick="selectAll()" />
							<?php if ($_SESSION['initials'] == 'TT') { ?><input type="button" name="deleteButton" style="margin-left:10px;" value="Delete selected orders" onclick="deleteMarkedOrders();" /><?php } ?>
							<input type="button" name="printOrders" value="Print selected" style="margin-left:10px;" onclick="printSelected()" />
						</div>
						<?php if ($totalPages > 1):
							include('pagination.php');
						endif; ?>
					</div>
					<br /><br />
	</form>
<?php
				}
			} ?>
<script type="text/javascript">
	function deleteMarkedOrders() {
		var numToDelete = 0;
		var checkboxes = getElementsByClassName(document, 'input', 'selector');
		for (box in checkboxes) {
			if (checkboxes[box].checked)
				numToDelete++;
		}
		var deleteDiv = document.getElementById('deleteDiv');
		deleteDiv.style.visibility = 'visible';
		document.getElementById('numberToDelete').innerHTML = numToDelete;
	}
</script>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->

</html>