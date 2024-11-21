<?php
/**
 * User Favorites Block
 *
 * This block will print a users favorites
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
 * $Id: user_favorites.php,v 1.1 2005/10/07 18:08:13 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */
 
$PGV_BLOCKS["print_user_favorites"]["name"]        = $pgv_lang["user_favorites_block"];
$PGV_BLOCKS["print_user_favorites"]["descr"]        = $pgv_lang["user_favorites_descr"];
$PGV_BLOCKS["print_user_favorites"]["type"]        = "user";
$PGV_BLOCKS["print_user_favorites"]["canconfig"]        = false;

//-- print user favorites
function print_user_favorites($block=true, $config="", $side, $index) {
		global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $TEXT_DIRECTION, $INDEX_DIRECTORY, $MEDIA_DIRECTORY, $MULTI_MEDIA, $MEDIA_DIRECTORY_LEVELS, $command, $PGV_DATABASE, $indilist;

		$userfavs = getUserFavorites(getUserName());
		if (!is_array($userfavs)) $userfavs = array();
		print "<div id=\"user_favorites\" class=\"block\">\n";
		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["my_favorites"]." &lrm;(".count($userfavs).")&lrm;</b>";
		print_help_link("mygedview_favorites_help", "qm");
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\">";
		if ($block) print "<div class=\"small_inner_block\">\n";
		if (count($userfavs)==0) {
		print $pgv_lang["no_favorites"];
		print "\n";
		} else {
		print "<table width=\"100%\" class=\"$TEXT_DIRECTION\">";
		$mygedcom = $GEDCOM;
		$current_gedcom = $GEDCOM;
		if ($block) $style = 1;
		else $style = 2;
		foreach($userfavs as $key=>$favorite) {
			if ($favorite["type"]=="INDI") {
				$current_gedcom = $GEDCOM;
				$GEDCOM = $favorite["file"];
				require $INDEX_DIRECTORY.$GEDCOM."_conf.php";
				if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
					$indexfile = $INDEX_DIRECTORY.$GEDCOM."_index.php";
					$fp = fopen($indexfile, "r");
					$fcontents = fread($fp, filesize($indexfile));
					fclose($fp);
					$lists = unserialize($fcontents);
					unset($fcontents);
					$indilist = $lists["indilist"];
					$current_gedcom = $GEDCOM;
				}
				$indirec = find_person_record($favorite["gid"]);
				print "<tr><td>";
				if (isset($favorite["id"])) $key=$favorite["id"];
				print "<div id=\"box".$favorite["gid"].".0\" class=\"person_box";
				if (preg_match("/1 SEX F/", $indirec)>0) print "F";
				else if (preg_match("/1 SEX M/", $indirec)>0) print "";
				else print "NN";
				print "\">\n";
				print_pedigree_person($favorite["gid"], $style, 1, $key);
				print "</div>\n";
				if ($command=="user" || userIsAdmin(getUserName())) print "<font size=\"1\"><a href=\"index.php?command=$command&amp;action=deletefav&amp;fv_id=".$key."\" onclick=\"return confirm('".$pgv_lang["confirm_fav_remove"]."');\">".$pgv_lang["remove"]."</a><br />\n";
				print "</td></tr>\n";
			}
		}
		$GEDCOM = $mygedcom;
		require $INDEX_DIRECTORY.$GEDCOM."_conf.php";
		if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
			$indexfile = $INDEX_DIRECTORY.$GEDCOM."_index.php";
			$fp = fopen($indexfile, "r");
			$fcontents = fread($fp, filesize($indexfile));
			fclose($fp);
			$lists = unserialize($fcontents);
			unset($fcontents);
			$indilist = $lists["indilist"];
		}
			print "</table>\n";
		}
	?>
<script language="JavaScript" type="text/javascript">
<!--
var pasteto;
function iopenfind(textbox) {
		pasteto = textbox;
		findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
}
function paste_id(value) {
		pasteto.value=value;
		document.addfavform.submit();
}
//-->
</script>
<?php
		print "<form name=\"addfavform\" method=\"get\" action=\"index.php\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"addfav\" />\n";
		print "<input class=\"pedigree_form\" type=\"text\" name=\"gid\" size=\"3\" value=\"\" /><a href=\"#\" onclick=\"iopenfind(document.addfavform.gid); return false;\"> ".$pgv_lang["find_id"]."</a>";
		print " <input type=\"submit\" value=\"".$pgv_lang["add"]."\" style=\"font-size: 8pt; \" />\n";
		print "\n</form>\n";
		if ($block) print "</div>\n";
		print "</div>\n"; // content
		print "</div>";   // block
}
?>