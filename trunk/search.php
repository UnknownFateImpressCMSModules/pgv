<?php
/**
 * Searches based on user query.
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
 * @subpackage Display
 * @version $Id: search.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require("config.php");

// Remove slashes
if (isset($query)) {
	if ($query == $pgv_lang["search"]) {
		unset($query);
		unset($action);
	}
	else {
	$query = stripslashes($query);
	$myquery = $query;
	}
}

if (!empty($firstname)) $myfirstname = $firstname;
else {
	unset($firstname);
	$myfirstname = "";
}
if (!empty($lastname)) $mylastname = $lastname;
else {
	unset($lastname);
	$mylastname = "";
}	
if (!empty($place)) $myplace = $place;
else {
	unset($place);
	$myplace = "";
}
if (!empty($year)) $myyear = $year;
else {
	unset($year);
	$myyear = "";
}


if (!isset($action)) $action="";

if ($action=="general") {
	//-- perform the search
	if ((isset($query)) && ($query!="")) {
		AddToSearchlog("Type: General<br />Query: ".$query, $ALLOW_CHANGE_GEDCOM);
		if (strlen($query) == 1) $query = preg_replace(array("/\?/", "/\|/", "/\*/"), array("\\\?","\\\|", "\\\\\*") , $query);
		if ($REGEXP_DB) $query = preg_replace(array("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array(".*",'\(','\)','\[','\]'), $query);
		else {
			$query = "%".preg_replace("/\s+/", "%", $query)."%";
		}

		// -- array of names
		$myindilist = array();
		$mysourcelist = array();
		$myfamlist = array();
		
		$myindilist = search_indis($query, $ALLOW_CHANGE_GEDCOM);
		uasort($myindilist, "itemsort");

		$mysourcelist = search_sources($query, $ALLOW_CHANGE_GEDCOM);
		uasort($mysourcelist, "itemsort");
		reset($mysourcelist);

		$myfamlist = search_fams($query, $ALLOW_CHANGE_GEDCOM);
		uasort($myfamlist, "itemsort");
		reset($myfamlist);

		//-- if only 1 item is returned, automatically forward to that item
		if ((count($myindilist)==1)&&(count($myfamlist)==0)&&(count($mysourcelist)==0)) {
			foreach($myindilist as $pid=>$indi) {
				if ($ALLOW_CHANGE_GEDCOM) $pid = substr($pid,0,strpos($pid,"["));
				header("Location: individual.php?pid=".$pid."&ged=".$indi["file"]);
				exit;
			}
		}
		if ((count($myindilist)==0)&&(count($myfamlist)==1)&&(count($mysourcelist)==0)) {
			foreach($myfamlist as $famid=>$fam) {
				if ($ALLOW_CHANGE_GEDCOM) $famid = substr($famid,0,strpos($famid,"["));
				header("Location: family.php?famid=".$famid."&ged=".$fam["file"]);
				exit;
			}
		}
		if ((count($myindilist)==0)&&(count($myfamlist)==0)&&(count($mysourcelist)==1)) {
			foreach($mysourcelist as $sid=>$source) {
				if ($ALLOW_CHANGE_GEDCOM) $sid = substr($sid,0,strpos($sid,"["));
				header("Location: source.php?sid=".$sid."&ged=".$source["file"]);
				exit;
			}
		}
	}
}

if ($action=="soundex") {
	if ((!empty($lastname))||(!empty($firstname))||(!empty($place))) {
		$logstring = "Type: Soundex<br />";
		if (!empty($lastname)) $logstring .= "Last name: ".$lastname."<br />";
		if (!empty($firstname)) $logstring .= "First name: ".$firstname."<br />";
		if (!empty($place)) $logstring .= "Place: ".$place."<br />";
		if (!empty($year)) $logstring .= "Year: ".$year."<br />";
		AddToSearchlog($logstring, $ALLOW_CHANGE_GEDCOM);
		if ($ALLOW_CHANGE_GEDCOM) $geds = $GEDCOMS;
		else $geds[$GEDCOM]["gedcom"] = $GEDCOM;
		
    	if (isset($firstname)) {
	    	if (strlen($firstname) == 1) $firstname = preg_replace(array("/\?/", "/\|/", "/\*/"), array("\\\?","\\\|", "\\\\\*") , $firstname);
			if ($REGEXP_DB) $firstname = preg_replace(array("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array(".*",'\(','\)','\[','\]'), $firstname);
			else {
				$firstname = "%".preg_replace("/\s+/", "%", $firstname)."%";
			}		
		}
    	if (isset($lastname)) {
		    if (strlen($lastname) == 1) $lastname = preg_replace(array("/\?/", "/\|/", "/\*/"), array("\\\?","\\\|", "\\\\\*") , $lastname);
			if ($REGEXP_DB) $lastname = preg_replace(array("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array(".*",'\(','\)','\[','\]'), $lastname);
			else {
				$lastname = "%".preg_replace("/\s+/", "%", $lastname)."%";
			}
		}
		if (isset($place)) {
			if (strlen($place) == 1) $place = preg_replace(array("/\?/", "/\|/", "/\*/"), array("\\\?","\\\|", "\\\\\*") , $place);
			if ($REGEXP_DB) $place = preg_replace(array("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array(".*",'\(','\)','\[','\]'), $place);
			else {
				$place = "%".preg_replace("/\s+/", "%", $place)."%";
			}		
		}
		if (isset($year)) {
	    	if (strlen($year) == 1) $year = preg_replace(array("/\?/", "/\|/", "/\*/"), array("\\\?","\\\|", "\\\\\*") , $year);
			if ($REGEXP_DB) $year = preg_replace(array("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array(".*",'\(','\)','\[','\]'), $year);
			else {
				$year = "%".preg_replace("/\s+/", "%", $year)."%";
			}		
		}
		$myindilist = array();
		$oldged = $GEDCOM;
		$sindilist = array();
		foreach($geds as $indexval => $value) {
			//-- in index mode we can only look in 1 gedcom
			if ($PGV_DATABASE!="index" || $value["gedcom"]==$GEDCOM) {
				$GEDCOM = $value["gedcom"];
				$INDILIST_RETRIEVED = false;
				$indilist = get_indi_list();
				// -- only get the names who match soundex
				foreach ($indilist as $key => $value) {
					foreach($value["names"] as $indexval => $namearray) {
						$save = false;
						$name = check_NN($namearray[0]);
						$savel=false;
						if (!empty($lastname)) {
							$surname = check_NN($namearray[2]);
							if (soundex($surname)==soundex($lastname)) $savel = true;
							if ($savel) $save=true;
						}
						$savef=false;
						if (!empty($firstname)) {
							$firstnames = preg_split("/\s/", trim($firstname));
							$pos1 = strpos($namearray[0], "/");
							if ($pos1===false) $pos1 = strlen($namearray[0]);
							$fname = substr($namearray[0], 0, $pos1);
							$fnames = preg_split("/\s/", trim($fname));
							for($i=0; $i<count($fnames); $i++) {
								for($j=0; $j<count($firstnames); $j++) {
									if (soundex($fnames[$i])==soundex($firstnames[$j])) $savef = true;
								}
							}
							if (($savel==true || empty($lastname)) && $savef==true) $save = true;
							else $save = false;
						}
					}
					if ((!empty($place))||(!empty($year))) {
						$indirec = find_person_record($key);
						if (!empty($place)) {
							$savep=false;
							$pt = preg_match_all("/\d PLAC (.*)/i", $indirec, $match, PREG_PATTERN_ORDER );
							if ($pt>0) {
								$places = array();
								for ($pp=0; $pp<count($match[1]); $pp++){
									$places[$pp] =preg_split("/,\s/", trim($match[1][$pp]));
								}
								$cp=count($places);
								for($p=0; $p<$cp; $p++) {
									for($pp=0; $pp<count($places[$p]); $pp++) {
										if (soundex(trim($places[$p][$pp]))==soundex(trim($place))) $savep = true;
									}
								}
							}
							if (($savel==true || empty($lastname)) && ($savef==true || empty($firstname)) && $savep==true) $save = true;
							else $save = false;
						}
						if (!empty($year) && $save==true) {
							$yt = preg_match("/\d DATE (.*$year.*)/i", $indirec, $match);
							if ($yt==0) $save = false;
						}
					}
					if ($save) $sindilist["$key"."[".$GEDCOM."]"] = $value;
				}
			}
		}
		$GEDCOM = $oldged;
		//-- if only 1 item is returned, automatically forward to that item
		if (count($sindilist)==1) {
			foreach($sindilist as $pid=>$indi) {
				$pid = substr($pid,0,strpos($pid,"["));
				header("Location: individual.php?pid=".$pid."&ged=".$indi["file"]);
				exit;
			}
		}
		uasort($sindilist, "itemsort");
		reset($sindilist);
	}
}

print_header($pgv_lang["search"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checknames(frm) {
		if (frm.year.value!="") {
			message=true;
			if (frm.firstname.value!="") message=false;
			if (frm.lastname.value!="") message=false;
			if (frm.place.value!="") message=false;
			if (message) {
				alert("<?php print $pgv_lang["invalid_search_input"]?>");
				frm.firstname.focus();
				return false;
			}
		}
		return true;
	}
//-->
</script>
<?php
print "\n\t<table class=\"list_table, $TEXT_DIRECTION\" width=\"100%\" border=\"0\"><tr><td style=\"padding: 10px;\" valign=\"top\" width=\"50%\">\n\t\t";
	print "\n\t<h2>".$pgv_lang["search_gedcom"]."</h2>\n\t";
	print "<form method=\"get\" action=\"search.php\">";
	print "<table class=\"list_table\"><tr><td class=\"list_label\" style=\"padding: 5px;\">";
	print $pgv_lang["enter_terms"];
	print "</td><td class=\"list_value\" style=\"padding: 5px;\">";
	print "<input type=\"hidden\" name=\"action\" value=\"general\" />";
	print "<input tabindex=\"1\" type=\"text\" name=\"query\" value=\"";
	if ($action=="general" && isset($myquery)) print $myquery;
	else print "";
	print "\" />";
	print_help_link("search_enter_terms_help", "qm");
	print "</td><td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\">";
	print "<input tabindex=\"2\" type=\"submit\" value=\"".$pgv_lang["search"]."\" />";
	print "</td></tr></table>";
	print "</form>";
	print "</td>";
	print "<td style=\"padding: 10px;\">";
	print "<form method=\"get\" onsubmit=\""?>return checknames(this);<?php print " \" action=\"search.php\">";
	print "<input type=\"hidden\" name=\"action\" value=\"soundex\" />";
	print "<table class=\"list_table\"><tr><td colspan=\"3\">";
	print "<span class=\"label\">".$pgv_lang["soundex_search"]."</span>";
	print_help_link("soundex_search_help", "qm");
	print "</td></tr><tr><td class=\"list_label\" width=\"35%\">";
	print $pgv_lang["firstname_search"];
	print "</td><td class=\"list_value\">";
	print "<input tabindex=\"3\" type=\"text\" name=\"firstname\" value=\"";
	if ($action=="soundex") print $myfirstname;
	print "\" />";

	print "</td><td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\" rowspan=\"4\">";
	print "<input tabindex=\"7\" type=\"submit\" value=\"";
	print $pgv_lang["search"];
	print "\" />";

	print "</td></tr><tr><td class=\"list_label\">";
	print $pgv_lang["lastname_search"];
	print "</td><td class=\"list_value\"><input tabindex=\"4\" type=\"text\" name=\"lastname\" value=\"";
	if ($action=="soundex") print $mylastname;
	print "\" /></td></tr>";
	print "<tr><td class=\"list_label\">";
	print $pgv_lang["search_place"];
	print "</td><td class=\"list_value\"><input tabindex=\"5\" type=\"text\" name=\"place\" value=\"";
	if ($action=="soundex") print $myplace;
	print "\" /></td></tr>";
	print "<tr><td class=\"list_label\">";
	print $pgv_lang["search_year"];
	print "</td><td class=\"list_value\"><input tabindex=\"6\" type=\"text\" name=\"year\" value=\"";
	if ($action=="soundex") print $myyear;
	print "\" /></td>";
	print "</tr></table>";
print "</form>";
print "</td></tr></table>";


// ---- section to search and display results on a general keyword search
if ($action=="general") {
	if ((isset($query)) && ($query!="")) {
		print "<br />";
		print "\n\t<div class=\"center\"><table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>";
		if (count($myindilist)>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
		if (count($myfamlist)>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
		if (count($mysourcelist)>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["sources"]."</td>";
		print "</tr>\n\t\t<tr>";
		$i=0;
		$cti=count($myindilist);
		if ($cti>0) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;
			$indi_private=0;
			$indi_hide=0;
			print "<td class=\"list_value_wrap\"><ul>";
			foreach ($myindilist as $key => $value) {
				if ($ALLOW_CHANGE_GEDCOM && $PGV_DATABASE!="index") {
					$p1 = strpos($key,"[");
					if ($p1!==false) {
						$p2 = strpos($key,"]");
						$GEDCOM = substr($key,$p1+1,$p2-$p1-1);
						$key = substr($key,0,$p1);
						if ($GEDCOM != $curged) {
							include(get_privacy_file());
							$curged = $GEDCOM;
						}
					}
				}
				//-- make sure that the data that was searched on is not in a private part of the record
				if (!displayDetailsById($key) && showLivingNameById($key)) {
					//-- any record that is not a FAMC, FAMS is private
					$record = get_sub_record(1, "1 NAME", $value["gedcom"]);

					$name = $value["names"][0][0];
					foreach($value["names"] as $indexval => $namearray) {
						if (preg_match("/".str2upper($query)."/i", str2upper($namearray[0]))>0) {
							$name = $namearray[0];
							break;
						}
					}
					if (preg_match("/".str2upper($query)."/i", str2upper($record))>0) print_list_person($key, array(check_NN(sortable_name_from_name($name)), $value["file"]));
					else $indi_hide++;
				}
		    	else {
			    	$name = $value["names"][0][0];
					foreach($value["names"] as $indexval => $namearray) {
						if (preg_match("/".str2upper($query)."/i", str2upper($namearray[0]))>0) {
							$name = $namearray[0];
							break;
						}
					}
					print_list_person($key, array(check_NN(sortable_name_from_name($name)), $value["file"]));
				}
		    	print "\n";
		    	$i++;
			}
			print "\n\t\t</ul></td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		$i=0;
		$ctf=count($myfamlist);
		if ($ctf>0) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;
			$fam_private=0;
			$fam_hide=0;
			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			foreach ($myfamlist as $key => $value) {
				if ($ALLOW_CHANGE_GEDCOM && $PGV_DATABASE!="index") {
					$p1 = strpos($key,"[");
					$p2 = strpos($key,"]");
					$GEDCOM = substr($key,$p1+1,$p2-$p1-1);
					$key = substr($key,0,$p1);
					if ($GEDCOM != $curged) {
						include(get_privacy_file());
						$curged = $GEDCOM;
					}
				}
			    print_list_family($key, array(check_NN($value["name"]), $value["file"]));
			    $i++;
			}
			print "\n\t\t</ul></td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		$i=0;
		$cts=count($mysourcelist);
		if ($cts>0) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;
			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			foreach ($mysourcelist as $key => $value) {
				if ($ALLOW_CHANGE_GEDCOM && $PGV_DATABASE!="index") {
					$p1 = strpos($key,"[");
					$p2 = strpos($key,"]");
					$GEDCOM = substr($key,$p1+1,$p2-$p1-1);
					$key = substr($key,0,$p1);
					if ($GEDCOM != $curged) {
						include(get_privacy_file());
						$curged = $GEDCOM;
					}
				}
				print "<li type=\"circle\">";
			    print "\n\t\t\t<a href=\"source.php?sid=$key&amp;ged=".$value["file"]."\"><span class=\"list_item\">".PrintReady($value["name"])."</span></a>\n";
			    print "</li>\n";
			    $i++;
			}
			print "\n\t\t</ul></td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		print "</tr><tr>\n\t";
		if ($cti > 0 || $cts > 0 || $ctf > 0) {
			if ($cti > 0) {
				$cti -= $indi_hide;
				print "<td>".$pgv_lang["total_indis"]." ".$cti;
				if ($indi_private>0) print "  (".$pgv_lang["private"]." ".$indi_private.")";
				if ($indi_hide>0) print "  --  ".$pgv_lang["hidden"]." ".$indi_hide;
				if ($indi_private>0 || $indi_hide>0) print_help_link("privacy_error_help", "qm");
				print "</td>";
			}
			if ($ctf > 0) {
				$ctf -= $fam_hide;
				print "<td>".$pgv_lang["total_fams"]." ".$ctf;
				if ($fam_private>0) print "  (".$pgv_lang["private"]." ".$fam_private.")";
				if ($fam_hide>0) print "  --  ".$pgv_lang["hidden"]." ".$fam_hide;
				if ($fam_private>0 || $fam_hide>0) print_help_link("privacy_error_help", "qm");
				print "</td>";
			}
			if ($cts > 0) print "<td>".$pgv_lang["total_sources"]." ".$cts."</td>";
			if ($cti > 0 || $cts > 0 || $ctf > 0) print "</tr>\n\t";
		}
		else print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></td></tr>\n\t\t";
		print "</table></div>";
	}
	else print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></div>\n\t\t";
}

// ----- section to search and display results for a Soundex last name search
if ($action=="soundex") {
// 	$query = "";	// Stop function PrintReady from doing strange things to accented names
	if ((!empty($lastname))||(!empty($firstname)) ||(!empty($place))) {
		print "<div class=\"center\"><br />";
		print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>\n\t\t";
		$i=0;
		$ct=count($sindilist);
		if ($ct > 0) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;
			$indi_private=0;
			$indi_hide=0;
			print "<td colspan=\"2\" class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["people"]."</td></tr>";
			print "<tr>\n\t\t<td class=\"list_value_wrap\"><ul>";

			foreach ($sindilist as $key => $value) {
				$p1 = strpos($key,"[");
				$p2 = strpos($key,"]");
				$GEDCOM = substr($key,$p1+1,$p2-$p1-1);
				$key = substr($key,0,$p1);
				if ($GEDCOM != $curged) {
					include(get_privacy_file());
					$curged = $GEDCOM;
				}
				print_list_person($key, array(check_NN(get_sortable_name($key)), $value["file"]));
				print "\n";
				if ($i == floor($ct / 2) && $ct>9) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
				$i++;
			}
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
			print "\n\t\t</ul></td>\n\t\t</tr>\n\t";
			if ($i>0) {
				print "<tr><td ";
				if ($ct > 9) print "colspan=\"2\">";
				else print ">";
				$ct -= $indi_hide;
				print $pgv_lang["total_indis"]." ".$ct;
				if ($indi_private>0) print "  (".$pgv_lang["private"]." ".$indi_private.")";
				if ($indi_hide>0) print "  --  ".$pgv_lang["hidden"]." ".$indi_hide;
				if ($indi_private>0 || $indi_hide>0) print_help_link("privacy_error_help", "qm");
				print "</td></tr>";
			}
		}
		else print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i></td></tr>\n\t\t";
		print "</table></div>";
	}
	else print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></div>\n\t\t";
}
print "<br /><br /><br />";
print_footer();
?>