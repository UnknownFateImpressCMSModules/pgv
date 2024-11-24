<?php
/**
 * Displays a list of the multimedia objects
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
 * @version $Id: medialist.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */
require("config.php");
global $MEDIA_EXTERNAL;
//global $TEXT_DIRECTION;

  //if ($TEXT_DIRECTION == "ltr")
  //{print "<td class=\"facts_value\" style=\"text-align:left; \">";}
  //else
  //{print "<td class=\"facts_value\" style=\"text-align:right; \">";}

function mediasort($a, $b) {
        return strnatcasecmp($a["title"], $b["title"]);
}

if (!isset($level)) $level=0;
if (!isset($action)) $action="";

// -- array of names
$medialist = array();
$foundlist = array();
print_header($pgv_lang["multi_title"]);
print "\n\t<div class=\"center\"><h2>".$pgv_lang["multi_title"]."</h2></div>\n\t";

//-- automatically generate an image
if (userIsAdmin(getUserName()) && $action=="generate" && !empty($file) && !empty($thumb)) {
	if (is_writable($MEDIA_DIRECTORY."thumbs")) generate_thumbnail($file, $thumb);
}

get_media_list();

//-- sort the media by title
usort($medialist, "mediasort");

//-- remove all private media objects
$newmedialist = array();
foreach($medialist as $indexval => $media) {
        print " ";
        $disp = true;
        $links = $media["link"];
	if (count($links) != 0) {
        foreach($links as $id=>$type) {
        	$disp = $disp && displayDetailsByID($id, $type);
        }
        if ($disp) $newmedialist[] = $media;
    }
}
$medialist = $newmedialist;
$ct = count($medialist);

if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;
$count = $max;
if ($start+$count > $ct)
        $count = $ct-$start;
print "\n\t<div align=\"center\">$ct ".$pgv_lang["media_found"]." <br />";
if ($ct>0){
	print "<form action=\"$PHP_SELF\" method=\"get\" > ".$pgv_lang["medialist_show"]." <select name=\"max\" onchange=\"javascript:submit();\">";
	for ($i=1;($i<=20&&$i-1<ceil($ct/10));$i++) {
	        print "<option value=\"".($i*10)."\" ";
	        if ($i*10==$max) print "selected=\"selected\" ";
	        print " >".($i*10)."</option>";
	}
	print "</select> ".$pgv_lang["per_page"];
	print "</form>";
}

print"\n\t<table class=\"list_table\">\n";
if ($ct>$max) {
        print "\n<tr>\n";
        print "<td align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
        if ($start>0) {
                $newstart = $start-$max;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?start=$newstart&amp;max=$max\">".$pgv_lang["prev"]."</a>\n";
        }
        print "</td><td align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
        if ($start+$max < $ct) {
                $newstart = $start+$count;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?start=$newstart&amp;max=$max\">".$pgv_lang["next"]."</a>\n";
        }
        print "</td></tr>\n";
}
print"\t\t<tr>\n\t\t";
// -- print the array
for($i=0; $i<$count; $i++) {
    $value = $medialist[$start+$i];
//	if (preg_match("'://'", $value["file"])){
	if ($MEDIA_EXTERNAL && (strstr($value["file"], "://")||stristr($value["file"], "mailto:"))){
		$image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
		$path_end=substr($value["file"], strlen($value["file"])-5);
		$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
		if (in_array($type, $image_type)){
		   $imgwidth = 400;
		   $imgheight = 500;
		} else {
		   $imgwidth = 800;
		   $imgheight = 400;
		}
    }
	else if (file_exists(filename_decode($value["file"]))) {
		$imgsize = getimagesize(filename_decode($value["file"]));
	    $imgwidth = $imgsize[0]+50;
	    $imgheight = $imgsize[1]+50;
	}
	else {
		$imgwidth=300;
		$imgheight=200;
	}
    print "\n\t\t\t<td class=\"list_value_wrap\" width=\"50%\">";
    print "<table class=\"$TEXT_DIRECTION\">\n\t<tr>\n\t\t<td valign=\"top\" style=\"white-space: normal;\">";

   	if (stristr($value["file"], "mailto:")){
		if ($MEDIA_EXTERNAL) print "<a href=\"".$value["file"]."\">";
	}
    else print "<a href=\"#\" onclick=\"return openImage('".urlencode($value["file"])."',$imgwidth, $imgheight);\">";
    if (file_exists(filename_decode($value["thumb"])) || strstr($value["thumb"], "://")) {
	    print "<img src=\"".$value["thumb"]."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" />";
	    $nothumb = false;
    }
	else {
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"]."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" />";
		$nothumb = true;
	}
	if (!($MEDIA_EXTERNAL) && stristr($value["file"], "mailto:"));
	else print "</a>";
	print "</td>\n\t\t<td class=\"list_value_wrap\" style=\"border: none;\" width=\"100%\">";
	if (userIsAdmin(getUserName()) && $nothumb && function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")) {
		if ((!strstr($value["file"], "mailto:"))&&(file_exists(filename_decode($value["file"]))||(strstr($value["file"], "://")))) {
			$ct = preg_match("/\.([^\.]+)$/", $value["file"], $match);
			if ($ct>0) {
				$ext = strtolower(trim($match[1]));
				if ($ext=="jpg" || $ext=="jpeg") print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."JPG</a><br />";
				if ($ext=="gif" && function_exists("imagecreatefromgif") && function_exists("imagegif")) print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."GIF</a><br />";
				if ($ext=="png" && function_exists("imagecreatefrompng") && function_exists("imagepng")) print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."PNG</a><br />";
			}
		}
	}
   	if (stristr($value["file"], "mailto:")){
		if ($MEDIA_EXTERNAL) print "<a href=\"".$value["file"]."\">";
	}
    else print "<a href=\"#\" onclick=\"return openImage('".urlencode($value["file"])."',$imgwidth, $imgheight);\">";

    print "<b>".PrintReady($value["title"])."</b>";
	if (!($MEDIA_EXTERNAL) && stristr($value["file"], "mailto:"));
	else print "</a>";

    $links = $value["link"];
	if (count($links) != 0){
		$indiexists = 0;
		$famexists = 0;
    	foreach($links as $id=>$type) {
            if (($type=="INDI")&&(displayDetailsByID($id))) {
            print " <br /><a href=\"individual.php?pid=".$id."\"> ".$pgv_lang["view_person"]." -".PrintReady(get_person_name($id))."</a>";
        	$indiexists = 1;
            }
            if ($type=="FAM") {
            	if ($indiexists && !$famexists) print "<br />";
	        	$famexists = 1;
           		print "<br /> <a href=\"family.php?famid=".$id."\"> ".$pgv_lang["view_family"]." - ".PrintReady(get_family_descriptor($id))."</a>";
        	}
            if ($type=="SOUR") {
	            if ($indiexists || $famexists) {
		            print "<br />";
		            $indiexists = 0;
					$famexists = 0;
        		}
            	print "<br /> <a href=\"source.php?sid=".$id."\"> ".$pgv_lang["view_source"]." - ".PrintReady(get_source_descriptor($id))."</a>";
   			}
        }
    }
    $value["file"] = filename_decode($value["file"]);
    if ((!strstr($value["file"], "://"))&&(!strstr($value["file"], "mailto:"))&&(!file_exists(filename_decode($value["file"])))) {
    	print "<br /><span class=\"error\">".$pgv_lang["file_not_found"]." ".$value["file"]."</span>";
    }
    print "<br /><br /><div class=\"indent\" style=\"white-space: normal; width: 95%;\">";
    print_fact_notes($value["gedcom"], $value["level"]);
    print "</div>";
    print "</td></tr></table>\n";
    print "</td>";
    if ($i%2 == 1 && $i < ($count-1)) print "\n\t\t</tr>\n\t\t<tr>";
}
print "\n\t\t</tr>";
if ($ct>$max) {
        print "\n<tr>\n";
        print "<td align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
        if ($start>0) {
                $newstart = $start-$max;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?start=$newstart&amp;max=$max\">".$pgv_lang["prev"]."</a>\n";
        }
        print "</td><td align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
        if ($start+$max < $ct) {
                $newstart = $start+$count;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?start=$newstart&amp;max=$max\">".$pgv_lang["next"]."</a>\n";
        }
        print "</td></tr>\n";
}
print "</table><br />";
print "\n</div>\n";
print_footer();

?>