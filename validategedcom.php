<?php
/**
 * Check for valid Gedcom, Step 3
 *
 * This file is Step 3 of 4 during the import process and should always be run before importgedcom.php
 * This file will check the gedcom being imported for errors and will attempt to cleanup those errors
 * using the functions in the functions_tools.php file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * $Id: validategedcom.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 * @package PhpGedView
 * @subpackage Tools
 * @see functions_tools.php
 */

require("config.php");
require("includes/functions_tools.php");

if (empty($ged)) $ged = $GEDCOM;
$GEDCOM = $ged;

if ($PGV_DATABASE=="index") {
	if (!userCanAccept(getUserName())) {
		print_header($pgv_lang["validate_gedcom"]);
		print $pgv_lang["access_denied"];
		print_footer();
		exit;
	}
}
else {
	if (!userGedcomAdmin(getUserName())) {
		print_header($pgv_lang["validate_gedcom"]);
		print $pgv_lang["access_denied"];
		print_footer();
		exit;
	}
}

$backup_gedcom = false;

//perform the cleanup
if((isset($_POST["action"])) && ($_POST["action"]=="cleanup")) {
	$filechanged=false;

	if (file_is_writeable($GEDCOMS[$GEDCOM]["path"]) && (file_exists($GEDCOMS[$GEDCOM]["path"]))) {
		$l_headcleanup = false;
		$l_macfilecleanup = false;
		$l_lineendingscleanup = false;
		$l_placecleanup = false;
		$l_datecleanup=false;
		$l_isansi = false;
		$fp = fopen($GEDCOMS[$GEDCOM]["path"], "rb");
		$fw = fopen($INDEX_DIRECTORY."/".$GEDCOM.".bak", "wb");
		//-- read the gedcom and test it in 8KB chunks
		while(!feof($fp)) {
			$fcontents = fread($fp, 1024*8);
			$lineend = "\n";
			if (need_macfile_cleanup()) {
				$l_macfilecleanup=true;
				$lineend = "\r";
			}
			
			//-- read ahead until the next line break
			$byte = "";
			while((!feof($fp)) && ($byte!=$lineend)) {
				$byte = fread($fp, 1);
				$fcontents .= $byte;
			}
			
			if (!$l_headcleanup && need_head_cleanup()) {
			head_cleanup();
				$l_headcleanup = true;
		}
	
			if ($l_macfilecleanup) {
			macfile_cleanup();
		}
	
		if (isset($_POST["cleanup_places"]) && $_POST["cleanup_places"]=="YES") {
			if(($sample = need_place_cleanup()) !== false) {
					$l_placecleanup=true;
				place_cleanup();
			}
		}
	
		if (line_endings_cleanup()) {
			$filechanged = true;
		}
	
		if(isset($_POST["datetype"])) {
			$filechanged=true;
		 	//month first
			date_cleanup($_POST["datetype"]);
		}
		if($_POST["xreftype"]!="NA") {
			$filechanged=true;
			xref_change($_POST["xreftype"]);
		}
		if ($_POST["utf8convert"]=="YES") {
			$filechanged=true;
			convert_ansi_utf8();
		}
			fwrite($fw, $fcontents);
		}
		fclose($fp);
		fclose($fw);
		copy($INDEX_DIRECTORY."/".$GEDCOM.".bak", $GEDCOMS[$GEDCOM]["path"]);
		
		header("Location: importgedcom.php?ged=$GEDCOM");
		exit;
	}
	else {
		$error = str_replace("#GEDCOM#", $GEDCOM, $pgv_lang["error_header_write"]);
	}
}

  	print_header($pgv_lang["validate_gedcom"]);
  	print "<br /><span class=\"subheaders\">".$pgv_lang["step3"]." ".$pgv_lang["validate_gedcom"]."</span><br />";
	print "<p>".$pgv_lang["performing_validation"]."</p>\n";
	if (!empty($error)) print "<span class=\"error\">$error</span><br />\n";
  	print "<form method=\"post\" action=\"validategedcom.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"cleanup\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"".$GEDCOM."\" />\n";

	$l_headcleanup = false;
	$l_macfilecleanup = false;
	$l_lineendingscleanup = false;
	$l_placecleanup = false;
	$l_datecleanup=false;
	$l_isansi = false;
	
	$fp = fopen($GEDCOMS[$GEDCOM]["path"], "r");
	//-- read the gedcom and test it in 8KB chunks
	while(!feof($fp)) {
		$fcontents = fread($fp, 1024*8);
		if (!$l_headcleanup && need_head_cleanup()) $l_headcleanup = true;
		if (!$l_macfilecleanup && need_macfile_cleanup()) $l_macfilecleanup = true;
		if (!$l_lineendingscleanup && need_line_endings_cleanup()) $l_lineendingscleanup = true;
		if (!$l_placecleanup && ($placesample = need_place_cleanup()) !== false) $l_placecleanup = true;
		if (!$l_datecleanup && ($datesample = need_date_cleanup()) !== false) $l_datecleanup = true;
		if (!$l_isansi && is_ansi()) $l_isansi = true;
	}
	fclose($fp);

	if ($l_headcleanup) {
		print "<span class=\"error\"><br />".$pgv_lang["invalid_header"]."</span>\n";
		print_help_link("invalid_header_help", "qm");
	}
	if ($l_macfilecleanup) {
		print "<span class=\"error\"><br />".$pgv_lang["macfile_detected"]."</span>\n";
		print_help_link("macfile_detected_help", "qm");
	}
	if ($l_lineendingscleanup) {
		print "<span class=\"error\"><br />".$pgv_lang["empty_lines_detected"]."</span>\n";
		print_help_link("empty_lines_detected_help", "qm");
	}
	if ($l_placecleanup) {
		print "<span class=\"error\"><br />".$pgv_lang["place_cleanup_detected"]."<br />".PrintReady(nl2br($placesample[0]))."</span>\n";
		print "<table class=\"facts_table\">";
	  	print "<tr><td class=\"facts_label\">".$pgv_lang["cleanup_places"];
		print_help_link("cleanup_places_help", "qm");
	  	print "</td><td class=\"facts_value\"><select name=\"cleanup_places\">\n";
		print "<option value=\"YES\" selected=\"selected\">".$pgv_lang["yes"]."</option>\n<option value=\"NO\">".$pgv_lang["no"]."</option>\n</select>";
		print "</td></tr></table>\n";
	}
	//-- check for date cleanup
  	if ($l_datecleanup) {
		print "<span class=\"error\"><br />".$pgv_lang["invalid_dates"]."</span>\n";
		print "<table class=\"facts_table\">";
	  	print "<tr><td class=\"facts_label\">".$pgv_lang["date_format"];
		print_help_link("detected_date_help", "qm");
	  	print "</td><td class=\"facts_value\">";
	  	if (preg_match("/[a-zA-Z]+/", $datesample[1])>0) {
		  	print "<input type=\"hidden\" name=\"datetype\" value=\"3\" />\n";
	  	}
	  	else {
	  		print "<select name=\"datetype\">\n";
			print "<option value=\"1\">".$pgv_lang["day_before_month"]."</option>\n<option value=\"2\">".$pgv_lang["month_before_day"]."</option>\n</select>";
		}
		print "<br />".$pgv_lang["example_date"]." $datesample[0]";
		print "</td></tr></table>\n";
	}

	//-- check for ansi encoding
	if ($l_isansi) {
		print "<span class=\"error\"><br />".$pgv_lang["ansi_encoding_detected"]."</span>\n";
		print "<table class=\"facts_table\">";
	  	print "<tr><td class=\"facts_label\">".$pgv_lang["ansi_to_utf8"];
		print_help_link("detected_ansi2utf_help", "qm");
	  	print "</td><td class=\"facts_value\"><select name=\"utf8convert\">\n";
		print "<option value=\"YES\" selected=\"selected\">".$pgv_lang["yes"]."</option>\n<option value=\"NO\">".$pgv_lang["no"]."</option>\n</select>";
		print "</td></tr></table>\n";
	}

	$cleanup_needed = false;
	if (!$l_datecleanup && !$l_isansi  && !$l_headcleanup && !$l_macfilecleanup &&!$l_placecleanup && !$l_lineendingscleanup) {
		print $pgv_lang["valid_gedcom"]."<br />\n";
		print "<input type=\"button\" value=\"".$pgv_lang["continue"]."\" onclick=\"window.location='importgedcom.php?ged=".preg_replace("/'/", "\'", $GEDCOM)."';\" />\n";
	}
	else {
		$cleanup_needed = true;
		if (!file_is_writeable($GEDCOMS[$GEDCOM]["path"]) && (file_exists($GEDCOMS[$GEDCOM]["path"]))) {
			print "<br /><span class=\"error\">".str_replace("#GEDCOM#", $GEDCOM, $pgv_lang["error_header_write"])."</span>\n";
		}
	}
	print "<br /><br /><span class=\"subheaders\">".$pgv_lang["optional"]."</span><br />";
	print $pgv_lang["optional_tools"]."<br />\n";
	print "<table class=\"facts_table\"><tr><td class=\"facts_label\">";
	//change XREF to RIN,REFN, on Don't change
	print $pgv_lang["change_id"];
	print_help_link("change_indi2id_help", "qm");
	print "</td><td class=\"facts_value\"><select name=\"xreftype\">\n";
	print "<option value=\"NA\">".$pgv_lang["do_not_change"]."</option>\n<option value=\"RIN\">RIN</option>\n";
	print "<option value=\"REFN\">REFN</option>\n</select>";
	print "</td></tr>\n";
	//-- option to convert to utf8
	if (!$l_isansi) {
		print "<tr><td class=\"facts_label\">".$pgv_lang["ansi_to_utf8"];
		print_help_link("convert_ansi2utf_help", "qm");
		print "</td><td class=\"facts_value\"><select name=\"utf8convert\">\n";
		print "<option value=\"YES\">".$pgv_lang["yes"]."</option>\n";
		print "<option value=\"NO\" selected=\"selected\">".$pgv_lang["no"]."</option>\n</select>";
		print "</td></tr>";
	}
	//-- option to start addmedia tool
	if ($MULTI_MEDIA_DB){
		print "<tr><td class=\"facts_label\">".$pgv_lang["inject_media_tool"];
		print_help_link("inject_media_tool_help", "qm");
		print "</td><td class=\"facts_value\"><a href=\"addmedia.php?ged=$GEDCOM&action=injectmedia\" target=\"media_win\">".$pgv_lang["launch_media_tool"]."</a></td></tr>\n";
	}
	print "<tr><td colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["cleanup"]."\" />\n";
	if ($cleanup_needed) {
		print " <input type=\"button\" value=\"".$pgv_lang["skip_cleanup"]."\" onclick=\"window.location='importgedcom.php?ged=$GEDCOM';\" />\n";
		print_help_link("skip_cleanup_help", "qm");
	}
	print "</td></tr></table>";
	print "</form>";


print_footer();
?>