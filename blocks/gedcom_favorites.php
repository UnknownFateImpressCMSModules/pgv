<?php
/**
 * Gedcom Favorites Block
 *
 * This block prints the active gedcom favorites
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
 * $Id: gedcom_favorites.php,v 1.5 2005/09/15 16:39:27 yalnifj Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */
 
$PGV_BLOCKS["print_gedcom_favorites"]["name"]        = $pgv_lang["gedcom_favorites_block"];
$PGV_BLOCKS["print_gedcom_favorites"]["descr"]        = $pgv_lang["gedcom_favorites_descr"];
$PGV_BLOCKS["print_gedcom_favorites"]["type"]        = "gedcom";
$PGV_BLOCKS["print_gedcom_favorites"]["canconfig"]   = false;

//-- print gedcom favorites
function print_gedcom_favorites($block = true, $config="", $side, $index) {
		global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $command;

		$userfavs = getUserFavorites($GEDCOM);
		if (!is_array($userfavs)) $userfavs = array();
		print "<div id=\"gedcom_favorites\" class=\"block\">\n";

		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["gedcom_favorites"]." &lrm;(".count($userfavs).")&lrm;</b>";
		print_help_link("index_favorites_help", "qm");
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\">";
		if ($block) print "<div class=\"small_inner_block\">\n";
		if (count($userfavs)==0) {
				if (userGedcomAdmin(getUserName())) print $pgv_lang["no_favorites"];
				else print $pgv_lang["no_gedcom_favorites"];
		}
		if ($block) $style = 1;
		else $style = 2;
		foreach($userfavs as $key=>$favorite) {
			if ($favorite["type"]=="INDI") {
				$indirec = find_person_record($favorite["gid"]);
				if (displayDetailsbyId($favorite["gid"])) {
					if (isset($favorite["id"])) $key=$favorite["id"];
					print "<div id=\"box".$favorite["gid"].".0\" class=\"person_box";
					if (preg_match("/1 SEX F/", $indirec)>0) print "F";
					else if (preg_match("/1 SEX M/", $indirec)>0) print "";
					else print "NN";
					print "\" style=\"position: static; left: 0px; top: auto; width: 99%; z-index: 1;\">\n";
					print_pedigree_person($favorite["gid"], $style, 1);
					if ($command=="user" || userGedcomAdmin(getUserName())) print "<font size=\"1\"><a href=\"index.php?command=$command&amp;action=deletefav&amp;fv_id=".$key."\" onclick=\"return confirm('".$pgv_lang["confirm_fav_remove"]."');\">".$pgv_lang["remove"]."</a></font><br />\n";
					print "</div>\n";
				}
			}
		}
		if (userGedcomAdmin(getUserName())) { ?>
			<script language="JavaScript" type="text/javascript">
			<!--
			var gpasteto;
			var gform;
			function giopenfind(textbox, form) {
					gpasteto = textbox;
					gform = form;
					findwin = window.open('findid.php?callback=gpaste_id', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
			}
			function gpaste_id(value) {
					gpasteto.value=value;
					gform.submit();
			}
			//-->
			</script>
			<?php
			print "<form name=\"addgfavform\" method=\"get\" action=\"index.php\">\n";
			print "<input type=\"hidden\" name=\"action\" value=\"addfav\" />\n";
			print "<input type=\"hidden\" name=\"command\" value=\"$command\" />\n";
			print "<input type=\"hidden\" name=\"favtype\" value=\"gedcom\" />\n";
			print "<input class=\"pedigree_form\" type=\"text\" name=\"gid\" size=\"3\" value=\"\" /><font size=\"1\"><a href=\"#\" onclick=\"giopenfind(document.addgfavform.gid, document.addgfavform); return false;\"> ".$pgv_lang["find_id"]."</a></font>";
			print " <input type=\"submit\" value=\"".$pgv_lang["add"]."\" style=\"font-size: 8pt; \" />\n";
			print "\n</form>\n";
		}
		if ($block) print "</div>\n";
		print "</div>"; // blockcontent
		print "</div>"; // block
}
?>