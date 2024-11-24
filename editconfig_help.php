<?php
/*=================================================
	Project: phpGedView
	File: editconfig_help.php
	Author: John Finlay
	Comments:
		English Language Configure Help file for PHPGedView

	Change Log:
		5/1/03 - File Created

===================================================*/
# $Id: editconfig_help.php,v 1.12 2005/04/12 17:54:44 yalnifj Exp $

require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
require ("help_text_vars.php");
print_simple_header($pgv_lang["config_help"]);
print '<span class="helpheader">';
print_text("config_help");
print '</span><br /><br /><span class="helptext">';
if ($help == "help_contents_help") {
		if (userIsAdmin(getUserName())) {
		$help = "admin_help_contents_help";
		print_text("admin_help_contents_head_help");
	}
	else print_text("help_contents_head_help");
	print_help_index($help);
}
else {
	if ($help == "help_uploadgedcom.php") $help = "help_addgedcom.php";
print_text($help);
}
print "</span><br /><br />";
print "<a href=\"help_text.php?help=help_contents_help\"><b>";
print_text("help_contents");
print "</b></a><br />";
print "<a href=\"#\" onclick=\"window.close();\"><b>";
print_text("close_window");
print "</b></a>";
print_simple_footer();
?>