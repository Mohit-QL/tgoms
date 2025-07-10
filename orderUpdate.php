<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Update Orders</title>
</head>

<body>

	<?php
	include('database.php');
	$z = 0;
	for ($i = 2941; $i < 2943; $i++) {
		if ($i < 1000)
			$filename = "orders/000" . $i . ".order";
		else
			$filename = "orders/00" . $i . ".order";

		if (file_exists($filename)) {
			$file_handle = fopen($filename, "r");
			echo $filename . ": ";

			$name = getValue($file_handle);
			echo $name . "<br />";
			$contact = getValue($file_handle);
			$projectName = getValue($file_handle);
			$address = getValue($file_handle);
			$city = getValue($file_handle);
			$state = getValue($file_handle);
			$zip = getValue($file_handle);
			$phone = getValue($file_handle);
			$email = getValue($file_handle);
			$shippingClient = getValue($file_handle);
			$shippingContact = getValue($file_handle);

			if ($shippingClient != 'NULL' && $shippingClient != "''") {
				$updateOrderSql = "UPDATE orders SET shippingClient=" . $shippingClient . " WHERE id=" . $i;
				mysqli_query($conn, $updateOrderSql) or die(mysqli_error($conn));
			}
		}
	}

	function getValue($fh)
	{
		include('database.php');

		$line = fgets($fh);
		if (strlen($line) == 0)
			$line = fgets($fh);

		$parts = explode(':', $line);
		if (sizeof($parts) < 2 || trim($parts[1]) == "")
			return "NULL";

		$value = mysqli_real_escape_string($conn, trim($parts[1]));
		for ($k = 2; $k < sizeof($parts); $k++)
			$value .= ":" . mysqli_real_escape_string($conn, trim($parts[$k]));

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

		return "'" . $value . "'";
	}
	?>
</body>

</html>