<?php
/**
 * Link media items to indi, sour and fam records 
 *
 * This is the page that does the work of linking items.
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
 * @package PhpGedView
 * @subpackage MediaDB
 */
require("config.php");
require("includes/functions_edit.php");
require("includes/functions_mediadb.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
 
//-- page parameters and checking
$paramok = true;
if (!isset($mediaid)) {$mediaid = ""; $paramok = false;}
if (!isset($linkto)) {$linkto = ""; $paramok = false;}
if (!isset($action)) $action = "choose";
if ($linkto == "person") {
	$toitems = $pgv_lang["to_person"];
} 
elseif ($linkto == "source") {
	$toitems = $pgv_lang["to_source"];
}
elseif ($linkto == "family") {
	$toitems = $pgv_lang["to_family"];
}
else {
	$toitems = "???";
	$paramok = false;
}
 
//-- evil script protection
if ( preg_match("/M\d{4,8}/",$mediaid, $matches) == 1 ) {
	$mediaid=$matches[0];
} else $paramok = false;


print_simple_header($pgv_lang["link_media"]." ".$toitems);





//-- check for admin 
$paramok =  userIsAdmin(getUserName());


if ($action == "choose" && $paramok) {

?>
<script language="JavaScript" type="text/javascript">
var pastefield;
function findIndi(field, indiname) {
	pastefield = field;
	window.open('findid.php?name_filter='+indiname, '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findPlace(field) {
	pastefield = field;
	window.open('findplace.php?place='+field.value, '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findMedia(field) {
	pastefield = field;
	window.open('findmedia.php', '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findSource(field) {
	pastefield = field;
	window.open('findsource.php', '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findRepository(field) {
	pastefield = field;
	window.open('findrepo.php', '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findFamily(field) {
	pastefield = field;
	window.open('findfamily.php', '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
var language_filter, magnify;
language_filter = "";
magnify = "";
function findSpecialChar(field) {
	pastefield = field;
	window.open('findspecialchar.php?language_filter='+language_filter+'&magnify='+magnify, '', 'top=55,left=55,width=200,height=500,scrollbars=1,resizeable=1');
	return false;
}

function addnewsource(field) {
	pastefield = field;
	window.open('edit_interface.php?action=addnewsource&pid=newsour', '', 'top=70,left=70,width=600,height=500,resizable=1,scrollbars=1');
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
<script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>

<?php


	
	
	
	
	if (!isset($linktoid)) $linktoid = "";
	
	print "\n\t<div class=\"center\"><h2>".$pgv_lang["link_media"]." ".$toitems;
	print_help_link("admin_link_media_help","qm");
	print "</h2></div>\n\t";
	
	print "<form name=\"link\" method=\"post\" action=\"inverselink.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
	print "<input type=\"hidden\" name=\"mediaid\" value=\"$mediaid\" />\n";
	print "<input type=\"hidden\" name=\"linkto\" value=\"$linkto\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<table class=\"facts_table\">";

	
	print "<tr><td class=\"facts_label\">&nbsp;";
	print $pgv_lang["media_id"];
	print "&nbsp;</td><td  class=\"facts_value\">".$mediaid;
	print "</td>";
	
	if ($linkto == "person") {
		print "<tr><td class=\"facts_label\">&nbsp;";
		print $pgv_lang["enter_pid"];
		print "&nbsp;</td><td  class=\"facts_value\">";

		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findindi_link("linktoid","");
	}
	
	if ($linkto == "family") {
		print "<tr><td class=\"facts_label\">&nbsp;";
		print $pgv_lang["enter_famid"];
		print "&nbsp;</td><td  class=\"facts_value\">";

		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findfamily_link("linktoid");
	}
	
	if ($linkto == "source") {
		print "<tr><td class=\"facts_label\">&nbsp;";
		print $pgv_lang["source"];
		print "&nbsp;</td><td  class=\"facts_value\">";

		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findsource_link("linktoid");
		print_addnewsource_link("linktoid");
	}
	
	
	// 2 _PRIM
	add_simple_tag("2 _PRIM");
	// 2 _THUM
	add_simple_tag("2 _THUM");
	
	print "</table>";
	print_add_layer("NOTE");
	
	if ($linkto != "source") {print_add_layer("SOUR");}
	
	print "<br /><input type=\"submit\" value=\"".$pgv_lang["set_link"]."\" /><br />\n";
	print "</form>\n";
	
		
	print "<br/><br/><center><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	
	print_simple_footer();
	
}  
elseif ($action == "update" && $paramok) {
	
	// find indi
	$indirec = find_gedcom_record($linktoid);
	
	if ($indirec) {
		
		print "We got an update: MediaID ".$mediaid." Linkto: ".$linktoid;
		
		$mediarec = "1 OBJE @".$mediaid."@\r\n";
		// build the details for this link
		for ($i=0; $i < count($tag);$i++) {
			
			if ($text[$i] != "") {
				
				// we don't have linked notes yet		
				if ($tag[$i] == "NOTE") {
						$mediarec .= textblock_to_note($glevels[$i],$text[$i]);
				}
				elseif ($islink[$i] == "1") { // source link
					$mediarec .= $glevels[$i]." ".$tag[$i]." @".$text[$i]."@\r\n";				
				}
				else {
					$mediarec .= $glevels[$i]." ".$tag[$i]." ".$text[$i]."\r\n";			
				}
			}
			elseif ($glevels[$i] == "2") { // skip any sub fields if empty
				while (($i < count($tag)-1) && ($glevels[$i + 1] != "2")) {
					$i += 1;
				}
				
			}
		}
		$newrec = trim($indirec."\r\n".$mediarec);
		print "<br /><pre>";
		var_dump($newrec);
		print "</pre>";
		
		// update the database
		update_db_link($mediaid, $linktoid, $mediarec, $ged, -1);
		
		replace_gedrec($linktoid, $newrec);
		
	} else {
		print "<br /><center>".$pgv_lang["invalid_id"]."</center>";	
	}
	print "<br/><br/><center><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();
}
else { 
	print "<center>nothing to do<center>";

	print "<br/><br/><center><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	
	print_simple_footer();

} // $paramok
 
 
?>