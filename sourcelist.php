<?php
/*=================================================
	Project: phpGedView
	File: sourcelist.php
	Author: John Finlay
	Comments:
		Parses gedcom file and displays a list of the sources in the file.

	Change Log:
		6/14/02 - File Created
		7/8/02 - changed to use index files

	phpGedView: Genealogy Viewer
    Copyright (C) 2002 to 2003  John Finlay and Others

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

===================================================*/
# $Id: sourcelist.php,v 1.1 2005/10/07 18:12:20 skenow Exp $

require("config.php");

if ($SHOW_SOURCES<getUserAccessLevel(getUserName())) {
	header("Location: index.php");
	exit;
}

$addsourcelist = get_source_add_title_list();  //-- array of additional source titlesadd
$sourcelist = get_source_list();               //-- array of regular source titles 

uasort($sourcelist, "itemsort"); 
uasort($addsourcelist, "itemsort"); 

$ca = count($addsourcelist);
$cs = get_list_size("sourcelist");
$ctot = $ca + $cs;
print_header($pgv_lang["source_list"]);
print "<div class=\"center\">";
print "<h2>".$pgv_lang["source_list"]."</h2>\n\t";

print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr><td class=\"list_label\"";
if($ca>0 || $cs>12)	print " colspan=\"2\"";
print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["large"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["sources"]."\" alt=\"".$pgv_lang["sources"]."\" />&nbsp;&nbsp;";
print $pgv_lang["titles_found"];
print "</td></tr><tr><td class=\"$TEXT_DIRECTION list_value_wrap\"><ul>";
$i=1;
if ($cs>0){
	// -- print the array
	foreach ($sourcelist as $key => $value) {
		if (begRTLText($value["name"])) 
		     print "\n\t\t\t<li type=\"circle\" class=\"rtl\" dir=\"rtl\">";
		else print "\n\t\t\t<li type=\"circle\" class=\"ltr\" dir=\"ltr\">";

		print "<a href=\"source.php?sid=$key\" class=\"list_item\">";
		print PrintReady($value["name"]);
		if ($SHOW_ID_NUMBERS)
			if ($TEXT_DIRECTION=="ltr") print " &lrm;($key)&lrm;";
			else print " &rlm;($key)&rlm;";

		print "</a></li>\n";

		if ($i==ceil($ctot/2) && $ctot>12) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		$i++;
	}
	if ($ca>0) {
	// -- print the additional array
//		print "</ul></td><td class=\" list_value_wrap\"><ul>";
		foreach ($addsourcelist as $key => $value) {
	    	if (begRTLText($value["name"])) 
		         print "\n\t\t\t<li type=\"circle\" class=\"rtl\" dir=\"rtl\">";
		    else print "\n\t\t\t<li type=\"circle\" class=\"ltr\" dir=\"ltr\">";			
		    print "<a href=\"source.php?sid=$key\" class=\"list_item\">";
	    	print PrintReady($value["name"]);
			if ($SHOW_ID_NUMBERS) 
				if ($TEXT_DIRECTION=="ltr") print " &lrm;($key)&lrm;";
				else print " &rlm;($key)&rlm;";
			print "</a></li>\n";
			
			if ($i==ceil($ctot/2) && $ctot>12) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
			$i++;

		}
	}

	print "\n\t\t</ul></td>\n\t\t";
 
	print "</tr><tr><td>".$pgv_lang["total_sources"]." ".$cs;
	if ($ca != 0) {
		print "&nbsp;&nbsp;(".$pgv_lang["titles_found"]."&nbsp;".($cs+$ca).")";
	}
}
else print "<span class=\"warning\"><i>".$pgv_lang["no_results"]."</span>";

print "</td>\n\t\t</tr>\n\t</table>";

print_help_link("sourcelist_listbox_help", "qm");
print "</div>";
print "<br /><br />";
print_footer();
?>