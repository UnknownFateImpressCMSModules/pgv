<?php
/*=================================================
   Project:	phpGedView
   File:	help_text.php
   Author:	John Finlay
   Comments:	English Help_text file for PHPGedView
   Change Log:	2004-01-06 - File Created
===================================================*/
# $Id: help_text.php,v 1.1 2005/10/07 18:08:01 skenow Exp $

require "config.php";
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];

if (!isset($help)) $help = "";

require ("help_text_vars.php");
print_simple_header($pgv_lang["help_header"]);
print "<a name=\"top\"></a><span class=\"helpheader\">".$pgv_lang["help_header"]."</span><br /><br />\n<div class=\"helptext\">\n";
$actione = "";
if (isset($action)) $actione = $action;
if (($help == "help_useradmin.php")&& ($actione == "edituser")) $help = "edit_useradmin_help";
if (($help == "help_login_register.php")&& ($actione == "pwlost")) $help = "help_login_lost_pw.php";
if ($help == "help_contents_help") {
	if (userIsAdmin(getUserName())) {
		$help = "admin_help_contents_help";
		print $pgv_lang["admin_help_contents_head_help"];
	}
	else print $pgv_lang["help_contents_head_help"];
	print_help_index($help);
}
else print_text($help);
print "\n</div>\n<br /><br /><br />";
print "<a href=\"#top\" title=\"".$pgv_lang["move_up"]."\">$UpArrow</a><br />";
print "<a href=\"help_text.php?help=help_contents_help\"><b>".$pgv_lang["help_contents"]."</b></a><br />";
print "<a href=\"#\" onclick=\"window.close();\"><b>".$pgv_lang["close_window"]."</b></a>";
print_simple_footer();
?>