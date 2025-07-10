<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/OMS.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>OMS Control Panel</title>
</head>
<body>
<?php
if( isset( $_POST['orderType'] ))
{
	include( 'database.php' );
	if( $_POST['orderType'] != "All" )
	$queryString = "SELECT DISTINCT clients.name, clients.email FROM clients JOIN orders ON clients.id = orders.clientID WHERE orders.category = '".$_POST['orderType']."' AND clients.email IS NOT NULL AND clients.email != '' ORDER BY clients.name ASC";
	else
	$queryString = "SELECT clients.name, clients.email FROM clients JOIN orders ON clients.id = orders.clientID WHERE clients.email IS NOT NULL AND clients.email != '' ORDER BY clients.name ASC";
	$result = mysql_query($queryString) or die(mysql_error());

	$tsv  = array();
	$html = array();
	while($row = mysql_fetch_array($result, MYSQL_NUM))
	{
		$tsv[]  = implode("\t", $row);
		$html[] = "<tr><td>" .implode("</td><td>", $row) .              "</td></tr>";
	}

	$tsv = implode("\r\n", $tsv);
	$html = "<table>" . implode("\r\n", $html) . "</table>";

	/*
	$fileName = 'CMD.xls';
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$fileName");

	echo $tsv;
	*/

	echo $html;
}
?>
<form name="searchForm" method="post">
    <label>Download client email addresses: <select name="orderType" style="width:92px;margin-right:0px">
    <option value="LM-Dealer">LM-Dealer</option>
    <option value="Screen Print" <?php if( $_POST['orderType'] == "LM-Dealer" ) echo "selected=1"?>>Screen Print</option>
    <option value="Logo Magnet" <?php if( $_POST['orderType'] == "Screen Print" ) echo "selected=1"?>>Logo Magnet</option>
    <option value="Logo Ventures" <?php if( $_POST['orderType'] == "Logo Magnet" ) echo "selected=1"?>>Logo Ventures</option>
    <option value="CMD" <?php if( $_POST['orderType'] == "Logo Ventures" ) echo "selected=1"?>>CMD</option>
    <option value="Promotional" <?php if( $_POST['orderType'] == "CMD" ) echo "selected=1"?>>Promotional</option>
    <option value="Embroidery" <?php if( $_POST['orderType'] == "Promotional" ) echo "selected=1"?>>Embroidery</option>
    <option value="Other" <?php if( $_POST['orderType'] == "Embroidery" ) echo "selected=1"?>>Other</option>
    <option value="All" <?php if( $_POST['orderType'] == "Other" ) echo "selected=1"?>>All</option>
    </select>
<br/>
<input type="submit" value="Download" name='searchSubmit' style="width:154px">
</form>
</body>
</html>