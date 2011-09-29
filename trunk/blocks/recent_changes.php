<?php
/**
 * On This Day Events Block
 *
 * This block will print a list of today's events
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
 * $Id: recent_changes.php,v 1.1 2005/10/07 18:08:13 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */

$PGV_BLOCKS["print_recent_changes"]["name"]        = $pgv_lang["recent_changes_block"];
$PGV_BLOCKS["print_recent_changes"]["descr"]        = $pgv_lang["recent_changes_descr"];
$PGV_BLOCKS["print_recent_changes"]["canconfig"]        = true;
$PGV_BLOCKS["print_recent_changes"]["config"] = array("days"=>30);

//-- Recent Changes block
//-- this block prints a list of changes that have occurred recently in your gedcom
function print_recent_changes($block=true, $config="", $side, $index) {
	global $pgv_lang, $factarray, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $REGEXP_DB, $DEBUG, $ASC, $IGNORE_FACTS, $IGNORE_YEAR, $TOTAL_QUERIES, $LAST_QUERY, $PGV_BLOCKS, $SHOW_SOURCES;

	$block = true;			// Always restrict this block's height
	
	if ($command=="user") $filter = "living";
	else $filter = "all";
	
	if (empty($config)) $config = $PGV_BLOCKS["print_recent_changes"]["config"];

	$daytext = "";
	$action = "today";
	$dayindilist = array();
	$dayfamlist = array();
	$daysourcelist = array();
	$dayrepolist = array();
	$found_facts = array();
	/* -- don't cache this block
	if (isset($_SESSION["recent_changes"][$command][$GEDCOM])&&(!isset($DEBUG)||($DEBUG==false))) {
		$found_facts = $_SESSION["recent_changes"][$command][$GEDCOM];
	}
	else {
		*/
		$monthstart = mktime(1,0,0,$monthtonum[strtolower($month)],$day,$year);
		$mmon = strtolower(date("M", $monthstart));
		$mmon2 = strtolower(date("M", $monthstart-(60*60*24*$config["days"])));
		$mday2 = date("d", $monthstart-(60*60*24*$config["days"]));
		if ($mmon=="mar" && $mmon2=="jan") $mmon2="feb";
		if ($REGEXP_DB) $query = "1 CHAN.*2 DATE[^\n]*$mmon $year";
		else $query = "%1 CHAN%2 DATE%$mmon $year%";
		
		$dayindilist = search_indis($query);
		$dayfamlist = search_fams($query);
		if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist = search_repos($query);
		if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist = search_sources($query);
		if ($mmon!=$mmon2) {
			if ($REGEXP_DB) $query = "1 CHAN.*2 DATE[^\n]*$mmon2 $year";
			else $query = "%1 CHAN%2 DATE%$mmon $year%";
			$dayindilist2 = search_indis($query);
			$dayfamlist2 = search_fams($query);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist2 = search_repos($query);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist2 = search_sources($query);
			$dayindilist = array_merge($dayindilist, $dayindilist2);
			$dayfamlist = array_merge($dayfamlist, $dayfamlist2);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist = array_merge($daysourcelist, $daysourcelist2);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist = array_merge($dayrepolist, $dayrepolist2);
		}
	/*
	}
	*/
	if ((count($dayindilist)>0)||(count($dayfamlist)>0)||(count($daysourcelist)>0)) {
		$found_facts = array();
		$last_total = $TOTAL_QUERIES;
		foreach($dayindilist as $gid=>$indi) {
			$disp = true;
			if (($filter=="living")&&(is_dead_id($gid)==1)) $disp = false;
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid);
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $indi["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));
						if ((str2lower($date[0]["month"])==$mmon && $date[0]["day"]<=$day)||(str2lower($date[0]["month"])==$mmon2 && $date[0]["day"]>$mday2)) {
							$found_facts[] = array($gid, $factrec, "INDI");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $indi["gedcom"], $i);
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$disp = true;
			if ($filter=="living") {
				$parents = find_parents_in_record($fam["gedcom"]);
				if (is_dead_id($parents["HUSB"])==1) $disp = false;
				else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["HUSB"]);
				if ($disp) {
					if (is_dead_id($parents["WIFE"])==1) $disp = false;
					else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["WIFE"]);
				}
			}
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid, "FAM");
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $fam["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));
						if ((str2lower($date[0]["month"])==$mmon && $date[0]["day"]<=$day)||(str2lower($date[0]["month"])==$mmon2 && $date[0]["day"]>$mday2)) {
							$found_facts[] = array($gid, $factrec, "FAM");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $fam["gedcom"], $i);
				}
			}
		}
		foreach($daysourcelist as $gid=>$source) {
			$disp = true;
			$disp = displayDetailsByID($gid, "SOUR");
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $source["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));
						if ((str2lower($date[0]["month"])==$mmon && $date[0]["day"]<=$day)||(str2lower($date[0]["month"])==$mmon2 && $date[0]["day"]>$mday2)) {
							$found_facts[] = array($gid, $factrec, "SOUR");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $source["gedcom"], $i);
				}
			}
		}
		foreach($dayrepolist as $rid=>$repo) {
			$disp = true;
			$disp = displayDetailsByID($gid, "REPO");
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));
						if ((str2lower($date[0]["month"])==$mmon && $date[0]["day"]<=$day)||(str2lower($date[0]["month"])==$mmon2 && $date[0]["day"]>$mday2)) {
							$found_facts[] = array($rid, $factrec, "REPO");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
				}
			}
		}
	}
	if (count($found_facts)>0) {
		print "<div id=\"recent_changes\" class=\"block\">";
		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["recent_changes"]."</b>";
		print_help_link("recent_changes_help", "qm");
		if ($PGV_BLOCKS["print_recent_changes"]["canconfig"]) {
			$username = getUserName();
			if ((($command=="gedcom")&&(userGedcomAdmin($username))) || (($command=="user")&&(!empty($username)))) {
				if ($command=="gedcom") $name = preg_replace("/'/", "\'", $GEDCOM);
				else $name = $username;
				print "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?name=$name&amp;command=$command&amp;action=configure&amp;side=$side&amp;index=$index', '', 'top=50,left=50,width=600,height=550,scrollbars=1,resizable=1'); return false;\">";
				print "<img class=\"adminicon\" src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$pgv_lang["config_block"]."\" /></a>\n";
			}
		}
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\" >";
		if ($block) print "<div class=\"small_inner_block\">\n";
		
		$ASC = 1;
		$IGNORE_FACTS = 1;
		$IGNORE_YEAR = 0;
		uasort($found_facts, "compare_facts");
		$lastgid="";
		foreach($found_facts as $index=>$factarr) {
			if ($factarr[2]=="INDI") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid)) {
					$indirec = find_person_record($gid);
					if ($lastgid!=$gid) {
						print "<img id=\"box-".$gid."-".$index."-sex\" src=\"$PGV_IMAGE_DIR/";
						if (preg_match("/1 SEX M/", $indirec)>0) print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
						else  if (preg_match("/1 SEX F/", $indirec)>0) print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
						else print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"];
						print "\" class=\"sex_image\" />";
						$name = check_NN(get_sortable_name($gid));
						print "<a href=\"individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_ID_NUMBERS) {
						   if ($TEXT_DIRECTION=="ltr") 
								print " &lrm;($gid)&lrm;";
						   else print " &rlm;($gid)&rlm;";
						}
						print "</a><br />\n";
						$lastgid=$gid;
					}
					print "<div class=\"indent" . ($TEXT_DIRECTION=="rtl"?"_rtl":"") . "\">";
					print $factarray["CHAN"];
					$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
					if ($ct>0) {
							print " - <span class=\"date\">".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
									print " - ".$match[1];
							}
							print "</span>\n";
					}
					print "</div><br />";
				}
			}
			
			if ($factarr[2]=="FAM") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "FAM")) {
					$famrec = find_family_record($gid);
					$name = get_family_descriptor($gid);
					if ($lastgid!=$gid) {
						print "<a href=\"family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
						   if ($TEXT_DIRECTION=="ltr") 
								print " &lrm;($gid)&lrm;";
						   else print " &rlm;($gid)&rlm;";
						}
						print "</a><br />\n";
						$lastgid=$gid;
					}
					print "<div class=\"indent" . ($TEXT_DIRECTION=="rtl"?"_rtl":"") . "\">";
					print $factarray["CHAN"];
					$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
					if ($ct>0) {
							print " - <span class=\"date\">".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
									print " - ".$match[1];
							}
							print "</span>\n";
					}
					print "</div><br />";
				}
			}
			
			if ($factarr[2]=="SOUR") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "SOUR")) {
					$sourcerec = find_source_record($gid);
					$name = get_source_descriptor($gid);
					if ($lastgid!=$gid) {
						print "<a href=\"source.php?sid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
						   if ($TEXT_DIRECTION=="ltr") 
								print " &lrm;($gid)&lrm;";
						   else print " &rlm;($gid)&rlm;";
						}
						print "</a><br />\n";
						$lastgid=$gid;
					}
					print "<div class=\"indent" . ($TEXT_DIRECTION=="rtl"?"_rtl":"") . "\">";
					print $factarray["CHAN"];
					$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
					if ($ct>0) {
							print " - <span class=\"date\">".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
									print " - ".$match[1];
							}
							print "</span>\n";
					}
					print "</div><br />";
				}
			}

			if ($factarr[2]=="REPO") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "REPO")) {
					$reporec = find_repo_record($gid);
					$name = get_repo_descriptor($gid);
					if ($lastgid!=$gid) {
						print "<a href=\"repo.php?rid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
						   if ($TEXT_DIRECTION=="ltr") 
								print " &lrm;($gid)&lrm;";
						   else print " &rlm;($gid)&rlm;";
						}
						print "</a><br />\n";
						$lastgid=$gid;
					}
					print "<div class=\"indent" . ($TEXT_DIRECTION=="rtl"?"_rtl":"") . "\">";
					print $factarray["CHAN"];
					$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
					if ($ct>0) {
							print " - <span class=\"date\">".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
									print " - ".$match[1];
							}
							print "</span>\n";
					}
					print "</div><br />";
				}
			}

		}
		
		if ($block) print "</div>\n"; //small_inner_block
		print "</div>"; // blockcontent
		print "</div>"; // block
	}
	
	//-- store the results in the session to improve speed of future page loads
	$_SESSION["recent_changes"][$command][$GEDCOM] = $found_facts;
}

function print_recent_changes_config($config) {
	global $pgv_lang, $PGV_BLOCKS;
	if (empty($config)) $config = $PGV_BLOCKS["print_recent_changes"]["config"];
	?>
	<?php print $pgv_lang["days_to_show"]; ?> <select name="days">
		<?php
		for($i=2; $i<=30; $i++) {
			print "\t\t<option value=\"$i\"";
			if ($config["days"]==$i) print " selected=\"selected\"";
			print ">$i</option>\n";
		}
		?>
	</select>
	<?php
}
?>