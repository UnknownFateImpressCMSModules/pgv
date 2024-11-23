<?php
/**
 * Popup window that will allow a user to search for a id
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
 * @version $Id: findid.php,v 1.40 2005/09/15 17:33:44 yalnifj Exp $
 */

require("config.php");
if (!isset($action)) $action="";
if (!isset($name_filter)) $name_filter="";
if (!isset($callback)) $callback = "paste_id";

print_simple_header($pgv_lang["find_individual"]);
if (isset($name_filter)) $name_filter = stripslashes($name_filter);

?>
<script language="JavaScript" type="text/javascript">
	function paste_id(id) {
		window.opener.<?php print $callback; ?>(id);
		window.close();
	}
</script>
<div class ="center">
<span class="subheaders"><?php print $pgv_lang["find_individual"]; ?></span>
<form name="filter" method="post">
<input type="hidden" name="action" value="filter" />
<input type="hidden" name="callback" value="<?php print $callback; ?>" />
<?php print $pgv_lang["name_contains"]; ?> <input type="text" name="name_filter" value="<?php print $name_filter;?>" />
<input type="submit" value="<?php print $pgv_lang["filter"];?>" /><br />
</form>
<?php
if ($action=="filter") {
	if (!isset($name_filter)) $name_filter="";
	$names = preg_split("/[\s,]+/", $name_filter);
	$num_names = count($names);
	if (!$REGEXP_DB) {
		for($i=0; $i<count($names); $i++){
			$names[$i] = "%".$names[$i]."%";
		}
	}
	if (!empty($name_filter)) $sindilist = search_indis_names($names);
	else $sindilist = get_indi_list();
	if (count($sindilist)==0) {
		print "<b>".$pgv_lang["no_results"]."</b><br />";
	}
	print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>\n\t\t<td class=\"list_value\">";
	uasort($sindilist, "itemsort");
	reset($sindilist);
	foreach($sindilist as $key=>$value) {
		$disp = displayDetailsById($key, "INDI");
		if ($disp || showLivingNameById($key)) {
 		    print "\n\t\t\t<a href=\"#\" onclick=\"paste_id('$key');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">".PrintReady(check_NN(get_sortable_name($key)));
			if (!$disp) {
				print "--<i>".$pgv_lang["private"]."</i>";
			}
			else {
				print_first_major_fact($key);
			}
		    print "</span></a><br />\n";
	    }
	}
	print "</td></tr></table>";
}
print "</div>";
?>
<script language="JavaScript" type="text/javascript">
document.filter.name_filter.focus();
</script>
<?php
print_simple_footer();
?>