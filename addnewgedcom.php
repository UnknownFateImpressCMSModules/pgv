<?php
/**
 * Create a new gedcom file
 *
 * Allow admin users to create a new gedcom with only one
 * person in it.
 *
 * $Id: addnewgedcom.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003	John Finlay and Others
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
 * @package PhpGedView
 * @subpackage Admin
 */
 
/**
 * load configuration and language files
 */
require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

if (!userIsAdmin(getUserName())) {
	header("Location: login.php?url=uploadgedcom.php");
	exit;
}

if ((isset($action)) && ($action=="add")) {
	$gedcomfile = basename($gedcomfile);
	$ct = preg_match("/\.ged$/", $gedcomfile);
	if ($ct==0) $gedcomfile.=".ged";
	if ((!file_exists($INDEX_DIRECTORY.$gedcomfile))&&(!isset($GEDCOMS[$gedcomfile]))) {
		$fp = fopen($INDEX_DIRECTORY.$gedcomfile, "wb");
		if ($fp) {
			$newgedcom = '0 HEAD
1 SOUR PhpGedView
2 VERS '.$VERSION.' '.$VERSION_RELEASE.'
1 DEST ANSTFILE
1 GEDC
2 VERS 5.5
2 FORM Lineage-Linked
1 CHAR UTF-8
0 @I1@ INDI
1 NAME Given Names /Surname/
1 SEX M
1 BIRT
2 DATE 01 JAN 1850
2 PLAC Click edit and change me
0 TRLR';
			fwrite($fp, $newgedcom);
			fclose($fp);
			$ged = array();
			$ged["gedcom"] = $gedcomfile;
			$ged["config"] = "config_gedcom.php";
			$ged["privacy"] = "privacy.php";
			$ged["title"] = str_replace("#GEDCOMFILE#", $gedcomfile, $pgv_lang["new_gedcom_title"]);
			$ged["path"] = $INDEX_DIRECTORY.$gedcomfile;
			$GEDCOMS[$gedcomfile] = $ged;
			store_gedcoms();
			header("Location: editconfig_gedcom.php?ged=".$gedcomfile);
			exit;
		}
		else {
			$error = $pgv_lang["error_title"];
		}
	}
	else {
		$error = $pgv_lang["file_exists"];
	}
}

print_header($pgv_lang["upload_gedcom"]);
?>
<div align="center">
<script language="JavaScript" type="text/javascript">
	function checkform(frm) {
		if (frm.gedcomfile.value=="") {
			alert('<?php print $pgv_lang["enter_filename"]; ?>');
			frm.gedcomfile.focus();
			return false;
		}
		return true;
	}
</script>
<?php
print "<span class=\"subheaders\">".$pgv_lang["step1"]." ".$pgv_lang["add_new_gedcom"]."</span><br /><br />\n";
if (!empty($error)) print "<span class=\"error\">".$error."</span><br /><br />\n";
print $pgv_lang["add_gedcom_instructions"]."<span dir=\"ltr\">" . $INDEX_DIRECTORY . "</span>";
?>
<br />
<form action="addnewgedcom.php" method="post" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="add" />
<?php print $pgv_lang["gedcom_file"];?> <input name="gedcomfile" type="text" /><br />
<input type="submit" value="<?php print $pgv_lang["add"];?>" />
</form>
<br /><br /><br />
</div>
<?php
print_footer();
?>