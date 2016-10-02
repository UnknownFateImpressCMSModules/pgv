<?php
/**
 * Gedcom Statistics Block
 *
 * This block prints statistical information for the currently active gedcom
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
 * @version $Id: gedcom_stats.php,v 1.2 2006/01/09 00:46:22 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */

$PGV_BLOCKS["print_gedcom_stats"]["name"]        = $pgv_lang["gedcom_stats_block"];
$PGV_BLOCKS["print_gedcom_stats"]["descr"]        = $pgv_lang["gedcom_stats_descr"];
$PGV_BLOCKS["print_gedcom_stats"]["canconfig"]   = true;
$PGV_BLOCKS["print_gedcom_stats"]["config"] = array("show_common_surnames"=>"yes");

//-- function to print the gedcom statistics block

function print_gedcom_stats($block = true, $config="", $side, $index) {
		global $PGV_BLOCKS, $pgv_lang, $day, $month, $year, $GEDCOM, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $command, $COMMON_NAMES_THRESHOLD, $PGV_IMAGE_DIR, $PGV_IMAGES;
		global $top10_block_present;		// Set in index.php

		if (empty($config)) $config = $PGV_BLOCKS["print_gedcom_stats"]["config"];
		
		print "<div id=\"gedcom_stats\" class=\"block\">\n";
		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["gedcom_stats"]."</b>";
		print_help_link("index_stats_help", "qm");
		if ($PGV_BLOCKS["print_gedcom_stats"]["canconfig"]) {
			$username = getUserName();
			if ((($command=="gedcom")&&(userGedcomAdmin($username))) || (($command=="user")&&(!empty($username)))) {
				if ($command=="gedcom") $name = preg_replace("/'/", "\'", $GEDCOM);
				else $name = $username;
				print "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?name=$name&amp;command=$command&amp;action=configure&amp;side=$side&amp;index=$index', '', 'top=50,left=50,width=600,height=550,scrollbars=1,resizable=1'); return false;\">";
				print "<img class=\"adminicon\" src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$pgv_lang["config_block"]."\" /></a>\n";
			}
		}
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\">";

		print "<b><a href=\"index.php?command=gedcom\">".PrintReady($GEDCOMS[$GEDCOM]["title"])."</a></b><br />\n";
		$head = find_gedcom_record("HEAD");
		$ct=preg_match("/1 SOUR (.*)/", $head, $match);
		if ($ct>0) {
			$softrec = get_sub_record(1, "1 SOUR", $head);
			$tt= preg_match("/2 NAME (.*)/", $softrec, $tmatch);
			if ($tt>0) $title = trim($tmatch[1]);
			else $title = trim($match[1]);
			if (!empty($title)) {
					$text = str_replace("#SOFTWARE#", $title, $pgv_lang["gedcom_created_using"]);
					$tt = preg_match("/2 VERS (.*)/", $softrec, $tmatch);
					if ($tt>0) $version = trim($tmatch[1]);
					else $version="";
					$text = str_replace("#VERSION#", $version, $text);
					print $text;
			}
		}
		$ct=preg_match("/1 DATE (.*)/", $head, $match);
		if ($ct>0) {
			$date = trim($match[1]);
			if (empty($title)) $text = str_replace("#DATE#", get_changed_date($date), $pgv_lang["gedcom_created_on"]);
			else $text = $text = str_replace("#DATE#", get_changed_date($date), $pgv_lang["gedcom_created_on2"]);
			print $text;
		}
		print "<br />\n";
		print "<b>".get_list_size("indilist")."</b> ".$pgv_lang["stat_individuals"]."\n";
		print "<b>".get_list_size("famlist")."</b> ".$pgv_lang["stat_families"]."\n";
				print "<b>".get_list_size("sourcelist")."</b> ".$pgv_lang["stat_sources"]."\n";
		print "<b>".get_list_size("otherlist")."</b> ".$pgv_lang["stat_other"]."<br />\n";

		if ($config["show_common_surnames"]=="yes") {
			$surnames = get_common_surnames_index($GEDCOM);
			if (count($surnames)>0) {
				print "<br /><b>".$pgv_lang["common_surnames"]."</b>";
				print_help_link("index_common_names_help", "qm");
				print "<br />\n";
				$i=0;
				foreach($surnames as $indexval => $surname) {
					if (stristr($surname["name"], "@N.N")===false) {
						if ($i>0) {
							print ", ";
						}
						print "<a href=\"indilist.php?surname=".urlencode($surname["name"])."\">".PrintReady($surname["name"])."</a>";
						$i++;
					}
				}
			}
		}
		print "</div>\n";
		print "</div>";
}

function print_gedcom_stats_config($config) {
	global $pgv_lang, $PGV_BLOCKS;
	if (empty($config)) $config = $PGV_BLOCKS["print_gedcom_stats"]["config"];
	?>
	Show common surnames? <select name="show_common_surnames">
		<option value="yes"<?php if ($config["show_common_surnames"]=="yes") print " selected=\"selected\"";?>><?php print $pgv_lang["yes"]; ?></option>
		<option value="no"<?php if ($config["show_common_surnames"]=="no") print " selected=\"selected\"";?>><?php print $pgv_lang["no"]; ?></option>
	</select>
	<?php
}
?>