<?php
/**
 * Gedcom News Block
 *
 * This block allows administrators to enter news items for the active gedcom
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
 * @version $Id: gedcom_news.php,v 1.2 2006/01/09 00:46:22 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */

$PGV_BLOCKS["print_gedcom_news"]["name"]        = $pgv_lang["gedcom_news_block"];
$PGV_BLOCKS["print_gedcom_news"]["descr"]        = $pgv_lang["gedcom_news_descr"];
$PGV_BLOCKS["print_gedcom_news"]["type"]        = "gedcom";
$PGV_BLOCKS["print_gedcom_news"]["canconfig"]   = false;

/**
 * Prints a gedcom news/journal
 *
 * @todo Add an allowed HTML translation
 */
function print_gedcom_news($block = true, $config="", $side, $index) {
		global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION, $GEDCOM, $command, $TIME_FORMAT, $VERSION;

		$uname = getUserName();
		$usernews = getUserNews($GEDCOM);
		print "<div id=\"gedcom_news\" class=\"block\">\n";

		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["gedcom_news"]."</b>";
		if (userGedcomAdmin(getUserName())){
				print_help_link("index_gedcom_news_ahelp", "qm_ah");
		}
				else print_help_link("index_gedcom_news_help", "qm");
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>";
		print "</table>";
		print "<div class=\"blockcontent\">";

		if ($block) print "<div class=\"small_inner_block, $TEXT_DIRECTION\">\n";
		if (count($usernews)==0) {
			print $pgv_lang["no_news"];
			print "<br />";
		}
		foreach($usernews as $key=>$news) {
				$day = date("j", $news["date"]);
				$mon = date("M", $news["date"]);
				$year = date("Y", $news["date"]);
				print "<div class=\"person_box\">\n";
				$ct = preg_match("/#(.+)#/", $news["title"], $match);
				if ($ct>0) {
						if (isset($pgv_lang[$match[1]])) $news["title"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["title"]);
				}
				print "<span class=\"news_title\">".PrintReady($news["title"])."</span><br />\n";
				print "<span class=\"news_date\">".get_changed_date("$day $mon $year")." - ".date($TIME_FORMAT, $news["date"])."</span><br /><br />\n";
				$ct = preg_match("/#(.+)#/", $news["text"], $match);
				if ($ct>0) {
						if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
				}
				$ct = preg_match("/#(.+)#/", $news["text"], $match);
				if ($ct>0) {
						if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
						$varname = $match[1];
						if (isset($$varname)) $news["text"] = preg_replace("/$match[0]/", $$varname, $news["text"]);
				}
				$trans = get_html_translation_table(HTML_SPECIALCHARS);
				$trans = array_flip($trans);
				$news["text"] = strtr($news["text"], $trans);
				$news["text"] = nl2br($news["text"]);
				print PrintReady($news["text"])."<br />\n";
				if (userGedcomAdmin($uname)) {
						print "<hr size=\"1\" />";
						print "<a href=\"#\" onclick=\"editnews('$key'); return false;\">".$pgv_lang["edit"]."</a> | ";
						print "<a href=\"index.php?action=deletenews&amp;news_id=$key&amp;command=$command\" onclick=\"return confirm('".$pgv_lang["confirm_news_delete"]."');\">".$pgv_lang["delete"]."</a><br />";
				}
				print "</div>\n";
		}
		if ($block) print "</div>\n";
		if (userGedcomAdmin($uname)) print "<a href=\"#\" onclick=\"addnews('".preg_replace("/'/", "\'", $GEDCOM)."'); return false;\">".$pgv_lang["add_news"]."</a>\n";
		print "</div>\n";
		print "</div>";

}
?>