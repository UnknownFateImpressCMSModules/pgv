<?php
/**
 * Gedcom Welcome Block
 *
 * This block prints basic information about the active gedcom
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
 * $Id: gedcom_block.php,v 1.1 2005/10/07 18:08:13 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */
 
$PGV_BLOCKS["print_gedcom_block"]["name"]        = $pgv_lang["gedcom_block"];
$PGV_BLOCKS["print_gedcom_block"]["descr"]       = $pgv_lang["gedcom_descr"];
$PGV_BLOCKS["print_gedcom_block"]["type"]        = "gedcom";
$PGV_BLOCKS["print_gedcom_block"]["canconfig"]   = false;

//-- function to print the gedcom block
function print_gedcom_block($block = true, $config="", $side, $index) {
	global $hits,$pgv_lang, $day, $month, $year, $GEDCOM, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $TIME_FORMAT,$SHOW_COUNTER, $command,$THEME_DIR,$TEXT_DIRECTION;


	print "<div id=\"gedcom_welcome\" class=\"block\" >\n";
	print "<table class=\"blockheader\" cellpadding=\"0\" cellspacing=\"0\" style=\"direction:ltr;padding:0;margin:0;\"><tr>";
	print "<td class=\"blockh1\" ></td>";
	print "<td class=\"blockh2\" ><div class=\"blockhc\">";
	print PrintReady("<b>".$GEDCOMS[$GEDCOM]["title"]."</b>");	 
	print "</div></td>";
	print "<td class=\"blockh3\"></td></tr></table>\n";
	print "<div class=\"blockcontent\">";
	print "<div class=\"center\">";
	print "<br />".get_changed_date("$day $month $year")." - ".date($TIME_FORMAT, time()-$_SESSION["timediff"])."<br />\n";
	if($SHOW_COUNTER)
			print $pgv_lang["hit_count"]."  ".$hits."<br />\n";
	print "\n<br />";
	if (userGedcomAdmin(getUserName())) {
		print "<a href=\"#\" onclick=\"window.open('index_edit.php?name=".preg_replace("/'/", "\'", $GEDCOM)."&amp;command=gedcom', '', 'top=50,left=50,width=600,height=550,scrollbars=1,resizable=1'); return false;\">".$pgv_lang["customize_gedcom_page"]."</a>\n";
	}
	print "</div>";
	print "</div>\n";
	print "</div>";
}
?>