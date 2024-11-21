<?php
/*=================================================
	Project: phpGedView
	File: uploadgedcom.php
	Author: John Finlay
	Comments:
		Allow admin users to upload a new gedcom using a
		web interface.

	phpGedView: Genealogy Viewer
	Copyright (C) 2002 to 2003  John Finlay and Others

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

===================================================*/
# $Id: uploadgedcom.php,v 1.1 2005/10/07 18:12:20 skenow Exp $

require "config.php";

if (!userGedcomAdmin(getUserName())) {
	header("Location: login.php?url=uploadgedcom.php");
	exit;
}

if (empty($action)) $action="upload_form";

//-- gedcom was uploaded
if ($action=="upload") {
	if (filesize($_FILES['gedcomfile']['tmp_name'])!= 0) {
		//-- check if the file was uploaded before
		if (!isset($GEDCOMS[basename($_FILES['gedcomfile']['name'])])) {
			if (move_uploaded_file($_FILES['gedcomfile']['tmp_name'], $INDEX_DIRECTORY.$_FILES['gedcomfile']['name'])) {
				//print "pass";
				AddToLog("Gedcom ".$INDEX_DIRECTORY.$_FILES['gedcomfile']['name']." uploaded by >".getUserName()."<");
				$ged = array();
				$ged["gedcom"] = $_FILES['gedcomfile']['name'];
				$ged["config"] = "config_gedcom.php";
				$ged["privacy"] = "privacy.php";
				$ged["title"] = str_replace("#GEDCOMFILE#", basename($_FILES['gedcomfile']['name']), $pgv_lang["new_gedcom_title"]);
				$ged["path"] = $INDEX_DIRECTORY.basename($_FILES['gedcomfile']['name']);
				$GEDCOMS[basename($_FILES['gedcomfile']['name'])] = $ged;
				store_gedcoms();
				header("Location: editconfig_gedcom.php?ged=".basename($_FILES['gedcomfile']['name']));
				exit;
			}
			else {
		 		$upload_errors = array(print_text("file_success",0,1), print_text("file_too_big",0,1), print_text("file_too_big",0,1),print_text("file_partial",0,1), print_text("file_missing",0,1));
				$error = print_text("upload_error",0,1)."<br />".$upload_errors[$_FILES['gedcomfile']['error']];
				$action = "upload_form";
			}
		}
		else {
			//-- move the file to a temporary backup location and display a form to ask the user if 
			//-- if they are want to replace the file or delete the old one or cancel
			if (@move_uploaded_file($_FILES['gedcomfile']['tmp_name'], $INDEX_DIRECTORY.$_FILES['gedcomfile']['name'].".bak")) {
				AddToLog("Gedcom ".$INDEX_DIRECTORY.$_FILES['gedcomfile']['name'].".bak uploaded by >".getUserName()."<");
				$action = "verify_upload";
			}
			else {
		 		$upload_errors = array(print_text("file_success",0,1), print_text("file_too_big",0,1), print_text("file_too_big",0,1),print_text("file_partial",0,1), print_text("file_missing",0,1));
				$error = print_text("upload_error",0,1)."<br />".$upload_errors[$_FILES['gedcomfile']['error']];
				$action = "upload_form";
			}
		}
	}
	else {
		$error = print_text("upload_error",0,1)."<br />".print_text("file_not_exists",0,1);
		$action = "upload_form";
	}
}
//-- gedcom was added
else if ($action=="add") {
	if (strtolower(substr(trim($GEDCOMPATH), -4)) != ".ged") $GEDCOMPATH .= ".ged";
	if ((strstr($GEDCOMPATH, "://"))||(file_exists($GEDCOMPATH))) {
		//-- only get the filename from the path
		$slpos = strrpos($GEDCOMPATH, "/");
		if (!$slpos) $slpos = strrpos($GEDCOMPATH,"\\");
		if ($slpos) $FILE = substr($GEDCOMPATH, $slpos+1);
		else $FILE=$GEDCOMPATH;
		AddToLog("Gedcom ".$FILE." added by >".getUserName()."<");
		// save existing values for existing gedcom file
		if (!isset($GEDCOMS[$FILE])) {
			$ged = array();
			$ged["gedcom"] = $FILE;
			$ged["config"] = "config_gedcom.php";
			$ged["privacy"] = "privacy.php";
			if (empty($gedcom_title)) $ged["title"] = str_replace("#GEDCOMFILE#", $FILE, print_text("new_gedcom_title",0,1));
			else $ged["title"] = $gedcom_title;
			$ged["path"] = $GEDCOMPATH;
			$GEDCOMS[$FILE] = $ged;
			store_gedcoms();
		}
		header("Location: editconfig_gedcom.php?ged=".$FILE);
		exit;
	}
	else {
		$action = "add_form";
	}
}
else if ($action=="update_gedcom") {
	if (file_exists($bakfile)) unlink($bakfile);
	rename($bakfile.".bak", $bakfile);
	header("Location: validategedcom.php?ged=".basename($bakfile));
	exit;
}
else if ($action=="cancel_upload") {
	unlink($bakfile.".bak");
	$action = "upload_form";
}

print_header(print_text("upload_gedcom",0,1));
if ($action=="upload_form") {
	print "<font class=\"subheaders\">";
	print_text("step1");
	print " ";
	print_text("upload_gedcom");
	print "</font><br /><br />\n";
	if (!empty($error)) {
		print "<font class=\"error\">".$error."</font><br />\n";
		print_text("common_upload_errors");
		print "<br />\n";
	}
	print_text("upload_help");
	print $INDEX_DIRECTORY;
?>
<br />
<form enctype="multipart/form-data" method="post" action="uploadgedcom.php">
<input type="hidden" name="action" value="upload" />
<?php print_text("gedcom_file");?> <input name="gedcomfile" type="file" size="60" />
<?php
if (!$filesize = ini_get('upload_max_filesize')) {
   $filesize = "2M";
}
print " ( ";
print_text("max_upload_size");
print " $filesize )";
?>
<br />
<input type="submit" value="<?php print_text("upload_gedcom");?>" />
</form>
<br /><br /><br />
<?php
}
else if ($action=="add_form") {
	require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
	if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
	print "<font class=\"subheaders\">";
	print_text("step1");
	print " ";
	print_text("add_gedcom");
	print "</font><br /><br />\n";
	if (!empty($error)) print "<font class=\"error\">".$error."</font><br /><br />\n";
?>
<script language="JavaScript" type="text/javascript">
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
	function getHelp(which) {
		if ((helpWin)&&(!helpWin.closed)) helpWin.location='editconfig_help.php?help='+which;
	}
	function closeHelp() {
		if (helpWin) helpWin.close();
	}
</script>
<form method="post" name="configform" action="uploadgedcom.php">
<?php print_text("review_readme");
$i = 0;
?>
<input type="hidden" name="action" value="add" />
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print_text("gedcom_path")?> <a href="#" onclick="return helpPopup('gedcom_path_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="GEDCOMPATH" value="" size="60" dir ="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('gedcom_path_help');" />
		<?php
			if (!empty($GEDCOMPATH)) {
				if (strtolower(substr(trim($GEDCOMPATH), -4)) != ".ged") $GEDCOMPATH .= ".ged";
				if ((!strstr($GEDCOMPATH, "://"))&&(!file_exists($GEDCOMPATH))) {
					$gedcomsplit = preg_split("/\//", $GEDCOMPATH);
					foreach ($gedcomsplit as $gedcomname){
						if (stristr($gedcomname, "ged")){
							print "<br /><font class=\"error\">".str_replace("#GEDCOM#", $gedcomname, print_text("error_header",0,1))."</font>\n";
						}
					}
				}
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print_text("gedcom_title")?> <a href="#" onclick="return helpPopup('gedcom_title_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="gedcom_title" value="" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('gedcom_title_help');" /></td>
	</tr>
</table>
<input type="submit" value="<?php print_text("add");?>" tabindex="<?php $i++; print $i?>" onclick="closeHelp();" />
</form>
<?php
}
else if ($action=="verify_upload") {
	?>
	<form method="post" name="verifyform" action="uploadgedcom.php">
		<input type="hidden" name="action" value="update_gedcom" />
		<input type="hidden" name="bakfile" value="<?php print $INDEX_DIRECTORY.$_FILES['gedcomfile']['name']; ?>" />
		<span class="error"><?php print_text("dataset_exists"); ?></span><br /><br /><?php
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$_FILES['gedcomfile']['name']) {
				print_text("changes_present");
				print "<br /><br />";
				break;
			}
		}
		print_text("verify_upload_instructions"); ?><br /><br />
		<input type="submit" value="<?php print_text("continue"); ?>" /> 
		<input type="button" value="<?php print_text("cancel_upload"); ?>" onclick="document.verifyform.action.value='cancel_upload'; document.verifyform.submit(); " />
	</form>
	<?php
}
print_footer();
?>