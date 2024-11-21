<?php
/***********************************************************
	File: editnews.php
	Project: phpGedView
	Author: John Finlay
	Posts to: none
	Comments:
		Send a message to a user in the system

	Change Log:
		11/06/32 - File Created (yalnifj)

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
# $Id: editnews.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
require("config.php");

$username = getUserName();
if (empty($username)) {
	print_simple_header("");
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

if (!isset($action)) $action="compose";

print_simple_header($pgv_lang["edit_news"]);

if (empty($uname)) $uname=$GEDCOM;

if ($action=="compose") {
	print '<span class="subheaders">'.$pgv_lang["edit_news"].'</span>';
	?>
	<script language="JavaScript" type="text/javascript">
		function checkForm(frm) {
			if (frm.title.value=="") {
				alert('<?php print $pgv_lang["enter_title"]; ?>');
				document.messageform.title.focus();
				return false;
			}
			if (frm.text.value=="") {
				alert('<?php print $pgv_lang["enter_text"]; ?>');
				document.messageform.text.focus();
				return false;
			}
			return true;
		}
	</script>
	<?php
	print "<br /><form name=\"messageform\" method=\"post\" onsubmit=\"return checkForm(this);";
	print "\">\n";
	if (isset($news_id)) {
		$news = getNewsItem($news_id);
	}
	else {
		$news_id="";
		$news = array();
		$news["username"] = $uname;
		$news["date"] = time()-$_SESSION["timediff"];
		$news["title"] = "";
		$news["text"] = "";
	}
	print "<input type=\"hidden\" name=\"action\" value=\"save\" />\n";
	print "<input type=\"hidden\" name=\"uname\" value=\"".$news["username"]."\" />\n";
	print "<input type=\"hidden\" name=\"news_id\" value=\"$news_id\" />\n";
	print "<input type=\"hidden\" name=\"date\" value=\"".$news["date"]."\" />\n";
	print "<table>\n";
	print "<tr><td align=\"right\">".$pgv_lang["title"]."</td><td><input type=\"text\" name=\"title\" size=\"50\" value=\"".$news["title"]."\" /><br /></td></tr>\n";
	print "<tr><td valign=\"top\" align=\"right\">".$pgv_lang["article_text"]."<br /></td><td><textarea name=\"text\" cols=\"50\" rows=\"7\">".$news["text"]."</textarea><br /></td></tr>\n";
	print "<tr><td></td><td><input type=\"submit\" value=\"".$pgv_lang["save"]."\" /></td></tr>\n";
	print "</table>\n";
	print "</form>\n";
}
else if ($action=="save") {
	$date=time()-$_SESSION["timediff"];
	if (empty($title)) $title="No Title";
	if (empty($text)) $text="No Text";
	$message = array();
	if (!empty($news_id)) $message["id"]=$news_id;
	$message["username"] = $uname;
	$message["date"]=$date;
	$message["title"] = $title;
	$message["text"] = $text;
	if (addNews($message)) {
		if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);
		print $pgv_lang["news_saved"];
	}
}
else if ($action=="delete") {
	if (deleteNews($news_id)) print $pgv_lang["news_deleted"];
}
print "<center><br /><br /><a href=\"#\" onclick=\"if (window.opener.refreshpage) window.opener.refreshpage(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>";

print_simple_footer();
?>