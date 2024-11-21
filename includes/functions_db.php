<?php
/**
 * PEAR:DB specific functions file
 *
 * This file implements the datastore functions necessary for PhpGedView to use an SQL database as its
 * datastore. This file also implements array caches for the database tables.  Whenever data is
 * retrieved from the database it is stored in a cache.  When a database access is requested the
 * cache arrays are checked first before querying the database.
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
 * @version $Id: functions_db.php,v 1.1 2005/10/07 18:08:21 skenow Exp $
 * @package PhpGedView
 * @subpackage DB
 */
if (strstr($_SERVER["PHP_SELF"],"functions")) {
	print "Now, why would you want to do that.	You're not hacking are you?";
	exit;
}

//-- load the PEAR:DB files
if (!class_exists("DB")) require_once('includes/DB.php');

//-- set the REGEXP status of databases
$REGEXP_DB = true;
if ($DBTYPE=='sqlite') $REGEXP_DB = false;

//-- uncomment the following line to turn on sql query logging
//$SQL_LOG = true;

/**
 * query the database
 *
 * this function will perform the given SQL query on the database
 * @param string $sql		the sql query to execture
 * @param boolean $show_error	whether or not to show any error messages
 * @return Object the connection result
 */
function dbquery($sql, $show_error=true) {
	global $DBCONN, $TOTAL_QUERIES, $INDEX_DIRECTORY, $SQL_LOG, $LAST_QUERY;

	if (!isset($DBCONN)) {
		print "No Connection";
		return false;
	}
	//-- make sure a database connection has been established
	if (DB::isError($DBCONN)) {
		print $DBCONN->getCode()." ".$DBCONN->getMessage();
		return $DBCONN;
	}
	$res =& $DBCONN->query($sql);
	$LAST_QUERY = $sql;
	$TOTAL_QUERIES++;
	if (!empty($SQL_LOG)) {
		$fp = fopen($INDEX_DIRECTORY."/sql_log.txt", "a");
		fwrite($fp, date("Y-m-d h:i:s")."\t".$_SERVER["SCRIPT_NAME"]."\t".$res->getUserInfo()."\r\n");
		fclose($fp);
	}
	if (DB::isError($res)) {
		if ($show_error) print "<span class=\"error\"><b>ERROR:".$res->getCode()." ".$res->getMessage()." <br />SQL:</b>".$res->getUserInfo()."</span><br /><br />\n";
	}
	return $res;
}

/**
 * query the database and return the first row
 *
 * this function will perform the given SQL query on the database and return the first row in the result set
 * @param string $sql		the sql query to execture
 * @param boolean $show_error	whether or not to show any error messages
 * @return array the found row
 */
function dbgetrow($sql, $show_error=true) {
	global $DBCONN, $TOTAL_QUERIES, $INDEX_DIRECTORY, $SQL_LOG;
	//-- make sure a database connection has been established
	if (DB::isError($DBCONN)) {
		return false;
	}

	$row =& $DBCONN->getRow($sql);
	$TOTAL_QUERIES++;
	if (!empty($SQL_LOG)) {
		$fp = fopen($INDEX_DIRECTORY."/sql_log.txt", "a");
		fwrite($fp, date("Y-m-d h:m:s")."\t".$_SERVER["SCRIPT_NAME"]."\t$sql\n");
		fclose($fp);
	}
	if (DB::isError($row)) {
		if ($show_error) print "<span class=\"error\"><b>ERROR:".$row->getMessage()." <br />SQL:</b>$sql</span><br /><br />\n";
	}
	return $row;
}

/**
 * prepare an item to be updated in the database
 *
 * add slashes and convert special chars so that it can be added to db
 * @param mixed $item		an array or string to be prepared for the database
 */
function db_prep($item) {
	global $DBCONN;

	if (is_array($item)) {
		foreach($item as $key=>$value) {
			$item[$key]=db_prep($value);
		}
		return $item;
	}
	else {
		if (DB::isError($DBCONN)) return $item;
		if (is_object($DBCONN)) return $DBCONN->escapeSimple($item);
		//-- use the following commented line to convert between character sets
		//return $DBCONN->escapeSimple(iconv("iso-8859-1", "UTF-8", $item));
	}
}

/**
 * Clean up an item retrieved from the database
 *
 * clean the slashes and convert special
 * html characters to their entities for
 * display and entry into form elements
 * @param mixed $item	the item to cleanup
 * @return mixed the cleaned up item
 */
function db_cleanup($item) {
//	return $item;
	if (is_array($item)) {
		foreach($item as $key=>$value) {
			if ($key!="gedcom") $item[$key]=stripslashes($value);
			else $key=$value;
		}
		return $item;
	}
	else {
		return stripslashes($item);
	}
}

/**
 * check if a gedcom has been imported into the database
 *
 * this function checks the database to see if the given gedcom has been imported yet.
 * @param string $ged the filename of the gedcom to check for import
 * @return bool return true if the gedcom has been imported otherwise returns false
 */
function check_for_import($ged) {
	global $TBLPREFIX, $BUILDING_INDEX, $DBCONN;

	$sql = "SELECT count(i_id) FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql, false);
	if (!DB::isError($res)) {
		$row =& $res->fetchRow();
		$res->free();
		if ($row[0]>0) return true;
	}
	return false;
}

/**
 * find the gedcom record for a family
 *
 * This function first checks the <var>$famlist</var> cache to see if the family has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * also lookup the husb and wife so that they are in the cache
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#family
 * @param string $famid the unique gedcom xref id of the family record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_family_record($famid) {
	global $TBLPREFIX;
	global $GEDCOM, $famlist, $DBCONN;

	if (empty($famid)) return false;

	if (isset($famlist[$famid]["gedcom"])&&($famlist[$famid]["file"]==$GEDCOM)) return $famlist[$famid]["gedcom"];

	$sql = "SELECT f_gedcom, f_file, f_husb, f_wife FROM ".$TBLPREFIX."families WHERE f_id LIKE '".$DBCONN->escapeSimple($famid)."' AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	$row =& $res->fetchRow();

	$famlist[$famid]["gedcom"] = $row[0];
	$famlist[$famid]["file"] = $row[1];
	$famlist[$famid]["husb"] = $row[2];
	$famlist[$famid]["wife"] = $row[3];
	find_person_record($row[2]);
	find_person_record($row[3]);
	$res->free();
	return $row[0];
}

/**
 * find the gedcom record for an individual
 *
 * This function first checks the <var>$indilist</var> cache to see if the individual has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#indi
 * @param string $pid the unique gedcom xref id of the individual record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_person_record($pid) {
	global $pgv_lang;
	global $TBLPREFIX;
	global $GEDCOM;
	global $BUILDING_INDEX, $indilist, $DBCONN;

	if (empty($pid)) return false;

	//-- first check the indilist cache
	// cache is unreliable for use with different gedcoms in user favorites (sjouke)
	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["file"]==$GEDCOM)) return $indilist[$pid]["gedcom"];

	$sql = "SELECT i_gedcom, i_name, i_isdead, i_file, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE i_id LIKE '".$DBCONN->escapeSimple($pid)."' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		if ($res->numRows()==0) {
			return false;
		}
		$row =& $res->fetchRow();
		$indilist[$pid]["gedcom"] = $row[0];
		$indilist[$pid]["names"] = get_indi_names($row[0]);
		$indilist[$pid]["isdead"] = $row[2];
		$indilist[$pid]["file"] = $row[3];
		$res->free();
		return $row[0];
	}
}

/**
 * find the gedcom record
 *
 * This function first checks the caches to see if the record has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#other
 * @param string $pid the unique gedcom xref id of the record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_gedcom_record($pid) {
	global $pgv_lang;
	global $TBLPREFIX;
	global $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist, $DBCONN;

	if (empty($pid)) return false;

	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["file"]==$GEDCOM)) return $indilist[$pid]["gedcom"];
	if ((isset($famlist[$pid]["gedcom"]))&&($famlist[$pid]["file"]==$GEDCOM)) return $famlist[$pid]["gedcom"];
	if ((isset($sourcelist[$pid]["gedcom"]))&&($sourcelist[$pid]["file"]==$GEDCOM)) return $sourcelist[$pid]["gedcom"];
	if ((isset($otherlist[$pid]["gedcom"]))&&($otherlist[$pid]["file"]==$GEDCOM)) return $otherlist[$pid]["gedcom"];

	$sql = "SELECT o_gedcom, o_file FROM ".$TBLPREFIX."other WHERE o_id LIKE '".$DBCONN->escapeSimple($pid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$res->free();
		$otherlist[$pid]["gedcom"] = $row[0];
		$otherlist[$pid]["file"] = $row[1];
		return $row[0];
	}
	$gedrec = find_source_record($pid);
	if (empty($gedrec)) {
		$gedrec = find_person_record($pid);
		if (empty($gedrec)) $gedrec = find_family_record($pid);
	}
	return $gedrec;
}

/**
 * find the gedcom record for a source
 *
 * This function first checks the <var>$sourcelist</var> cache to see if the source has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#source
 * @param string $sid the unique gedcom xref id of the source record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_source_record($sid) {
	global $fcontents;
	global $pgv_lang;
	global $TBLPREFIX;
	global $GEDCOM, $sourcelist, $DBCONN;

	if ($sid=="") return false;
	if (isset($sourcelist[$sid]["gedcom"])) return $sourcelist[$sid]["gedcom"];

	$sql = "SELECT s_gedcom, s_name, s_file FROM ".$TBLPREFIX."sources WHERE s_id LIKE '".$DBCONN->escapeSimple($sid)."' AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$sourcelist[$sid]["name"] = stripslashes($row[1]);
		$sourcelist[$sid]["gedcom"] = $row[0];
		$sourcelist[$sid]["file"] = $row[2];
		$res->free();
		return $row[0];
	}
	else {
		return false;
		//return find_record_in_file($sid);
	}
}

// Find a repository record
function find_repo_record($rid) {
	global $fcontents;
	global $pgv_lang;
	global $TBLPREFIX;
	global $GEDCOM, $repolist, $DBCONN;

	if ($rid=="") return false;
	if (isset($repolist[$rid]["gedcom"])) return $repolist[$rid]["gedcom"];

	$sql = "SELECT o_id, o_gedcom, o_file FROM ".$TBLPREFIX."other WHERE o_id LIKE '".$DBCONN->escapeSimple($rid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$tt = preg_match("/1 NAME (.*)/", $row[1], $match);
		if ($tt == "0") $name = $row[0]; else $name = $match[1];
		$repolist[$rid]["name"] = stripslashes($name);
		$repolist[$rid]["gedcom"] = $row[1];
		$repolist[$rid]["file"] = $row[2];
		$res->free();
		return $row[1];
	}
	else {
		return false;
	}
}

/**
 * find and return the id of the first person in the gedcom
 * @return string the gedcom xref id of the first person in the gedcom
 */
function find_first_person() {
	global $GEDCOM, $TBLPREFIX;
	$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file='$GEDCOM' ORDER BY i_id";
	$row =& dbgetrow($sql);
	return $row[0];
}

//=================== IMPORT FUNCTIONS ======================================

/**
 * import record into database
 *
 * this function will parse the given gedcom record and add it to the database
 * @param string $indirec the raw gedcom record to parse
 * @param boolean $update whether or not this is an updated record that has been accepted
 */
function import_record($indirec, $update=false) {
	global $DBCONN, $gid, $type,$indilist,$famlist,$sourcelist,$otherlist, $TOTAL_QUERIES, $prepared_statement;
	global $TBLPREFIX, $GEDCOM_FILE, $FILE, $pgv_lang, $USE_RIN, $CREATE_GENDEX, $gdfp, $placecache;
	global $ALPHABET_upper, $ALPHABET_lower, $place_id, $WORD_WRAPPED_NOTES;

	//-- import different types of records
	$ct = preg_match("/0 @(.*)@ ([A-Z_]+)/", $indirec, $match);
	if ($ct > 0) {
		$gid = $match[1];
		$type = trim($match[2]);
	}
	else {
		$ct = preg_match("/0 (.*)/", $indirec, $match);
		if ($ct>0) {
			$gid = trim($match[1]);
			$type = trim($match[1]);
		}
		else {
			print $pgv_lang["invalid_gedformat"]."<br /><pre>$indirec</pre>\n";
		}
	}

	//-- remove double @ signs
	$indirec = preg_replace("/@+/", "@", $indirec);

	// remove heading spaces
	$indirec = preg_replace("/\n(\s*)/", "\n", $indirec);

	//-- if this is an import from an online update then import the places
	if ($update) update_places($gid, $indirec, $update);

	if ($type == "INDI") {
		$indirec = cleanup_tags_y($indirec);
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$sfams = "";
		for($j=0; $j<$ct; $j++) {
			$sfams .= $match[$j][1].";";
		}
		$ct = preg_match_all("/1 FAMC @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$cfams = "";
		for($j=0; $j<$ct; $j++) {
			$cfams .= $match[$j][1].";";
		}
		$isdead = -1;
		$indi = array();
		$names = get_indi_names($indirec, true);
		$j=0;
		foreach($names as $indexval => $name) {
			if ($j>0) {
				$sql = "INSERT INTO ".$TBLPREFIX."names VALUES('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($FILE)."','".$DBCONN->escapeSimple($name[0])."','".$DBCONN->escapeSimple($name[1])."','".$DBCONN->escapeSimple($name[2])."','".$DBCONN->escapeSimple($name[3])."')";
				$res =& dbquery($sql);
			}
			$j++;
		}
		$indi["names"] = $names;
		$indi["isdead"] = $isdead;
		$indi["gedcom"] = $indirec;
		$indi["file"] = $FILE;
		if ($USE_RIN) {
			$ct = preg_match("/1 RIN (.*)/", $indirec, $match);
			if ($ct>0) $rin = trim($match[1]);
			else $rin = $gid;
			$indi["rin"] = $rin;
		}
		else $indi["rin"] = $gid;

		$sql = "INSERT INTO ".$TBLPREFIX."individuals VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($indi["file"])."','".$DBCONN->escapeSimple($indi["rin"])."','".$DBCONN->escapeSimple($names[0][0])."',-1,'".$DBCONN->escapeSimple($indi["gedcom"])."','".$DBCONN->escapeSimple($names[0][1])."','".$DBCONN->escapeSimple($names[0][2])."')";
		$res =& dbquery($sql);
		//-- PEAR supports prepared statements in mysqli we will use this code instead of the code above
		//if (!isset($prepared_statement)) $prepared_statement = $DBCONN->prepare("INSERT INTO ".$TBLPREFIX."individuals VALUES (?,?,?,?,?,?,?,?)");
		//$data = array($DBCONN->escapeSimple($gid), $DBCONN->escapeSimple($indi["file"]), $indi["rin"], $names[0][0], -1, $indi["gedcom"], $DBCONN->escapeSimple($names[0][1]), $names[0][2]);
		//$res =& $DBCONN->execute($prepared_statement, $data);
		//$TOTAL_QUERIES++;
		if (DB::isError($res)) {
		   // die(__LINE__." ".__FILE__."  ".$res->getMessage());
		}
	}
	else if ($type == "FAM") {
		$indirec = cleanup_tags_y($indirec);
		$parents = array();
		$ct = preg_match("/1 HUSB @(.*)@/", $indirec, $match);
		if ($ct>0) $parents["HUSB"]=$match[1];
		else $parents["HUSB"]=false;
		$ct = preg_match("/1 WIFE @(.*)@/", $indirec, $match);
		if ($ct>0) $parents["WIFE"]=$match[1];
		else $parents["WIFE"]=false;
		$ct = preg_match_all("/\d CHIL @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$chil = "";
		for($j=0; $j<$ct; $j++) {
			$chil .= $match[$j][1].";";
		}
		$fam = array();
		$fam["HUSB"] = $parents["HUSB"];
		$fam["WIFE"] = $parents["WIFE"];
		$fam["CHIL"] = $chil;
		$fam["gedcom"] = $indirec;
		$fam["file"] = $FILE;
		//$famlist[$gid] = $fam;
		$sql = "INSERT INTO ".$TBLPREFIX."families (f_id, f_file, f_husb, f_wife, f_chil, f_gedcom) VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($fam["file"])."','".$DBCONN->escapeSimple($fam["HUSB"])."','".$DBCONN->escapeSimple($fam["WIFE"])."','".$DBCONN->escapeSimple($fam["CHIL"])."','".$DBCONN->escapeSimple($fam["gedcom"])."')";
		$res =& dbquery($sql);
	}
	else if ($type=="SOUR") {
		$et = preg_match("/1 ABBR (.*)/", $indirec, $smatch);
		if ($et>0) $name = $smatch[1];
		$tt = preg_match("/1 TITL (.*)/", $indirec, $smatch);
		if ($tt>0) $name = $smatch[1];
		if (empty($name)) $name = $gid;
		$subindi = preg_split("/1 TITL /",$indirec);
		if (count($subindi)>1) {
			$pos = strpos($subindi[1], "\n1", 0);
			if ($pos) $subindi[1] = substr($subindi[1],0,$pos);
			$ct = preg_match_all("/2 CON[C|T] (.*)/", $subindi[1], $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$name = trim($name);
				if ($WORD_WRAPPED_NOTES) $name .= " ".$match[$i][1];
				else $name .= $match[$i][1];
			}
		}
		$sql = "INSERT INTO ".$TBLPREFIX."sources VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($FILE)."','".$DBCONN->escapeSimple($name)."','".$DBCONN->escapeSimple($indirec)."')";
		$res =& dbquery($sql);
	}
	else if (preg_match("/_/", $type)==0) {
		if ($type=="HEAD") {
			$ct=preg_match("/1 DATE (.*)/", $indirec, $match);
			if ($ct == 0) {
				$indirec = trim($indirec);
				$indirec .= "\r\n1 DATE ".date("d")." ".date("M")." ".date("Y");
			}
		}
		$sql = "INSERT INTO ".$TBLPREFIX."other VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($FILE)."','".$DBCONN->escapeSimple($type)."','".$DBCONN->escapeSimple($indirec)."')";
		$res =& dbquery($sql);
	}
}

/**
 * update the is_dead status in the database
 *
 * this function will update the is_dead field in the individuals table with the correct value
 * calculated by the is_dead() function.  To improve import performance, the is_dead status is first
 * set to -1 during import.  The first time the is_dead status is retrieved this function is called to update
 * the database.  This makes the first request for a person slower, but will speed up all future requests.
 * @param string $gid	gedcom xref id of individual to update
 * @param array $indi	the $indi array struction for the individal as used in the <var>$indilist</var>
 * @return int	1 if the person is dead, 0 if living
 */
function update_isdead($gid, $indi) {
	global $TBLPREFIX, $USE_RIN, $indilist, $DBCONN;
	$isdead = 0;
	$isdead = is_dead($indi["gedcom"]);
	if (empty($isdead)) $isdead = 0;
	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_isdead=$isdead WHERE i_id LIKE '".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($indi["file"])."'";
	$res =& dbquery($sql);
	if (isset($indilist[$gid])) $indilist[$gid]["isdead"] = $isdead;
	return $isdead;
}

/**
 * Add a new calculated name to the individual names table
 *
 * this function will add a new name record for the given individual, this function is called from the
 * importgedcom.php script stage 5
 * @param string $gid	gedcom xref id of individual to update
 * @param string $newname	the new calculated name to add
 * @param string $surname	the surname for this name
 * @param string $letter	the letter for this name
 */
function add_new_name($gid, $newname, $letter, $surname, $indirec) {
	global $TBLPREFIX, $USE_RIN, $indilist, $FILE, $DBCONN;

	$sql = "INSERT INTO ".$TBLPREFIX."names VALUES('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($FILE)."','".$DBCONN->escapeSimple($newname)."','".$DBCONN->escapeSimple($letter)."','".$DBCONN->escapeSimple($surname)."','C')";
	$res =& dbquery($sql);

	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_gedcom='".$DBCONN->escapeSimple($indirec)."' WHERE i_id='".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($FILE)."'";
	$res =& dbquery($sql);

	$indilist[$gid]["names"][] = array($newname, $letter, $surname, 'C');
	$indilist[$gid]["gedcom"] = $indirec;
}

/**
 * extract all places from the given record and insert them
 * into the places table
 * @param string $indirec
 */
function update_places($gid, $indirec, $update=false) {
	global $FILE, $placecache, $TBLPREFIX, $DBCONN;

	if (!isset($placecache)) $placecache = array();
	//-- import all place locations
	$pt = preg_match_all("/\d PLAC (.*)/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$pt; $i++) {
		$place = trim($match[$i][1]);
		$places = preg_split("/,/", $place);
		$secalp = array_reverse($places);
		$parent_id = 0;
		$level = 0;
		foreach($secalp as $indexval => $place) {
			$place = trim($place);
			$place=preg_replace('/\\\"/', "", $place);
			$place=preg_replace("/[\><]/", "", $place);
			if (empty($parent_id)) $parent_id=0;
			$key = strtolower($place."_".$level."_".$parent_id);
			$addgid = true;
			if (isset($placecache[$key])) {
				$parent_id = $placecache[$key][0];
				if (strpos($placecache[$key][1], $gid.",")===false) {
					$placecache[$key][1] = "$gid,".$placecache[$key][1];
					$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($parent_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($FILE)."')";
					$res =& dbquery($sql);
				}
			}
			else {
				$skip = false;
				if ($update) {
					$sql = "SELECT * FROM ".$TBLPREFIX."places WHERE p_place LIKE '".$DBCONN->escapeSimple($place)."' AND p_level=$level AND p_parent_id='$parent_id' AND p_file='".$DBCONN->escapeSimple($FILE)."'";
					$res =& dbquery($sql);
					if ($res->numRows()>0) {
						$row = $res->fetchRow(DB_FETCHMODE_ASSOC);
						$res->free();
						$parent_id = $row["p_id"];
						$skip=true;
						$placecache[$key] = array($parent_id, $gid.",");
						$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($parent_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($FILE)."')";
						$res =& dbquery($sql);
					}
				}
				if (!$skip) {
					if (!isset($place_id)) {
						$place_id = get_next_id("places", "p_id");
					}
					else $place_id++;
					$sql = "INSERT INTO ".$TBLPREFIX."places VALUES($place_id, '".$DBCONN->escapeSimple($place)."', $level, '$parent_id', '".$DBCONN->escapeSimple($FILE)."')";
					$res =& dbquery($sql);
					$parent_id = $place_id;
					$placecache[$key] = array($parent_id, $gid.",");
					$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($place_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($FILE)."')";
					$res =& dbquery($sql);
				}
			}
			$level++;
		}
	}
	return $pt;
}

/**
 * Create database schema
 *
 * function that checks if the database exists and creates tables
 * automatically handles version updates
 */
function setup_database($stage) {
	global $TBLPREFIX, $pgv_lang, $DBCONN, $DBTYPE;

	//---------- Check if tables exist
	$tables_exist=false;
	$has_rin = false;
	$has_places = false;
	$has_place_gid = false;
	$has_first_letter = false;
	$has_surname = false;
	$has_names = false;
	$has_placelinks = false;
	$has_research = false;

	$data = $DBCONN->getListOf('tables');
	foreach($data as $indexval => $table) {
		if ($table==$TBLPREFIX."individuals") {
			$tables_exist=true;
			if ($DBTYPE!="sqlite") {
				$info = $DBCONN->tableInfo($TBLPREFIX."individuals");
				foreach($info as $indexval => $field) {
					if ($field["name"]=="i_rin") $has_rin = true;
					if ($field["name"]=="i_letter") $has_first_letter = true;
					if ($field["name"]=="i_surname") $has_surname = true;
				}
			}
			else {
				$has_rin = true;
				$has_first_letter = true;
				$has_surname = true;
			}
		}
		if ($table==$TBLPREFIX."places") {
			$has_places = true;
			if ($DBTYPE!="sqlite") {
				$info = $DBCONN->tableInfo($TBLPREFIX."places");
				foreach($info as $indexval => $field) {
					if ($field["name"]=="p_gid") $has_place_gid = true;
				}
			}
		}
		if ($table==$TBLPREFIX."families") {
			if ($DBTYPE!="sqlite") {
				$info = $DBCONN->tableInfo($TBLPREFIX."families");
				foreach($info as $indexval => $field) {
					if ($field["name"]=="f_name") {
						$fsql = "ALTER TABLE ".$TBLPREFIX."families DROP COLUMN f_name";
						$fres =& dbquery($sql);
					}
				}
			}
		}
		if ($table==$TBLPREFIX."names") {
			$has_names = true;
		}
		if ($table==$TBLPREFIX."placelinks") {
			$has_placelinks = true;
		}
		if ($table==$TBLPREFIX."researchlog") {
			$has_research = true;
		}
	}

	if (($tables_exist)&&(!$has_rin || !$has_first_letter)) {
		$sql = "DROP table if exists ".$TBLPREFIX."individuals, ".$TBLPREFIX."families, ".$TBLPREFIX."sources, ".$TBLPREFIX."other, ".$TBLPREFIX."places";
		$res =& dbquery($sql);
		$tables_exist = false;
	}

	if (!$tables_exist) {
		$sql = "CREATE TABLE ".$TBLPREFIX."individuals (i_id VARCHAR(30), i_file VARCHAR(255), i_rin VARCHAR(30), i_name VARCHAR(255), i_isdead INT DEFAULT 1, i_gedcom TEXT, i_letter VARCHAR(5), i_surname VARCHAR(100))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX indi_id ON ".$TBLPREFIX."individuals (i_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX indi_name ON ".$TBLPREFIX."individuals (i_name)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX indi_letter ON ".$TBLPREFIX."individuals (i_letter)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX indi_file ON ".$TBLPREFIX."individuals (i_file)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX indi_surn ON ".$TBLPREFIX."individuals (i_surname)";
			$res =& dbquery($sql);
			print $pgv_lang["created_indis"]."<br />\n";
			$has_surname=true;
		}
		else {
			print $pgv_lang["created_indis_fail"]."<br />\n";
			exit;
		}

		$sql = "CREATE TABLE ".$TBLPREFIX."families (f_id VARCHAR(30), f_file VARCHAR(255), f_husb VARCHAR(30), f_wife VARCHAR(30), f_chil VARCHAR(255), f_gedcom TEXT)";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX fam_id ON ".$TBLPREFIX."families (f_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX fam_file ON ".$TBLPREFIX."families (f_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_fams"]."<br />\n";
		}
		else {
			print $pgv_lang["created_fams_fail"]."<br />\n";
			exit;
		}
		$sql = "CREATE TABLE ".$TBLPREFIX."sources (s_id VARCHAR(30), s_file VARCHAR(255), s_name VARCHAR(255), s_gedcom TEXT)";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX sour_id ON ".$TBLPREFIX."sources (s_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX sour_name ON ".$TBLPREFIX."sources (s_name)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX sour_file ON ".$TBLPREFIX."sources (s_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_sources"]."<br />\n";
		}
		else {
			print $pgv_lang["created_sources_fail"]."<br />\n";
			exit;
		}

		$sql = "CREATE TABLE ".$TBLPREFIX."other (o_id VARCHAR(30), o_file VARCHAR(255), o_type VARCHAR(20), o_gedcom TEXT)";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX other_id ON ".$TBLPREFIX."other (o_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX other_file ON ".$TBLPREFIX."other (o_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_other"]."<br />\n";
		}
		else {
			print $pgv_lang["created_other_fail"]."<br />\n";
			exit;
		}

		$sql = "CREATE TABLE ".$TBLPREFIX."places (p_id INT NOT NULL, p_place VARCHAR(150), p_level INT, p_parent_id INT, p_file VARCHAR(255), PRIMARY KEY(p_id))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX place_place ON ".$TBLPREFIX."places (p_place)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_level ON ".$TBLPREFIX."places (p_level)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_parent ON ".$TBLPREFIX."places (p_parent_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_file ON ".$TBLPREFIX."places (p_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_places"]."<br />\n";
		}
		else {
			print $pgv_lang["created_places_fail"]."<br />\n";
			exit;
		}
		$sql = "CREATE TABLE ".$TBLPREFIX."placelinks (pl_p_id INT, pl_gid VARCHAR(30), pl_file VARCHAR(255))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX plindex_place ON ".$TBLPREFIX."placelinks (pl_p_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX plindex_gid ON ".$TBLPREFIX."placelinks (pl_gid)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX plindex_file ON ".$TBLPREFIX."placelinks (pl_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_places"]."<br />\n";
		}
		else {
			print $pgv_lang["created_places_fail"]."<br />\n";
			exit;
		}
	}
	else if (!$has_places) {
		$sql = "CREATE TABLE ".$TBLPREFIX."places (p_id INT NOT NULL, p_place VARCHAR(150), p_level INT, p_parent_id INT, p_file VARCHAR(255), PRIMARY KEY(p_id))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX place_place ON ".$TBLPREFIX."places (p_place)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_level ON ".$TBLPREFIX."places (p_level)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_parent ON ".$TBLPREFIX."places (p_parent_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX place_file ON ".$TBLPREFIX."places (p_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_places"]."<br />\n";
		}
		else {
			print $pgv_lang["created_places_fail"]."<br />\n";
			exit;
		}
	}
	else if ($has_place_gid) {
		$sql = "ALTER TABLE ".$TBLPREFIX."places DROP COLUMN p_gid";
		$res =& dbquery($sql);
	}
	else if (!$has_placelinks) {
		$sql = "CREATE TABLE ".$TBLPREFIX."placelinks (pl_p_id INT, pl_gid VARCHAR(30), pl_file VARCHAR(255))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX plindex_place ON ".$TBLPREFIX."placelinks (pl_p_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX plindex_gid ON ".$TBLPREFIX."placelinks (pl_gid)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX plindex_file ON ".$TBLPREFIX."placelinks (pl_file)";
			$res =& dbquery($sql);
			print $pgv_lang["created_places"]."<br />\n";
		}
		else {
			print $pgv_lang["created_places_fail"]."<br />\n";
			exit;
		}
	}
	if (!$has_names) {
		$sql = "CREATE TABLE ".$TBLPREFIX."names (n_gid VARCHAR(30), n_file VARCHAR(255), n_name VARCHAR(255), n_letter VARCHAR(5), n_surname VARCHAR(100), n_type VARCHAR(10))";
		//if (preg_match("/mysql/", $DBTYPE)>0) $sql .= " TYPE=INNODB";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX name_gid ON ".$TBLPREFIX."names (n_gid)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX name_name ON ".$TBLPREFIX."names (n_name)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX name_letter ON ".$TBLPREFIX."names (n_letter)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX name_type ON ".$TBLPREFIX."names (n_type)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX name_surn ON ".$TBLPREFIX."names (n_surname)";
			$res =& dbquery($sql);
		}
		if (!$has_surname) {
			$sql = "ALTER TABLE ".$TBLPREFIX."individuals ADD COLUMN i_surname VARCHAR(100)";
			$res =& dbquery($sql);
		}
	}
	else if (!$has_surname) {
		$sql = "ALTER TABLE ".$TBLPREFIX."individuals ADD COLUMN i_surname VARCHAR(100)";
		$res =& dbquery($sql);
		$sql = "CREATE INDEX indi_surn ON ".$TBLPREFIX."individuals (i_surname)";
		$res =& dbquery($sql);
		if ($has_names) {
			$sql = "ALTER TABLE ".$TBLPREFIX."names ADD COLUMN n_surname VARCHAR(100), n_type VARCHAR(10)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX name_surn ON ".$TBLPREFIX."names (n_surname)";
			$res =& dbquery($sql);
		}
	}
	/*
	if (!$has_research) {
		$sql = "CREATE TABLE ".$TBLPREFIX."researchlog (r_id VARCHAR(30), r_file VARCHAR(255), r_sid VARCHAR(30), r_gedcom TEXT)";
		$res =& dbquery($sql);
		if(!DB::isError($res)) {
			$sql = "CREATE INDEX rlog_id ON ".$TBLPREFIX."researchlog (r_id)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX rlog_file ON ".$TBLPREFIX."researchlog (r_file)";
			$res =& dbquery($sql);
			$sql = "CREATE INDEX rlog_sid ON ".$TBLPREFIX."researchlog (r_sid)";
			$res =& dbquery($sql);
		}
	}*/

	/*-- commenting out as it seems to cause more problems than it helps
	$sql = "LOCK TABLE ".$TBLPREFIX."individuals WRITE, ".$TBLPREFIX."families WRITE, ".$TBLPREFIX."sources WRITE, ".$TBLPREFIX."other WRITE, ".$TBLPREFIX."places WRITE, ".$TBLPREFIX."users WRITE";
	$res =& dbquery($sql);
	*/
	if (preg_match("/mysql|pgsql/", $DBTYPE)>0) $DBCONN->autoCommit(false);
	//-- start a transaction
	$sql = "BEGIN";
	$res =& dbquery($sql);
}

/**
 * delete a gedcom from the database
 *
 * deletes all of the imported data about a gedcom from the database
 * @param string $FILE	the gedcom to remove from the database
 */
function empty_database($FILE) {
	global $TBLPREFIX, $DBCONN;

	$FILE = $DBCONN->escapeSimple($FILE);
	$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_file='$FILE'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_file='$FILE'";
	$res =& dbquery($sql);
}

/**
 * perform any database cleanup
 *
 * during the import process it might be necessary to cleanup some database values.  In index mode
 * the file handles need to be closed.  For database mode we probably don't need to do anything in
 * this funciton.
 */
function cleanup_database() {
	global $DBTYPE, $DBCONN;
	/*-- commenting out as it seems to cause more problems than it helps
	$sql = "UNLOCK TABLES";
	$res =& dbquery($sql);
	*/
	//-- end the transaction
	$sql = "COMMIT";
	$res =& dbquery($sql);
	if (preg_match("/mysql|pgsql/", $DBTYPE)>0) $DBCONN->autoCommit(false);
	RETURN;
}

/**
 * get a list of all the source titles
 *
 * returns an array of all of the sourcetitles in the database.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
 * @return array the array of source-titles
 */
function get_source_add_title_list() {
	global $sourcelist, $GEDCOM;
	global $TBLPREFIX, $DBCONN;

	$sourcelist = array();

 	$sql = "SELECT s_id, s_file, s_file as s_name, s_gedcom FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOM)."' and ((s_gedcom LIKE '% _HEB %') || (s_gedcom LIKE '% ROMN %'));";

	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["s_gedcom"];
		$row = db_cleanup($row);
		$ct = preg_match("/\d ROMN (.*)/", $row["s_gedcom"], $match);
 		if ($ct==0) $ct = preg_match("/\d _HEB (.*)/", $row["s_gedcom"], $match);
		$source["name"] = $match[1];
		$source["file"] = $row["s_file"];
		$sourcelist[$row["s_id"]] = $source;
	}
	$res->free();

	return $sourcelist;
}

/**
 * get a list of all the sources
 *
 * returns an array of all of the sources in the database.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
 * @return array the array of sources
 */
function get_source_list() {
	global $sourcelist, $GEDCOM;
	global $TBLPREFIX, $DBCONN;

	$sourcelist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY s_name";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["s_gedcom"];
		$row = db_cleanup($row);
		$source["name"] = $row["s_name"];
		$source["file"] = $row["s_file"];
//		$source["nr"] = 0;
		$sourcelist[$row["s_id"]] = $source;
	}
	$res->free();

	return $sourcelist;
}

//-- get the repositorylist from the datastore
function get_repo_list() {
	global $repolist, $GEDCOM;
	global $TBLPREFIX, $DBCONN;

	$repolist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOM)."' AND o_type='REPO'";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$tt = preg_match("/1 NAME (.*)/", $row["o_gedcom"], $match);
		if ($tt == "0") $name = $row["o_id"]; else $name = $match[1];
		$repo["id"] = "@".$row["o_id"]."@";
		$repo["file"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repo["gedcom"] = $row["o_gedcom"];
		$row = db_cleanup($row);
		$repolist[$name]= $repo;
	}
	$res->free();
	ksort($repolist);
	return $repolist;
}

//-- get the repositorylist from the datastore
function get_repo_id_list() {
	global $GEDCOM;
	global $TBLPREFIX, $DBCONN;

	$repo_id_list = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOM)."' AND o_type='REPO' ORDER BY o_id";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$tt = preg_match("/1 NAME (.*)/", $row["o_gedcom"], $match);
		if ($tt>0) $repo["name"] = $match[1];
		else $repo["name"] = "";
		$repo["file"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repo["gedcom"] = $row["o_gedcom"];
		$row = db_cleanup($row);
		$repo_id_list[$row["o_id"]] = $repo;
	}
	$res->free();
	return $repo_id_list;
}


//-- get the indilist from the datastore
function get_indi_list() {
	global $indilist, $GEDCOM, $DBCONN;
	global $TBLPREFIX, $INDILIST_RETRIEVED;

	if ($INDILIST_RETRIEVED) return $indilist;
	$indilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY i_surname";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$indi = array();
		$indi["gedcom"] = $row["i_gedcom"];
		$row = db_cleanup($row);
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "A"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["file"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
	}
	$res->free();

	$sql = "SELECT * FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY n_surname";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (isset($indilist[$row["n_gid"]])) {
			$indilist[$row["n_gid"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
		}
	}
	$res->free();
	$INDILIST_RETRIEVED = true;
	return $indilist;
}

//-- get the famlist from the datastore
function get_fam_list() {
	global $famlist, $GEDCOM, $indilist, $DBCONN;
	global $TBLPREFIX, $FAMLIST_RETRIEVED;

	if ($FAMLIST_RETRIEVED) return $famlist;
	$famlist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$fam = array();
		$fam["gedcom"] = $row["f_gedcom"];
		$row = db_cleanup($row);
		$hname = get_sortable_name($row["f_husb"]);
		$wname = get_sortable_name($row["f_wife"]);
		$name = "";
		if (!empty($hname)) $name = $hname;
		else $name = "@N.N., @P.N.";

		if (!empty($wname)) $name .= " + ".$wname;
		else $name .= " + @N.N., @P.N.";

		$fam["name"] = $name;
		$fam["HUSB"] = $row["f_husb"];
		$fam["WIFE"] = $row["f_wife"];
		$fam["CHIL"] = $row["f_chil"];
		$fam["file"] = $row["f_file"];
		$famlist[$row["f_id"]] = $fam;
	}
	$res->free();
	$FAMLIST_RETRIEVED = true;
	return $famlist;
}

//-- get the otherlist from the datastore
function get_other_list() {
	global $otherlist, $GEDCOM, $DBCONN;
	global $TBLPREFIX;

	$otherlist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["o_gedcom"];
		$row = db_cleanup($row);
		$source["type"] = $row["o_type"];
		$source["file"] = $row["o_file"];
		$otherlist[$row["o_id"]]= $source;
	}
	$res->free();
	return $otherlist;
}

//-- search through the gedcom records for individuals
/**
 * Search the database for individuals that match the query
 *
 * uses a regular expression to search the gedcom records of all individuals and returns an
 * array list of the matching individuals
 *
 * @author	yalnifj
 * @param	string $query a regular expression query to search for
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myindilist array with all individuals that matched the query
 */
function search_indis($query, $allgeds=false, $ANDOR="AND") {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB;
	$myindilist = array();
	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";
	if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (i_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR i_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR i_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(i_gedcom $term '".$DBCONN->escapeSimple(str2upper($q))."' OR i_gedcom $term '".$DBCONN->escapeSimple(str2lower($q))."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "i_file='".$DBCONN->escapeSimple($allgeds[$i])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()){
			$row = db_cleanup($row);
			if ($allgeds) {
				$myindilist[$row[0]."[".$row[2]."]"]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]."[".$row[2]."]"]["file"] = $row[2];
				$myindilist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
				$myindilist[$row[0]."[".$row[2]."]"]["isdead"] = $row[4];
				if ($myindilist[$row[0]."[".$row[2]."]"]["file"] == $GEDCOM) $indilist[$row[0]] = $myindilist[$row[0]."[".$row[2]."]"];
			}
			else {
				$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]]["file"] = $row[2];
				$myindilist[$row[0]]["gedcom"] = $row[3];
				$myindilist[$row[0]]["isdead"] = $row[4];
				if ($myindilist[$row[0]]["file"] == $GEDCOM) $indilist[$row[0]] = $myindilist[$row[0]];
			}
		}
		$res->free();
	}
	return $myindilist;
}

//-- search through the gedcom records for individuals in families
function search_indis_fam($add2myindilist) {
	global $TBLPREFIX, $GEDCOM, $indilist, $myindilist;

	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		if (isset($add2myindilist[$row[0]])){
			$add2my_fam=$add2myindilist[$row[0]];
			$row = db_cleanup($row);
			$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
			$myindilist[$row[0]]["file"] = $row[2];
			$myindilist[$row[0]]["gedcom"] = $row[3].$add2my_fam;
			$myindilist[$row[0]]["isdead"] = $row[4];
			$indilist[$row[0]] = $myindilist[$row[0]];
		}
	}
	$res->free();
	return $myindilist;
}

function search_indis_year_range($startyear, $endyear, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB;

	$myindilist = array();
	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "i_gedcom REGEXP '".$DBCONN->escapeSimple("2 DATE[^\n]* ".$i)."'";
		else $sql .= "i_gedcom LIKE '".$DBCONN->escapeSimple("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
		$myindilist[$row[0]]["file"] = $row[2];
		$myindilist[$row[0]]["gedcom"] = $row[3];
		$myindilist[$row[0]]["isdead"] = $row[4];
		$indilist[$row[0]] = $myindilist[$row[0]];
	}
	$res->free();
	return $myindilist;
}


//-- search through the gedcom records for individuals
function search_indis_names($query, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB;

	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";

	$myindilist = array();
	if (empty($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals";
	else if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE i_name $term '".$DBCONN->escapeSimple($query)."'";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if (!empty($q)) {
				if ($i>0) $sql .= " AND ";
				$sql .= "i_name $term '".$DBCONN->escapeSimple($q)."'";
				$i++;
			}
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
		$myindilist[$row[0]]["file"] = $row[2];
		$myindilist[$row[0]]["gedcom"] = $row[3];
		$myindilist[$row[0]]["isdead"] = $row[4];
		$indilist[$row[0]] = $myindilist[$row[0]];
	}
	$res->free();
	return $myindilist;
}

//-- search through the gedcom records for families
function search_fams($query, $allgeds=false, $ANDOR="AND") {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB;
	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";
	$myfamlist = array();
	if (!is_array($query)) $sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (f_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR f_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR f_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	else {
		$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(f_gedcom $term '".$DBCONN->escapeSimple(str2upper($q))."' OR f_gedcom $term '".$DBCONN->escapeSimple(str2lower($q))."')";
			$i++;
		}
		$sql .= ")";
	}
	
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "f_file='".$DBCONN->escapeSimple($allgeds[$i])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res =& dbquery($sql);
	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$GEDCOM = $row[3];
		$hname = get_sortable_name($row[1]);
		$wname = get_sortable_name($row[2]);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		if ($allgeds) {
			$myfamlist[$row[0]."[".$row[3]."]"]["name"] = $name;
			$myfamlist[$row[0]."[".$row[3]."]"]["file"] = $row[3];
			$myfamlist[$row[0]."[".$row[3]."]"]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]."[".$row[3]."]"];
		}
		else {
			$myfamlist[$row[0]]["name"] = $name;
			$myfamlist[$row[0]]["file"] = $row[3];
			$myfamlist[$row[0]]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myfamlist;
}


//-- search through the gedcom records for families with daterange
function search_fams_year_range($startyear, $endyear, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB;

	$myfamlist = array();
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (";
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "f_gedcom REGEXP '".$DBCONN->escapeSimple("2 DATE[^\n]* ".$i)."'";
		else $sql .= "f_gedcom LIKE '".$DBCONN->escapeSimple("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$hname = get_sortable_name($row[1]);
		$wname = get_sortable_name($row[2]);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		$myfamlist[$row[0]]["name"] = $name;
		$myfamlist[$row[0]]["file"] = $row[3];
		$myfamlist[$row[0]]["gedcom"] = $row[4];
		$famlist[$row[0]] = $myfamlist[$row[0]];
	}
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for sources
function search_sources($query, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $REGEXP_DB;
	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";
	$mysourcelist = array();
	$sql = "SELECT s_id, s_name, s_file, s_gedcom FROM ".$TBLPREFIX."sources WHERE (s_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR s_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR s_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	if (!$allgeds) $sql .= " AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "s_file='".$DBCONN->escapeSimple($allgeds[$i])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if ($allgeds) {
			$mysourcelist[$row[0]."[".$row[2]."]"]["name"] = $row[1];
			$mysourcelist[$row[0]."[".$row[2]."]"]["file"] = $row[2];
			$mysourcelist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
		}
		else {
			$mysourcelist[$row[0]]["name"] = $row[1];
			$mysourcelist[$row[0]]["file"] = $row[2];
			$mysourcelist[$row[0]]["gedcom"] = $row[3];
		}
	}
	$res->free();
	return $mysourcelist;
}

//-- search through the gedcom records for sources
function search_repos($query, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $REGEXP_DB;
	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";
	$myrepolist = array();
	$sql = "SELECT o_id, o_file, o_gedcom FROM ".$TBLPREFIX."other WHERE o_type='REPO' AND (o_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR o_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR o_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	if (!$allgeds) $sql .= " AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$tt = preg_match("/1 NAME (.*)/", $row[2], $match);
		if ($tt == "0") $name = $row[0]; else $name = $match[1];
		if ($allgeds) {
			$myrepolist[$row[0]."[".$row[1]."]"]["name"] = $name;
			$myrepolist[$row[0]."[".$row[1]."]"]["file"] = $row[1];
			$myrepolist[$row[0]."[".$row[1]."]"]["gedcom"] = $row[2];
		}
		else {
			$myrepolist[$row[0]]["name"] = $name;
			$myrepolist[$row[0]]["file"] = $row[1];
			$myrepolist[$row[0]]["gedcom"] = $row[2];
		}
	}
	$res->free();
	return $myrepolist;
}

/**
 * get place parent ID
 * @param array $parent
 * @param int $level
 * @return int
 */
function get_place_parent_id($parent, $level) {
	global $DBCONN, $TBLPREFIX, $GEDCOM;

	$parent_id=0;
	for($i=0; $i<$level; $i++) {
		$escparent=preg_replace("/\?/","\\\\\\?", $DBCONN->escapeSimple($parent[$i]));
		$psql = "SELECT p_id FROM ".$TBLPREFIX."places WHERE p_level=".$i." AND p_parent_id=$parent_id AND p_place LIKE '".$escparent."' AND p_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY p_place";
		$res = dbquery($psql);
		$row =& $res->fetchRow();
		$res->free();
		if (empty($row[0])) break;
		$parent_id = $row[0];
	}
	return $parent_id;
}

/**
 * find all of the places in the hierarchy
 * The $parent array holds the parent hierarchy of the places
 * we want to get.  The level holds the level in the hierarchy that
 * we are at.
 */
function get_place_list() {
	global $numfound, $j, $level, $parent, $found;
	global $GEDCOM, $TBLPREFIX, $placelist, $positions, $DBCONN;

	// --- find all of the place in the file
	if ($level==0) $sql = "SELECT p_place FROM ".$TBLPREFIX."places WHERE p_level=0 AND p_file='$GEDCOM' ORDER BY p_place";
	else {
		$parent_id = get_place_parent_id($parent, $level);
		$sql = "SELECT p_place FROM ".$TBLPREFIX."places WHERE p_level=$level AND p_parent_id=$parent_id AND p_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY p_place";
	}
	$res =& dbquery($sql);
	while ($row =& $res->fetchRow()) {
		$placelist[] = $row[0];
		$numfound++;
	}
	$res->free();
}

/**
 * get all of the place connections
 * @param array $parent
 * @param int $level
 * @return array
 */
function get_place_positions($parent, $level) {
	global $positions, $TBLPREFIX, $GEDCOM, $DBCONN;

	$p_id = get_place_parent_id($parent, $level);
	$sql = "SELECT DISTINCT pl_gid FROM ".$TBLPREFIX."placelinks WHERE pl_p_id=$p_id AND pl_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while ($row =& $res->fetchRow()) {
		$positions[] = $row[0];
	}
	return $positions;
}

function search_places($sql, $splace) {
	global $placelist;

	$res =& dbquery($sql);
	$k=0;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		print " ";
		if ($k%4000 == 0) print "\n";
		// -- put all the places into an array
		if (empty($splace)) $ct = preg_match_all("/\d PLAC (.*)/", $row[1], $match, PREG_SET_ORDER);
		else $ct = preg_match_all("/\d PLAC (.*$splace.*)/i", $row[1], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$place = $match[$i][1];
			$place=trim($place);

			$place=preg_replace("/[\.\"\><]/", "", $place);
			$levels = preg_split ("/,/", $place);		// -- split the place into comma seperated values
			$levels = array_reverse($levels);				// -- reverse the array so that we get the top level first
			$placetext="";
			$j=0;
			foreach($levels as $indexval => $level) {
				if ($j>0) $placetext .= ", ";
				$placetext .= trim($level);
				$j++;
			}
			$placelist[] = $placetext;
			$k++;
		}//--end for
	}//-- end while
	$res->free();
}

//-- find all of the places
function find_place_list($place) {
	global $GEDCOM, $TBLPREFIX, $placelist, $indilist, $famlist, $sourcelist, $otherlist, $DBCONN;
/*
	// --- find all of the place in the file
	$sql = "SELECT i_id, i_gedcom FROM ".$TBLPREFIX."individuals WHERE i_gedcom LIKE '% PLAC %' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT f_id, f_gedcom FROM ".$TBLPREFIX."families WHERE f_gedcom LIKE '% PLAC %' AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT s_id, s_gedcom FROM ".$TBLPREFIX."sources WHERE s_gedcom LIKE '% PLAC %' AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT o_id, o_gedcom FROM ".$TBLPREFIX."other WHERE o_gedcom LIKE '% PLAC %' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
*/
	$sql = "SELECT p_id, p_place, p_parent_id  FROM ".$TBLPREFIX."places WHERE p_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY p_parent_id, p_id";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()) {
		if ($row[2]==0) $placelist[$row[0]] = $row[1];
		else {
			$placelist[$row[0]] = $placelist[$row[2]].", ".$row[1];
		}
	}
	if (!empty($place)) {
		$found = array();
		foreach($placelist as $indexval => $pplace) {
			if (preg_match("/$place/i", $pplace)>0) {
				$upperplace = str2upper($pplace);
				if (!isset($found[$upperplace])) {
					$found[$upperplace] = $pplace;
				}
			}
		}
		$placelist = array_values($found);
	}
}

function find_media($sql, $type) {
	global $ct, $medialist, $MEDIA_DIRECTORY, $foundlist, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$res =& dbquery($sql);
	while($row =& $res->fetchRow()){
		print " ";
		find_media_in_record($row[0]);
	}
	$res->free();
}

//-- find all of the media
function get_media_list() {
	global $GEDCOM, $TBLPREFIX, $medialist, $ct, $DBCONN;

	$ct=0;
	$sql = "SELECT i_gedcom, i_id FROM ".$TBLPREFIX."individuals WHERE i_gedcom LIKE '% OBJE%' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	find_media($sql, 'INDI');
	$sql = "SELECT f_gedcom, f_id FROM ".$TBLPREFIX."families WHERE f_gedcom LIKE '% OBJE%' AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	find_media($sql, 'FAM');
	$sql = "SELECT s_gedcom, s_id FROM ".$TBLPREFIX."sources WHERE s_gedcom LIKE '% OBJE%' AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	find_media($sql, 'SOUR');
	$sql = "SELECT o_gedcom, o_id FROM ".$TBLPREFIX."other WHERE o_gedcom LIKE '% OBJE%' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	find_media($sql, 'OTHER');

}

/**
 * get all first letters of individual's last names
 * @see indilist.php
 * @return array	an array of all letters
 */
function get_indi_alpha() {
	global $CHARACTER_SET, $TBLPREFIX, $GEDCOM, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN;
	$indialpha = array();
	$sql = "SELECT DISTINCT i_letter as alpha FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY alpha";
	$res =& dbquery($sql);

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($indialpha[$letter])) $indialpha[$letter]=$letter;
	}
	$res->free();

	$sql = "SELECT DISTINCT n_letter as alpha FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	if (!$SHOW_MARRIED_NAMES) $sql .= " AND n_type!='C'";
	$sql .= " ORDER BY alpha";
	$res =& dbquery($sql);

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($indialpha[$letter])) $indialpha[$letter]=$letter;
	}
	$res->free();
	return $indialpha;
}

//-- get the first character in the list
function get_fam_alpha() {
	global $CHARACTER_SET, $TBLPREFIX, $GEDCOM, $LANGUAGE, $famalpha, $DBCONN;

	$famalpha = array();
	$sql = "SELECT DISTINCT i_letter as alpha FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOM)."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY alpha";
	$res =& dbquery($sql);

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($famalpha[$letter])) $famalpha[$letter]=$letter;
	}
	$res->free();
	$sql = "SELECT DISTINCT n_letter as alpha FROM ".$TBLPREFIX."names, ".$TBLPREFIX."individuals WHERE i_file=n_file AND i_id=n_gid AND n_file='".$DBCONN->escapeSimple($GEDCOM)."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY alpha";
	$res =& dbquery($sql);

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($famalpha[$letter])) $famalpha[$letter]=$letter;
	}
	$res->free();
	$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOM)."' AND (f_husb='' || f_wife='')";
	$res =& dbquery($sql);
	if ($res->numRows()>0) {
		$famalpha["@"] = "@";
	}
	$res->free();
	return $famalpha;
}

/**
 * Get Individuals Starting with a letter
 *
 * This function finds all of the individuals who start with the given letter
 * @param string $letter	The letter to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_alpha_indis($letter) {
	global $TBLPREFIX, $GEDCOM, $LANGUAGE, $indilist, $surname, $SHOW_MARRIED_NAMES, $DBCONN;

	$tindilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE ";
	if ($LANGUAGE == "hungarian"){
		if (strlen($letter) >= 2) $sql .= "i_letter = '".$DBCONN->escapeSimple($letter)."' ";
		else {
			if ($letter == "C") $text = "CS";
			else if ($letter == "D") $text = "DZ";
			else if ($letter == "G") $text = "GY";
			else if ($letter == "L") $text = "LY";
			else if ($letter == "N") $text = "NY";
			else if ($letter == "S") $text = "SZ";
			else if ($letter == "T") $text = "TY";
			else if ($letter == "Z") $text = "ZS";
			if (isset($text)) $sql .= "(i_letter = '".$DBCONN->escapeSimple($letter)."' AND i_letter != '".$DBCONN->escapeSimple($text)."') ";
			else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
		}
	}
	else if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(i_letter = '".$DBCONN->escapeSimple($letter)."' OR i_letter = '".$DBCONN->escapeSimple($text)."') ";
		else if ($letter=="A") $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."' ";
		else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
	}
	else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%'";
	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND i_surname LIKE '%".$DBCONN->escapeSimple($surname)."%' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY i_name";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (substr($row["i_letter"], 0, 1)==substr($letter, 0, 1)||(isset($text)?substr($row["i_letter"], 0, 1)==substr($text, 0, 1):FALSE)){
			$indi = array();
			$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], 'P'));
			$indi["isdead"] = $row["i_isdead"];
			$indi["gedcom"] = $row["i_gedcom"];
			$indi["file"] = $row["i_file"];
			$tindilist[$row["i_id"]] = $indi;
			//-- cache the item in the $indilist for improved speed
			$indilist[$row["i_id"]] = $indi;
		}
	}
	$res->free();

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND ";
	if ($LANGUAGE == "hungarian"){
		if (strlen($letter) >= 2) $sql .= "n_letter = '".$DBCONN->escapeSimple($letter)."' ";
		else {
			if ($letter == "C") $text = "CS";
			else if ($letter == "D") $text = "DZ";
			else if ($letter == "G") $text = "GY";
			else if ($letter == "L") $text = "LY";
			else if ($letter == "N") $text = "NY";
			else if ($letter == "S") $text = "SZ";
			else if ($letter == "T") $text = "TY";
			else if ($letter == "Z") $text = "ZS";
			if (isset($text)) $sql .= "(n_letter = '".$DBCONN->escapeSimple($letter)."' AND n_letter != '".$DBCONN->escapeSimple($text)."') ";
			else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
		}
	}
	else if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(n_letter = '".$DBCONN->escapeSimple($letter)."' OR n_letter = '".$DBCONN->escapeSimple($text)."') ";
		else if ($letter=="A") $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."' ";
		else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
	}
	else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%'";
	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND n_surname LIKE '%".$DBCONN->escapeSimple($surname)."%' ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY i_name";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (substr($row["n_letter"], 0, 1)==substr($letter, 0, 1)||(isset($text)?substr($row["n_letter"], 0, 1)==substr($text, 0, 1):FALSE)){
			if (!isset($indilist[$row["i_id"]])) {
				$indi = array();
				$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"), array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]));
				$indi["isdead"] = $row["i_isdead"];
				$indi["gedcom"] = $row["i_gedcom"];
				$indi["file"] = $row["i_file"];
				//-- cache the item in the $indilist for improved speed
				$indilist[$row["i_id"]] = $indi;
				$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
			}
			else {
				$indilist[$row["i_id"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
				$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
			}
		}
	}
	$res->free();

	return $tindilist;
}

/**
 * Get Individuals with a given surname
 *
 * This function finds all of the individuals who have the given surname
 * @param string $surname	The surname to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_surname_indis($surname) {
	global $TBLPREFIX, $GEDCOM, $LANGUAGE, $indilist, $SHOW_MARRIED_NAMES, $DBCONN;

	$tindilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE i_surname LIKE '".$DBCONN->escapeSimple($surname)."' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$indi = array();
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["gedcom"] = $row["i_gedcom"];
		$indi["file"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
		$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
	}
	$res->free();

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND n_surname LIKE '".$DBCONN->escapeSimple($surname)."' ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOM)."' ORDER BY n_surname";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (isset($indilist[$row["i_id"]])) {
			$indilist[$row["i_id"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
			$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
		}
		else {
			$indi = array();
			$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"), array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]));
			$indi["isdead"] = $row["i_isdead"];
			$indi["gedcom"] = $row["i_gedcom"];
			$indi["file"] = $row["i_file"];
			$indilist[$row["i_id"]] = $indi;
			$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
		}
	}
	$res->free();
	return $tindilist;
}

/**
 * Get Families Starting with a letter
 *
 * This function finds all of the families who start with the given letter
 * @param string $letter	The letter to search on
 * @return array	$indilist array
 * @see get_alpha_indis()
 * @see http://www.phpgedview.net/devdocs/arrays.php#famlist
 */
function get_alpha_fams($letter) {
	global $TBLPREFIX, $GEDCOM, $famlist, $indilist, $pgv_lang, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN;
	$tfamlist = array();
	$temp = $SHOW_MARRIED_NAMES;
	$SHOW_MARRIED_NAMES = false;
	$myindilist = get_alpha_indis($letter);
	$SHOW_MARRIED_NAMES = $temp;
	//-- escaped letter for regular expressions
	if ($letter=="(" || $letter=="[" || $letter=="?" || $letter=="/" || $letter=="*" || $letter=="+") $letter = "\\".$letter;

	foreach($myindilist as $gid=>$indi) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		$surnames = array();
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["husb"]==$gid) {
				$HUSB = $famlist[$famid]["husb"];
				$WIFE = $famlist[$famid]["wife"];
			}
			else {
				$HUSB = $famlist[$famid]["wife"];
				$WIFE = $famlist[$famid]["husb"];
			}
			$hname="";
			$surnames = array();
			foreach($indi["names"] as $indexval => $namearray) {
				//-- don't use married names in the family list
				if ($namearray[3]!='C') {
					$text = "";
					if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
						if ($letter == "Ø") $text = "OE";
						else if ($letter == "Æ") $text = "AE";
						else if ($letter == "Å") $text = "AA";
					}
					if ((preg_match("/^$letter/", $namearray[1])>0)||(!empty($text)&&preg_match("/^$text/", $namearray[1])>0)) {
						$surnames[str2upper($namearray[2])] = $namearray[2];
						$hname = sortable_name_from_name($namearray[0]);
					}
				}
			}
			if (!empty($hname)) {
				$wname = get_sortable_name($WIFE);
				if (hasRTLText($hname)) {
					$indirec = find_person_record($WIFE);
					if (isset($indilist[$WIFE])) {
						foreach($indilist[$WIFE]["names"] as $n=>$namearray) {
							if (hasRTLText($namearray[0])) {
								$wname = sortable_name_from_name($namearray[0]);
								break;
							}
						}
					}
				}
				$name = $hname ." + ". $wname;
				$famlist[$famid]["name"] = $name;
				if (!isset($famlist[$famid]["surnames"])||count($famlist[$famid]["surnames"])==0) $famlist[$famid]["surnames"] = $surnames;
				else array_merge($famlist[$famid]["surnames"], $surnames);
				$tfamlist[$famid] = $famlist[$famid];
			}
		}
	}

	//-- handle the special case for @N.N. when families don't have any husb or wife
	//-- SHOULD WE SHOW THE UNDEFINED? MA
	if ($letter=="@") {
		$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
		$res =& dbquery($sql);
		if ($res->numRows()>0) {
			while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				$fam = array();
				$hname = get_sortable_name($row["f_husb"]);
				$wname = get_sortable_name($row["f_wife"]);
				if (!empty($hname)) $name = $hname;
				else $name = "@N.N., @P.N.";
				if (!empty($wname)) $name .= " + ".$wname;
				else $name .= " + @N.N., @P.N.";
				$fam["name"] = $name;
				$fam["HUSB"] = $row["f_husb"];
				$fam["WIFE"] = $row["f_wife"];
				$fam["CHIL"] = $row["f_chil"];
				$fam["gedcom"] = $row["f_gedcom"];
				$fam["file"] = $row["f_file"];
				$fam["surnames"] = array("@N.N.");
				$tfamlist[$row["f_id"]] = $fam;
				//-- cache the items in the lists for improved speed
				$famlist[$row["f_id"]] = $fam;
			}
		}
		$res->free();
	}
	return $tfamlist;
}

/**
 * Get Families with a given surname
 *
 * This function finds all of the individuals who have the given surname
 * @param string $surname	The surname to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_surname_fams($surname) {
	global $TBLPREFIX, $GEDCOM, $famlist, $indilist, $pgv_lang, $DBCONN, $SHOW_MARRIED_NAMES;
	$tfamlist = array();
	$temp = $SHOW_MARRIED_NAMES;
	$SHOW_MARRIED_NAMES = false;
	$myindilist = get_surname_indis($surname);
	$SHOW_MARRIED_NAMES = $temp;
	foreach($myindilist as $gid=>$indi) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["husb"]==$gid) {
				$HUSB = $famlist[$famid]["husb"];
				$WIFE = $famlist[$famid]["wife"];
			}
			else {
				$HUSB = $famlist[$famid]["wife"];
				$WIFE = $famlist[$famid]["husb"];
			}
			$hname = "";
			foreach($indi["names"] as $indexval => $namearray) {
				if (stristr($namearray[2], $surname)!==false) $hname = sortable_name_from_name($namearray[0]);
			}
			if (!empty($hname)) {
				$wname = get_sortable_name($WIFE);
				if (hasRTLText($hname)) {
					$indirec = find_person_record($WIFE);
					if (isset($indilist[$WIFE])) {
						foreach($indilist[$WIFE]["names"] as $n=>$namearray) {
							if (hasRTLText($namearray[0])) {
								$wname = sortable_name_from_name($namearray[0]);
								break;
							}
						}
					}
				}
				$name = $hname ." + ". $wname;
				$famlist[$famid]["name"] = $name;
				$tfamlist[$famid] = $famlist[$famid];
			}
		}
	}

	//-- handle the special case for @N.N. when families don't have any husb or wife
	//-- SHOULD WE SHOW THE UNDEFINED? MA
	if ($surname=="@N.N.") {
		$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
		$res =& dbquery($sql);
		if ($res->numRows()>0) {
			while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				$fam = array();
				$hname = get_sortable_name($row["f_husb"]);
				$wname = get_sortable_name($row["f_wife"]);
				if (empty($hname)) $hname = "@N.N., @P.N.";
				if (empty($wname)) $wname = "@N.N., @P.N.";
				if (empty($row["f_husb"])) $name = $hname." + ".$wname;
				else $name = $wname." + ".$hname;
				$fam["name"] = $name;
				$fam["HUSB"] = $row["f_husb"];
				$fam["WIFE"] = $row["f_wife"];
				$fam["CHIL"] = $row["f_chil"];
				$fam["gedcom"] = $row["f_gedcom"];
				$fam["file"] = $row["f_file"];
				$tfamlist[$row["f_id"]] = $fam;
				//-- cache the items in the lists for improved speed
				$famlist[$row["f_id"]] = $fam;
			}
		}
		$res->free();
	}
	return $tfamlist;
}

//-- function to find the gedcom id for the given rin
function find_rin_id($rin) {
	global $TBLPREFIX, $GEDCOM, $DBCONN;

	$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_rin='$rin' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		return $row["i_id"];
	}
	return $rin;
}

function delete_gedcom($ged) {
	global $INDEX_DIRECTORY, $TBLPREFIX, $pgv_changes, $DBCONN;

	@unlink($INDEX_DIRECTORY.$ged."_conf.php");
	$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."favorites WHERE fv_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."news WHERE n_username='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."blocks WHERE b_username='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_file='".$DBCONN->escapeSimple($ged)."'";
	$res =& dbquery($sql);
	

	if (isset($pgv_changes)) {
		//-- erase any of the changes
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) unset($pgv_changes[$cid]);
		}
		write_changes();
	}
}

//-- return the current size of the given list
//- list options are indilist famlist sourcelist and otherlist
function get_list_size($list) {
	global $TBLPREFIX, $GEDCOM, $DBCONN;

	switch($list) {
		case "indilist":
			$sql = "SELECT count(i_id) FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "famlist":
			$sql = "SELECT count(f_id) FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "sourcelist":
			$sql = "SELECT count(s_id) FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "otherlist":
			$sql = "SELECT count(o_id) FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			while($row =& $res->fetchRow()) return $row[0];
		break;
	}
	return 0;
}

/**
 * Accpet changed gedcom record into database
 *
 * This function gets an updated record from the gedcom file and replaces it in the database
 * @author John Finlay
 * @param string $cid The change id of the record to accept
 */
function accept_changes($cid) {
	global $pgv_changes, $GEDCOM, $TBLPREFIX, $FILE, $DBCONN, $MULTI_MEDIA_DB;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes)-1];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}
		$FILE = $GEDCOM;
		$gid = $change["gid"];
		$indirec = find_record_in_file($gid);
		if (empty($indirec)) {
			$indirec = find_gedcom_record($gid);
		}
		$tt = preg_match("/0 @.+@ (.+)/", $indirec, $match);
		if ($tt>0) $type = trim($match[1]);
		else $type = "OTHER";

		$sql = "SELECT pl_p_id FROM ".$TBLPREFIX."placelinks WHERE pl_gid='".$DBCONN->escapeSimple($gid)."' AND pl_file='".$DBCONN->escapeSimple($GEDCOM)."'";
		$res =& dbquery($sql);
		$placeids = array();
		while($row =& $res->fetchRow()) {
			$placeids[] = $row[0];
		}
		$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_gid='".$DBCONN->escapeSimple($gid)."' AND pl_file='".$DBCONN->escapeSimple($GEDCOM)."'";
		$res =& dbquery($sql);

		//-- delete any unlinked places
		foreach($placeids as $indexval => $p_id) {
			$sql = "SELECT count(pl_p_id) FROM ".$TBLPREFIX."placelinks WHERE pl_p_id=$p_id AND pl_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			$row =& $res->fetchRow();
			if ($row[0]==0) {
				$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_id=$p_id AND p_file='".$DBCONN->escapeSimple($GEDCOM)."'";
				$res =& dbquery($sql);
			}
		}

		if ($type=="INDI") {
			$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_id LIKE '".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
			$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_gid LIKE '".$DBCONN->escapeSimple($gid)."' AND n_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
		}
		else if ($type=="FAM") {
			$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_id LIKE '".$DBCONN->escapeSimple($gid)."' AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
		}
		else if ($type=="SOUR") {
			$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_id LIKE '".$DBCONN->escapeSimple($gid)."' AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
		}
		else {
			$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_id LIKE '".$DBCONN->escapeSimple($gid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
			$res =& dbquery($sql);
		}
		if ($change["type"]!="delete") import_record($indirec, true);
		unset($pgv_changes[$cid]);
		write_changes();
		if (isset($_SESSION["recent_changes"]["user"][$GEDCOM])) unset($_SESSION["recent_changes"]["user"][$GEDCOM]);
		if (isset($_SESSION["recent_changes"]["gedcom"][$GEDCOM])) unset($_SESSION["recent_changes"]["gedcom"][$GEDCOM]);
		AddToLog("Accepted change $cid ".$change["type"]." into database ->" . getUserName() ."<-");
		if ($MULTI_MEDIA_DB) {
			require_once("includes/functions_mediadb.php");
			commit_db_changes($GEDCOM, $indirec);
		}
		return true;
	}
	return false;
}

/**
 * get the top surnames
 * @param int $num	how many surnames to return
 * @return array
 */
function get_top_surnames($num) {
	global $TBLPREFIX, $GEDCOM, $DBCONN;

	$surnames = array();
	$sql = "SELECT COUNT(i_surname) as count, i_surname from ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOM)."' GROUP BY i_surname ORDER BY count DESC";
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row[1])]["match"])) $surnames[str2upper($row[1])]["match"] += $row[0];
			else {
				$surnames[str2upper($row[1])]["name"] = $row[1];
				$surnames[str2upper($row[1])]["match"] = $row[0];
			}
		}
		$res->free();
	}
	$sql = "SELECT COUNT(n_surname) as count, n_surname from ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOM)."' AND n_type!='C' GROUP BY n_surname ORDER BY count DESC";
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row[1])]["match"])) $surnames[str2upper($row[1])]["match"] += $row[0];
			else {
				$surnames[str2upper($row[1])]["name"] = $row[1];
				$surnames[str2upper($row[1])]["match"] = $row[0];
			}
		}
		$res->free();
	}
	return $surnames;
}

/**
 * get next unique id for the given table
 * @param string $table 	the name of the table
 * @param string $field		the field to get the next number for
 * @return int the new id
 */
function get_next_id($table, $field) {
	global $TBLPREFIX;

	$newid = 0;
	$sql = "SELECT MAX($field) FROM ".$TBLPREFIX.$table;
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		$row = $res->fetchRow();
		$res->free();
		$newid = $row[0];
	}
	$newid++;
	return $newid;
}
?>