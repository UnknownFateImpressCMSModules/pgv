<?php
/***********************************************************
	File: findsource.php
	Project: phpGedView
	Author: John Finlay
	Posts to: none
	Comments:
		Popup window that will allow a user to search for a source

	Change Log:
		9/03/03 - File Created (yalnifj)

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

***********************************************************/
# $Id: findsource.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
require("config.php");

$sourcelist = get_source_list();
$ct = count($sourcelist);

print_simple_header($pgv_lang["source_list"]);
?>
<script language="JavaScript" type="text/javascript">
	function paste_id(id) {
		window.opener.paste_id(id);
		window.close();
	}
</script>
<?php
print "\n\t<center><h2>".$pgv_lang["source_list"]."</h2></center>\n\t";
print "\n\t<center>$ct ".$pgv_lang["sources_found"]." <br />\n\t<table class=\"list_table\">\n\t\t<tr>\n\t\t<td class=\"list_value $TEXT_DIRECTION\">";
$i=0;
// -- print the array
foreach ($sourcelist as $key => $value) {
    print "\n\t\t\t<a href=\"#\" onclick=\"paste_id('$key');\"><span class=\"list_item\">".PrintReady($value["name"])."</span></a><br />\n";
    $i++;
}
print "\n\t\t</td>\n\t\t</tr>\n\t</table></center>";
print_simple_footer();
?>