<?php
/**
 * Add media to gedcom file
 *
 * This file allows the user to maintain a seperate table
 * of media files and associate them with individuals in the gedcom
 * and then add these records later.
 * Requires SQL mode.
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
 * @package PhpGedView
 * @subpackage MediaDB
 * @version $Id: addmedia.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

/**
 * TODO
 *
 * implement mediadb interface for the main core
 *
 * Migration of existing media tables and data to new table structure.
 *
 * Conform to change mechanism during interactive update sessions so that
 * undo/accept works as expected
 *
 */


/**
 * load config file
 */
require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

require("includes/functions_edit.php");
require("includes/functions_mediadb.php");

if (empty($ged)) $ged = $GEDCOM;
$GEDCOM = $ged;

print_header($pgv_lang["add_media_tool"]);


//-- only allow users with edit privileges to access script.
if (!userIsAdmin(getUserName())) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?ged=$GEDCOM&url=addmedia.php");
	exit;
}

?>
<script language="JavaScript" type="text/javascript">
var language_filter, magnify;
language_filter = "";
magnify = "";
function findSpecialChar(field) {
	pastefield = field;
	window.open('findspecialchar.php?language_filter='+language_filter+'&magnify='+magnify, '', 'top=55,left=55,width=200,height=500,scrollbars=1,resizeable=1');
	return false;
}
function openerpasteid(id) {
	window.opener.paste_id(id);
	window.close();
}

function paste_id(value) {
	pastefield.value = value;
}

function paste_char(value,lang,mag) {
	pastefield.value += value;
	language_filter = lang;
	magnify = mag;
}
</script>

<?php
print "\n\t<div class=\"center\"><h2>".$pgv_lang["add_media_tool"];
print_help_link("admin_add_media_help","qm");
print "</h2></div>\n<center>".$pgv_lang["add_media_descr"]."\n<br /><b>$ged</b></center><br />";


if (empty($action)) $action="showmediaform";

if (!isset($m_ext)) $m_ext="";
if (!isset($m_titl)) $m_titl="";
if (!isset($m_file)) $m_file="";

check_media_db();

if ($action=="newentry") {

	$sql = "SELECT * FROM ".$TBLPREFIX."media WHERE m_file = '".$m_file."'";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	if ($ct==0) {

		if ((trim($text[0]) != "")) {
			$note = trim(textblock_to_note(1,$text[0]));
		} else { $note = "";}
		$sql = "INSERT INTO ".$TBLPREFIX."media VALUES(NULL, '','".addslashes($m_ext)."','".addslashes($m_titl)."','".addslashes($m_file)."','".addslashes($m_gedfile)."','".addslashes($note)."')";
		$res =& dbquery($sql);
		$sql = "SELECT LAST_INSERT_ID() `lindex`";
		$res =& dbquery($sql);
		$ct = $res->numRows();
		if ($ct==1) {
			$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
			$m_id = $row["lindex"];
		}
		$pad = array("0000","000","00","0","","","");
		$m_media = "M".$pad[strlen($m_id)].$m_id;
		$sql = "UPDATE ".$TBLPREFIX."media SET m_media = '".$m_media."' WHERE m_id = ".$m_id;
		$res =& dbquery($sql);

		$mediarec = "\r\n0 @".$m_media."@ OBJE";
		$mediarec .= "\r\n1 FILE ".$m_file;
		$mediarec .= "\r\n1 FORM ".$m_ext;
		$mediarec .= "\r\n1 TITL ".$m_titl;
		if ($note != "") {
			$mediarec .= "\r\n".$note;
		}

		append_gedrec1($mediarec,false);

		print "<center>".$m_file." added successfully as <b>".$m_media."</b></center><br />";

	}else{

		$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
		print "<center>".$m_file." exists with description of <b>'".$row["m_titl"]."'</b> and GEDCOM ID of <b>".$row["m_media"]."</b></center><br />";

	}
	$action = "showmediaform";
}


if ($action=="delete") {
	remove_db_media($m_id, $m_gedfile);
	$action = "showmedia";
}

if ($action=="showmedia") {
	$medialist = get_db_media_list();
	if (count($medialist)>0) {
		print "<table class=\"list_table\">\n";
		print "<tr><td class=\"list_label\">".$pgv_lang["delete"]."</td><td class=\"list_label\">".$pgv_lang["title"]."</td><td class=\"list_label\">".$pgv_lang["gedcomid"]."</td>\n";
		print "<td class=\"list_label\">".$factarray["FILE"]."</td><td class=\"list_label\">".$pgv_lang["highlighted"]."</td><td class=\"list_label\">order</td><td class=\"list_label\">gedcom</td></tr>\n";
		foreach($medialist as $indexval => $media) {
			print "<tr>";
			print "<td class=\"list_value\"><a href=\"addmedia.php?action=delete&m_id=".$media["ID"]."\">delete</a></td>";
			print "<td class=\"list_value\"><a href=\"addmedia.php?action=edit&m_id=".$media["ID"]."\">edit</a></td>";
			print "<td class=\"list_value\">".$media["TITL"]."</td>";
			print "<td class=\"list_value\">";
			print_list_person($media["INDI"], array(get_person_name($media["INDI"]), $GEDCOM));
			print "</td>";
			print "<td class=\"list_value\">".$media["FILE"]."</td>";
			print "<td class=\"list_value\">".$media["_PRIM"]."</td>";
			print "<td class=\"list_value\">".$media["ORDER"]."</td>";
			print "<td class=\"list_value\">".$media["GEDFILE"]."</td>";
			print "</tr>\n";
		}
		print "</table>\n";
	}
}


if ($action=="showmediaform") {




?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pasteto;

	function open_find(textbox) {
		pasteto = textbox;
		findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
	function findMedia(field) {
		pasteto = field;
		window.open('findmedia.php', '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
		return false;
	}
	//-->
	</script>
<?php

//-- add a table and form to easily add new values to the table
	print "<form method=\"post\" name=\"newmedia\" action=\"addmedia.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"newentry\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<table class=\"facts_table\">\n";
	print "<tr><td class=\"facts_label\">".$factarray["FILE"];
	print_help_link("edit_FILE_help","qm");
	print "</td><td class=\"facts_value\"><input type=\"text\" id=\"m_file\" name=\"m_file\" />";
	print_specialchar_link("m_file","");
	print_findmedia_link("m_file");
	print "</td></tr>\n";
	print "<tr><td class=\"facts_label\">".$pgv_lang["extension"];
	print_help_link("edit_FORM_help","qm");
	print "</td><td class=\"facts_value\"><input type=\"text\" id=\"m_ext\" width=\"4\" name=\"m_ext\" value=\"jpeg\" />";
	print_autopaste_link("m_ext", array("avi", "bmp", "gif", "jpeg", "mp3", "ole", "pcx", "tiff", "wav"), false);
	print "<tr><td class=\"facts_label\">".$pgv_lang["title"];
	print_help_link("edit_TITL_help","qm");
	print "</td><td class=\"facts_value\"><input id=\"titl\" type=\"text\" name=\"m_titl\" />";
	print_specialchar_link("titl","");
	print "</td></tr>\n";
	print "<tr><td class=\"facts_label\">".$pgv_lang["gedcom_file"];
	print_help_link("edit_ged_list_help","qm");
	print "</td><td class=\"facts_value\"><select name=\"m_gedfile\">";
	foreach($GEDCOMS as $ged=>$gedarray) {
		print "<option value=\"$ged\"";
		if ($ged==$GEDCOM) print " selected=\"selected\"";
		print ">".PrintReady($gedarray["title"])."</option>\n";
	}
	print "</select></td></tr>\n";
	print "</table>\n";
	print_add_layer("NOTE");

	print "<center><br /><input type=\"submit\" value=\"".$pgv_lang["add_media_button"]."\" /><br /><br />\n";
	print "</form>\n";
}

if ($action=="injectmedia") {

	$medialist = get_db_media_list();

	// check for already imported media
	$test = find_record_in_file($medialist[0]["XREF"]);
	if ($test) {
		print "<div align=\"center\" class=\"error\" ><h2>This gedcom has already had the media information inserted into it, operation aborted</h3></div>";
	} else {

		$ct = 0;
		$nct = 0;
		foreach($medialist as $indexval => $media) {
			$mediarec = "\r\n0 @".$media["XREF"]."@ OBJE";
			$mediarec .= "\r\n1 FILE ".$media["FILE"];
			$mediarec .= "\r\n1 TITL ".$media["TITL"];
			$mediarec .= "\r\n1 FORM ".$media["FORM"];
			if (strlen($media["NOTE"])>0) {$mediarec .= "\r\n".$media["NOTE"]; $nct++;};
			$pos1 = strrpos($fcontents, "0");
			$fcontents = substr($fcontents, 0, $pos1).trim($mediarec)."\r\n".substr($fcontents, $pos1);
			write_file();
			$ct++;
		}
		print "<center>$ct media items added, $nct with notes</center>";

		$ct = 0;
		$nct = 0;
		$mappinglist = get_db_mapping_list();
		$oldindi = "";
		for ($i=0; $i < count($mappinglist); $i++) {
			$media = $mappinglist[$i];
			$indi = $media["INDI"];
			if ($indi != $oldindi) {
				if ($i > 0) { db_replace_gedrec($oldindi, $indirec);};
				$oldindi = $indi;
				$indirec = find_record_in_file($indi);
			}
		    if (strlen($media["NOTE"])>0) {$indirec .= "\r\n".trim($media["NOTE"]); $nct++;};

		}
		db_replace_gedrec($indi, $indirec);

		print "<center>$ct link items added, $nct with notes</center>";
		print "<p><center>".$pgv_lang["adds_completed"]."<center></p><br /><br />\n";
	}
	print "<p><center><a href=\"#\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></center></p><br /><br />\n";
} else {
	print_media_nav("addmedia");
}

print_footer();


?>