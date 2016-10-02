<?php
/**
 * Report Engine
 *
 * Processes PGV XML Reports and generates a report
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package PhpGedView
 * @subpackage Reports
 * @version $Id: reportengine.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require("config.php");
require("includes/functions_charts.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);
@set_time_limit($TIME_LIMIT*2);
function get_tag_values($tag) {
	global $tags, $values;

	$indexes = $tags[$tag];
	$vals = array();
	foreach($indexes as $indexval => $i) {
		$vals[] = $values[$i];
	}
	return $vals;
}

if (empty($action)) $action = "choose";
if (!isset($report)) $report = "";
if (!isset($output)) $output = "PDF";
if (!isset($vars)) $vars = array();
if (!isset($varnames)) $varnames = array();
if (!isset($type)) $type = array();

$newvars = array();
foreach($vars as $name=>$var) {
	$newvars[$name]["id"] = clean_input($var);
	if (!empty($type[$name]) && (($type[$name]=="INDI")||($type[$name]=="FAM")||($type[$name]=="SOUR"))) {
		$gedcom = find_gedcom_record($var);
		if (empty($gedcom)) $action="setup";
		$newvars[$name]["gedcom"] = $gedcom;
	}
}
$vars = $newvars;

foreach($varnames as $indexval => $name) {
	if (!isset($vars[$name])) {
		$vars[$name]["id"] = "";
	}
}

$reports = get_report_list();
if (!empty($report)) {
	$r = basename($report);
	if (!isset($reports[$r]["access"])) $action = "choose";
	else if ($reports[$r]["access"]<getUserAccessLevel(getUserName())) $action = "choose";
}

//-- choose a report to run
if ($action=="choose") {
	$reports = get_report_list(true);
	print_header($pgv_lang["choose_report"]);

	print "<br /><br /><div class=\"center\">\n";
	print "<form name=\"choosereport\" method=\"get\" action=\"reportengine.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"setup\" />\n";
	print "<input type=\"hidden\" name=\"output\" value=\"$output\" />\n";
	print "<h2>".$pgv_lang["choose_report"]."</h2>\n";
	print "<select name=\"report\">\n";
	foreach($reports as $file=>$report) {
		print "<option value=\"".$report["file"]."\">".$report["title"][$LANGUAGE]."</option>\n";
	}
	print "</select>\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["select_report"]."\" />\n";
	print "</form></div>\n";
	print "<br /><br />\n";

	print_footer();
}

//-- setup report to run
else if ($action=="setup") {
	print_header($pgv_lang["enter_report_values"]);
	//-- make sure the report exists
	if (!file_exists($report)) {
		print "<span class=\"error\">The specified report cannot be found</span>\n";
	}
	else {
		require_once("includes/reportheader.php");
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		//-- open the file
		if (!($fp = fopen($report, "r"))) {
		   die("could not open XML input");
		}
		//-- read the file and parse it 4kb at a time
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
			}
		}
		xml_parser_free($xml_parser);

		?>
<script type="text/javascript">
<!--
var pasteto;
function iopenfind(textbox) {
	pasteto = textbox;
	findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
}
function fopenfind(textbox) {
	pasteto = textbox;
	findwin = window.open('findfamily.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
}
function popenfind(textbox) {
	pasteto = textbox;
	findwin = window.open('findplace.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
}
function paste_id(value) {
	pasteto.value=value;
}
//-->
</script>
		<?php
		print "<br /><br /><div class=\"center\">\n";
		print "<h2>".$pgv_lang["enter_report_values"]."</h2>\n";
		print '<script type="text/javascript" src="CalendarPopup.js"></script>';
		print '<script type="text/javascript">document.write(getCalendarStyles());</script>';
		print "<form name=\"setupreport\" method=\"get\" action=\"reportengine.php\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"run\" />\n";
		print "<input type=\"hidden\" name=\"report\" value=\"$report\" />\n";
		print "<input type=\"hidden\" name=\"output\" value=\"PDF\" />\n";
		/* -- this will allow user to select future output formats
		print "<select name=\"output\">\n";
		print "<option value=\"HTML\">HTML</option>\n";
		print "<option value=\"PDF\">PDF</option>\n";
		print "</select><br />\n";
		*/
		print "<table class=\"$TEXT_DIRECTION\">";
		print "<tr><td>".$pgv_lang["selected_report"]."</td><td><b>".$report_array["title"]."</b></td></tr>\n";
		//print_r($inputs);
		foreach($report_array["inputs"] as $indexval => $input) {
			if ((($input["name"] == "sources") && ($SHOW_SOURCES>=getUserAccessLevel(getUserName()))) || ($input["name"] != "sources")) {
				if (($input["name"] != "photos") || ($MULTI_MEDIA)) {
				print "<tr><td>\n";
				print "<input type=\"hidden\" name=\"varnames[]\" value=\"".$input["name"]."\" />\n";
				print $input["value"]."</td><td>";
				if (!isset($input["type"])) $input["type"] = "text";
				if (!isset($input["default"])) $input["default"] = "";
				if (isset($input["lookup"])) {
					if ($input["lookup"]=="INDI") {
						if (!empty($pid)) $input["default"] = clean_input($pid);
						else $input["default"] = check_rootid($input["default"]);
					}
					if ($input["lookup"]=="FAM") {
						if (!empty($famid)) $input["default"] = clean_input($famid);
					}
					if ($input["lookup"]=="SOUR") {
						if (!empty($sid)) $input["default"] = clean_input($sid);
					}
				}
				if ($input["type"]=="text") print "<input type=\"text\" name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."\" value=\"".$input["default"]."\" />";
				if ($input["type"]=="checkbox") {
					print "<input type=\"checkbox\" name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."\" value=\"1\"";
					if ($input["default"]=="1") print "checked=\"checked\"";
					print " />";
				}
				if ($input["type"]=="select") {
					print "<select name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."\">\n";
					$options = preg_split("/[, ]+/", $input["options"]);
					foreach($options as $indexval => $option) {
						print "\t<option value=\"$option\">";
						if (isset($pgv_lang[$option])) print $pgv_lang[$option];
						else if (isset($factarray[$option])) print $factarray[$option];
						else print $option;
						print "</option>\n";
					}
					print "</select>\n";
				}		
				if (isset($input["lookup"])) {
					print "<input type=\"hidden\" name=\"type[".$input["name"]."]\" value=\"".$input["lookup"]."\" />";
					if ($input["lookup"]=="FAM") print "<a href=\"#\" onclick=\"fopenfind(document.setupreport.".$input["name"]."); return false;\"> ".$pgv_lang["find_id"]."</a>";
					if ($input["lookup"]=="INDI") print "<a href=\"#\" onclick=\"iopenfind(document.setupreport.".$input["name"]."); return false;\"> ".$pgv_lang["find_id"]."</a>";
					if ($input["lookup"]=="PLAC") print "<a href=\"#\" onclick=\"popenfind(document.setupreport.".$input["name"]."); return false;\"> ".$pgv_lang["find_place"]."</a>";
					if ($input["lookup"]=="DATE") {
						?>
						<script type="text/javascript">
						<!--
							var d_<?php print $input["name"]; ?> = new CalendarPopup("div_<?php print $input["name"]; ?>");
							d_<?php print $input["name"]; ?>.showYearNavigation();
							d_<?php print $input["name"]; ?>.showYearNavigationInput();
							d_<?php print $input["name"]; ?>.setTodayText(<?php print "\"".$pgv_lang["today"]."\""; ?>);
							d_<?php print $input["name"]; ?>.setMonthNames(<?php foreach($monthtonum as $mon=>$num) {
								if (isset($pgv_lang[$mon])) {
									if ($num>1) print ",";
									print "\"".$pgv_lang[$mon]."\"";
								}
							}?>);
						//-->
						</script>
						<a href="#" onclick="d_<?php print $input["name"]; ?>.select(document.getElementById('<?php print $input["name"]; ?>'),'a_<?php print $input["name"]; ?>','d NNN yyyy'); return false;"><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["small"]; ?>" border="0" name="a_<?php print $input["name"]; ?>" id="a_<?php print $input["name"]; ?>" alt="<?php print $pgv_lang["select_date"];?>" /></a>
						<div id="div_<?php print $input["name"]; ?>" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
						<?php
					}
				}
				print "</td></tr>\n";
				}
			}
		}
//		print "<tr><td>".$pgv_lang["download_report"]."</td><td><input type=\"checkbox\" name=\"download\" value=\"1\" checked=\"checked\" /></td></tr>\n";
		print "</table>\n";
		print "<input type=\"hidden\" name=\"download\" value=\"\" />\n";
		// -- removing this button because it often doesn't work, print "<input type=\"submit\" value=\"".$pgv_lang["run_report"]."\" />\n";
		print "<input type=\"submit\" value=\"".$pgv_lang["download_report"]."\" onclick=\"document.setupreport.elements['download'].value='1';\"/>\n";
		print "</form>\n";
		print "</div><br /><br />\n";
	}
	print_footer();
}
//-- run the report
else if ($action=="run") {
	//-- load the report generator
	if ($output=="HTML") require("includes/reporthtml.php");
	else if ($output=="PDF") require("includes/reportpdf.php");

	//-- start the sax parser
	$xml_parser = xml_parser_create();
	//-- make sure everything is case sensitive
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	//-- set the main element handler functions
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, "characterData");

	//-- open the file
	if (!($fp = fopen($report, "r"))) {
	   die("could not open XML input");
	}
	//-- read the file and parse it 4kb at a time
	while ($data = fread($fp, 4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);

}

?>