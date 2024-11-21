<?php
/**
 * Logged In Users Block
 *
 * This block will print a list of the users who are currently logged in
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
 * @version $Id: logged_in.php,v 1.2 2006/01/09 00:46:22 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */

$PGV_BLOCKS["print_logged_in_users"]["name"]        = $pgv_lang["logged_in_users_block"];
$PGV_BLOCKS["print_logged_in_users"]["descr"]        = $pgv_lang["logged_in_users_descr"];
$PGV_BLOCKS["print_logged_in_users"]["canconfig"]        = false;

/**
 * logged in users
 *
 * prints a list of other users who are logged in
 */
/**
 * logged in users
 *
 * prints a list of other users who are logged in
 */
function print_logged_in_users($block=true, $config="", $side, $index) {
		global $pgv_lang, $PGV_SESSION_TIME, $TEXT_DIRECTION;

		$cusername = getUserName();
		$thisuser = getUser($cusername);
		if (!$thisuser['visibleonline']) return;
		$users = getUsers();
		$loggedusers = array();
		foreach($users as $indexval => $user) {
			if ($user["loggedin"]=="Y") {
				if (time() - $user["sessiontime"] > $PGV_SESSION_TIME) userLogout($user["username"]);
				else if ((userIsAdmin($cusername))||($user['visibleonline'])) $loggedusers[] = $user;
			}
		}
		if (count($loggedusers)<2) return;

		print "<div id=\"logged_in_users\" class=\"block\">\n";
		print "<table class=\"blockheader\" cellspacing=\"0\" cellpadding=\"0\" style=\"direction:ltr;\"><tr>";
		print "<td class=\"blockh1\" >&nbsp;</td>";
		print "<td class=\"blockh2\" ><div class=\"blockhc\">";
		print "<b>".$pgv_lang["users_logged_in"]." &lrm;(".count($loggedusers).")&lrm;</b>";
		print_help_link("index_loggedin_help", "qm");
		print "</div></td>";
		print "<td class=\"blockh3\">&nbsp;</td></tr>\n";
		print "</table>";
		print "<div class=\"blockcontent\">";
		if ($block) print "<div class=\"small_inner_block\">\n";
		uasort($loggedusers, "usersort");
		foreach($loggedusers as $indexval => $user) {
			print "<br />".PrintReady($user["fullname"]);
			if ($TEXT_DIRECTION=="ltr") print " &lrm; - ".$user["username"]."&lrm;<br />";
			else print " &rlm; - ".$user["username"]."&rlm;<br />";
			if ($cusername!=$user["username"]) {
				if ($user["contactmethod"] != "none") print "<a href=\"#\" onclick=\"return message('".$user["username"]."');\">".$pgv_lang["message"]."</a><br />";
				else print "<br /";
			}
		}
		if ($block) print "</div>\n";
		print "</div>"; // blockcontent
		print "</div>"; // block
}

?>