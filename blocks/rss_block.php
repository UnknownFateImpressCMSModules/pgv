<?php
/**
 * RSS Block
 *
 * This is the RSS block 
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
 * $Id: rss_block.php,v 1.1 2005/10/07 18:08:13 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */
//$PGV_BLOCKS["print_RSS_block"]["name"]        = "RSS Block";
//$PGV_BLOCKS["print_RSS_block"]["descr"]        = "RSS Feed Block";
$PGV_BLOCKS["print_RSS_block"]["name"]        = $pgv_lang["RSS_block"];
$PGV_BLOCKS["print_RSS_block"]["descr"]        = $pgv_lang["rss_descr"];
$PGV_BLOCKS["print_RSS_block"]["type"]        = "gedcom";
$PGV_BLOCKS["print_RSS_block"]["canconfig"]        = false;
/**
 * Print RSS Block
 *
 * Prints a block allowing the user to login to the site directly from the portal
 */
function print_RSS_block($block = true, $config="", $side, $index) {
		global $LANGUAGE, $pgv_lang, $GEDCOM, $GEDCOMS, $command, $PHP_SELF, $QUERY_STRING, $ENABLE_MULTI_LANGUAGE;

		print "<div id=\"login_block\" class=\"block\">\n";
		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>". $pgv_lang["rss_feeds"] . "</b>";
		print_help_link("rss_feed_help", "qm");
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\">";
		print "<div class=\"center\">";
		print "<form method=\"post\" action=\"\" name=\"rssform\">\n";
		//print get_lang_select();
		print "<br />";
		//print "\n\t<select name=\"rssStyle\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=' + document.rssform.lang.value + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";
		print "\n\t<select name=\"rssStyle\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=" . $LANGUAGE . "' + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";
		print "\n\t\t<option value=\"RSS0.91\">RSS 0.91</option>";
		print "\n\t\t<option value=\"RSS1.0\" selected=\"selected\">RSS 1.0</option>";
		print "\n\t\t<option value=\"RSS2.0\">RSS 2.0</option>";
		print "\n\t\t<option value=\"ATOM\">ATOM</option>";
		//print "\n\t\t<option value=\"ATOM0.3\">ATOM 0.3</option>";
		print "\n\t\t<option value=\"HTML\">HTML</option>";
		print "\n\t\t<option value=\"JS\">JavaScript</option>";
		print "\n\t</select>";
		//print "\n\t<select name=\"module\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=' + document.rssform.lang.value + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";
		print "\n\t<select name=\"module\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=" . $LANGUAGE . "' + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";
		print "\n\t\t<option value=\"\">" . $pgv_lang["all"] . "</option>";
		print "\n\t\t<option value=\"today\">" . $pgv_lang["on_this_day"] . " </option>";
		print "\n\t\t<option value=\"upcoming\">" . $pgv_lang["upcoming_events"] . "</option>";
		print "\n\t\t<option value=\"gedcomStats\">" . $pgv_lang["gedcom_stats"] . "</option>";
		print "\n\t\t<option value=\"gedcomNews\">" . $pgv_lang["gedcom_news"] . "</option>";
		print "\n\t\t<option value=\"top10Surnames\">" . $pgv_lang["block_top10"] . "</option>";
		print "\n\t</select>";
		print "<br /><br /><a id=\"rss_button\" href=\"rss.php?lang=" . $LANGUAGE . "\"><img class=\"icon\" src=\"images/xml.gif\" alt=\"RSS\" title=\"RSS\" /></a>";
		print "</form></div>\n";
		print "<div class=\"center\">";
		print "</div>";
		print "</div>";
		print "</div>";
}

/*function get_lang_select() {
	 global $ENABLE_MULTI_LANGUAGE, $pgv_lang, $pgv_language, $flagsfile, $LANGUAGE, $language_settings;
	 global $LANG_FORM_COUNT;
	 global $PHP_SELF, $QUERY_STRING;
	 $ret="";
	 if ($ENABLE_MULTI_LANGUAGE) {
		if (empty($LANG_FORM_COUNT)) $LANG_FORM_COUNT=1;
		else $LANG_FORM_COUNT++;

		//$ret .= "<select name=\"lang\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=' + document.rssform.lang.value + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";
		$ret .= "<select name=\"lang\" class=\"header_select\" onchange=\"javascript:document.getElementById('rss_button').href = 'rss.php?lang=" . $LANGUAGE . "' + (document.rssform.module.value==''? '' : '&module=' + document.rssform.module.value) + (document.rssform.rssStyle.value==''? '' : '&rssStyle=' + document.rssform.rssStyle.value);\">";

		foreach ($pgv_language as $key=>$value) {
			if ($language_settings[$key]["pgv_lang_use"]) {
				$ret .= "\n\t\t\t<option value=\"$key\" ";
				if ($LANGUAGE == $key) {
					$ret .=  "selected=\"selected\"";
				}
				$ret .=  ">".$pgv_lang[$key]."</option>";
			}
		}
		$ret .=  "</select>\n\n";
	 }
	 return $ret;
}*/
?>