<?php
/**
 * Function for printing
 *
 * Various printing functions used by all scripts and included by the functions.php file.
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
 * @version $Id: functions_print.php,v 1.4 2006/03/06 00:26:33 skenow Exp $
 */
if (strstr($_SERVER["PHP_SELF"],"functions")) {
	 print "Now, why would you want to do that. You're not hacking are you?";
	 exit;
}

/**
 * print a submitter record
 *
 * find and print submitter information
 * @param string $sid  the Gedcom Xref ID of the submitter to print
 */
function print_submitter_info($sid) {
	 $srec = find_gedcom_record($sid);
	 preg_match("/1 NAME (.*)/", $srec, $match);
	 // PAF creates REPO record without a name
	 // Check here if REPO NAME exists or not
	 if (isset($match[1])) print "$match[1]<br />";
	 print_address_structure($srec, 1);
	 print_media_links($srec, 1);
}

/**
 * print a repository record
 *
 * find and print repository information attached to a source
 * @param string $sid  the Gedcom Xref ID of the repository to print
 */
function print_repository_record($sid) {
	 global $pgv_lang;
	 $source = find_gedcom_record($sid);
	 $ct = preg_match("/1 NAME (.*)/", $source, $match);
	 if ($ct > 0) {
		 $ct2 = preg_match("/0 @(.*)@/", $source, $rmatch);
		 if ($ct2>0) $rid = trim($rmatch[1]);
		 print "<span class=\"label\">".$pgv_lang["repo_name"]."</span> <span class=\"field\"><a href=\"repo.php?rid=$rid\">".PrintReady($match[1])."</a></span><br />";
	 }
	 print_address_structure($source, 1);
	 print_fact_notes($source, 1);
}

/**
 * print a person in a list
 *
 * This function will print a
 * clickable link to the individual.php
 * page with the person's name
 * lastname, firstname and their
 * birthplace and date
 * @author John Finlay
 * @param string $key the GEDCOM xref id of the person to print
 * @param array $value is an array of the form array($name, $GEDCOM)
 */
function print_list_person($key, $value) {
	global $pgv_lang, $PHP_SELF, $pass, $indi_private, $indi_hide, $factarray;
	global $GEDCOM, $SHOW_ID_NUMBERS, $TEXT_DIRECTION, $SHOW_PEDIGREE_PLACES, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_DEATH_LISTS;

	$GEDCOM = $value[1];
	if (!isset($indi_private)) $indi_private=0;
	if (!isset($indi_hide)) $indi_hide=0;
	$disp = displayDetailsByID($key);
	if (showLivingNameByID($key)||$disp) {
		print "<li>";

//		if (begRTLText($value[0]))                            //-- For future use
//			 print "<li class=\"rtl\" dir=\"rtl\">";
//		else print "<li class=\"ltr\" dir=\"ltr\">";

		print "<a href=\"individual.php?pid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		if ($SHOW_ID_NUMBERS){
		   if ($TEXT_DIRECTION=="ltr") print " <span dir=\"ltr\">($key)</span>";
  		   else print " <span dir=\"rtl\">($key)</span>";
		}
		if (!$disp) {
			print " -- <i>".$pgv_lang["private"]."</i>";
			$indi_private++;
		}
		else {
			$fact = print_first_major_fact($key);
			if (isset($SHOW_DEATH_LISTS) && $SHOW_DEATH_LISTS==true) {
				if ($fact!="DEAT") {
					$indirec = find_person_record($key);
					$factrec = get_sub_record(1, "1 DEAT", $indirec);
					if (strlen($factrec)>7 and showFact("DEAT", $key) and !FactViewRestricted($key, $factrec)) {
						print " -- <i>";
						print $factarray["DEAT"];
						print " ";
						print_fact_date($factrec);
						print_fact_place($factrec);
						print "</i>";
					}
				}
			}
		}
		print "</a></li>";
	}
	else {
		$pass = TRUE;
		$indi_hide++;
	}
}

//-- print information about a family for a list view
function print_list_family($key, $value, $findid=false) {
	global $pgv_lang, $pass, $fam_private, $fam_hide, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS;
	global $GEDCOM, $HIDE_LIVE_PEOPLE, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION;

	$GEDCOM = $value[1];
	if (!isset($fam_private)) $fam_private=0;
	if (!isset($fam_hide)) $fam_hide=0;
	$famrec=find_family_record($key);
	$display = displayDetailsByID($key, "FAM");
	$showLivingHusb=true;
	$showLivingWife=true;
	$parents = find_parents($key);
	//-- check if we can display both parents
	if (!$display) {
		$showLivingHusb=showLivingNameByID($parents["HUSB"]);
		$showLivingWife=showLivingNameByID($parents["WIFE"]);
	}

	if ($showLivingWife && $showLivingHusb) {

//		if (begRTLText($value[0]))                                   // To be used after 3.2
//			 print "<li class=\"rtl\" dir=\"rtl\">";
//		else print "<li class=\"ltr\" dir=\"ltr\">";

		print "<li>";
		if ($findid == true) print "<a href=\"#\" onclick=\"pasteid('".$key."'); return false;\" class=\"list_item\"><b>".$value[0]."</b>";
		else print "<a href=\"family.php?famid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";

		if ($SHOW_FAM_ID_NUMBERS)
			if ($TEXT_DIRECTION=="ltr")	print " <span dir=\"ltr\">($key)</span>";
  			else print " <span dir=\"rtl\">($key)</span>";

		if (!$display) {
			print " -- <i>".$pgv_lang["private"]."</i>";
			$fam_private++;
		}
		else {
			$bpos1 = strpos($famrec, "1 MARR");
			if ($bpos1) {
				$birthrec = get_sub_record(1, "1 MARR", $famrec);
				if (!FactViewRestricted($key, $birthrec)) {
					print " -- <i>".$pgv_lang["marriage"]." ";
					$bt = preg_match("/1 \w+/", $birthrec, $match);
					if ($bt>0) {
						 $bpos2 = strpos($birthrec, $match[0]);
						 if ($bpos2) $birthrec = substr($birthrec, 0, $bpos2);
					}
					print_fact_date($birthrec);
					print_fact_place($birthrec);
				}
				print "</i>";
			}
		}
		print "</a>";
		print "</li>\n";
	}															//begin re-added by pluntke
	if (!$showLivingWife || !$showLivingHusb) {				   	//fixed THIS line (changed && to ||)
		$pass = TRUE;
		$fam_hide++;
	}															//end re-added by pluntke
}

/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid	the Gedcom Xref ID of the   to print
 * @param int $style	the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param boolean $show_famlink	set to true to show the icons for the popup links and the zoomboxes
 * @param int $count	on some charts it is important to keep a count of how many boxes were printed
 */
function print_pedigree_person($pid, $style=1, $show_famlink=true, $count=0) {
	 global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $PRIV_PUBLIC, $factarray, $ZOOM_BOXES, $LINK_ICONS, $view, $PHP_SELF, $GEDCOM;
	 global $pgv_lang, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $show_full, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	 global $CONTACT_EMAIL, $CONTACT_METHOD, $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES, $ABBREVIATE_CHART_LABELS;
	 global $chart_style, $box_width, $generations;
	 global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE;

	 flush();
	 if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	 if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;
	 if (!isset($show_full)) $show_full=$PEDIGREE_FULL_DETAILS;
	 if ($pid==false) {
			   print "\n\t\t\t<div id=\"out-".rand()."\" class=\"person_boxNN\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\">";
		  print "<br />";
		  print "\n\t\t\t</div>";
		  return false;
	 }
	 $lbwidth = $bwidth*.75;
	 if ($lbwidth < 150) $lbwidth = 150;
	 $indirec=find_person_record($pid);
	 if (!$indirec) $indirec = find_record_in_file($pid);
	 $isF = "NN";
	 if (preg_match("/1 SEX F/", $indirec)>0) $isF="F";
	 else if (preg_match("/1 SEX M/", $indirec)>0) $isF="";
	 $disp = displayDetailsByID($pid, "INDI");
	 if ($disp || showLivingNameByID($pid)) {
		  if ($show_famlink) {
			   if ($LINK_ICONS!="disabled") {
					//-- draw a box for the family popup
					print "\n\t\t<div id=\"I".$pid.".".$count."links\" style=\"position:absolute; ";
					print "left: 0px; top:0px; width: ".($lbwidth)."px; visibility:hidden; z-index:'100';\">";
					print "\n\t\t\t<table class=\"person_box$isF\"><tr><td class=\"details1\">";
					print "<a href=\"pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["index_header"]."</b></a>\n";
					print "<br /><a href=\"descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["descend_chart"]."</b></a><br />\n";
					$username = getUserName();
					if (!empty($username)) {
						 $tuser=getUser($username);
						 if (!empty($tuser["gedcomid"][$GEDCOM])) {
							  print "<a href=\"relationship.php?pid1=".$tuser["gedcomid"][$GEDCOM]."&amp;pid2=".$pid."&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["relationship_to_me"]."</b></a><br />\n";
						 }
					}
					if (file_exists("ancestry.php")) print "<a href=\"ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["ancestry_chart"]."</b></a><br />\n";
					if (file_exists("fanchart.php") and defined("IMG_ARC_PIE") and function_exists("imagettftext"))  print "<a href=\"fanchart.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["fan_chart"]."</b></a><br />\n";
					if (file_exists("hourglass.php")) print "<a href=\"hourglass.php?pid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["hourglass_chart"]."</b></a><br />\n";
					$ct = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						 $famid = $match[$i][1];
						 $famrec = find_family_record($famid);
						 if ($famrec) {
							  $parents = find_parents_in_record($famrec);
							  $spouse = "";
							  if ($pid==$parents["HUSB"]) $spouse = $parents["WIFE"];
							  if ($pid==$parents["WIFE"]) $spouse=$parents["HUSB"];
							  $num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
							  if ((!empty($spouse))||($num>0)) {
								   print "<a href=\"family.php?famid=$famid&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["fam_spouse"]."</b></a><br /> \n";
								if (!empty($spouse)) {
									print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid."');\">";
 									   if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($spouse))||(showLivingNameByID($spouse))) print PrintReady(get_person_name($spouse));
									   else print $pgv_lang["private"];
									   print "</a><br />\n";
								}
							  }
							  for($j=0; $j<$num; $j++) {
								   $cpid = $smatch[$j][1];
								   print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"individual.php?pid=$cpid&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">";
 								   if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($cpid))||(showLivingNameByID($cpid))) print PrintReady(get_person_name($cpid));
								   else print $pgv_lang["private"];
								   print "<br /></a>";
							  }
						 }
					}
					print "</td></tr></table>\n\t\t</div>";
			   }
			   print "\n\t\t\t<div id=\"out-$pid.$count\"";
			   if ($style==1) print " class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden; z-index:'-1';\"";
			   else print " style=\"padding: 2px;\"";
			   if (($ZOOM_BOXES!="disabled")&&(!$show_full)) {
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('$pid.$count', $style); return false;\" onmouseout=\"restorebox('$pid.$count', $style); return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('$pid.$count', $style);\" onmouseup=\"restorebox('$pid.$count', $style);\"";
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print " onclick=\"expandbox('$pid.$count', $style);\"";
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
			   //-- links and zoom icons
			   if ($TEXT_DIRECTION == "rtl") {
					print "<div id=\"icons-$pid.$count\" style=\"float:left; width: 25px; height: 50px;";
			   } else {
					print "<div id=\"icons-$pid.$count\" style=\"float:right; width: 25px; height: 50px;";
			   }
			   if ($show_full) print " display: block;";
			   else print " display: none;";
			   print "\">";
			   if ($LINK_ICONS!="disabled") {
					$click_link="#";
					if (preg_match("/pedigree.php/", $PHP_SELF)>0) $click_link="pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM";
					if (preg_match("/hourglass.php/", $PHP_SELF)>0) $click_link="hourglass.php?pid=$pid&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if (preg_match("/ancestry.php/", $PHP_SELF)>0) $click_link="ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if (preg_match("/descendancy.php/", $PHP_SELF)>0) $click_link="descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if ((preg_match("/family.php/", $PHP_SELF)>0)&&!empty($famid)) $click_link="family.php?famid=$famid&amp;ged=$GEDCOM";
					if (preg_match("/individual.php/", $PHP_SELF)>0) $click_link="individual.php?pid=$pid&amp;ged=$GEDCOM";
					print "<a href=\"$click_link\" ";
					if ($LINK_ICONS=="mouseover") print "onmouseover=\"show_family_box('".$pid.".".$count."', '";
					if ($LINK_ICONS=="click") print "onclick=\"toggle_family_box('".$pid.".".$count."', '";
					if ($style==1) print "box$pid";
					else print "relatives";
					print "');";
					print " return false;\" ";
					print "onmouseout=\"family_box_timeout('".$pid.".".$count."');";
					print " return false;\"";
					if (($click_link=="#")&&($LINK_ICONS!="click")) print "onclick=\"return false;\"";
					print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"]."\" width=\"25\" border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".$pgv_lang["person_links"]."\" title=\"".$pgv_lang["person_links"]."\" /></a>";
			   }
			   if (($ZOOM_BOXES!="disabled")&&($show_full)) {
					print "<a href=\"#\"";
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('$pid.$count', $style);\" onmouseout=\"restorebox('$pid.$count', $style);\" onclick=\"return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('$pid.$count', $style);\" onmouseup=\"restorebox('$pid.$count', $style);\"";
					if ($ZOOM_BOXES=="click") print " onclick=\"expandbox('$pid.$count', $style); return false;\"";
					print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]."\" width=\"25\" height=\"25\" border=\"0\" alt=\"".$pgv_lang["zoom_box"]."\" title=\"".$pgv_lang["zoom_box"]."\" /></a>";
			   }
			   print "</div>\n";
		  }
		  else {
			   if ($style==1) {
					print "\n\t\t\t<div id=\"out-$pid.$count\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"";
			   }
			   else {
					print "\n\t\t\t<div id=\"out-$pid.$count\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"";
			   }
			   if ($ZOOM_BOXES!="disabled") {
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('$pid.$count', $style); return false;\" onmouseout=\"restorebox('$pid.$count', $style); return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('$pid.$count', $style);\" onmouseup=\"restorebox('$pid.$count', $style);\"";
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print " onclick=\"expandbox('$pid.$count', $style);\"";
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  }
	 }
	 else {
		  if ($style==1) print "\n\t\t\t<div id=\"out-$pid.$count\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  else print "\n\t\t\t<div id=\"out-$pid.$count\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
	 }

	 //-- find the name
	 $name = get_person_name($pid);
	 if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $pid)) {
		  $object = find_highlighted_object($pid, $indirec);
		  if (!empty($object["thumb"])) {
			   $size = @getimagesize($object["thumb"]);
			   $class = "pedigree_image_portrait";
			   if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
			   if($TEXT_DIRECTION == "rtl") $class .= "_rtl";
			   print "<img id=\"box-$pid.$count-thumb\" src=\"".$object["thumb"]."\" vspace=\"0\" hspace=\"0\" class=\"$class\" alt =\"\"";
			   if (!$show_full) print " style=\"display: none;\"";
			   print " />\n";
		  }
	 }

	 //-- find additional name
	 $addname = get_add_person_name($pid);

	 //-- check if the persion is visible
	 if (!$disp) {
		  if (showLivingName($indirec)) {
			   print "<img id=\"box-$pid.$count-sex\" src=\"$PGV_IMAGE_DIR/";
			   if ($isF=="") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
			   else  if ($isF=="F")print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
			   else  print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
			   print "\" class=\"sex_image\" />";
			   print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\"><span id=\"namedef-$pid.$count\" ";

			   if (hasRTLText($name) && $style=="1")
					print "class=\"name2\">";
			   else print "class=\"name$style\">";

 			   print PrintReady($name);

			   if ($SHOW_ID_NUMBERS) {
					print "</span><span class=\"details$style\">";
		      	    if ($TEXT_DIRECTION=="ltr") print " &lrm;($pid)&lrm;";
			        else print " &rlm;($pid)&rlm;";
					print "</span>";
			   }
			  if (strlen($addname) > 0) {
				   print "<br />";
				   if (hasRTLText($addname) && $style=="1")
						print "<span id=\"addnamedef-$pid.$count\" class=\"name2\"> ";
				   else print "<span id=\"addnamedef-$pid.$count\" class=\"name$style\"> ";
 				   print PrintReady($addname)."</span><br />";
			 }
		     print "</a>";
		  }
		  else {
			   $user = getUser($CONTACT_EMAIL);
			   print "<a href=\"#\" onclick=\"if (confirm('".preg_replace("'<br />'", " ", $pgv_lang["privacy_error"])."\\n\\n".str_replace("#user[fullname]#", $user["fullname"], $pgv_lang["clicking_ok"])."')) ";
			   if ($CONTACT_METHOD!="none") {
					if ($CONTACT_METHOD=="mailto") print "window.location = 'mailto:".$user["email"]."'; ";
					else print "message('$CONTACT_EMAIL', '$CONTACT_METHOD'); ";
			   }
			   print "return false;\"><span id=\"namedef-$pid.$count\" class=\"name$style\">".$pgv_lang["private"]."</span></a>\n";
		  }
		  if ($show_full) {
			   print "<br /><span id=\"fontdef-$pid.$count\" class=\"details$style\">";
			   print $pgv_lang["private"];
			   print "</span>";
		  }
		  print "\n\t\t\t</td></tr></table></div>";
		  return;
	 }
	 print "<span class=\"name$style\">";
	 print "<img id=\"box-$pid.$count-sex\" src=\"$PGV_IMAGE_DIR/";
	 if ($isF=="") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
	 else  if ($isF=="F")print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
	 else  print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
	 print "\" class=\"sex_image\" />";
	 print "</span>\r\n";
	 print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\"";
	 if (! $show_full) {
		  //not needed or wanted for mouseover //if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="mousedown") print "onmousedown=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="click") print "onclick=\"event.cancelBubble = true;\"";
	 }
	 if (hasRTLText($name) && $style=="1")
		  print "><span id=\"namedef-$pid.$count\" class=\"name2";
	 else print "><span id=\"namedef-$pid.$count\" class=\"name$style";

	 // add optional CSS style for each fact
	 $cssfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","CAST","DSCR","EDUC","IDNO",
	 "NATI","NCHI","NMR","OCCU","PROP","RELI","RESI","SSN","TITL","BAPL","CONL","ENDL","SLGC","_MILI");
	 foreach($cssfacts as $indexval => $fact) {
		  $ct = preg_match_all("/1 $fact/", $indirec, $nmatch, PREG_SET_ORDER);
		  if ($ct>0) print " $fact";
	 }
	 print "\">";
	 print PrintReady($name);
	 print "</span>";
	 if ($SHOW_ID_NUMBERS) {
			if ($TEXT_DIRECTION=="ltr") print "<span class=\"details$style\"> &lrm;($pid)&lrm; </span>";
			else print "<span class=\"details$style\"> &rlm;($pid)&rlm; </span>";
	 }
	 if ($SHOW_LDS_AT_GLANCE) {
		 print "<span class=\"details$style\">".get_lds_glance($indirec)."</span>";
	 }
	  if (strlen($addname) > 0) {
		   print "<br />";
		   if (hasRTLText($addname) && $style=="1")
				print "<span id=\"addnamedef-$pid.$count\" class=\"name2\"> ";
		   else print "<span id=\"addnamedef-$pid.$count\" class=\"name$style\"> ";
		   print PrintReady($addname)."</span><br />";
	 }
	 print "</a>";
	 if (!$show_full) print "\n<div id=\"inout-$pid.$count\" style=\"display: none;\">\n";
	 print "<div id=\"fontdef-$pid.$count\" class=\"details$style\">";

	 $birttag = "BIRT";
	 $bpos1 = strpos($indirec, "1 BIRT");
	 if ($bpos1) {
	 	if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
	 }
	 //-- no birth check for christening or baptism
	 else {
		  $bpos1 = strpos($indirec, "1 CHR");
		  if ($bpos1) {
			   $birttag = "CHR";
			   if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
		  }
		  else {
			   $bpos1 = strpos($indirec, "1 BAPM");
			   if ($bpos1) {
					$birttag = "BAPM";
					if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
			   }
		  }
	 }
	 //-- section to display optional tags in the boxes
	 if (!empty($CHART_BOX_TAGS)) {
		 $opt_tags = preg_split("/[, ]+/", $CHART_BOX_TAGS);
		 foreach($opt_tags as $indexval => $tag) {
			 if (!empty($tag)) {
			 	if (showFact($tag, $pid)) print_simple_fact($indirec, $tag, $pid);
		 	}
		 }
	 }

	 //-- find all level 1 sub records
	  $skipfacts = array($birttag,"DEAT","SEX","FAMS","FAMC","NAME","TITL","NOTE","SOUR","SSN","OBJE","HUSB","WIFE","CHIL","ALIA","ADDR","PHON","SUBM","_EMAIL","CHAN","URL","EMAIL","WWW");
	  $subfacts = get_all_subrecords($indirec, implode(",", $skipfacts));

	  if ($show_full) print "\n<div id=\"inout-$pid.$count\" style=\"display: none;\">\n";
	  $f2 = 0;
	  foreach($subfacts as $indexval => $factrec) {
		  if (!FactViewRestricted($pid, $factrec)){
			if ($f2>0) print "<br />\n";
			$f2++;
			// handle ASSO record
			if (strstr($factrec, "1 ASSO")) {
				print_asso_rela_record($pid, $factrec, false);
				continue;
			}
//			$fft = preg_match("/^1 ([_A-Z]{3,5})(.*)/m", $factrec, $ffmatch);
			$fft = preg_match("/^1 (\w+)(.*)/m", $factrec, $ffmatch);
			if ($fft>0) {
					$fact = trim($ffmatch[1]);
					$details = trim($ffmatch[2]);
				}
			if (($fact!="EVEN")&&($fact!="FACT")) print "<span class=\"details_label\">".$factarray[$fact]."</span> ";
			else {
				$tct = preg_match("/2 TYPE (.*)/", $factrec, $match);
				if ($tct>0) {
					 $facttype = trim($match[1]);
					 print "<span class=\"details_label\">";
					 if (isset($factarray[$facttype])) print $factarray[$facttype];
					 else print $facttype;
					 print "</span> ";
				}
			}
			if (get_sub_record(2, "2 DATE", $factrec)=="") {
				if ($details=="Y") print $pgv_lang["yes"];
				else if ($details=="N") print $pgv_lang["no"];
				else print PrintReady($details);
			}
			else print PrintReady($details);
			print_fact_date($factrec, false, false, $fact, $pid, $indirec);
			//-- print spouse name for marriage events
			$ct = preg_match("/_PGVFS @(.*)@/", $factrec, $match);
			if ($ct>0) {
				$famid = $match[1];
			}
			$ct = preg_match("/_PGVS @(.*)@/", $factrec, $match);
			if ($ct>0) {
				$spouse=$match[1];
				if ($spouse!=="") {
					 print " <a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
					 if (displayDetailsById($spouse)||showLivingNameById($spouse)) print PrintReady(get_person_name($spouse));
					 else print $pgv_lang["private"];
					 print "</a>";
				}
				if (($view!="preview") && ($spouse!=="")) print " - ";
				if ($view!="preview") print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"]."]</a>\n";
			}
			print_fact_place($factrec, true, true);
		}
	  }
	 print "</div>\n";

	 $bpos1 = strpos($indirec, "1 DEAT");
	 if ($bpos1) {
		  if (showFact("DEAT", $pid)) {
			  print_simple_fact($indirec, "DEAT", $pid);
		  }
	 }
	 print "</div>";
	 print "\n\t\t\t</td></tr></table></div>";
}

/**
 * print out standard HTML header
 *
 * this funciton will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
 * other auxillary files needed to run PGV.  It will also include the theme specific header file.
 * This function should be called by every page before anything is output.
 * @param string $title	the title to put in the <TITLE></TITLE> header tags
 * @param string $head
 * @param boolean $use_alternate_styles
 */
function print_header($title, $head="",$use_alternate_styles=true) {
	global $pgv_lang, $bwidth;
	global $HOME_SITE_URL, $HOME_SITE_TEXT, $SERVER_URL;
	global $view, $cart;
	global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR, $PGV_DATABASE, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD, $INDEX_DIRECTORY;
	global $PHP_SELF, $QUERY_STRING, $action, $query, $changelanguage,$theme_name;
	global $FAVICON, $stylesheet, $print_stylesheet, $rtl_stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile;
	global $PGV_IMAGES, $TEXT_DIRECTION, $ONLOADFUNCTION,$REQUIRE_AUTHENTICATION, $SHOW_SOURCES;
	global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE, $META_SURNAME_KEYWORDS;
//	header("Content-Type: text/html; charset=$CHARACTER_SET");
//	print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
//	print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n\t<head>\n\t\t";
//	print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t\t";
//	if( $FAVICON ) {
//	   print "<link rel=\"shortcut icon\" href=\"$FAVICON\" type=\"image/x-icon\"></link>\n\t\t";
//	}
	if (isset ($META_TITLE) && $META_TITLE != '')
	{
		$META_TITLE .= ' - ';
	}
	else
	{
		$META_TITLE = '';
	}
//	if (!isset($META_TITLE)) $META_TITLE = "";
//	print "<title>".PrintReady(strip_tags($title)." - ".$META_TITLE." - PhpGedView", TRUE)."</title>\n\t";
$title = PrintReady(strip_tags($title)." - {$META_TITLE}PhpGedView", true);
$GLOBALS['wrapper']['header1'] = str_replace('{pgvtitle}', $title, $GLOBALS['wrapper']['header1']);
$GLOBALS['wrapper']['header2'] = str_replace('{pgvtitle}', $title, $GLOBALS['wrapper']['header2']);
print $GLOBALS['wrapper']['header1'];

	 if (!$REQUIRE_AUTHENTICATION){
		print "<link href=\"" . $SERVER_URL .  "rss.php\" rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" />\n\t";
	 }



	 print "<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) print "<link rel=\"stylesheet\" href=\"$rtl_stylesheet\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 if ($use_alternate_styles) {
	 $sheet = $THEME_DIR;
	 if (stristr($_SERVER["HTTP_USER_AGENT"], "Opera"))
		  print "<link rel=\"stylesheet\" href=\"".$sheet."opera.css\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 else if (stristr($_SERVER["HTTP_USER_AGENT"], "Netscape"))
		  print "<link rel=\"stylesheet\" href=\"".$sheet."netscape.css\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 else if (stristr($_SERVER["HTTP_USER_AGENT"], "Gecko"))
		  print "<link rel=\"stylesheet\" href=\"".$sheet."mozilla.css\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 else if (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
		  print "<link rel=\"stylesheet\" href=\"".$sheet."msie.css\" type=\"text/css\" media=\"screen\"></link>\n\t";
	 }


	 print "<link rel=\"stylesheet\" href=\"$print_stylesheet\" type=\"text/css\" media=\"print\"></link>\n\t";

	 if (@strstr($_SERVER["HTTP_USER_AGENT"], "IE")) print "<style type=\"text/css\">\nFORM { margin-top: 0px; margin-bottom: 0px; }\n</style>\n";
	 if($TEXT_DIRECTION == "rtl") print "\n<style type=\"text/css\">body{ direction: rtl;	text-align: right; } ul{ background-position: right;} </style>";
	 print "<!-- PhpGedView -->\n";
	 if (isset($changelanguage)) {
		  $terms = preg_split("/[&?]/", $QUERY_STRING);
		  $vars = "";
		  for ($i=0; $i<count($terms); $i++) {
			   if ((!empty($terms[$i]))&&(strstr($terms[$i], "changelanguage")===false)&&(strpos($terms[$i], "NEWLANGUAGE")===false)) {
					$vars .= $terms[$i]."&";
			   }
		  }
		  $query_string = $vars;
	 }
	 else $query_string = $QUERY_STRING;
	 if ($view!="preview") {
		 $old_META_AUTHOR = $META_AUTHOR;
		 $old_META_PUBLISHER = $META_PUBLISHER;
		 $old_META_COPYRIGHT = $META_COPYRIGHT;
		 $old_META_DESCRIPTION = $META_DESCRIPTION;
		 $old_META_PAGE_TOPIC = $META_PAGE_TOPIC;

		  $cuser = getUser($CONTACT_EMAIL);
		  if ($cuser) {
			  if (empty($META_AUTHOR)) $META_AUTHOR = $cuser["fullname"];
			  if (empty($META_PUBLISHER)) $META_PUBLISHER = $cuser["fullname"];
			  if (empty($META_COPYRIGHT)) $META_COPYRIGHT = $cuser["fullname"];
		  }
		  if (!empty($META_AUTHOR)) print "<meta name=\"author\" content=\"".$META_AUTHOR."\" />\n";
		  if (!empty($META_PUBLISHER)) print "<meta name=\"publisher\" content=\"".$META_PUBLISHER."\" />\n";
		  if (!empty($META_COPYRIGHT)) print "<meta name=\"copyright\" content=\"".$META_COPYRIGHT."\" />\n";

		  print "<meta name=\"keywords\" content=\"".$META_KEYWORDS;
		  $surnames = get_common_surnames_index($GEDCOM);
		  foreach($surnames as $surname=>$count) if (!empty($surname)) print ", $surname";
		  print "\" />\n";

		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];

		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";
	 	  if (!empty($META_ROBOTS)) print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"PhpGedView - http://www.phpgedview.net\" />\n";
		 $META_AUTHOR = $old_META_AUTHOR;
		 $META_PUBLISHER = $old_META_PUBLISHER;
		 $META_COPYRIGHT = $old_META_COPYRIGHT;
		 $META_DESCRIPTION = $old_META_DESCRIPTION;
		 $META_PAGE_TOPIC = $old_META_PAGE_TOPIC;
	}
	else {

?>
<script language="JavaScript" type="text/javascript">
<!--
function hidePrint() {
	 var printlink = document.getElementById('printlink');
	 var printlinktwo = document.getElementById('printlinktwo');
	 if (printlink) {
		  printlink.style.display='none';
		  printlinktwo.style.display='none';
	 }
}

function showBack() {
	 var backlink = document.getElementById('backlink');
	 if (backlink) {
		  backlink.style.display='block';
	 }
}
//-->
</script>
<?php
}
?>
<script language="JavaScript" type="text/javascript">
	 <!--
	 <?php print "query = \"$query_string\";\n"; ?>
	 <?php print "textDirection = \"$TEXT_DIRECTION\";\n"; ?>
	 <?php print "PHP_SELF = \"$PHP_SELF\";\n"; ?>
	 /* keep the session id when opening new windows */
	 <?php print "sessionid = \"".session_id()."\";\n"; ?>
	 <?php print "sessionname = \"".session_name()."\";\n"; ?>

	 plusminus = new Array();
	 plusminus[0] = new Image();
	 plusminus[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]; ?>";
	 plusminus[1] = new Image();
	 plusminus[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]; ?>";

	 arrows = new Array();
	 arrows[0] = new Image();
	 arrows[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow2"]["other"]; ?>";
	 arrows[1] = new Image();
	 arrows[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow2"]["other"]; ?>";
	 arrows[2] = new Image();
	 arrows[2].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow2"]["other"]; ?>";
	 arrows[3] = new Image();
	 arrows[3].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow2"]["other"]; ?>";

function delete_record(pid, linenum) {
	 if (confirm('<?php print $pgv_lang["check_delete"]; ?>')) {
		  window.open('edit_interface.php?action=delete&pid='+pid+'&linenum='+linenum+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}
function deleteperson(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_person"]; ?>')) {
		  window.open('edit_interface.php?action=deleteperson&pid='+pid+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}

function deletesource(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_source"]; ?>')) {
		  window.open('edit_interface.php?action=deletesource&pid='+pid+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}

function deleterepository(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_repo"]; ?>')) {
		  window.open('edit_interface.php?action=deleterepo&pid='+pid+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}

function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($PHP_SELF)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}

var whichhelp = 'help_<?php print basename($PHP_SELF)."&amp;action=".$action; ?>';

//-->
</script>
<script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>
<?php
print $GLOBALS['wrapper']['header2'];
//	 print $head;
//	 print "</head>\n\t<body";
//	 if ($view=="preview") print " onbeforeprint=\"hidePrint();\" onafterprint=\"showBack();\"";
//	 if ($TEXT_DIRECTION=="rtl" || !empty($ONLOADFUNCTION)) {
//		 print " onload=\"$ONLOADFUNCTION";
//	 	if ($TEXT_DIRECTION=="rtl") print " maxscroll = document.documentElement.scrollLeft;";
//	 	print " loadHandler();";
//	 	print "\"";
//	}
// 	else print " onload=\"loadHandler();\"";
//	 print ">\n\t";
	 print "<!-- begin header section -->\n";
	 if ($view!="preview") {
		  include($headerfile);
		  include($toplinks);
	 }
	 else {
		  include($print_headerfile);
	 }
	 print "<!-- end header section -->\n";
	 print "<!-- begin content section -->\n";
}

/**
 * print simple HTML header
 *
 * this funciton will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
 * other auxillary files needed to run PGV.  It does not include any theme specific header files.
 * This function should be called by every page before anything is output on popup pages.
 * @param string $title	the title to put in the <TITLE></TITLE> header tags
 * @param string $head
 * @param boolean $use_alternate_styles
 */
function print_simple_header($title) {
	 global $pgv_lang;
	 global $HOME_SITE_URL;
	 global $HOME_SITE_TEXT;
	 global $view;
	 global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR, $PGV_DATABASE;
	 global $PHP_SELF, $QUERY_STRING, $action, $query, $changelanguage;
	 global $FAVICON, $stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile, $PHP_SELF;
	 global $TEXT_DIRECTION, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD,$PGV_IMAGES;
	 global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE;

	 header("Content-Type: text/html; charset=$CHARACTER_SET");
	 print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	 print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n\t<head>\n\t\t";
	 print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t\t";
	if( $FAVICON ) {
	   print "<link rel=\"shortcut icon\" href=\"$FAVICON\" type=\"image/x-icon\"></link>\n\t\t";
	}
	if (!isset($META_TITLE)) $META_TITLE = "";
	print "<title>".PrintReady(strip_tags($title))." - ".$META_TITLE." - PhpGedView</title>\n\t<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\"></link>\n\t";
	 if($TEXT_DIRECTION == "rtl") print "\n<style type=\"text/css\">body{ direction: rtl;	text-align: right; } </style>";
	 $old_META_AUTHOR = $META_AUTHOR;
		 $old_META_PUBLISHER = $META_PUBLISHER;
		 $old_META_COPYRIGHT = $META_COPYRIGHT;
		 $old_META_DESCRIPTION = $META_DESCRIPTION;
		 $old_META_PAGE_TOPIC = $META_PAGE_TOPIC;

		  $cuser = getUser($CONTACT_EMAIL);
		  if ($cuser) {
			  if (empty($META_AUTHOR)) $META_AUTHOR = $cuser["fullname"];
			  if (empty($META_PUBLISHER)) $META_PUBLISHER = $cuser["fullname"];
			  if (empty($META_COPYRIGHT)) $META_COPYRIGHT = $cuser["fullname"];
		  }
		  if (!empty($META_AUTHOR)) print "<meta name=\"author\" content=\"".$META_AUTHOR."\" />\n";
		  if (!empty($META_PUBLISHER)) print "<meta name=\"publisher\" content=\"".$META_PUBLISHER."\" />\n";
		  if (!empty($META_COPYRIGHT)) print "<meta name=\"copyright\" content=\"".$META_COPYRIGHT."\" />\n";

		  print "<meta name=\"keywords\" content=\"".$META_KEYWORDS;
		  $surnames = get_common_surnames_index($GEDCOM);
		  foreach($surnames as $surname=>$count) print ", $surname";
		  print "\" />\n";

		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];

		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";
	 	  if (!empty($META_ROBOTS)) print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"PhpGedView v$VERSION $PGV_DATABASE - http://www.phpgedview.net\" />\n";
		 $META_AUTHOR = $old_META_AUTHOR;
		 $META_PUBLISHER = $old_META_PUBLISHER;
		 $META_COPYRIGHT = $old_META_COPYRIGHT;
		 $META_DESCRIPTION = $old_META_DESCRIPTION;
		 $META_PAGE_TOPIC = $old_META_PAGE_TOPIC;
?>
	 <script language="JavaScript" type="text/javascript">
	 <!--
	 /* set these vars so that the session can be passed to new windows */
	 <?php print "sessionid = \"".session_id()."\";\n"; ?>
	 <?php print "sessionname = \"".session_name()."\";\n"; ?>

	 plusminus = new Array();
	 plusminus[0] = new Image();
	 plusminus[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]; ?>";
	 plusminus[1] = new Image();
	 plusminus[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]; ?>";

	 function expand_layer(sid) {
		  var sbox = document.getElementById(sid);
		  var sbox_img = document.getElementById(sid+"_img");
		  var sbox_style = sbox.style;
		  if (sbox_style.display=='none') {
			   sbox_style.display='block';
			   sbox_img.src = plusminus[1].src;
		  }
		  else {
			   sbox_style.display='none';
			   sbox_img.src = plusminus[0].src;
		  }
		  return false;
	 }
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('help_text.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'help_text.php?help='+which;
		return false;
	}

function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($PHP_SELF)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}
	 //-->
	 </script>
	 <script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>
	 <?php
	 print "</head>\n\t<body style=\"margin: 5px;\"";
	 print " onload=\"loadHandler();\">\n\t";
}

// -- print the html to close the page
function print_footer() {
	 global $without_close, $pgv_lang, $view, $buildindex, $pgv_changes, $PGV_DATABASE, $VERSION_RELEASE, $DBTYPE;
	 global $VERSION, $SHOW_STATS, $PHP_SELF, $QUERY_STRING, $footerfile, $print_footerfile, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $printlink;
	 global $PGV_IMAGE_DIR, $theme_name, $PGV_IMAGES, $TEXT_DIRECTION, $footer_count;
	 if (!isset($footer_count)) $footer_count = 1;
	 else $footer_count++;

	 print "<!-- begin footer -->\n";
	 $QUERY_STRING = preg_replace("/&/", "&", $QUERY_STRING);
	 if ($view!="preview") {
		  include($footerfile);
	 }
	 else {
		  include($print_footerfile);
		  print "\n\t<div style=\"text-align: center; width: 95%\"><br />";
		  if (!$printlink) {
			   print "\n\t<br /><a id=\"printlink\" href=\"#\" onclick=\"print(); return false;\">".$pgv_lang["print"]."</a><br />";
			   print "\n\t <a id=\"printlinktwo\"	  href=\"#\" onclick=\"history.back(); return false;\">".$pgv_lang["cancel_preview"]."</a><br />";
		  }
		  $printlink = true;
		  print "\n\t<a id=\"backlink\" style=\"display: none;\" href=\"#\" onclick=\"history.back(); return false;\">".$pgv_lang["cancel_preview"]."</a><br />";
		  print "</div>";
	 }
//	 print "\n\t</body>\n</html>";
print $GLOBALS['wrapper']['footer'];
}

// -- print the html to close the page
function print_simple_footer() {
	 global $pgv_lang;
	 global $start_time, $buildindex;
	 global $VERSION, $SHOW_STATS;
	 global $PHP_SELF, $QUERY_STRING;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;

	 if (empty($PHP_SELF)) {
		  $PHP_SELF = $_SERVER["PHP_SELF"];
		  $QUERY_STRING = $_SERVER["QUERY_STRING"];
	 }
	 print "\n\t<br /><br /><div align=\"center\" style=\"width: 99%;\">";
	 print_contact_links();
	 print "\n\t<a href=\"http://www.phpgedview.net\" target=\"_blank\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedview"]["other"]."\" border=\"0\" alt=\"PhpGedView Version $VERSION\" title=\"PhpGedView Version $VERSION\" /></a><br />";
	 if ($SHOW_STATS) print_execution_stats();
	 print "</div>";
	 print "\n\t</body>\n</html>";
}

/**
 * Prints Exection Statistics
 *
 * prints out the execution time and the databse queries
 */
function print_execution_stats() {
	global $PGV_DATABASE, $start_time, $pgv_lang, $TOTAL_QUERIES, $PRIVACY_CHECKS;

	$end_time = getmicrotime();
	$exectime = $end_time - $start_time;
	print "<br /><br />".$pgv_lang["exec_time"];
	printf(" %.3f ".$pgv_lang["sec"], $exectime);
	if ($PGV_DATABASE=='db') print "  ".$pgv_lang["total_queries"]." $TOTAL_QUERIES.";
	if (!$PRIVACY_CHECKS) $PRIVACY_CHECKS=0;
	print " ".$pgv_lang["total_privacy_checks"]." $PRIVACY_CHECKS.";
	if (function_exists("memory_get_usage")) {
		print " ".$pgv_lang["total_memory_usage"]." ".memory_get_usage().".";
	}
	print "<br />";
}


//-- print a form to change the language
function print_lang_form($option=0) {
	 global $ENABLE_MULTI_LANGUAGE, $pgv_lang, $pgv_language, $flagsfile, $LANGUAGE, $language_settings;
	 global $LANG_FORM_COUNT;
	 global $PHP_SELF, $QUERY_STRING;
	 if ($ENABLE_MULTI_LANGUAGE) {
		  if (empty($LANG_FORM_COUNT)) $LANG_FORM_COUNT=1;
		  else $LANG_FORM_COUNT++;
		  print "\n\t<div class=\"lang_form\">\n";
		  switch($option) {
			   case 1:
			   //-- flags option
			   foreach ($pgv_language as $key=>$value)
			   {
				 if (($key != $LANGUAGE) and ($language_settings[$key]["pgv_lang_use"]))
				 {
					print "<a href=\"$PHP_SELF?$QUERY_STRING&amp;changelanguage=yes&amp;NEWLANGUAGE=$key\">";
					print "<img src=\"" . $flagsfile[$key] . "\" class=\"flag\" border=\"0\" width=\"50\" alt=\"" . $pgv_lang[$key]. "\" title=\"" . $pgv_lang[$key]. "\" style=\"filter:alpha(opacity=20);-moz-opacity:0.2\" onmouseover=\"makevisible(this,0)\" onmouseout=\"makevisible(this,1)\" /></a>\n";
				 }
				 else
				 {
					if ($language_settings[$key]["pgv_lang_use"]) print "<img src=\"" . $flagsfile[$key] . "\" class=\"flag\" border=\"0\" alt=\"" . $pgv_lang[$key]. "\" title=\"" . $pgv_lang[$key]. "\" />\n";
				 }
			   }
			   break;
			   default:
					print "<form name=\"langform$LANG_FORM_COUNT\" action=\"$PHP_SELF";
					print "\" method=\"get\">";
					$vars = preg_split("/&amp;/", $QUERY_STRING);
					foreach($vars as $indexval => $var) {
						$parts = preg_split("/=/", $var);
						if (count($parts)>1) {
							if (($parts[0]!="changelanguage")&&($parts[0]!="NEWLANGUAGE"))
								print "\n\t\t<input type=\"hidden\" name=\"$parts[0]\" value=\"".urldecode($parts[1])."\" />";
						}
					}
					print "\n\t\t<input type=\"hidden\" name=\"changelanguage\" value=\"yes\" />\n\t\t<select name=\"NEWLANGUAGE\" class=\"header_select\" onchange=\"submit();\">";
					print "\n\t\t\t<option value=\"\">".$pgv_lang["change_lang"]."</option>";
					foreach ($pgv_language as $key=>$value) {
						 if ($language_settings[$key]["pgv_lang_use"]) {
							  print "\n\t\t\t<option value=\"$key\" ";
							  if ($LANGUAGE == $key) print "class=\"selected-option\"";
							  print ">".$pgv_lang[$key]."</option>";
						 }
					}
					print "</select>\n</form>\n";
			   break;
		  }
		  print "</div>";
	 }
}

/**
 * print user links
 *
 * this function will print login/logout links and other links based on user privileges
 */
function print_user_links() {
	 global $pgv_lang, $PHP_SELF, $QUERY_STRING, $GEDCOM, $PRIV_USER, $PRIV_PUBLIC, $USE_REGISTRATION_MODULE, $pid;
	 global $LOGIN_URL;

	 $username = getUserName();
	 $user = getUser($username);
	 print "<div class=\"user_links\">";
	 if ($user && !empty($username)) {
		  print '<a href="edituser.php" class="link">'.$pgv_lang["logged_in_as"].' ('.$username.')</a><br />';
		  if ($user["canadmin"] || (userGedcomAdmin($username, $GEDCOM))) print "<a href=\"admin.php\" class=\"link\">".$pgv_lang["admin"]."</a> | ";
		  print "<a href=\"index.php?logout=1\" class=\"link\">".$pgv_lang["logout"]."</a>";
	 }
	 else {
		  $QUERY_STRING = preg_replace("/logout=1/", "", $QUERY_STRING);
		  print "<a href=\"$LOGIN_URL?url=".urlencode(basename($PHP_SELF)."?".$QUERY_STRING."&amp;ged=$GEDCOM")."\" class=\"link\">".$pgv_lang["login"]."</a>";
	 }
	 print "</div>";
}

/**
 * print links for genealogy and technical contacts
 *
 * this function will print appropriate links based on the preferred contact methods for the genealogy
 * contact user and the technical support contact user
 */
function print_contact_links($style=0) {
	global $WEBMASTER_EMAIL, $SUPPORT_METHOD, $CONTACT_EMAIL, $CONTACT_METHOD, $pgv_lang;

	if ($SUPPORT_METHOD=="none" && $CONTACT_METHOD=="none") return;
	if ($SUPPORT_METHOD=="none") $WEBMASTER_EMAIL = $CONTACT_EMAIL;
	if ($CONTACT_METHOD=="none") $CONTACT_EMAIL = $WEBMASTER_EMAIL;
	switch($style) {
		case 0:
			print "<div class=\"contact_links\">\n";
			//--only display one message if the contact users are the same
			if ($CONTACT_EMAIL==$WEBMASTER_EMAIL) {
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) print $pgv_lang["for_all_contact"]." <a href=\"#\" onclick=\"message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;\">".$user["fullname"]."</a><br />\n";
				else {
					print $pgv_lang["for_support"]." <a href=\"mailto:";
					if ($user) print $user["email"]."\">".$user["fullname"]."</a><br />\n";
					else print $WEBMASTER_EMAIL."\">".$WEBMASTER_EMAIL."</a><br />\n";
				}
			}
			//-- display two messages if the contact users are different
			else {
				  $user = getUser($CONTACT_EMAIL);
				  if (($user)&&($CONTACT_METHOD!="mailto")) print $pgv_lang["for_contact"]." <a href=\"#\" onclick=\"message('$CONTACT_EMAIL', '$CONTACT_METHOD'); return false;\">".$user["fullname"]."</a><br /><br />\n";
				  else {
					   print $pgv_lang["for_contact"]." <a href=\"mailto:";
					   if ($user) print $user["email"]."\">".$user["fullname"]."</a><br />\n";
					   else print $CONTACT_EMAIL."\">".$CONTACT_EMAIL."</a><br />\n";
				  }

				  $user = getUser($WEBMASTER_EMAIL);
				  if (($user)&&($SUPPORT_METHOD!="mailto")) print $pgv_lang["for_support"]." <a href=\"#\" onclick=\"message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;\">".$user["fullname"]."</a><br />\n";
				  else {
					   print $pgv_lang["for_support"]." <a href=\"mailto:";
					   if ($user) print $user["email"]."\">".$user["fullname"]."</a><br />\n";
					   else print $WEBMASTER_EMAIL."\">".$WEBMASTER_EMAIL."</a><br />\n";
				  }
			}
			print "</div>\n";
			break;
		case 1:
			$menuitems = array();
			if ($CONTACT_EMAIL==$WEBMASTER_EMAIL) {
				$submenu = array();
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["support_contact"]." ".$user["fullname"];
					$submenu["onclick"] = "message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["support_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["fullname"];
					}
					else {
						$submenu["link"] .= $WEBMASTER_EMAIL;
						$submenu["label"] .= $WEBMASTER_EMAIL;
					}
				}
	            $submenu["label"] = $pgv_lang["support_contact"];
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;
			}
			else {
				$submenu = array();
				$user = getUser($CONTACT_EMAIL);
				if (($user)&&($CONTACT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["genealogy_contact"]." ".$user["fullname"];
					$submenu["onclick"] = "message('$CONTACT_EMAIL', '$CONTACT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["genealogy_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["fullname"];
					}
					else {
						$submenu["link"] .= $CONTACT_EMAIL;
						$submenu["label"] .= $CONTACT_EMAIL;
					}
				}
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;

	            $submenu = array();
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["support_contact"]." ".$user["fullname"];
					$submenu["onclick"] = "message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["support_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["fullname"];
					}
					else {
						$submenu["link"] .= $WEBMASTER_EMAIL;
						$submenu["label"] .= $WEBMASTER_EMAIL;
					}
				}
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;
	        }
            return $menuitems;
			break;
	}
}

//-- print user favorites
function print_favorite_selector($option=0) {
	global $pgv_lang, $GEDCOM, $PHP_SELF, $SHOW_ID_NUMBERS, $PGV_IMAGE_DIR, $PGV_IMAGES, $pid, $PGV_DATABASE, $INDEX_DIRECTORY, $indilist;
	global $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION;

	$username = getUserName();
	if (!empty($username)) $userfavs = getUserFavorites($username);
	else {
		if ($REQUIRE_AUTHENTICATION) return false;
		$userfavs = array();
	}
	$gedcomfavs = getUserFavorites($GEDCOM);
	if ((empty($username))&&(count($gedcomfavs)==0)) return;
	print "<div class=\"favorites_form\">\n";
	switch($option) {
		case 1:
			$menu = array();
			$menu["label"] = $pgv_lang["my_favorites"];
			$menu["labelpos"] = "right";
			$menu["link"] = "#";
			$menu["class"] = "favmenuitem";
			$menu["hoverclass"] = "favmenuitem_hover";
			$menu["flyout"] = "down";
			$menu["submenuclass"] = "favsubmenu";
			$menu["items"] = array();
			$mygedcom = $GEDCOM;
			$current_gedcom = $GEDCOM;
			$mypid = $pid;
			foreach($userfavs as $key=>$favorite) {
				if ($favorite["type"]=="INDI") {
					$pid = $favorite["gid"];
					$current_gedcom = $GEDCOM;
					$GEDCOM = $favorite["file"];
					$submenu = array();
					if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
						$indilist = load_gedcom_indilist();
						$current_gedcom = $GEDCOM;
					}
					if (displayDetailsById($pid, $favorite["type"])) {
						$indirec = find_person_record($pid);
						$submenu["label"] = PrintReady(get_person_name($favorite["gid"]));
						if ($SHOW_ID_NUMBERS)
	 						if ($TEXT_DIRECTION=="ltr")
								 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
							else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
						$submenu["labelpos"] = "right";
						$submenu["link"] = "individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM";
						$submenu["class"] = "favsubmenuitem";
						$submenu["hoverclass"] = "favsubmenuitem_hover";
						$menu["items"][] = $submenu;
					}
				}
			}
			$pid = $mypid;
			$GEDCOM = $mygedcom;
			if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) $indilist = load_gedcom_indilist();
			if ((!empty($username))&&(strpos($_SERVER["PHP_SELF"], "individual.php")!==false)) {
				$menu["items"][]="separator";
				$submenu = array();
				$submenu["label"] = $pgv_lang["add_to_my_favorites"];
				$submenu["labelpos"] = "right";
				$submenu["link"] = "individual.php?action=addfav&amp;gid=$pid&amp;pid=$pid";
				$submenu["class"] = "favsubmenuitem";
				$submenu["hoverclass"] = "favsubmenuitem_hover";
				$menu["items"][] = $submenu;
		   }
		   if (count($gedcomfavs)>0) {
				$menu["items"][]="separator";
				$submenu = array();
				$submenu["label"] = $pgv_lang["gedcom_favorites"];
				$submenu["labelpos"] = "right";
				$submenu["link"] = "#";
				$submenu["class"] = "favsubmenuitem";
				$submenu["hoverclass"] = "favsubmenuitem_hover";
				$menu["items"][] = $submenu;
				$current_gedcom = $GEDCOM;
				foreach($gedcomfavs as $key=>$favorite) {
					if ($favorite["type"]=="INDI") {
						$GEDCOM = $favorite["file"];
						$pid = $favorite["gid"];
						$submenu = array();
						if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
							$indilist = load_gedcom_indilist();
							$current_gedcom = $GEDCOM;
						}
						if (displayDetailsById($pid, $favorite["type"])) {
							$submenu["label"] = PrintReady(get_person_name($pid));
							if ($SHOW_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
									$submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
							   else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";//							if ($SHOW_ID_NUMBERS) $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
							$submenu["labelpos"] = "right";
							$submenu["link"] = "individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["class"] = "favsubmenuitem";
							$submenu["hoverclass"] = "favsubmenuitem_hover";
							$menu["items"][] = $submenu;
						}
					}
				}
			}
				$pid = $mypid;
				$GEDCOM = $mygedcom;
				if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) $indilist = load_gedcom_indilist();
				print_menu($menu);
				break;
			default:
			   print "<form name=\"favoriteform\" action=\"$PHP_SELF";
			   print "\" method=\"post\" onsubmit=\"return false;\">";
			   print "\n\t\t<select name=\"fav_id\" class=\"header_select\" onchange=\"if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value!='') window.location='individual.php?'+document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value; if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value=='add') window.location='individual.php?action=addfav&amp;gid=$pid&amp;pid=$pid';\">";
			   if (!empty($username)) {
					print "\n\t\t\t<option value=\"\">- ".$pgv_lang["my_favorites"]." -</option>";
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
					foreach($userfavs as $key=>$favorite) {
						 if ($favorite["type"]=="INDI") {
							$current_gedcom = $GEDCOM;
							$GEDCOM = $favorite["file"];
							$pid = $favorite["gid"];
							if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
								$indilist = load_gedcom_indilist();
								$current_gedcom = $GEDCOM;
							}
							if (displayDetailsById($pid, $favorite["type"])) {
								$indirec = find_person_record($pid);
								print "\n\t\t\t<option value=\"pid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".strip_tags(PrintReady(get_person_name($pid)));
	 							if ($SHOW_ID_NUMBERS)
	 							   if ($TEXT_DIRECTION=="ltr")
										print " &lrm;(".$favorite["gid"].")&lrm;";
								   else print " &rlm;(".$favorite["gid"].")&rlm;";
								print "</option>";
							}
						}
					}
					$GEDCOM = $mygedcom;
					$pid = $mypid;
					if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) $indilist = load_gedcom_indilist();
			   }
			   if (count($gedcomfavs)>0) {
					print "<option value=\"\">- ".$pgv_lang["gedcom_favorites"]." -</option>\n";
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
					foreach($gedcomfavs as $key=>$favorite) {
						 if ($favorite["type"]=="INDI") {
	 						$current_gedcom = $GEDCOM;
							$GEDCOM = $favorite["file"];
							$pid = $favorite["gid"];
							if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) {
								$indilist = load_gedcom_indilist();
								$current_gedcom = $GEDCOM;
							}
							if (displayDetailsById($pid, $favorite["type"])) {
								$indirec = find_person_record($pid);
// LtR names with
//   * parenthesis on RtL page printed in wrong word sequence
//   * with name suffix . printed with the . to the left of the name
// RtL names on LtR pages same problems
// OK on other pages and in the portal Favorites
								print "\n\t\t\t<option value=\"pid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".strip_tags(PrintReady(get_person_name($pid)));
	     						if ($SHOW_ID_NUMBERS)
	 							   if ($TEXT_DIRECTION=="ltr")
	     						    print " &lrm;(".$favorite["gid"].")&lrm;";
								   else print " &rlm;(".$favorite["gid"].")&rlm;";
								print "</option>";
							}
						}
					}
					$GEDCOM = $mygedcom;
					$pid = $mypid;
					if (($PGV_DATABASE == "index") and ($GEDCOM != $current_gedcom)) $indilist = load_gedcom_indilist();
			   }
			   if ((!empty($username))&&(strpos($_SERVER["PHP_SELF"], "individual.php")!==false)) print "<option value=\"add\">- ".$pgv_lang["add_to_my_favorites"]." -</option>\n";
			   print "</select>\n\t</form>\n";
			   break;
	 }
	 print "</div>\n";
}

/**
 * print a gedcom title linked to the gedcom portal
 *
 * This function will print the HTML to link the current gedcom title back to the
 * gedcom portal welcome page
 * @author John Finlay
 */
function print_gedcom_title_link($InHeader=FALSE) {
	 global $GEDCOMS, $GEDCOM;
	 if ((count($GEDCOMS)==0)||(empty($GEDCOM))) return;
	 if (isset($GEDCOMS[$GEDCOM])) print "<a href=\"index.php?command=gedcom\" class=\"gedcomtitle\">".PrintReady($GEDCOMS[$GEDCOM]["title"], $InHeader)."</a>";
// John wanted to define once for the session - how?? - this works only for standard theme?? MA
}

/**
 * print a simple form of the fact
 *
 * function to print the details of a fact in a simple format
 * @param string $indirec the gedcom record to get the fact from
 * @param string $fact the fact to print
 * @param string $pid the id of the individual to print, required to check privacy
 */
function print_simple_fact($indirec, $fact, $pid) {
	global $pgv_lang, $SHOW_PEDIGREE_PLACES, $factarray, $ABBREVIATE_CHART_LABELS;
	$emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","OBJE","CHAN","_SEPR","RESI", "DATA", "MAP");

	$factrec = get_sub_record(1, "1 $fact", $indirec);
	if ((empty($factrec))||(FactViewRestricted($pid, $factrec))) return;

	$label = "";
	if (isset($pgv_lang[$fact])) $label = $pgv_lang[$fact];
	else if (isset($factarray[$fact])) $label = $factarray[$fact];
	if ($ABBREVIATE_CHART_LABELS) $label = get_first_letter($label);
	print "<span class=\"details_label\">".$label."</span> ";
	if (showFactDetails($fact, $pid)) {
		if (!in_array($fact, $emptyfacts)) {
			$ct = preg_match("/1 $fact(.*)/", $factrec, $match);
			if ($ct>0) print PrintReady(trim($match[1]));
		}
		// 1 DEAT Y|N with no DATE => print YES|NO
		if (get_sub_record(2, "2 DATE", $factrec)=="") {
			$event=strtoupper(trim(substr($factrec,6,2)));
			if ($event=="Y") print $pgv_lang["yes"];
			if ($event=="N") print $pgv_lang["no"];
		}
		print_fact_date($factrec, false, false, $fact, $pid, $indirec);
		print_fact_place($factrec, false, true, true);
	}
	print "<br />\n";
}

/**
 * print a fact record
 *
 * prints a fact record designed for the personal facts and details page
 * @param string $factrec	The gedcom subrecord
 * @param string $pid		The Gedcom Xref ID of the person the fact belongs to (required to check fact privacy)
 * @param int $linenum		The line number where this fact started in the original gedcom record (required for editing)
 * @param string $indirec	optional INDI record for age calculation at family event
 */
function print_fact($factrec, $pid, $linenum, $indirec=false) {
	 global $factarray;
	 global $sexarray;
	 global $nonfacts, $birthyear, $birthmonth, $birthdate;
	 global $hebrew_birthyear, $hebrew_birthmonth, $hebrew_birthdate;
	 global $BOXFILLCOLOR;
	 global $pgv_lang, $GEDCOM;
	 global $WORD_WRAPPED_NOTES;
	 global $TEXT_DIRECTION;
	 global $HIDE_GEDCOM_ERRORS, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS;
	 global $CONTACT_EMAIL, $view, $FACT_COUNT, $monthtonum;
	 global $dHebrew;
	 $FACT_COUNT++;
	 $estimates = array("abt","aft","bef","est","cir");

	 $ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
	 if ($ft>0) {
		  $fact = trim($match[1]);
		  $event = trim($match[2]);
	 }
	 else {
		  $fact="";
		  $event="";
	 }
	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="blue";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="red";

	 // -- avoid known non facts
	 if (in_array($fact, $nonfacts)) return;

	 //-- do not print empty facts
	 $lines = preg_split("/\n/", trim($factrec));
	 if ((count($lines)<2)&&($event=="")) return;

	 // See if RESN tag prevents display or edit/delete
	 $resn_tag = preg_match("/2 RESN (.*)/", $factrec, $match);
	 if ($resn_tag == "1") $resn_value = strtolower(trim($match[1]));

	 if (array_key_exists($fact, $factarray)) {
		  // -- handle generic facts
		  if ($fact!="EVEN" && $fact!="FACT") {
			   $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   print "\n\t\t<tr>";
			   print "\n\t\t\t<td class=\"facts_label$styleadd\">";
			   print $factarray[$fact];
			   if ((userCanEdit(getUserName()))&&($styleadd!="red")&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
					$menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					$menu["link"] = "#";
					$menu["onclick"] = "return edit_record('$pid', $linenum);";
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return edit_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["copy"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return copy_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["delete"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return delete_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					print " <div style=\"width:25px;\">";
					print_menu($menu);
					print "</div>";
			   }
			   print "</td>";
		  }
		  else {
			   if (!showFact("EVEN", $pid)) return false;
			   // -- find generic type for each fact
			   $ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
			   if ($ct>0) $factref = trim($match[1]);
			   else $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   print "\n\t\t<tr>";
			   if (isset($factarray["$factref"])) print "<td class=\"facts_label\">" .$factarray["$factref"];
			   else print "<td class=\"facts_label$styleadd\">" . $factref;
			   if ((userCanEdit(getUserName()))&&($styleadd!="red")&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
				   $menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					$menu["link"] = "#";
					$menu["onclick"] = "return edit_record('$pid', $linenum);";
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return edit_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["delete"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return delete_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["copy"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return copy_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					print " <div style=\"width:25px;\">";
					print_menu($menu);
					print "</div>";
				}
			   print "</td>";
		  }
		  print "<td class=\"facts_value$styleadd\">";
		  $user = getUser(getUserName());
		  if ((showFactDetails($factref, $pid)) && (FactViewRestricted($pid, $factrec))) {
			   print $factarray["RESN"].": ";
			   if (isset($pgv_lang[$resn_value])) print $pgv_lang[$resn_value];
			   else if (isset($factarray[$resn_value])) print $factarray[$resn_value];
			   else print $resn_value;
			   print "<br />\n";
		  }
		  if ((showFactDetails($factref, $pid)) && (!FactViewRestricted($pid, $factrec))) {
				// -- first print TYPE for some facts
				if ($fact!="EVEN" && $fact!="FACT") {
					$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
					if ($ct>0) {
						$type = trim($match[1]);
						if (isset ($factarray["MARR_".str2upper($type)])) print $factarray["MARR_".str2upper($type)];
						else if (isset($factarray[$type])) print $factarray[$type];
						else if (isset($pgv_lang[$type])) print $pgv_lang[$type];
						else print $type;
						print "<br />";
					}
				}
			   // -- find date for each fact
			   print_fact_date($factrec, true, true, $fact, $pid, $indirec);

			   //-- print spouse name for marriage events
			   $ct = preg_match("/PGV_SPOUSE: (.*)/", $factrec, $match);
			   if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						 print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						 if (displayDetailsById($spouse)||showLivingNameById($spouse)) print PrintReady(get_person_name($spouse));
						 else print $pgv_lang["private"];
						 print "</a>";
					}
					if (($view!="preview") && ($spouse!=="")) print " - ";
					if ($view!="preview") {
	 				     print "<a href=\"family.php?famid=$pid\">";
						 if ($TEXT_DIRECTION == "ltr") print " &lrm;";
 						 else print " &rlm;";
						 print "[".$pgv_lang["view_family"];
  						 if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($pid)&lrm;";
  						 if ($TEXT_DIRECTION == "ltr") print "&lrm;]</a>\n";
 						 else print "&rlm;]</a>\n";
                    }
			   }

			   //-- print other characterizing fact information
			   if ($event!="" and $fact!="ASSO") {
					$ct = preg_match("/@(.*)@/", $event, $match);
					if ($ct>0) {
						 $gedrec = find_gedcom_record($match[1]);
						 if (strstr($gedrec, "INDI")!==false) print "<a href=\"individual.php?pid=$match[1]&amp;ged=$GEDCOM\">".get_person_name($match[1])."</a><br />";
						 else if ($fact=="REPO") print_repository_record($match[1]);
						 else print_submitter_info($match[1]);
					}
					else if ($fact=="ALIA") {
						 //-- strip // from ALIA tag for FTM generated gedcoms
						 print preg_replace("'/'", "", $event)."<br />";
					}
					else if ($event=="Y") {
						if (get_sub_record(2, "2 DATE", $factrec)=="") print $pgv_lang["yes"]."<br />";
					}
					else if ($event=="N") {
						if (get_sub_record(2, "2 DATE", $factrec)=="") print $pgv_lang["no"]."<br />";
					}
					else if (strstr("URL WWW ", $fact." ")) {
						 print "<a href=\"".$event."\" target=\"new\">".PrintReady($event)."</a>";
					}
					else if (strstr("_EMAIL", $fact)) {
						 print "<a href=\"mailto:".$event."\">".$event."</a>";
					}
 					else if (strstr("FAX", $fact)) print "&lrm;".$event." &lrm;";
					else if (!strstr("PHON ADDR ", $fact." ")) print PrintReady($event." ");

					$temp = trim(get_cont(2, $factrec), "\r\n");
					if (strstr("PHON ADDR ", $fact." ")===false && $temp!="") {
						if ($WORD_WRAPPED_NOTES) print " ";
						print PrintReady($temp);
					}
			   }
			   //-- find description for some facts
			   $ct = preg_match("/2 DESC (.*)/", $factrec, $match);
			   if ($ct>0) print PrintReady($match[1]);

				// -- print PLACe, TEMPle and STATus
				print_fact_place($factrec, true, true, true);
				if (preg_match("/ (PLAC)|(STAT)|(TEMP)|(SOUR) /", $factrec)>0 || (!empty($event)&&$fact!="ADDR")) print "<br />\n";

				// -- print BURIal -> CEMEtery
				$ct = preg_match("/2 CEME (.*)/", $factrec, $match);
				if ($ct>0) print $factarray["CEME"].": ".$match[1]."<br />\n";
			   //-- print address structure
			   if ($fact!="ADDR" && $fact!="PHON") {
				   print_address_structure($factrec, 2);
			   }
			   else {
				   print_address_structure($factrec, 1);
			   }
				// -- Enhanced ASSOciates > RELAtionship
				print_asso_rela_record($pid, $factrec);

			   // -- find _PGVU field
			   $ct = preg_match("/2 _PGVU (.*)/", $factrec, $match);
			   if ($ct>0) print $factarray["_PGVU"].": ".$match[1];

			   // -- Find RESN tag
			   if (isset($resn_value)) {
				   print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }

				if ($fact!="ADDR") {
					//-- catch all other facts that could be here
					$special_facts = array("ADDR","ALIA","ASSO","CEME","CONC","CONT","DATE","DESC","EMAIL",
					"FAMC","FAMS","FAX","NOTE","OBJE","PHON","PLAC","RESN","SOUR","STAT","TEMP",
					"TIME","TYPE","WWW","_EMAIL","_PGVU", "URL");
					$ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						if (!in_array($match[$i][1], $special_facts)) {
							print "<span class=\"label\">";
							if (isset($factarray[$match[$i][1]])) print $factarray[$match[$i][1]].": ";
							else print $match[$i][1].": ";
							print "</span>";
							$value = trim($match[$i][2]);
							if (isset($pgv_lang[strtolower($value)])) print $pgv_lang[strtolower($value)];
							else print PrintReady($value);
							print "<br />\n";
						}
					}
				}

			   // -- find source for each fact
			   print_fact_sources($factrec, 2);

			   // -- find notes for each fact
			   print_fact_notes($factrec, 2);

			   //-- find multimedia objects
			   print_media_links($factrec, 2);
		  }
		  print "</td>";
		  print "\n\t\t</tr>";
	 }
	 else {
		  // -- catch all unknown codes here
		  $body = $pgv_lang["unrecognized_code"]." ".$fact;
		  if (!$HIDE_GEDCOM_ERRORS) print "\n\t\t<tr><td class=\"facts_label$styleadd\"><span class=\"error\">".$pgv_lang["unrecognized_code"].": $fact</span></td><td class=\"facts_value$styleadd\">$event<br />".$pgv_lang["unrecognized_code_msg"]." <a href=\"#\" onclick=\"message('$CONTACT_EMAIL','', '', '$body'); return false;\">".$CONTACT_EMAIL."</a>.</td></tr>";
	 }
}
//------------------- end print fact function

/**
 * print a source linked to a fact (2 SOUR)
 *
 * this function is called by the print_fact function and other functions to
 * print any source information attached to the fact
 * @param string $factrec	The fact record to look for sources in
 * @param int $level		The level to look for sources at
 */
function print_fact_sources($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES, $FACT_COUNT, $PGV_IMAGE_DIR, $FACT_COUNT, $PGV_IMAGES, $SHOW_SOURCES;
	 $nlevel = $level+1;

	 if ($SHOW_SOURCES<getUserAccessLevel(getUserName())) return;

	 // -- Systems not using source records [ 1046971 ]
	 $ct = preg_match_all("/$level SOUR (.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		if (strpos($match[$j][1], "@")===false) {
			$srec = get_sub_record($level, " SOUR ", $factrec, $j+1);
			$srec = substr($srec, 5); // remove SOUR
			$srec = str_replace("\n".($level+1)." CONT ", " ", $srec); // remove n+1 CONT
			$srec = str_replace("\n".($level+1)." CONC ", "", $srec); // remove n+1 CONC
			print "<span class=\"label\">".$pgv_lang["source"].":</span> <span class=\"field\">".PrintReady($srec)."</span><br />";
		}
	 }
	 // -- find source for each fact
	 $ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	 $spos2 = 0;
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level SOUR @".$match[$j][1]."@", $spos2);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $srec = substr($factrec, $spos1, $spos2-$spos1);
//		  $lt = preg_match_all("/$nlevel _?[A-Z]+/", $srec, $matches);
		  $lt = preg_match_all("/$nlevel \w+/", $srec, $matches);
		  if ($j > 0) print "<br />";
		  print "\n\t\t<span class=\"label\">";
		  $sid = $match[$j][1];
		  if ($lt>0) print "<a href=\"#\" onclick=\"expand_layer('$sid$j-$FACT_COUNT'); return false;\"><img id=\"{$sid}{$j}-{$FACT_COUNT}_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> ";
		  print $pgv_lang["source"];
		  print ":</span> <span class=\"field\"><a href=\"source.php?sid=".$sid."\">";
		  print PrintReady(get_source_descriptor($sid));

		  //-- Print additional source title
    	  $add_descriptor = get_add_source_descriptor($sid);
    	  if ($add_descriptor) print " - ".PrintReady($add_descriptor);

		  print "</a></span>";
		  print "<div id=\"$sid$j-$FACT_COUNT\" class=\"source_citations\">";
		   $cs = preg_match("/$nlevel PAGE (.*)/", $srec, $cmatch);
		   if ($cs>0) {
				print "\n\t\t\t<span class=\"label\">".$factarray["PAGE"].": </span><span class=\"field\">".PrintReady($cmatch[1]);
				$pagerec = get_sub_record($nlevel, $cmatch[0], $srec);
				$text = get_cont($nlevel+1, $pagerec);
				$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
				print PrintReady($text);
				print "</span>";
		   }
		   $cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
		   if ($cs>0) {
				print "<br /><span class=\"label\">".$factarray["EVEN"]." </span><span class=\"field\">".$cmatch[1]."</span>";
				$cs = preg_match("/".($nlevel+1)." ROLE (.*)/", $srec, $cmatch);
				if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$factarray["ROLE"]." </span><span class=\"field\">$cmatch[1]</span>";
		   }
		   $cs = preg_match("/$nlevel DATA/", $srec, $cmatch);
		   if ($cs>0) {
				$cs = preg_match("/".($nlevel+1)." DATE (.*)/", $srec, $cmatch);
				if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["date"].": </span><span class=\"field\">".get_changed_date($cmatch[1])."</span>";
				$tt = preg_match_all("/".($nlevel+1)." TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
				for($k=0; $k<$tt; $k++) {
					 print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".PrintReady($tmatch[$k][1]);
					 print PrintReady(get_cont($nlevel+2, $srec));
					 print "</span>";
				}
		   }
		   $cs = preg_match("/".$nlevel." DATE (.*)/", $srec, $cmatch);
		   if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["date"].": </span><span class=\"field\">".get_changed_date($cmatch[1])."</span>";
		   $cs = preg_match("/$nlevel QUAY (.*)/", $srec, $cmatch);
		   if ($cs>0) print "<br /><span class=\"label\">".$factarray["QUAY"]." </span><span class=\"field\">".$cmatch[1]."</span>";

		   $cs = preg_match_all("/$nlevel TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
		   for($k=0; $k<$cs; $k++) {
				print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1];
				$text = get_cont($nlevel+1, $srec);
				$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
				print PrintReady($text);
				print "</span>";
		   }
		   print "<div class=\"indent\">";
		   print_media_links($srec, $nlevel);
		   print_fact_notes($srec, $nlevel);
		   print "</div>";
//		  print "</div><br />";
		  print "</div>";
	 }
}

function print_main_sources($factrec, $level, $pid, $linenum) {
	 global $pgv_lang;
	 global $factarray, $view;
	 global $WORD_WRAPPED_NOTES, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_SOURCES;

	 if ($SHOW_SOURCES<getUserAccessLevel(getUserName())) return;

	 $nlevel = $level+1;

	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="blue";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="red";
	 // -- find source for each fact
	 $ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	 $spos2 = 0;
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level SOUR @".$match[$j][1]."@", $spos2);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $srec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!showFact("SOUR", $pid)) return false;
		  print "\n\t\t\t<tr><td class=\"facts_label$styleadd\">";
		  print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["large"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />";
		  print $pgv_lang["source"];
		  if (userCanEdit(getUserName())&&($styleadd!="red")&&($view!="preview")) {
			  $menu = array();
				$menu["label"] = $pgv_lang["edit"];
				$menu["labelpos"] = "right";
				$menu["icon"] = "";
				$menu["link"] = "#";
				$menu["onclick"] = "return edit_record('$pid', $linenum);";
				$menu["class"] = "";
				$menu["hoverclass"] = "";
				$menu["flyout"] = "down";
				$menu["submenuclass"] = "submenu";
				$menu["items"] = array();
				$submenu = array();
				$submenu["label"] = $pgv_lang["edit"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return edit_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["delete"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return delete_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["copy"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return copy_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				print " <div style=\"width:25px;\">";
				print_menu($menu);
				print "</div>";
			}
		  print "</td>";
		  print "\n\t\t\t<td class=\"facts_value$styleadd\">";
		  if (showFactDetails("SOUR", $pid)) {
			   print "<a href=\"source.php?sid=".$match[$j][1]."\">";
    		   print PrintReady(get_source_descriptor($match[$j][1]));

    		   //-- Print additional source title
    		   $add_descriptor = get_add_source_descriptor($match[$j][1]);
    		   if ($add_descriptor) print " - ".PrintReady($add_descriptor);
			   print "</a>";
			   $source = find_source_record($match[$j][1]);
			   if ($source) {

				    $cs = preg_match("/$nlevel PAGE (.*)/", $srec, $cmatch);
					if ($cs>0) {
						 print "\n\t\t\t<br />".$factarray["PAGE"].": $cmatch[1]";
						 $text = get_cont($nlevel+1, $srec);
						 $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
//						 print $text;
						 print PrintReady($text);
					}
					$cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
					if ($cs>0) {
						 print "<br /><span class=\"label\">".$factarray["EVEN"]." </span><span class=\"field\">".$cmatch[1]."</span>";
						 $cs = preg_match("/".($nlevel+1)." ROLE (.*)/", $srec, $cmatch);
						 if ($cs>0) print "\n\t\t\t<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$factarray["ROLE"]." </span><span class=\"field\">$cmatch[1]</span>";
					}
					$cs = preg_match("/$nlevel DATA/", $srec, $cmatch);
					if ($cs>0) {
						 print "<br /><span class=\"label\">".$factarray["DATA"]." </span>";
						 $cs = preg_match("/".($nlevel+1)." DATE (.*)/", $srec, $cmatch);
						 if ($cs>0) print "\n\t\t\t<br />&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["date"].":  </span><span class=\"field\">$cmatch[1]</span>";
						 $tt = preg_match_all("/".($nlevel+1)." TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
						 for($k=0; $k<$tt; $k++) {
							  print "<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1]."</span>";

							  print get_cont($nlevel+2, $srec);
						 }
					}

					$cs = preg_match("/$nlevel QUAY (.*)/", $srec, $cmatch);
					if ($cs>0) print "<br /><span class=\"label\">".$factarray["QUAY"]." </span><span class=\"field\">".$cmatch[1]."</span>";

					$cs = preg_match_all("/$nlevel TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
					for($k=0; $k<$cs; $k++) {
						 print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1];
						 $trec = get_sub_record($nlevel, $tmatch[$k][0], $srec);
						 $text = get_cont($nlevel+1, $trec);
						 $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
						 print $text;
						 print "</span>";
					}
					print_media_links($srec, $nlevel);
					print_fact_notes($srec, $nlevel);
			   }
		  }
		  print "</td></tr>";
	 }
}

//-- Print all of the notes in this fact record
function print_fact_notes($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES;

	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, $match[$j][0]);
		  $spos2 = strpos($factrec, "\n$level", $spos1+1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $nrec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!isset($match[$j][1])) $match[$j][1]="";
		  $nt = preg_match("/@(.*)@/", $match[$j][1], $nmatch);
		  if ($nt==0) {
			   //-- print embedded note records
			   $text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
			   $text .= get_cont($nlevel, $nrec);
			   $text = preg_replace("'(http://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">URL</a>", $text);
			   $text = trim($text);
			   if (!empty($text)) {
				   print "\n\t\t<br /><span class=\"label\">".$pgv_lang["note"].": </span><span class=\"field\">";
			   	   print PrintReady($text);
		   		}
		  }
		  else {
			   //-- print linked note records
			   $noterec = find_gedcom_record($nmatch[1]);
			   $nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
			   $text ="";
			   if ($nt>0) $text = preg_replace("/~~/", "<br />", trim($n1match[1]));
			   $text .= get_cont(1, $noterec);
			   $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">URL</a>", $text);
			   $text = trim($text);
			   if (!empty($text)) {
				   print "\n\t\t<br /><span class=\"label\">".$pgv_lang["note"].": </span><span class=\"field\">";
			   	   print PrintReady($text);
		   		}
		   		if (preg_match("/1 SOUR/", $noterec)>0) {
			   		print "<br />\n";
					print_fact_sources($noterec, 1);
				}
		  }
		  if (preg_match("/$nlevel SOUR/", $factrec)>0) {
		  	print "<div class=\"indent\">";
		  	print_fact_sources($nrec, $nlevel);
		  	print "</div></span>";
	  	}
	 }
}

/**
 * print main note row
 *
 * this function will print a table row for a fact table for a level 1 note in the main record
 * @param string $factrec	the raw gedcom sub record for this note
 * @param int $level		The start level for this note, usually 1
 * @param string $pid		The gedcom XREF id for the level 0 record that this note is a part of
 * @param int $linenum		The line number in the level 0 record where this record was found.  This is used for online editing.
 */
function print_main_notes($factrec, $level, $pid, $linenum) {
	 global $pgv_lang;
	 global $factarray, $view;
	 global $WORD_WRAPPED_NOTES, $PGV_IMAGE_DIR;
	 global $PGV_IMAGES;

	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="blue";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="red";

	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level NOTE ".$match[$j][1]);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $nrec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!showFact("NOTE", $pid)) return false;
		  print "\n\t\t<tr><td valign=\"top\" class=\"facts_label$styleadd\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["note"]["other"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />".$pgv_lang["note"].":";
		  if (userCanEdit(getUserName())&&($styleadd!="red")&&($view!="preview")) {
			$menu = array();
			$menu["label"] = $pgv_lang["edit"];
			$menu["labelpos"] = "right";
			$menu["icon"] = "";
			$menu["link"] = "#";
			$menu["onclick"] = "return edit_record('$pid', $linenum);";
			$menu["class"] = "";
			$menu["hoverclass"] = "";
			$menu["flyout"] = "down";
			$menu["submenuclass"] = "submenu";
			$menu["items"] = array();
			$submenu = array();
			$submenu["label"] = $pgv_lang["edit"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return edit_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["delete"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return delete_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["copy"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return copy_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			print " <div style=\"width:25px;\">";
			print_menu($menu);
			print "</div>";
		}
		  print " </td>\n<td class=\"facts_value$styleadd\">";
		  if (showFactDetails("NOTE", $pid)) {
			   $nt = preg_match("/\d NOTE @(.*)@/", $match[$j][0], $nmatch);
			   if ($nt==0) {
					//-- print embedded note records
					$text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
					$text .= get_cont($nlevel, $nrec);
					$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
//					print $text;
					print PrintReady($text);
			   }
			   else {
					//-- print linked note records
					$noterec = find_gedcom_record($nmatch[1]);
					$nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
					$text ="";
					if ($nt>0) $text = preg_replace("/~~/", "<br />", trim($n1match[1]));
					$text .= get_cont(1, $noterec);
					$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
					print PrintReady($text)."<br />\n";
					print_fact_sources($noterec, 1);
			   }
			   print "<br />\n";
			   print_fact_sources($nrec, $nlevel);
		  }
		  print "</td></tr>";
	 }
}

//-- Print the links to multi-media objects
function print_main_media($factrec, $level, $pid, $linenum) {
	 global $MULTI_MEDIA, $GEDCOM, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $MEDIA_EXTERNAL;
	 global $pgv_lang;
	 global $factarray, $view;
	 global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION;

	 if( !$MULTI_MEDIA ) return;

	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="blue";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="red";

	 $nlevel = $level+1;

	 $ct = preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 for($i=0; $i<$ct; $i++) {
		  $thumbnail="";
		  $filename="";
		  $title="";
		  if (!showFact("OBJE", $pid)) return false;
		  $spos1 = strpos($factrec, "$level OBJE".$omatch[$i][1]);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $orec = substr($factrec, $spos1, $spos2-$spos1);
		  $nt = preg_match("/@(.*)@/", $omatch[$i][1], $nmatch);
		  if ($nt==0) {
			   $tt = preg_match("/$nlevel TITL (.*)/", $orec, $match);
			   if ($tt>0) {
				   $title = $match[1];
			   	   $title = trim($match[1]);
		   	   }
			   preg_match("/$nlevel _*FILE (.*)/", $orec, $amatch);
			   if ($MEDIA_EXTERNAL && (strstr($amatch[1], "://")||stristr($amatch[1], "mailto:"))){
			   		$filename = trim($amatch[1]);
					$image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
					$path_end=substr($filename, strlen($filename)-5);
					$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
					if ($MEDIA_EXTERNAL && in_array($type, $image_type)) {
						$thumbnail = $MEDIA_DIRECTORY."thumbs/".extract_filename($filename);
						//$thumbnail=$filename;
					}
					else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
			   }
			   else {
				   $fullpath = extract_fullpath($orec);
				   $filename = "";
				   $filename = extract_filename($fullpath);
				   $thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
				   $thumbnail = trim($thumbnail);
				   $filename = $MEDIA_DIRECTORY.$filename;
				   $filename = trim($filename);
			   }
			   if (empty($title)) $title = $filename;
		  }
		  //-- look for a matching record
		  else {
			   $objrec = find_gedcom_record($nmatch[1]);
			   if ($objrec) {
					$tt = preg_match("/1 TITL (.*)/", $objrec, $match);
					$ft = preg_match("/1 _*FILE (.*)/", $objrec, $amatch);
					if ($tt>0) $title = trim($match[1]);
					if ($ft>0) {
					   if ($MEDIA_EXTERNAL && (strstr($amatch[1], "://")||stristr($amatch[1], "mailto:"))){
					   		$filename = trim($amatch[1]);
						    $image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
							$path_end=substr($filename, strlen($filename)-5);
							$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
							if ($MEDIA_EXTERNAL && in_array($type, $image_type)) $thumbnail=$filename;
							else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
					   }
					   else {
						   $fullpath = extract_fullpath($objrec);
						   $filename = "";
						   $filename = extract_filename($fullpath);
						   $thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
						   $thumbnail = trim($thumbnail);
						   $filename = $MEDIA_DIRECTORY.$filename;
						   $filename = trim($filename);
					   }
					}
					if (empty($title)) $title = $filename;
			   }
			   else {
					return false;
			   }
		  }

		  $imgwidth = 300;
		  $imgheight = 300;
		  if (preg_match("'://'", $filename)) {
			if (in_array($type, $image_type)){
			   $imgwidth = 400;
			   $imgheight = 500;
			} else {
			   $imgwidth = 800;
			   $imgheight = 400;
			}
		  }
		  else if ((preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($filename)))) {
			   $imgsize = @getimagesize(filename_decode($filename));
			   if ($imgsize) {
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+50;
			   }
		  }

		  if (empty($thumbnail)) $thumbnail = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
		  print "\n\t\t<tr><td class=\"facts_label$styleadd\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />".$factarray["OBJE"].":";
		  if (userCanEdit(getUserName())&&($styleadd!="red")&&($view!="preview")) {
		 	$menu = array();
			$menu["label"] = $pgv_lang["edit"];
			$menu["labelpos"] = "right";
			$menu["icon"] = "";
			$menu["link"] = "#";
			$menu["onclick"] = "return edit_record('$pid', $linenum);";
			$menu["class"] = "";
			$menu["hoverclass"] = "";
			$menu["flyout"] = "down";
			$menu["submenuclass"] = "submenu";
			$menu["items"] = array();
			$submenu = array();
			$submenu["label"] = $pgv_lang["edit"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return edit_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["delete"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return delete_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["copy"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return copy_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			print " <div style=\"width:25px;\">";
			print_menu($menu);
			print "</div>";
		}
		  print "</td><td class=\"facts_value$styleadd\"><span class=\"field\">";
		  if (showFactDetails("OBJE", $pid)) {
			   if (preg_match("'://'", $thumbnail)||(preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($thumbnail)))) print "<a href=\"#\" onclick=\"return openImage('".urlencode($filename)."',$imgwidth, $imgheight);\"><img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\" alt=\"\" /></a>";

			   if ($MEDIA_EXTERNAL && stristr($filename, "mailto:")) print "<a href=\"".$filename."\">";
			   else print "<a href=\"#\" onclick=\"return openImage('".urlencode($filename)."',$imgwidth, $imgheight);\">";
 			   print "<i>".PrintReady($title)."</i></a>";  //Does not work for I90 picture on Heb page

			   $tt = preg_match("/$nlevel FORM (.*)/", $orec, $match);
			   if ($tt>0) print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">$match[1]</span>";
			   print "</span>";
			   print "<br />\n";
			   //-- print spouse name for marriage events
			   $ct = preg_match("/PGV_SPOUSE: (.*)/", $factrec, $match);
			   if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						 print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						 if (displayDetailsById($spouse)||showLivingNameById($spouse)) print PrintReady(get_person_name($spouse));
						 else print $pgv_lang["private"];
						 print "</a>";
					}
					if (($view!="preview") && ($spouse!=="")) print " - ";
					if ($view!="preview") {
						 $ct = preg_match("/PGV_FAMILY_ID: (.*)/", $factrec, $match);
						 if ($ct>0) {
							  $famid = trim($match[1]);
							  print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"];
							  if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;";
							  print "]</a>\n";
						 }
					}
			   }
			   print "<br />\n";
			   print_fact_notes($orec, $nlevel);
			   print_fact_sources($orec, $nlevel);
		  }
		  print "</td></tr>";
	 } //-- end for loop
}
//-- Print the links to multi-media objects
function print_media_links($factrec, $level) {
	 global $MULTI_MEDIA;
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $MEDIA_EXTERNAL;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;

	 if( !$MULTI_MEDIA )
		  return;

	 $nlevel = $level+1;

	 if ($level==1) $size=50;
	 else $size=25;

	 $ct = preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 $spos2 = 0;
	 for($i=0; $i<$ct; $i++) {
		  $thumbnail="";
		  $filename="";
		  $title="";
		  $spos1 = strpos($factrec, $omatch[$i][0], $spos2);
		  $spos2 = strpos($factrec, "\n$level", $spos1+1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $orec = substr($factrec, $spos1, $spos2-$spos1);
		  $nt = preg_match("/@(.*)@/", $omatch[$i][1], $nmatch);
		  if ($nt==0) {
			   $tt = preg_match("/$nlevel TITL (.*)/", $orec, $match);
			   if ($tt>0) {
				   $title = $match[1];
			   	   $title = trim($match[1]);
		   	   }
			   preg_match("/$nlevel _*FILE (.*)/", $orec, $amatch);
			   if ($MEDIA_EXTERNAL && (strstr($amatch[1], "://")||stristr($amatch[1], "mailto:"))){
			   		$filename = trim($amatch[1]);
				    $image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
					$path_end=substr($filename, strlen($filename)-5);
					$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
					if ($MEDIA_EXTERNAL && in_array($type, $image_type)) $thumbnail=$filename;
					else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
			   }
			   else {
				   $fullpath = extract_fullpath($orec);
				   $filename = "";
				   $filename = extract_filename($fullpath);
				   $thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
				   $thumbnail = trim($thumbnail);
				   $filename = $MEDIA_DIRECTORY.$filename;
				   $filename = trim($filename);
			   }
			   if (empty($title)) $title = $filename;
		  }
		  //-- look for a matching record
		  else {
			   $objrec = find_gedcom_record($nmatch[1]);
			   if ($objrec) {
					$tt = preg_match("/1 TITL (.*)/", $objrec, $match);
					$ft = preg_match("/1 _*FILE (.*)/", $objrec, $amatch);
					if ($tt>0) $title = trim($match[1]);
					if ($ft>0) {
					   if ($MEDIA_EXTERNAL && (strstr($amatch[1], "://")||stristr($amatch[1], "mailto:"))){
					   		$filename = trim($amatch[1]);
						    $image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
							$path_end=substr($filename, strlen($filename)-5);
							$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
							if ($MEDIA_EXTERNAL && in_array($type, $image_type)) $thumbnail=$filename;
							else $thumbnail=$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
					   }
					   else {
						   $fullpath = extract_fullpath($objrec);
						   $filename = "";
						   $filename = extract_filename($fullpath);
						   $thumbnail = $MEDIA_DIRECTORY."thumbs/".$filename;
						   $thumbnail = trim($thumbnail);
						   $filename = $MEDIA_DIRECTORY.$filename;
						   $filename = trim($filename);
					   }
					}
					if (empty($title)) $title = $filename;
			   }
			   else {
					return false;
			   }
		  }
		  $imgwidth = 300;
		  $imgheight = 300;
		  if (preg_match("'://'", $filename)>0) {
			if (in_array($type, $image_type)){
			   $imgwidth = 400;
			   $imgheight = 500;
			} else {
			   $imgwidth = 800;
			   $imgheight = 400;
			}
		  }
		  else if ((preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($filename)))) {
			   $imgsize = getimagesize(filename_decode($filename));
			   if ($imgsize){
				   $imgwidth = $imgsize[0]+50;
				   $imgheight = $imgsize[1]+50;
			   }
		  }
		  if (empty($thumbnail)) $thumbnail = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"];
		  if ((preg_match("'://'", $thumbnail)>0)||(preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($thumbnail)))){
		  	print "\n\t\t<table><tr><td valign=\"top\">";
        	if (stristr($filename, "mailto:")){
				if ($MEDIA_EXTERNAL) print "<a href=\"".$filename."\">";
			}
			else print "<a href=\"#\" onclick=\"return openImage('".urlencode($filename)."',$imgwidth, $imgheight);\">";
			print "<img src=\"".$thumbnail."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" width=\"50\"/>";
            if (!($MEDIA_EXTERNAL) && stristr($filename, "mailto:"));
			else print "</a>";
			print "</td><td>";
		  }
		  else print "<br />";
		  print "<span class=\"label\">".$factarray["OBJE"].": </span><span class=\"field\">";
          if (stristr($filename, "mailto:")){
			if ($MEDIA_EXTERNAL) print "<a href=\"".$filename."\">";
		  }
		  else print "<a href=\"#\" onclick=\"return openImage('".urlencode($filename)."',$imgwidth, $imgheight);\">";
		  print "<i>".PrintReady($title)."</i>";
          if (!($MEDIA_EXTERNAL) && stristr($filename, "mailto:"));
		  else print "</a>";
		  $tt = preg_match("/$nlevel FORM (.*)/", $orec, $match);
		  if ($tt>0) print "\n\t\t\t<span class=\"label\"><br />".$factarray["FORM"].": </span> <span class=\"field\">$match[1]</span>";
		  print "</span>";
		  print "<br />\n";
		  print_fact_notes($orec, $nlevel);
		  print_fact_sources($orec, $nlevel);
		  if ((preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($thumbnail)))) print "</td></tr></table>";
	 } //-- end for loop
}

/**
 * print an address structure
 *
 * takes a gedcom ADDR structure and prints out a human readable version of it.
 * @param string $factrec	The ADDR subrecord
 * @param int $level		The gedcom line level of the main ADDR record
 */
function print_address_structure($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES;

	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level ADDR(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 for($i=0; $i<$ct; $i++) {
 		  $arec = get_sub_record($level, "$level ADDR", $factrec, $i+1);
 		  if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["ADDR"].": </span><br /><div class=\"indent\">";
		  $cn = preg_match("/$nlevel _NAME (.*)/", $arec, $cmatch);
		  if ($cn>0) print str_replace("/", "", $cmatch[1])."<br />\n";
		  print PrintReady(trim($omatch[$i][1]));
		  $cont = get_cont($nlevel, $arec);
		  if (!empty($cont)) print PrintReady($cont);
		  else {
			  if (strlen(trim($omatch[$i][1])) > 0) print "<br />";
			  $cs = preg_match("/$nlevel ADR1 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  print "<br />";
					  $cn=0;
				  }
				  print PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel ADR2 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  print "<br />";
					  $cn=0;
				  }
				  print PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  print "<br />";
				  print PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  print ", ".PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  print " ".PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel CTRY (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  print "<br />";
				  print PrintReady($cmatch[1]);
			  }
		  }
		  if ($level>1) print "</div>\n";
	 }
	 $ct = preg_match_all("/$level PHON (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		 print "<br />";
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["PHON"].": </span><span class=\"field\">";
			   print "&lrm;".$omatch[$i][1]."&lrm;";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level EMAIL (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		 print "<br />";
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["EMAIL"].": </span><span class=\"field\">";
			   print "<a href=\"mailto:".$omatch[$i][1]."\">".$omatch[$i][1]."</a>\n";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		 print "<br />";
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["FAX"].": </span><span class=\"field\">";
 			   print "&lrm;".$omatch[$i][1]."&lrm;";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level (WWW|URL) (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		 print "<br />";
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["URL"].": </span><span class=\"field\">";
			   print "<a href=\"".$omatch[$i][2]."\" target=\"_blank\">".$omatch[$i][2]."</a>\n";
			   if ($level>1) print "</span>\n";
		  }
	 }
}

//-- function to print a privacy error with contact method
function print_privacy_error($username) {
	 global $pgv_lang, $CONTACT_METHOD, $SUPPORT_METHOD, $WEBMASTER_EMAIL;
	 $method = $CONTACT_METHOD;
	 if ($username==$WEBMASTER_EMAIL) $method = $SUPPORT_METHOD;

	 $user = getUser($username);
	 if (!$user) $method = "mailto";

	 print "<br /><span class=\"error\">".$pgv_lang["privacy_error"];
	 if ($method=="none") {
		  print "</span><br />\n";
		  return;
	 }
	 print $pgv_lang["more_information"];
	 if ($method=="mailto") {
		  if (!$user) {
			   $email = $username;
			   $fullname = $username;
		  }
		  else {
			   $email = $user["email"];
			   $fullname = $user["fullname"];
		  }
		  print " <a href=\"mailto:$email\">".$fullname."</a></span><br />";
	 }
	 else {
		  print " <a href=\"#\" onclick=\"message('$username','$method'); return false;\">".$user["fullname"]."</a></span><br />";
	 }
}

// Function to print popup help boxes
function print_help_link($help, $helpText) {
	 global $SHOW_CONTEXT_HELP, $pgv_lang,$view, $PGV_USE_HELPIMG, $PGV_IMAGES, $PGV_IMAGE_DIR;

	 if ($PGV_USE_HELPIMG) $sentense = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["help"]["small"]."\" class=\"icon\" width=\"15\" height=\"15\" alt=\"\" />";
	 else $sentense = $pgv_lang[$helpText];

	 if (($view!="preview")&&($_SESSION["show_context_help"])){
			   if ($helpText=="qm_ah"){
					if (userIsAdmin(getUserName())){
						 print " <a class=\"error help\" tabindex=\"0\" href=\"javascript://".$help."\" onclick=\"return helpPopup('$help');\">".$sentense."</a> \n";
					}
		  }
		  else print " <a class=\"help\" tabindex=\"0\" href=\"javascript:// ".$help."\" onclick=\"return helpPopup('$help');\">".$sentense."</a> \n";
	 }
}

/**
 * print a language variable
 *
 * It accepts any kind of language variable. This can be a single variable but also
 * a variable with included variables that needs to be converted.
 * print_text, which used to be called print_help_text, now takes 3 parameters
 *		of which only the 1st is mandatory
 * The first parameter is the variable that needs to be processed.  At nesting level zero,
 *		this is the name of a $pgv_lang array entry.  "whatever" refers to
 *		$pgv_lang["whatever"].  At nesting levels greater than zero, this is the name of
 *		any global variable, but *without* the $ in front.  For example, VERSION or
 *		pgv_lang["whatever"] or factarray["rowname"].
 * The second parameter is $level for the nested vars in a sentence.  This indicates
 *		that the function has been called recursively.
 * The third parameter $noprint is for returning the text instead of printing it
 *		This parameter, when set to 2 means, in addition to NOT printing the result,
 *		the input string $help is text that needs to be interpreted instead of being
 *		the name of a $pgv_lang array entry.  This lets you use this function to work
 *		on something other than $pgv_lang array entries, but coded according to the
 *		same rules.
 * When we want it to return text we need to code:
 * print_text($mytext, 0, 1);
 * @param string $help		The variable that needs to be processed.
 * @param int $level		The position of the embedded variable
 * @param int $noprint		The switch if the text needs to be printed or returned
 */
function print_text($help, $level=0, $noprint=0){
	 global $pgv_lang, $factarray, $VERSION, $VERSION_RELEASE, $COMMON_NAMES_THRESHOLD;
	 global $INDEX_DIRECTORY, $GEDCOMS, $GEDCOM, $GEDCOM_TITLE, $LANGUAGE;
	 global $GUESS_URL, $UpArrow;
	 global $repeat;
	 if (!isset($_SESSION["DEBUG_LANG"])) $DEBUG_LANG = "no";
	 else $DEBUG_LANG = $_SESSION["DEBUG_LANG"];
	 if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Variable called: ".$help."<br /><br />";
	 if (!isset($repeat)) $repeat = 0;
	 else $repeat++;
	 if ($repeat > 500) exit;
	 $sentence = "";
	 if ($level>0) {
		  $value ="";
		  eval("if (!empty(\$$help)) \$value = \$$help;");
		  if (empty($value)) return "";
		  $sentence = $value;
	 }
	 if (empty($sentence)) {
		  if ($noprint == 2) {
			  $sentence = $help;
	  	  }
	  	  else if (!empty($pgv_lang[$help])) $sentence = $pgv_lang[$help];
		  else {
			  if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Variable not present: ".$help."<br /><br />";
			  if ($level==0) {
				  if ($noprint==0) $sentence = "pgv_lang[".$help."]:".$pgv_lang["var_not_exist"];
		  	  } else $sentence = $pgv_lang["help_not_exist"];
		  }
	 }

	 $mod_sentence = "";
	 $replace = "";
	 $replace_text = "";
	 $sub = "";
	 $pos1 = 0;
	 $pos2 = 0;
	 $ct = preg_match_all("/#([a-zA-Z0-9_.\-\[\]]+)#/", $sentence, $match, PREG_SET_ORDER);
	 for($i=0; $i<$ct; $i++) {
		  $value = "";
		  $newreplace = preg_replace(array("/\[/","/\]/"), array("['","']"), $match[$i][1]);
		  if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Embedded variable: ".$match[$i][1]."<br /><br />";
		  $value = print_text($newreplace, $level+1);
		  if (!empty($value)) $sentence = str_replace($match[$i][0], $value, $sentence);
		  else if ($noprint==0) $sentence = str_replace($match[$i][0], $match[$i][1].": ".$pgv_lang["var_not_exist"], $sentence);
	 }
	 // ------ Replace paired ~  by tag_start and tag_end (those vars contain CSS classes)
	 while (stristr($sentence, "~") == TRUE){
		  $pos1 = strpos($sentence, "~");
		  $mod_sentence = substr_replace($sentence, " ", $pos1, 1);
		  if (stristr($mod_sentence, "~")){		// If there's a second one:
			  $pos2 = strpos($mod_sentence, "~");
			  $replace = substr($sentence, ($pos1+1), ($pos2-$pos1-1));
			  $replace_text = "<span class=\"helpstart$level\">".str2upper($replace)."</span>";
			  $sentence = str_replace("~".$replace."~", $replace_text, $sentence);
		  } else break;
	 }

	 if ($noprint>0) return $sentence;
	 if ($level>0) return $sentence;
	 print $sentence;
}

function print_help_index($help){
	 global $pgv_lang;
	 $sentence = $pgv_lang[$help];
	 $mod_sentence = "";
	 $replace = "";
	 $replace_text = "";
	 $sub = "";
	 $pos1 = 0;
	 $pos2 = 0;
	 $admcol=false;
	 $ch=0;
	 $help_sorted = array();
	 $var="";
	 while (stristr($sentence, "#") == TRUE){
		$pos1 = strpos($sentence, "#");
		$mod_sentence = substr_replace($sentence, " ", $pos1, 1);
		$pos2 = strpos($mod_sentence, "#");
		$replace = substr($sentence, ($pos1+1), ($pos2-$pos1-1));
		$sub = preg_replace(array("/pgv_lang\\[/","/\]/"), array("",""), $replace);
		if (isset($pgv_lang[$sub])) {
			$items = preg_split("/,/", $pgv_lang[$sub]);
			$var = $pgv_lang[$items[1]];
		}
		$sub = preg_replace(array("/factarray\\[/","/\]/"), array("",""), $replace);
		if (isset($factarray[$sub])) {
			$items = preg_split("/,/", $factarray[$sub]);
			$var = $factarray[$items[1]];
		}
		if (substr($var,0,1)=="_") {
			$admcol=true;
			$ch++;
		}
		   $replace_text = "<a href=\"help_text.php?help=".$items[0]."\">".$var."</a><br />";
		   $help_sorted[$replace_text] = $var;
		   $sentence = str_replace("#".$replace."#", $replace_text, $sentence);
	 }
	 uasort($help_sorted, "stringsort");
	 if ($ch==0) $ch=count($help_sorted);
	 else $ch +=$ch;
	 if ($ch>0) print "<table width=\"100%\"><tr><td style=\"vertical-align: top;\"><ul>";
	 $i=0;
	 foreach ($help_sorted as $k => $help_item){
		print "<li>".$k."</li>";
		$i++;
		if ($i==ceil($ch/2)) print "</ul></td><td style=\"vertical-align: top;\"><ul>";
	 }
	 if ($ch>0) print "</ul></td></tr></table>";

}

/**
 * prints a JavaScript popup menu
 *
 * This function will print the DHTML required
 * to create a JavaScript Popup menu.  The $menu
 * parameter is an array that looks like this
 * $menu["label"] = "Charts";
 * $menu["labelpos"] = "down"; // tells where the text should be positioned relative to the picture options are up down left right
 * $menu["icon"] = "images/pedigree.gif";
 * $menu["hovericon"] = "images/pedigree2.gif";
 * $menu["link"] = "pedigree.php";
 * $menu["class"] = "menuitem";
 * $menu["hoverclass"] = "menuitem_hover";
 * $menu["flyout"] = "down"; // options are up down left right
 * $menu["items"] = array(); // an array of like menu items
 * @author John Finlay
 * @param array $menu the menuitems array to print
 */
function print_menu($menu, $parentmenu="") {
	 global $menucount, $TEXT_DIRECTION;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;

	 if (!isset($menucount)) $menucount=0;
	 else $menucount++;

	 if ($menu=="separator") {
		  print "<div id=\"menu$menucount\" style=\"width: 90%; clear: both;\">\n";
		  print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"90%\" height=\"3\" alt=\"\" />\n";
		  print "</div>\n";
		  return;
	 }
	 if (empty($menu["labelpos"])) $menu["labelpos"]="right";
	 if (empty($menu["label"])) $menu["label"]=" ";
	 if (empty($menu["link"])) $menu["link"]= "#";
	 print "<div id=\"menu$menucount\" style=\"clear: both;\" class=\"".$menu["class"]."\">\n";
	 print "<a href=\"".$menu["link"]."\" onmouseover=\"";
	 if ((!empty($menu["items"]))&&(count($menu["items"])>=0)) print "show_submenu('menu".$menucount."_subs', 'menu".$menucount."', '".$menu["flyout"]."'); ";
	 if (!empty($menu["hoverclass"])) print "change_class('menu$menucount', '".$menu["hoverclass"]."'); ";
	 if (!empty($menu["hovericon"])) print "change_icon('menu".$menucount."_icon', '".$menu["hovericon"]."'); ";
	 print "\" onmouseout=\"";
	 if ((!empty($menu["items"]))&&(count($menu["items"])>=0)) print "timeout_submenu('menu".$menucount."_subs'); ";
	 if (!empty($menu["hoverclass"])) print "change_class('menu$menucount', '".$menu["class"]."'); ";
	 if (!empty($menu["hovericon"])) print "change_icon('menu".$menucount."_icon', '".$menu["icon"]."'); ";
	 if (!empty($menu["onclick"])) {
		  print "\" onclick=\"".$menu["onclick"];
	 }

	 print "\">";
	 if ($menu["labelpos"]=="up" || $menu["labelpos"]=="left") print $menu["label"];
	 if ($menu["labelpos"]=="up") print "<br />\n";
	 if (!empty($menu["icon"])) {
		  print "\n<img id=\"menu".$menucount."_icon\" src=\"".$menu["icon"]."\" class=\"icon\" alt=\"".preg_replace("/\"/", "", $menu["label"])."\" title=\"".preg_replace("/\"/", "", $menu["label"])."\" ";
		  if ($menu["labelpos"]=="left")
			   if ($TEXT_DIRECTION=="ltr") print "align=\"right\" ";
			   else print "align=\"left\" ";
		  else if ($menu["labelpos"]=="right")
			   if ($TEXT_DIRECTION=="ltr") print "align=\"left\" ";
			   else	print "align=\"right\" ";
		  print " />\n";
	 }
	 if ($menu["labelpos"]=="down") print "<br />";
	 if ($menu["labelpos"]=="down" || $menu["labelpos"]=="right") print $menu["label"]."<br />";
	 print "</a>\n";
	 if ((!empty($menu["items"]))&&(is_array($menu["items"]))&&(count($menu["items"])>=0)) {
		  $submenuid = "menu".$menucount."_subs";
		  if ($TEXT_DIRECTION=="ltr") print "<div style=\"text-align: left;\">";
		  else print "<div style=\"text-align: right;\">";
		  print "<div id=\"menu".$menucount."_subs\" class=\"".$menu["submenuclass"]."\" style=\"position: absolute; visibility: hidden; z-index: 100;";
		  if ($menu["flyout"]=="right") {
			  if ($TEXT_DIRECTION=="ltr") print " left: 80px;";
			  else print " right: 80px;";
		  }
		  print "\" onmouseover=\"show_submenu('$parentmenu'); show_submenu('$submenuid');\" onmouseout=\"timeout_submenu('menu".$menucount."_subs');\">\n";
		  foreach($menu["items"] as $indexval => $submenu) {
			   print_menu($submenu, $submenuid);
		  }
		  print "</div></div>\n";
	 }
	 print "</div>\n";
}

/**
 * gets a menu with links to the gedcom portals
 *
 * This function will create the menu structure and print
 * the menu that will take the visitor to the gedcom portals
 * @author John Finlay
 */
function get_gedcom_menu() {
	 global $GEDCOMS, $GEDCOM, $pgv_lang, $ALLOW_CHANGE_GEDCOM;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;

	 $menu = array();
	 $menu["label"] = $pgv_lang["welcome_page"];
	 $menu["labelpos"] = "down";
	 $menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["large"];
	 $menu["link"] = "index.php?command=gedcom";
	 $menu["class"] = "menuitem";
	 $menu["hoverclass"] = "menuitem_hover";
	 $menu["flyout"] = "down";
	 $menu["submenuclass"] = "submenu";

	 if ($ALLOW_CHANGE_GEDCOM && count($GEDCOMS)>1) {
		  $menu["items"] = array();

		  foreach($GEDCOMS as $ged=>$gedarray) {
			   $submenu = array();
			   $submenu["label"] = PrintReady($gedarray["title"]);
			   $submenu["labelpos"] = "right";
			   $submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"];
			   $submenu["link"] = "index.php?command=gedcom&amp;ged=$ged";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $menu["items"][] = $submenu;
		  }
	 }
	 return $menu;
}

/**
 * prints out a menu with links related to the user account
 *
 * This function will create the menu structure and print
 * the menu that will take the visitor to mygedview portal and other user account options
 */
function get_mygedview_submenu() {
	 global $GEDCOMS, $GEDCOM, $pgv_lang,$PGV_IMAGES;
	 global $PGV_IMAGE_DIR, $MEDIA_DIRECTORY, $MULTI_MEDIA;

	 $items = array();
	 $username = getUserName();
	 if (!empty($username)) {
		  $user = getUser($username);
		  $submenu = array();
		  $submenu["label"] = $pgv_lang["mgv"];
		  $submenu["labelpos"] = "right";
		if (isset($PGV_IMAGES["mygedview"]["small"]))
			$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"];
		  $submenu["link"] = "index.php?command=user";
		  $submenu["class"] = "submenuitem";
		  $submenu["hoverclass"] = "submenuitem_hover";
		  $items[] = $submenu;
		  if ($user["editaccount"]) {
			  $submenu = array();
			  $submenu["label"] = $pgv_lang["editowndata"];
			  $submenu["labelpos"] = "right";
			if (isset($PGV_IMAGES["mygedview"]["small"]))
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"];
			  $submenu["link"] = "edituser.php";
			  $submenu["class"] = "submenuitem";
			  $submenu["hoverclass"] = "submenuitem_hover";
			  $items[] = $submenu;
		  }
		  if (!empty($user["gedcomid"][$GEDCOM])) {
				$submenu = array();
				$submenu["label"] = $pgv_lang["quick_update_title"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"];
				$submenu["link"] = "#";
				$submenu["onclick"] = "return quickEdit('".$user["gedcomid"][$GEDCOM]."');";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["my_pedigree"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"];
				$submenu["link"] = "pedigree.php?rootid=".$user["gedcomid"][$GEDCOM];
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["my_indi"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"];
				$submenu["link"] = "individual.php?pid=".$user["gedcomid"][$GEDCOM];
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
		  }
		  if ((userIsAdmin($username)) || (userGedcomAdmin($username, $GEDCOM))){
			   $items[]="separator";
			   $submenu = array();
			   $submenu["label"] = $pgv_lang["admin"];
			   $submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["admin"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"];
			   $submenu["link"] = "admin.php";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $items[] = $submenu;
			   $submenu = array();
			   $submenu["label"] = $pgv_lang["manage_gedcoms"];
			   $submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["admin"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"];
			   $submenu["link"] = "editgedcoms.php";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $items[] = $submenu;
			   if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$submenu = array();
				$submenu["label"] = $pgv_lang["upload_media"];
				$submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["media"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"];
				$submenu["link"] = "uploadmedia.php";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
			  }
		  }
		  else if (userCanEdit($username)) {
			  if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$items[]="separator";
				$submenu = array();
				$submenu["label"] = $pgv_lang["upload_media"];
				$submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["media"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"];
				$submenu["link"] = "uploadmedia.php";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
			  }
		  }
	 }
	 return $items;
}

/**
 * get the reports submenu
 *
 */
function get_reports_submenu($class="submenuitem", $hoverclass="submenuitem_hover") {
	global $GEDCOMS, $GEDCOM, $pgv_lang, $PGV_IMAGES;
	global $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE, $PRIV_HIDE;
	global $PGV_IMAGE_DIR, $LANGUAGE;

	$reports = get_report_list();
	$items = array();
	$submenu = array();
	$submenu["label"] = $pgv_lang["choose_report"];
	$submenu["labelpos"] = "right";
	if (isset($PGV_IMAGES["reports"]["small"])) $submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["reports"]["small"];
	$submenu["link"] = "reportengine.php";
	$submenu["class"] = $class;
	$submenu["hoverclass"] = $hoverclass;
	$items[] = $submenu;
	$username = getUserName();
	foreach($reports as $file=>$report) {
		if (!isset($report["access"])) $report["access"] = $PRIV_PUBLIC;
		if ($report["access"]>=getUserAccessLevel($username)) {
			$submenu = array();
			if (!empty($report["title"][$LANGUAGE])) $submenu["label"] = $report["title"][$LANGUAGE];
			else $submenu["label"] = implode("", $report["title"]);
			$submenu["labelpos"] = "right";
			$submenu["link"] = "reportengine.php?action=setup&amp;report=".$report["file"];
			if (isset($PGV_IMAGES["reports"]["small"]) and isset($PGV_IMAGES[$report["icon"]]["small"])) $iconfile=$PGV_IMAGE_DIR."/".$PGV_IMAGES[$report["icon"]]["small"];
			if (isset($iconfile) && file_exists($iconfile)) $submenu["icon"] = $iconfile;
			$submenu["class"] = $class;
			$submenu["hoverclass"] = $hoverclass;
			$items[] = $submenu;
		}
	}
	return $items;
}

//-------------------------------------------------------------------------------------------------------------
// switches between left and rigth align on chosen text direction
//-------------------------------------------------------------------------------------------------------------
function write_align_with_textdir_check($t_dir)
{
  global $TEXT_DIRECTION;

  if ($t_dir == "left")
  {
	 if ($TEXT_DIRECTION == "ltr")
	 {
	   print " style=\"text-align:left; \" ";
	 }
	 else
	 {
	   print " style=\"text-align:right; \" ";
	 }
  }
  else
  {
	 if ($TEXT_DIRECTION == "ltr")
	 {
	   print " style=\"text-align:right; \" ";
	 }
	 else
	 {
	   print " style=\"text-align:left; \" ";
	 }
  }
}

//-- print theme change dropdown box
function print_theme_dropdown($style=0) {
	 global $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES, $THEME_DIR, $pgv_lang, $themeformcount;
	 if ($ALLOW_THEME_DROPDOWN && $ALLOW_USER_THEMES) {
		  if (!isset($themeformcount)) $themeformcount = 0;
		  $themeformcount++;
		  $uname = getUserName();
		  $user = getUser($uname);
		  isset($_SERVER["QUERY_STRING"]) == true?$tqstring = "?".$_SERVER["QUERY_STRING"]:$tqstring = "";
		  $frompage = $_SERVER["PHP_SELF"].$tqstring;
		  $themes = get_theme_names();
		  print "<div class=\"theme_form\">\n";
		  switch ($style) {
			   case 0:
			   print "<form action=\"themechange.php\" name=\"themeform$themeformcount\" method=\"post\">";
			   print "<input type=\"hidden\" name=\"frompage\" value=\"".urlencode($frompage)."\" />";
			   print "<select name=\"mytheme\" class=\"header_select\" onchange=\"document.themeform$themeformcount.submit();\">";
			   print "<option value=\"\">".$pgv_lang["change_theme"]."</option>\n";
			   foreach($themes as $indexval => $themedir) {
					print "<option value=\"".$themedir["dir"]."\"";
					if ($uname) {
						 if ($themedir["dir"] == $user["theme"]) print " class=\"selected-option\"";
					}
					else {
						  if ($themedir["dir"] == $THEME_DIR) print " class=\"selected-option\"";
					}
					print ">".$themedir["name"]."</option>\n";
			   }
			   print "</select></form>";
			   break;
			   case 1:
					$menu = array();
					$menu["label"] = $pgv_lang["change_theme"];
					$menu["labelpos"] = "left";
					$menu["link"] = "#";
					$menu["class"] = "thememenuitem";
					$menu["hoverclass"] = "thememenuitem_hover";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "themesubmenu";
					$menu["items"] = array();
					foreach($themes as $indexval => $themedir) {
						 $submenu = array();
						 $submenu["label"] = $themedir["name"];
						 $submenu["labelpos"] = "right";
						 $submenu["link"] = "themechange.php?frompage=".urlencode($frompage)."&amp;mytheme=".$themedir["dir"];
						 $submenu["class"] = "favsubmenuitem";
						 $submenu["hoverclass"] = "favsubmenuitem_hover";
						 $menu["items"][] = $submenu;
					}
					print_menu($menu);
			   break;
		  }
		  print "</div>\n";
	 }
	 else {
		  print "&nbsp;";
	 }
}

/**
 * print information for a name record
 *
 * Called from the individual information page
 * @see individual.php
 * @param string $factrec	the raw gedcom record of the name to print
 * @param int $linenum		the line number from the original INDI gedcom record where this name record started, used for editing
 */
function print_name_record($factrec, $linenum) {
   global $pgv_lang, $pid, $factarray, $NAME_COUNT, $view, $disp, $TOTAL_NAMES;

   if ((!showFact("NAME", $pid))||(!showFactDetails("NAME", $pid))) return false;
   $lines = split("\n", $factrec);
   $NAME_COUNT++;
   print "<td valign=\"top\"";
   if (preg_match("/PGV_OLD/", $factrec)>0) print " class=\"namered\"";
   if (preg_match("/PGV_NEW/", $factrec)>0) print " class=\"nameblue\"";
   print ">";
   if ($NAME_COUNT>1) print "\n\t\t<span class=\"label\">".$pgv_lang["aka"]." </span><br />\n";
   $ct = preg_match_all("/2 (SURN)|(GIVN) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
   if ($ct==0) {
		$nt = preg_match("/1 NAME (.*)/", $factrec, $nmatch);
		if ($nt>0){
			print "\n\t\t<span class=\"label\">".$pgv_lang["name"].": </span><br />";
			$name = trim($nmatch[1]);
			$name = preg_replace("'/,'", ",", $name);
   			$name = preg_replace("'/'", " ", $name);
			// handle PAF extra NPFX [ 961860 ]
			$ct = preg_match("/2 NPFX (.*)/", $factrec, $match);
			if ($ct>0) {
				$npfx = trim($match[1]);
				if (strpos($name, $npfx)===false) $name = $npfx." ".$name;
			}
			print PrintReady($name)."<br />\n";
		}
   }
// $ct = preg_match_all("/2 ([_A-Z]+) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
   $ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
   for($i=0; $i<$ct; $i++) {
		  $fact = trim($nmatch[$i][1]);
		  if (($fact!="SOUR")&&($fact!="NOTE")) {
				  print "\n\t\t\t<span class=\"label\">";
				  if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
				  else if (isset($factarray[$fact])) print $factarray[$fact];
				  else print $fact;
				  print ":</span><span class=\"field\"> ";
				  if (isset($nmatch[$i][2])) {
				  		$name = trim($nmatch[$i][2]);
				  		$name = preg_replace("'/,'", ",", $name);
		   				$name = preg_replace("'/'", " ", $name);
						print PrintReady(check_NN($name));
			  	  }
				  print " </span><br />";
		  }
   }
   if ($TOTAL_NAMES>1) {
	   if (($view!="preview") && (userCanEdit(getUserName())&&($disp)) && (preg_match("/PGV_OLD/", $factrec)==0)) {
			   print "<font size=\"1\">&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"edit_name('$pid', $linenum); return false;\">".$pgv_lang["edit_name"]."</a> | ";
			   print "<a href=\"#\" onclick=\"delete_record('$pid', $linenum); return false;\">".$pgv_lang["delete_name"]."</a></font>\n";
			   if ($NAME_COUNT==2)print_help_link("delete_name_help", "qm");
			   print "<br />\n";
	   }
	}
   $ct = preg_match("/\d (NOTE)|(SOUR)/", $factrec);
   if ($ct>0) {
		  // -- find sources for this name
		  print "<div class=\"indent\">";
		  print_fact_sources($factrec, 2);
		  //-- find the notes for this name
		  print "&nbsp;&nbsp;&nbsp;";
		  print_fact_notes($factrec, 2);
		  print "</div><br />";
   }
   print "</td>\n";
}

/**
 * print information for a sex record
 *
 * Called from the individual information page
 * @see individual.php
 * @param string $factrec	the raw gedcom record to print
 * @param int $linenum		the line number from the original INDI gedcom record where this sex record started, used for editing
 */
function print_sex_record($factrec, $linenum) {
   global $pgv_lang, $sexarray, $pid, $sex, $PGV_IMAGE_DIR, $PGV_IMAGES, $disp, $view, $SEX_COUNT;
   if ((!showFact("SEX", $pid))||(!showFactDetails("SEX", $pid))) return false;
//   $ft = preg_match("/\d\s(_?\w+)(.*)/", $factrec, $match);
   $ft = preg_match("/\d\s(\w+)(.*)/", $factrec, $match);
   $sex = trim($match[2]);
   if (empty($sex)) $sex = "U";
	print "<td valign=\"top\"><span class=\"label\">".$pgv_lang["sex"].":    </span><span class=\"field\">".$sexarray[$sex];
	print " <img src=\"$PGV_IMAGE_DIR/";
	if ($sex=="M") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
	else if ($sex=="F") print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
	else print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["sex"]." ".$pgv_lang["unknown"];
	print "\" width=\"0\" height=\"0\" class=\"sex_image\" border=\"0\" />";
	if ($SEX_COUNT>1) {
		if (($view!="preview") && (userCanEdit(getUserName())&&($disp)) && (preg_match("/PGV_OLD/", $factrec)==0)) {
		    if ($linenum=="new") print "<font size=\"1\"><a href=\"#\" onclick=\"add_new_record('$pid', 'SEX'); return false;\">".$pgv_lang["edit"]."</a>";
		    else print "<font size=\"1\"><a href=\"#\" onclick=\"edit_record('$pid', $linenum); return false;\">".$pgv_lang["edit"]."</a>";
		}
	}
	print "<br /></span>";
   // -- find sources
   print "&nbsp;&nbsp;&nbsp;";
   print_fact_sources($factrec, 2);
   //-- find the notes
   print "&nbsp;&nbsp;&nbsp;";
   print_fact_notes($factrec, 2);
   print "</td>";
}

/**
 * Prepare text with parenthesis for printing
 * Convert & to &amp; for xhtml compliance
 *
 * @param string $text to be printed
 */
function PrintReady($text, $InHeaders=false) {
	global $TEXT_DIRECTION, $SpecialChar, $SpecialPar, $query, $action, $firstname, $lastname, $place, $year;

	// Check whether Search page highlighting should be done or not
	$HighlightOK = false;
	if (strstr($_SERVER["PHP_SELF"], "search.php")) {	// If we're on the Search page
		if (!$InHeaders) {								//   and also in page body
			if ((isset($query) and ($query != "")) || (isset($action) && ($action === "soundex"))) {		//   and the query isn't blank
				$HighlightOK = true;					// It's OK to mark search result
			}
		}
	}

	$SpecialOpen = '(';
	$SpecialClose = array('(');

	//-- convert all & to &amp;
	$text = preg_replace("/&/", "&amp;", $text);
	//-- make sure we didn't double convert &amp; to &amp;amp;
	$text = preg_replace("/&amp;(\w+);/", "&$1;", $text);
    $text=trim($text);
    //-- if we are on the search page body, then highlight any search hits
    if ($HighlightOK) {
	    if (isset($query)) {
	    $queries = preg_split("/\.\*/", $query);
	    $newtext = $text;
	    $hasallhits = true;
	    foreach($queries as $index=>$query1) {
		    if (preg_match("/(".$query1.")/i", $text)) {
	    		$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
    		}
			else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
				$nlen = strlen($query1);
				$npos = strpos(str2upper($text), str2upper($query1));
	    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
	    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
    		}
    		else $hasallhits = false;
    	}
    	if ($hasallhits) $text = $newtext;
    }
    	if (isset($action) && ($action === "soundex")) {

	    	if (isset($firstname)) {
	    		$queries = preg_split("/\.\*/", $firstname);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($lastname)) {
	    		$queries = preg_split("/\.\*/", $lastname);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($place)) {
	    		$queries = preg_split("/\.\*/", $place);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($year)) {
	    		$queries = preg_split("/\.\*/", $year);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    	}
    }
    if ($TEXT_DIRECTION=="ltr" && hasRTLText($text)) {
   		if (hasLTRText($text)) {
	   		// Text contains both RtL and LtR characters
	   		// return the parenthesis with surrounding &rlm; and the rest as is

	   		$printvalue = "";
	   		$first = 1;
	   		$linestart = 0;
	   		for ($i=0; $i<strlen($text); $i++) {
                $byte = substr($text,$i,1);
				if (substr($text,$i,6) == "<br />") $linestart = $i+6;
				if (in_array($byte,$SpecialPar)	||
                   (($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />") && in_array($byte,$SpecialChar))) {
			   		if ($first==1) {
				   		if ($byte==")" && !in_array(substr($text,$i+1),$SpecialClose)) {
 			   		    	 $printvalue .= "&lrm;".$byte."&lrm;";
 			   			$linestart = $i+1;
 			   			}
 				   		else
				   		if (in_array($byte,$SpecialChar)) {                          //-- all special chars
				   		    if (hasRTLText(substr($text,$linestart,4)))
				   		    	 $printvalue .= "&rlm;".$byte."&rlm;";
				   			else $printvalue .= "&lrm;".$byte."&lrm;";
			   			}
				   		else {
				   		$first = 0;
				   			if (hasRTLText(substr($text,$i+1,4))) {
				   		     $printvalue .= "&rlm;";
				   			    $ltrflag = 0;
			   			     }
				   			else {
					   			$printvalue .= "&lrm;";
				   			    $ltrflag = 1;
				   			}
				   		$printvalue .= substr($text,$i,1);
			   		}
			   		}
			   		else {
				   		$first = 1;
				   		$printvalue .= substr($text,$i,1);
				   		if ($ltrflag)
				   		     $printvalue .= "&lrm;";
				   		else $printvalue .= "&rlm;";
			   		}
		   		}
		   			 else if (oneRTLText(substr($text,$i,2))) {
			   		    	$printvalue .= substr($text,$i,2);
			   		    	$i++;
		   		    	}
		   		    	else $printvalue .= substr($text,$i,1);
	   		}
			if (!$first)
				if ($ltrflag)
					 $printvalue .= "&lrm;";
				else $printvalue .= "&rlm;";

 			return $printvalue;
   		}
   		else return "&rlm;".$text."&rlm;";
	}
	else if ($TEXT_DIRECTION=="rtl" && hasLTRText($text)) {
   		$printvalue = "";
   		$linestart = 0;
   		$first = 1;
   		for ($i=0; $i<strlen($text); $i++) {
            $byte = substr($text,$i,1);
            if (substr($text,$i,6) == "<br />") $linestart = $i+6;
			if (in_array($byte,$SpecialPar)	|| (($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />") && in_array($byte,$SpecialChar))) {
		   		if ($first==1) {
			   		if ($byte==")" && !in_array(substr($text,$i+1),$SpecialClose)) {
			   		    	$printvalue .= "&rlm;".$byte."&rlm;";
			   				$linestart = $i+1;
			   			}
				   		else
			   		if (in_array($byte,$SpecialChar) && ($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />")) {
			   		    if (hasRTLText(substr($text,$linestart,4)))
			   		    	 $printvalue .= "&rlm;".$byte."&rlm;";
			   			else $printvalue .= "&lrm;".$byte."&lrm;";
		   			}
		   			else {
			   		$first = 0;
				   		if (hasRTLText(substr($text,$i+1,4))) {
			   		     $printvalue .= "&rlm;";
			   			    $ltrflag = 0;
			   		     }
				   		else {
					   		$printvalue .= "&lrm;";
			   			    $ltrflag = 1;
				   		}
			   		$printvalue .= substr($text,$i,1);
		   		}
		   		}
		   		else {
			   		$first = 1;
			   		$printvalue .= substr($text,$i,1);
			   		if ($ltrflag)
			   		     $printvalue .= "&lrm;";
			   		else $printvalue .= "&rlm;";
		   		}
	   		}
	   		else {
		   		if (oneRTLText(substr($text,$i,2))) {
		   		    $printvalue .= substr($text,$i,2);
		   		    $i++;
	   		    }
	   		    else $printvalue .= substr($text,$i,1);
   		    }
   		}
	 	if (!$first) if ($ltrflag) $printvalue .= "&lrm;";
		else $printvalue .= "&rlm;";
		return $printvalue;
     }
	 else return $text;
}

/**
 * print ASSO RELA information
 *
 * Ex1:
 * <code>1 ASSO @I1@
 * 2 RELA Twin</code>
 *
 * Ex2:
 * <code>1 CHR
 * 2 ASSO @I1@
 * 3 RELA Godfather
 * 2 ASSO @I2@
 * 3 RELA Godmother</code>
 *
 * @author opus27
 * @param string $pid		person or family ID
 * @param string $factrec	the raw gedcom record to print
 * @param string $linebr 	optional linebreak
 */
function print_asso_rela_record($pid, $factrec, $linebr=false) {
	global $GEDCOM, $SHOW_ID_NUMBERS, $TEXT_DIRECTION, $pgv_lang, $factarray, $PGV_IMAGE_DIR, $PGV_IMAGES;

	// get ASSOciate(s) ID(s)
	$ct = preg_match_all("/\d ASSO @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		$level = substr($match[$i][0],0,1);
		$pid2 = $match[$i][1];
		// get RELAtionship field
		$assorec = get_sub_record($level, " ASSO ", $factrec, $i+1);
		$rct = preg_match("/\d RELA (.*)/", $assorec, $rmatch);
		if ($rct>0) {
			// RELAtionship name in user language
			$key = strtolower(trim($rmatch[1]));
			if (isset($pgv_lang["$key"])) $rela = $pgv_lang[$key];
			else $rela = $rmatch[1];
			$p = strpos($rela, "(=");
			if ($p>0) $rela = trim(substr($rela, 0, $p));
		}
		else $rela = $factarray["RELA"]; // default
		print "<br />$rela: ";
		// ASSOciate ID link
		$gedrec = find_gedcom_record($pid2);
		if (strstr($gedrec, "@ INDI")!==false
		or  strstr($gedrec, "@ SUBM")!==false) {
			// ID name
			if ((DisplayDetailsByID($pid2))||(showLivingNameByID($pid2))) {
				$name = get_person_name($pid2);
				$addname = get_add_person_name($pid2);
			}
			else {
				$name = $pgv_lang["private"];
				$addname = "";
			}
			print "<a href=\"individual.php?pid=$pid2&amp;ged=$GEDCOM\">" . PrintReady($name);
			if (!empty($addname)) print "<br />" . PrintReady($addname);
			if ($SHOW_ID_NUMBERS) print " <span dir=\"$TEXT_DIRECTION\">($pid2)</span>";
			print "</a>";
			// ID age
			$dct = preg_match("/2 DATE (.*)/", $factrec, $dmatch);
			if ($dct>0) print " <span class=\"age\">".get_age($gedrec, $dmatch[1])."</span>";
			// RELAtionship calculation : for a family print relationship to both spouses
			$famrec = find_family_record($pid);
			if ($famrec) {
				$parents = find_parents_in_record($famrec);
				$pid1 = $parents["HUSB"];
				if ($pid1) print " - <a href=\"relationship.php?pid1=$pid1&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["husband"] . "\" alt=\"" . $pgv_lang["husband"] . "\" class=\"sex_image\" />]</a>";
				$pid1 = $parents["WIFE"];
				if ($pid1) print " - <a href=\"relationship.php?pid1=$pid1&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["wife"] . "\" alt=\"" . $pgv_lang["wife"] . "\" class=\"sex_image\" />]</a>";
			}
			else print " - <a href=\"relationship.php?pid1=$pid&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "]</a>";
		}
		else {
			print $pgv_lang["unknown"];
			if ($SHOW_ID_NUMBERS) print " <span dir=\"$TEXT_DIRECTION\">($pid2)</span>";
		}
		if ($linebr) print "<br />\n";
		print_fact_sources($assorec, $level+1);
		print_fact_notes($assorec, $level+1);
	}
}

/**
 * Print age of parents
 *
 * @param string $pid	child ID
 * @param string $bdate	child birthdate
 */
function print_parents_age($pid, $bdate) {
	global $pgv_lang, $SHOW_PARENTS_AGE, $PGV_IMAGE_DIR, $PGV_IMAGES;

	if ($SHOW_PARENTS_AGE) {
		$famids = find_family_ids($pid);
		// dont show age of parents if more than one family (ADOPtion)
		if (count($famids)==1) {
			print " <span class=\"age\">";
			$parents = find_parents($famids[0]);
			// father
			$spouse = $parents["HUSB"];
			if ($spouse and showFact("BIRT", $spouse)) {
				$age = convert_number(get_age(find_person_record($spouse), $bdate, false));
				if (10<$age and $age<80) print "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["father"] . "\" alt=\"" . $pgv_lang["father"] . "\" class=\"sex_image\" />$age";
			}
			// mother
			$spouse = $parents["WIFE"];
			if ($spouse and showFact("BIRT", $spouse)) {
				$age = convert_number(get_age(find_person_record($spouse), $bdate, false));
				if (10<$age and $age<80) print "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["mother"] . "\" alt=\"" . $pgv_lang["mother"] . "\" class=\"sex_image\" />$age";
			}
			print "</span>";
		}
	}
}

/**
 * print fact DATE TIME
 *
 * @author opus27
 * @param string $factrec	gedcom fact record
 * @param boolean $anchor	option to print a link to calendar
 * @param boolean $time		option to print TIME value
 * @param string $fact		optional fact name (to print age)
 * @param string $pid		optional person ID (to print age)
 * @param string $indirec	optional individual record (to print age)
 */
function print_fact_date($factrec, $anchor=false, $time=false, $fact=false, $pid=false, $indirec=false) {
	$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		// link to calendar
		if ($anchor) print get_date_url($match[1]);
		// simple date
		else print PrintReady(get_changed_date(trim($match[1])));
		// time
		if ($time) {
			$timerec = get_sub_record(2, "2 TIME", $factrec);
			if (empty($timerec)) $timerec = get_sub_record(2, "2 DATE", $factrec);
			$tt = preg_match("/[2-3] TIME (.*)/", $timerec, $tmatch);
			if ($tt>0) print " - <span class=\"date\">".$tmatch[1]."</span>";
		}
		if ($fact and $pid) {
			// age of parents at child birth
			if ($fact=="BIRT") print_parents_age($pid, $match[1]);
			// age at event
			else if ($fact!="CHAN") {
				if (!$indirec) $indirec=find_person_record($pid);
				// do not print age after death
				$deatrec=get_sub_record(1, "1 DEAT", $indirec);
				if ((compare_facts($factrec, $deatrec)!=1)||(strstr($factrec, "1 DEAT"))) print get_age($indirec,$match[1]);
			}
		}
		print " ";
	}
}

/**
 * print fact PLACe TEMPle STATus
 *
 * @param string $factrec	gedcom fact record
 * @param boolean $anchor	option to print a link to placelist
 * @param boolean $sub		option to print place subrecords
 * @param boolean $lds		option to print LDS TEMPle and STATus
 */
function print_fact_place($factrec, $anchor=false, $sub=false, $lds=false) {
	global $SHOW_PEDIGREE_PLACES, $TEMPLE_CODES, $pgv_lang, $factarray;

	$out = false;
	$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		$levels = preg_split("/,/", $match[1]);
		if ($anchor) {
			$place = trim($match[1]);
			// reverse the array so that we get the top level first
			$levels = array_reverse($levels);
			print "<a href=\"placelist.php?action=show&amp;";
			foreach($levels as $pindex=>$ppart) {
				 // routine for replacing ampersands
				 $ppart = preg_replace("/amp\%3B/", "", trim($ppart));
				 print "parent[$pindex]=".PrintReady($ppart)."&amp;";
			}
			print "level=".count($levels);
			print "\"> ".PrintReady($place)."</a>";
		}
		else {
			print " -- ";
			for ($level=0; $level<$SHOW_PEDIGREE_PLACES; $level++) {
				if (!empty($levels[$level])) {
					if ($level>0) print ", ";
					print PrintReady($levels[$level]);
				}
			}
		}
	}
	$ctn=0;
	if ($sub) {
		$placerec = get_sub_record(2, "2 PLAC", $factrec);
		if (!empty($placerec)) {
			$cts = preg_match("/\d ROMN (.*)/", $placerec, $match);
			if ($cts>0) {
				if ($ct>0) print "<br />\n";
				print " ".PrintReady($match[1]);
			}
			$cts = preg_match("/\d _HEB (.*)/", $placerec, $match);
			if ($cts>0) {
				if ($ct>0) print "<br />\n";
				print " ".PrintReady($match[1]);
			}
			$cts = preg_match("/\d LATI (.*)/", $placerec, $match);
			if ($cts>0) print "<br />".$factarray["LATI"].": ".$match[1];
			$cts = preg_match("/\d LONG (.*)/", $placerec, $match);
			if ($cts>0) print " ".$factarray["LONG"].": ".$match[1];
			$ctn = preg_match("/\d NOTE (.*)/", $placerec, $match);
			if ($ctn>0) {
				print_fact_notes($placerec, 3);
				$out = true;
			}
		}
	}
	if ($lds) {
		$ct = preg_match("/2 TEMP (.*)/", $factrec, $match);
		if ($ct>0) {
			$tcode = trim($match[1]);
			if (array_key_exists($tcode, $TEMPLE_CODES)) print $pgv_lang["temple"].": ".$TEMPLE_CODES[$tcode];
			else print $pgv_lang["temple_code"].$tcode;
		}
		$ct = preg_match("/2 STAT (.*)/", $factrec, $match);
		if ($ct>0) {
			print "<br />".$pgv_lang["status"].": ";
			print trim($match[1]);
		}
	}
}

/**
 * print first major fact for an Individual
 *
 * @param string $key	indi pid
 */
function print_first_major_fact($key) {
	global $pgv_lang, $factarray, $PGV_BASE_DIRECTORY, $factsfile, $LANGUAGE;

	$majorfacts = array("BIRT", "CHR", "BAPM", "DEAT", "BURI", "BAPL", "ADOP");

	// make sure factarray is loaded
	if (!isset($factarray)) {
		require($PGV_BASE_DIRECTORY.$factsfile["english"]);
		if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);
	}

	$indirec = find_person_record($key);
	foreach ($majorfacts as $indexval => $fact) {
		$factrec = get_sub_record(1, "1 $fact", $indirec);
		if (strlen($factrec)>7 and showFact("$fact", $key) and !FactViewRestricted($key, $factrec)) {
			print " -- <i>";
			if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
			else if (isset($factarray[$fact])) print $factarray[$fact];
			else print $fact;
			print " ";
			print_fact_date($factrec);
			print_fact_place($factrec);
			print "</i>";
			break;
		}
	}
	return $fact;
}

/**
 * Print a config sensitive menu on the four media administration pages
 *
 * @param string $me Calling page codestring.
 */
function print_media_nav($me ){
	global $MEDIA_DIRECTORY_LEVELS,$MULTI_MEDIA_DB, $pgv_lang, $GEDCOM;

	$admin = "<a href=\"admin.php\">".$pgv_lang["admin"]."</a>";
	$uploadmedia = "<a href=\"uploadmedia.php\">".$pgv_lang["upload_media"]."</a>";
	$managemedia = "<a href=\"findmedia.php?embed=true\">".$pgv_lang["manage_media_files"]."</a>";
	$addmedia = "<a href=\"addmedia.php?ged=$GEDCOM\">".$pgv_lang["add_media_records"]."</a>";
	$linkmedia = "<a href=\"linkmedia.php?ged=$GEDCOM\">".$pgv_lang["link_media_records"]."</a>";

	if ($me == "uploadmedia") {
		print $admin."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if ($MEDIA_DIRECTORY_LEVELS > 0) {
			print $managemedia;
		} elseif ($MULTI_MEDIA_DB) {
			print $addmedia;
		}
	} elseif ($me == "managemedia") {
		print $admin."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		print $uploadmedia."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if ($MULTI_MEDIA_DB) {
			print $addmedia;
		}
	} elseif ($me == "addmedia") {
		print $admin."&nbsp;&nbsp;&nbsp;";
		if ($MEDIA_DIRECTORY_LEVELS > 0) {
			print $managemedia."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		} else{
			print $uploadmedia."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		print $linkmedia;
	} elseif ($me == "linkmedia") {
		print $addmedia."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		print $admin;
	} else {
		print "<div class=\"error\" >ERROR: Unknown calling page</div>"; // this should never happen no need for translation
	}

}

?>