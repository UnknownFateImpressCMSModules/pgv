<?php
/**
 * Various functions used by the Edit interface
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
 * @subpackage Edit
 * @see functions_places.php
 * @version $Id: functions_edit.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

if (strstr($_SERVER["PHP_SELF"],"functions")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

/**
 * The DEBUG variable allows you to turn on debugging
 * which will write all communication output to the pgv log files
 * in the index directory and print other information to the screen.
 * Set this to true to enable debugging,
 * but be sure to set it back to false when you are done debugging.
 * @global boolean $DEBUG
 */
$DEBUG = false;

$NPFX_accept = array("Adm", "Amb", "Brig", "Can", "Capt", "Chan", "Chapln", "Cmdr", "Col", "Cpl", "Cpt", "Dr", "Gen", "Gov", "Hon", "Lady", "Lt", "Mr", "Mrs", "Ms", "Msgr", "Pfc", "Pres", "Prof", "Pvt", "Rep", "Rev", "Sen", "Sgt", "Sir", "Sr", "Sra", "Srta", "Ven");
$SPFX_accept = array("al", "da", "de", "den", "dem", "der", "di", "du", "el", "la", "van", "von");
$NSFX_accept = array("Jr", "Sr", "I", "II", "III", "IV", "MD", "PhD");
$FILE_FORM_accept = array("avi", "bmp", "gif", "jpeg", "mp3", "ole", "pcx", "tiff", "wav");
$emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","OBJE","CHAN","_SEPR","RESI", "DATA", "MAP");

/**
 * read the contents of a gedcom file
 *
 * opens a gedcom file and reads the contents into the <var>$fcontents</var> global string
 */
function read_gedcom_file() {
	global $fcontents;
	global $GEDCOM, $GEDCOMS;
	global $pgv_lang;
	$fcontents = "";
	if (isset($GEDCOMS[$GEDCOM])) {
		$fp = fopen($GEDCOMS[$GEDCOM]["path"], "r");
		$fcontents = fread($fp, filesize($GEDCOMS[$GEDCOM]["path"]));
		fclose($fp);
	}
}

//-- read the file onto the stack
read_gedcom_file();

//-------------------------------------------- newConnection
//-- this function creates a new unique connection
//-- and adds it to the connections file
//-- it returns the connection identifier
function newConnection() {
	return session_name()."\t".session_id()."\n";
}

//-------------------------------------------- get_next_record
//-- gets the next person in the gedcom, if we reach the end then
//-- returns false
function get_next_xref($gid, $type='INDI') {
	global $GEDCOM, $myindilist, $pgv_changes;

	if (!isset($myindilist[$gid])) {
		print "ERROR 4: Could not find gedcom record with xref:$gid\n";
		AddToLog("ERROR 4: Could not find gedcom record with xref:$gid ->" . getUserName() ."<-");
		return false;
	}
	$found = false;
	foreach($myindilist as $key=>$value) {
		if ($found) {
			return $key;
		}
		if ($key==$gid) $found=true;
	}
	//print "ERROR 14: Reached the end of the list\n";
	return "";
}

//-------------------------------------------- get_prev_record
//-- gets the previous person in the gedcom, if we reach the start then
//-- returns the last record
function get_prev_xref($gid, $type='INDI') {
	global $GEDCOM, $myindilist, $pgv_changes;

	if (!isset($myindilist[$gid])) {
		print "ERROR 4: Could not find gedcom record with xref:$gid\n";
		AddToLog("ERROR 4: Could not find gedcom record with xref:$gid ->" . getUserName() ."<-");
		return false;
	}
	$found = false;
	foreach($myindilist as $key=>$value) {
		if ($key==$gid) $found=true;
		if ($found) {
			if (isset($prev)) {
				return $prevkey;
			}
			else {
				//print "ERROR 15: Reached the beginning of the list\n";
				return "";
			}
		}
		$prev = $value;
		$prevkey = $key;
	}
	//print "ERROR 14: Reached the end of the list\n";
	return "";
}

//-------------------------------------------- replace_gedrec
//-- This function will replace a gedcom record with
//-- a the id $gid with the $gedrec
function replace_gedrec($gid, $gedrec, $chan=true) {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save;
	$pos1 = strpos($fcontents, "0 @$gid@");
	if ($pos1===false) {
		print "ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."\n";
		AddToLog("ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."->" . getUserName() ."<-");
		return false;
	}
	if (($gedrec = check_gedcom($gedrec, $chan))!==false) {
		$pos2 = strpos($fcontents, "\n0", $pos1+1);
		if ($pos2===false) {
			$undo = substr($fcontents, $pos1);
			$fcontents = substr($fcontents, 0,$pos1)."\r\n".trim($gedrec)."\r\n0 TRLR\r\n";
		}
		else {
			$pos2++;
			$undo = substr($fcontents, $pos1, $pos2-$pos1);
			$fcontents = substr($fcontents, 0,$pos1).trim($gedrec)."\r\n".substr($fcontents, $pos2);
		}
		$change = array();
		$change["gid"] = $gid;
		$change["gedcom"] = $GEDCOM;
		$change["type"] = "replace";
		$change["status"] = "submitted";
		$change["user"] = getUserName();
		$change["time"] = time();
		$change["undo"] = $undo;
		if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
		$pgv_changes[$gid."_".$GEDCOM][] = $change;
		if (!isset($manual_save) || ($manual_save==false)) {
			AddToLog("Replacing gedcom record $gid ->" . getUserName() ."<-");
			return write_file();
		}
		else return true;
	}
	return false;
}

//-------------------------------------------- append_gedrec
//-- this function will append a new gedcom record at
//-- the end of the gedcom file.
function append_gedrec($gedrec) {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save;

	if (($gedrec = check_gedcom($gedrec))!==false) {
		$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
		$gid = $match[1];
		$type = trim($match[2]);
		$xref = get_new_xref($type);
		$gedrec = preg_replace("/0 @(.*)@/", "0 @$xref@", $gedrec);
		$pos1 = strrpos($fcontents, "0");
		$fcontents = substr($fcontents, 0, $pos1).trim($gedrec)."\r\n".substr($fcontents, $pos1);
		$change = array();
		$change["gid"] = $xref;
		$change["gedcom"] = $GEDCOM;
		$change["type"] = "append";
		$change["status"] = "submitted";
		$change["user"] = getUserName();
		$change["time"] = time();
		$change["undo"] = "";
		if (!isset($pgv_changes[$xref."_".$GEDCOM])) $pgv_changes[$xref."_".$GEDCOM] = array();
		$pgv_changes[$xref."_".$GEDCOM][] = $change;
		AddToLog("Appending new $type record $xref ->" . getUserName() ."<-");
		if (!isset($manual_save) || ($manual_save==false)) {
			if (write_file()) return $xref;
			else return false;
		}
		else return $xref;
	}
	return false;
}

//-------------------------------------------- get_new_xref
//-- get the next available xref
function get_new_xref($type='INDI') {
	global $fcontents, $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX, $pgv_changes, $GEDCOM;

	$ct = preg_match_all("/0 @(.*)@ $type/", $fcontents, $match, PREG_SET_ORDER);
	$num = 0;
	for($i=0; $i<$ct; $i++) {
		$ckey = $match[$i][1];
		$bt = preg_match("/(\d+)/", $ckey, $bmatch);
		if ($bt>0) {
			$bnum = trim($bmatch[1]);
			if ($num < $bnum) $num = $bnum;
		}
	}
	$num++;
	
	$prefix = $type{0};
	if ($type == "SOUR") $prefix = $SOURCE_ID_PREFIX;
	else if ($type == "REPO") $prefix = $REPO_ID_PREFIX;
	else if ($type == "FAM") $prefix = $FAM_ID_PREFIX;
	else if ($type == "INDI") $prefix = $GEDCOM_ID_PREFIX;
	
	$key = $prefix.$num;
	while(isset($pgv_changes[$key."_".$GEDCOM])) {
		$num++;
		$key = $prefix.$num;
	}
	return $key;
}

//-------------------------------------------- delete_gedrec
//-- this function will delete the gedcom record with
//-- the given $gid
function delete_gedrec($gid) {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save;
	$pos1 = strpos($fcontents, "0 @$gid@");
	if ($pos1===false) {
		print "ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."\n";
		AddToLog("ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."->" . getUserName() ."<-");
		return false;
	}
	$pos2 = strpos($fcontents, "\n0", $pos1+1);
	if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
	else $pos2++;
	$undo = substr($fcontents, $pos1, $pos2-$pos1);
	$fcontents = substr($fcontents, 0,$pos1).substr($fcontents, $pos2);
	$change = array();
	$change["gid"] = $gid;
	$change["gedcom"] = $GEDCOM;
	$change["type"] = "delete";
	$change["status"] = "submitted";
	$change["user"] = getUserName();
	$change["time"] = time();
	$change["undo"] = $undo;
	if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
	$pgv_changes[$gid."_".$GEDCOM][] = $change;
	AddToLog("Deleting gedcom record $gid ->" . getUserName() ."<-");
	if (!isset($manual_save)) return write_file();
	else return true;
}

//-------------------------------------------- check_gedcom
//-- this function will check a GEDCOM record for valid gedcom format
function check_gedcom($gedrec, $chan=true) {
	global $pgv_lang, $DEBUG;

	$gedrec = stripslashes($gedrec);
	$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
	if ($ct==0) {
		print "ERROR 20: Invalid GEDCOM 5.5 format.\n";
		AddToLog("ERROR 20: Invalid GEDCOM 5.5 format.->" . getUserName() ."<-");
		if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>\n";
		return false;
	}
	$gedrec = trim($gedrec);
	if ($chan) {
		$pos1 = strpos($gedrec, "1 CHAN");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+4);
			if ($pos2===false) $pos2 = strlen($gedrec);
			$newgedrec = substr($gedrec, 0, $pos1);
			$newgedrec .= "1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU ".getUserName()."\r\n";
			$newgedrec .= substr($gedrec, $pos2);
			$gedrec = $newgedrec;
		}
		else {
			$newgedrec = "\r\n1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU ".getUserName();
			$gedrec .= $newgedrec;
		}
	}
	$gedrec = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $gedrec);
	return $gedrec;
}

//-------------------------------------------- undo_change
//-- this function will undo a change in the gedcom file
function undo_change($cid, $index) {
	global $fcontents, $pgv_changes, $GEDCOMS, $GEDCOM, $manual_save;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[$index];
		$change["undo"] = $change["undo"];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
			read_gedcom_file();
		}
		if ($change["type"]=="delete") {
			$pos1 = strrpos($fcontents, "0");
			$fcontents = substr($fcontents, 0, $pos1).trim($change["undo"])."\r\n".substr($fcontents, $pos1);
		}
		else if ($change["type"]=="append") {
			$pos1 = strpos($fcontents, "0 @".$change["gid"]."@");
			if ($pos1===false) {
				print "ERROR 4: Could not find gedcom record with gid:".$change["gid"]."\n";
				AddToLog("ERROR 4: Could not find gedcom record with gid:".$change["gid"]." ->" . getUserName() ."<-");
				return false;
			}
			$pos2 = strpos($fcontents, "\n0", $pos1+1);
			if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
			else $pos2++;
			if ($pos2!==false) $fcontents = substr($fcontents, 0,$pos1).substr($fcontents, $pos2);
		}
		else if ($change["type"]=="replace") {
			$pos1 = strpos($fcontents, "0 @".$change["gid"]."@");
			if ($pos1===false) {
				$ct = preg_match("/0 @(.*)@/", $change["undo"], $match);
				if ($ct>0) {
					$gid = trim($match[1]);
					$pos1 = strpos($fcontents, "0 @".$gid."@");
				}
			}
			if ($pos1===false) {
				//print "ERROR 4: Could not find gedcom record with gid:".$change["gid"]."\n";
				//return false;
				if (!empty($change["undo"])) {
					$fcontents .= "\r\n".$change["undo"];
				}
			}
			else {
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
				if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
				else $pos2++;
				$fcontents = substr($fcontents, 0,$pos1).trim($change["undo"])."\r\n".substr($fcontents, $pos2);
			}
		}
		if ($index==0) unset($pgv_changes[$cid]);
		else {
			for($i=$index; $i<count($pgv_changes[$cid]); $i++) {
				unset($pgv_changes[$cid][$i]);
			}
			if (count($pgv_changes[$cid])==0) unset($pgv_changes[$cid]);
		}
		AddToLog("Undoing change $cid - $index ".$change["type"]." ->" . getUserName() ."<-");
		if (!isset($manual_save) || ($manual_save==false)) {
			return write_file();
		}
		else return true;
	}
	return false;
}

//-------------------------------------------- write_file
//-- this function writes the $fcontents back to the
//-- gedcom file
function write_file() {
	global $fcontents, $GEDCOMS, $GEDCOM, $pgv_changes, $INDEX_DIRECTORY;

	if (preg_match("/win/i", PHP_OS)==0) {
		$fcontents = preg_replace('/\r/', "\n", $fcontents);
		$fcontents = preg_replace('/\n+/', "\n", $fcontents);
	}
	else {
		$fcontents = preg_replace('/([^\r])\n/', "$1\r\n", $fcontents);
	}
	$fcontents = preg_replace('/\\\+/', "\\", $fcontents);
	if (preg_match("/0 TRLR/", $fcontents)==0) $fcontents.="0 TRLR\n";
	//-- write the gedcom file
	if (!is_writable($GEDCOMS[$GEDCOM]["path"])) {
		print "ERROR 5: GEDCOM file is not writable.  Unable to complete request.\n";
		AddToLog("ERROR 5: GEDCOM file is not writable.  Unable to complete request. ->" . getUserName() ."<-");
		return false;
	}
	$fp = fopen($GEDCOMS[$GEDCOM]["path"], "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request.\n";
		AddToLog("ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request. ->" . getUserName() ."<-");
		return false;
	}
	$fw = fwrite($fp, $fcontents);
	if ($fw===false) {
		print "ERROR 7: Unable to write to GEDCOM file.\n";
		AddToLog("ERROR 7: Unable to write to GEDCOM file. ->" . getUserName() ."<-");
		fclose($fp);
		return false;
	}
	fclose($fp);

	return write_changes();
}

/**
 * prints a form to add an individual or edit an individual's name
 *
 * @param string $nextaction	the next action the edit_interface.php file should take after the form is submitted
 * @param string $famid			the family that the new person should be added to
 * @param string $namerec		the name subrecord when editing a name
 * @param string $famtag		how the new person is added to the family
 */
function print_indi_form($nextaction, $famid, $linenum="", $namerec="", $famtag="CHIL") {
	global $pgv_lang, $factarray, $pid, $PGV_IMAGE_DIR, $PGV_IMAGES, $monthtonum, $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $USE_RTL_FUNCTIONS;

	init_calendar_popup();
	print "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$nextaction\" />\n";
	print "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\" />\n";
	print "<input type=\"hidden\" name=\"famid\" value=\"$famid\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";
	print "<table class=\"facts_table\">";

	// preset child/father SURN
	$surn = "";
	if (empty($namerec)) {
		$indirec = "";
		if ($famtag=="CHIL" and $nextaction=="addchildaction") {
			$famrec = find_family_record($famid);
			if (empty($famrec)) $famrec = find_record_in_file($famid);
			$parents = find_parents_in_record($famrec);
			$indirec = find_person_record($parents["HUSB"]);
		}
		if ($famtag=="HUSB" and $nextaction=="addnewparentaction") {
			$indirec = find_person_record($pid);
		}
		$nt = preg_match("/\d SURN (.*)/", $indirec, $ntmatch);
		if ($nt) $surn = $ntmatch[1];
		else {
			$nt = preg_match("/1 NAME (.*)[\/](.*)[\/]/", $indirec, $ntmatch);
			if ($nt) $surn = $ntmatch[2];
		}
		if ($surn) $namerec = "1 NAME  /".trim($surn,"\r\n")."/";
	}
	// handle PAF extra NPFX [ 961860 ]
	$nt = preg_match("/\d NPFX (.*)/", $namerec, $nmatch);
	$npfx=trim(@$nmatch[1]);
	// 1 NAME = NPFX GIVN /SURN/ NSFX
	$nt = preg_match("/\d NAME (.*)/", $namerec, $nmatch);
	$name=@$nmatch[1];
	if (strlen($npfx) and strpos($name, $npfx)===false) $name = $npfx." ".$name;
	add_simple_tag("0 NAME ".$name);
	// 2 NPFX
	add_simple_tag("0 NPFX ".$npfx);
	// 2 GIVN
	$nt = preg_match("/\d GIVN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 GIVN ".@$nmatch[1]);
	// 2 NICK
	$nt = preg_match("/\d NICK (.*)/", $namerec, $nmatch);
	add_simple_tag("0 NICK ".@$nmatch[1]);
	// 2 SPFX
	$nt = preg_match("/\d SPFX (.*)/", $namerec, $nmatch);
	add_simple_tag("0 SPFX ".@$nmatch[1]);
	// 2 SURN
	$nt = preg_match("/\d SURN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 SURN ".@$nmatch[1]);
	// 2 NSFX
	$nt = preg_match("/\d NSFX (.*)/", $namerec, $nmatch);
	add_simple_tag("0 NSFX ".@$nmatch[1]);
	// 2 _HEB
	$nt = preg_match("/\d _HEB (.*)/", $namerec, $nmatch);
	if ($nt>0 || $USE_RTL_FUNCTIONS) {
		add_simple_tag("0 _HEB ".@$nmatch[1]);
	}
	// 2 ROMN
	$nt = preg_match("/\d ROMN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 ROMN ".@$nmatch[1]);

	if ($surn) $namerec = ""; // reset if modified

	if (empty($namerec)) {
		// 2 _MARNM
		add_simple_tag("0 _MARNM");
		// 1 SEX
		if ($famtag=="HUSB") add_simple_tag("0 SEX M");
		else if ($famtag=="WIFE") add_simple_tag("0 SEX F");
		else add_simple_tag("0 SEX");
		// 1 BIRT
		// 2 DATE
		// 2 PLAC
		add_simple_tag("0 BIRT");
		add_simple_tag("0 DATE", "BIRT");
		add_simple_tag("0 PLAC", "BIRT");
		// 1 DEAT
		// 2 DATE
		// 2 PLAC
		add_simple_tag("0 DEAT");
		add_simple_tag("0 DATE", "DEAT");
		add_simple_tag("0 PLAC", "DEAT");
		print "</table>\n";
		print_add_layer("SOUR", 1);
		print_add_layer("NOTE", 1);
		print_add_layer("OBJE", 1);
		print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
	}
	else {
		if ($namerec!="NEW") {
			$gedlines = split("\n", $namerec);	// -- find the number of lines in the record
			$fields = preg_split("/\s/", $gedlines[0]);
			$glevel = $fields[0];
			$level = $glevel;
			$type = trim($fields[1]);
			$level1type = $type;
			$tags=array();
			$i = 0;
			$namefacts = array("NPFX", "GIVN", "NICK", "SPFX", "SURN", "NSFX", "NAME", "_HEB", "ROMN");
			do {
				if (!in_array($type, $namefacts)) {
					$text = "";
					for($j=2; $j<count($fields); $j++) {
						if ($j>2) $text .= " ";
						$text .= $fields[$j];
					}
					$iscont = false;
					while(($i+1<count($gedlines))&&(preg_match("/".($level+1)." (CON[CT])\s?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
						$iscont=true;
						if ($cmatch[1]=="CONT") $text.="\r\n";
						if ($WORD_WRAPPED_NOTES) $text .= " ";
						$text .= $cmatch[2];
						$i++;
					}
					add_simple_tag($level." ".$type." ".$text);
				}
				$tags[]=$type;
				$i++;
				if (isset($gedlines[$i])) {
					$fields = preg_split("/\s/", $gedlines[$i]);
					$level = $fields[0];
					if (isset($fields[1])) $type = trim($fields[1]);
				}
			} while (($level>$glevel)&&($i<count($gedlines)));
		}
		// 2 _MARNM
		add_simple_tag("0 _MARNM");
		print "</tr>\n";
		print "</table>\n";
		print_add_layer("SOUR");
		print_add_layer("NOTE");
		print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
	}
	print "</form>\n";
	?>
	<script type="text/javascript" src="autocomplete.js"></script>
	<script type="text/javascript">
	<!--
	//	copy php arrays into js arrays
	var npfx_accept = new Array(<?php foreach ($NPFX_accept as $indexval => $npfx) print "'".$npfx."',"; print "''";?>);
	var spfx_accept = new Array(<?php foreach ($SPFX_accept as $indexval => $spfx) print "'".$spfx."',"; print "''";?>);
	Array.prototype.in_array = function(val) {
		for (var i in this) {
			if (this[i] == val) return true;
		}
		return false;
	}
	function trim(str) {
		return str.replace(/(^\s*)|(\s*$)/g,'');
	}
	function updatewholename() {
		frm = document.forms[0];
		var npfx=trim(frm.NPFX.value);
		if (npfx) npfx+=" ";
		var givn=trim(frm.GIVN.value);
		var spfx=trim(frm.SPFX.value);
		if (spfx) spfx+=" ";
		var surn=trim(frm.SURN.value);
		var nsfx=trim(frm.NSFX.value);
		frm.NAME.value = npfx + givn + " /" + spfx + surn + "/ " + nsfx;
	}
	function togglename() {
		frm = document.forms[0];

		// show/hide NAME
		var ronly = frm.NAME.readOnly;
		if (ronly) {
			updatewholename();
			frm.NAME.readOnly=false;
			frm.NAME_spec.style.display="inline";
			frm.NAME_plus.style.display="inline";
			frm.NAME_minus.style.display="none";
			disp="none";
		}
		else {
			// split NAME = (NPFX) GIVN / (SPFX) SURN / (NSFX)
			var name=frm.NAME.value+'//';
			var name_array=name.split("/");
			var givn=trim(name_array[0]);
			var givn_array=givn.split(" ");
			var surn=trim(name_array[1]);
			var surn_array=surn.split(" ");
			var nsfx=trim(name_array[2]);

			// NPFX
			var npfx='';
			do {
				search=givn_array[0]; // first word
				search=search.replace(/(\.*$)/g,''); // remove trailing '.'
				if (npfx_accept.in_array(search)) npfx+=givn_array.shift()+' ';
				else break;
			} while (givn_array.length>0);
			frm.NPFX.value=trim(npfx);

			// GIVN
			frm.GIVN.value=trim(givn_array.join(' '));

			// SPFX
			var spfx='';
			do {
				search=surn_array[0]; // first word
				search=search.replace(/(\.*$)/g,''); // remove trailing '.'
				if (spfx_accept.in_array(search)) spfx+=surn_array.shift()+' ';
				else break;
			} while (surn_array.length>0);
			frm.SPFX.value=trim(spfx);

			// SURN
			frm.SURN.value=trim(surn_array.join(' '));

			// NSFX
			frm.NSFX.value=trim(nsfx);

			// NAME
			frm.NAME.readOnly=true;
			frm.NAME_spec.style.display="none";
			frm.NAME_plus.style.display="none";
			frm.NAME_minus.style.display="inline";
			disp="table-row";
			if (document.all) disp="inline"; // IE
		}
		// show/hide
		document.getElementById("NPFX_tr").style.display=disp;
		document.getElementById("GIVN_tr").style.display=disp;
		document.getElementById("NICK_tr").style.display=disp;
		document.getElementById("SPFX_tr").style.display=disp;
		document.getElementById("SURN_tr").style.display=disp;
		document.getElementById("NSFX_tr").style.display=disp;
	}
	function checkform() {
		frm = document.addchildform;
		/* if (frm.GIVN.value=="") {
			alert('<?php print $pgv_lang["must_provide"].$pgv_lang["given_name"]; ?>');
			frm.GIVN.focus();
			return false;
		}
		if (frm.SURN.value=="") {
			alert('<?php print $pgv_lang["must_provide"].$pgv_lang["surname"]; ?>');
			frm.SURN.focus();
			return false;
		}*/
		var fname=frm.NAME.value;
		fname=fname.replace(/ /g,'');
		fname=fname.replace(/\//g,'');
		if (fname=="") {
			alert('<?php print $pgv_lang["must_provide"]." ".$factarray["NAME"]; ?>');
			frm.NAME.focus();
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php
	// force name expand on form load (maybe optional in a further release...)
	print "<script type='text/javascript'>togglename();</script>";
}

/**
 * javascript declaration for calendar popup
 *
 * @param none
 */
function init_calendar_popup() {
	?>
	<script type="text/javascript" src="CalendarPopup.js"></script>
	<script type="text/javascript">
	<!--
		document.write(getCalendarStyles());
	//-->
	</script>
	<?php
}

/**
 * generates javascript code for calendar popup in user's language
 *
 * @param string id		form text element id where to return date value
 * @see init_calendar_popup()
 */
function print_calendar_popup($id) {
	global $pgv_lang, $monthtonum, $WEEK_START, $PGV_IMAGE_DIR, $PGV_IMAGES;

	print "<script type='text/javascript'>\n";
	print "var cal".$id." = new CalendarPopup(\"caldiv".$id."\");\n";
	print "cal".$id.".showYearNavigation();\n";
	print "cal".$id.".showYearNavigationInput();\n";
	// today
	print "cal".$id.".setTodayText(\"".$pgv_lang["today"]."\");\n";
	// month names
	print "cal".$id.".setMonthNames(";
	foreach($monthtonum as $mon=>$num) {
		if (isset($pgv_lang[$mon])) {
			if ($num>1) print ",";
			print "\"".$pgv_lang[$mon]."\"";
		}
	}
	print ");\n";
	// day headers
	print "cal".$id.".setDayHeaders(";
	foreach(array('sunday','monday','tuesday','wednesday','thursday','friday','saturday') as $indexval => $day) {
		if (isset($pgv_lang[$day])) {
			if ($day!=="sunday") print ",";
			print "\"".get_first_letter($pgv_lang[$day])."\"";
		}
	}
	print ");\n";
	// week start day
	print "cal".$id.".setWeekStartDay(".$WEEK_START.");\n";
	print "cal".$id.".setTodayText(\"".$pgv_lang["today"]."\");\n";
	print "</script>\n";
	// calendar button
	$text = $pgv_lang["select_date"];
	print "<a href=\"javascript: ".$text."\" onclick=\"cal".$id.".select(document.getElementById('".$id."'),'img".$id."','d NNN yyyy'); return false;\">";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["small"]."\" name=\"img".$id."\" id=\"img".$id."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" /></a>\n";
	print "<div id=\"caldiv".$id."\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\"></div>\n";
}

/**
 * prints a link to open the Find Special Character window
 */
function print_specialchar_link($element_id,$vert) {
	global $pgv_lang;

	$text = $pgv_lang["find_specialchar"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findSpecialChar(document.getElementById('$element_id')); updatewholename(); \">";
	print "<img id=\"".$element_id."_spec\" name=\"".$element_id."_spec\" src=\"images/keyboard.gif\"  alt=\"".$text."\"  title=\"".$text."\" border=\"0\" align=\"middle\" /></a>";
}

function print_autopaste_link($element_id, $choices, $concat=1) {
	global $pgv_lang;

	print "<small>";
	foreach ($choices as $indexval => $choice) {
		print " &nbsp;<a href=\"javascript: ".$pgv_lang["copy"]."\" onclick=\"document.getElementById('".$element_id."').value ";
		if ($concat) print "+=' "; else print "='";
		print $choice."'; updatewholename();\">".$choice."</a>";
	}
	print "</small>";
}

function print_findplace_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_place"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findPlace(document.getElementById('".$element_id."'));\">";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" /></a>";
}

function print_findindi_link($element_id, $indiname) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_id"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findIndi(document.getElementById('".$element_id."'), '".$indiname."');\">";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" /></a>";
}

function print_findsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_sourceid"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findSource(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["source"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_source"];
	print "</a>";
}

function print_addnewsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["create_source"];
	print "&nbsp;&nbsp;&nbsp;<big>+</big><a href=\"javascript: ".$text."\" onclick=\"return addnewsource(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["source"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_source"];
	print "</a>";
}

function print_findrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_repository"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findRepository(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["source"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_repository"];
	print "</a>";
}

function print_addnewrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["create_repository"];
	print "&nbsp;&nbsp;&nbsp;<big>+</big><a href=\"javascript: ".$text."\" onclick=\"return addnewrepository(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["source"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_repository"];
	print "</a>";
}

function print_findfamily_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_family"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findFamily(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["sfamily"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_family"];
	print "</a>";
}

function print_findmedia_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["find_media"];
	print " <a href=\"javascript: ".$text."\" onclick=\"return findMedia(document.getElementById('".$element_id."'));\">";
	if (isset($PGV_IMAGES["media"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else print $pgv_lang["find_media"];
	print "</a>";
}

/**
 * add a new tag input field
 *
 * called for each fact to be edited on a form.
 * Fact level=0 means a new empty form : data are POSTed by name
 * else data are POSTed using arrays :
 * glevels[] : tag level
 *  islink[] : tag is a link
 *     tag[] : tag name
 *    text[] : tag value
 *
 * @param string $tag			fact record to edit (eg 2 DATE xxxxx)
 * @param string $upperlevel	optional upper level tag (eg BIRT)
 */
function add_simple_tag($tag, $upperlevel="") {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_DIRECTORY, $TEMPLE_CODES, $STATUS_CODES, $REPO_ID_PREFIX, $SPLIT_PLACES;
	global $assorela, $tags, $emptyfacts;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $upload_count;
	static $tabkey;
	
	if (!isset($tabkey)) $tabkey = 1;

	$largetextfacts = array("TEXT","PUBL","NOTE");
	$subnamefacts = array("NPFX", "GIVN", "NICK", "SPFX", "SURN", "NSFX");

	@list($level, $fact, $value) = explode(" ", $tag);

	// element name : used to POST data
	if ($upperlevel) $element_name=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE | ...
	else if ($level==0) $element_name=$fact; // ex: OCCU
	else $element_name="text[]";

	// element id : used by javascript functions
	if ($level==0) $element_id=$fact; // ex: NPFX | GIVN ...
	else $element_id=$fact.floor(microtime()*1000000); // ex: SOUR56402
	if ($upperlevel) $element_id=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE ...

	// field value
	$islink = (substr($value,0,1)=="@" and substr($value,0,2)!="@#");
	if ($islink) $value=trim($value, " @");
	else $value=trim(substr($tag, strlen($fact)+3));

	// rows & cols
	$rows=1;
	$cols=60;
	if ($islink) $cols=10;
	if ($fact=="FORM") $cols=5;
	if ($fact=="DATE" or $fact=="TIME" or $fact=="TYPE") $cols=20;
	if ($fact=="LATI" or $fact=="LONG") $cols=12;
	if (in_array($fact, $subnamefacts)) $cols=25;
	if (in_array($fact, $largetextfacts)) { $rows=10; $cols=70; }
	if ($fact=="ADDR") $rows=5;
	if ($fact=="REPO") $cols = strlen($REPO_ID_PREFIX) + 4;

	// label
	$style="";
	print "<tr id=\"".$element_id."_tr\" ";
	if (in_array($fact, $subnamefacts)) print " style=\"display:none;\""; // hide subname facts
	print " >\n";
	print "<td class=\"facts_label".$style."\">";
	if ($GLOBALS["DEBUG"]) print $element_name."<br />\n";
	if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
	else if (isset($factarray[$fact])) print $factarray[$fact];
	else print $fact;
	print "\n";

	// help link
	if (!in_array($fact, $emptyfacts)) {
		if ($fact=="DATE") print_help_link("def_gedcom_date_help", "qm");
		else if ($fact=="RESN") print_help_link($fact."_help", "qm");
		else print_help_link("edit_".$fact."_help", "qm");
	}

	// tag level
	if ($level>0) {
		if ($fact=="TEXT") {
			print "<input type=\"hidden\" name=\"glevels[]\" value=\"".($level-1)."\" />";
			print "<input type=\"hidden\" name=\"islink[]\" value=\"0\" />";
			print "<input type=\"hidden\" name=\"tag[]\" value=\"DATA\" />";
			print "<input type=\"hidden\" name=\"text[]\" value=\"\" />";
		}
		print "<input type=\"hidden\" name=\"glevels[]\" value=\"".$level."\" />\n";
		print "<input type=\"hidden\" name=\"islink[]\" value=\"".($islink)."\" />\n";
		print "<input type=\"hidden\" name=\"tag[]\" value=\"".$fact."\" />\n";
	}
	print "\n</td>";

	// value
	print "<td class=\"facts_value\">\n";
	if ($GLOBALS["DEBUG"]) print $tag."<br />\n";

	// retrieve linked NOTE
	if ($fact=="NOTE" and $islink) {
		$noteid = $value;
		print "<input type=\"hidden\" name=\"text[]\" value=\"".$noteid."\" />\n";
		$noterec = find_gedcom_record($noteid);
		$nt = preg_match("/0 @$value@ NOTE (.*)/", $noterec, $n1match);
		if ($nt!==false) $value=trim(strip_tags(@$n1match[1].get_cont(1, $noterec)));
		$element_name="NOTE[".$noteid."]";
	}

	if (in_array($fact, $emptyfacts)) {
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />";
	}
	else if ($fact=="TEMP") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
		foreach($TEMPLE_CODES as $code=>$temple) {
			print "<option value=\"$code\"";
			if ($code==$value) print " selected=\"selected\"";
			print ">$temple</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="STAT") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		print "<option value=''>No special status</option>\n";
		foreach($STATUS_CODES as $code=>$status) {
			print "<option value=\"$code\"";
			if ($code==$value) print " selected=\"selected\"";
			print ">$status</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="RELA") {
		$text=strtolower($value);
		// add current relationship if not found in default list
		if (!array_key_exists($text, $assorela)) $assorela[$text]=$text;
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		foreach ($assorela as $key=>$value) {
			print "<option value=\"". $key . "\"";
			if ($key==$text) print " selected=\"selected\"";
			print ">" . $assorela["$key"] . "</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="RESN") {
		?>
		<script type="text/javascript">
		<!--
		function update_RESN_img(resn_val) {
			document.getElementById("RESN_none").style.display="none";
			document.getElementById("RESN_locked").style.display="none";
			document.getElementById("RESN_privacy").style.display="none";
			document.getElementById("RESN_confidential").style.display="none";
			document.getElementById("RESN_"+resn_val).style.display="inline";
			if (resn_val=='none') resn_val='';
			document.getElementById("<?php print $element_id?>").value=resn_val;
		}
		//-->
		</script>
		<?php
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" />\n";
		print "<table><tr valign=\"top\">\n";
		foreach (array("none", "locked", "privacy", "confidential") as $resn_index => $resn_val) {
			if ($resn_val=="none") $resnv=""; else $resnv=$resn_val;
			print "<td><input tabindex=\"".$tabkey."\" type=\"radio\" name=\"RESN_radio\" onclick=\"update_RESN_img('".$resn_val."')\"";
			print " value=\"".$resnv."\"";
			if ($value==$resnv) print " checked=\"checked\"";
			print " /><small>".$pgv_lang[$resn_val]."</small>";
			print "<br />&nbsp;<img id=\"RESN_".$resn_val."\" src=\"images/RESN_".$resn_val.".gif\"  alt=\"".$pgv_lang[$resn_val]."\" title=\"".$pgv_lang[$resn_val]."\" border=\"0\"";
			if ($value==$resnv) print " style=\"display:inline\""; else print " style=\"display:none\"";
			print " /></td>\n";
		}
		print "</tr></table>\n";
	}
	else if ($fact=="_PRIM" or $fact=="_THUM") {
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		print "<option value=\"\"></option>\n";
		print "<option value=\"Y\"";
		if ($value=="Y") print " selected=\"selected\"";
		print ">".$pgv_lang["yes"]."</option>\n";
		print "<option value=\"N\"";
		if ($value=="N") print " selected=\"selected\"";
		print ">".$pgv_lang["no"]."</option>\n";
		print "</select>\n";
	}
	else if ($fact=="SEX") {
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\">\n<option value=\"M\"";
		if ($value=="M") print " selected=\"selected\"";
		print ">".$pgv_lang["male"]."</option>\n<option value=\"F\"";
		if ($value=="F") print " selected=\"selected\"";
		print ">".$pgv_lang["female"]."</option>\n<option value=\"U\"";
		if ($value=="U" || empty($value)) print " selected=\"selected\"";
		print ">".$pgv_lang["unknown"]."</option>\n</select>\n";
	}
	else {
		// textarea
		if ($rows>1) print "<textarea tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" rows=\"".$rows."\" cols=\"".$cols."\">".$value."</textarea>\n";
		// text
		else {
			print "<input tabindex=\"".$tabkey."\" type=\"text\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".htmlspecialchars($value)."\" size=\"".$cols."\" dir=\"ltr\"";
			if ($fact=="NPFX") print " onkeyup=\"wactjavascript_autoComplete(npfx_accept,this,event)\" autocomplete=\"off\" ";
			if (in_array($fact, $subnamefacts)) print " onchange=\"updatewholename();\"";
			if ($fact=="DATE") print " onblur=\"valid_date(this);\"";
			print " />\n";
		}
		// split PLAC
		if ($fact=="PLAC") {
			print "<div id=\"".$element_id."_pop\" style=\"display: inline;\">\n";
			print_specialchar_link($element_id, false);
			print_findplace_link($element_id);
			print "</div>\n";
			if ($SPLIT_PLACES) {
				if (!function_exists("print_place_subfields")) require("includes/functions_places.php");
				print_place_subfields($element_id);
			}
		}
		else if ($cols>20 and $fact!="NPFX") print_specialchar_link($element_id, false);
	}
	// MARRiage TYPE : hide text field and show a selection list
	if ($fact=="TYPE" and $tags[0]=="MARR") {
		print "<script type='text/javascript'>";
		print "document.getElementById('".$element_id."').style.display='none'";
		print "</script>";
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."_sel\" onchange=\"document.getElementById('".$element_id."').value=this.value;\" >\n";
		foreach (array("Unknown", "Civil", "Religious", "Partners") as $indexval => $key) {
			if ($key=="Unknown") print "<option value=\"\"";
			else print "<option value=\"".$key."\"";
			$a=strtolower($key);
			$b=strtolower($value);
			if (@strpos($a, $b)!==false or @strpos($b, $a)!==false) print " selected=\"selected\"";
			print ">".$factarray["MARR_".strtoupper($key)]."</option>\n";
		}
		print "</select>";
	}

	// popup links
	if ($fact=="DATE") print_calendar_popup($element_id);
	if ($fact=="FAMC") print_findfamily_link($element_id, "");
	if ($fact=="FAMS") print_findfamily_link($element_id, "");
	if ($fact=="ASSO") print_findindi_link($element_id, get_person_name($value));
	if ($fact=="FILE") print_findmedia_link($element_id);
	if ($fact=="SOUR") print_findsource_link($element_id);
	if ($fact=="SOUR" and !$value) print_addnewsource_link($element_id);
	if ($fact=="REPO") print_findrepository_link($element_id);
	if ($fact=="REPO" and !$value) print_addnewrepository_link($element_id);

	// current value
	if ($fact=="DATE") print get_changed_date($value);
	if ($fact=="ASSO" and $value) print " ".get_person_name($value)." (".$value.")";
	if ($fact=="SOUR" and $value) print " ".get_source_descriptor($value)." (".$value.")";

	// pastable values
	if ($fact=="NPFX") print " <img src=\"images/autocomplete.gif\" alt=\"autocompletion active\" title=\"autocompletion active\" border=\"0\" align=\"middle\" />";
	if ($fact=="SPFX") print_autopaste_link($element_id, $SPFX_accept);
	if ($fact=="NSFX") print_autopaste_link($element_id, $NSFX_accept);
	if ($fact=="FORM") print_autopaste_link($element_id, $FILE_FORM_accept, false);

	// split NAME
	if ($fact=="NAME") {
		print "&nbsp;<a href=\"javascript: ".$pgv_lang["show_details"]."\" onclick=\"togglename(); return false;\"><img id=\"".$element_id."_plus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /></a>\n";
		print "<a href=\"javascript: ".$pgv_lang["show_details"]."\" onclick=\"togglename(); return false;\"><img style=\"display:none;\" id=\"".$element_id."_minus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /></a>\n";
	}

	print "</td></tr>\n";

	// upload FILE option
	if ($fact=="FILE" and is_writable($MEDIA_DIRECTORY)) {
		if (!isset($upload_count)) $upload_count = 0;
		$upload_count++;
		print "<tr><td class=\"facts_label\">".$pgv_lang["upload_file"];
		print "<input type=\"hidden\" name=\"glevels[]\" value=\"".$level."\" />\n";
		print "<input type=\"hidden\" name=\"islink[]\" value=\"0\" />\n";
		print "<input type=\"hidden\" name=\"tag[]\" value=\"FILE\" />\n";
		print_help_link("edit_UPLOAD_FILE_help", "qm");
		print "</td>\n";
		print "<td class=\"facts_value\">\n";
		print "<input type=\"hidden\" name=\"text[]\" size=\"40\" />\n";
		print "<input tabindex=\"".$tabkey."\" type=\"file\" name=\"UPLOAD$upload_count\" size=\"40\" />\n";
		print "<br /> ".$MEDIA_DIRECTORY."\n";
		print "</td></tr>\n";
	}
	$tabkey++;
}

/**
 * prints collapsable fields to add ASSO/RELA, SOUR, OBJE ...
 *
 * @param string $tag		Gedcom tag name
 */
function print_add_layer($tag, $level=2) {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $MEDIA_DIRECTORY;

	if ($tag=="SOUR") {
		//-- Add new source to fact
		print "<a href=\"#\" onclick=\"return expand_layer('newsource');\"><img id=\"newsource_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_source"]."</a>";
		print_help_link("edit_add_SOUR_help", "qm");
		print "<br />";
		print "<div id=\"newsource\" style=\"display: none;\">\n";
		print "<table class=\"facts_table\">\n";
		// 2 SOUR
		add_simple_tag("$level SOUR @");
		// 3 PAGE
		add_simple_tag(($level+1)." PAGE");
		// 3 DATA
		// 4 TEXT
		add_simple_tag(($level+2)." TEXT");
		print "</table></div>";
	}
	if ($tag=="ASSO") {
		//-- Add a new ASSOciate
		print "<a href=\"#\" onclick=\"return expand_layer('newasso');\"><img id=\"newasso_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_asso"]."</a>";
		print_help_link("edit_add_ASSO_help", "qm");
		print "<br />";
		print "<div id=\"newasso\" style=\"display: none;\">\n";
		print "<table class=\"facts_table\">\n";
		// 2 ASSO
		add_simple_tag(($level)." ASSO @");
		// 3 RELA
		add_simple_tag(($level+1)." RELA");
		// 3 NOTE
		add_simple_tag(($level+1)." NOTE");
		print "</table></div>";
	}
	if ($tag=="NOTE") {
		//-- Add new note to fact
		print "<a href=\"#\" onclick=\"return expand_layer('newnote');\"><img id=\"newnote_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_note"]."</a>";
		print_help_link("edit_add_NOTE_help", "qm");
		print "<br />\n";
		print "<div id=\"newnote\" style=\"display: none;\">\n";
		print "<table class=\"facts_table\">\n";
		// 2 NOTE
		add_simple_tag(($level)." NOTE");
		print "</table></div>";
	}
	if ($tag=="OBJE") {
		//-- Add new obje to fact
		print "<a href=\"#\" onclick=\"return expand_layer('newobje');\"><img id=\"newobje_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_obje"]."</a>";
		print_help_link("add_media_help", "qm");
		print "<br />";
		print "<div id=\"newobje\" style=\"display: none;\">\n";
		print "<table class=\"facts_table\">\n";
		// 2 OBJE
		add_simple_tag(($level)." OBJE");
		// 3 FORM
		add_simple_tag(($level+1)." FORM");
		// 3 FILE
		add_simple_tag(($level+1)." FILE");
		// 3 TITL
		add_simple_tag(($level+1)." TITL");
		if ($level==1) {
			// 3 _PRIM
			add_simple_tag(($level+1)." _PRIM");
			// 3 _THUM
			add_simple_tag(($level+1)." _THUM");
		}
		print "</table></div>";
	}
}
/**
 * Add Debug Log
 *
 * This function checks the if the global $DEBUG
 * variable is true and adds debugging information
 * to the log file
 * @param string $logstr	the string to add to the log
 */
function addDebugLog($logstr) {
	global $DEBUG;
	if ($DEBUG) AddToLog($logstr);
}

/**
 * Add new gedcom lines from interface update arrays
 * @param string $newged	the new gedcom record to add the lines to
 * @return string	The updated gedcom record
 */
function handle_updates($newged) {
	global $glevels, $islink, $tag, $uploaded_files, $text, $NOTE;
	
	for($j=0; $j<count($glevels); $j++) {
		//-- update external note records first
		if (($islink[$j])&&($tag[$j]=="NOTE")) {
			if (empty($NOTE[$text[$j]])) {
				delete_gedrec($text[$j]);
				$text[$j] = "";
			}
			else {
				$noterec = find_gedcom_record($text[$j]);
				$newnote = "0 @$text[$j]@ NOTE\r\n";
				$newline = "1 CONC ".$NOTE[$text[$j]];
				$newlines = preg_split("/\r?\n/", $newline);
				for($k=0; $k<count($newlines); $k++) {
					if ($k>0) $newlines[$k] = "1 CONT ".$newlines[$k];
					if (strlen($newlines[$k])>255) {
						while(strlen($newlines[$k])>255) {
							$newnote .= substr($newlines[$k], 0, 255)."\r\n";
							$newlines[$k] = substr($newlines[$k], 255);
							$newlines[$k] = "1 CONC ".$newlines[$k];
						}
						$newnote .= trim($newlines[$k])."\r\n";
					}
					else {
						$newnote .= trim($newlines[$k])."\r\n";
					}
				}
				$notelines = preg_split("/\r?\n/", $noterec);
				for($k=1; $k<count($notelines); $k++) {
					if (preg_match("/1 CON[CT] /", $notelines[$k])==0) $newnote .= trim($notelines[$k])."\r\n";
				}
				if ($GLOBALS["DEBUG"]) print "<pre>$newnote</pre>";
				replace_gedrec($text[$j], $newnote);
			}
		} //-- end of external note handling code
		
		//print $glevels[$j]." ".$tag[$j];
		//-- for facts with empty values they must have sub records
		//-- this section checks if they have subrecords
		$k=$j+1;
		$pass=false;
		while(($k<count($glevels))&&($glevels[$k]>$glevels[$j])) {
			if (!empty($text[$k])) {
				if (($tag[$j]!="OBJE")||($tag[$k]=="FILE")) {
					$pass=true;
					break;
				}
			}
			if (($tag[$k]=="FILE")&&(count($uploaded_files)>0)) {
				$filename = array_shift($uploaded_files);
				if (!empty($filename)) {
					$text[$k] = $filename;
					$pass=true;
					break;
				}
			}
			$k++;
		}

		//-- if the value is not empty then write the line to the gedcom record
		if ((!empty($text[$j]))||($pass==true)) {
			if ($islink[$j]) $text[$j]="@".$text[$j]."@";
			$newline = $glevels[$j]." ".$tag[$j];
			//print $newline;
			if (!empty($text[$j])) $newline .= " ".$text[$j];
			//-- convert returns to CONT lines and break up lines longer than 255 chars
			$newlines = preg_split("/\r?\n/", $newline);
			for($k=0; $k<count($newlines); $k++) {
				if ($k>0) $newlines[$k] = ($glevels[$j]+1)." CONT ".$newlines[$k];
				if (strlen($newlines[$k])>255) {
					while(strlen($newlines[$k])>255) {
						$newged .= substr($newlines[$k], 0, 255)."\r\n";
						$newlines[$k] = substr($newlines[$k], 255);
						$newlines[$k] = ($glevels[$j]+1)." CONC ".$newlines[$k];
					}
					$newged .= trim($newlines[$k])."\r\n";
				}
				else {
					$newged .= trim($newlines[$k])."\r\n";
				}
			}
		}
	}
	
	return $newged;
}
?>