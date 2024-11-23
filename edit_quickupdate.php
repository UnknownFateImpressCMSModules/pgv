<?php
/**
 * PopUp Window to provide users with a simple quick update form.
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
 * @subpackage Edit
 * @version $Id: edit_quickupdate.php,v 1.4 2005/08/17 10:42:52 canajun2eh Exp $
 */

require("config.php");
require("includes/functions_edit.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?type=simple&url=edit_interface.php");
	exit;
}

print_simple_header($pgv_lang["quick_update_title"]);

//-- only allow logged in users to access this page
$uname = getUserName();
if ((!$ALLOW_EDIT_GEDCOM)||(!$USE_QUICK_UPDATE)||(empty($uname))) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

$user = getUser($uname);

if (empty($pid)) {
	if (!empty($user["gedcomid"][$GEDCOM])) $pid = $user["gedcomid"][$GEDCOM];
	else $pid = "";
}
$pid = clean_input($pid);

//-- only allow editors or users who are editing their own individual or their immediate relatives
$pass = false;
if (!empty($user["gedcomid"][$GEDCOM])) {
	if ($pid==$user["gedcomid"][$GEDCOM]) $pass=true;
	else {
		$famids = array_merge(find_sfamily_ids($user["gedcomid"][$GEDCOM]), find_family_ids($user["gedcomid"][$GEDCOM]));
		foreach($famids as $indexval => $famid) {
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_gedcom_record($famid);
			else $famrec = find_record_in_file($famid);
			if (preg_match("/1 HUSB @$pid@/", $famrec)>0) $pass=true;
			if (preg_match("/1 WIFE @$pid@/", $famrec)>0) $pass=true;
			if (preg_match("/1 CHIL @$pid@/", $famrec)>0) $pass=true;
		}
	}
}
if (empty($pid)) $pass=false;
if ((!userCanEdit($uname))&&(!$pass)) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

//-- find the latest gedrec for the individual
if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
else $gedrec = find_record_in_file($pid);

//-- only allow edit of individual records
$disp = true;
$ct = preg_match("/0 @$pid@ (.*)/", $gedrec, $match);
if ($ct>0) {
	$type = trim($match[1]);
	if ($type=="INDI") {
		$disp = displayDetailsById($pid);
	}
	else {
		print $pgv_lang["access_denied"];
		print_simple_footer();
		exit;
	}
}

if ((!$disp)||(!$ALLOW_EDIT_GEDCOM)) {

	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userCanEdit(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
	}
	print_simple_footer();
	exit;
}

//-- put the updates into the gedcom record
if ($action=="update") {
	print "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
	print "<b>".PrintReady(get_person_name($pid))."</b><br /><br />";
	
	AddToLog("Quick update attempted for $pid by >".getUserName()."<");

	$updated = false;
	$error = "";
	//-- check for name update
	if (!empty($GIVN) || !empty($SURN)) {
		if (preg_match("/1 NAME.+\n/", $gedrec)>0) {
			if (!empty($GIVN)) {
				$gedrec = preg_replace("/1 NAME.+\//", "1 NAME $GIVN /", $gedrec);
				$gedrec = preg_replace("/2 GIVN.+\n/", "2 GIVN $GIVN\r\n", $gedrec);
			}
			if (!empty($SURN)) {
				$gedrec = preg_replace("/1 NAME(.+)\/.*\/\n/", "1 NAME$1/$SURN/\r\n", $gedrec);
				$gedrec = preg_replace("/2 SURN.+\n/", "2 SURN $SURN\r\n", $gedrec);
			}
		}
		else $gedrec .= "\r\n1 NAME $GIVN /$SURN/\r\n2 GIVN $GIVN\r\n2 SURN $SURN";
		$updated = true;
	}

	//-- check for fact update
	if (!empty($fact)) {
		$factrec = "1 $fact\r\n";
		if (!empty($DATE)) $factrec .= "2 DATE $DATE\r\n";
		if (!empty($PLAC)) $factrec .= "2 PLAC $PLAC\r\n";
		if (!empty($RESN)) $factrec .= "2 RESN $RESN\r\n";
		$pos1 = strpos($gedrec, "1 $fact");
		$noupdfact = false;
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
			if ($pos2===false) $pos2 = strlen($gedrec)-1;
			$oldfac = substr($gedrec, $pos1, $pos2-$pos1);
			$noupdfact = FactEditRestricted($pid, $oldfac);
			if ($noupdfact) {
				print "<br />".$pgv_lang["update_fact_restricted"]." ".$factarray[$fact]."<br /><br />";
			}
			else {
				$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
			}
		}
		else $gedrec .= "\r\n".$factrec;
		if ($noupdfact == false) $updated = true;
	}

	//-- check for photo update
	if (!empty($_FILES["FILE"]['tmp_name'])) {
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		if (!move_uploaded_file($_FILES['FILE']['tmp_name'], $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']))) {
			$error .= "<br />".$pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['FILE']['error']];
		}
		else {
			$filename = $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']);
			$thumbnail = $MEDIA_DIRECTORY."thumbs/".basename($_FILES['FILE']['name']);
			generate_thumbnail($filename, $thumbnail);

			$factrec = "1 OBJE\r\n";
			$factrec .= "2 FILE ".$filename."\r\n";
			if (!empty($TITL)) $factrec .= "2 TITL $TITL\r\n";

			if (empty($replace)) $gedrec .= "\r\n".$factrec;
			else {
				$fact = "OBJE";
				$pos1 = strpos($gedrec, "1 $fact");
				if ($pos1!==false) {
					$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
					if ($pos2===false) $pos2 = strlen($gedrec)-1;
					$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
				}
				else $gedrec .= "\r\n".$factrec;
			}
			$updated = true;
		}
	}

	//--address phone email
	if (!empty($ADDR)) {
		$factrec = "1 ADDR ";
		$lines = preg_split("/\r*\n/", $ADDR);
		$factrec .= $lines[0]."\r\n";
		for($i=1; $i<count($lines); $i++) $factrec .= "2 CONT ".$lines[$i]."\r\n";
		$pos1 = strpos($gedrec, "1 ADDR");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
			if ($pos2===false) $pos2 = strlen($gedrec)-1;
			$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		}
		else $gedrec .= "\r\n".$factrec;
		$updated = true;
	}
	if (!empty($PHON)) {
		$factrec = "1 PHON $PHON\r\n";
		$pos1 = strpos($gedrec, "1 PHON");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
			if ($pos2===false) $pos2 = strlen($gedrec)-1;
			$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		}
		else $gedrec .= "\r\n".$factrec;
		$updated = true;
	}
	if (!empty($EMAIL)) {
		$factrec = "1 EMAIL $EMAIL\r\n";
		$pos1 = strpos($gedrec, "1 EMAIL");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
			if ($pos2===false) $pos2 = strlen($gedrec)-1;
			$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		}
		else $gedrec .= "\r\n".$factrec;
		$updated = true;
	}
	//--add new spouse name, birth, marriage
	if (!empty($SGIVN) || !empty($SSURN)) {
		//-- first add the new spouse
		$spouserec = "0 @REF@ INDI\r\n";
		$spouserec .= "1 NAME $SGIVN /$SSURN/\r\n";
		$spouserec .= "2 GIVN $SGIVN\r\n";
		$spouserec .= "2 SURN $SSURN\r\n";
		if (!empty($SSEX)) $spouserec .= "1 SEX $SSEX\r\n";
		if (!empty($BDATE)||!empty($BPLAC)) {
			$spouserec .= "1 BIRT\r\n";
			if (!empty($BDATE)) $spouserec .= "2 DATE $BDATE\r\n";
			if (!empty($BPLAC)) $spouserec .= "2 PLAC $BPLAC\r\n";
			if (!empty($BRESN)) $spouserec .= "2 RESN $BRESN\r\n";
		}
		$xref = append_gedrec($spouserec);

		//-- next add the new family record
		$famrec = "0 @REF@ FAM\r\n";
		if ($SSEX=="M") $famrec .= "1 HUSB @$xref@\r\n1 WIFE @$pid@\r\n";
		else $famrec .= "1 HUSB @$pid@\r\n1 WIFE @$xref@\r\n";
		$newfamid = append_gedrec($famrec);

		//-- add the new family id to the new spouse record
		$spouserec = find_record_in_file($xref);
		$spouserec .= "\r\n1 FAMS @$newfamid@\r\n";
		replace_gedrec($xref, $spouserec);
		
		//-- last add the new family id to the persons record
		$gedrec .= "\r\n1 FAMS @$newfamid@\r\n";
		$updated = true;
	}
	if (!empty($MDATE)||!empty($MPLAC)) {
		if (empty($newfamid)) {
			$famids = find_sfamily_ids($pid);
			if (count($famids)==0) {
				$famrec = "0 @REF@ FAM\r\n";
				if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\r\n";
				else $famrec .= "1 WIFE @$pid@";
				$newfamid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMS @$newfamid@";
				$updated = true;
			}
			else {
				$newfamid = $famids[count($famids)-1];
			}
		}
		$famrec = find_record_in_file($newfamid);
		$factrec = "1 MARR\r\n";
		if (!empty($MDATE)) $factrec .= "2 DATE $MDATE\r\n";
		if (!empty($MPLAC)) $factrec .= "2 PLAC $MPLAC\r\n";
		if (!empty($MRESN)) $factrec .= "2 RESN $MRESN\r\n";
		$pos1 = strpos($famrec, "1 MARR");
		$noupdfact = false;
		if ($pos1!==false) {
			$pos2 = strpos($famrec, "\n1 ", $pos1+1);
			if ($pos2===false) $pos2 = strlen($famrec)-1;
			$oldfac = substr($famrec, $pos1, $pos2);
			$noupdfact = FactEditRestricted($pid, $oldfac);
			if ($noupdfact) {
				print "<br />".$pgv_lang["update_fact_restricted"]." ".$factarray["MARR"]."<br /><br />";
			}
			else {
				$famrec = substr($famrec, 0, $pos1) . "\r\n".$factrec . substr($famrec, $pos2);
			}
		}
		else $famrec .= "\r\n".$factrec;
		if ($noupdfact == false){
			replace_gedrec($newfamid, $famrec);
			$updated = true;
		}

	}

	//--add new child, name, birth
	if (!empty($CGIVN) || !empty($CSURN)) {
		//-- first add the new child
		$childrec = "0 @REF@ INDI\r\n";
		$childrec .= "1 NAME $CGIVN /$CSURN/\r\n";
		if (!empty($CSEX)) $childrec .= "1 SEX $CSEX\r\n";
		if (!empty($CDATE)||!empty($CPLAC)) {
			$childrec .= "1 BIRT\r\n";
			if (!empty($CDATE)) $childrec .= "2 DATE $CDATE\r\n";
			if (!empty($CPLAC)) $childrec .= "2 PLAC $CPLAC\r\n";
			if (!empty($CRESN)) $childrec .= "2 RESN $CRESN\r\n";
		}
		$cxref = append_gedrec($childrec);

		//-- next find the family record
		$famids = find_sfamily_ids($pid);
		//-- if a new family was already made by adding a spouse or a marriage
		//-- then use that id, otherwise look for current families
		if (empty($newfamid) && (count($famids)==0)) {
			$famrec = "0 @REF@ FAM\r\n";
			if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\r\n";
			else $famrec .= "1 WIFE @$pid@\r\n";
			$famrec .= "1 CHIL @$cxref@\r\n";
			$newfamid = append_gedrec($famrec);
			
			//-- add the new family to the new child
			$childrec = find_record_in_file($cxref);
			$childrec .= "\r\n1 FAMC @$newfamid@\r\n";
			replace_gedrec($cxref, $childrec);
			
			//-- add the new family to the original person
			$gedrec .= "\r\n1 FAMS @$newfamid@";
			$updated = true;
		}
		else {
			if (empty($newfamid)) $newfamid = $famids[count($famids)-1];
			if (!isset($pgv_changes[$newfamid."_".$GEDCOM])) $famrec = trim(find_gedcom_record($newfamid));
			else $famrec = trim(find_record_in_file($newfamid));
			$famrec .= "\r\n1 CHIL @$cxref@\r\n";
			replace_gedrec($newfamid, $famrec);
			
			//-- add the family to the new child
			$childrec = find_record_in_file($cxref);
			$childrec .= "\r\n1 FAMC @$newfamid@\r\n";
			replace_gedrec($cxref, $childrec);
		}
		print $pgv_lang["update_successful"]."<br />\n";;
	}

	if ($updated && empty($error)) {
		print $pgv_lang["update_successful"];
		AddToLog("Quick update for $pid by >".getUserName()."<");
		//print "<pre>$gedrec</pre>";
		replace_gedrec($pid, $gedrec);
	}
	if (!empty($error)) {
		print "<span class=\"error\">".$error."</span>";
	}

	print "<center><br /><br /><br />";
	print "<a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();
	exit;
}

print "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
print "<b>".PrintReady(get_person_name($pid))."</b><br />";
print $pgv_lang["quick_update_instructions"];

init_calendar_popup();
?>
<script language="JavaScript">
<!--
var pastefield;
function findPlace(field) {
	pastefield = field;
	window.open('findplace.php?place='+field.value, '', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function paste_id(value) {
	pastefield.value = value;
}

var helpWin;
function helpPopup(which) {
	if ((!helpWin)||(helpWin.closed)) helpWin = window.open('help_text.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
	else helpWin.location = 'help_text.php?help='+which;
	return false;
}
//-->
</script>
<form method="post" action="edit_quickupdate.php?pid=<?php print $pid;?>" name="quickupdate" enctype="multipart/form-data">
<input type="hidden" name="action" value="update" />
<table>
<tr><td colspan="4"><b><?php print $pgv_lang["update_name"]; ?></b><?php print_help_link("quick_update_name_help", "qm"); ?></td></tr>
<tr>
	<td align="right"><?php print $factarray["GIVN"]; print_help_link("edit_given_name_help", "qm");?></td><td><input type="text" name="GIVN" /></td>
	<td align="right"><?php print $factarray["SURN"]; print_help_link("edit_surname_help", "qm");?></td><td><input type="text" name="SURN" /></td>
</tr>
<tr><td colspan="4"><br /></td></tr>

<tr><td colspan="4"><b><?php print $pgv_lang["update_fact"]; ?></b><?php print_help_link("quick_update_fact_help", "qm"); ?></td></tr>
<tr>
	<td></td>
	<td colspan="3"><select name="fact">
		<option value=""><?php print $pgv_lang["select_fact"]; ?></option>
	<?php
	$addfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","GRAD",
	"BAPL","CONL","ENDL","SLGC","_MILI");
	usort($addfacts, "factsort");
	foreach($addfacts as $indexval => $fact) {
		print "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
	}
	?>
		</select>
	</td>
</tr>
<tr><td align="right">
		<?php print $factarray["DATE"]; print_help_link("def_gedcom_date_help", "qm");?>
	</td>
	<td><input type="text" size="15" name="DATE" id="DATE" onBlur="valid_date(this);" /><?php print_calendar_popup("DATE");?></td>
	<td align="right"><?php print $factarray["PLAC"]; print_help_link("edit_PLAC_help", "qm");?></td>
	<td><input type="text" name="PLAC" id="place" />
		<a href="#" onclick="return findPlace(document.getElementById('place'));"><?php print $pgv_lang["find_place"]; ?></a>
	</td>
</tr>
<?php if ($SHOW_QUICK_RESN) { ?>
<tr><td align="right"><?php print $factarray["RESN"]; print_help_link("RESN_help", "qm");?></td>
	<td colspan="3">
	<?php
	print "<select name=\"RESN\"><option value=\"\"></option><option value=\"confidential\"";
	print ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
	print ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
	print ">".$pgv_lang["privacy"]."</option>";
	print "</select>\n";
	?>
	</td>
</tr>
<?php } ?>
<tr><td colspan="4"><br /></td></tr>

<?php if ($MULTI_MEDIA && (is_writable($MEDIA_DIRECTORY))) { ?>
<tr><td colspan="4"><b><?php print $pgv_lang["update_photo"]; ?></b><?php print_help_link("quick_update_photo_help", "qm"); ?></td></tr>
<tr>
	<td align="right">
		<?php print $factarray["TITL"]; ?>
	</td>
	<td colspan="3">
		<input type="text" name="TITL" size="40" />
	</td>
</tr>
<tr>
	<td align="right">
		<?php print $factarray["FILE"]; ?>
	</td>
	<td colspan="3">
		<input type="file" name="FILE" size="40" />
	</td>
</tr>
<?php if (preg_match("/1 OBJE/", $gedrec)>0) { ?>
<tr>
	<td></td>
	<td colspan="3">
		<input type="checkbox" name="replace" value="yes" /> <?php print $pgv_lang["photo_replace"]; ?>
	</td>
</tr>
<?php } ?>
<tr><td colspan="4"><br /></td></tr>
<?php } ?>

<?php if (!is_dead_id($pid)) { /*-- don't show address for dead people */ ?>
<tr><td colspan="4"><b><?php print $pgv_lang["update_address"]; ?></b><?php print_help_link("quick_update_address_help", "qm"); ?></td></tr>
<tr>
	<td align="right">
		<?php print $factarray["ADDR"]; ?>
	</td>
	<td colspan="3">
		<textarea name="ADDR" cols="35" rows="4"></textarea>
	</td>
</tr>
<tr>
	<td align="right">
		<?php print $factarray["PHON"]; ?>
	</td>
	<td colspan="3">
		<input type="text" name="PHON" size="20" />
	</td>
</tr>
<tr>
	<td align="right">
		<?php print $factarray["EMAIL"]; ?>
	</td>
	<td colspan="3">
		<input type="text" name="EMAIL" size="40" />
	</td>
</tr>
<tr><td colspan="4"><br /></td></tr>
<?php } ?>

<tr><td colspan="4"><b><?php if (preg_match("/1 SEX M/", $gedrec)>0) print $pgv_lang["add_new_wife"]; else print $pgv_lang["add_new_husb"]; ?></b><?php print_help_link("quick_update_spouse_help", "qm"); ?></td></tr>
<tr>
	<td align="right"><?php print $factarray["GIVN"]; print_help_link("edit_given_name_help", "qm");?></td><td><input type="text" name="SGIVN" /></td>
	<td align="right"><?php print $factarray["SURN"]; print_help_link("edit_surname_help", "qm");?></td><td><input type="text" name="SSURN" /></td>
</tr>
<tr>
	<td align="right"><?php print $pgv_lang["sex"]; print_help_link("edit_sex_help", "qm");?></td><td colspan="3">
		<select name="SSEX">
			<option value="M"<?php if (preg_match("/1 SEX F/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["male"]; ?></option>
			<option value="F"<?php if (preg_match("/1 SEX M/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["female"]; ?></option>
			<option value="U"<?php if (preg_match("/1 SEX U/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	</td>
</tr>
<tr>
	<td align="right"><?php print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"]; print_help_link("def_gedcom_date_help", "qm");?>
	</td>
	<td><input type="text" size="15" name="BDATE" id="BDATE" onBlur="valid_date(this);" /><?php print_calendar_popup("BDATE");?></td>
	<td align="right"><?php print $factarray["PLAC"]; print_help_link("edit_PLAC_help", "qm");?></td>
	<td><input type="text" name="BPLAC" id="bplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="banchor1x" id="banchor1x" />
		<a href="#" onclick="return findPlace(document.getElementById('bplace'));"><?php print $pgv_lang["find_place"]; ?></a>
	</td>
</tr>
<?php if ($SHOW_QUICK_RESN) { ?>
<tr><td align="right"><?php print $factarray["RESN"]; print_help_link("RESN_help", "qm");?></td><td colspan="3">
<?php
	print "<select name=\"BRESN\"><option value=\"\"></option><option value=\"confidential\"";
	print ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
	print ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
	print ">".$pgv_lang["privacy"]."</option>";
	print "</select>\n";
	?>
</td>
</tr>
<?php } ?>
<tr><td colspan="4"><br /></td></tr>

<tr>
	<td colspan="4"><b><?php print $factarray["MARR"]; ?></b><?php print_help_link("quick_update_marriage_help", "qm"); ?>
	</td>
</tr>
<tr><td align="right">
		<?php print $factarray["DATE"]; print_help_link("def_gedcom_date_help", "qm");?>
	</td>
	<td><input type="text" size="15" name="MDATE" id="MDATE" onBlur="valid_date(this);" /><?php print_calendar_popup("MDATE");?></td>
	<td align="right"><?php print $factarray["PLAC"]; print_help_link("edit_PLAC_help", "qm");?></td>
	<td><input type="text" name="MPLAC" id="mplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="manchor1x" id="manchor1x" />
		<a href="#" onclick="return findPlace(document.getElementById('mplace'));"><?php print $pgv_lang["find_place"]; ?></a>
	</td>
</tr>
<?php if ($SHOW_QUICK_RESN) { ?>
<tr><td align="right"><?php print $factarray["RESN"]; print_help_link("RESN_help", "qm");?></td><td colspan="3">
<?php
	print "<select name=\"MRESN\"><option value=\"\"></option><option value=\"confidential\"";
	print ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
	print ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
	print ">".$pgv_lang["privacy"]."</option>";
	print "</select>\n";
	?>
</td>
</tr>
<?php } ?>
<tr><td colspan="4"><br /></td></tr>

<tr><td colspan="4"><b><?php print $pgv_lang["add_new_chil"]; ?></b><?php print_help_link("quick_update_child_help", "qm"); ?></td></tr>
<tr>
	<td align="right"><?php print $factarray["GIVN"]; print_help_link("edit_given_name_help", "qm");?></td><td><input type="text" name="CGIVN" /></td>
	<td align="right"><?php print $factarray["SURN"]; print_help_link("edit_surname_help", "qm");?></td><td><input type="text" name="CSURN" /></td>
</tr>
<tr>
	<td align="right"><?php print $pgv_lang["sex"]; print_help_link("edit_sex_help", "qm");?></td><td colspan="3">
		<select name="CSEX">
			<option value="M"><?php print $pgv_lang["male"]; ?></option>
			<option value="F"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
<tr>
	<td align="right"><?php print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"]; print_help_link("def_gedcom_date_help", "qm");?>
	</td>
	<td><input type="text" size="15" name="CDATE" id="CDATE" onBlur="valid_date(this);" /><?php print_calendar_popup("CDATE");?></td>
	<td align="right"><?php print $factarray["PLAC"]; print_help_link("edit_PLAC_help", "qm");?></td>
	<td><input type="text" name="CPLAC" id="cplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="canchor1x" id="canchor1x" />
		<a href="#" onclick="return findPlace(document.getElementById('cplace'));"><?php print $pgv_lang["find_place"]; ?></a>
	</td>
</tr>
<?php if ($SHOW_QUICK_RESN) { ?>
<tr><td align="right"><?php print $factarray["RESN"]; print_help_link("RESN_help", "qm");?></td><td colspan="3">
<?php
	print "<select name=\"CRESN\"><option value=\"\"></option><option value=\"confidential\"";
	print ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
	print ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
	print ">".$pgv_lang["privacy"]."</option>";
	print "</select>\n";
	?>
</td>
</tr>
<?php } ?>
<tr><td colspan="4"><br /></td></tr>
</table>
<input type="submit" value="<?php print $pgv_lang["save"]; ?>" />
</form>
<?php
print_simple_footer();
?>