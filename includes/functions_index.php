<?php

/**
 * Index mode specific functions file
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
 * @package PhpGedView
 * @subpackage Index
 * @version $Id: functions_index.php,v 1.1 2005/10/07 18:08:21 skenow Exp $
 */
if (strstr($_SERVER["PHP_SELF"],"functions")) {
	print "Now, why would you want to do that.	You're not hacking are you?";
	exit;
}

$REGEXP_DB = true;

/**
 * check if a gedcom has been imported into the database
 *
 * this function checks the database to see if the given gedcom has been imported yet.
 * @param string $ged the filename of the gedcom to check for import
 * @return bool return true if the gedcom has been imported otherwise returns false
 */
function check_for_import($ged) {
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	$indexfile = $INDEX_DIRECTORY.$ged."_index.php";
	if ((!file_exists($indexfile))||(filesize($indexfile)<=1)) return false;
	return true;
}

/**
 * find the gedcom record for a family
 *
 * This function first checks the <var>$famlist</var> cache to see if the family has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * also lookup the husb and wife so that they are in the cache
 * @param string $famid the unique gedcom xref id of the family record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_family_record($famid) {
	global $famlist;

	if (empty($famid)) return false;
	$famid = str2upper($famid);
	if (isset($famlist[$famid])) return preg_replace("/\\\'/", "'", $famlist[$famid]["gedcom"]);

	return false;
}

/**
 * find the gedcom record for an individual
 *
 * This function first checks the <var>$indilist</var> cache to see if the individual has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @param string $pid the unique gedcom xref id of the individual record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_person_record($pid) {
	global $indilist, $BUILDING_INDEX, $GEDCOM;

	if (empty($pid)) return false;
	$pid = str2upper($pid);
	if (isset($indilist[$pid])) return preg_replace("/\\\'/", "'", $indilist[$pid]["gedcom"]);

	return false;
}

/**
 * find the gedcom record
 *
 * This function first checks the caches to see if the record has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @param string $pid the unique gedcom xref id of the record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_gedcom_record($pid) {
	global $indilist, $famlist, $sourcelist, $otherlist;

	if (empty($pid)) return false;
	$pid = str2upper($pid);
	if (isset($indilist[$pid])) return preg_replace("/\\\'/", "'", $indilist[$pid]["gedcom"]);
	if (isset($famlist[$pid])) return preg_replace("/\\\'/", "'", $famlist[$pid]["gedcom"]);
	if (isset($sourcelist[$pid])) return preg_replace("/\\\'/", "'", $sourcelist[$pid]["gedcom"]);
	if (isset($otherlist[$pid])) return preg_replace("/\\\'/", "'", $otherlist[$pid]["gedcom"]);
	$pid = strtoupper($pid);
	if (isset($indilist[$pid])) return preg_replace("/\\\'/", "'", $indilist[$pid]["gedcom"]);
	if (isset($famlist[$pid])) return preg_replace("/\\\'/", "'", $famlist[$pid]["gedcom"]);
	if (isset($sourcelist[$pid])) return preg_replace("/\\\'/", "'", $sourcelist[$pid]["gedcom"]);
	if (isset($otherlist[$pid])) return preg_replace("/\\\'/", "'", $otherlist[$pid]["gedcom"]);

	return false;
}

/**
 * find the gedcom record for a source
 *
 * This function first checks the <var>$sourcelist</var> cache to see if the source has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @param string $sid the unique gedcom xref id of the source record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_source_record($sid) {
	global $fcontents, $sourcelist;
	global $pgv_lang;

	if ($sid=="") return false;
	$sid = str2upper($sid);
	if (isset($sourcelist[$sid])) return preg_replace("/\\\'/", "'", $sourcelist[$sid]["gedcom"]);

	return find_record_in_file($sid);
}

/**
 * find the gedcom record for a repository
 *
 * This function first checks the <var>$otherlist</var> cache to see if the source has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @param string $rid the unique gedcom xref id of the repository record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_repo_record($rid) {
	global $fcontents, $otherlist;
	global $pgv_lang;

	if ($rid=="") return false;
	$rid = str2upper($rid);
	if (isset($otherlist[$rid])) return preg_replace("/\\\'/", "'", $otherlist[$rid]["gedcom"]);

	return find_record_in_file($rid);
}

/**
 * find and return the id of the first person in the gedcom
 * @return string the gedcom xref id of the first person in the gedcom
 */
function find_first_person() {
	global $fcontents, $indilist;

	foreach($indilist as $key=>$indi) return $key;
}

//=================== IMPORT FUNCTIONS
//-- function to import a record into the database
function import_record($indirec) {
	global $gid, $type,$indilist,$famlist,$sourcelist,$otherlist;
	global $TBLPREFIX, $GEDCOM_FILE, $FILE, $FP, $pgv_lang, $USE_RIN;
	global $ALPHABET_upper, $ALPHABET_lower, $WORD_WRAPPED_NOTES;

	//-- remove double @ signs
	$indirec = preg_replace("/@+/", "@", $indirec);

	// Remove heading spaces
	$indirec = preg_replace("/\n(\s*)/", "\n", $indirec);

	$ct = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
	if ($ct > 0) {
		$gid = $match[1];
		$type = trim($match[2]);
	}
	else {
		$ct = preg_match("/0 (.*)/", $indirec, $match);
		$gid = trim($match[1]);
		$type = trim($match[1]);
	}
	if ($type == "INDI") {
		$indirec = cleanup_tags_y($indirec);
		$names = get_indi_names($indirec, true);
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
		$isdead = 0;

		$indi = array();
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
		$indilist[$gid] = $indi;
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
		$famlist[$gid] = $fam;
	}
	else if ($type=="SOUR") {
		$et = preg_match("/1 ABBR (.*)/", $indirec, $smatch);
		if ($et>0) $name = $smatch[1];
		$tt = preg_match("/1 TITL (.*)/", $indirec, $smatch);
		if ($tt>0) $name = $smatch[1];
		else $name = $gid;
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
		$source = array();
		$source["name"] = $name;
		$source["gedcom"] = $indirec;
		$source["file"] = $FILE;
		$sourcelist[$gid] = $source;
	}
	else if (preg_match("/_/", $type)==0) {
		if ($type=="HEAD") {
			$ct=preg_match("/1 DATE (.*)/", $indirec, $match);
			if ($ct == 0) {
				$indirec = trim($indirec);
				$indirec .= "\r\n1 DATE ".date("d")." ".date("M")." ".date("Y");
			}
		}
		$source = array();
		$source["type"] = $type;
		$source["gedcom"] = $indirec;
		$source["file"] = $FILE;
		$otherlist[$gid] = $source;
	}
}

function update_isdead($gid, $indi) {
	global $FP, $USE_RIN, $indilist;

	$isdead = 0;
	$isdead = is_dead($indi["gedcom"]);
	if (empty($isdead)) $isdead = 0;
	$indi["isdead"] = $isdead;
	$indilist[$gid] = $indi;
}

function update_family_name($gid, $fam) {
	global $indilist, $pgv_lang, $FP, $famlist;

	$name = "";
	if (!empty($fam["HUSB"])) {
		$name = $indilist[$fam["HUSB"]]["names"][0][0];
		$name .= " + ";
	}
	else $name = "";

	if ((!empty($fam["WIFE"]))&&(isset($indilist[$fam["WIFE"]]["names"][0][0]))) $name .= $indilist[$fam["WIFE"]]["names"][0][0];
	else $name .= "";
	$famlist[$gid]["name"] = $name;
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
	global $TBLPREFIX, $USE_RIN, $indilist, $FILE;

	$indilist[$gid]["names"][] = array($newname, $letter, $surname, "C");
	$indilist[$gid]["gedcom"] = $indirec;
}

//-- function that checks if the database exists and creates tables
function setup_database($stage) {
	global $FP, $INDEX_DIRECTORY, $pgv_lang, $FILE;
	$indexfile = $INDEX_DIRECTORY.$FILE."_index.php";
	$FP = fopen($indexfile, "ab");
	if (!$FP) {
		print "<font class=\"error\">".$pgv_lang["unable_to_create_index"]."</font>";
		exit;
	}
}

//-- erase the data for a gedcom file
function empty_database($FILE) {
	global $INDEX_DIRECTORY, $FP, $pgv_lang;

	$indexfile = $INDEX_DIRECTORY.$FILE."_index.php";
	fclose($FP);
	$FP = fopen($indexfile, "wb");
	if (!$FP) {
		print "<font class=\"error\">".$pgv_lang["unable_to_create_index"]."</font>";
		exit;
	}
}

//-- cleanup the database
function cleanup_database() {
	global $FP, $indilist, $famlist, $sourcelist, $otherlist;

	fwrite($FP, serialize($indilist));
	fwrite($FP, "\n---END-LIST---\n");
	fwrite($FP, serialize($famlist));
	fwrite($FP, "\n---END-LIST---\n");
	fwrite($FP, serialize($sourcelist));
	fwrite($FP, "\n---END-LIST---\n");
	fwrite($FP, serialize($otherlist));
	fwrite($FP, "\n---END-LIST---\n");
	fclose($FP);
}

//-- get the indilist from the datastore
function get_indi_list() {
	global $indilist;

	if (!isset($indilist)) $indilist = array();
	return $indilist;
}

//-- get the famlist from the datastore
function get_fam_list() {
	global $famlist;

	if (!isset($famlist)) $famlist = array();
	return $famlist;
}

//-- get the otherlist from the datastore
function get_other_list() {
	global $otherlist;

	return $otherlist;
}

//-- get the additional sourcelist from the datastore MA @@@@
function get_source_add_title_list() {
	global $sourcelist;
	global $addsourcelist;

    $addsourcelist = array();
    $k=0;
    foreach($sourcelist as $sid => $value) {
	    $source = array();
		$ct = preg_match("/\d ROMN (.*)/", $value["gedcom"], $match);
		if ($ct==0) $ct = preg_match("/\d _HEB (.*)/", $value["gedcom"], $match);
	    if ($ct>0) {
		    $source["name"] 		= $match[1];
			$source["gedcom"] 		= $value["gedcom"];
			$source["file"] 		= $value["file"];
			$addsourcelist[$sid] 	= $source;
		}
	    $k++;
	}
	return $addsourcelist;
}

//-- get the sourcelist from the datastore
function get_source_list() {
	global $sourcelist;

	return $sourcelist;
}


//-- get the repositorylist
function get_repo_list() {
	global $otherlist, $GEDCOM;

	$repolist = array();
	$ct = count($otherlist);
	foreach($otherlist as $key=>$value) {
		if (($value["file"] == $GEDCOM) && ($value["type"] == "REPO")) {
			$repo = array();
			$tt = preg_match("/1 NAME (.*)/", $value["gedcom"], $match);
			$repo["id"] = "@".$key."@";
			$repo["file"] = $value["file"];
			$repo["type"] = $value["type"];
			$repo["gedcom"] = $value["gedcom"];
			$repolist[$match[1]]= $repo;
		}
	}
	ksort($repolist);
	return $repolist;
}

//-- get the repositorylist
function get_repo_id_list() {
	global $otherlist, $GEDCOM;

	$repo_id_list = array();

	$ct = count($otherlist);
	foreach($otherlist as $key=>$value) {
		if (($value["file"] == $GEDCOM) && ($value["type"] == "REPO")) {
			$repo = array();
			$tt = preg_match("/1 NAME (.*)/", $value["gedcom"], $match);
			if ($tt>0) $repo["name"] = $match[1];
			else $repo["name"] = "";
			$repo["file"] = $value["file"];
			$repo["type"] = $value["type"];
			$repo["gedcom"] = $value["gedcom"];
			$repo_id_list[$key] = $repo;
		}
	}
	return $repo_id_list;
}

//-- search through the gedcom records for individuals
function search_indis($query) {
	global $indilist;

	if (!is_array($query)) $query = array($query);

	$myindilist = array();
	foreach($indilist as $gid=>$indi) {
		$add=1;
		foreach($query as $indexval => $q) {
			$q = str2upper($q);
			$q = preg_replace("'/'", "\\/", $q);
			$ct = preg_match("/($q)/is", str2upper($indi["gedcom"]), $recmatch);
			$add = $add && $ct;
		}
		if ($add) $myindilist[$gid] = $indi;
	}
	return $myindilist;
}

//-- search through the gedcom records for individuals in families
function search_indis_fam($add2myindilist) {
	global $indilist, $myindilist;

	foreach($indilist as $gid=>$indi) {
		if (isset($add2myindilist[$gid])){
			$add2my_fam=$add2myindilist[$gid];
			$indi_merged=$indi;
			$indi_merged["gedcom"] .= $add2my_fam;
			$myindilist[$gid] = $indi_merged;
		}
	}
	return $myindilist;
}

//-- search through the gedcom records for individuals, based on a year range
function search_indis_year_range($startyear, $endyear) {
	global $indilist;

	$myindilist = array();
	foreach($indilist as $gid=>$indi) {
		$ct = preg_match_all("/2 DATE.* (\d\d\d\d|\d\d\d)/i", $indi["gedcom"], $recmatch, PREG_PATTERN_ORDER);
		for ($i=1; $i<=$ct; $i++){
			if (($recmatch[1][$i-1] >= $startyear) && ($recmatch[1][$i-1] <= $endyear)) $myindilist[$gid] = $indi;
		}
	}
	return $myindilist;
}


//-- search through the gedcom records for families, based on a year range
function search_fams_year_range($startyear, $endyear) {
	global $famlist;

	$myfamlist = array();
	foreach($famlist as $gid=>$fam) {
		$ct = preg_match_all("/2 DATE.* (\d\d\d\d|\d\d\d)/i", $fam["gedcom"], $recmatch, PREG_PATTERN_ORDER);
		for ($i=1; $i<=$ct; $i++){
			if (($recmatch[1][$i-1] >= $startyear) && ($recmatch[1][$i-1] <= $endyear)) $myfamlist[$gid] = $fam;
		}
	}
	return $myfamlist;
}



//-- search through the gedcom records for individuals
function search_indis_names($query) {
	global $indilist;

	if (!is_array($query)) {
		$query = str2upper($query);
		$query = array($query);
	}

	$myindilist = array();
	foreach($indilist as $gid=>$indi) {
		$add = 1;
		foreach($query as $indexval => $q) {
			$q = preg_replace("'/'", "\\/", $q);
			$ct = preg_match("/($q)/i", str2upper($indi["names"][0][0]), $recmatch);
			$add = $add && $ct;
		}
		if ($add) $myindilist[$gid] = $indi;
	}

	return $myindilist;
}

//-- search through the gedcom records for families
function search_fams($query) {
	global $famlist;

	$query = preg_replace("'/'", "\\/", $query);
	$query = str2upper($query);
	$myfamlist = array();
	foreach($famlist as $gid=>$fam) {
		if (isset($fam["gedcom"])) $ct = preg_match("/($query)/i", str2upper($fam["gedcom"]), $recmatch);
		if ($ct==0) $ct = preg_match("/($query)/is", str2upper($fam["name"]), $recmatch);
		//-- getting the family name here makes the search too slow
		if ($ct>0) $myfamlist[$gid] = $fam;
	}

	return $myfamlist;
}

//-- search through the gedcom records for sources
function search_sources($query) {
	global $sourcelist;

	$query = preg_replace("'/'", "\\/", $query);
	$query = str2upper($query);
	$mysourcelist = array();
	foreach($sourcelist as $gid=>$source) {
		$ct = preg_match("/($query)/is", str2upper($source["gedcom"]), $recmatch);
		if ($ct>0) $mysourcelist[$gid] = $source;
	}

	return $mysourcelist;
}

//-- search through the gedcom records for sources
function search_repos($query) {
	global $otherlist;

	$query = preg_replace("'/'", "\\/", $query);
	$query = str2upper($query);
	$myrepolist = array();
	foreach($otherlist as $gid=>$other) {
		$ct = preg_match("/($query)/is", str2upper($other["gedcom"]), $recmatch);
		if ($ct>0) $myrepolist[$gid] = $other;
	}

	return $myrepolist;
}

function find_places($list) {
	global $placelist, $numfound, $j, $level, $parent, $positions, $found;

	foreach ($list as $key=>$value) {
		print " ";
		// -- put all the places into an array
		$ct = preg_match_all("/\d PLAC (.*)/", $value["gedcom"], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$place = $match[$i][1];
			$place=trim($place);

			$place=preg_replace("/[\"\><]/", "", $place);
			$levels = preg_split ("/,/", $place);		// -- split the place into comma seperated values
			$levels = array_reverse($levels);				// -- reverse the array so that we get the top level first
			$good=true;
			for($k=0; $k<$level; $k++) {
				if (!isset($levels[$k])) $good=false;
				else {
					$levels[$k]=trim($levels[$k]);
					if ($levels[$k]!=$parent[$k]) $good=false;
				}
			}
			if ($good) $positions[] = $key;
			if ((isset($levels[$level]))&&($good)) {
				$numfound++;
				$levels[$level]=trim($levels[$level]);
				if (stristr($found, $levels[$level].";")===false) {
					$placelist[]=$levels[$level];
					$found .= $levels[$level]."; ";
				}
			}
		}//--end for
	}//-- end while
}

//-- find all of the places
function get_place_list() {
	global $GEDCOM, $TBLPREFIX, $placelist, $positions, $indilist, $famlist, $sourcelist, $otherlist;

	// --- find all of the place in the file
	find_places($indilist);
	find_places($famlist);
	find_places($sourcelist);
	find_places($otherlist);
	$positions = array_unique($positions);
	$positions = array_values($positions);
}

//-- get all of the place connections
function get_place_positions($parent, $level) {
	global $positions;
	return $positions;
}

function search_places($list, $splace) {
	global $placelist;
	$k=0;
	foreach ($list as $key=>$value) {
		// -- put all the places into an array
		if (empty($splace)) $ct = preg_match_all("/\d PLAC (.*)/", $value["gedcom"], $match, PREG_SET_ORDER);
		else $ct = preg_match_all("/\d PLAC (.*$splace.*)/i", $value["gedcom"], $match, PREG_SET_ORDER);
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
}

//-- find all of the places
function find_place_list($place) {
	global $GEDCOM, $TBLPREFIX, $placelist, $indilist, $famlist, $sourcelist, $otherlist;

	// --- find all of the place in the file
	search_places($indilist, $place);
	search_places($famlist, $place);
	search_places($sourcelist, $place);
	search_places($otherlist, $place);
	$found = array();
	foreach($placelist as $indexval => $place) {
		$upperplace = str2upper($place);
		if (!isset($found[$upperplace])) {
			$found[$upperplace] = $place;
		}
	}
	$placelist = array_values($found);
}

function find_media($list, $type) {
	global $ct, $medialist, $MEDIA_DIRECTORY, $foundlist, $PGV_IMAGE_DIR, $PGV_IMAGES;

	foreach ($list as $key=>$value) {
		print " ";
		if (isset($value["gedcom"])) find_media_in_record($value["gedcom"]);
	}
}

//-- find all of the media
function get_media_list() {
	global $GEDCOM, $TBLPREFIX, $medialist, $indilist, $sourcelist, $famlist, $otherlist, $ct;

	$ct=0;
	find_media($indilist, 'INDI');
	find_media($famlist, 'FAM');
	find_media($sourcelist, 'SOUR');
	find_media($otherlist, 'OTHER');
}

//-- get the first character in the list
function get_indi_alpha() {
	global $indilist, $CHARACTER_SET, $LANGUAGE, $SHOW_MARRIED_NAMES;

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	$indialpha = array();
	foreach($indilist as $gid=>$indi) {
		foreach($indi["names"] as $indexval => $name) {
			if ($SHOW_MARRIED_NAMES || $name[3]!='C') {
				$letter = $name[1];
	
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
		
				if (!isset($indialpha[$letter])) {
					$indialpha[$letter]["letter"]=$letter;
					$indialpha[$letter]["gid"]=$gid;
				}
				else {
					$indialpha[$letter]["gid"] .= ",$gid";
				}
			}
		}
	}

	return $indialpha;
}

//-- find all of the individuals who start with the given letter
function get_alpha_indis($letter) {
	global $indialpha, $indilist, $surname;

	$tindilist = array();
	$list = $indialpha[$letter]["gid"];
	$gids = preg_split("/,/", $list);
	foreach($gids as $indexval => $gid) {
		$tindilist[$gid] = $indilist[$gid];
	}
	return $tindilist;
}

//-- find all of the individuals who start with the given letter
function get_surname_indis($surname) {
	global $indialpha, $indilist, $alpha, $SHOW_MARRIED_NAMES;

	$tindilist = array();
	$list = $indialpha[$alpha]["gid"];
	$gids = preg_split("/,/", $list);
	foreach($gids as $indexval => $gid) {
		foreach($indilist[$gid]["names"] as $indexval => $name) {
			if ($SHOW_MARRIED_NAMES || $name[3]!='C') {
				if (stristr(str2upper($surname), str2upper($name[2]))) $tindilist[$gid] = $indilist[$gid];
			}
		}
	}
	return $tindilist;
}

//-- get the first character in the list
function get_fam_alpha() {
	global $famlist, $CHARACTER_SET, $LANGUAGE, $SHOW_MARRIED_NAMES;

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	$famalpha = array();

	$tindilist = search_indis("1 FAMS @");
	foreach($tindilist as $gid=>$indi) {
		foreach($indi["names"] as $indexval => $name) {
			if ($SHOW_MARRIED_NAMES || $name[3]!='C') {
				$letter = $name[1];
	
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
		
				if (!isset($famalpha[$letter])) {
					$famalpha[$letter]["letter"]=$letter;
					$famalpha[$letter]["gid"]=$gid;
				}
				else {
					$famalpha[$letter]["gid"] .= ",$gid";
				}
			}
		}
	}
	$letter = "@";
	if (!isset($famalpha[$letter])) {
		foreach($famlist as $gid=>$fam) {
			if (empty($fam["HUSB"]) || empty($fam["WIFE"])) {
				$famalpha[$letter]["letter"] = $letter;
			}
		}
	}

	return $famalpha;
}

//-- find all of the individuals who start with the given letter
function get_alpha_fams($letter) {
	global $famalpha, $famlist, $surname, $indilist, $LANGUAGE;

	$expalpha = $letter;
	if ($expalpha=="(") $expalpha = '\(';
	if ($expalpha=="[") $expalpha = '\[';
	if ($expalpha=="?") $expalpha = '\?';
	if ($expalpha=="/") $expalpha = '\/';

	$tfamlist = array();
	if (!isset($famalpha[$letter])) return $tfamlist;
	$list = $famalpha[$letter]["gid"];
	$gids = preg_split("/,/", $list);
	$gids = array_unique($gids);
	foreach($gids as $indexval => $gid) {
		$indi = $indilist[$gid];
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		$surnames = array();
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["HUSB"]==$gid) {
				$HUSB = $famlist[$famid]["HUSB"];
				$WIFE = $famlist[$famid]["WIFE"];
			}
			else {
				$HUSB = $famlist[$famid]["WIFE"];
				$WIFE = $famlist[$famid]["HUSB"];
			}
			$hname="";
			foreach($indi["names"] as $indexval => $namearray) {
				//-- don't use married names in the family list
				if ($namearray[3]!='C') {
					$text = "";
					if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
						if ($letter == "Ø") $text = "OE";
						else if ($letter == "Æ") $text = "AE";
						else if ($letter == "Å") $text = "AA";
					}
					if ((preg_match("/^$expalpha/", $namearray[1])>0)||(!empty($text)&&preg_match("/^$text/", $namearray[1])>0)) {
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
	if ($letter=="@") {
		foreach($famlist as $gid=>$fam) {
			if (empty($fam["HUSB"]) && empty($fam["WIFE"])) {
				$fam["surnames"] = array("@N.N.");
				$tfamlist[$gid] = $fam;
			}
		}
	}
	return $tfamlist;
}

function get_surname_fams($surname) {
	global $famalpha, $famlist, $indilist, $alpha;

	global $famalpha, $famlist, $surname, $indilist, $LANGUAGE;

	$tfamlist = array();
	if (!isset($famalpha[$alpha])) return $tfamlist;
	$list = $famalpha[$alpha]["gid"];
	$gids = preg_split("/,/", $list);
	foreach($gids as $indexval => $gid) {
		$indi = $indilist[$gid];
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$surnames = array();
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["HUSB"]==$gid) {
				$HUSB = $famlist[$famid]["HUSB"];
				$WIFE = $famlist[$famid]["WIFE"];
			}
			else {
				$HUSB = $famlist[$famid]["WIFE"];
				$WIFE = $famlist[$famid]["HUSB"];
			}
			$hname = "";
			foreach($indi["names"] as $indexval => $namearray) {
					if (stristr($namearray[2], $surname)!==false) $hname = sortable_name_from_name($namearray[0]);
			}
			if (!empty($hname)) {
				$wname = get_sortable_name($WIFE);
				if (hasRTLText($hname)) {
					if (isset($indilist[$WIFE]["names"])) {
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
	if ($alpha=="@") {
		foreach($famlist as $gid=>$fam) {
			if (empty($fam["HUSB"]) || empty($fam["WIFE"])) {
				$hname = trim(get_sortable_name($fam["HUSB"]));
				$wname = trim(get_sortable_name($fam["WIFE"]));
				if (empty($hname)) $hname = "@N.N., @P.N.";
				if (empty($wname)) $wname = "@N.N., @P.N.";
				if (empty($fam["HUSB"])) $name = $hname." + ".$wname;
				else $name = $wname." + ".$hname;
				$fam["name"] = $name;
				$tfamlist[$gid] = $fam;
				$famlist[$gid] = $fam;
			}
		}
	}
	return $tfamlist;
}

//-- function to find the gedcom id for the given rin
function find_rin_id($rin) {
	global $indilist;

	foreach($indilist as $gid=>$indi) {
		if ($indi["rin"]==$rin) return $gid;
	}
	return $rin;
}

function delete_gedcom($ged) {
	global $INDEX_DIRECTORY, $favorites, $pgv_news, $pgv_changes;

	if (isset($pgv_changes)) {
		//-- erase any of the changes
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) unset($pgv_changes[$cid]);
		}
		write_changes();
		//-- delete old favorites
		$oldfavs = $favorites;
		if (!empty($favorites)) {
			foreach($oldfavs as $fv_id=>$fav) {
				if ($fav["file"]==$ged) deleteFavorite($fv_id);
			}
		}
		//-- delete gedcom blocks
		$ublock = array();
		$ublock["right"] = array();
		$ublock["main"] = array();
		setBlocks($ged, $ublock);
		//-- delete gedcom news
		$unews = getUserNews($ged);
		foreach($unews as $nid=>$n) deleteNews($nid);
	}

	@unlink($INDEX_DIRECTORY.$ged."_conf.php");
	@unlink($INDEX_DIRECTORY.$ged."_index.php");
}

//-- return the current size of the given list
//- list options are indilist famlist sourcelist and otherlist
function get_list_size($list) {
	global $TBLPREFIX, $indilist, $famlist, $sourcelist, $otherlist;

	switch($list) {
		case "indilist":
			return count($indilist);
		break;
		case "famlist":
			return count($famlist);
		break;
		case "sourcelist":
			return count($sourcelist);
		break;
		case "otherlist":
			return count($otherlist);
		break;
	}
	return 0;
}

/**
 * load indilist from GEDCOM index file
 *
 * @param none
 */
function load_gedcom_indilist() {
	global $INDEX_DIRECTORY, $GEDCOM;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_index.php";
	if (!file_exists($indexfile)) return false;
	$fp = fopen($indexfile, "r");
	$fcontents = fread($fp, filesize($indexfile));
	fclose($fp);
	$lists = unserialize($fcontents);
	unset($fcontents);
	return $lists["indilist"];
}

/**
 * Accpet changed gedcom record into database
 *
 * This function gets an updated record from the gedcom file and replaces it in the database
 * @param string $cid The change id of the record to accept
 */
function accept_changes($cid, $manual_write = false) {
	global $pgv_changes, $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist, $FILE;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes)-1];
		$change["undo"] = $change["undo"];
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

		if ($type=="INDI") {
			unset($indilist[$gid]);
		}
		else if ($type=="FAM") {
			unset($famlist[$gid]);
		}
		else if ($type=="SOUR") {
			unset($sourcelist[$gid]);
		}
		else {
			unset($otherlist[$gid]);
		}
		if ($change["type"]!="delete") {
			import_record($indirec, true);
			if ($type=="INDI") {
				update_isdead($gid, $indilist[$gid]);
				$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
					$famid = trim($match[$i][1]);
					if (isset($famlist[$famid])) update_family_name($famid, $famlist[$famid]);
				}
			}
			else if ($type=="FAM") {
				update_family_name($gid, $famlist[$gid]);
			}
		}
		unset($pgv_changes[$cid]);

		if (!$manual_write) {
			setup_database(1);
			empty_database($FILE);
			cleanup_database();
			write_changes();
		}
		if (isset($_SESSION["recent_changes"]["user"][$GEDCOM])) unset($_SESSION["recent_changes"]["user"][$GEDCOM]);
		if (isset($_SESSION["recent_changes"]["gedcom"][$GEDCOM])) unset($_SESSION["recent_changes"]["gedcom"][$GEDCOM]);
		AddToLog("Accepted change $cid ".$change["type"]." into database ->" . getUserName() ."<-");
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
	global $indilist, $surnames;

	foreach($indilist as $gid=>$indi) {
		$name = $indi["names"][0][2];
		if (stristr($name, "@N.N")===false) {
			surname_count($name);
		}
	}
	return $surnames;
}
?>