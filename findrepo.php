<?php
/***********************************************************
	File: findsource.php
	Project: phpGedView
	Author: Boudewijn Sjouke
	Posts to: none
	Comments:
		Popup window that will allow a user to search for a source

	Change Log:
		1/26/05 - File Created (sjouke)

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
require("config.php");

$repolist = get_repo_list();
$ct = count($repolist);

print_simple_header($pgv_lang["repo_list"]);
?>
<script language="JavaScript" type="text/javascript">
	function paste_id(id) {
		window.opener.paste_id(id);
		window.close();
	}
</script>
<?php
print "\n\t<center><h2>".$pgv_lang["repo_list"]."</h2></center>\n\t";
print "\n\t<center>$ct ".$pgv_lang["repos_found"]." <br />\n\t<table class=\"list_table\">\n\t\t<tr>\n\t\t<td class=\"list_value\">";
$i=0;
// -- print the array
foreach ($repolist as $key => $value) {
	$id = $value["id"];
    print "\n\t\t\t<a href=\"#\" onclick=\"paste_id('$id');\"><span class=\"list_item\">".PrintReady($key)."</span></a><br />\n";
	if ($i == floor($ct / 2)) print "\n\t\t</td>\n\t\t<td class=\"list_value\">";
	$i++;
}
print "\n\t\t</td>\n\t\t</tr>\n\t</table></center>";
print_simple_footer();
?>