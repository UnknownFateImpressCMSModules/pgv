<?php
/**
 * Popup window that will allow a user to find special characters
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: findspecialchar.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 * @author Matthew O'Malley
 */

require("config.php");
if (!isset($action)) $action="";
if (!isset($language_filter)) $language_filter="";
if (empty($language_filter)) {
	if (!empty($_SESSION["language_filter"])) $language_filter = $_SESSION["language_filter"];
	else $language_filter=$lang_short_cut[$LANGUAGE];
}
if (!isset($magnify)) $magnify=false;

$_SESSION["language_filter"] = $language_filter;

print_simple_header($pgv_lang["find_specialchar"]); // change for languages

require("includes/specialchars.php");
?>
<script language="JavaScript" type="text/javascript">
<!--
	var language_filter;
	function paste_char(selected_char,language_filter,magnify) {
		window.opener.paste_char(selected_char,language_filter,magnify);
		//window.close();
		return false;
	}
	function setMagnify() {
		document.filter.magnify.value = '<?PHP print !$magnify; ?>';
		document.filter.submit();
	}
-->
</script>
<style type="text/css">
<!--
.largechars {
	font-size: 18px;
}
-->
</style>
<div class ="center">
<span class="subheaders"><?php print $pgv_lang["find_specialchar"]; // change for languages 
?></span>

<form name="filter" method="post">
<input type="hidden" name="action" value="filter" />
<input type="hidden" name="magnify" value="<?php print $magnify; ?>" />
<select id="language_filter" name="language_filter" onchange="submit();">
<?PHP
print "\n\t<option value=\"\">".$pgv_lang["change_lang"]."</option>";
$language_options = "";
foreach($specialchar_languages as $key=>$value) {
	$language_options.= "\n\t<option value=\"$key\">$value</option>";
}
$language_options = str_replace("\"$language_filter\"","\"$language_filter\" selected",$language_options);
print $language_options;
?>
	</select><br><a href="#" onclick="setMagnify()"><?PHP print $pgv_lang["magnify"]; ?></a>
<?php print"&nbsp;|&nbsp;<a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a>" ?>
</form>


<?php
print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>\n\t\t<td class=\"list_value\" width=\"50\">";
//upper case special characters
foreach($ucspecialchars as $key=>$value) {
	$value = str_replace("'","\'",$value);
	print "\n\t\t\t<a href=\"#\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
	if ($magnify) print "<span class=\"largechars\">";
	print $key;
	if ($magnify) print "</span>";
	print "</span></a><br />";
}
print "</td>\n\t\t<td class=\"list_value\" width=\"50\">";
// lower case special characters
foreach($lcspecialchars as $key=>$value) {
	$value = str_replace("'","\'",$value);
	print "\n\t\t\t<a href=\"#\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
	if ($magnify) print "<span class=\"largechars\">";
	print $key;
	if ($magnify) print "</span>";
	print "</span></a><br />\n";
}
print "</td>\n\t\t<td class=\"list_value\" width=\"50\">";
// other special characters (not letters)
foreach($otherspecialchars as $key=>$value) {
	$value = str_replace("'","\'",$value);
	print "\n\t\t\t<a href=\"#\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
	if ($magnify) print "<span class=\"largechars\">";
	print $key;
	if ($magnify) print "</span>";
	print "</span></a><br />\n";
}
print "\n\t\t</td>\n\t\t</tr>\n\t</table>";
print "<br /><br /><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br />\n";
print "</div>";

print_simple_footer();
?>