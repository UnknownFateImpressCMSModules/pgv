<?php
/**
 * Parses gedcom file and displays a list of the families in the file.
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
 * @version $Id: findfamily.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

require("config.php");
print_simple_header($pgv_lang["find_fam_list"]);
?>
<script language="JavaScript" type="text/javascript">
	function pasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
</script>
<?php
print "<center>\n";
print "\n\t<h2>".$pgv_lang["family_list"]."</h2>";
if (empty($surname_sublist)) $surname_sublist = "yes";
if (empty($show_all)) $show_all = "no";

// Remove slashes
if (isset($alpha)) $alpha = stripslashes($alpha);
if (isset($surname)) $surname = stripslashes($surname);

/**
 * Check for the @ symbol
 *
 * This variable is used for checking if the @ symbol is present in the alphabet list.
 * @global boolean $pass
 */
$pass = FALSE;

/**
 * Total famlist array
 *
 * The tfamlist array will contain families that are extracted from the database.
 * @global array $tfamlist
 */
$tfamlist = array();

/**
 * Family alpha array
 *
 * The famalpha array will contain all first letters that are extracted from families last names
 * @global array $famalpha
 */

$famalpha = get_fam_alpha();

if ($PGV_DATABASE=="index") uasort($famalpha, "lettersort");
else uasort($famalpha, "stringsort");

if (count($famalpha) > 0) {
	foreach($famalpha as $letter=>$list) {
		if (empty($alpha)) {
			if (!empty($surname)) {
				if (isRTLText($surname)) $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,2);
				else $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,1);
			}
		}
		if ($letter != "@") {
			if (!isset($startalpha) && !isset($alpha)) {
				$startalpha = $letter;
				$alpha = $letter;
			}
			print "<a href=\"findfamily.php?alpha=".urlencode($letter)."&amp;surname_sublist=$surname_sublist\">";
			if (($alpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
			else print $letter;
			print "</a> | \n";
		}
		if ($letter === "@") $pass = TRUE;
	}
	if ($pass == TRUE) {
		if (isset($alpha) && $alpha == "@") print "<a href=\"findfamily.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
		else print "<a href=\"findfamily.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\">".PrintReady($pgv_lang["NN"])."</a>";
		print " | \n";
	$pass = FALSE;
	}
	if ($show_all=="yes") print "<a href=\"findfamily.php?show_all=yes&amp;surname_sublist=$surname_sublist\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
	else print "<a href=\"findfamily.php?show_all=yes&amp;surname_sublist=$surname_sublist\">".$pgv_lang["all"]."</a>\n";
	if (isset($startalpha)) $alpha = $startalpha;
}
print_help_link("alpha_help", "qm");

print "<br /><br /><table class=\"list_table, $TEXT_DIRECTION\"><tr>";
if (($surname_sublist=="yes")&&($show_all=="yes")) {
	get_fam_list();
	if (!isset($alpha)) $alpha="";
	$surnames = array();
	$fam_hide=0;
	foreach($famlist as $gid=>$fam) {
		if (displayDetailsById($gid, "FAM")||showLivingNameById($gid, "FAM")) {
			$names = preg_split("/\+/", $fam["name"]);
			$foundnames = array();
			for($i=0; $i<count($names); $i++) {
				$name = trim($names[$i]);
				$sname = extract_surname($name);
				if (isset($foundnames[$sname])) {
					$surnames[$sname]["match"]--;
				}
				else $foundnames[$sname]=1;
			}
		}
		else $fam_hide++;
	}
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" colspan=\"$col\">";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["large"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["families"]."\" alt=\"".$pgv_lang["families"]."\" />&nbsp;&nbsp;";
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";
	foreach($surnames as $surname=>$namecount) {
		if (stristr($namecount["name"], "@")) $namelist = check_NN($namecount["name"]);
		else $namelist = $namecount["name"];
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"findfamily.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".$namelist . "&rlm; - [".($namecount["match"])."]&rlm;";
		}
		else {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"findfamily.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".$namelist . "&lrm; - [".($namecount["match"])."]&lrm;";
		}

		print "</a></div>\n";
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	if ($count>1 || $fam_hide>0) {
		print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
		if ($count>1) print $pgv_lang["total_fams"]." ".count($famlist)."&nbsp;";
		if ($count>1 && $fam_hide>0) print "--&nbsp;";
		if ($fam_hide>0) print $pgv_lang["hidden"]." ".$fam_hide;
		if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
		print "</td>\n";
	}
}
else if (($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	$tfamlist = get_alpha_fams($alpha);
	$surnames = array();
	$fam_hide=0;
	foreach($tfamlist as $gid=>$fam) {
		if ((displayDetailsByID($gid, "FAM"))||(showLivingNameById($gid, "FAM"))) {
			$names = preg_split("/\+/", $fam["name"]);
			$i=0;
			foreach($names as $indexval => $name) {
				extract_surname(trim($name));
				$i++;
			}
		}
		else $fam_hide++;
	}
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$count_indi = 0;
	$count_fam = 0;
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" colspan=\"$col\">";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["families"]."\" alt=\"".$pgv_lang["families"]."\" />&nbsp;&nbsp;";
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";
	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) {
 			print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"findfamily.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".$namecount["name"]."&rlm;&nbsp;-&nbsp;[".($namecount["fam"])."]&rlm;";
		}
		else {
 			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"findfamily.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".$namecount["name"]."&lrm;&nbsp;-&nbsp;[".($namecount["fam"])."]&lrm;";
		}
		print "</a>&nbsp;</div>\n";
		$count_indi += $namecount["match"];
		$count_fam += $namecount["fam"];
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	if ($count>1 || $fam_hide>0) {
		print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
	    if (oneRTLText($alpha) || ($alpha == "@" && begRTLText($pgv_lang["NN"])))
		     print $pgv_lang["total_indis"]." &lrm;(".strtolower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&lrm;&nbsp;&rlm;&nbsp;".($count_indi)."&rlm;&nbsp;<br />";
		else print $pgv_lang["total_indis"]." &rlm;(".strtolower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&rlm;&nbsp;&lrm;".($count_indi)."&lrm;&nbsp;<br />";
		print $pgv_lang["total_fams"]." ".$count_fam."&nbsp;<br />";
		print $pgv_lang["surnames"]." ".$count."&nbsp;</td>\n";
	}
}
else {
	//-- if the surname is set then only get the names in that surname list

	if ((!empty($surname))&&($surname_sublist=="yes") || ($surname_sublist=="no")&&(!empty($alpha))) {
		$newfamlist = array();
	$tfamlist = get_alpha_fams($alpha);
		if ($surname!="@N.N") {
			$match=0;
		foreach($tfamlist as $gid=>$fam) {
				$names = preg_split("/\+/", $fam["name"]);
				unset($surnames);
				$sname = array();
				$i=0;
				foreach($names as $indexval => $name) {
					$sname[$i] = extract_surname(trim($name));
					$i++;
				}
				if (!empty($sname[0]) || !empty($sname[1])) {
					if (stristr(strtoupper(substr($names[0], 0, strpos($names[0],","))),$sname[0])){
						if (isset($names[0])) $newname = $names[0]." + ";
						if (isset($names[1])) $newname .= $names[1];
					}
					else {
						if (isset($names[1])) $newname = $names[1]." + ";
						if (isset($names[0])) $newname .= $names[0];
					}
					if (get_first_letter(trim($names[0]))==get_first_letter(trim($names[1]))) $match += 2;
					else $match ++;
					$fam["name"] = $newname;
					$newfamlist[$gid] = $fam;
				}
		}
		$tfamlist = $newfamlist;
	}
	}
	//-- simplify processing for ALL famlist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		print "<td class=\"list_value_wrap\"><ul>\n";
		$tfamlist = get_fam_list();
		uasort($tfamlist, "itemsort");
		$count = count($tfamlist);
		$i=0;
		foreach($tfamlist as $gid => $fam) {
			$fam["name"] = check_NN($fam["name"]);
			print_list_family($gid, array($fam["name"], $fam["file"]));
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
	}
		print "</ul></td>\n";
		if ($count>1) {
			print "</tr><tr><td colspan=\"2\" align=\"center\">";
			print $pgv_lang["total_fams"]." ".$count."</td>\n";
			}
		}
		else {
		print "<td class=\"list_value_wrap\"><ul>\n";
		uasort($tfamlist, "itemsort");
		$count = count($tfamlist);
		$i=0;
		foreach($tfamlist as $gid => $fam) {
			$fam["name"] = check_NN($fam["name"]);
			print_list_family($gid, array($fam["name"], $fam["file"]), true);
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		}
		print "</ul></td>\n";
		if ($count>1) {
			print "</tr><tr><td colspan=\"2\" align=\"center\">";
 	    	if (oneRTLText($alpha) || ($alpha == "@" && begRTLText($pgv_lang["NN"]))) print $pgv_lang["total_indis"]." &lrm;(".strtolower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&lrm;&nbsp;&rlm;".$match."&rlm;&nbsp;<br />";
			else print $pgv_lang["total_indis"]." &rlm;(".strtolower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&rlm;&nbsp;&lrm;".$match."&lrm;&nbsp;<br />";
			$count -= $fam_hide;
			print $pgv_lang["total_fams"]." ".$count;
			if ($fam_private>0) print "&nbsp;(".$pgv_lang["private"]." ".$fam_private.")";
			if ($fam_hide>0) print "&nbsp;--&nbsp;";
			if ($fam_hide>0) print $pgv_lang["hidden"]." ".$fam_hide;
			print "</td>\n";
		}
	}
}
print "</tr></table>";

print_help_link("name_list_help", "qm");
if ($show_all=="yes" && $alpha != "@"){
	if ($surname_sublist=="yes") print "<br /><a href=\"findfamily.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
 	else print "<br /><a href=\"findfamily.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if (empty($alpha)) {
	if ($surname_sublist=="yes") print "<br /><a href=\"findfamily.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<br /><a href=\"findfamily.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a><br />\n";
}
else if ($alpha != "@" && is_array(isset($surname))) {
	print "<br /><a href=\"findfamily.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if ($alpha != "@") {
	if ($surname_sublist=="yes") print "<br /><a href=\"findfamily.php?alpha=$alpha&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<br /><a href=\"findfamily.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
if ($alpha != "@") print_help_link("skip_sublist_help", "qm");
print "<br /><br />\n";
print "</div>\n";
print_footer();


?>