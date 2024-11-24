<?php
/**
 * Link media items to indi, sour and fam records
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
 * @subpackage MediaDB
 * @version $Id: linkmedia.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

require("config.php");
require("includes/functions_mediadb.php");

//-- page variables
if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;
if (!isset($action)) $action = "list";


//-- only allow users with edit privileges to access script.
if ((!userCanEdit(getUserName())) || (!$ALLOW_EDIT_GEDCOM)) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

print_header($pgv_lang["link_media_records"]);

?>
<script language="JavaScript" type="text/javascript">
function ilinkitem(mediaid, type) {
	window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
</script>

<?php

print "\n\t<div class=\"center\"><h2>".$pgv_lang["link_media"];
print_help_link("admin_link_media_help","qm");
print "</h2></div>\n\t";

//-- this is already sorted on most recently added first
$medialist = get_db_media_links();

//-- filter code goes here
if ($action == "filter") {
	//-- add filtering code
	$action = "list";

}

if ($action == "list") {
	$ct = count($medialist);
	$count = $max;
	if ($start+$count > $ct)
	        $count = $ct-$start;
	print "\n\t<div align=\"center\">$ct ".$pgv_lang["media_found"]." <br />";
	if ($ct>0){
		print "<form action=\"$PHP_SELF\" method=\"get\" > ".$pgv_lang["medialist_show"]." <select name=\"max\" onchange=\"javascript:submit();\">";
		for ($i=1;($i<=20&&$i-1<ceil($ct/10));$i++) {
		        print "<option value=\"".($i*10)."\" ";
		        if ($i*10==$max) print "selected=\"selected\" ";
		        print " >".($i*10)."</option>";
		}
		print "</select> ".$pgv_lang["per_page"];
		print "</form>";
	}
	print"\n\t<table class=\"list_table\">\n";


	foreach ($medialist as $indexval => $media) {

		//-- design descision, if the admin has not already assigned a thumb because he does not want them
		//   don't show auto thumb generation here, but just the media icon with a view option.
		print "\n\t\t<tr><td class=\"list_value $TEXT_DIRECTION\">";
		$file = $media["FILE"];
		$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
		$repl = addcslashes($MEDIA_DIRECTORY."thumbs/",'/.');
		$thumb = stripcslashes(preg_replace($srch, $repl, $file));

		print "<a href=\"#\" onclick=\"return openImage('".urlencode($file)."');\">";
		if (file_exists($thumb)) {
	    	print "<img src=\"".$thumb."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" /></a>";
		} else {
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"]."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" /></a>";
		}
		print "</td><td class=\"list_value $TEXT_DIRECTION\">";  // image cell
		print "<b>".$media["TITL"]."</b>";
		print "<br />Media ID :- ".$media["XREF"]."<br /><br />";
		if ($media["LINKED"]) {
			print "This item is linked to the folliwing records.<br />";
			foreach ($media["INDIS"] as $indexval => $indi) {
				$indirec = find_record_in_file($indi);
				$tt = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
				if ($tt > 0) $type = trim($match[2]);
				if ($type=="INDI") {
		            print " <br /><a href=\"individual.php?pid=".$indi."\"> ".$pgv_lang["view_person"]." -".PrintReady(get_person_name($indi))."</a>";
				}
				else if ($type=="FAM") {
		           	print "<br /> <a href=\"family.php?famid=".$indi."\"> ".$pgv_lang["view_family"]." - ".PrintReady(get_family_descriptor($indi))."</a>";
				}
				else if ($type=="SOUR") {
		            	print "<br /> <a href=\"source.php?sid=".$indi."\"> ".$pgv_lang["view_source"]." - ".PrintReady(get_source_descriptor($indi))."</a>";
				}
				//-- no reason why we might not get media linked to media. eg stills from movie clip, or differents resolutions of the same item
				else if ($type=="OBJE") {
					//-- TODO add a similar function get_media_descriptor($gid)
				}
			}
		} else {
			print "This item is not linked to any gedcom record";
		}
		print "<br /><br /><div align=\"left\"><table><tr><td>";

		print_link_menu($media["XREF"]);
		print "</td><td>";
		print_help_link("admin_set_link_help","qm");
		print "</td></tr></table></div>";

		/*		print "\n\t\t<td class=\"list_value $TEXT_DIRECTION\">";
		print "</td><td><pre>";
		var_dump($media);
		print "</pre>";
*/

	}

	print "\n\t\t</td></tr>";
	print "\n\t</table>";
} //-- end $action == "list"






print "<br /><br /><center>";
print_media_nav("linkmedia");
print "<br /><br /></center>";

print_footer();


/**
 * Generate Move To flyout menu
 *
 * Access control to directories are in this routine
 *
 * @param mixed $dirlist array() list of subdirectories
 * @param string $directory string current working directory
 * @param sring $filename filename to generate this menu and links for
 */
function print_link_menu($mediaid) {
	global $pgv_lang;

	// main link displayed on page
	$menu = array();
	$menu["label"] = "&nbsp;&nbsp;".$pgv_lang["set_link"]."&nbsp;&nbsp;";
	$menu["link"] = "#";
    $menu["class"] = "thememenuitem";
    $menu["hoverclass"] = "thememenuitem_hover";
    $menu["submenuclass"] = "submenu";
    $menu["flyout"] = "left";
	$menu["items"] = array();

	$submenu = array();
	$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;".$pgv_lang["to_person"]."&nbsp;&nbsp;&nbsp;</b>";
	$submenu["link"] = "#";
	$submenu["onclick"] = "return ilinkitem('$mediaid','person')";
	$submenu["class"] = "themesubmenuitem";
	$submenu["hoverclass"] = "themesubmenuitem_hover";
	$menu["items"][] = $submenu;

	$submenu = array();
	$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;".$pgv_lang["to_family"]."&nbsp;&nbsp;&nbsp;</b>";
	$submenu["link"] = "#";
	$submenu["onclick"] = "return ilinkitem('$mediaid','family')";
	$submenu["class"] = "themesubmenuitem";
	$submenu["hoverclass"] = "themesubmenuitem_hover";
	$menu["items"][] = $submenu;

	$submenu = array();
	$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;".$pgv_lang["to_source"]."&nbsp;&nbsp;&nbsp;</b>";
	$submenu["link"] = "#";
	$submenu["onclick"] = "return ilinkitem('$mediaid','source')";
	$submenu["class"] = "themesubmenuitem";
	$submenu["hoverclass"] = "themesubmenuitem_hover";
	$menu["items"][] = $submenu;

	print_menu($menu);
}


?>