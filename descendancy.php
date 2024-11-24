<?php
/**
 * Parses gedcom file and displays a descendancy tree.
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
 * @subpackage Charts
 * @version $Id: descendancy.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

// -- include config file
require("config.php");
require("includes/functions_charts.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

/**
 * print a child descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $depth the descendancy depth to show
 */
function print_child_descendancy($pid, $depth) {
	global $pgv_lang, $view, $show_full, $generations, $box_width;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent;
	global $dabo;

	// print child
	print "<li>";
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
	if ($depth==$generations) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	else                      print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	print_pedigree_person($pid, 1, $view!="preview");
	print "</td>";

	// check if child has parents and add an arrow
	print "<td>&nbsp;</td>";
	print "<td>";
	$sfamids = find_family_ids($pid);
	foreach($sfamids as $indexval => $sfamid) {
		$parents = find_parents($sfamid);
		if ($parents) {
			$parid=$parents["HUSB"];
			if ($parid=="") $parid=$parents["WIFE"];
			if ($parid!="") print_url_arrow($parid.$pid, "?pid=$parid&amp;generations=$generations&amp;show_full=$show_full&amp;box_width=$box_width", $pgv_lang["start_at_parents"], 2);
		}
	}

	// d'Aboville child number
	$level =$generations-$depth;
	if ($show_full) print "<br /><br />&nbsp;";
	print "<span dir=\"ltr\">"; //needed so that RTL languages will display this properly
	if (!isset($dabo[$level])) $dabo[$level]=0;
	$dabo[$level]++;
	$dabo[$level+1]=0;
	for ($i=0; $i<=$level;$i++) print $dabo[$i].".";
	print "</span>";
	print "</td></tr>";

	// empty descendancy
	$sfam = find_sfamily_ids($pid);
	if ($depth>0 and count($sfam)<1) {
		print "<tr><td></td>";
		print "<td class=\"details1\" ><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" style=\"filter:alpha(opacity=40);-moz-opacity:0.4\" alt=\"".$pgv_lang["no_children"]."\" title=\"".$pgv_lang["no_children"]."\" /> ".$pgv_lang["no_children"]."</td>";
		print "<td></td></tr>";
	}
	print "</table>";
	print "</li>\r\n";
	if ($depth<1) return;

	// loop for each spouse
	foreach ($sfam as $indexval => $famid) {
		print_family_descendancy($pid, $famid, $depth);
	}
}

/**
 * print a family descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param string $famid family Gedcom Id
 * @param int $depth the descendancy depth to show
 */
function print_family_descendancy($pid, $famid, $depth) {
	global $pgv_lang, $factarray, $view, $show_full, $generations, $box_width, $bwidth;
	global $GEDCOM, $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent;

	if ($famid=="") return;

	$famrec = find_family_record($famid);
	$parents = find_parents($famid);
	if ($parents) {

		// spouse id
		$id = $parents["WIFE"];
		if ($id==$pid) $id = $parents["HUSB"];

		// print marriage info
		print "<li>";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" />";
		print "<span class=\"details1\" style=\"white-space: nowrap; \" >";
		print "<a href=\"#\" onclick=\"expand_layer('".$famid."'); return false;\" class=\"top\"><img id=\"".$famid."_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".$pgv_lang["view_family"]."\" /></a> ";
		if (showFact("MARR", $famid)) print_simple_fact($famrec, "MARR", $id); else print $pgv_lang["private"];
		print "</span>";

		// print spouse
		print "<ul style=\"list-style: none; display: block;\" id=\"$famid\">";
		print "<li>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
		print_pedigree_person("$id", 1, $view!="preview");
		print "</td>";

		// check if spouse has parents and add an arrow
		print "<td>&nbsp;</td>";
		print "<td>";
		$sfamids = find_family_ids($id);
		foreach($sfamids as $indexval => $sfamid) {
			$parents = find_parents($sfamid);
			if ($parents) {
				$parid=$parents["HUSB"];
				if ($parid=="") $parid=$parents["WIFE"];
				if ($parid!="") print_url_arrow($parid.$pid, "?pid=$parid&amp;generations=$generations&amp;show_full=$show_full&amp;box_width=$box_width", $pgv_lang["start_at_parents"], 2);
			}
		}
		if ($show_full) print "<br /><br />&nbsp;";
		print "</td></tr>";

		// children
		$children = get_children_ids($famid);
		print "<tr><td colspan=\"3\" class=\"details1\" >&nbsp;";
		if (count($children)<1) print $pgv_lang["no_children"];
		else print $factarray["NCHI"].": ".count($children);
		print "</td></tr></table>";
		print "</li>\r\n";
		foreach ($children as $indexval => $child) {
			print_child_descendancy($child, $depth-1);
		}
		print "</ul>\r\n";
		print "</li>\r\n";
	}
}

// -- args
if (!isset($show_full)) $show_full=$PEDIGREE_FULL_DETAILS;
if (!isset($generations)) $generations = 10;
if (empty($generations)) $generations = 10;
if ($generations > $MAX_DESCENDANCY_GENERATIONS) $generations = $MAX_DESCENDANCY_GENERATIONS;
if (!isset($view)) $view="";

// -- size of the boxes
if (!isset($box_width)) $box_width = "100";
if (empty($box_width)) $box_width = "100";
$box_width=max($box_width, 50);
$box_width=min($box_width, 300);
$Dbwidth*=$box_width/100;
if ($show_full==false) {
	$Dbheight=25;
//	$Dbwidth-=40;
}
$bwidth=$Dbwidth;
$bheight=$Dbheight;

// -- root id
if (!isset($pid)) $pid="";
$pid = clean_input($pid);
$pid=check_rootid($pid);
if ((DisplayDetailsByID($pid))||(showLivingNameByID($pid))) $name = get_person_name($pid);
else $name = $pgv_lang["private"];

// -- print html header information
print_header($name." ".$pgv_lang["descend_chart"]);
if (strlen($name)<30) $cellwidth="420";
else $cellwidth=(strlen($name)*14);
print "\n\t<table class=\"list_table, $TEXT_DIRECTION\"><tr><td width=\"${cellwidth}px\" valign=\"top\">\n\t\t";
print "\n\t<h2>".$pgv_lang["descend_chart"].":<br />".PrintReady($name)."</h2>";
?>

<script type="text/javascript">
<!--
var pasteto;
function open_find(textbox) {
	pasteto = textbox;
	findwin = window.open('findid.php', '', 'left=50,top=50,width=410,height=320,resizable=1,scrollbars=1');
}
function paste_id(value) {
	pasteto.value=value;
}
//-->
</script>

<?php
$gencount=0;
if ($view!="preview") {
	print "</td><td><form method=\"get\" name=\"people\" action=\"?\">\n";
	print "\n\t\t<table class=\"list_table, $TEXT_DIRECTION\">\n\t\t<tr>";
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["root_person"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
	print "\n\t\t<input class=\"pedigree_form\" type=\"text\" name=\"pid\" size=\"3\" value=\"$pid\" />";
	print "<font size=\"1\"> <a href=\"javascript:open_find(document.people.pid);\">".$pgv_lang["find_id"]."</a></font>";
	print_help_link("desc_rootid_help", "qm");
	print "</td>";
	print "<td rowspan=\"3\" class=\"list_value\">";
	print "\n\t\t<table class=\"$TEXT_DIRECTION\" style=\"width: 100%;\">\n\t\t<tr><td class=\"sublinks_cell\" style=\" vertical-align:top;\">";
	print "<input type=\"hidden\" name=\"show_full\" value=\"$show_full\" />";
	print "<input type=\"checkbox\" value=\"";
	if ($show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';\"";
	else print "0\" onclick=\"document.people.show_full.value='1';\"";
	print " />".$pgv_lang["show_details"];
	print_help_link("show_full_help", "qm");
	print "<br /><br /><br /><br /></td><td class=\"subheaders\" style=\"width: 60px; vertical-align:bottom;text-align: ".($TEXT_DIRECTION=="rtl"?"left":"right").";\">";
	print "<input type=\"submit\" value=\"".$pgv_lang["view"]."\" />";
	print "</td></tr></table>\n";
	print "</td>";
	print "</tr><tr>";
	print "<td class=\"list_label\" >&nbsp;" . $pgv_lang["generations"] . "&nbsp;</td>";
	print "<td class=\"list_value\"><input type=\"text\" size=\"3\" name=\"generations\" value=\"$generations\" />";
	print_help_link("desc_generations_help", "qm");
	print "</td>";
	print "</tr><tr>";
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["box_width"] . "&nbsp;</td>";
	print "<td class=\"list_value\"><input type=\"text\" size=\"3\" name=\"box_width\" value=\"$box_width\" /> <b>%</b>";
	print_help_link("box_width_help", "qm");
	print "</td>";
	print "</tr></table>";
	print "</form>\n";
}
print "</td></tr></table>";
?>

<?php
// -- "d'Aboville" numbering system [ http://www.saintclair.org/numbers/numdob.html ]
$dabo=array();

// -- root descendancy
print "<ul style=\"list-style: none; display: block;\" id=\"descendancy_chart".($TEXT_DIRECTION=="rtl" ? "_rtl" : "") ."\">\r\n";
print_child_descendancy($pid, $generations);
print "</ul>";
print "<br />";

print_footer();

?>