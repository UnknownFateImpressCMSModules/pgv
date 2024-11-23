<?php
/*=================================================
	Project: phpGedView
	File: gedrecord.php
	Author: John Finlay
	Input Variables: $pid
	Comments:
		Parses gedcom file and displays record for given id in raw text

	Change Log:
		6/3/02 - File Created
===================================================*/
# $Id: gedrecord.php,v 1.19 2005/05/06 14:43:43 yalnifj Exp $

require("config.php");
header("Content-Type: text/html; charset=$CHARACTER_SET");
?>
<html>
	<head>
		<title><?php print "$pid Record"; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php print $CHARACTER_SET; ?>" />
	</head>
	<body>

<?php

if (!isset($pid)) $pid = "";
$pid = clean_input($pid);

$username = GetUserName();

if ((!$SHOW_GEDCOM_RECORD) && (!UserCanAccept($username))) {
	print "<span class=\"error\">This page has been disabled by the site administrator.</span>\n";
	print "</body></html>";
	exit;
}

if ((find_person_record($pid))&&(!displayDetailsByID($pid))) {
	print_privacy_error($CONTACT_EMAIL);
	print "</body></html>";
	exit;
}
if (!isset($fromfile)) $indirec = find_gedcom_record($pid);
else $indirec = find_record_in_file($pid);
$indirec = privatize_gedcom($indirec);
print "<pre>$indirec</pre>";
print "</body></html>";

?>