<?php
/**
 * MyGedView page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
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
 * @subpackage Display
 * @version $Id: index_edit.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require("config.php");

/**
 * Block definition array
 *
 * The following block definition array defines the
 * blocks that can be used to customize the portals
 * their names and the function to call them
 * "name" is the name of the block in the lists
 * "descr" is a textual description of the block
 * "type" the options are "user" or "gedcom" or undefined
 * - The type determines which lists the block is available in.
 * - Leaving the type undefined allows it to be on both the user and gedcom portal
 * @global $PGV_BLOCKS
 */
$PGV_BLOCKS = array();

//-- load all of the blocks
$d = dir("blocks");
while (false !== ($entry = $d->read())) {
	if (($entry!=".") && ($entry!="..") && ($entry!="CVS") && (strstr($entry, ".php")!==false)) {
		include_once("blocks/".$entry);
	}
}
$d->close();

if (!isset($action)) $action="";
if (!isset($command)) $command="user";
if (!isset($main)) $main=array();
if (!isset($right)) $right=array();
if (!isset($setdefault)) $setdefault=false;
if (!isset($side)) $side="main";
if (!isset($index)) $index=1;

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname) || empty($name)) {
	print_simple_header("");
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}
else $user = getUser($uname);

if (!userIsAdmin($uname)) $setdefault=false;

//-- get the blocks list
if ($command=="user") {
	$ublocks = getBlocks($uname);
	if (($action=="reset") || ((count($ublocks["main"])==0) && (count($ublocks["right"])==0))) {
		$ublocks["main"] = array();
		$ublocks["main"][] = array("print_todays_events", "");
		$ublocks["main"][] = array("print_user_messages", "");
		$ublocks["main"][] = array("print_user_favorites", "");

		$ublocks["right"] = array();
		$ublocks["right"][] = array("print_welcome_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_upcoming_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
}
else {
	$ublocks = getBlocks($GEDCOM);
	if (($action=="reset") or ((count($ublocks["main"])==0) and (count($ublocks["right"])==0))) {
		$ublocks["main"] = array();
		$ublocks["main"][] = array("print_gedcom_stats", "");
		$ublocks["main"][] = array("print_gedcom_news", "");
		$ublocks["main"][] = array("print_gedcom_favorites", "");
		$ublocks["main"][] = array("review_changes_block", "");

		$ublocks["right"] = array();
		$ublocks["right"][] = array("print_gedcom_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_todays_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
}

if ($command=="user") print_simple_header($pgv_lang["mygedview"]);
else print_simple_header($GEDCOMS[$GEDCOM]["title"]);

?>
<script language="JavaScript" type="text/javascript">
<!--
function parentrefresh() {
	window.opener.refreshpage();
	window.close();
}
//-->
</script>
<?php
if ($action=="updateconfig") {
	$block = $ublocks[$side][$index];
	if (isset($PGV_BLOCKS[$block[0]]["canconfig"]) && $PGV_BLOCKS[$block[0]]["canconfig"] && isset($PGV_BLOCKS[$block[0]]["config"]) && is_array($PGV_BLOCKS[$block[0]]["config"])) {
		$config = $block[1];
		foreach($PGV_BLOCKS[$block[0]]["config"] as $config_name=>$config_value) {
			if (isset($_POST[$config_name])) {
				$config[$config_name] = stripslashes($_POST[$config_name]);
			}
		}
		$ublocks[$side][$index][1] = $config;
		setBlocks($name, $ublocks, $setdefault);
	}
	print $pgv_lang["config_update_ok"]."<br />\n";
	if (isset($_POST["nextaction"])) $action = $_POST["nextaction"];
}

if ($action=="update") {
	$newublocks["main"] = array();
	if (is_array($main)) {
		foreach($main as $indexval => $b) {
			$config = "";
			$index = "";
			reset($ublocks["main"]);
			foreach($ublocks["main"] as $index=>$block) {
				if ($block[0]==$b) {
					$config = $block[1];
					break;
				}
			}
			if ($index!="") unset($ublocks["main"][$index]);
			$newublocks["main"][] = array($b, $config);
		}
	}

	$newublocks["right"] = array();
	if (is_array($right)) {
		foreach($right as $indexval => $b) {
			$config = "";
			$index = "";
			reset($ublocks["right"]);
			foreach($ublocks["right"] as $index=>$block) {
				if ($block[0]==$b) {
					$config = $block[1];
					break;
				}
			}
			if ($index!="") unset($ublocks["right"][$index]);
			$newublocks["right"][] = array($b, $config);
		}
	}
	$ublocks = $newublocks;
	setBlocks($name, $ublocks, $setdefault);

	if (isset($_POST["nextaction"])) $action = $_POST["nextaction"];
}

if ($action=="configure") {
	$block = $ublocks[$side][$index];
	print "<h2>".$pgv_lang["config_block"]."</h2>\n";
	print "<b>".$PGV_BLOCKS[$block[0]]["name"]."</b><br />\n";
	print "\n<form method=\"post\" action=\"index_edit.php\">\n";
	print "<input type=\"hidden\" name=\"command\" value=\"$command\" />\n";
	print "<input type=\"hidden\" name=\"action\" value=\"updateconfig\" />\n";
	print "<input type=\"hidden\" name=\"name\" value=\"$name\" />\n";
	print "<input type=\"hidden\" name=\"nextaction\" value=\"configure\" />\n";
	print "<input type=\"hidden\" name=\"side\" value=\"$side\" />\n";
	print "<input type=\"hidden\" name=\"index\" value=\"$index\" />\n";
	if ($PGV_BLOCKS[$block[0]]["canconfig"]) {
		eval($block[0]."_config(\$block[1]);");
		print "<br /><br /><input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
	}
	else {
		print "This block cannot be configured.";
	}
	print "</form>\n";
}
else {
?>
<script language="JavaScript" type="text/javascript">
<!--
/**
 * Add Block JavaScript function
 *
 * This function adds an option from the add select list to the section select list
 * @param String section_name the name of the select to add the option to
 * @param String add_name the name of the select to get the option from
 */
function add_block(section_name, add_name) {
	section_select = document.getElementById(section_name);
	add_select = document.getElementById(add_name);
	if ((section_select) && (add_select)) {
		if (add_select.selectedIndex == 0) return false;
		add_option = add_select.options[add_select.selectedIndex];
		section_select.options[section_select.length] = new Option(add_option.text, add_option.value);
	}
}

/**
 * Romove Block JavaScript function
 *
 * This function removes the selected option from the given select list
 * @param String section_name the name of the select to remove the selected option from
 */
function remove_block(section_name) {
	section_select = document.getElementById(section_name);
	if (section_select) {
		if (section_select.selectedIndex == -1) return false;
		section_select.options[section_select.selectedIndex] = null;
	}
}

/**
 * Move Up Block JavaScript function
 *
 * This function moves the selected option up in the given select list
 * @param String section_name the name of the select to move the options
 */
function move_up_block(section_name) {
	section_select = document.getElementById(section_name);
	if (section_select) {
		if (section_select.selectedIndex <= 0) return false;
		index = section_select.selectedIndex;
		temp = new Option(section_select.options[index-1].text, section_select.options[index-1].value);
		section_select.options[index-1] = new Option(section_select.options[index].text, section_select.options[index].value);
		section_select.options[index] = temp;
		section_select.selectedIndex = index-1;
	}
}

/**
 * Move Down Block JavaScript function
 *
 * This function moves the selected option down in the given select list
 * @param String section_name the name of the select to move the options
 */
function move_down_block(section_name) {
	section_select = document.getElementById(section_name);
	if (section_select) {
		if (section_select.selectedIndex < 0) return false;
		if (section_select.selectedIndex >= section_select.length-1) return false;
		index = section_select.selectedIndex;
		temp = new Option(section_select.options[index+1].text, section_select.options[index+1].value);
		section_select.options[index+1] = new Option(section_select.options[index].text, section_select.options[index].value);
		section_select.options[index] = temp;
		section_select.selectedIndex = index+1;
	}
}

/**
 * Move Block from one column to the other JavaScript function
 *
 * This function moves the selected option down in the given select list
 * @author KosherJava
 * @param String add_to_column the name of the select to move the option to
 * @param String remove_from_column the name of the select to remove the option from
 */
function move_left_right_block(add_to_column, remove_from_column) {
	section_select = document.getElementById(remove_from_column);
	add_select = document.getElementById(add_to_column);
	if ((section_select) && (add_select)) {
		add_option = add_select.options[add_select.selectedIndex];
		section_select.options[section_select.length] = new Option(add_option.text, add_option.value);
		add_select.options[add_select.selectedIndex] = null; //remove from list
	}
}

/**
 * Select Options JavaScript function
 *
 * This function selects all the options in the multiple select lists
 */
function select_options() {
	section_select = document.getElementById('main_select');
	if (section_select) {
		for(i=0; i<section_select.length; i++) {
			section_select.options[i].selected=true;
		}
	}
	section_select = document.getElementById('right_select');
	if (section_select) {
		for(i=0; i<section_select.length; i++) {
			section_select.options[i].selected=true;
		}
	}
	return true;
}

<?php
print "var block_descr = new Array();\n";
foreach($PGV_BLOCKS as $b=>$block) {
	print "block_descr['$b'] = '".preg_replace("/'/", "\\'", $block["descr"])."';\n";
}
?>

/**
 * Show Block Description JavaScript function
 *
 * This function shows a description for the selected option
 * @param String add_name the name of the select to get the option from
 */
function show_description(add_name) {
	add_select = document.getElementById(add_name);
	instruct = document.getElementById('instructions');
	if (add_select && instruct) {
		if (add_select.selectedIndex == 0) instruct.innerHTML = "";
		else instruct.innerHTML = block_descr[add_select.options[add_select.selectedIndex].value];
	}
}
//-->
</script>
<?php
if ($command=="user") print "<b>".str2upper($pgv_lang["customize_page"])."</b><br />";
else print "<b>".str2upper($pgv_lang["customize_gedcom_page"])."</b><br />";
print $pgv_lang["portal_config_intructions"];

print "\n<form method=\"post\" action=\"index_edit.php\" onsubmit=\"return select_options();\">\n<input type=\"hidden\" name=\"command\" value=\"$command\" />\n<input type=\"hidden\" name=\"action\" value=\"update\" />\n<input type=\"hidden\" name=\"name\" value=\"$name\" />\n";
print "<table border=\"1\">\n";
print "<tr><td valign=\"top\" class=\"list_value\">\n";
print "<b>".$pgv_lang["main_section"]."</b>";
print "</td>\n";
print "<td class=\"list_value\">";
print "<b>".$pgv_lang["right_section"]."</b>";
print "</td></tr>\n";
print "<tr><td valign=\"top\">\n";
print "<table><tr><td>";
print "<select multiple=\"multiple\" style=\"text-align:left;\" id=\"main_select\" name=\"main[]\" size=\"10\">\n";
//-- start of main content section
foreach($ublocks["main"] as $indexval => $block) {
	if (function_exists($block[0])) print "<option value=\"$block[0]\">".$PGV_BLOCKS[$block[0]]["name"]."</option>\n";
}
//-- end of main content section
print "</select>\n";
print "</td><td>";
print "<input type=\"button\" class=\"details1\" onclick=\"move_up_block('main_select');\" value=\"".$pgv_lang["move_up"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"move_down_block('main_select');\" value=\"".$pgv_lang["move_down"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"move_left_right_block('main_select', 'right_select');\" value=\"".$pgv_lang["move_right"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"remove_block('main_select');\" value=\"".$pgv_lang["remove"]."\" />\n";

print "</td></tr></table>\n";
print "</td><td valign=\"top\">\n";
print "<table><tr><td>";
print "<select multiple=\"multiple\" style=\"text-align:left;\" id=\"right_select\" name=\"right[]\" size=\"10\">\n";
//-- start of blocks section
foreach($ublocks["right"] as $indexval => $block) {
	if (function_exists($block[0])) print "<option value=\"$block[0]\">".$PGV_BLOCKS[$block[0]]["name"]."</option>\n";
}
//-- end of blocks section
print "</select>\n";
print "</td><td>";
print "<input type=\"button\" class=\"details1\" onclick=\"move_up_block('right_select');\" value=\"".$pgv_lang["move_up"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"move_down_block('right_select');\" value=\"".$pgv_lang["move_down"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"move_left_right_block('right_select', 'main_select');\" value=\"".$pgv_lang["move_left"]."\" />\n";
print "<input type=\"button\" class=\"details1\" onclick=\"remove_block('right_select');\" value=\"".$pgv_lang["remove"]."\" />\n";
print "</td></tr></table>\n";
print "</td></tr>\n";
print "<tr><td>\n";
print "<select style=\"text-align:left;\" id=\"main_add\" name=\"main_add\" onchange=\"show_description('main_add');\">\n";
print "<option value=\"\">".$pgv_lang["add_main_block"]."</option>\n";
foreach($PGV_BLOCKS as $b=>$BLOCK) {
	if (!isset($BLOCK["type"])) $BLOCK["type"]=$command;
	print "<option value=\"$b\">".$BLOCK["name"]."</option>\n";
}
print "</select>\n";
print "<input type=\"button\" class=\"details1\" onclick=\"add_block('main_select', 'main_add');\" value=\"".$pgv_lang["add"]."\" />\n";
print "</td><td>\n";
print "<select style=\"text-align:left;\" id=\"right_add\" name=\"right_add\" onchange=\"show_description('right_add');\">\n";
print "<option value=\"\">".$pgv_lang["add_right_block"]."</option>\n";
foreach($PGV_BLOCKS as $b=>$BLOCK) {
	if (!isset($BLOCK["type"])) $BLOCK["type"]=$command;
	print "<option value=\"$b\">".$BLOCK["name"]."</option>\n";
}
print "</select>\n";
print "<input type=\"button\" class=\"details1\" onclick=\"add_block('right_select', 'right_add');\" value=\"".$pgv_lang["add"]."\" />\n";
print "</td></tr>\n";
print "<tr><td colspan=\"2\">\n";
print "<div id=\"instructions\"></div>\n";
if ((userIsAdmin($uname))&&($command=='user')) {
	print $pgv_lang["use_blocks_for_default"]."<input type=\"checkbox\" name=\"setdefault\" value=\"1\" /><br />\n";
}
print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
print "<input type=\"button\" value=\"".$pgv_lang["reset_default_blocks"]."\" onclick=\"window.location='index_edit.php?command=$command&amp;action=reset&amp;name=".preg_replace("/'/", "\'", $name)."';\" />\n";
print "</td></tr>\n";
print "</table>\n";
print "</form>\n";
}
//-- end default action
print "<br /><br /><br /><center>";
print "<a href=\"#\" onclick=\"parentrefresh();\">".$pgv_lang["close_window"]."</a></center>";
print_simple_footer();
?>