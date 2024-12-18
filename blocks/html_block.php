<?php

/**

 * Simple HTML Block
 *
 * This block will print simple HTML text entered by an admin
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
 * @version $Id: html_block.php,v 1.2 2006/01/09 00:46:22 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */

$PGV_BLOCKS["print_html_block"]["name"]        = $pgv_lang["html_block_name"];
$PGV_BLOCKS["print_html_block"]["descr"]       = $pgv_lang["html_block_descr"];
$PGV_BLOCKS["print_html_block"]["canconfig"]   = true;
$PGV_BLOCKS["print_html_block"]["config"]      = array("html"=>"<p class=\"blockhc\"><b>Put your title here</b></p>\r\n<p>Click the configure button <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"]."\" alt=\"".$pgv_lang["config_block"]."\" /> to change what is printed here.</p>");

function print_html_block($block=true, $config="", $side, $index) {
	global $pgv_lang, $PGV_IMAGE_DIR, $TEXT_DIRECTION, $PGV_IMAGES, $HTML_BLOCK_COUNT, $PGV_BLOCKS, $command, $GEDCOM;

	if (empty($config)) $config = $PGV_BLOCKS["print_html_block"]["config"];
	if (!isset($HTML_BLOCK_COUNT)) $HTML_BLOCK_COUNT = 0;
	$HTML_BLOCK_COUNT++;
	print "<div id=\"html_block$HTML_BLOCK_COUNT\" class=\"block\">\n";
	print "<div class=\"blockcontent\">";
	if ($block) print "<div class=\"small_inner_block\">\n";

	$ct = preg_match("/#(.+)#/", $config["html"], $match);
	if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $config["html"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $config["html"]);
	}
	$ct = preg_match("/#(.+)#/", $config["html"], $match);
	if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $config["html"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $config["html"]);
			$varname = $match[1];
			if (!empty($$varname)) {
				$value = $$varname;
				$config["html"] = preg_replace("/$match[0]/", $value, $config["html"]);
			}
	}
	print stripslashes($config["html"]);

	if ($block) print "</div>\n";
	if ($PGV_BLOCKS["print_html_block"]["canconfig"]) {
		$username = getUserName();
		if ((($command=="gedcom")&&(userGedcomAdmin($username))) || (($command=="user")&&(!empty($username)))) {
			if ($command=="gedcom") $name = preg_replace("/'/", "\'", $GEDCOM);
			else $name = $username;
			print "<br /><a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?name=$name&amp;command=$command&amp;action=configure&amp;side=$side&amp;index=$index', '', 'top=50,left=50,width=600,height=550,scrollbars=1,resizable=1'); return false;\">";
			print "<img class=\"adminicon\" src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$pgv_lang["config_block"]."\" /></a>\n";
		}
	}
	print "</div>"; // blockcontent
	print "</div>"; // block
}

function print_html_block_config($config) {
	global $pgv_lang, $PGV_BLOCKS;
	if (empty($config)) $config = $PGV_BLOCKS["print_html_block"]["config"];
	?>
	<textarea name="html" rows="8" cols="70"><?php print $config["html"]; ?></textarea>
	<?php
}
?>