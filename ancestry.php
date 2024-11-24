<?php
/**
 * Displays pedigree tree as a printable booklet
 *
 * with Sosa-Stradonitz numbering system
 * ($rootid=1, father=2, mother=3 ...)
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
 * @version $Id: ancestry.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */
require("config.php");
require("includes/functions_charts.php");
require($PGV_BASE_DIRECTORY . $factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

/**
 * print a child ascendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $sosa child sosa number
 * @param int $depth the ascendancy depth to show
 */
function print_child_ascendancy($pid, $sosa, $depth) {
	global $pgv_lang, $view, $show_full, $OLD_PGENS, $box_width, $chart_style;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent;
	global $SHOW_EMPTY_BOXES, $pidarr;

	// child
	print "<li name=\"sosa$sosa\">";
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a name=\"sosa".$sosa."\"></a>";
	$new=($pid=="" or !isset($pidarr["$pid"]));
	if ($sosa==1) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" /></td><td>\n";
	else          print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" /></td><td>\n";
	print_pedigree_person($pid, 1, $view!="preview");
	print "</td>";
	print "<td>";
	if ($sosa>1) print_url_arrow($pid, "?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;show_full=$show_full&amp;box_width=$box_width&amp;chart_style=$chart_style", $pgv_lang["ancestry_chart"], 3);
	print "</td>";
	print "<td class=\"details1\">&nbsp;<span class=\"person_box". (($sosa==1) ? "NN" : (($sosa%2) ? "F" : "")) . "\">&nbsp;$sosa&nbsp;</span>&nbsp;";
	print "</td><td class=\"details1\">";
	$relation ="";
	if (!$new) $relation = "<br />[=<a href=\"#sosa".$pidarr["$pid"]."\">".$pidarr["$pid"]."</a> - ".get_sosa_name($pidarr["$pid"])."]";
	else $pidarr["$pid"]=$sosa;
	print get_sosa_name($sosa).$relation;
	print "</td>";
	print "</tr></table>";

	// parents
	$famids = find_family_ids($pid);
	$famid = @$famids[0];
	$famrec = find_family_record($famid);
	$parents = @find_parents($famid);
	if (($parents or $SHOW_EMPTY_BOXES) and $new and $depth>0) {
		// print marriage info
		print "<span class=\"details1\" style=\"white-space: nowrap;\" >";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" align=\"middle\" /><a href=\"#\" onclick=\"expand_layer('".$sosa."'); return false;\" class=\"top\"><img id=\"".$sosa."_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".$pgv_lang["view_family"]."\" /></a> ";
//		print $pgv_lang["parents"]. " : ";
    	print "&nbsp;<span class=\"person_box\">&nbsp;".($sosa*2)."&nbsp;</span>&nbsp;".$pgv_lang["and"];
 		print "&nbsp;<span class=\"person_boxF\">&nbsp;".($sosa*2+1)." </span>&nbsp;";
		if (showFact("MARR", $famid)) print_simple_fact($famrec, "MARR", $parents["WIFE"]); else print $pgv_lang["private"];
		print "</span>";
		// display parents recursively
		print "<ul style=\"list-style: none; display: block;\" id=\"$sosa\">"; 
		print_child_ascendancy($parents["HUSB"], $sosa*2, $depth-1);
		print_child_ascendancy($parents["WIFE"], $sosa*2+1, $depth-1);
		print "</ul>\r\n";
	}
	print "</li>\r\n";
}

// -- args

if (!isset($show_full)) $show_full = $PEDIGREE_FULL_DETAILS;
if ($show_full == "") $show_full = 0;
if (!isset($chart_style)) $chart_style = 1;
if ($chart_style=="") $chart_style = 1;
if (!isset($show_cousins)) $show_cousins = 1;
if ($show_cousins == "") $show_cousins = 0;
if ((!isset($PEDIGREE_GENERATIONS)) || ($PEDIGREE_GENERATIONS == "")) $PEDIGREE_GENERATIONS = $DEFAULT_PEDIGREE_GENERATIONS;

if ($PEDIGREE_GENERATIONS > $MAX_PEDIGREE_GENERATIONS) {
	$PEDIGREE_GENERATIONS = $MAX_PEDIGREE_GENERATIONS;
	$max_generation = true;
}

if ($PEDIGREE_GENERATIONS < 2) {
	$PEDIGREE_GENERATIONS = 2;
	$min_generation = true;
}
$OLD_PGENS = $PEDIGREE_GENERATIONS;

if (!isset($rootid)) $rootid = "";
$rootid = clean_input($rootid);
$rootid = check_rootid($rootid);

// -- size of the boxes
if (!isset($box_width)) $box_width = "100";
if (empty($box_width)) $box_width = "100";
$box_width=max($box_width, 50);
$box_width=min($box_width, 300);
$Dbwidth*=$box_width/100;
if (!$show_full) $Dbheight=25;
$bwidth=$Dbwidth;
$bheight=$Dbheight;
$pbwidth = $bwidth+12;
$pbheight = $bheight+14;

if ((DisplayDetailsByID($rootid)) || (showLivingNameByID($rootid))) {
	$name = get_person_name($rootid);
	$addname = get_add_person_name($rootid);
}
else {
	$name = $pgv_lang["private"];
	$addname = "";
}
// -- print html header information
print_header($name . " " . $pgv_lang["ancestry_chart"]);
if (strlen($name)<30) $cellwidth="420";
else $cellwidth=(strlen($name)*14);
print "\n\t<table class=\"list_table, $TEXT_DIRECTION\"><tr><td width=\"${cellwidth}px\" valign=\"top\">\n\t\t";
if ($view == "preview") print "<h2>" . str_replace("#PEDIGREE_GENERATIONS#", convert_number($PEDIGREE_GENERATIONS), $pgv_lang["gen_ancestry_chart"]) . ":";
else print "<h2>" . $pgv_lang["ancestry_chart"] . ":";
print "<br />".PrintReady($name);
if ($addname != "") print "<br />" . PrintReady($addname);
print "</h2>";
// -- print the form to change the number of displayed generations
if ($view != "preview") {
	$show_famlink = true;
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
	//-->
	</script>
	<?php
	if (isset($max_generation) == true) print "<span class=\"error\">" . str_replace("#PEDIGREE_GENERATIONS#", convert_number($PEDIGREE_GENERATIONS), $pgv_lang["max_generation"]) . "</span>";
	if (isset($min_generation) == true) print "<span class=\"error\">" . $pgv_lang["min_generation"] . "</span>";
	print "\n\t</td><td><form name=\"people\" method=\"get\" action=\"?\">";
	print "<input type=\"hidden\" name=\"chart_style\" value=\"$chart_style\" />";
	print "<input type=\"hidden\" name=\"show_full\" value=\"$show_full\" />";
	print "<input type=\"hidden\" name=\"show_cousins\" value=\"$show_cousins\" />";
	print "\n\t\t<table class=\"list_table, $TEXT_DIRECTION\">\n\t\t<tr>";
	// rootid
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["root_person"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
	print "<input class=\"pedigree_form\" type=\"text\" name=\"rootid\" size=\"3\" value=\"$rootid\" /><font size=\"1\"> <a href=\"javascript:open_find(document.people.rootid);\">" . $pgv_lang["find_id"] . "</a></font> ";
	print_help_link("rootid_help", "qm");
	print "</td>";
	// chart style
	print "<td rowspan=\"3\" class=\"list_value\">";
	print "\n\t\t<table class=\"$TEXT_DIRECTION\" style=\" width: 100%;\">\n\t\t<tr><td class=\"sublinks_cell\" style=\" white-space: nowrap;\">";
	print "<input type=\"radio\" name=\"chart_style\" value=\"0\"";
	if (!$chart_style) print " checked=\"checked\"";
	else print " onclick=\"document.people.chart_style.value='1';\"";
	print " />".$pgv_lang["ancestry_list"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"1\"";
	if ($chart_style) print " checked=\"checked\"";
	else print " onclick=\"document.people.chart_style.value='0';\"";
	print " />".$pgv_lang["ancestry_booklet"];
	print_help_link("chart_style_help", "qm");
	// show cousins
	print "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" value=\"";
	if ($show_cousins) print "1\" checked=\"checked\" onclick=\"document.people.show_cousins.value='0';\"";
	else print "0\" onclick=\"document.people.show_cousins.value='1';\"";
	print " />" . $pgv_lang["show_cousins"];
	print_help_link("show_cousins_help", "qm");
	// show full
	print "<br /><br /><input type=\"checkbox\" value=\"";
	if ($show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';\"";
	else print "0\" onclick=\"document.people.show_full.value='1';\"";
	print " />" . $pgv_lang["show_details"];
	print_help_link("show_full_help", "qm");
	// submit
	print "</td><td class=\"subheaders\" style=\"width: 60px; vertical-align:bottom;text-align: ".($TEXT_DIRECTION=="rtl"?"left":"right").";\">";
	print "<input type=\"submit\" value=\"" . $pgv_lang["view"] . "\" />";
	print "</td></tr></table>\n";
	print "</td>";
	print "</tr><tr>";
	// generations
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["generations"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
	print "<input type=\"text\" name=\"PEDIGREE_GENERATIONS\" size=\"3\" value=\"$OLD_PGENS\" /> ";
	print_help_link("PEDIGREE_GENERATIONS_help", "qm");
	print "</td>";
	print "</tr><tr>";
	// box width
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["box_width"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
	print "<input type=\"text\" size=\"3\" name=\"box_width\" value=\"$box_width\" /> <b>%</b> ";
	print_help_link("box_width_help", "qm");
	print "</td>";
	print "</tr></table>";
	print "\n\t\t</form><br />";
}
print "</td></tr></table>";

if ($chart_style) {
	// first page : show indi facts
	print_pedigree_person($rootid, 2, false, 1);
	// expand the layer
	echo <<< END
	<script language="JavaScript" type="text/javascript">
		expandbox("$rootid.1", 2);
	</script>
	<br />
END;
	// process the tree
	$treeid = pedigree_array($rootid);
	$treesize = pow(2, (int)($PEDIGREE_GENERATIONS))-1;
	for ($i = 0; $i < $treesize; $i++) {
		$pid = $treeid[$i];
		if ($pid) {
			$famids = find_family_ids($pid);
			$parents = @find_parents($famids[0]);
			if ($parents) print_sosa_family($famids[0], $pid, $i + 1);
			// show empty family only if it is the first and only one
			else if ($i == 0) print_sosa_family("", $pid, $i + 1);
		}
	}
}
else {
	$pidarr=array();
	print "<ul style=\"list-style: none; display: block;\" id=\"ancestry_chart".($TEXT_DIRECTION=="rtl" ? "_rtl" : "") ."\">\r\n";
	print_child_ascendancy($rootid, 1, $OLD_PGENS);
	print "</ul>";
	print "<br />";
}

print_footer();
?>