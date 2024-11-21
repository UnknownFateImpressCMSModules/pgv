<?php
/**
 * PopUp window that will allow a user to search for a place
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
 * @subpackage Edit
 * @version $Id: findplace.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */
require("config.php");
if (!isset($action)) $action="";
if (!isset($place)) $place="";

print_simple_header($pgv_lang["find_place"]);
?>
<script language="JavaScript" type="text/javascript">
	function pasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
</script>
<div align="center">
<font class="subheaders"><?php print $pgv_lang["find_place"]; ?></font>
<form name="filter" method="get">
<input type="hidden" name="action" value="filter" />
<?php print $pgv_lang["place_contains"]; ?> <input type="text" name="place" value="<?php print $place;?>" />
<input type="submit" value="<?php print $pgv_lang["filter"];?>" /><br />
</form>
<?php
if ($action=="") {
	$action="filter";
	//$place="";
}
if ($action=="filter") {
	$placelist = array();
	find_place_list($place);
	uasort($placelist, "stringsort");

	if (count($placelist)==0) {
		print "<b>".$pgv_lang["no_results"]."</b><br />";
	}
	else {
		print "\n\t<table class=\"list_table\">\n\t\t<tr>\n\t\t<td class=\"list_value $TEXT_DIRECTION\">";
		foreach($placelist as $indexval => $revplace) {
			$levels = preg_split ("/,/", $revplace);		// -- split the place into comma seperated values
			$levels = array_reverse($levels);				// -- reverse the array so that we get the top level first
			$placetext="";
			$j=0;
			foreach($levels as $indexval => $level) {
				if ($j>0) $placetext .= ", ";
				$placetext .= trim($level);
				$j++;
			}
			print "<a href=\"#\" onclick=\"pasteid('".preg_replace(array("/'/",'/"/'), array("\'",'&quot;'), $placetext)."');\">".PrintReady($revplace)."</a><br />\n";
		}
		print "</td></tr></table>";
	}
}
print "</center>";
print_simple_footer();
?>