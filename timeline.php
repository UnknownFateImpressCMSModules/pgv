<?php
/**
 * Display a timeline chart for a group of individuals
 *
 * Use the $pids array to set which individuals to show on the chart
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
 * @subpackage Charts
 * @version $Id: timeline.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require("config.php");
require("includes/functions_charts.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

$bheight = 30;
$placements = array();

$familyfacts = array();

function print_time_fact($factitem) {
	global $baseyear, $topyear, $birthyears, $basexoffset, $baseyoffset, $factcount, $TEXT_DIRECTION, $scale;
	global $factarray, $pgv_lang, $bheight, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_PEDIGREE_PLACES, $placements;
	global $familyfacts, $GEDCOM, $pids;

	$factrec = $factitem[1];
//	$ct = preg_match("/1 (_?[^\s]+)(.*)/", $factrec, $match);
	$ct = preg_match("/1 (\w+)(.*)/", $factrec, $match);
	if ($ct > 0) {
		$fact = trim($match[1]);
		$desc = trim($match[2]);
		if ($fact=="EVEN" || $fact=="FACT") {
			$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
			if ($ct>0) $fact = trim($match[1]);
		}
		$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
		if ($ct>0) {
			//-- check if this is a family fact
			$ct = preg_match("/1 _PGVFS @(.*)@/", $factrec, $fmatch);
			if ($ct>0) {
				$famid = trim($fmatch[1]);
				//-- if we already showed this family fact then don't print it
				if (isset($familyfacts[$famid.$fact])&&($familyfacts[$famid.$fact]!=$factitem["p"])) return;
				$familyfacts[$famid.$fact] = $factitem["p"];
			}
			$datestr = trim($match[1]);
			$date = parse_date($datestr);
			$year = $date[0]["year"];

			$month = $date[0]["mon"];
			$day = $date[0]["day"];
			$xoffset = $basexoffset+20;
			$yoffset = $baseyoffset+(($year-$baseyear) * $scale)-($scale);
			$yoffset = $yoffset + (($month / 12) * $scale);
			$yoffset = $yoffset + (($day / 30) * ($scale/12));
			$yoffset = floor($yoffset);
			$place = round($yoffset / $bheight);
			$i=1;
			$j=0;
			$tyoffset = 0;
			while(isset($placements[$place])) {
				if ($i==$j) {
					$tyoffset = $bheight * $i;
					$i++;
				}
				else {
					$tyoffset = -1 * $bheight * $j;
					$j++;
				}

				$place = round(($yoffset+$tyoffset) / ($bheight));
			}
			$yoffset += $tyoffset;
			$xoffset += abs($tyoffset);
			$placements[$place] = $yoffset;
			//-- do not print hebrew dates
			if (($date[0]["year"]!=0)&&(stristr($date[0]["ext"], "hebrew")===false)) {
				print "\n\t\t<div id=\"fact$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($xoffset):"right: ".($xoffset))."px; top:".($yoffset)."px; font-size: 8pt; height: ".($bheight)."px; \" onmousedown=\"factMD(this, '".$factcount."', ".($yoffset-$tyoffset).");\">\n";
				print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"cursor: hand;\"><tr><td>\n";
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" name=\"boxline$factcount\" id=\"boxline$factcount\" height=\"3\" align=\"left\" hspace=\"0\" width=\"10\" vspace=\"0\" alt=\"\" />\n";
				print "</td><td valign=\"top\" class=\"person".$factitem["p"]."\">\n";
				if (isset($factarray[$fact])) print $factarray[$fact];
				else if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
				else print $fact;
				print "--";
				print "<span class=\"date\">".get_changed_date($datestr)."</span> ";
				if (!empty($desc)) print $desc." ";
				if ($SHOW_PEDIGREE_PLACES>0) {
					$pct = preg_match("/2 PLAC (.*)/", $factrec, $match);
					if ($pct>0) {
						print " - ";
						$plevels = preg_split("/,/", $match[1]);
						for($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
							if (!empty($plevels[$plevel])) {
								if ($plevel>0) print ", ";
								print PrintReady($plevels[$plevel]);
							}
						}
					}
				}
				$age = get_age(find_person_record($factitem["pid"]), $datestr);
				if (!empty($age)) print $age;
				//-- print spouse name for marriage events
				$ct = preg_match("/1 _PGVS @(.*)@/", $factrec, $match);
				if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						for($p=0; $p<count($pids); $p++) {
							if ($pids[$p]==$spouse) break;
						}
						if ($p==count($pids)) $p = $factitem["p"];
						print " <span class=\"person$p\"> <a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						if (displayDetailsById($spouse)||showLivingNameById($spouse)) print get_person_name($spouse);
						else print $pgv_lang["private"];
						print "</a> </span>";
					}
				}
				print "</td></tr></table>\n";
				print "</div>";
				if ($TEXT_DIRECTION=='ltr') {
					$img = "dline2";
					$ypos = "0%";
				}
				else {
					$img = "dline";
					$ypos = "100%";
				}
				$dyoffset = ($yoffset-$tyoffset)+$bheight/3;
				if ($tyoffset<0) {
					$dyoffset = $yoffset+$bheight/3;
					if ($TEXT_DIRECTION=='ltr') {
						$img = "dline";
						$ypos = "100%";
					}
					else {
						$img = "dline2";
						$ypos = "0%";
					}
				}
				//-- print the diagnal line
				print "\n\t\t<div id=\"dbox$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20))."px; top:".($dyoffset)."px; font-size: 8pt; height: ".(abs($tyoffset))."px; width: ".(abs($tyoffset))."px;";
				print " background-image: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$img]["other"]."');";
				print " background-position: 0% $ypos; \" >\n";
				print "</div>\n";
			}
		}
	}
}

if (!isset($pids)){
	if (!isset($newpid)) {
		$pids=array();
		$pids[] = check_rootid("");
	}
	else {
		$newpid = clean_input($newpid);
		$indirec = find_person_record($newpid);
		if (empty($indirec)) {
			if (stristr($newpid, "I")===false) $newpid = "I".$newpid;
		}
		$pids[] = $newpid;
		unset($newpid);
	}
}
if (!is_array($pids)) $pids = array();
else {
	//-- make sure that arrays are indexed by numbers
	$pids = array_values($pids);
}
//-- cleanup user input
foreach($pids as $key=>$value) {
	$pids[$key] = clean_input($value);
}



// aGEDCOM elements that will be found but should not be displayed
$nonfacts = "FAMS,FAMC,MAY,BLOB,OBJE,SEX,NAME,SOUR,NOTE,BAPL,ENDL,SLGC,SLGS,_TODO,CHAN,HUSB,WIFE,CHIL";

print_header($pgv_lang["timeline_title"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
function showhide(divbox, checkbox) {
	if (checkbox.checked) {
		MM_showHideLayers(divbox, ' ', 'show', ' ');
	}
	else {
		MM_showHideLayers(divbox, ' ', 'hide', ' ');
	}
}

var pasteto = null;

function open_find(textbox) {
	pasteto = textbox;
	findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
}
function paste_id(value) {
	pasteto.value=value;
}
//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--
var N = (document.all) ? 0 : 1;
var ob=null;
var Y=0;
var X=0;
var oldx=0;
var personnum=0;
var type=0;
var state=0;
var oldstate=0;
var boxmean = 0;

function ageMD(divbox, num) {
	ob=divbox;
	personnum=num;
	type=0;
	if (N) {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
	}
	else {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
		oldx = event.clientX + document.documentElement.scrollLeft;
	}
}

function factMD(divbox, num, mean) {
	if (ob!=null) return;
	ob=divbox;
	personnum=num;
	boxmean = mean;
	type=1;
	if (N) {
		oldx=ob.offsetLeft;
		oldlinew=0;
	}
	else {
		oldx = ob.offsetLeft;
		oldlinew = event.clientX + document.documentElement.scrollLeft;
	}
}

function MM(e) {
	if (ob) {
		tldiv = document.getElementById("timeline_chart");
		if (!tldiv) tldiv = document.getElementById("timeline_chart_rtl");
		if (type==0) {
			// age boxes
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
			}
			if ((newy >= topy-bheight/2)&&(newy<=bottomy)) newy = newy;
			else if (newy < topy-bheight/2) newy = topy-bheight/2;
			else newy = (bottomy-1);
			ob.style.top = newy+"px";
			tyear = ((newy+bheight-4 - topy) + scale)/scale + baseyear
			year = Math.floor(tyear);
			month = Math.floor((tyear*12)-(year*12));
			day = Math.floor((tyear*365)-(year*365 + month*30));
			mstamp = (year*365)+(month*30)+day;
			bdstamp = (birthyears[personnum]*365)+(birthmonths[personnum]*30)+birthdays[personnum];
			daydiff = mstamp - bdstamp;
			ba = 1;
			if (daydiff < 0 ) {
				ba = -1;
				daydiff = (bdstamp - mstamp);
			}
			yage = Math.floor(daydiff / 365);
			mage = Math.floor((daydiff-(yage*365))/30);
			dage = Math.floor(daydiff-(yage*365)-(mage*30));
			if (dage<0) mage = mage -1;
			if (dage<-30) {
				dage = 30+dage;
			}
			if (mage<0) yage = yage-1;
			if (mage<-11) {
				mage = 12+mage;
			}
			yearform = document.getElementById('yearform'+personnum);
			ageform = document.getElementById('ageform'+personnum);
			yearform.innerHTML = year+"      "+month+" <?php print get_first_letter($pgv_lang["month"]);?>   "+day+" <?php print get_first_letter($pgv_lang["day"]);?>";
			ageform.innerHTML = (ba*yage)+" <?php print get_first_letter($pgv_lang["year"]);?>   "+(ba*mage)+" <?php print get_first_letter($pgv_lang["month"]);?>   "+(ba*dage)+" <?php print get_first_letter($pgv_lang["day"]);?>";
			var line = document.getElementById('ageline'+personnum);
			temp = newx-oldx;
			if (textDirection=='rtl') temp = temp * -1;
			line.style.width=(line.width+temp)+"px";
			oldx=newx;
			return false;
		}
		else {
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
				linewidth = e.pageX;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
				linewidth = event.clientX + document.documentElement.scrollLeft;
			}
			// get diagnal line box
			dbox = document.getElementById('dbox'+personnum);
			// set up limits
			if (boxmean-175 < topy) etopy = topy;
			else etopy = boxmean-175;
			if (boxmean+175 > bottomy) ebottomy = bottomy;
			else ebottomy = boxmean+175;
			// check if in the bounds of the limits
			if ((newy >= etopy)&&(newy<=ebottomy)) newy = newy;
			else if (newy < etopy) newy = etopy;
			else if (newy >ebottomy) newy = ebottomy;
			// calculate the change in Y position
			dy = newy-ob.offsetTop;
			// check if we are above the starting point and switch the background image
			if (newy < boxmean) {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				dy = (-1)*dy;
				state=1;
				dbox.style.top = (newy+bheight/3)+"px";
			}
			else {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}

				dbox.style.top = (boxmean+(bheight/3))+"px";
				state=0;
			}
			// the new X posistion moves the same as the y position
			if (textDirection=='ltr') newx = dbox.offsetLeft+Math.abs(newy-boxmean);
			else newx = dbox.offsetRight+Math.abs(newy-boxmean);
			// set the X position of the box
			if (textDirection=='ltr') ob.style.left=newx+"px";
			else ob.style.right=newx+"px";
			// set new top positions
			ob.style.top = newy+"px";
			// get the width for the diagnal box
			newwidth = (ob.offsetLeft-dbox.offsetLeft);
			// set the width
			dbox.style.width=newwidth+"px";
			if (textDirection=='rtl') dbox.style.right = (dbox.offsetRight - newwidth) + 'px';
			dbox.style.height=newwidth+"px";
			// change the line width to the change in the mouse X position
			line = document.getElementById('boxline'+personnum);
			if (oldlinew!=0) line.width=line.width+(linewidth-oldlinew);
			oldlinew = linewidth;
			oldx=newx;
			oldstate=state;
			return false;
		}
	}
}

function MU() {
	ob = null;
	oldx=0;
}

if (N) {
	document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);
	//document.onmousedown = MD;
}
document.onmousemove = MM;
document.onmouseup = MU;
//-->
</script>
<?php
$indifacts = array();						// array to store the fact records in for sorting and displaying
$birthyears=array();
$birthmonths=array();
$birthdays=array();
$baseyear=date("Y");
$topyear=0;
print "\n\t<h2>".$pgv_lang["timeline_chart"]."</h2>";
if ($view!="preview") print "\n\t\t<form name=\"people\" action=\"timeline.php\">";
if (!empty($pids[0])){
	if (!displayDetailsById($pids[0])) {
		if (showLivingNameById($pids[0])) print "&nbsp;<a href=\"individual.php?pid=$pids[0]\">".PrintReady(get_person_name($pids[0]))."</a>";
		print_privacy_error($CONTACT_EMAIL);
		print "<br />";
		$pids[0]="";
	}
}

if (!empty($newpid)) {
	$newpid = clean_input($newpid);
	$indirec = find_person_record($newpid);
	if (empty($indirec)) {
		if (stristr($newpid, "I")===false) $newpid = "I".$newpid;
	}
	if (!displayDetailsById($newpid)) {
		if ($view!="preview"){
			if (showLivingNameById($newpid)) print "&nbsp;<a href=\"individual.php?pid=$newpid\">".PrintReady(get_person_name($newpid))."</a>";
			print_privacy_error($CONTACT_EMAIL);
			print "<br />";
		}
		unset($newpid);
	}
	else {
		$pids[count($pids)]=$newpid;
	}
}


print "\n\t\t\t<table class=\"timeline_table\"><tr>";
$pidlinks = "";
$pt = count($pids);
$newpids = array();
for($pp=0; $pp<$pt; $pp++) {
	$indirec = find_person_record($pids[$pp]);
	if (empty($indirec)) {
		$pids[$pp]=$GEDCOM_ID_PREFIX.$pids[$pp];
		$indirec = find_person_record($pids[$pp]);
	}
	if (!empty($indirec)&&(displayDetailsById($pids[$pp]))) {
		$pidlinks .= "pids[]=$pids[$pp]&amp;";
		$newpids[] = $pids[$pp];
	}
}
$pids = $newpids;
//-- first get all of the facts
for($p=0; $p<count($pids); $p++) {
	$pid = $pids[$p];
	$indirec = find_person_record($pid);
	if (($indirec!==false)&&(displayDetailsById($pid))) {
		// find all the fact information
		$facts = get_all_subrecords($indirec, $nonfacts, true, false);
		foreach($facts as $indexval => $factrec) {
			//-- get the fact type
			$ct = preg_match("/1 (\w+)(.*)/", $factrec, $match);
			if ($ct > 0) {
				$fact = trim($match[1]);
				$desc = trim($match[2]);
				//-- check for a date
				$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
				if ($ct>0) {
					$datestr = trim($match[1]);
					$date = parse_date($datestr);
					//-- do not print hebrew dates
					if ((stristr($date[0]["ext"], "hebrew")===false)&&($date[0]["year"]!=0)) {
						if ($fact=='BIRT') {
							$birthyears[$pid] = $date[0]["year"];
							$birthmonths[$pid] = $date[0]["mon"];
							$birthdays[$pid] = $date[0]["day"];
						}
						if ($date[0]["year"]<$baseyear) $baseyear=$date[0]["year"];
						if ($date[0]["year"]>$topyear) $topyear=$date[0]["year"];
						if (!is_dead_id($pid)) {
							if ($topyear < date("Y")) $topyear = date("Y");
						}
						$tfact = array();
						$tfact["p"] = $p;
						$tfact["pid"] = $pid;
						$tfact[1] = $factrec;
						$indifacts[] = $tfact;
					}
				}
			}
		}
	}
}
if (empty($scale)) {
	$scale = round(($topyear-$baseyear)/20 * count($indifacts)/4);
	if ($scale<6) $scale = 6;
}
if ($scale<2) $scale=2;

//-- print the field box
for($p=0; $p<count($pids); $p++) {
	$pid = $pids[$p];
	if (preg_match("/[A-Za-z]+/", $pid)==0) $pid = $GEDCOM_ID_PREFIX.$pid;
	$indirec = find_person_record($pid);
	$isF = "NN";
	if (preg_match("/1 SEX F/", $indirec)>0) $isF="F";
	else if (preg_match("/1 SEX M/", $indirec)>0) $isF="";
	if (($indirec!==false)&&(displayDetailsById($pid))) {
		print "<td class=\"person$p\">\n\t\t\t\t";
		print "<img src=\"$PGV_IMAGE_DIR/";
		if ($isF=="") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
		else  if ($isF=="F")print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
		else  print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"];
		print "\" vspace=\"0\" hspace=\"0\" class=\"sex_image\" border=\"0\" />";
 		print "<a href=\"individual.php?pid=$pid\">&nbsp;".PrintReady(get_person_name($pid))."<br />";
		//-- find additional name
		$addname = get_add_person_name($pid);
		if (strlen($addname) > 0) print PrintReady($addname);
		print "</a>";
		print "<input type=\"hidden\" name=\"pids[$p]\" value=\"$pid\" />";
		if ($view!="preview") {
		print "<br /><a href=\"timeline.php?";
		$j=0;
		for($pp=0; $pp<$p; $pp++) {
			print "pids[$j]=$pids[$pp]&amp;";
			$j++;
		}
		for($pp=$p+1; $pp<count($pids); $pp++) {
			print "pids[$j]=$pids[$pp]&amp;";
			$j++;
		}
			if (count($pids)==1) print "pids[0]=&amp;";
		print "scale=$scale";
		print "\">";
			print "<span class=\"details1\">".$pgv_lang["remove_person"]."</span></a>";
			print_help_link("remove_person_help", "qm");
		}
		if ((isset($birthyears[$pid]))&&($view!="preview")) {
			print "\n\t\t\t\t<span class=\"details1\"><br /><input type=\"checkbox\" name=\"agebar$p\" value=\"ON\" onclick=\"showhide('agebox$p', this);\" />".$pgv_lang["show_age"]."</span>";
			print_help_link("show_age_marker_help", "qm");
		}
		print "<br />\n\t\t\t</td>";
	}
	else {
		print "<td class=\"person$p\">\n\t\t\t\t";
		print_privacy_error($CONTACT_EMAIL);
		print "<input type=\"hidden\" name=\"pids[$p]\" value=\"$pid\" />";
		if ($view!="preview") {
		print "<br /><a href=\"timeline.php?";
		$j=0;
		for($pp=0; $pp<$p; $pp++) {
			print "pids[$j]=$pids[$pp]&amp;";
			$j++;
		}
		for($pp=$p+1; $pp<count($pids); $pp++) {
			print "pids[$j]=$pids[$pp]&amp;";
			$j++;
		}
			if (count($pids)==1) print "pids[0]=&amp;";
		print "scale=$scale";
		print "\">";
			print "<span class=\"details1\">".$pgv_lang["remove_person"]."</span></a>";
			print_help_link("remove_person_help", "qm");
		}
		print "<br />\n\t\t\t</td>";
	}
}

if (($p<6)&&($view!="preview")) {
	print "\n\t\t\t\t<td class=\"person$p\" valign=\"top\">".$pgv_lang["add_another"]."  ";
	print_help_link("add_person_help", "qm");
	print "<input class=\"pedigree_form\" type=\"text\" size=\"5\" name=\"newpid\" /> <font size=\"1\"><a href=\"javascript:open_find(document.people.newpid);\">".$pgv_lang["find_id"]."</a></font> <input type=\"submit\" value=\"".$pgv_lang["show"]."\" /></td>";
}
print "<td>";
if (($p!=0)&&($view!="preview")) {
	print "<a href=\"$PHP_SELF?".$pidlinks."scale=".($scale+2)."\">".$pgv_lang["zoom_in"]."</a><br />";
	print "<a href=\"$PHP_SELF?".$pidlinks."scale=".($scale-2)."\">".$pgv_lang["zoom_out"]."</a>";
}
print "</td>";
print "</tr></table>\n";
//print "<input type=\"text\" name=\"test\" />\n";
if ($view!="preview") print "</form>";
$baseyear -= 5;
$topyear += 5;
// if there are no ids coming in then don't try to print info for them
if ($p!=0) {
	if ($view!="preview") print "\n\t".$pgv_lang['timeline_instructions']."<br /><br />";
	print "<div id=\"timeline_chart\">\n";
	//-- print the timeline line image
	print "\n\t\t<div id=\"line\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20))."px; top:".($baseyoffset)."px; \">\n";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" width=\"3\" height=\"".($baseyoffset+(($topyear-$baseyear)*$scale))."\" alt=\"\" ";
	print " />\n";
	print "</div>";
	//-- print divs for the grid
	print "\n\t\t<div id=\"scale$baseyear\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".($baseyoffset-5)."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
	print $baseyear."--";
	print "</div>";
	for($i=$baseyear+1; $i<$topyear; $i++) {
		if ($i % (25/$scale)==0)  {
			print "\n\t\t<div id=\"scale$i\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($i-$baseyear)*$scale)-$scale/2)."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
			print $i."--";
			print "</div>";
		}
	}
	print "\n\t\t<div id=\"scale$topyear\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($topyear-$baseyear)*$scale))."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
	print $topyear."--";
	print "</div>";
	usort($indifacts, "compare_facts");
	$factcount=0;
	foreach($indifacts as $indexval => $fact) {
		print_time_fact($fact);
		$factcount++;
	}

	//print_r($placements);
	// print the age boxes
	for($p=0; $p<count($pids); $p++) {
		$ageyoffset = $baseyoffset + ($bheight*$p);
		print "\n\t\t<div id=\"agebox$p\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20))."px; top:".$ageyoffset."px; height:".$bheight."px; visibility: hidden;\" onmousedown=\"ageMD(this, $p);\">";
		print "\n\t\t\t<table cellspacing=\"0\" cellpadding=\"0\"><tr><td>";
		print "\n\t\t\t<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" name=\"ageline$p\" id=\"ageline$p\" align=\"left\" hspace=\"0\" vspace=\"0\" width=\"25\" height=\"3\" alt=\"\" />";
		print "\n\t\t\t</td><td valign=\"top\">";
		$tyear = round(($ageyoffset+($bheight/2))/$scale)+$baseyear;
		if (!empty($birthyears[$pids[$p]])) {
			$tage = $tyear-$birthyears[$pids[$p]];
			print "\n\t\t\t<table class=\"person$p\" style=\"cursor: hand;\"><tr><td valign=\"top\" width=\"120\">".$pgv_lang["year"]."  <span id=\"yearform$p\" class=\"field\">";
			print " $tyear</span></td><td valign=\"top\" width=\"130\">(".$pgv_lang["age"]."  <span id=\"ageform$p\" class=\"field\">$tage</span>) ";
			print "\n\t\t\t</td></tr></table>";
		}
		print "\n\t\t\t</td></tr></table>\n\t\t<br /><br /><br /></div><br /><br /><br /><br />";
	}

	print "\n<script language=\"JavaScript\" type=\"text/javascript\">";
	print "\n<!--\nvar bottomy = ".($baseyoffset+(($topyear-$baseyear)*$scale))."-5;";
	print "\nvar topy = ".($baseyoffset).";";
	print "\nvar baseyear = $baseyear-(25/$scale);";
	print "\nvar birthyears = new Array();";
	print "\nvar birthmonths = new Array();";
	print "\nvar birthdays = new Array();";
	for($c=0; $c<count($pids); $c++) {
		if (isset($birthyears[$pids[$c]])) print "\nbirthyears[".$c."]=".$birthyears[$pids[$c]].";";
		if (isset($birthmonths[$pids[$c]])) print "\nbirthmonths[".$c."]=".$birthmonths[$pids[$c]].";";
		if (isset($birthdays[$pids[$c]])) print "\nbirthdays[".$c."]=".$birthdays[$pids[$c]].";";
	}
	print "\nvar bheight=$bheight;";
	print "\nvar scale=$scale;";
	print "\n//-->\n</script>\n";

	print "</div>\n";
}
?>
<script language="JavaScript" type="text/javascript">
	timeline_chart_div = document.getElementById("timeline_chart");
	if (!timeline_chart_div) timeline_chart_div = document.getElementById("timeline_chart_rtl");
	if (timeline_chart_div) timeline_chart_div.style.height = '<?php print $baseyoffset+(($topyear-$baseyear)*$scale*1.1); ?>px';
</script>
<?php

print_footer();

?>