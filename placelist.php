<?php
/**
 * Displays a place hierachy
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
 * @subpackage Lists
 * @version $Id: placelist.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

require("config.php");

function case_in_array($value, $array) {
	foreach($array as $key=>$val) {
		if (strcasecmp($value, $val)==0) return true;
	}
	return false;
}

if (empty($action)) $action = "find";
if (empty($display)) $display = "hierarchy";

if ($display=="hierarchy") print_header($pgv_lang["place_list"]);
else print_header($pgv_lang["place_list2"]);

print "\n\t<div class=\"center\">";
if ($display=="hierarchy") print "<h2>".$pgv_lang["place_list"]."</h2>\n\t";
else print "<h2>".$pgv_lang["place_list2"]."</h2>\n\t";

if (!isset($parent)) $parent=array();
else {
	if (!is_array($parent)) $parent = array();
	else $parent = array_values($parent);
}
// Remove slashes
foreach ($parent as $p => $child){
	$parent[$p] = stripslashes($child);
}

if (!isset($level)) {
	$level=0;
}

if ($level>count($parent)) $level = count($parent);
if ($level<count($parent)) $level = 0;

//-- extract the place form encoded in the gedcom
$header = find_gedcom_record("HEAD");
$hasplaceform = strpos($header, "1 PLAC");

//-- hierarchical display
if ($display=="hierarchy") {
	// -- array of names
	$placelist = array();
	$positions = array();
	$numfound = 0;
	get_place_list();
	// -- sort the array
	uasort($placelist, "stringsort");

	//-- create a query string for passing to search page
	$tempparent = array_reverse($parent);
	if (count($tempparent)>0) $squery = "&query=".urlencode($tempparent[0]);
	else $squery="";
	for($i=1; $i<$level; $i++) {
		$squery.=", ".urlencode($tempparent[$i]);
	}

	//-- if the number of places found is 0 then automatically redirect to search page
	if ($numfound==0) {
		$action="show";
	}

	// -- print the breadcrumb hierarchy
	$numls=0;
	if ($level>0) {
		//-- link to search results
		if ((($level>1)||($parent[0]!=""))&&($numfound>0)) {
			print $numfound."  ".$pgv_lang["connections"].": ";
		}
		//-- breadcrumb
		$numls = count($parent)-1;
		$num_place="";
		//-- place and page text orientation is opposite -> top level added at the beginning of the place text
		print "<a href=\"placelist.php?level=0\">";
		if ($numls>=0 && (($TEXT_DIRECTION=="ltr" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="rtl" && !hasRtLText($parent[$numls])))) print $pgv_lang["top_level"].", ";
		print "</a>";
	    for($i=$numls; $i>=0; $i--) {
			print "<a href=\"placelist.php?level=".($i+1)."&amp;";
			for ($j=0; $j<=$i; $j++) {
				$levels = preg_split ("/,/", trim($parent[$j]));
				// Routine for replacing ampersands
				foreach($levels as $pindex=>$ppart) {
					$ppart = urlencode($ppart);
					$ppart = preg_replace("/amp\%3B/", "", trim($ppart));
					print "&amp;parent[$j]=".$ppart;
				}
			}
 			print "\">";
 			if (trim($parent[$i])=="") print $pgv_lang["unknown"];
			else print PrintReady($parent[$i]);
			print "</a>";
 			if ($i>0) print ", ";
 			else if (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$i])) || ($TEXT_DIRECTION=="ltr" &&  !hasRtLText($parent[$i])))  print ", ";
			if (empty($num_place)) $num_place=$parent[$i];
		}
	}
	print "<a href=\"placelist.php?level=0\">";
	//-- place and page text orientation is the same -> top level added at the end of the place text
	if ($level==0 || ($numls>=0 && (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="ltr" && !hasRtLText($parent[$numls]))))) print $pgv_lang["top_level"];
	print "</a>";

	print_help_link("ppp_levels_help", "qm");

	// show country clickable map if found
	if ($level==1 || $level==2) {
		$country = $parent[0];
		if ($country == "\xD7\x99\xD7\xA9\xD7\xA8\xD7\x90\xD7\x9C") $country = "ISR"; // Israel hebrew name
		$country = strtoupper($country);
		if (strlen($country)!=3) {
			// search country code using current language countries table
			require($PGV_BASE_DIRECTORY."languages/countries.en.php");
			if (file_exists($PGV_BASE_DIRECTORY."languages/countries.".$lang_short_cut[$LANGUAGE].".php")) require($PGV_BASE_DIRECTORY."languages/countries.".$lang_short_cut[$LANGUAGE].".php");
			foreach ($countries as $countrycode => $countryname) {
				if (strtoupper($countryname) == $country) {
					$country = $countrycode;
					break;
				}
			}
			// if code not found - the gedcom language differ from user browser language
			if (strlen($country)!=3) $country = substr($country, 0, 3); // possible code if no country found
		}
		$imgfile = "places/".$country."/".$country;
		$maplang = "places/".$country."/".$country.".".$lang_short_cut[$LANGUAGE];
		if ($level==1) {
			$mapfile = $maplang.".htm";
			if (!file_exists($mapfile)) $mapfile = $imgfile.".htm";
			$imgfile = $imgfile.".gif";
			$maplist = $country;
			$mapname = $parent[0];
		}
		else {															// added to support county level
			$level2 = str_replace("/ /","-", $parent[1]);
			$mapfile = $maplang."_".$level2.".htm";
			if (!file_exists(filename_encode($mapfile))) $mapfile = $imgfile."_".$level2.".htm";
			if (!file_exists($mapfile)) $mapfile = $imgfile.".htm";
			$imgfile = $imgfile."_".$level2.".gif";
			$maplist = $country."_".$level2;
			$mapname = $parent[1];
			
		}

		if (file_exists($imgfile) and file_exists($mapfile)) {
			include ($mapfile);
			$printmap = "<img src='".$imgfile."' usemap='#".$maplist."' border='0' alt='".$mapname."' title='".$mapname."' />";
//			print "<br /><img src='".$imgfile."' usemap='#".$country."' border='0' alt='".$country."' title='".$country."' />";
			?>
			<script type="text/javascript" src="strings.js"></script>
			<script type="text/javascript">
			<!--
			//	copy php array into js array
			var states_accept = new Array(<?php foreach ($placelist as $key => $value) print "'".str_replace("'", "\'", $value)."',"; print "''";?>)
			Array.prototype.in_array = function(val) {
				for (var i in this) {
					if (this[i] == val) return true;
				}
				return false;
			}
			function setPlaceState(txt) {
				if (txt=='') return;
				// search full text [California (CA)]
				var search = txt
				if (states_accept.in_array(search)) return(location.href = 'placelist.php?level=<?php print ($level==1?"2":"3")."&parent[0]=".$parent[0]."&parent[1]=".($level==1?"":$parent[1]."&parent[2]=")?>'+search);
				// search without optional code [California]
				txt = txt.replace(/(\/)/,' ('); // case: finnish/swedish ==> finnish (swedish
				p=txt.indexOf(' (');
				if (p>1) search=txt.substring(0,p);
				else return;
				if (states_accept.in_array(search)) return(location.href = 'placelist.php?level=<?php print ($level==1?"2":"3")."&parent[0]=".$parent[0]."&parent[1]=".($level==1?"":$parent[1]."&parent[2]=")?>'+search);
				// search with code only [CA]
				search=txt.substring(p+2);
				p=search.indexOf(')');
				if (p>1) search=search.substring(0,p);
				if (states_accept.in_array(search)) return(location.href = 'placelist.php?level=<?php print ($level==1?"2":"3")."&parent[0]=".$parent[0]."&parent[1]=".($level==1?"":$parent[1]."&parent[2]=")?>'+search);
			}
			//-->
			</script>
			<?php
		}
	}

	//-- create a string to hold the variable links
	$linklevels="";
	for($j=0; $j<$level; $j++) {
		$linklevels .= "&amp;parent[$j]=".urlencode($parent[$j]);
	}
	$i=0;
	$ct1=count($placelist);

	// -- print the array
	foreach ($placelist as $key => $value) {
		if ($i==0) {
			if (isset($printmap)) {
				print "\n\t<br /><br />\n\t<table width=\"95%\" ><tr><td style=\"text-align: center;\">";
				print $printmap;
				print "</td><td style=\"margin-left:15; vertical-align: top;\">";
			}
			print "\n\t<br /><br />\n\t<table class=\"list_table, $TEXT_DIRECTION\"";
			if ($TEXT_DIRECTION=="rtl") print " dir=\"rtl\"";
			print ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print ">&nbsp;";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
			if ($level>0) {
				print " ".$pgv_lang["place_list_aft"]." ";
				print PrintReady($num_place);
			}
			else print $pgv_lang["place_list"];

			print "&nbsp;";
			print_help_link("ppp_placelist_help", "qm");
			print "</td></tr><tr><td class=\"list_value\"><ul>\n\t\t\t";
		}

		print "<li type=\"square\">\n<a href=\"placelist.php?action=$action&amp;level=".($level+1).$linklevels;
		print "&amp;parent[$level]=".urlencode($value)."\" class=\"list_item\">";

		if (trim($value)=="") print $pgv_lang["unknown"];
		else print PrintReady($value);
		print "</a></li>\n";
	    $i++;
		if ($ct1 > 20){
			if ($i == ceil($ct1 / 3)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
			if ($i == ceil(($ct1 / 3) * 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
		}
		else if ($ct1 > 4 && $i == ceil($ct1 / 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
	}
	if ($i>0){
		print "\n\t\t</ul></td></tr>";
		if (($action!="show")&&($level>0)) {
			print "<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print ">\n\t";
			print $pgv_lang["view_records_in_place"];
			print_help_link("ppp_view_records_help", "qm");
			print "</td></tr><tr><td class=\"list_value\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print " style=\"text-align: center;\">";
			print "<a href=\"placelist.php?action=show&amp;level=$level";
			foreach($parent as $key=>$value) {
				print "&amp;parent[$key]=".urlencode(trim($value));
			}
			print "\"><span class=\"formField\">";
			if (trim($value)=="") print $pgv_lang["unknown"];
			else print PrintReady($value);
			print "</span></a> ";
			print "</td></tr>";
		}
		print "</table>";
	}
	if (isset($printmap)) print "</td></tr></table>";

}

if ($level > 0) {
	if ($action=="show") {
		// -- array of names
		$myindilist = array();
		$mysourcelist = array();
		$myfamlist = array();

		$positions = get_place_positions($parent, $level);
		for($i=0; $i<count($positions); $i++) {
			$gid = $positions[$i];
			$indirec=find_gedcom_record($gid);
			$ct = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
			if ($ct>0) {
				$type = trim($match[2]);
				if ($type == "INDI") {
					$myindilist["$gid"] = get_sortable_name($gid);
				}
				else if ($type == "FAM") {
					$myfamlist["$gid"] = get_family_descriptor($gid);
				}
				else if ($type == "SOUR") {
					$mysourcelist["$gid"] = get_source_descriptor($gid);
				}
			}
		}

		print "\n\t<br /><br /><table class=\"list_table, $TEXT_DIRECTION\">\n\t\t<tr>";
		$ci = count($myindilist);
		$cs = count($mysourcelist);
		$cf = count($myfamlist);
		if ($ci>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["individuals"]."</td>";
		if ($cs>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["sources"]."</td>";
		if ($cf>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
		$i=0;
//		$pass = FALSE;
		$indi_private=0;
		$indi_hide=0;
		$indisurnames = array();
		foreach($myindilist as $gid=>$indi) {
//			if (showLivingNameByID($gid)||displayDetailsByID($gid)) {
				$name = trim($indi);
				$names = preg_split("/,/", $name);
				$indi = check_NN($names);
				$indisurnames[$gid] = array();
				$indisurnames[$gid]["name"] = $indi;
				$indisurnames[$gid]["gid"] = $gid;
//			}
//			else {
//				$pass = TRUE;
//			}
		}
		print "</tr><tr>";
		if ($ci>0) {
			uasort($indisurnames, "itemsort");
			print "\n\t\t<td class=\"list_value_wrap\">";
			print "\n<ul>";
			foreach ($indisurnames as $indexval => $value) {
	    		print_list_person($value["gid"], array($value["name"], $GEDCOM));
				$i++;
			}
			/*
			if ($indi_hide>0) {
				print "<li>".$pgv_lang["hidden"]." (".$indi_hide.")";
				print_help_link("privacy_error_help", "qm");
				print "</li>";
			}*/
			print "\n</ul>";
			print "\n\t\t</td>\n\t\t";
		}
		if ($cs>0) {
			print "<td class=\"list_value_wrap\">";
			print "\n<ul>";
			asort($mysourcelist);
			$i=0;
			foreach ($mysourcelist as $key => $value) {
			    print "\n\t\t\t<li type=\"circle\"><a href=\"source.php?sid=$key\"><span class=\"list_item\">$value</span></a></li>\n";
			    $i++;
			}
			print "\n</ul>";
			print "<br />\n\t\t</td>\n\t\t";
		}
		$surnames = array();
		foreach($myfamlist as $gid=>$fam) {
			// Added space to regexp after z to also remove prefixes
			$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $fam);
			$name = trim($name);
			$names = preg_split("/[,+]/", $name);
			$surname = $names[0];
			$firstname = "";
			if (isset($names[1])) $firstname = trim($names[1]);
			else $surname = "";
			$surname = str2upper(trim($surname));
			if (!isset($surnames[$surname.$firstname.$gid])) {
				$surnames[$surname.$firstname.$gid] = array();
				// Convert names again to include prefixes for displaying
				$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/"), array(",",","), $fam);
				$names = preg_split("/[,+]/", $name);
				$fam = check_NN($names);
				$surnames[$surname.$firstname.$gid]["name"] = $fam;
				$surnames[$surname.$firstname.$gid]["gid"] = $gid;
			}
		}
		$i=0;
		if (isset($surnames)) $ct=count($surnames);
		if ($ct>0) {
			uasort($surnames, "itemsort");
			reset($surnames);
			print "<td class=\"list_value_wrap\">";
			print "\n<ul>";
			foreach ($surnames as $indexval => $value) {
				print_list_family($value["gid"], array($value["name"], $GEDCOM));
				$i++;
			}
			/*if ($fam_hide>0) {
				print "<li>".$pgv_lang["hidden"]." (".$fam_hide.")";
				print_help_link("privacy_error_help", "qm");
				print "</li>";
			}*/
			print "</ul></td>";
		}
		print "\n\t\t</tr><tr>";
		if ($ci>0) {
			print "<td>";
			$ci -= $indi_hide;
			if ($ci>0)print $pgv_lang["total_indis"]." ".$ci;
			if ($indi_private>0) print "&nbsp;(".$pgv_lang["private"]." ".$indi_private.")";
			if ($ci>0 && $indi_hide>0) print "&nbsp;--&nbsp;";
			if ($indi_hide>0) {
				print $pgv_lang["hidden"]." ".$indi_hide;
				print_help_link("privacy_error_help", "qm");
			}
			print "</td>\n";
		}
		if ($cs>0) {
			print "<td>";
			if ($cs>0) print $pgv_lang["total_sources"]." ".$cs;
//			if ($cs>0) $cs." ".print $pgv_lang["total_sources"];
			print "</td>\n";
		}
		if ($cf>0) {
			print "<td>";
			$cf -= $fam_hide;
			if ($cf>0)print $pgv_lang["total_fams"]." ".$cf;
			if ($fam_private>0) print "&nbsp;(".$pgv_lang["private"]." ".$fam_private.")";
			if ($cf>0 && $fam_hide>0) print "&nbsp;--&nbsp;";
			if ($fam_hide>0) {
				print $pgv_lang["hidden"]." ".$fam_hide;
				print_help_link("privacy_error_help", "qm");
			}
			print "</td>\n";
		}
		print "</tr>\n\t</table>";
		print_help_link("ppp_name_list_help", "qm");
		print "<br />";
	}
}

//-- list type display
if ($display=="list") {
	$placelist = array();

	find_place_list("");
	uasort($placelist, "stringsort");
	if (count($placelist)==0) {
		print "<b>".$pgv_lang["no_results"]."</b><br />";
	}
	else {
		print "\n\t<table class=\"list_table, $TEXT_DIRECTION\"";
		if ($TEXT_DIRECTION=="rtl") print " dir=\"rtl\"";
		print ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
		$ct = count($placelist);
		print " colspan=\"".($ct>20?"3":"2")."\">&nbsp;";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
		print $pgv_lang["place_list2"];
		print "&nbsp;";
		print_help_link("ppp_placelist_help2", "qm");
		print "</td></tr><tr><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		$i=0;
		foreach($placelist as $indexval => $revplace) {
			$linklevels = "";
			$levels = preg_split ("/,/", $revplace);		// -- split the place into comma seperated values
			$level=0;
			$revplace = "";
			foreach($levels as $indexval => $place) {
				$place = trim($place);
				$linklevels .= "&amp;parent[$level]=".urlencode($place);
				$level++;
				if ($level>1) $revplace .= ", ";
				if ($place=="") $revplace .= $pgv_lang["unknown"];
				else $revplace .= $place;
			}
			print "<li type=\"square\"><a href=\"placelist.php?action=show&amp;display=hierarchy&amp;level=$level$linklevels\">";
			print PrintReady($revplace)."</a></li>\n";
			$i++;
			if ($ct > 20){
				if ($i == floor($ct / 3)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
				if ($i == floor(($ct / 3) * 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
			}
			else if ($i == floor($ct/2)) print "</ul></td><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		}
		print "\n\t\t</ul></td></tr>\n\t\t";
		if ($i>1) {
			print "<tr><td>";
			if ($i>0) print $pgv_lang["total_unic_places"]." ".$i;
			print "</td></tr>\n";
		}
		print "\n\t\t</table>";
	}
}

if ($display=="list") print "<br /><a href=\"placelist.php?display=hierarchy\">".$pgv_lang["show_place_hierarchy"]."</a><br /><br />\n";
else print "<br /><a href=\"placelist.php?display=list\">".$pgv_lang["show_place_list"]."</a>\n<br /><br />";
if ($hasplaceform) {
	$placeheader = substr($header, $hasplaceform);
	$ct = preg_match("/2 FORM (.*)/", $placeheader, $match);
	if ($ct>0) {
		print  $pgv_lang["form"].$match[1];
		print_help_link("ppp_match_one_help", "qm");
	}
}
else {
	print $pgv_lang["form"].$pgv_lang["default_form"]."  ".$pgv_lang["default_form_info"];
	print_help_link("ppp_default_form_help", "qm");
}

print "<br /><br /></div>";
print_footer();

?>