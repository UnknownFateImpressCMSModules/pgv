<?php
/**
 * Various functions used to generate the PhpGedView RSS feed.
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
 * @version $Id: functions_rss.php,v 1.4 2005/12/29 17:12:42 canajun2eh Exp $
 * @package PhpGedView
 * @subpackage RSS
 */

if (strstr($_SERVER["PHP_SELF"],"functions")) {
        print "Now, why would you want to do that.        You're not hacking are you?";
        exit;
}

require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);

if (isset($_SESSION["timediff"])) $time = time()-$_SESSION["timediff"];
else $time = time();
$day = date("j", $time);
$month = date("M", $time);
$year = date("Y", $time);

function iso8601_date($time) {
   $tzd = date('O',$time);
   $tzd = $tzd[0] . str_pad((int) ($tzd / 100), 2, "0", STR_PAD_LEFT) .
                   ':' . str_pad((int) ($tzd % 100), 2, "0", STR_PAD_LEFT);
   $date = date('Y-m-d\TH:i:s', $time) . $tzd;
   return $date;
}

function getUpcomingEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $command, $indilist, $TEXT_DIRECTION;
	global $PGV_IMAGES, $PGV_IMAGE_DIR,$SERVER_URL;
	
	if ($command=="user") $filter = "living";
	else $filter = "all";
	
	$daytext = "";
	$dataArray[0] = $pgv_lang["upcoming_events"];
	$dataArray[1] = time();
	$monthstart = mktime(1,0,0,$monthtonum[strtolower($month)],$day,$year);
	$mmon = strtolower(date("M", $monthstart));
	$myindilist = array();
	$myfamlist = array();
	$query = "2 DATE [0-9]{1,2} $mmon";
	$myindilist = search_indis($query);
	$myfamlist = search_fams($query);
	if ((count($myindilist)>0)||(count($myfamlist)>0)) {
		$oldmonth=$month;
		$oldyear = $year;
		$oldday = $day;
		for($k=0; $k<30; $k++) {
			$mday = date("j", $monthstart);
			$mmon = strtolower(date("M", $monthstart));
			$day = $mday;
			$year = (int)date("Y", $monthstart);
			if ($mmon!=strtolower($month)) {
				$query = "2 DATE [0-9]{1,2} $mmon";
				$myindilist = search_indis($query);
				$myfamlist = search_fams($query);
				$month=$mmon;
			}
			$dayindilist = array();
			if ($mday<10) $query = "2 DATE 0?$mday $mmon";
			else $query = "2 DATE $mday $mmon";
			foreach($myindilist as $gid=>$indi) {
				if (preg_match("/$query/i", $indi["gedcom"])>0) {
					if (displayDetailsById($gid)) $dayindilist[$gid]=$indi;
				}
			}
			$dayfamlist = array();
			foreach($myfamlist as $gid=>$fam) {
				if (preg_match("/$query/i", $fam["gedcom"])>0) {
					if (displayDetailsById($gid, "FAM")) $dayfamlist[$gid]=$fam;
				}
			}
			if ((count($dayindilist)>0)||(count($dayfamlist)>0)) {
				
				foreach($dayindilist as $gid=>$indi) {
					$disp = true;
					if ($disp) {
						$indilines = split("\n", $indi["gedcom"]);
						$factrec = "";
						$lct = count($indilines);
						$text = "";
						for($i=1; $i<=$lct; $i++) {
							if ($i<$lct) $line = $indilines[$i];
							if (empty($line)) $line = " ";
							if ($i==$lct||($line{0}=="1")) {
								if (!empty($factrec)) {
									$ct = preg_match("/$query/i", $factrec, $match);
									if ($ct>0) {
										$tempText = get_calendar_fact($factrec, "block", $filter, $gid);
										$text.= str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
									}
								}
								$factrec="";
							}
							$factrec.=$line."\n";
						}
						if (!empty($text)) {
							$daytext .= "<a href=\"" .$SERVER_URL . "individual.php?pid=$gid&amp;ged=".$indi["file"]."\">".get_person_name($gid);
							if ($SHOW_ID_NUMBERS) $daytext .= " &lrm;($gid)&lrm;";
							$daytext .= "</a> ";
							$daytext .= $text;
						}
					}
				}
				foreach($dayfamlist as $gid=>$fam) {
					$indilines = split("\n", $fam["gedcom"]);
					$factrec = "";
					$lct = count($indilines);
					$text = "";
					for($i=1; $i<=$lct; $i++) {
						if ($i<$lct) $line = $indilines[$i];
						if (empty($line)) $line = " ";
						if ($i==$lct||($line{0}=="1")) {
							if (!empty($factrec)) {
								$ct = preg_match("/$query/i", $factrec, $match);
								if ($ct>0) {
									$tempText = get_calendar_fact($factrec, "block", $filter, $gid);
									$text.= str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
								}
							}
							$factrec="";
						}
						$factrec.=$line."\n";
					}
					if (!empty($text)) {
//						$names = preg_split("/[,+]/", $fam["name"]);
//						$fam["name"] = check_NN($names);
//						$daytext .= "<a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$fam["file"]."\">".$fam["name"];
						$name = get_family_descriptor($gid);
						$daytext .= "<a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$fam["file"]."\">".$name;
						if ($SHOW_FAM_ID_NUMBERS) $daytext .= " &lrm;($gid)&lrm;";
						$daytext .= "</a> ";
						$daytext .= $text;
					}
				}
				
			}
			$monthstart += (60*60*24);
		}
		$day = $oldday;
		$month = $oldmonth;
		$year = $oldyear;
		
	}
	$dataArray[2]  = $daytext;
	return $dataArray;
}

function getTodaysEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $command, $TEXT_DIRECTION;
	global $PGV_IMAGE_DIR, $PGV_IMAGES,$SERVER_URL;
	
	if ($command=="user") $filter = "living";
	else $filter = "all";
	$action = "today";
	$dataArray[0] = $pgv_lang["on_this_day"];
	$dataArray[1] = time();
	$daytext = "";
	$dayindilist = array();
	$dayfamlist = array();
	if ($day<10) $query = "2 DATE 0?$day $month";
	else $query = "2 DATE $day $month";
	$dayindilist = search_indis($query);
	$dayfamlist = search_fams($query);
	if ((count($dayindilist)>0)||(count($dayfamlist)>0)) {
		
		foreach($dayindilist as $gid=>$indi) {
			$disp = true;
			if (($filter=="living")&&(is_dead_id($gid)==1)) $disp = false;
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid);
			if ($disp) {
				$indilines = split("\n", $indi["gedcom"]);
				$factrec = "";
				$lct = count($indilines);
				$text = "";
				for($i=1; $i<=$lct; $i++) {
					if ($i<$lct) $line = $indilines[$i];
					if (empty($line)) $line = " ";
					if ($i==$lct||($line{0}=="1")) {
						if (!empty($factrec)) {
							$ct = preg_match("/$query/i", $factrec, $match);
							if ($ct>0) {
								$tempText = get_calendar_fact($factrec, $action, $filter, $gid);
								$text.= str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
							}
						}
						$factrec="";
					}
					$factrec.=$line."\n";
				}
				if (!empty($text)) {
					$daytext .= "<a href=\"".$SERVER_URL ."individual.php?pid=$gid&amp;ged=".$indi["file"]."\">".get_person_name($gid);
					if ($SHOW_ID_NUMBERS) $daytext .= " &lrm;($gid)&lrm;";
					$daytext .= "</a> ";
					$daytext .= $text;
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$indilines = split("\n", $fam["gedcom"]);
			$factrec = "";
			$lct = count($indilines);
			$text = "";
			for($i=1; $i<=$lct; $i++) {
				if ($i<$lct) $line = $indilines[$i];
				if (empty($line)) $line = " ";
				if ($i==$lct||($line{0}=="1")) {
					if (!empty($factrec)) {
						$ct = preg_match("/$query/i", $factrec, $match);
						if ($ct>0) {
							$tempText = get_calendar_fact($factrec, $action, $filter, $gid);
							$text.= str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
						}
					}
					$factrec="";
				}
				$factrec.=$line."\n";
			}
			if (!empty($text)) {
				$display = true;
				if (preg_match("/$query/i", $fam["gedcom"])>0) {
					if (displayDetailsById($gid, "FAM")) $dayfamlist[$gid]=$fam;
				}
				$parents = find_parents($gid);
				if (!empty($parents["HUSB"])) {
					if (($filter=="living")&&(is_dead_id($parents["HUSB"])==1)) $display = false;
					else if ($HIDE_LIVE_PEOPLE) $display = displayDetailsByID($parents["HUSB"]);
				}
				if ($display) {
					if (!empty($parents["WIFE"])) {
						if (($filter=="living")&&(is_dead_id($parents["WIFE"])==1)) $display = false;
						else if ($HIDE_LIVE_PEOPLE) $display = displayDetailsByID($parents["WIFE"]);
					}
				}
				if ($display) {
//					$names = preg_split("/[,+]/", $fam["name"]);
//					$fam["name"] = check_NN($names);
//					$daytext .= "<a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$fam["file"]."\">".$fam["name"];
					$name = get_family_descriptor($gid);
					$daytext .= "<a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$fam["file"]."\">".$name;
					if ($SHOW_FAM_ID_NUMBERS) $daytext .= " &lrm;($gid)&lrm;";
					$daytext .= "</a> ";
					$daytext .= $text;
				}
			}
			
		}
	}
	$dataArray[2] = $daytext;
	return $dataArray;
}

function getGedcomStats() {
	global $pgv_lang, $day, $month, $year, $GEDCOM, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $command, $COMMON_NAMES_THRESHOLD, $SERVER_URL, $RTLOrd;
	
	$data = "";
	$dataArray[0] = $pgv_lang["gedcom_stats"] . " - " . $GEDCOMS[$GEDCOM]["title"];
	
	$head = find_gedcom_record("HEAD");
	$ct=preg_match("/1 SOUR (.*)/", $head, $match);
	if ($ct>0) {
		$softrec = get_sub_record(1, "1 SOUR", $head);
		$tt= preg_match("/2 NAME (.*)/", $softrec, $tmatch);
		if ($tt>0) $title = trim($tmatch[1]);
		else $title = trim($match[1]);
		if (!empty($title)) {
			$text = strip_tags(str_replace("#SOFTWARE#", $title, $pgv_lang["gedcom_created_using"]));
			$tt = preg_match("/2 VERS (.*)/", $softrec, $tmatch);
			if ($tt>0) $version = trim($tmatch[1]);
			else $version="";
			$text = strip_tags(str_replace("#VERSION#", $version, $text));
			$data .= $text;
		}
	}
	$ct=preg_match("/1 DATE (.*)/", $head, $match);
	if ($ct>0) {
		$date = trim($match[1]);
		$dataArray[1] = strtotime($date);
		
	}
	$data .= " <br />\n";
	$data .= get_list_size("indilist"). " - " .$pgv_lang["stat_individuals"]." <br/>";
	$data .= get_list_size("famlist"). " - ".$pgv_lang["stat_families"]." <br/>";
	$data .= get_list_size("sourcelist")." - ".$pgv_lang["stat_sources"]." <br/> ";
	$data .= get_list_size("otherlist")." - ".$pgv_lang["stat_other"]." <br />";
	$surnames = get_common_surnames_index($GEDCOM);
	if (count($surnames)>0) {
		$data .= $pgv_lang["common_surnames"]." <br />";
		$i=0;
		foreach($surnames as $indexval => $surname) {
			if ($i>0) $data .= ", ";
			if (in_array(ord(substr($surname["name"], 0, 2)),$RTLOrd)) {
//  		if (ord(substr($surname["name"], 0, 2),$RTLOrd)){}
				$data .= "<a href=\"".$SERVER_URL ."indilist.php?surname=".urlencode($surname["name"])."\">".$surname["name"]."</a>";
			}
			else $data .= "<a href=\"".$SERVER_URL ."indilist.php?surname=".$surname["name"]."\">".$surname["name"]."</a>";
			$i++;
		}
	}
	$dataArray[2] = $data;
	return $dataArray;
	
}


function getGedcomNews() {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION, $GEDCOM, $command, $TIME_FORMAT, $VERSION, $SERVER_URL;
	
	$usernews = getUserNews($GEDCOM);
	
	$dataArray = array();
	foreach($usernews as $key=>$news) {
		
		$day = date("j", $news["date"]);
		$mon = date("M", $news["date"]);
		$year = date("Y", $news["date"]);
		$data = "";
		$ct = preg_match("/#(.+)#/", $news["title"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["title"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["title"]);
		}
		$itemArray[0] = $news["title"];
		
		$itemArray[1] = iso8601_date($news["date"]);
		$ct = preg_match("/#(.+)#/", $news["text"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
		}
		$ct = preg_match("/#(.+)#/", $news["text"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
			$varname = $match[1];
			if (isset($$varname)) $news["text"] = preg_replace("/$match[0]/", $$varname, $news["text"]);
		}
		$trans = get_html_translation_table(HTML_SPECIALCHARS);
		$trans = array_flip($trans);
		$news["text"] = strtr($news["text"], $trans);
		$news["text"] = nl2br($news["text"]);
		$data .= $news["text"];
		$itemArray[2] = $data;
		$dataArray[] = $itemArray;
		
	}
	return $dataArray;
	
}

function getTop10Surnames($block=true, $config="") {
	global $pgv_lang, $GEDCOM;
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $PGV_BLOCKS, $command, $PGV_IMAGES, $PGV_IMAGE_DIR;

	$data = "";
	$dataArray = array();


	function top_surname_sort($a, $b) {
		return $b["match"] - $a["match"];
	}

	$PGV_BLOCKS["print_block_name_top10"]["config"] = array("num"=>10);

	if (empty($config)) $config = $PGV_BLOCKS["print_block_name_top10"]["config"];

	$dataArray[0] = str_replace("10", $config["num"], $pgv_lang["block_top10_title"]);
	$dataArray[1] = time();

	//-- cache the result in the session so that subsequent calls do not have to
	//-- perform the calculation all over again.
	if (isset($_SESSION["top10"][$GEDCOM])) {
		$surnames = $_SESSION["top10"][$GEDCOM];
	}
	else {
		$surnames = get_top_surnames($config["num"]);

		// Insert from the "Add Names" list if not already in there
		if ($COMMON_NAMES_ADD != "") {
			$addnames = preg_split("/[,;] /", $COMMON_NAMES_ADD);
			if (count($addnames)==0) $addnames[] = $COMMON_NAMES_ADD;
			foreach($addnames as $indexval => $name) {
				//$surname = str2upper($name);
				$surname = $name;
				if (!isset($surnames[$surname])) {
					$surnames[$surname]["name"] = $name;
					$surnames[$surname]["match"] = $COMMON_NAMES_THRESHOLD;
				}
			}
		}

		// Remove names found in the "Remove Names" list
		if ($COMMON_NAMES_REMOVE != "") {
			$delnames = preg_split("/[,;] /", $COMMON_NAMES_REMOVE);
			if (count($delnames)==0) $delnames[] = $COMMON_NAMES_REMOVE;
			foreach($delnames as $indexval => $name) {
				//$surname = str2upper($name);
				$surname = $name;
				unset($surnames[$surname]);
			}
		}

		// Sort the list and save for future reference
		uasort($surnames, "top_surname_sort");
		$_SESSION["top10"][$GEDCOM] = $surnames;
	}
	if (count($surnames)>0) {
		$i=0;
		foreach($surnames as $indexval => $surname) {
			if (stristr($surname["name"], "@N.N")===false) {
				$data .= "<a href=\"indilist.php?surname=".urlencode($surname["name"])."\">".PrintReady($surname["name"])."</a> [".$surname["match"]."] <br />";
				$i++;
				if ($i>=$config["num"]) break;
			}
		}
	}
	$dataArray[2] = $data;
	return $dataArray;
}

?>