<?php
/**
 * Top 10 Surnames Block for Xoops
 *
 * This block will show the top 10 surnames that occur most frequently in the active gedcom
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
 * @version $Id: pgv_top10_surnames.php,v 1.2 2006/01/09 00:46:22 skenow Exp $
 * @package PhpGedView
 * @subpackage Blocks
 */
$PGV_BLOCKS["print_block_name_top10"]["name"]       = $pgv_lang["block_top10"];
$PGV_BLOCKS["print_block_name_top10"]["descr"]      = $pgv_lang["block_top10_descr"];
$PGV_BLOCKS["print_block_name_top10"]["canconfig"]  = true;
$PGV_BLOCKS["print_block_name_top10"]["config"]     = array("num"=>10);

function b_pgv_top10_show ($block=true, $config="", $side, $index) {
	global $pgv_lang, $GEDCOM, $DEBUG, $TEXT_DIRECTION;
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $PGV_BLOCKS, $command, $PGV_IMAGES, $PGV_IMAGE_DIR;

	function top_surname_sort($a, $b) {
		return $b["match"] - $a["match"];
	}

	if (empty($config)) $config = $PGV_BLOCKS["print_block_name_top10"]["config"];

	//-- cache the result in the session so that subsequent calls do not have to
	//-- perform the calculation all over again.
	if (isset($_SESSION["top10"][$GEDCOM])&&(!isset($DEBUG)||($DEBUG==false))) {
		$surnames = $_SESSION["top10"][$GEDCOM];
	}
	else {
		$surnames = get_top_surnames($config["num"]);

// Insert from the "Add Names" list if not already in there
		if ($COMMON_NAMES_ADD != "") {
			$addnames = preg_split("/[,;] /", $COMMON_NAMES_ADD);
			if (count($addnames)==0) $addnames[] = $COMMON_NAMES_ADD;
			foreach($addnames as $indexval => $name) {
				$surname = str2upper($name);
				if (!isset($surnames[$surname])) {
					$surnames[$surname]["name"] = $name;
					$surnames[$surname]["match"] = $COMMON_NAMES_THRESHOLD;
				}
			}
		}

// Remove names found in the "Remove Names" list
		if ($COMMON_NAMES_REMOVE != "") {
			$delnames = preg_split("/[,;] /", $COMMON_NAMES_REMOVE);
			if (count($delnames)==0) $delnames[] = $COMMON_NAMES_REMOVE;
			foreach($delnames as $indexval => $name) {
				$surname = str2upper($name);
				unset($surnames[$surname]);
			}
		}

// Sort the list and save for future reference
		uasort($surnames, "top_surname_sort");
		$_SESSION["top10"][$GEDCOM] = $surnames;
	}
	return $surnames;

}
 ?>