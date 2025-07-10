var categoryChosen = false;
var typeChosoen = false;
var numToDelete = 0;
var formName = 'mainform';
function js_array_to_php_array (a)
// This converts a javascript array to a string in PHP serialized format.
// This is useful for passing arrays to PHP. On the PHP side you can 
// unserialize this string from a cookie or request variable. For example,
// assuming you used javascript to set a cookie called "php_array"
// to the value of a javascript array then you can restore the cookie 
// from PHP like this:
//    <?php
//    session_start();
//    $my_array = unserialize(urldecode(stripslashes($_COOKIE['php_array'])));
//    print_r ($my_array);
//    ?>
// This automatically converts both keys and values to strings.
// The return string is not URL escaped, so you must call the
// Javascript "escape()" function before you pass this string to PHP.
{
    var a_php = "";
    var total = 0;
    for (var key in a)
    {
        ++ total;
        a_php = a_php + "s:" +
                String(key).length + ":\"" + String(key) + "\";s:" +
                String(a[key]).length + ":\"" + String(a[key]) + "\";";
    }
    a_php = "a:" + total + ":{" + a_php + "}";
    return a_php;
}

function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && document.all)? document.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}
	}
	return (arrReturnElements)
}

function addToDelete( id )
{
	if( document.forms['orderForm'].elements['check'+id].checked )
		numToDelete++;
	else	
		numToDelete--;
	return true;
}

function deleteMarkedOrders() {
	var checkboxes = document.querySelectorAll('.selector');
	var selectedIds = [];
	checkboxes.forEach(checkbox => {
		if (checkbox.checked) {
			var orderID = checkbox.name.replace('check', '');
			checkbox.checked = false;
			selectedIds.push(orderID);
		}
	});

	if (selectedIds.length == 0) {
		alert('Please select at least one order to delete');
		return false;
	}

	document.getElementById('ids').value = selectedIds.join(',');
	var deleteDiv = document.getElementById('deleteDiv');
	deleteDiv.style.visibility = 'visible';
	document.getElementById('numberToDelete').innerHTML = selectedIds.length;
}

function selectAll()
{
	var checkboxes = getElementsByClassName(document, 'input', 'selector');
	for( box in checkboxes )
		checkboxes[box].checked = true;
}

function checkAll()
{
	document.forms['navi'].elements['screenPrint'].checked = false;
	document.forms['navi'].elements['logoVentures'].checked = false;
	document.forms['navi'].elements['promotional'].checked = false;
	document.forms['navi'].elements['embroidery'].checked = false;
	document.forms['navi'].elements['other'].checked = false;
	return true;
}

function checkOther()
{
	document.forms['navi'].elements['allCats'].checked = false;
	return true;
}

function printSelected()
{
	var checkboxes = getElementsByClassName(document, 'input', 'selector');
	var ids = new Array();
	for( box in checkboxes )
	{
		if( checkboxes[box].checked )
			ids.push(checkboxes[box].name.substring(5));
	}
	
	if(ids.length == 0) {
		alert('Please select at least one order to print.');
		return false;
	}

	var phpIDs = escape(js_array_to_php_array(ids));
	window.open('printAll.php?ids='+phpIDs, 'print','status=1,width=200,height=100');
}

function printOrderByID( id )
{
	var ids = new Array();
	ids.push(id);
	var phpIDs = escape(js_array_to_php_array(ids));
	window.open('printAll.php?ids='+phpIDs, 'print','status=1,width=200,height=100');
}


function changebilldisplay ()
{
	/* Invoked when the user chooses a different client from the drop-down. AJAX */
}

/* User checked the "Same as billing" checkbox */
function sameshipdisplay ()
{
	if( !document.forms[formName].elements['samebilling'].checked )
	{
	  document.forms[formName].elements['shippingClient'].value = "";
	  document.forms[formName].elements['shippingContact'].value = "";
	  document.forms[formName].elements['shippingAddress'].value = "";
	  document.forms[formName].elements['shippingCity'].value = "";
	  document.forms[formName].elements['shippingState'].value = "";
	  document.forms[formName].elements['shippingZip'].value = "";
	}
	else
	{
	  document.forms[formName].elements['shippingClient'].value = document.forms[formName].elements['name'].value; 
	  document.forms[formName].elements['shippingContact'].value = document.forms[formName].elements['contact'].value;
	  document.forms[formName].elements['shippingAddress'].value = document.forms[formName].elements['address'].value;
	  document.forms[formName].elements['shippingCity'].value = document.forms[formName].elements['city'].value;
	  document.forms[formName].elements['shippingState'].value = document.forms[formName].elements['state'].value;
	  document.forms[formName].elements['shippingZip'].value = document.forms[formName].elements['zip'].value;
	}
}

/* User clicked an initial button */
function initialButton( buttonName )
{
	var sides = buttonName.split(",");
	var existingInitials = document.forms[formName].elements[sides[0]].value;
	if( existingInitials != sides[1] )
	{
		document.forms[formName].elements[sides[0]].value = sides[1];
		if( sides[0] == "artistApproved" )
			document.forms[formName].elements["artistApproved2"].value = sides[1];
		document.forms[formName].elements[sides[0]+"_"].value = sides[1];
	}
	else
	{
		document.forms[formName].elements[sides[0]].value = " ";
		if( sides[0] == "artistApproved" )
			document.forms[formName].elements["artistApproved2"].value = " ";
		document.forms[formName].elements[sides[0]+"_"].value = "";
	}
}

function maketotalswith(fn)
{
	formName = fn;
	maketotals();
}
function maketotals()
{
	// let max = parseInt(localStorage.getItem('orderItems')) || 0;
	var subTotal = 0;

	var container = document.getElementById('totalContainer');
	var inputs = container.getElementsByTagName('input');

	var numbers = []; // Array to store extracted numbers

	for (var i = 0; i < inputs.length; i++) {
		var name = inputs[i].name;
		if (name) {
			var match = name.match(/^(\d+)total$/); // Match the number in the name
			if (match) {
				numbers.push(parseInt(match[1])); // Add the number to the array
			}
		}
	}

	numbers.forEach((i) => 
	{
		document.forms[formName].elements[i+'quantity'].value = (1 * (document.forms[formName].elements[i+'yxs'].value)) + (1 * document.forms[formName].elements[i+'ys'].value) + (1 * document.forms[formName].elements[i+'ym'].value) + (1 * document.forms[formName].elements[i+'yl'].value) + (1 * document.forms[formName].elements[i+'yxl'].value) + (1 * document.forms[formName].elements[i+'s'].value) + (1 * document.forms[formName].elements[i+'m'].value) + (1 * document.forms[formName].elements[i+'l'].value) + (1 * document.forms[formName].elements[i+'xl'].value) + (1 * document.forms[formName].elements[i+'xxl'].value) + (1 * document.forms[formName].elements[i+'xxxl'].value) + (1 * document.forms[formName].elements[i+'xxxxl'].value) + (1 * document.forms[formName].elements[i+'misc'].value);

		if( document.forms[formName].elements[i+'quantity'].value == 0)
			document.forms[formName].elements[i+'quantity'].value = '';
		if( document.forms[formName].elements[i+'yxs'].value == 0)
			document.forms[formName].elements[i+'yxs'].value = '';
		if( document.forms[formName].elements[i+'ys'].value == 0)
			document.forms[formName].elements[i+'ys'].value = '';
		if( document.forms[formName].elements[i+'ym'].value == 0)
			document.forms[formName].elements[i+'ym'].value = '';
		if( document.forms[formName].elements[i+'yl'].value == 0)
			document.forms[formName].elements[i+'yl'].value = '';
		if( document.forms[formName].elements[i+'yxl'].value == 0)
			document.forms[formName].elements[i+'yxl'].value = '';
		if( document.forms[formName].elements[i+'s'].value == 0)
			document.forms[formName].elements[i+'s'].value = '';
		if( document.forms[formName].elements[i+'m'].value == 0)
			document.forms[formName].elements[i+'m'].value = '';
		if( document.forms[formName].elements[i+'l'].value == 0)
			document.forms[formName].elements[i+'l'].value = '';
		if( document.forms[formName].elements[i+'xl'].value == 0)
			document.forms[formName].elements[i+'xl'].value = '';
		if( document.forms[formName].elements[i+'xxl'].value == 0)
			document.forms[formName].elements[i+'xxl'].value = '';
		if( document.forms[formName].elements[i+'xxxl'].value == 0)
			document.forms[formName].elements[i+'xxxl'].value = '';
		if( document.forms[formName].elements[i+'xxxxl'].value == 0)
			document.forms[formName].elements[i+'xxxxl'].value = '';
		if( document.forms[formName].elements[i+'misc'].value == 0)
			document.forms[formName].elements[i+'misc'].value = '';
	})
  
	numbers.forEach((i) =>  {
		formatcurrency ( i+'total', document.forms[formName].elements[i+'price'].value * document.forms[formName].elements[i+'quantity'].value);
	});

	numbers.forEach((i) => {
		let value = document.forms[formName].elements[i + 'total'].value;
		let numericValue = parseFloat(value) || 0;
		subTotal += numericValue;
	});

	formatcurrency ( 'subtotal' , subTotal );

  if (document.forms[formName].elements['salestaxradio'].checked) { formatcurrency ( 'salesTax', document.forms[formName].elements['subtotal'].value * 0.06); }
  else                                                  	    { document.forms[formName].elements['salesTax'].value = ''; }


  formatcurrency( 'screenTotal', document.forms[formName].elements['screenNumber'].value * document.forms[formName].elements['screenCharge'].value);
formatcurrency( 'artTotal', document.forms[formName].elements['artNumber'].value * document.forms[formName].elements['artCharge'].value);

  formatcurrency ( 'total', (1 * document.forms[formName].elements['subtotal'].value) + (1 * document.forms[formName].elements['salesTax'].value) + (1 * document.forms[formName].elements['screenTotal'].value) + (1 * document.forms[formName].elements['dieCharge'].value) + (1 * document.forms[formName].elements['artTotal'].value) + (1 * document.forms[formName].elements['colorCharge'].value) + (1 * document.forms[formName].elements['shippingCharge'].value) + (1 * document.forms[formName].elements['miscCharge'].value));

  formatcurrency ( 'balance', (1 * document.forms[formName].elements['total'].value) - (1 * document.forms[formName].elements['deposit'].value) );

  numbers.forEach((i) => {
  	formatcurrency ( i+'price' , 0 + 1 * document.forms[formName].elements[i+'price'].value );
  });

  formatcurrency ( 'screenTotal' , 0 + 1 * document.forms[formName].elements['screenTotal'].value );
  formatcurrency ( 'dieCharge' , 0 + 1 * document.forms[formName].elements['dieCharge'].value );
  formatcurrency ( 'artTotal' , 0 + 1 * document.forms[formName].elements['artTotal'].value );
  formatcurrency ( 'colorCharge' , 0 + 1 * document.forms[formName].elements['colorCharge'].value );
  formatcurrency ( 'shippingCharge' , 0 + 1 * document.forms[formName].elements['shippingCharge'].value );
  formatcurrency ( 'miscCharge' , 0 + 1 * document.forms[formName].elements['miscCharge'].value );
  formatcurrency ( 'deposit' , 0 + 1 * document.forms[formName].elements['deposit'].value );
}

function formatcurrency (formid, formval) {
  formval = '' + formval;
  var decimal = formval.indexOf('.');
  var predec = formval.substring(0,decimal);
  var postdec = formval.substring(decimal+1);

  if      (predec.length + postdec.length == formval.length) { formval = formval + '.00'; }
  else if (postdec.length == 1) { formval = formval + '0'; }
  else if (postdec.length > 2)  { formval = (Math.round(100 * formval * 1)) / 100; }

  if( formval == 0 )
    document.forms[formName].elements[formid].value = '';
  else
  	document.forms[formName].elements[formid].value = formval + '';
}

var dtCh= "/";

function isInteger(s){
    var i;
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    return true;
}

function stripCharsInBag(s, bag){
    var i;
    var returnString = "";
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length == 2){
          year = 2000 + (year * 1);
	}
	else if (strYear.length != 4 || year==0 || year < 2006 || year > 2100){
		alert("Please enter a valid 4 digit year.")
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}

        if ( day < 10 ) { day = '' + 0 + day }
        if ( month < 10 ) { month = month = '' + 0 + month }

	return true
}

function ValidateDate(formid){
	if (isDate(document.forms[formName].elements[formid].value)==false){
                document.forms[formName].elements[formid].value = '';
		document.forms[formName].elements[formid].focus();
		return false
	}
    document.forms[formName].elements[formid].value = month + '/' + day + '/' + year;
    return true
}

function DateChange (dateid) {
  var tempvar = document.forms[formName].elements[dateid].value;
  var pos1=tempvar.indexOf(dtCh);
  var pos2=tempvar.indexOf(dtCh,pos1+1);
  var strDay = '';
  var strMonth = '';

  if (pos1 > 0) {
    var strMonth=tempvar.substring(0,pos1);
    if (pos2 > 0) { var strDay=tempvar.substring(pos1+1,pos2); }
    else          {
      var strDay=tempvar.substring(pos1+1);
      if ( strDay.length == 2 || (strDay.length == 1 && (strDay * 1) > 3) ) { document.forms[formName].elements[dateid].value += dtCh; }
    }
  }
  else if ( (tempvar.length == 2 && strMonth < 1) || ((tempvar * 1) > 1 && tempvar.length == 1 && strMonth < 1 ) ) { document.forms[formName].elements[dateid].value += dtCh; }
}