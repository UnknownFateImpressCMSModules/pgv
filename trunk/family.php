<?php
/**
 * Parses gedcom file and displays information about a family.
 *
 * You must supply a $famid value with the identifier for the family.
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
 * $Id: family.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 * @package PhpGedView
 * @subpackage Charts
 */

require("config.php");
require("includes/functions_charts.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

$bwidth=$Dbwidth;
$pbwidth = $bwidth+12;
$pbheight = $bheight+14;

if (!isset($action)) $action = "";
if (empty($show_changes)) $show_changes = "yes";

$show_famlink = true;
if ((isset($view)) && ($view == "preview")) {
    $show_famlink = false;
} 

if (!isset($famid)) $famid="";
$famid = clean_input($famid);
$display = displayDetailsByID($famid, "FAM");
$showLivingHusb=true;
$showLivingWife=true;
$parents = find_parents($famid);
//-- check if we can display both parents
if (!$display) {
	$showLivingHusb=showLivingNameByID($parents["HUSB"]);
	$showLivingWife=showLivingNameByID($parents["WIFE"]);
}

$accept_success=false;
if (userCanAccept(getUserName())) {
	if ($action=="accept") {
		if (accept_changes($famid."_".$GEDCOM)) {
			$show_changes="no";
			$accept_success=true;
			unset($famlist[$famid]);
			$parents = find_parents($famid);
		}
	}
}

$famrec = find_family_record($famid);

//-- make sure we have the true id from the record
$ct = preg_match("/0 @(.*)@/", $famrec, $match);
if ($ct>0) $famid = trim($match[1]);

if (!$showLivingHusb && !$showLivingWife) {
	print_header($pgv_lang["private"]." ".$pgv_lang["family_info"]);
	print_privacy_error($CONTACT_EMAIL);
	print_footer();
	exit;
}
$none=1;
if ($showLivingHusb){
	$title = "";
	if (get_person_name($parents["HUSB"]) !== "Individual ") {
		$title = get_person_name($parents["HUSB"]);
		$none=0;
	}
	if ($showLivingWife && (get_person_name($parents["WIFE"]) !== "Individual ")) {
		if ($none==0) $title.= " + ";
		$title .= get_person_name($parents["WIFE"]);
		$none=0;
	}
	print_header($title." ".$pgv_lang["family_info"]);
}
if ($none) print_header($pgv_lang["family_info"]);
if (empty($parents["HUSB"]) || empty($parents["WIFE"])) $link_relation=0;
else $link_relation=1;
?>

<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(shownew) {
		fromfile="";
		if (shownew=="yes") fromfile='&fromfile=1';
		var recwin = window.open("gedrecord.php?pid=<?php print $famid; ?>"+fromfile, "", "top=50,left=50,width=300,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
		window.location = '<?php print $PHP_SELF."?".preg_replace("/&amp;/", "&", $QUERY_STRING)."&show_changes=yes"; ?>';
	}
//-->
</script>
<?php
print "<table><tr><td>";
print_family_parents($famid);
print "</td><td valign=\"top\">";
if ($view!="preview") {
	print '<table class="sublinks_table" cellspacing="4" cellpadding="0">';
	print '    <tr>';
	print '        <td class="list_label '.$TEXT_DIRECTION.'" colspan="4">'.$pgv_lang["fams_charts"].'</td></tr>';
	print '    <tr>';
	print '        <td class="sublinks_cell '.$TEXT_DIRECTION.'">';

	//-- charts menu
	if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
	else $ff="";	
	$menu = array();
	$menu["label"] = $pgv_lang["charts"];
	$menu["labelpos"] = "right";
	if (!empty($PGV_IMAGES["timeline"]["small"]))
		$menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"];
	$menu["link"] = "timeline.php?pids[0]=".$parents['HUSB']."&amp;pids[1]=".$parents['WIFE'];
 	$menu["class"] = "submenuitem$ff";
	$menu["hoverclass"] = "submenuitem_hover$ff";
	$menu["flyout"] = "down";
	$menu["submenuclass"] = "submenu$ff";
	$menu["items"] = array();
	
	$submenu = array();
	$submenu["label"] = $pgv_lang["parents_timeline"];
	$submenu["labelpos"] = "right";
	if (!empty($PGV_IMAGES["timeline"]["small"]))
		$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"];
	$submenu["link"] = "timeline.php?pids[0]=".$parents['HUSB']."&amp;pids[1]=".$parents['WIFE'];
	$submenu["class"] = "submenuitem$ff";
	$submenu["hoverclass"] = "submenuitem_hover$ff";
	$menu["items"][] = $submenu;
	$submenu = array();
	$submenu["label"] = $pgv_lang["children_timeline"];
	$submenu["labelpos"] = "right";
	if (!empty($PGV_IMAGES["timeline"]["small"]))
		$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"];
	$submenu["link"] = "timeline.php?";
	$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$submenu["link"] .= "pids[$i]=".$match[$i][1]."&amp;";
	}
	$submenu["class"] = "submenuitem$ff";
	$submenu["hoverclass"] = "submenuitem_hover$ff";
	$menu["items"][] = $submenu;
	$submenu = array();
	$submenu["label"] = $pgv_lang["family_timeline"];
	$submenu["labelpos"] = "right";
	if (!empty($PGV_IMAGES["timeline"]["small"]))
		$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"];
	$submenu["link"] = "timeline.php?pids[0]=".$parents['HUSB']."&amp;pids[1]=".$parents['WIFE'];
	$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$submenu["link"] .= "&amp;pids[".($i+2)."]=".$match[$i][1];
	}
	$submenu["class"] = "submenuitem$ff";
	$submenu["hoverclass"] = "submenuitem_hover$ff";
	$menu["items"][] = $submenu;
	print_menu($menu);

	if (file_exists("reports/familygroup.xml")) {
		print '        </td><td class="sublinks_cell '.$TEXT_DIRECTION.'">';

		$menu = array();
		$menu["label"] = $pgv_lang["reports"];
		$menu["labelpos"] = "right";
		If ($THEME_DIR != $PGV_BASE_DIRECTORY."themes/minimal/" && $THEME_DIR != $PGV_BASE_DIRECTORY."themes/simplygreen/")
			$menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["reports"]["small"];
		$menu["link"] = "reportengine.php?action=setup&amp;report=reports/familygroup.xml&amp;famid=$famid";
		$menu["class"] = "submenuitem$ff";
		$menu["hoverclass"] = "submenuitem_hover$ff";
		$menu["flyout"] = "down";
		$menu["submenuclass"] = "submenu$ff";
		$menu["items"] = array();

		$submenu = array();
		$submenu["label"] = $pgv_lang["family_group_report"];
		$submenu["labelpos"] = "right";
 		if (!empty($PGV_IMAGES["reports"]["small"]))
			$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["reports"]["small"];
		$submenu["link"] = "reportengine.php?action=setup&amp;report=reports/familygroup.xml&amp;famid=$famid";
		$submenu["class"] = "submenuitem$ff";
		$submenu["hoverclass"] = "submenuitem_hover$ff";
		$menu["items"][] = $submenu;
	
	print_menu($menu);
	}
	
	if (userCanEdit(getUserName())&&($display)) {
		print '        </td><td class="sublinks_cell '.$TEXT_DIRECTION.'">';
		//-- charts menu
		$menu = array();
		$menu["label"] = $pgv_lang["edit_fam"];
		$menu["labelpos"] = "right";
		if (!empty($PGV_IMAGES["edit_fam"]["small"]))
			$menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_fam"]["small"];
		$menu["link"] = "#";
		$menu["onclick"] = "return edit_raw('$famid');";
		$menu["class"] = "submenuitem$ff";
		$menu["hoverclass"] = "submenuitem_hover$ff";
		$menu["flyout"] = "down";
		$menu["submenuclass"] = "submenu$ff";
		$menu["items"] = array();
		$submenu = array();
		$submenu["label"] = $pgv_lang["edit_raw"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["link"] = "#";
		$submenu["onclick"] = "return edit_raw('$famid');";
		$submenu["class"] = "submenuitem$ff";
		$submenu["hoverclass"] = "submenuitem_hover$ff";
		$menu["items"][] = $submenu;
		$submenu = array();
		$submenu["label"] = $pgv_lang["add_child_to_family"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["link"] = "#";
		$submenu["onclick"] = "return addnewchild('$famid');";
		$submenu["class"] = "submenuitem$ff";
		$submenu["hoverclass"] = "submenuitem_hover$ff";
		$menu["items"][] = $submenu;
		$submenu = array();
		$submenu["label"] = $pgv_lang["reorder_children"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["link"] = "#";
		$submenu["onclick"] = "return reorder_children('$famid');";
		$submenu["class"] = "submenuitem$ff";
		$submenu["hoverclass"] = "submenuitem_hover$ff";
		$menu["items"][] = $submenu;
		if (isset($pgv_changes[$famid."_".$GEDCOM])) {
			$menu["items"][] = "separator";
			$submenu = array();
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			if ($show_changes=="no") {
				$submenu["label"] = $pgv_lang["show_changes"];
				$submenu["link"] = "family.php?famid=$famid&amp;show_changes=yes";
			}
			else {
				$submenu["label"] = $pgv_lang["hide_changes"];
				$submenu["link"] = "family.php?famid=$famid&amp;show_changes=no";
			}
			$submenu["class"] = "submenuitem$ff";
			$submenu["hoverclass"] = "submenuitem_hover$ff";
			$menu["items"][] = $submenu;
			if (userCanAccept(getUserName())) {
				$submenu = array();
				$submenu["label"] = $pgv_lang["accept_all"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				if ($PGV_DATABASE!="index") $submenu["link"] = "family.php?famid=$famid&amp;action=accept";
				else {
					$submenu["link"] = "#";
					$submenu["onclick"] = "window.open('edit_changes.php','','width=600,height=600,resizable=1,scrollbars=1'); return false;";
				}
				$submenu["class"] = "submenuitem$ff";
				$submenu["hoverclass"] = "submenuitem_hover$ff";
				$menu["items"][] = $submenu;
			}
		}
		print_menu($menu);
	}
	
	if ($display && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART>=getUserAccessLevel())) {
		print '        </td><td class="sublinks_cell '.$TEXT_DIRECTION.'">';
		$menu = array();
		$menu["label"] = $pgv_lang["other"];
		$menu["labelpos"] = "right";
		if ($SHOW_GEDCOM_RECORD) {
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"];
				if ($show_changes=="yes"  && userCanEdit(getUserName())) $menu["link"] = "javascript:show_gedcom_record('new');";
				else $menu["link"] = "javascript:show_gedcom_record();";
		}
		else {
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"];
				$menu["link"] = "clippings.php?action=add&amp;id=$famid&amp;type=fam";
		}
		$menu["class"] = "submenuitem$ff";
		$menu["hoverclass"] = "submenuitem_hover$ff";
		$menu["flyout"] = "down";
		$menu["submenuclass"] = "submenu$ff";
		$menu["items"] = array();
		if ($SHOW_GEDCOM_RECORD) {
			$submenu = array();
			$submenu["label"] = $pgv_lang["view_gedcom"];
			$submenu["labelpos"] = "right";
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"];
			if ($show_changes=="yes"  && userCanEdit(getUserName())) $submenu["link"] = "javascript:show_gedcom_record('new');";
			else $submenu["link"] = "javascript:show_gedcom_record();";
			$submenu["class"] = "submenuitem$ff";
			$submenu["hoverclass"] = "submenuitem_hover$ff";
			$menu["items"][] = $submenu;
		}
		if ($ENABLE_CLIPPINGS_CART>=getUserAccessLevel()) {
			$submenu = array();
			$submenu["label"] = $pgv_lang["add_to_cart"];
			$submenu["labelpos"] = "right";
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"];
			$submenu["link"] = "clippings.php?action=add&amp;id=$famid&amp;type=fam";
			$submenu["class"] = "submenuitem$ff";
			$submenu["hoverclass"] = "submenuitem_hover$ff";
			$menu["items"][] = $submenu;
		}
		print_menu($menu);	
	}
	print "</td></tr></table>\n";
	if ($accept_success) print "<b>".$pgv_lang["accept_successful"]."</b><br />";
}
print "</td></tr></table>\n";
print "<table width=\"95%\"><tr><td valign=\"top\" style=\"width: " . ($pbwidth) . "px;\">\n";
print_family_children($famid);
print "</td><td valign=\"top\">";
print_family_facts($famid);
print "</td></tr></table>\n";
print "<br />";
print_footer();

?>