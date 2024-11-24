<?php
/**
 * Allow an admin user to download the entire gedcom	file.
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
 * @subpackage Admin
 * @version $Id: downloadgedcom.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require "config.php";

if ((!userGedcomAdmin(getUserName()))||(empty($ged))) {
	header("Location: editgedcoms.php");
	exit;
}
if (!isset($action)) $action="";
if (!isset($remove)) $remove="no";
if (!isset($convert)) $convert="no";
if ($action=="download") {

	header("Content-Type: text/plain; charset=$CHARACTER_SET");
	header("Content-Disposition: attachment; filename=$ged; size=".filesize($GEDCOMS[$GEDCOM]["path"]));
	$GEDCOM = $ged;
	$indilist = get_indi_list();
	$famlist = get_fam_list();
	$sourcelist = get_source_list();
	$otherlist = get_other_list();

	if (isset($otherlist["HEAD"])) {
		$head = $otherlist["HEAD"]["gedcom"];
		$pos1 = strpos($head, "1 SOUR");
		if ($pos1!==false) {
			$pos2 = strpos($head, "\n1", $pos1+1);
			if ($pos2===false) $pos2 = strlen($head);
			$newhead = substr($head, 0, $pos1);
			$newhead .= substr($head, $pos2+1);
			$head = $newhead;
		}
		$head = preg_replace("/1 DATE.*/", "", $head);
		$head = trim($head);
		$head .= "\r\n1 SOUR PhpGedView\r\n2 NAME PhpGedView Online Genealogy\r\n2 VERS $VERSION $VERSION_RELEASE\r\n";
		$head .= "1 DATE ".date("j M Y")."\r\n";
		if (strstr($head, "1 PLAC")===false) {
			$head .= "1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
		}
	}
	else {
		$head = "0 HEAD\r\n1 SOUR PhpGedView\r\n2 NAME PhpGedView Online Genealogy\r\n2 VERS $VERSION $VERSION_RELEASE\r\n1 DEST DISKETTE\r\n1 DATE ".date("j M Y")."\r\n";
		$head .= "1 GEDC\r\n2 VERS 5.5\r\n2 FORM LINEAGE-LINKED\r\n1 CHAR $CHARACTER_SET\r\n1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
	}
	if ($convert=="yes") {
		$head = preg_replace("/UTF-8/", "ANSI", $head);
		$head = utf8_decode($head);
	}
	$head = remove_custom_tags($head, $remove);
	print $head;
	foreach($indilist as $indexval => $indi) {
		$rec = trim($indi["gedcom"])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($convert=="yes") $rec = utf8_decode($rec);
		print $rec;
	}
	foreach($famlist as $indexval => $fam) {
		$rec = trim($fam["gedcom"])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($convert=="yes") $rec = utf8_decode($rec);
		print $rec;
	}
	foreach($sourcelist as $indexval => $source) {
		$rec = trim($source["gedcom"])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($convert=="yes") $rec = utf8_decode($rec);
		print $rec;
	}
	foreach($otherlist as $key=>$other) {
		if (($key!="HEAD")&&($key!="TRLR")) {
			$rec = trim($other["gedcom"])."\r\n";
			$rec = remove_custom_tags($rec, $remove);
			if ($convert=="yes") $rec = utf8_decode($rec);
			print $rec;
		}
	}
	print "0 TRLR\r\n";
}
else {
	print_header($pgv_lang["download_gedcom"]);
	?>
	<div class="center">
	<h2><?php print $pgv_lang["download_gedcom"]; ?></h2>
	<br />
	<form name="convertform" method="post">
		<input type="hidden" name="action" value="download" />
		<table>
		<tr><td align="right"><input type="checkbox" name="convert" value="yes" /></td><td align="left"><?php print $pgv_lang["utf8_to_ansi"]; print_help_link("utf8_ansi_help", "qm"); ?></td></tr>
		<tr><td align="right"><input type="checkbox" name="remove" value="yes" checked="checked" /></td><td align="left"><?php print $pgv_lang["remove_custom_tags"]; print_help_link("remove_tags_help", "qm"); ?></td></tr>
		</table>
		<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
		<br /><br />
	</form>
	<?php
	print $pgv_lang["download_note"]."<br /><br /><br />\n";
	print "</div>";
	print_footer();
}
?>