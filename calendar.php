<?php
# PHP Calendar (version 2.3), written by Keith Devens
# http://keithdevens.com/software/php_calendar
#  see example at http://keithdevens.com/weblog
# Edited by Elija McPherson-Burke for use on the TGOMS site
# License: http://keithdevens.com/software/license

function generate_calendar($year, $month, $o_query, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array())
{
	$first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
	#remember that mktime will automatically correct if invalid dates are entered
	# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	# this provides a built in "rounding" feature to generate_calendar()

	$time = time();
	$today = date('j', $time);
	$tomonth = date('n', $time);

	$day_names = array(); #generate all the day names according to the current locale
	for ($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t += 86400) #January 4, 1970 was a Sunday
		$day_names[$n] = ucfirst(gmstrftime('%A', $t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)) . '&nbsp;' . $year;  #note that some locales don't capitalize month and day names

	#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn);
	@list($n, $nl) = each($pn); #previous and next links, if applicable
	if ($p) $p = '<span class="calendar-prev">' . ($pl ? '<a href="' . htmlspecialchars($pl) . '">' . $p . '</a>' : $p) . '</span>&nbsp;';
	if ($n) $n = '&nbsp;<span class="calendar-next">' . ($nl ? '<a href="' . htmlspecialchars($nl) . '">' . $n . '</a>' : $n) . '</span>';
	$calendar = '<table class="calendar">' . "\n" .
		'<caption class="calendar-month">' . $p . ($month_href ? '<a href="' . htmlspecialchars($month_href) . '">' . $title . '</a>' : $title) . $n . "</caption>\n<tr>";

	if ($day_name_length) { #if the day names should be shown ($day_name_length > 0)
		#if day_name_length is >3, the full name of the day will be printed
		foreach ($day_names as $d)
			$calendar .= '<th abbr="' . htmlentities($d) . '">' . htmlentities($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</th>';
		$calendar .= "</tr>\n<tr>";
	}

	if ($weekday > 0) $calendar .= '<td colspan="' . $weekday . '">&nbsp;</td>'; #initial 'empty' days
	for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {
		if ($weekday == 7) {
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}

		$lastRow = 0;
		$readyCount = 0;
		$orderCount = 0;
		$tooFar = false;
		while (($result = mysqli_fetch_array($o_query)) && !$tooFar) {
			$type = $result['type'];
			$date = explode("/", $result['sortDate']);
			$a_month = $date[0];
			$a_day = $date[1];
			$a_year = $date[2];
			if ($a_year > $year || ($a_year == $year && ($a_month > $month || ($a_month == $month && $a_day > $day)))) {
				mysqli_data_seek($o_query, $lastRow);
				$lastRow--;
				$tooFar = true;
			} else if ($a_year == $year && $a_month == $month && $a_day == $day) {
				if ($type == "Ready to Print")
					$readyCount++;
				else if ($type == "Order")
					$orderCount++;
			}
			$lastRow++;
		}

		if ($tomonth == $month && $today == $day)
			$p_day = "<span class='today-span'>$day</span>";
		else
			$p_day = $day;

		if ($readyCount == 0)
			$readyCount = "";
		if ($orderCount == 0)
			$orderCount = "";

		if (isset($days[$day]) and is_array($days[$day])) {
			@list($link, $classes, $content) = $days[$day];
			if (is_null($content))  $content  = $day;
			$calendar .= '<td' . ($classes ? ' class="' . htmlspecialchars($classes) . '">' : '>') .
				($link ? '<a href="' . htmlspecialchars($link) . '">' . $content . '</a>' : $content) . '</td>';
		} else $calendar .= "<td>$p_day<div class='cal-nums'><span class='top-cal-num'>$orderCount</span>\n<span class='bottom-cal-num'>$readyCount</span></span></td>";
	}
	if ($weekday != 7) $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>'; #remaining "empty" days

	return $calendar . "</tr>\n</table>\n";
}
