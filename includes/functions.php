<?php
/**
 * Core Functions that can be used by any page in PGV
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
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
 * @version $Id: functions.php,v 1.27 2005/12/16 15:53:54 yalnifj Exp $
 */

/**
 * security check to prevent hackers from directly accessing this file
 */
if (strstr($_SERVER["PHP_SELF"],"functions.php")) {
	print "Why do you want to do that?";
	exit;
}

// ************************************************* START OF INITIALIZATION FUNCTIONS ********************************* //
/**
 * initialize and check the database
 *
 * this function will create a database connection and return false if any errors occurred
 * @return boolean true if database successully connected, false if there was an error
 */
function check_db() {
	global $PGV_DATABASE, $DBTYPE, $DBHOST, $DBUSER, $DBPASS, $DBNAME, $DBCONN, $TOTAL_QUERIES, $PHP_SELF;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	if ($PGV_DATABASE=="db") {
		if ((is_object($DBCONN)) && (!DB::isError($DBCONN))) return true;
		//-- initialize query counter
		$TOTAL_QUERIES = 0;

		$dsn = array(
			'phptype'  => $DBTYPE,
			'username' => $DBUSER,
			'password' => $DBPASS,
			'hostspec' => $DBHOST,
			'database' => $DBNAME
		);

		$options = array(
			'debug' 	  => 3,
			'portability' => DB_PORTABILITY_ALL,
			'persistent'  => false
		);

		$DBCONN = DB::connect($dsn, $options);
		if (DB::isError($DBCONN)) {
			//die($DBCONN->getMessage());
			return false;
		}

		//-- protect the username and password on other pages
		if (preg_match("/editconfig\.php/", $PHP_SELF)==0) {
			$DBUSER = "";
			$DBPASS = "";
		}
		return true;
	}

	//-- index mode check
	if ($PGV_DATABASE=="index") {
		$indexfile = $INDEX_DIRECTORY.$GEDCOM."_index.php";

		//-- check for index files and update them if necessary
		if (!isset($BUILDING_INDEX)) {
			$updateindex=false;
			if ((file_exists($indexfile))&&(!empty($GEDCOMS[$GEDCOM]["path"])&&file_exists($GEDCOMS[$GEDCOM]["path"]))) {
				$indextime = filemtime($indexfile);
				$gedtime = filemtime($GEDCOMS[$GEDCOM]["path"]);
				if ($indextime < $gedtime) $updateindex=true;
			}
			else {
				$updateindex=true;
			}

			if (file_exists($indexfile)) {
				$fp = fopen($indexfile, "rb");

				$fcontents = fread($fp, filesize($indexfile));
				fclose($fp);

				$state = 1;
				$offset = 0;
				while($state<5) {
					$pos1 = strpos($fcontents, "\n---END-LIST---\n");
					if ($pos1!==false) {
						$data = substr($fcontents, 0, $pos1);
						if ($state<4) $fcontents = substr($fcontents, strpos($fcontents, "-\n", $pos1)+2);
						switch($state) {
							case 1:
								$indilist = unserialize($data);
								break;
							case 2:
								$famlist = unserialize($data);
								break;
							case 3:
								$sourcelist = unserialize($data);
								break;
							case 4:
								$otherlist = unserialize($data);
								break;
						}
						$data = "";
						$state++;
					}
					else {
						$lists = unserialize($fcontents);
						unset($fcontents);
						$indilist = $lists["indilist"];
						$famlist = $lists["famlist"];
						$sourcelist = $lists["sourcelist"];
						$otherlist = $lists["otherlist"];
						return true;
//						return false;
					}
				}
				unset($fcontents);
			}
		}
		return true;
	}

	return false;
}

/**
 * get gedcom configuration file
 *
 * this function returns the path to the currently active GEDCOM configuration file
 * @return string path to gedcom.ged_conf.php configuration file
 */
function get_config_file() {
	global $GEDCOMS, $GEDCOM, $PGV_BASE_DIRECTORY;

	if (count($GEDCOMS)==0) {
		return $PGV_BASE_DIRECTORY."config_gedcom.php";
	}
	if ((!empty($GEDCOM))&&(isset($GEDCOMS[$GEDCOM]))) return $GEDCOMS[$GEDCOM]["config"];
	foreach($GEDCOMS as $GEDCOM=>$gedarray) {
		$_SESSION["GEDCOM"] = $GEDCOM;
		return $PGV_BASE_DIRECTORY.$gedarray["config"];
	}
}

/**
 * Get the version of the privacy file
 *
 * This function opens the given privacy file and returns the privacy version from the file
 * @param string $privfile the path to the privacy file
 * @return string the privacy file version number
 */
function get_privacy_file_version($privfile) {
	$privversion = "0";

	//-- check to make sure that the privacy file is the current version
	if (file_exists($privfile)) {
		$privcontents = implode("", file($privfile));
		$ct = preg_match("/PRIVACY_VERSION.*=.*\"(.+)\"/", $privcontents, $match);
		if ($ct>0) {
			$privversion = trim($match[1]);
		}
	}

	return $privversion;
}

/**
 * Get the path to the privacy file
 *
 * Get the path to the privacy file for the currently active GEDCOM
 * @return string path to the privacy file
 */
function get_privacy_file() {
	global $GEDCOMS, $GEDCOM, $PGV_BASE_DIRECTORY, $REQUIRED_PRIVACY_VERSION;

	$privfile = "privacy.php";
	if (count($GEDCOMS)==0) {
		$privfile = $PGV_BASE_DIRECTORY."privacy.php";
	}
	if ((!empty($GEDCOM))&&(isset($GEDCOMS[$GEDCOM]))) {
		if ((isset($GEDCOMS[$GEDCOM]["privacy"]))&&(file_exists($GEDCOMS[$GEDCOM]["privacy"]))) $privfile = $GEDCOMS[$GEDCOM]["privacy"];
		else $privfile = $PGV_BASE_DIRECTORY."privacy.php";
	}
	else {
		foreach($GEDCOMS as $GEDCOM=>$gedarray) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			if ((isset($gedarray["privacy"]))&&(file_exists($gedarray["privacy"]))) $privfile = $PGV_BASE_DIRECTORY.$gedarray["privacy"];
			else $privfile = $PGV_BASE_DIRECTORY."privacy.php";
		}
	}
	$privversion = get_privacy_file_version($privfile);
	if ($privversion<$REQUIRED_PRIVACY_VERSION) $privfile = $PGV_BASE_DIRECTORY."privacy.php";

	return $privfile;
}

/**
 * Get the current time in micro seconds
 *
 * returns a timestamp for the current time in micro seconds
 * obtained from online documentation for the microtime() function
 * on php.net
 * @return float time in micro seconds
 */
function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Store GEDCOMS array
 *
 * this function will store the <var>$GEDCOMS</var> array in the <var>$INDEX_DIRECTORY</var>/gedcoms.php
 * file.  The gedcoms.php file is included in session.php to create the <var>$GEDCOMS</var>
 * array with every page request.
 * @see session.php
 */
function store_gedcoms() {
	global $GEDCOMS, $pgv_lang, $INDEX_DIRECTORY, $DEFAULT_GEDCOM, $COMMON_NAMES_THRESHOLD, $GEDCOM, $CONFIGURED;

	if (!$CONFIGURED) return false;
	uasort($GEDCOMS, "gedcomsort");
	$gedcomtext = "<?php\n//--START GEDCOM CONFIGURATIONS\n";
	$gedcomtext .= "\$GEDCOMS = array();\n";
	foreach($GEDCOMS as $indexval => $GED) {
		$GED["config"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["config"]);
		if (isset($GED["privacy"])) $GED["privacy"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["privacy"]);
		else $GED["privacy"] = "privacy.php";
		$GED["path"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["path"]);
		$GED["title"] = stripslashes($GED["title"]);
		$GED["title"] = preg_replace("/\"/", "\\\"", $GED["title"]);
		$gedcomtext .= "\$gedarray = array();\n";
		$gedcomtext .= "\$gedarray[\"gedcom\"] = \"".$GED["gedcom"]."\";\n";
		$gedcomtext .= "\$gedarray[\"config\"] = \"".$GED["config"]."\";\n";
		$gedcomtext .= "\$gedarray[\"privacy\"] = \"".$GED["privacy"]."\";\n";
		$gedcomtext .= "\$gedarray[\"title\"] = \"".$GED["title"]."\";\n";
		$gedcomtext .= "\$gedarray[\"path\"] = \"".$GED["path"]."\";\n";
		if (empty($GED["commonsurnames"])) {
			if ($GED["gedcom"]==$GEDCOM) {
				$GED["commonsurnames"] = "";
				$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
//				$GED["commonsurnames"] = ",";
				foreach($surnames as $indexval => $surname) {
					$GED["commonsurnames"] .= $surname["name"].", ";
				}
			}
			else $GED["commonsurnames"]="";
		}
		$GEDCOMS[$GED["gedcom"]]["commonsurnames"] = $GED["commonsurnames"];
		$gedcomtext .= "\$gedarray[\"commonsurnames\"] = \"".addslashes($GED["commonsurnames"])."\";\n";
		$gedcomtext .= "\$GEDCOMS[\"".$GED["gedcom"]."\"] = \$gedarray;\n";
	}
	$gedcomtext .= "\n\$DEFAULT_GEDCOM = \"$DEFAULT_GEDCOM\";\n";
	$gedcomtext .= "\n"."?>";

	$fp = fopen($INDEX_DIRECTORY."gedcoms.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $gedcomtext);
		fclose($fp);
	}
}

/**
 * Check if a gedcom file is downloadable over the internet
 *
 * @author opus27
 * @param string $gedfile gedcom file
 * @return mixed 	$url if file is downloadable, false if not
 */
function check_gedcom_downloadable($gedfile) {
	global $SERVER_URL, $pgv_lang;

	//$url = $SERVER_URL;
	$url = "http://localhost/";
	if (substr($url,-1,1)!="/") $url .= "/";
	$url .= preg_replace("/ /", "%20", $gedfile);
	@ini_set('user_agent','MSIE 4\.0b2;'); // force a HTTP/1.0 request
	@ini_set('default_socket_timeout', '10'); // timeout
	$handle = @fopen ($url, "r");
	if ($handle==false) return false;
	// open successfull : now make sure this is a GEDCOM file
	$txt = fread ($handle, 80);
	fclose($handle);
	if (strpos($txt, " HEAD")==false) return false;
	return $url;
}

/**
 * Check if a person is dead
 *
 * For the given XREF id, this function will return true if the person is dead
 * and false if the person is alive.
 * @param string $pid		The Gedcom XREF ID of the person to check
 * @return boolean			True if dead, false if alive
 */
function is_dead_id($pid) {
	global $indilist, $BUILDING_INDEX, $GEDCOM, $PGV_DATABASE;

	if (empty($pid)) return true;

	//-- if using indexes then first check the indi_isdead array
	if ((!$BUILDING_INDEX)&&(isset($indilist))) {
		//-- check if the person is already in the $indilist cache
		if ((!isset($indilist[$pid]["isdead"]))||($indilist[$pid]["file"]!=$GEDCOM)) {
			//-- load the individual into the cache by calling the find_person_record function
			$gedrec = find_person_record($pid);
			if (empty($gedrec)) return true;
		}
		if ((isset($indilist[$pid]["isdead"]))&&($indilist[$pid]["file"]==$GEDCOM)) {
			if (($PGV_DATABASE!='index')&&($indilist[$pid]["isdead"]==-1)) {
				$indilist[$pid]["isdead"] = update_isdead($pid, $indilist[$pid]);
			}
			return $indilist[$pid]["isdead"];
		}
	}
	return is_dead(find_person_record($pid));
}

// This functions checks if an existing file is physically writeable
// The standard PHP function only checks for the R/O attribute and doesn't
// detect authorisation by ACL.
function file_is_writeable($file) {
	$err_write = false;
	$handle = @fopen($file,"r+");
	if	($handle)	{
		$i = fclose($handle);
		$err_write = true;
	}
	return($err_write);
}

// ************************************************* START OF GEDCOM FUNCTIONS ********************************* //
/**
 * get a gedcom subrecord
 *
 * searches a gedcom record and returns a subrecord of it.  A subrecord is defined starting at a
 * line with level N and all subsequent lines greater than N until the next N level is reached.
 * For example, the following is a BIRT subrecord:
 * <code>1 BIRT
 * 2 DATE 1 JAN 1900
 * 2 PLAC Phoenix, Maricopa, Arizona</code>
 * The following example is the DATE subrecord of the above BIRT subrecord:
 * <code>2 DATE 1 JAN 1900</code>
 * @author John Finlay (yalnifj)
 * @param int $level the N level of the subrecord to get
 * @param string $tag a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
 * @param string $gedrec the parent gedcom record to search in
 * @param int $num this allows you to specify which matching <var>$tag</var> to get.  Oftentimes a
 * gedcom record will have more that 1 of the same type of subrecord.  An individual may have
 * multiple events for example.  Passing $num=1 would get the first 1.  Passing $num=2 would get the
 * second one, etc.
 * @return string the subrecord that was found or an empty string "" if not found.
 */
function get_sub_record($level, $tag, $gedrec, $num=1) {
	$pos1=0;
	$subrec = "";
	if (empty($gedrec)) return "";
	while(($num>0)&&($pos1<strlen($gedrec))) {
		$pos1 = strpos($gedrec, $tag, $pos1);
		if ($pos1===false) return "";
		$pos2 = strpos($gedrec, "\n$level", $pos1+1);
		if (!$pos2) {
			if ($num==1) return substr($gedrec, $pos1);
			else return "";
		}
		if ($num==1) $subrec = substr($gedrec, $pos1, $pos2-$pos1);
		$num--;
		$pos1 = $pos2;
	}
	return $subrec;
}

/**
 * find all of the level 1 subrecords of the given record
 * @param string $gedrec the gedcom record to get the subrecords from
 * @param string $ignore a list of tags to ignore
 * @param boolean $families whether to include any records from the family
 * @param boolean $sort whether or not to sort the record by date
 * @param boolean $ApplyPriv whether to apply privacy right now or later
 * @return array an array of the raw subrecords to return
 */
function get_all_subrecords($gedrec, $ignore="", $families=true, $sort=true, $ApplyPriv=true) {
	global $ASC, $IGNORE_FACTS, $IGNORE_YEAR;
	
	$repeats = array();

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}
	$prev_tags = array();
	$ct = preg_match_all("/\n1 (\w+)(.*)/", $gedrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$fact = trim($match[$i][1]);
		if (strpos($ignore, $fact)===false) {
			if (!$ApplyPriv || (showFact($fact, $id)&& showFactDetails($fact,$id))) {
				if (isset($prev_tags[$fact])) $prev_tags[$fact]++;
				else $prev_tags[$fact] = 1;
				$subrec = get_sub_record(1, "1 $fact", $gedrec, $prev_tags[$fact]);
				if (!$ApplyPriv || !FactViewRestricted($id, $subrec)) {
					if ($fact=="EVEN") {
						$tt = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
						if ($tt>0) {
							$type = trim($tmatch[1]);
							if (!$ApplyPriv || (showFact($type, $id)&&showFactDetails($type,$id))) $repeats[] = trim($subrec)."\r\n";
						}
						else $repeats[] = trim($subrec)."\r\n";
					}
					else $repeats[] = trim($subrec)."\r\n";
				}
			}
		}
	}

	//-- look for any records in FAMS records
	if ($families) {
		$ft = preg_match_all("/1 FAMS @(.+)@/", $gedrec, $fmatch, PREG_SET_ORDER);
		for($f=0; $f<$ft; $f++) {
			$famid = $fmatch[$f][1];
			$famrec = find_gedcom_record($fmatch[$f][1]);
			$parents = find_parents_in_record($famrec);
			if ($id==$parents["HUSB"]) $spid = $parents["WIFE"];
			else $spid = $parents["HUSB"];
			$prev_tags = array();
			$ct = preg_match_all("/\n1 (\w+)(.*)/", $famrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$fact = trim($match[$i][1]);
				if (strpos($ignore, $fact)===false) {
					if (!$ApplyPriv || (showFact($fact, $id)&&showFactDetails($fact,$id))) {
						if (isset($prev_tags[$fact])) $prev_tags[$fact]++;
						else $prev_tags[$fact] = 1;
						$subrec = get_sub_record(1, "1 $fact", $famrec, $prev_tags[$fact]);
						$subrec .= "\r\n1 _PGVS @$spid@\r\n";
						$subrec .= "1 _PGVFS @$famid@\r\n";
						if ($fact=="EVEN") {
							$ct = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
							if ($ct>0) {
								$type = trim($tmatch[1]);
								if (!$ApplyPriv or (showFact($type, $id)&&showFactDetails($type,$id))) $repeats[] = trim($subrec)."\r\n";
							}
							else $repeats[] = trim($subrec)."\r\n";
						}
						else $repeats[] = trim($subrec)."\r\n";
					}
				}
			}
		}
	}

	if ($sort) {
		$ASC = 0;
  		$IGNORE_FACTS = 0;
  		$IGNORE_YEAR = 0;
		usort($repeats, "compare_facts");
	}
	return $repeats;
}

/**
 * get CONT lines
 *
 * get the N+1 CONT or CONC lines of a gedcom subrecord
 * @param int $nlevel the level of the CONT lines to get
 * @param string $nrec the gedcom subrecord to search in
 * @return string a string with all CONT or CONC lines merged
 */
function get_cont($nlevel, $nrec) {
	global $WORD_WRAPPED_NOTES;
	$text = "";
	$tt = preg_match_all("/$nlevel CON[CT](.*)/", $nrec, $cmatch, PREG_SET_ORDER);
	for($i=0; $i<$tt; $i++) {
		if (strstr($cmatch[$i][0], "CONT")) $text.="<br />\n";
		else if ($WORD_WRAPPED_NOTES) $text.=" ";
		$conctxt = $cmatch[$i][1];
		if (!empty($conctxt)) {
			if ($conctxt{0}==" ") $conctxt = substr($conctxt, 1);
			$conctxt = preg_replace("/[\r\n]/","",$conctxt);
			$text.=$conctxt;
		}
	}
	$text = preg_replace("/~~/", "<br />", $text);
	return $text;
}

/**
 * find the parents in a family
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents($famid) {
	global $pgv_lang;

	$famrec = find_family_record($famid);
	if (empty($famrec)) {
		if (userCanEdit(getUserName())) {
			$famrec = find_record_in_file($famid);
			if (empty($famrec)) return false;
		}
		else return false;
	}
	return find_parents_in_record($famrec);
}

/**
 * find the parents in a family record
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents_in_record($famrec) {
	global $pgv_lang;

	if (empty($famrec)) return false;
	$parents = array();
	$ct = preg_match("/1 HUSB @(.*)@/", $famrec, $match);
	if ($ct>0) $parents["HUSB"]=$match[1];
	else $parents["HUSB"]="";
	$ct = preg_match("/1 WIFE @(.*)@/", $famrec, $match);
	if ($ct>0) $parents["WIFE"]=$match[1];
	else $parents["WIFE"]="";
	return $parents;
}

/**
 * find all child family ids
 *
 * searches an individual gedcom record and returns an array of the FAMC ids where this person is a
 * child in the family
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_family_ids($pid) {
	$families = array();
	if (!$pid) return $families;

	$indirec = find_person_record($pid);
	$ct = preg_match_all("/1\sFAMC\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$families[$i]=$match[$i][1];
	}
	return $families;
}

/**
 * find all spouse family ids
 *
 * searches an individual gedcom record and returns an array of the FAMS ids where this person is a
 * sopuse in the family
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_sfamily_ids($pid) {
	$families = array();
	if (empty($pid)) return $families;
	$indirec = find_person_record($pid);
	$ct = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $match,PREG_SET_ORDER);

	if ($ct>0){
		for($i=0; $i<$ct; $i++) {
			$families[$i] = $match[$i][1];
		}
	}
	return $families;
}

/**
 * find record in file
 *
 * this function finds a gedcom record in the gedcom file by searching through the file 4Kb at a
 * time
 * @param string $gid the gedcom xref id of the record to find
 */
function find_record_in_file($gid) {
	global $GEDCOMS, $GEDCOM, $indilist;
	$fpged = fopen($GEDCOMS[$GEDCOM]["path"], "r");
	if (!$fpged) return false;
	$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
	$fcontents = "";
	$count = 0;
	while(!feof($fpged)) {
		$fcontents .= fread($fpged, $BLOCK_SIZE);
		$count++;
		$pos1 = strpos($fcontents, "0 @$gid@", 0);
		if ($pos1===false)  {
			$pos1 = strrpos($fcontents, "\n");
		//	print $pos1."-".$count."<br /> ";
			$fcontents = substr($fcontents, $pos1);
		//	print "[".$fcontents."]";
		}
		else {
			$pos2 = strpos($fcontents, "\n0", $pos1+1);
			while((!$pos2)&&(!feof($fpged))) {
				$fcontents .= fread($fpged, $BLOCK_SIZE);
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
			}
			if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
			else $indirec = substr($fcontents, $pos1);
//			fclose($fpged);
			$ct = preg_match("/0 @.+@ (.+)/", $indirec, $match);
			if ($ct>0) {
				$type = trim($match[1]);
				//-- add record to indilist for caching
				if ($type=="INDI") {
					$indilist[$gid]["gedcom"]=$indirec;
					$indilist[$gid]["names"]=get_indi_names($indirec);
					$indilist[$gid]["file"]=$GEDCOM;
					$indilist[$gid]["isdead"] = -1;
				}
			}
			fclose($fpged);
			return $indirec;
			break;
		}
	}
	fclose($fpged);
	return false;
}

function cleanup_tags_y($irec) {
	$cleanup_facts = array("ANUL","CENS","DIV","DIVF","ENGA","MARR","MARB","MARC","MARL","MARS","BIRT","CHR","DEAT","BURI","CREM","ADOP","DSCR","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI");
	$irec .= "\r\n1";
//	$ft = preg_match_all("/1\s(_?\w+)\s/", $irec, $match);
	$ft = preg_match_all("/1\s(\w+)\s/", $irec, $match);
	for($i=0; $i<$ft; $i++){
		$sfact = $match[1][$i];
		$sfact = trim($sfact);
		if (in_array($sfact, $cleanup_facts)) {
			$srchstr = "/1\s".$sfact."\sY\r\n2/";
			$replstr = "1 ".$sfact."\r\n2";
			$srchstr2 = "/1\s".$sfact."(.{0,1})\r\n2/";
			$srchstr = "/1\s".$sfact."\sY\r\n2/";
			$srchstr3 = "/1\s".$sfact."\sY\r\n1/";
			$irec = preg_replace($srchstr,$replstr,$irec);
			if (preg_match($srchstr2,$irec)){
				$irec = preg_replace($srchstr3,"1",$irec);
			}
		}
	}
	$irec=substr($irec,0,-3);
	return $irec;
}

// ************************************************* START OF MULTIMEDIA FUNCTIONS ********************************* //
/**
 * find the highlighted media object for a gedcom entity
 *
 * New rules for finding primary picture and using thumbnails either under
 * the thumbs directory or with OBJE's with _THUM:
 * - skip files that have _PRIM/_THUM N
 * - default to first (existing) files
 * - first _PRIM and _THUM with Y override defaults
 * @param string $pid the individual, source, or family id
 * @param string $indirec the gedcom record to look in
 * @return array an object array with indexes "thumb" and "file" for thumbnail and filename
 */
function find_highlighted_object($pid, $indirec) {
	global $MEDIA_DIRECTORY, $GEDCOM, $MEDIA_DIRECTORY_LEVELS, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_EXTERNAL;
	if (!showFactDetails("OBJE", $pid)) return false;
	$objects = array();
	//-- first find find all of the objects and put them into the object array structure
	$opos1 = strpos($indirec, "1 OBJE");
	while($opos1) {
		$object = array();
		$object["file"] = "";
		$object["thumb"] = "";
		$object["PRIM"] = "";
		$object["THUM"] = "";
		$opos2 = strpos($indirec, "\n1", $opos1+1);
		if ($opos2) $objerec = substr($indirec, $opos1, $opos2-$opos1);
		else $objerec = substr($indirec, $opos1);
		$ct = preg_match("/\d _PRIM (.*)/", $objerec, $match);
		if ($ct>0) $object["PRIM"] = trim($match[1]);
		$ct = preg_match("/\d _THUM (.*)/", $objerec, $match);
		if ($ct>0) $object["THUM"] = trim($match[1]);
		$nt = preg_match("/OBJE @(.*)@/", $objerec, $nmatch);
		if ($nt>0) {
			$objerec = find_gedcom_record($nmatch[1]);
		}
		$ft = preg_match("/\d _*FILE (.*)/", $objerec, $amatch);
		if ($ft>0) {
			$fullpath = trim($amatch[1]);
			$filename = "";
			if (strstr( $fullpath, "://")) {
				$filename=$fullpath;
				$image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
				$path_end=substr($fullpath, strlen($fullpath)-5);
				$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
				if ($MEDIA_EXTERNAL && in_array($type, $image_type)) {
					//$thumbnail=trim($fullpath);
					$thumbnail = $MEDIA_DIRECTORY."thumbs/".extract_filename($filename);
				}
				else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
				$object["thumb"] = $thumbnail;
				$object["file"] = $filename;
			}
			else if (stristr( $fullpath, "mailto:")===false) {
				$filename = extract_filename($fullpath);
				$thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
				$thumbnail = trim($thumbnail);
				$filename = $MEDIA_DIRECTORY.$filename;
				$filename = trim($filename);
				if (file_exists(filename_decode($filename))) {
					$object["thumb"] = $thumbnail;
					$object["file"] = $filename;
				}
			}
		}
		$objects[] = $object;
		$opos1 = strpos($indirec, "1 OBJE", $opos1+1);
	}
	//-- if we didn't find any objects return false
	if (count($objects)==0) return false;

	//-- now loop through the objects to find the correct highlight based on the logic defined above
	//--  the default highlighted object is the first one found
	$highlight = $objects[0];
	foreach($objects as $i=>$object) {
		if ($object["THUM"]=="Y") {
			$object["thumb"] = $object["file"];
			$highlight = $object;
		}
		else if (($object["PRIM"]=="Y")&&($highlight["PRIM"]!="Y")) $highlight = $object;
	}
	if (!empty($highlight["file"]) && (($highlight["PRIM"]!="N") || ($highlight["THUM"]!="N"))) {
		if (!file_exists(filename_decode($highlight["thumb"])) && $highlight["THUM"]!="Y") {
			if (is_writable($MEDIA_DIRECTORY."thumbs")) generate_thumbnail($highlight["file"], $highlight["thumb"]);
		}
		return $highlight;
	}
	return false;
}

/**
 * get the full file path
 *
 * get the file path from a multimedia gedcom record
 * @param string $mediarec a OBJE subrecord
 * @return the fullpath from the FILE record
 */
function extract_fullpath($mediarec) {
	preg_match("/(\d) _*FILE (.*)/", $mediarec, $amatch);
	if (empty($amatch[2])) return "";
	$level = trim($amatch[1]);
	$fullpath = trim($amatch[2]);
	$filerec = get_sub_record($level, $amatch[0], $mediarec);
	$fullpath .= get_cont($level+1, $filerec);
	return $fullpath;
}

/**
 * get the relative filename for a media item
 *
 * gets the relative file path from the full media path for a media item.  checks the
 * <var>$MEDIA_DIRECTORY_LEVELS</var> to make sure the directory structure is maintained.
 * @param string $fullpath the full path from the media record
 * @return string a relative path that can be appended to the <var>$MEDIA_DIRECTORY</var> to reference the item
 */
function extract_filename($fullpath) {
	global $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;
	$filename="";
	$regexp = "'[/\\\]'";
	$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
	$repl = "";
	if (!strstr($fullpath, "://")) $nomedia = stripcslashes(preg_replace($srch, $repl, $fullpath));
	else $nomedia = $fullpath;
	$ct = preg_match($regexp, $nomedia, $match);
	if ($ct>0) {
		$subelements = preg_split($regexp, $nomedia);
		$subelements = array_reverse($subelements);
		$max = $MEDIA_DIRECTORY_LEVELS;
		if ($max>=count($subelements)) $max=count($subelements)-1;
		for($s=$max; $s>=0; $s--) {
			if ($s!=$max) $filename = $filename."/".$subelements[$s];
			else $filename = $subelements[$s];
		}
	}
	else $filename = $nomedia;
	//print "$filename<br />\n";
	return $filename;
}

//-- This function finds and returns all of the media objects in a given gedcom record
function find_media_in_record($gedrec) {
	global $medialist, $MEDIA_DIRECTORY, $ct, $PGV_IMAGE_DIR, $PGV_IMAGES, $foundlist, $medialinks, $MEDIA_EXTERNAL;

	$pos1=0;
	while($pos1 = strpos($gedrec, " OBJE", $pos1)) {
		//-- get the media sub record from the main gedcom record
		while(($pos1>0)&&($gedrec[$pos1]!="\n")) $pos1--;
		if ($pos1>0) $pos1++;
		$level = $gedrec[$pos1];
		$pos2 = strpos($gedrec, "\n$level ", $pos1+5);
		if ($pos2!==false) {
			$mediarec = substr($gedrec, $pos1, $pos2-$pos1);
			$pos1 = $pos2;
		}
		else {
			$mediarec = substr($gedrec, $pos1);
			$pos1 = strlen($gedrec);
		}
		//-- search if it is an embedded or linked media object
		$embed = preg_match("/(\d) _*FILE (.*)/", $mediarec, $embmatch);
		if ($embed==0) {
			//-- if it is a linked object then store a reference to this individual/family in the
			//-- $medialinks array
			$c2t = preg_match("/@(.*)@/", $mediarec, $match);
			if ($c2t>0) {
				$oid = $match[1];
				$tt = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
				if ($tt>0) $id = $match[1];
 				else $id=$ct;
				$type = trim($match[2]);
				if (!isset($medialinks)) $medialinks = array();
				if (!isset($medialinks[$oid])) $medialinks[$oid] = array();
				$medialinks[$oid][$id] = $type;
			}
		}
		else {
			//-- if it is an embedded object then get the filename from it
			$level = $embmatch[1];
			$tt = preg_match("/\d TITL (.*)/", $mediarec, $match);
			$fullpath = extract_fullpath($mediarec);
			$filename = "";
			if ((strstr( $fullpath, "://"))||(strstr( $fullpath, "mailto:"))) {
				$filename=$fullpath;
			    $image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
				$path_end=substr($fullpath, strlen($fullpath)-5);
				$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
				if ($MEDIA_EXTERNAL && in_array($type, $image_type)) {
					//$thumbnail=trim($fullpath);
					$thumbnail = $MEDIA_DIRECTORY."thumbs/".extract_filename($filename);
				}
				else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
			}
			else {
				$filename = extract_filename($fullpath);
				$thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
				$thumbnail = trim($thumbnail);
				$filename = $MEDIA_DIRECTORY.$filename;
				$filename = trim($filename);
			}
			if ($tt>0) $title = trim($match[1]);
			else $title="";
			if (empty($title)) $title = $filename;
			$isprim="N";
			$isthumb="N";
			$pt = preg_match("/\d _PRIM (.*)/", $mediarec, $match);
			if ($pt>0) $isprim = trim($match[1]);
			$pt = preg_match("/\d _THUM (.*)/", $mediarec, $match);
			if ($pt>0) $isthumb = trim($match[1]);
			$linked = preg_match("/0 @(.*)@ OBJE/", $mediarec, $match);
			if ($linked>0) {
				$linkid = trim($match[1]);
				if (isset($medialinks[$linkid])) $links = $medialinks[$linkid];
				else $links = array();
			}
			else {
				$tt = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
				if ($tt>0) $id = $match[1];
				else $id=$ct;
				$type = trim($match[2]);
				if ((isset($foundlist[$filename]))&&(isset($medialist[$foundlist[$filename]]["link"]))) {
					$links = $medialist[$foundlist[$filename]]["link"];
				}
				else $links = array();
				$links[$id] = $type;
			}
			if (!isset($foundlist[$filename])) {
				$media = array();
				$media["file"] = $filename;
				$media["thumb"] = $thumbnail;
				$media["title"] = $title;
				$media["gedcom"] = $mediarec;
				$media["level"] = $level;
				$media["THUM"] = $isthumb;
				$media["PRIM"] = $isprim;
				$medialist[$ct]=$media;
				$foundlist[$filename] = $ct;
				$ct++;
			}
			$medialist[$foundlist[$filename]]["link"]=$links;
		}
	}
}

/**
 * function to generate a thumbnail image
 * @param string $filename
 * @param string $thumbnail
 */
function generate_thumbnail($filename, $thumbnail) {
	global $MEDIA_DIRECTORY;

	if (file_exists($thumbnail)) return false;
	if (!is_writable($MEDIA_DIRECTORY."thumbs")) return false;
	$ext = "";
	$ct = preg_match("/\.([^\.]+)$/", $filename, $match);
	if ($ct>0) {
		$ext = strtolower(trim($match[1]));
	}
	if ($ext!='jpg' && $ext!='jpeg' && $ext!='gif' && $ext!='png') return false;
	
	if (!strstr($filename, "://")) {
		$imgsize = @getimagesize($filename);
		//-- check if file is small enough to be its own thumbnail
		if (($imgsize[0]<150)&&($imgsize[1]<150)) {
			@copy($filename, $thumbnail);
			return true;
		}
	}
	else {
		$fp = fopen(preg_replace("/ /", "%20", $filename), "rb");
		if ($fp===false) return false;
		$conts = "";
		while(!feof($fp)) {
			$conts .= fread($fp, 4098);
		}
		fclose($fp);
		$fp = fopen($thumbnail, "wb");
		fwrite($fp, $conts);
		fclose($fp);
		$thumbnail = preg_replace("/%20/", " ", $thumbnail);
		$imgsize = @getimagesize($filename);
		if ($imgsize===false) return false;
		if (($imgsize[0]<150)&&($imgsize[1]<150)) return true;
	}

	$width = 100;
	$height = round($imgsize[1] * ($width/$imgsize[0]));
	if ($ext=="gif") {
		if (function_exists("imagecreatefromgif") && function_exists("imagegif")) {
			$im = imagecreatefromgif($filename);
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagegif($new, $thumbnail);
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}
	else if ($ext=="jpg" || $ext=="jpeg") {
		if (function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")) {
			$im = imagecreatefromjpeg($filename);
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagejpeg($new, $thumbnail);
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}
	else if ($ext=="png") {
		if (function_exists("imagecreatefrompng") && function_exists("imagepng")) {
			$im = imagecreatefrompng($filename);
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagepng($new, $thumbnail);
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}
		
	return false;
}

// ************************************************* START OF SORTING FUNCTIONS ********************************* //
/**
 * Function to sort GEDCOM fact tags based on their tanslations
 */
function factsort($a, $b) {
   global $factarray;

   return stringsort($factarray[$a], $factarray[$b]);
}
/**
 * String sorting function
 * @param string $a
 * @param string $b
 * @return int negative numbers sort $a first, positive sort $b first
 */
function stringsort($aname, $bname) {
	global $LANGUAGE, $alphabet, $CHARACTER_SET;

	$alphabet = getAlphabet();

	if (is_array($aname)) debug_print_backtrace();

	//-- get the name lengths
	$alen = strlen($aname);
	$blen = strlen($bname);

	//-- loop through the characters in the string and if we find one that is different between the strings
	//-- return the difference
	$hungarianex = array("CS","DZ","GY","LY","NY","SZ","TY","ZS","DZS");
	$danishex = array("OE", "AE", "AA");
	for($i=0; ($i<$alen)&&($i<$blen); $i++) {
		if ($LANGUAGE == "hungarian" && $i==0){
			$aletter = substr($aname, $i, 3);
			if (strtoupper($aletter) == "DZS");
			else $aletter = substr($aname, $i, 2);
			if (in_array(strtoupper($aletter), $hungarianex));
			else $aletter = $aname{$i};

			$bletter = substr($bname, $i, 3);
			if (strtoupper($bletter) == "DZS");
			else $bletter = substr($bname, $i, 2);
			if (in_array(strtoupper($bletter), $hungarianex));
			else $bletter = $bname{$i};
		}
		else if (($LANGUAGE == "danish" || $LANGUAGE == "norwegian")){
			$aletter = substr($aname, $i, 2);
			if (in_array(strtoupper($aletter), $danishex)) {
				if (strtoupper($aletter) == "AA") {
					if ($aletter == "aa") $aname=substr_replace($aname, "å", $i, 2);
					else $aname=substr_replace($aname, "Å", $i, 2);
				}
				else if (strtoupper($aletter) == "OE") {
					if ($i==0 || $aletter=="Oe") $aname=substr_replace($aname, "Ø", $i, 2);
				}
				else if (strtoupper($aletter) == "AE") {
					if ($aletter == "ae") $aname=substr_replace($aname, "æ", $i, 2);
					else $aname=substr_replace($aname, "Æ", $i, 2);
				}
			}
			$aletter = substr($aname, $i, 1);

			$bletter = substr($bname, $i, 2);
			if (in_array(strtoupper($bletter), $danishex)) {
				if (strtoupper($bletter) == "AA") {
					if ($bletter == "aa") $bname=substr_replace($bname, "å", $i, 2);
					else $bname=substr_replace($bname, "Å", $i, 2);
				}
				else if (strtoupper($bletter) == "OE") {
					if ($i==0 || $bletter=="Oe") $bname=substr_replace($bname, "Ø", $i, 2);
				}
				else if (strtoupper($bletter) == "AE") {
					if ($bletter == "ae") $bname=substr_replace($bname, "æ", $i, 2);
					else $bname=substr_replace($bname, "Æ", $i, 2);
				}
			}
			$bletter = substr($bname, $i, 1);
		}
		else {
			$aletter = substr($aname, $i, 1);
			$bletter = substr($bname, $i, 1);
		}
		if ($CHARACTER_SET=="UTF-8") {
			$ord = ord($aletter);
			if ($ord==92 || $ord==195 || $ord==196 || $ord==197 || $ord==206 || $ord==207 || $ord==208 || $ord==209 || $ord==214 || $ord==215 || $ord==216 || $ord==217 || $ord==218 || $ord==219){
				$aletter = stripslashes(substr($aname, $i, 2));
			}
			else if ($ord==228 || $ord==229 || $ord == 230 || $ord==232 || $ord==233){
				$aletter = substr($aname, $i, 3);
			}
			else if (strlen($aletter) == 1) $aletter = strtoupper($aletter);

			$ord = ord($bletter);
			if ($ord==92 || $ord==195 || $ord==196 || $ord==197 || $ord==206 || $ord==207 || $ord==208 || $ord==209 || $ord==214 || $ord==215 || $ord==216 || $ord==217 || $ord==218 || $ord==219){
				$bletter = stripslashes(substr($bname, $i, 2));
			}
			else if ($ord==228 || $ord==229 || $ord == 230 || $ord==232 || $ord==233){
				$bletter = substr($bname, $i, 3);
			}
			else if (strlen($bletter) == 1) $bletter = strtoupper($bletter);
		}

		if ($aletter!=$bletter) {
			//-- get the position of the letter in the alphabet string
			$apos = strpos($alphabet, $aletter);
			//print $aletter."=".$apos." ";
			$bpos = strpos($alphabet, $bletter);
			//print $bletter."=".$bpos." ";
			if ($LANGUAGE == "hungarian" && $i==0){ // Check for combination of letters not in the alphabet
				if ($apos==0 || $bpos==0){			// (see array hungarianex)
					$lettera=strtoupper($aletter);
					if (in_array($lettera, $hungarianex)) {
						if ($apos==0) $apos = (strpos($alphabet, substr($lettera,0,1))*3)+(strlen($aletter)>2?2:1);
					}
					else $apos = $apos*3;
					$letterb=strtoupper($bletter);
					if (in_array($letterb, $hungarianex)) {
						if ($bpos==0) $bpos = (strpos($alphabet, substr($letterb,0,1))*3)+(strlen($bletter)>2?2:1);
					}
					else $bpos = $bpos*3;
				}
			}

			if (($bpos!==false)&&($apos===false)) return -1;
			if (($bpos===false)&&($apos!==false)) return 1;
			if (($bpos===false)&&($apos===false)) return ord($aletter)-ord($bletter);
			//print ($apos-$bpos)."<br />";
			if ($apos!=$bpos) return ($apos-$bpos);
		}
	}

	//-- if we made it through the loop then check if one name is longer than the
	//-- other, the shorter one should be first
	if ($alen!=$blen) return ($alen-$blen);

	//-- the strings are exactly the same so return 0
	return 0;
}

/**
 * User Name comparison Function
 *
 * This function just needs to call the itemsort function on the fullname
 * field of the array
 * @param array $a first user array
 * @param array $b second user array
 * @return int negative numbers sort $a first, positive sort $b first
 */
function usersort($a, $b) {
	return stringsort($a["fullname"], $b["fullname"]);
}

/**
 * sort arrays or strings
 *
 * this function is called by the uasort PHP function to compare two items and tell which should be
 * sorted first.  It uses the language alphabets to create a string that will is used to compare the
 * strings.  For each letter in the strings, the letter's position in the alphabet string is found.
 * Whichever letter comes first in the alphabet string should be sorted first.
 * @param array $a first item
 * @param array $b second item
 * @return int negative numbers sort $a first, positive sort $b first
 */
function itemsort($a, $b) {
	if (isset($a["name"])) $aname = sortable_name_from_name($a["name"]);
	else if (isset($a["names"])) $aname = sortable_name_from_name($a["names"][0][0]);
	else if (is_array($a)) $aname = sortable_name_from_name($a[0]);
	else $aname=$a;
	if (isset($b["name"])) $bname = sortable_name_from_name($b["name"]);
	else if (isset($b["names"])) $bname = sortable_name_from_name($b["names"][0][0]);
	else if (is_array($b)) $bname = sortable_name_from_name($b[0]);
	else $bname=$b;

	$aname = strip_prefix($aname);
	$bname = strip_prefix($bname);
	return stringsort($aname, $bname);
}

//-- comparison function for usort
//-- used for index mode
function lettersort($a, $b) {
	return stringsort($a["letter"], $b["letter"]);
}

/**
 * compare two fact records by date
 *
 * Compare facts function is used by the usort PHP function to sort fact baseds on date
 * it parses out the year and if the year is the same, it creates a timestamp based on
 * the current year and the month and day information of the fact
 * @param mixed $a an array with the fact record at index 1 or just a string with the factrecord
 * @param mixed $b an array with the fact record at index 1 or just a string with the factrecord
 * @return int -1 if $a should be sorted first, 0 if they are the same, 1 if $b should be sorted first
 */
function compare_facts($a, $b) {
	global $factarray, $pgv_lang, $ASC, $IGNORE_YEAR, $IGNORE_FACTS, $DEBUG, $USE_RTL_FUNCTIONS;

	if (!isset($ASC)) $ASC = 0;
	if (!isset($IGNORE_YEAR)) $IGNORE_YEAR = 0;
	if (!isset($IGNORE_FACTS)) $IGNORE_FACTS = 0;

	$adate=0;
	$bdate=0;

	$bef = -1;
	$aft = 1;
	if ($ASC) {
		$bef = 1;
		$aft = -1;
	}

	if (is_array($a)) $arec = $a[1];
	else $arec = $a;
	if (is_array($b)) $brec = $b[1];
	else $brec = $b;
	if ($DEBUG) print "\n<br />".substr($arec,0,6)."==".substr($brec,0,6)." ";

	if (!$IGNORE_FACTS) {
		$ft = preg_match("/1\s(\w+)(.*)/", $arec, $match);
		if ($ft>0) $afact = $match[1];
		else $afact="";
		$afact = trim($afact);

		$ft = preg_match("/1\s(\w+)(.*)/", $brec, $match);
		if ($ft>0) $bfact = $match[1];
		else $bfact="";
		$bfact = trim($bfact);

		//-- make sure CHAN facts are displayed at the end of the list
		if ($afact=="CHAN" && $bfact!="CHAN") return $aft;
		if ($afact!="CHAN" && $bfact=="CHAN") return $bef;

		//-- BIRT at the top of the list
		if ($afact=="BIRT" && $bfact!="BIRT") return $bef;
		if ($afact!="BIRT" && $bfact=="BIRT") return $aft;

		//-- DEAT before BURI
		if ($afact=="DEAT" && $bfact=="BURI") return $bef;
		if ($afact=="BURI" && $bfact=="DEAT") return $aft;

		//-- DEAT before CREM
		if ($afact=="DEAT" && $bfact=="CREM") return $bef;
		if ($afact=="CREM" && $bfact=="DEAT") return $aft;

		//-- group address related data together
		$addr_group = array("ADDR"=>1,"PHON"=>2,"EMAIL"=>3,"FAX"=>4,"WWW"=>5);
		if (isset($addr_group[$afact]) && isset($addr_group[$bfact])) {
			return $addr_group[$afact]-$addr_group[$bfact];
		}
		if (isset($addr_group[$afact]) && !isset($addr_group[$bfact])) {
			return $aft;
		}
		if (!isset($addr_group[$afact]) && isset($addr_group[$bfact])) {
			return $bef;
		}
	}
	
	$cta = preg_match("/2 DATE (.*)/", $arec, $match);
	if ($cta>0) $adate = parse_date(trim($match[1]));
	$ctb = preg_match("/2 DATE (.*)/", $brec, $match);
	if ($ctb>0) $bdate = parse_date(trim($match[1]));
	//-- DEAT after any other fact if one date is missing
	if ($cta==0 || $ctb==0) {
		if (isset($afact)) {
			if ($afact=="BURI") return $aft;
			if ($afact=="DEAT") return $aft;
			if ($afact=="SLGC") return $aft;
			if ($afact=="SLGS") return $aft;
			if ($afact=="BAPL") return $aft;
			if ($afact=="ENDL") return $aft;
		}
		if (isset($bfact)) {
			if ($bfact=="BURI") return $bef;
			if ($bfact=="DEAT") return $bef;
			if ($bfact=="SLGC") return $bef;
			if ($bfact=="SLGS") return $bef;
			if ($bfact=="BAPL") return $bef;
			if ($bfact=="ENDL") return $bef;
		}
	}

	//-- check if both had a date
	if($cta<$ctb) return $aft;
	if($cta>$ctb) return $bef;
	//-- neither had a date so sort by fact name
	if(($cta==0)&&($ctb==0)) {
		if (isset($afact)) {
			if ($afact=="EVEN" || $afact=="FACT") {
				$ft = preg_match("/2 TYPE (.*)/", $arec, $match);
				if ($ft>0) $afact = trim($match[1]);
			}
		}
		else $afact = "";
		if (isset($bfact)) {
			if ($bfact=="EVEN" || $bfact=="FACT") {
				$ft = preg_match("/2 TYPE (.*)/", $brec, $match);
				if ($ft>0) $bfact = trim($match[1]);
			}
		}
		else $bfact = "";
		if (isset($factarray[$afact])) $afact = $factarray[$afact];
		else if (isset($pgv_lang[$afact])) $afact = $pgv_lang[$afact];
		if (isset($factarray[$bfact])) $bfact = $factarray[$bfact];
		else if (isset($pgv_lang[$bfact])) $bfact = $pgv_lang[$bfact];
		return stringsort($afact, $bfact);
	}
	if ($IGNORE_YEAR) {
    // Calculate Current year Gregorian date for Hebrew date 		
        if ($USE_RTL_FUNCTIONS && isset($adate[0]["ext"]) && strstr($adate[0]["ext"], "#DHEBREW")!==false) $adate = jewishGedcomDateToCurrentGregorian($adate);
		if ($USE_RTL_FUNCTIONS && isset($bdate[0]["ext"]) && strstr($bdate[0]["ext"], "#DHEBREW")!==false) $bdate = jewishGedcomDateToCurrentGregorian($bdate);
	}
	else {
    // Calculate Original year Gregorian date for Hebrew date 		
		if ($USE_RTL_FUNCTIONS && isset($adate[0]["ext"]) && strstr($adate[0]["ext"], "#DHEBREW")!==false) $adate = jewishGedcomDateToGregorian($adate);
		if ($USE_RTL_FUNCTIONS && isset($bdate[0]["ext"]) && strstr($bdate[0]["ext"], "#DHEBREW")!==false) $bdate = jewishGedcomDateToGregorian($bdate);
    }
    
if ($DEBUG) print $adate[0]["year"]."==".$bdate[0]["year"]." ";
	if ($adate[0]["year"]==$bdate[0]["year"] || $IGNORE_YEAR) {
		// Check month
		$montha = $adate[0]["mon"];
		$monthb = $bdate[0]["mon"];

		if ($montha == $monthb) {
		// Check day
			$newa = $adate[0]["day"]." ".$adate[0]["month"]." ".date("Y");
			$newb = $bdate[0]["day"]." ".$bdate[0]["month"]." ".date("Y");
			$astamp = strtotime($newa);
			$bstamp = strtotime($newb);
			if ($astamp==$bstamp) {
				if ($IGNORE_YEAR && ($adate[0]["year"]!=$bdate[0]["year"])) return ($adate[0]["year"] < $bdate[0]["year"]) ? $aft : $bef;
				$cta = preg_match("/[2-3] TIME (.*)/", $arec, $amatch);
				$ctb = preg_match("/[2-3] TIME (.*)/", $brec, $bmatch);
				//-- check if both had a time
				if($cta<$ctb) return $aft;
				if($cta>$ctb) return $bef;
				//-- neither had a time so return 0;
				if(($cta==0)&&($ctb==0)) return 0;

				$atime = trim($amatch[1]);
				$btime = trim($bmatch[1]);
				$astamp = strtotime($newa." ".$atime);
				$bstamp = strtotime($newb." ".$btime);
				if ($astamp==$bstamp) return 0;
			}
			return ($astamp < $bstamp) ? $bef : $aft;
		}
		else return ($montha < $monthb) ? $bef : $aft;
	}
if ($DEBUG) print (($adate[0]["year"] < $bdate[0]["year"]) ? $bef : $aft)." ";
	return ($adate[0]["year"] < $bdate[0]["year"]) ? $bef : $aft;
}

/**
 * fact date sort
 *
 * compare individuals by a fact date
 */
function compare_date($a, $b) {
	global $sortby;

	$tag = "BIRT";
	if (!empty($sortby)) $tag = $sortby;
	$abirt = get_sub_record(1, "1 $tag", $a["gedcom"]);
	$bbirt = get_sub_record(1, "1 $tag", $b["gedcom"]);
	$c = compare_facts($abirt, $bbirt);
	if ($c==0) return itemsort($a, $b);
	else return $c;
}

function gedcomsort($a, $b) {
	$aname = str2upper($a["title"]);
	$bname = str2upper($b["title"]);

	return stringsort($aname, $bname);
}

// ************************************************* START OF MISCELLANIOUS FUNCTIONS ********************************* //
/**
 * Get relationship between two individuals in the gedcom
 *
 * function to calculate the relationship between two people it uses hueristics based on the
 * individuals birthdate to try and calculate the shortest path between the two individuals
 * it uses a node cache to help speed up calculations when using relationship privacy
 * this cache is indexed using the string "$pid1-$pid2"
 * @param string $pid1 the ID of the first person to compute the relationship from
 * @param string $pid2 the ID of the second person to compute the relatiohip to
 * @param bool $followspouse whether to add spouses to the path
 * @param int $maxlenght the maximim length of path
 * @param bool $ignore_cache enable or disable the relationship cache
 * @param int $path_to_find which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 */
function get_relationship($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $TIME_LIMIT, $start_time, $pgv_lang, $NODE_CACHE, $NODE_CACHE_LENGTH, $USE_RELATIONSHIP_PRIVACY, $pgv_changes, $GEDCOM;

	$pid1 = strtoupper($pid1);
	$pid2 = strtoupper($pid2);
	if (isset($pgv_changes[$pid2."_".$GEDCOM]) && userCanEdit(getUserName())) $indirec = find_record_in_file($pid2);
	else $indirec = find_person_record($pid2);
	//-- check the cache
	if ($USE_RELATIONSHIP_PRIVACY && !$ignore_cache) {
		if(isset($NODE_CACHE["$pid1-$pid2"])) {
			if ($NODE_CACHE["$pid1-$pid2"]=="NOT FOUND") return false;
			if (($maxlength==0)||(count($NODE_CACHE["$pid1-$pid2"]["path"])-1<=$maxlength)) return $NODE_CACHE["$pid1-$pid2"];
			else return false;
		}
		//-- check the cache for person 2's children
		$famids = array();
		$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famids[$i]=$match[$i][1];
		}
		foreach($famids as $indexval => $fam) {
			$famrec = find_family_record($fam);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$child = $match[$i][1];
				if (!empty($child)){
					if(isset($NODE_CACHE["$pid1-$child"])) {
						if (($maxlength==0)||(count($NODE_CACHE["$pid1-$child"]["path"])+1<=$maxlength)) {
							$node1 = $NODE_CACHE["$pid1-$child"];
							if ($node1!="NOT FOUND") {
								$node1["path"][] = $pid2;
								$node1["pid"] = $pid2;
								$ct = preg_match("/1 SEX F/", $indirec, $match);
								if ($ct>0) $node1["relations"][] = "mother";
								else $node1["relations"][] = "father";
							}
							$NODE_CACHE["$pid1-$pid2"] = $node1;
							if ($node1=="NOT FOUND") return false;
							return $node1;
						}
						else return false;
					}
				}
			}
		}

		if ((!empty($NODE_CACHE_LENGTH))&&($maxlength>0)) {
			if ($NODE_CACHE_LENGTH>=$maxlength) return false;
		}
	}
	//-- end cache checking

	//-- get the birth year of p2 for calculating heuristics
	$birthrec = get_sub_record(1, "1 BIRT", $indirec);
	$byear2 = -1;
	if ($birthrec!==false) {
		$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
		if ($dct>0) $byear2 = $match[1];
	}
	if ($byear2==-1) {
		$numfams = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for($j=0; $j<$numfams; $j++) {
			// Get the family record
			if (isset($pgv_changes[$fmatch[$j][1]."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fmatch[$j][1]);
			else $famrec = find_family_record($fmatch[$j][1]);

			// Get the set of children
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $cmatch, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				// Get each child's record
				if (isset($pgv_changes[$cmatch[$i][1]."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($cmatch[$i][1]);
				else $childrec = find_person_record($cmatch[$i][1]);
				$birthrec = get_sub_record(1, "1 BIRT", $childrec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $bmatch);
					if ($dct>0) $byear2 = $bmatch[1]-25;
				}
			}
		}
	}
	//-- end of approximating birth year

	//-- current path nodes
	$p1nodes = array();
	//-- ids visited
	$visited = array();

	//-- set up first node for person1
	$node1 = array();
	$node1["path"] = array();
	$node1["path"][] = $pid1;
	$node1["length"] = 0;
	$node1["pid"] = $pid1;
	$node1["relations"] = array();
	$node1["relations"][] = "self";
	$p1nodes[] = $node1;

	$visited[$pid1] = true;

	$found = false;
	$count=0;
	while(!$found) {
		//-- the following 2 lines ensure that the user can abort a long relationship calculation
		//-- refer to http://www.php.net/manual/en/features.connection-handling.php for more
		//-- information about why these lines are included
		if (headers_sent()) {
			print " ";
			if ($count%100 == 0) flush();
		}
		$count++;
		$end_time = getmicrotime();
		$exectime = $end_time - $start_time;
		if (($TIME_LIMIT>1)&&($exectime > $TIME_LIMIT-1)) {
			print "<span class=\"error\">".$pgv_lang["timeout_error"]."</span>\n";
			return false;
		}
		if (count($p1nodes)==0) {
			if ($maxlength!=0) {
				if (!isset($NODE_CACHE_LENGTH)) $NODE_CACHE_LENGTH = $maxlength;
				else if ($NODE_CACHE_LENGTH<$maxlength) $NODE_CACHE_LENGTH = $maxlength;
			}
			if (headers_sent()) {
				print "\n<!-- Relationship $pid1-$pid2 NOT FOUND | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
				print_execution_stats();
				print "-->\n";
			}
			$NODE_CACHE["$pid1-$pid2"] = "NOT FOUND";
			return false;
		}
		//-- search the node list for the shortest path length
		$shortest = -1;
		foreach($p1nodes as $index=>$node) {
			if ($shortest == -1) $shortest = $index;
			else {
				$node1 = $p1nodes[$shortest];
				if ($node1["length"] > $node["length"]) $shortest = $index;
			}
		}
		if ($shortest==-1) return false;
		$node = $p1nodes[$shortest];
		if (($maxlength==0)||(count($node["path"])<=$maxlength)) {
			if ($node["pid"]==$pid2) {
			}
			else {
				//-- hueristic values
				$fatherh = 1;
				$motherh = 1;
				$siblingh = 2;
				$spouseh = 2;
				$childh = 3;

				//-- generate heuristic values based of the birthdates of the current node and p2
				if (isset($pgv_changes[$node["pid"]."_".$GEDCOM]) && userCanEdit(getUserName())) $indirec = find_record_in_file($node["pid"]);
				else $indirec = find_person_record($node["pid"]);
				$byear1 = -1;
				$birthrec = get_sub_record(1, "1 BIRT", $indirec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
					if ($dct>0) $byear1 = $match[1];
				}
				if (($byear1!=-1)&&($byear2!=-1)) {
					$yeardiff = $byear1-$byear2;
					if ($yeardiff < -140) {
						$fatherh = 20;
						$motherh = 20;
						$siblingh = 15;
						$spouseh = 15;
						$childh = 1;
					}
					else if ($yeardiff < -100) {
						$fatherh = 15;
						$motherh = 15;
						$siblingh = 10;
						$spouseh = 10;
						$childh = 1;
					}
					else if ($yeardiff < -60) {
						$fatherh = 10;
						$motherh = 10;
						$siblingh = 5;
						$spouseh = 5;
						$childh = 1;
					}
					else if ($yeardiff < -20) {
						$fatherh = 5;
						$motherh = 5;
						$siblingh = 3;
						$spouseh = 3;
						$childh = 1;
					}
					else if ($yeardiff<20) {
						$fatherh = 3;
						$motherh = 3;
						$siblingh = 1;
						$spouseh = 1;
						$childh = 5;
					}
					else if ($yeardiff<60) {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 5;
						$spouseh = 2;
						$childh = 10;
					}
					else if ($yeardiff<100) {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 10;
						$spouseh = 3;
						$childh = 15;
					}
					else {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 15;
						$spouseh = 4;
						$childh = 20;
					}
				}
				//-- check all parents and siblings of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMC\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
					if (!isset($visited[$match[$i][1]])) $famids[$i]=$match[$i][1];
				}
				foreach($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fam);
					else $famrec = find_family_record($fam);
					$parents = find_parents_in_record($famrec);
					if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
						$node1 = $node;
						$node1["length"]+=$fatherh;
						$node1["path"][] = $parents["HUSB"];
						$node1["pid"] = $parents["HUSB"];
						$node1["relations"][] = "father";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0) $path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						}
						else $visited[$parents["HUSB"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
						$node1 = $node;
						$node1["length"]+=$motherh;
						$node1["path"][] = $parents["WIFE"];
						$node1["pid"] = $parents["WIFE"];
						$node1["relations"][] = "mother";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0) $path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						}
						else $visited[$parents["WIFE"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$siblingh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "sibling";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
				//-- check all spouses and children of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
//					if (!isset($visited[$match[$i][1]])) $famids[$i]=$match[$i][1];
					$famids[$i]=$match[$i][1];
				}
				foreach($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fam);
					else $famrec = find_family_record($fam);
					if ($followspouse) {
						$parents = find_parents_in_record($famrec);
						if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["HUSB"];
							$node1["pid"] = $parents["HUSB"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$parents["HUSB"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
						if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["WIFE"];
							$node1["pid"] = $parents["WIFE"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$parents["WIFE"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$childh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "child";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	} //-- end while loop
	if (headers_sent()) {
		print "\n<!-- Relationship $pid1-$pid2 | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
		print_execution_stats();
		print "-->\n";
	}
	return $resnode;
}

/**
 * write changes
 *
 * this function writes the $pgv_changes back to the <var>$INDEX_DIRECTORY</var>/pgv_changes.php
 * file so that it can be read in and checked to see if records have been updated.  It also stores
 * old records so that they can be undone.
 * @return bool true if successful false if there was an error
 */
function write_changes() {
	global $GEDCOMS, $GEDCOM, $pgv_changes, $INDEX_DIRECTORY, $CONTACT_EMAIL, $PGV_DATABASE, $LAST_CHANGE_EMAIL;

	if (!isset($LAST_CHANGE_EMAIL)) $LAST_CHANGE_EMAIL = time();
	//-- write the changes file
	$changestext = "<?php\n\$LAST_CHANGE_EMAIL = $LAST_CHANGE_EMAIL;\n\$pgv_changes = array();\n";
	foreach($pgv_changes as $gid=>$changes) {
		if (count($changes)>0) {
			$changestext .= "\$pgv_changes[\"$gid\"] = array();\n";
			foreach($changes as $indexval => $change) {
				$changestext .= "// Start of change record.\n";
				$changestext .= "\$change = array();\n";
				$changestext .= "\$change[\"gid\"] = '".$change["gid"]."';\n";
				$changestext .= "\$change[\"gedcom\"] = '".$change["gedcom"]."';\n";
				$changestext .= "\$change[\"type\"] = '".$change["type"]."';\n";
				$changestext .= "\$change[\"status\"] = '".$change["status"]."';\n";
				$changestext .= "\$change[\"user\"] = '".$change["user"]."';\n";
				$changestext .= "\$change[\"time\"] = '".$change["time"]."';\n";
				$changestext .= "\$change[\"undo\"] = '".str_replace("\\\\'", "\\'", preg_replace("/'/", "\\'", $change["undo"]))."';\n";
				$changestext .= "// End of change record.\n";
				$changestext .= "\$pgv_changes[\"$gid\"][] = \$change;\n";
			}
		}
	}
	$changestext .= "\n"."?>\n";
	$fp = fopen($INDEX_DIRECTORY."pgv_changes.php", "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open changes file resource.  Unable to complete request.\n";
		return false;
	}
	$fw = fwrite($fp, $changestext);
	if ($fw===false) {
		print "ERROR 7: Unable to write to changes file.\n";
		fclose($fp);
		return false;
	}
	fclose($fp);
	return true;
}

/**
 * get theme names
 *
 * function to get the names of all of the themes as an array
 * it searches the themes directory and reads the name from the theme_name variable
 * in the theme.php file.
 * @return array and array of theme names and their corresponding directory
 */
function get_theme_names() {
	$themes = array();
	$d = dir("themes");
	while (false !== ($entry = $d->read())) {
		if ($entry!="." && $entry!=".." && $entry!="CVS" && is_dir("themes/$entry")) {
			$theme = array();
			$themefile = implode("", file("themes/$entry/theme.php"));
			$tt = preg_match("/theme_name\s+=\s+\"(.*)\";/", $themefile, $match);
			if ($tt>0) $themename = trim($match[1]);
			else $themename = "themes/$entry";
			$theme["name"] = $themename;
			$theme["dir"] = "themes/$entry/";
			$themes[] = $theme;
		}
	}
	$d->close();
	uasort($themes, "itemsort");
	return $themes;
}

/**
 * format a fact for calendar viewing
 *
 * @param string $factrec the fact record
 * @param string $action tells what type calendar the user is viewing
 * @param string $filter should the fact be filtered by living people etc
 * @param string $pid the gedcom xref id of the record this fact belongs to
 * @return string a html text string that can be printed
 */
function get_calendar_fact($factrec, $action, $filterof, $pid, $filterev="all") {
	global $pgv_lang, $factarray, $year, $month, $day, $TEMPLE_CODES, $CALENDAR_FORMAT, $monthtonum, $TEXT_DIRECTION, $SHOW_PEDIGREE_PLACES, $caltype;
	global $hYear, $USE_RTL_FUNCTIONS;
	$Upcoming = false;
	if ($action == "upcoming") {
		$action = "today";
		$Upcoming = true;
	}
	$skipfacts = array("CHAN", "BAPL", "SLGC", "SLGS", "ENDL");

//	$ft = preg_match("/1\s(_?\w+)\s(.*)/", $factrec, $match);
	$ft = preg_match("/1\s(\w+)(.*)/", $factrec, $match);
	if ($ft>0) $fact = $match[1];
	else return "filter";
	if (in_array($fact, $skipfacts)) return "filter";
	if ((!showFact($fact, $pid))||(!showFactDetails($fact, $pid))) return "";
	if (FactViewRestricted($pid, $factrec)) return "";
	$fact = trim($fact);
	$factref = $fact;
	if ($fact=="EVEN" || $fact=="FACT") {
		$ct = preg_match("/2 TYPE (.*)/", $factrec, $tmatch);
		if ($ct>0) {
			$factref = trim($tmatch[1]);
		    if ((!showFact($factref, $pid))||(!showFactDetails($factref, $pid))) return "";
	    }
	}

	// Use current year for age in dayview
	if ($action == "today"){
		$yearnow = getdate();
		$yearnow = $yearnow["year"];
	}
	else	{
		$yearnow = $year;
	}
	
	$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
	if ($hct>0) 
	   if ($USE_RTL_FUNCTIONS) $yearnow = $hYear;		
	
	$text = "";

	if ((!in_array($fact, $skipfacts) && !in_array($factref, $skipfacts)) || ($fact==$filterev) || ($factref==$filterev)) {
		if ($fact=="EVEN" || $fact=="FACT") {
			if ($ct>0) {
				if (isset($factarray["$factref"])) $text .= $factarray["$factref"];
				else $text .= $factref;
			}
			else $text .= $factarray[$fact];
		}
		else {
			if (isset($factarray[$fact])) $text .= $factarray[$fact];
			else $text .= $fact;
		}
		if ($filterev!="all" && $filterev!=$fact && $filterev!=$factref) return "filter";

		if ($text!="") $text=PrintReady($text);

		$ct = preg_match("/\d DATE(.*)/", $factrec, $match);
		if ($ct>0) {
			$text .= " - <span class=\"date\">".get_date_url($match[1])."</span>";
//			$yt = preg_match("/ (\d\d\d\d)/", $match[1], $ymatch);
			$yt = preg_match("/ (\d\d\d\d|\d\d\d)/", $match[1], $ymatch);
			if ($yt>0) {
				$age = $yearnow - $ymatch[1];
				$yt2 = preg_match("/(...) (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
				if ($yt2>0) {
					if (isset($monthtonum[strtolower(trim($bmatch[1]))])) {
						$emonth = $monthtonum[strtolower(trim($bmatch[1]))];
						if (!$Upcoming && ($emonth<$monthtonum[strtolower($month)])) $age--;
						$bt = preg_match("/(\d+) ... (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
						if ($bt>0) {
							$edate = trim($bmatch[1]);
							if (!$Upcoming && ($edate<$day)) $age--;
						}
					}
				}
				$yt3 = preg_match("/(.+) ... (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
				if ($yt3>0) {
					if (!$Upcoming && ($bmatch[1]>$day)) $age--;
				}
				if (($filterof=="recent")&&($age>100)) return "filter";
				// Limit facts to before the given year in monthview
				if (($age<0) && ($action == "calendar")) return "filter";
				if ($action!='year'){
					$text .= " (" . str_replace("#year_var#", convert_number($age), $pgv_lang["year_anniversary"]).")";
				}
 				if($TEXT_DIRECTION == "rtl"){
 					$text .= "&lrm;";
 				}
			}
			if (($action=='today')||($action=='year')) {
				// -- find place for each fact
				if ($SHOW_PEDIGREE_PLACES>0) {
					$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
					if ($ct>0) {
						$text .=($action=='today'?"<br />":" ");
						$plevels = preg_split("/,/", $match[1]);
						for($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
							if (!empty($plevels[$plevel])) {
								if ($plevel>0) $text .=", ";
								$text .= PrintReady($plevels[$plevel]);
							}
						}
					}
				}

				// -- find temple code for lds facts
				$ct = preg_match("/2 TEMP (.*)/", $factrec, $match);
				if ($ct>0) {
					$tcode = $match[1];
					$tcode = trim($tcode);
					if (array_key_exists($tcode, $TEMPLE_CODES)) $text .= "<br />".$pgv_lang["temple"].": ".$TEMPLE_CODES[$tcode];
					else $text .= "<br />".$pgv_lang["temple_code"].$tcode;
				}
			}
		}
		$text .= "<br />";
	}
	if ($text=="") return "filter";
	return $text;
}

//-- this function will convert a digit number to a number in a different language
function convert_number($num) {
	global $pgv_lang, $LANGUAGE;

	if ($LANGUAGE == "chinese") {
		$numstr = "$num";
		$zhnum = "";
		//-- currently limited to numbers <10000
		if (strlen($numstr)>4) return $numstr;

		$ln = strlen($numstr);
		$numstr = strrev($numstr);
		for($i=0; $i<$ln; $i++) {
			if (($i==1)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["10"].$zhnum;
			if (($i==2)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["100"].$zhnum;
			if (($i==3)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["1000"].$zhnum;
			if (($i!=1)||($numstr{$i}!=1)) $zhnum = $pgv_lang[$numstr{$i}].$zhnum;
		}
		return $zhnum;
	}
	return $num;
}

//-- this function is a wrapper to the php mail() function so that we can change settings globally
// for more info on format="flowed" see: http://www.joeclark.org/ffaq.html
// for deatiled info on MIME (RFC 1521) email see: http://www.freesoft.org/CIE/RFC/1521/index.htm
function pgvMail($to, $subject, $message, $extraHeaders){
	global $pgv_lang, $CHARACTER_SET, $LANGUAGE, $PGV_STORE_MESSAGES, $TEXT_DIRECTION;
	$mailFormat = "plain";
	//$mailFormat = "html";
	//$mailFormat = "multipart"

	$mailFormatText = "text/plain";

	$boundry = "PGV-123454321-PGV"; //unique identifier for multipart
	$boundry2 = "PGV-123454321-PGV2";

	if($TEXT_DIRECTION == "rtl") { // needed for rtl but we can change this to a global config
		$mailFormat = "html";
	}

	if($mailFormat == "html"){
		$mailFormatText = "text/html";
	} else if($mailFormat == "multipart") {
		$mailFormatText = "multipart/related; \r\n\tboundary=\"$boundry\""; //for double display use:multipart/mixed
	} else {
		$mailFormatText = "text/plain";
	}

	$defaultExtraHeaders = "\r\nContent-type: " . $mailFormatText . ";\r\n";

	if($mailFormat != "multipart"){
		$defaultExtraHeaders .= "\tcharset=\"$CHARACTER_SET\";\r\n\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit\r\n";
	}

	if($mailFormat == "html" || $mailFormat == "multipart"){
		$defaultExtraHeaders .= "Mime-Version: 1.0\r\n";
	}

	$extraHeaders .= $defaultExtraHeaders; //add custom extra header


	if($mailFormat == "html") {
		//wrap message in html
		$htmlMessage = "";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre></body>";
		$htmlMessage .= "</html>";
		$message = $htmlMessage;
	} else if($mailFormat == "multipart"){
		//wrap message in html
		$htmlMessage = "--$boundry\r\n";
		$htmlMessage .= "Content-Type: multipart/alternative; \r\n\tboundry=--$boundry2\r\n\r\n";
		$htmlMessage = "--$boundry2\r\n";
		$htmlMessage .= "Content-Type: text/plain; \r\n\tcharset=\"$CHARACTER_SET\";\r\n\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
		$htmlMessage .= $message;
		$htmlMessage .= "\r\n\r\n--$boundry2\r\n";
		$htmlMessage .= "Content-Type: text/html; \r\n\tcharset=\"$CHARACTER_SET\";\r\n\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre>";
		$htmlMessage .= "<img src=\"cid:pgvlogo@pgvserver\" alt=\"\" style=\"border: 0px; display: block; margin-left: auto; margin-right: auto;\" />";
		$htmlMessage .= "</body>";
		$htmlMessage .= "</html>";
		$htmlMessage .= "\r\n--$boundry2--\r\n";
		$htmlMessage .= "\r\n--$boundry\r\n";
		$htmlMessage .= getPgvMailLogo();
		$htmlMessage .= "\r\n\r\n\r\n\r\n--$boundry--";
		$message = $htmlMessage;
	}
	mail($to, hex4email($subject,$CHARACTER_SET), $message, $extraHeaders);
}

function getPgvMailLogo(){
// the following is a base64 encoded PGV logo for use in html formatted email.
	$pgvLogo =
"Content-Type: image/gif;
	name=\"gedview.gif\"
Content-Transfer-Encoding: base64
Content-ID: <pgvlogo@pgvserver>
Content-Description: gedview.gif
Content-Location: gedview.gif

R0lGODlhZAAtAPcAAAMZLwUmRRIsRBo0SwctURU2UyY+UxE8ZTJHWx5GagxFehVKeyZIaC1NazdT
bSVTfT5Zc0dYaFNjc25ubmBtemRyf3V1dXt7extRgx9SgShbijdegjRkkD1rlERmhk1vj1NqgFl0
jkJwml99mWl6iUxhrFZpr0t2oFF7oVR+pV1utGx6q2N1vG18wHmJmFeApmaMr2uLqHWArHaSq32I
sHuatn+gvXiFwoCAgIODg4WEhYaEhYmGh4eIiImJiYyMjIKPmpGRkZeXl5qbm4aNroiXpJGdqYiP
tImfsouUuJacu5WhrJmksp2os5Knup+lvpWpvKChoaenp6Onraqrq6Cst6ast6qyu7Gys7O2urW4
vLu8vL6/v4ONx42WzJae05asxp2yxp+2zJyj1KWsw6W3x6yywq+2wa2zx6u4xKK6z6q6y7S6wbi9
w7K2yLS7ybm8y6Sr2Kq/06qx3Lu/0ru/3bC34rzAxbXBzb3Cy7LF1rnF0L3J1bTH2bfJ2rzM27/P
38LCw8LFysTGz8fIysvMzcXL0szO0sLO2cTS38jR2c3X39DR09PW2dTY3dnZ2t3e38HE48jL58HR
4MbU4cnW48rX5MvZ5szY5M3Z5c7Z5c3a5s/a5s/b58zQ7sra6Mzb6c7d6tLa4dfa4NPc4NTc4dDa
5NDc59Td5dTV7NLd6NPe6dDe69rc79TX8dbg5t7g4tbg6tPg7Nbh7dji69zi6N/l6tnj7Nvk7Nvk
7dvm793m7d3l7t/n7t/o79/h893o8uLk5eTm6eDn7ebo6eHo7+Tq7ebr7ejp6evs7OLp8OPr8uTr
8ebs8uDq9OLt9ubu9err8+nt8Ovv8+3u8O7v8Onu9O3t9uLk+Orr+uvw9Ovx9uzx9ezy9u7y9u/z
9+3z+e/1+vHy8vDy9PDz9fL09vX29vH0+PL1+PL2+fH2+vP3+/T3+fDw/PH2/PT0/PX4+vb5+/P5
/Pj5+vv7+/n6/Pz7/Pr8/fv+//z8/Pz9/v7+/v7+//7//////wAAACH5BAkAAP8ALAAAAABkAC0A
AAj+AP8JFLglCA8dOxIqVMjDh8OFECNKnEjxx4+FPi5S3KhQB48gWwaKhBXokTl/KFOq9GdOnLiT
K2PKnEkTpT5kyVSKC0avps+U5hoFEvnvUaCe/vYpXcrUHxYcOXJEQZYUpVKr+5Jm1Yqy39WUX7Ve
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";

return $pgvLogo;
}

/**
 * hex encode a string
 *
 * this function encodes a string in quoted_printable format
 * found at http://us3.php.net/bin2hex
 */
function hex4email ($string,$charset) {
	global $LANGUAGE;

	//-- check if the string has extended characters in it
	$str = utf8_decode($string);
	//-- if the strings are the same no conversion is necessary
	if ($str==$string) return $string;
	//-- convert to string into quoted_printable format
	$string = bin2hex ($string);
	$encoded = chunk_split($string, 2, '=');
	$encoded = preg_replace ("/=$/","",$encoded);
	$string = "=?$charset?Q?=" . $encoded . "?=";
	return $string;
}

/**
 * decode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to decode the filename
 * before it can be used on the filesystem
 */
function filename_decode($filename) {
	if (preg_match("/Win32/", $_SERVER["SERVER_SOFTWARE"])>0) return utf8_decode($filename);
	else return $filename;
}

/**
 * encode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to encode the filename
 * before it can be used in PGV
 */
function filename_encode($filename) {
	if (preg_match("/Win32/", $_SERVER["SERVER_SOFTWARE"])>0) return utf8_encode($filename);
	else return $filename;
}


//-- This function changes the used gedcom connected to a language
function change_gedcom_per_language($new_gedcom_name,$new_language_name)
{
  global $QUERY_STRING;
  global $PHP_SELF;

  $QUERY_STRING = preg_replace("/&amp;/", "&", $QUERY_STRING);
  $QUERY_STRING = preg_replace("/&&/", "&", $QUERY_STRING);
  $terms = preg_split("/&/", $QUERY_STRING);
  $vars = "";
  for ($i=0; $i<count($terms); $i++)
  {
	if (substr($terms[$i],0,7) == "gedcom=")$terms[$i]="";
	if ((!empty($terms[$i]))&&(strstr($terms[$i], "changelanguage")===false)&&(strpos($terms[$i], "NEWLANGUAGE")===false))
	{
	  $vars .= $terms[$i]."&";
	}
  }
  $QUERY_STRING = $vars;
  if (empty($QUERY_STRING))$QUERY_STRING = "GEDCOM=".$new_gedcom_name; else $QUERY_STRING = $QUERY_STRING . "&gedcom=".$new_gedcom_name;
  $QUERY_STRING = preg_replace("/&&/", "&", $QUERY_STRING);
  $_SESSION["GEDCOM"] = "GEDCOM=".$new_gedcom_name;
  $_SESSION['CLANGUAGE'] = $new_language_name;
  header("Location: ".$PHP_SELF."?".$QUERY_STRING);
  exit;
}

function getAlphabet(){
	global $ALPHABET_upper, $ALPHABET_lower, $LANGUAGE, $alphabet;

	//-- setup the language alphabet string
	if (!isset($alphabet)) {
		$alphabet = "0123456789".$ALPHABET_upper[$LANGUAGE].$ALPHABET_lower[$LANGUAGE];
		foreach ($ALPHABET_upper as $l => $upper){
			if ($l <> $LANGUAGE) $alphabet.=$upper;
		}
		foreach ($ALPHABET_lower as $l => $lower){
			if ($l <> $LANGUAGE) $alphabet.=$lower;
		}
	}
	return $alphabet;
}

/**
 * get a list of the reports in the reports directory
 *
 * When $force is false, the function will first try to read the reports list from the$INDEX_DIRECTORY."/reports.dat"
 * data file.  Otherwise the function will parse the report xml files and get the titles.
 * @param boolean $force	force the code to look in the directory and parse the files again
 * @return array 	The array of the found reports with indexes [title] [file]
 */
function get_report_list($force=false) {
	global $INDEX_DIRECTORY, $report_array, $vars, $xml_parser, $elementHandler, $LANGUAGE;

	$files = array();
	if (!$force) {
		//-- check if the report files have been cached
		if (file_exists($INDEX_DIRECTORY."/reports.dat")) {
			$reportdat = "";
			$fp = fopen($INDEX_DIRECTORY."/reports.dat", "r");
			while ($data = fread($fp, 4096)) {
				$reportdat .= $data;
			}
			fclose($fp);
			$files = unserialize($reportdat);
			foreach($files as $indexval => $file) {
				if (isset($file["title"][$LANGUAGE]) && (strlen($file["title"][$LANGUAGE])>1)) return $files;
			}
		}
	}

	//-- find all of the reports in the reports directory
	$d = dir("reports");
	while (false !== ($entry = $d->read())) {
		if (($entry!=".") && ($entry!="..") && ($entry!="CVS") && (strstr($entry, ".xml")!==false)) {
			if (!isset($files[$entry]["file"])) $files[$entry]["file"] = "reports/".$entry;
		}
	}
	$d->close();

	require_once("includes/reportheader.php");
	$report_array = array();
	if (!function_exists("xml_parser_create")) return $report_array;
	foreach($files as $file=>$r) {
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		if (file_exists($r["file"])) {
			//-- open the file
			if (!($fp = fopen($r["file"], "r"))) {
			   die("could not open XML input");
			}
			//-- read the file and parse it 4kb at a time
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
				}
			}
			fclose($fp);
			xml_parser_free($xml_parser);
			if (isset($report_array["title"]) && isset($report_array["access"]) && isset($report_array["icon"])) {
				$files[$file]["title"][$LANGUAGE] = $report_array["title"];
				$files[$file]["access"] = $report_array["access"];
				$files[$file]["icon"] = $report_array["icon"];
			}
		}
	}

	$fp = @fopen($INDEX_DIRECTORY."/reports.dat", "w");
	@fwrite($fp, serialize($files));
	@fclose($fp);

	return $files;
}

/**
 * clean up user submitted input before submitting it to the SQL query
 *
 * This function will take user submitted input string and remove any special characters
 * before they are submitted to the SQL query.
 * Examples of invalid characters are _ & ?
 * @param string $pid	The string to cleanup
 * @return string	The cleaned up string
 */
function clean_input($pid) {
	$pid = preg_replace("/[%?_]/", "", $pid);
	return $pid;
}

/**
 * get a quick-glance view of current LDS ordinances
 * @param string $indirec
 * @return string
 */
function get_lds_glance($indirec) {
	$text = "";

	$ord = get_sub_record(1, "1 BAPL", $indirec);
	if ($ord) $text .= "B";
	else $text .= "_";
	$ord = get_sub_record(1, "1 ENDL", $indirec);
	if ($ord) $text .= "E";
	else $text .= "_";
	$found = false;
	$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$famrec = find_family_record($match[$i][1]);
		if ($famrec) {
			$ord = get_sub_record(1, "1 SLGS", $famrec);
			if ($ord) {
				$found = true;
				break;
			}
		}
	}
	if ($found) $text .= "S";
	else $text .= "_";
	$ord = get_sub_record(1, "1 SLGC", $indirec);
	if ($ord) $text .= "P";
	else $text .= "_";
	return $text;
}

/**
 * Check for facts that may exist only once for a certain record type.
 * If the fact already exists in the second array, delete it from the first one.
 */
 function CheckFactUnique($uniquefacts, $recfacts, $type) {

	 foreach($recfacts as $indexval => $fact) {
		if (($type == "SOUR") || ($type == "REPO")) $factrec = $fact[0];
		if (($type == "FAM") || ($type == "INDI")) $factrec = $fact[1];
//		$ft = preg_match("/1 (_?[A-Z]{3,5})(.*)/", $factrec, $match);
		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			$key = array_search($fact, $uniquefacts);
			if ($key !== false) unset($uniquefacts[$key]);
		}
	 }
	 return $uniquefacts;
 }

/**
 * remove any custom PGV tags from the given gedcom record
 * custom tags include _PGVU and _THUM
 * @param string $gedrec	the raw gedcom record
 * @return string		the updated gedcom record
 */
function remove_custom_tags($gedrec, $remove="no") {
	if ($remove=="yes") {
		//-- remove _PGVU
		$gedrec = preg_replace("/\d _PGVU .*/", "", $gedrec);
		//-- remove _THUM
		$gedrec = preg_replace("/\d _THUM .*/", "", $gedrec);
	}
	//-- cleanup so there are not any empty lines
	$gedrec = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $gedrec);
	//-- make downloaded file DOS formatted
	$gedrec = preg_replace("/([^\r])\n/", "$1\n", $gedrec);
	return $gedrec;
}

// optional extra file
if (file_exists($PGV_BASE_DIRECTORY . "functions.extra.php")) require $PGV_BASE_DIRECTORY . "functions.extra.php";

?>