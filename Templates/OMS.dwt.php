<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" href="../styles.css" type="text/css" />
<script language="javascript" src="../oms.js"></script>
<script language="javascript" src="../CalendarPopup.js"></script>
<script language="javascript">
	var cal = new CalendarPopup("testdiv1");
	var calx = new CalendarPopup("testdiv2");
	var calxx = new CalendarPopup("testdiv3");
	var calxxx = new CalendarPopup("testdiv4");
	document.write(getCalendarStyles());
</script>
<!-- TemplateBeginEditable name="doctitle" -->
<title>OMS</title>
<!-- TemplateEndEditable -->
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>
<body>
<form action="oms.pl" method=post>
<img src="oms.gif" width='185px' height='84px' />
<div style="position:absolute; left:200px; top:30px;">
<input class="topNaviButton" type='submit' name='neworder' value='New Order Form' />
<input class="topNaviButton" type='submit' name='sortorders' value='Sort' />
<input class="topNaviButton" type='submit' name='searchorders' value='Search' />
<br />
<input class="topNaviButton" type='submit' name='department' value='Department' />
<input class="topNaviButton" type='submit' name='controlpanel' value='Control Panel' />
<input class="topNaviButton" type='submit' name='logout' value='Log Out' />
</div>
</form>
<!-- TemplateBeginEditable name="body" -->
<!-- TemplateEndEditable -->
</body>
</html>
