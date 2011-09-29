<?php
/**
 * Import Gedcom File
 *
 * Parse a gedcom file into the datastore
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
 * $Id: importgedcom.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 * @package PhpGedView
 * @subpackage Admin
 */

//-- set the building index flag to tell the rest of the program that we are importing and so shouldn't
//-- perform some of the same checks
$BUILDING_INDEX = true;

require("config.php");

/**
 * function that sets up the html required to run the progress bar
 * @param long $FILE_SIZE	the size of the file
 */
function setup_progress_bar($FILE_SIZE) {
	global $pgv_lang, $ged, $TIME_LIMIT;
	?>
<script type="text/javascript">
<!--
function complete_progress(time, exectext, go_pedi, go_welc) {
	progress = document.getElementById("progress_header");
	progress.innerHTML = '<?php print "<span class=\"error\"><b>".$pgv_lang["import_complete"]."</b></span><br />";?>'+exectext+' '+time+' '+"<?php print $pgv_lang["sec"]; ?>";
	progress = document.getElementById("link1");
	progress.innerHTML = '<a href="pedigree.php?ged=<?php print preg_replace("/'/", "\'", $ged); ?>">'+go_pedi+'</a>';
	progress = document.getElementById("link2");
	progress.innerHTML = '<a href="index.php?command=gedcom&ged=<?php print preg_replace("/'/", "\'", $ged); ?>">'+go_welc+'</a>';
	progress = document.getElementById("link3");
	progress.innerHTML = '<a href="editgedcoms.php">'+"<?php print $pgv_lang["manage_gedcoms"]."</a>"; ?>";
}
function wait_progress() {
	progress = document.getElementById("progress_header");
	progress.innerHTML = '<?php print $pgv_lang["please_be_patient"]; ?>';
}

	var FILE_SIZE = <?php print $FILE_SIZE; ?>;
	var TIME_LIMIT = <?php print $TIME_LIMIT; ?>;
	function update_progress(bytes, time) {
		perc = Math.round(100*(bytes / FILE_SIZE));
		if (perc>100) perc = 100;
		progress = document.getElementById("progress_div");
		progress.style.width = perc+"%";
		progress.innerHTML = perc+"%";

		perc = Math.round(100*(time / TIME_LIMIT));
		if (perc>100) perc = 100;
		progress = document.getElementById("time_div");
		progress.style.width = perc+"%";
		progress.innerHTML = perc+"%";
	}
	//-->
	</script>
<?php
	print "<table style=\"width: 800px;\"><tr><td>";
	print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
	print "<b>".$pgv_lang["import_progress"]."</b>";
	print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
	print "<div id=\"progress_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
	print "</div>\n";
	print "</div>\n";
	print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
	print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
	print "</td></tr></table>";
	print "<table style=\"width: 800px;\"><tr><td>";
	print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
	print "<b>".$pgv_lang["time_limit"]." $TIME_LIMIT</b>";
	print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
	print "<div id=\"time_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
	print "</div>\n";
	print "</div>\n";
	print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
	print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
	print "</td></tr></table>";
	flush();
}
//-- end of setup_progress_bar function

if (!isset($stage)) $stage = 0;
if ((empty($ged))||(!isset($GEDCOMS[$ged]))) $ged = $GEDCOM;

$temp = $THEME_DIR;
$GEDCOM_FILE = $GEDCOMS[$ged]["path"];
$FILE = $ged;
$TITLE = $GEDCOMS[$ged]["title"];
require($GEDCOMS[$ged]["config"]);
if ($LANGUAGE <> $_SESSION["CLANGUAGE"]) $LANGUAGE = $_SESSION["CLANGUAGE"];

if (!userGedcomAdmin(getUserName())) {
	print_header($pgv_lang["building_indi"]);
	print $pgv_lang["access_denied"];
	print_footer();
	exit;
}

$temp2 = $THEME_DIR;
$THEME_DIR = $temp;
print_header($pgv_lang["building_indi"]);
$THEME_DIR = $temp2;
if (isset($GEDCOM_FILE)) {
	if ((!strstr($GEDCOM_FILE, "://"))&&(!file_exists($GEDCOM_FILE))) {
		print "<span class=\"error\"><b>Could not locate gedcom file at $GEDCOM_FILE<br /></b></span>\n";
		unset($GEDCOM_FILE);
	}
}

setup_database($stage);
if ($stage==0) {
	print "<span class=\"subheaders\">".$pgv_lang["step4"]." ".$pgv_lang["importing_records"]."</span><br /><br />";
	//-- check if this GEDCOM is already being used
	$dataset_exists = check_for_import($FILE);
	if (($dataset_exists)&&(!isset($erase_dataset))) {
	?>
		<br /><br />
		<form action="importgedcom.php" method="post">
			<input type="hidden" name="ged" value="<?php print $ged;?>" />
			<input type="hidden" name="erase_dataset" value="Yes" />
			<span class="error"><?php print $pgv_lang["dataset_exists"]?> <i><?php print $FILE; ?></i>.<br /><br /><?php
			foreach($pgv_changes as $cid=>$changes) {
				if ($changes[0]["gedcom"]==$ged) {
					print $pgv_lang["changes_present"]."<br /><br />";
					break;
				}
			}?></span>
			<?php print $pgv_lang["empty_dataset"]?> <br />
			<input type="submit" class="button" value="<?php print $pgv_lang["yes"];?>" /> <input type="button" class="button" value="<?php print $pgv_lang["no"];?>" onclick="window.location='editgedcoms.php';" /><br /><br />
		</form>
	<?php
	cleanup_database();
	print_footer();
	exit;
	}

	if (!isset($erase_dataset)) $erase_dataset="No";
	//-- clear all of the things from the old dataset
	if ($erase_dataset=="Yes") {
		empty_database($FILE);
		//-- erase any of the changes
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) unset($pgv_changes[$cid]);
		}
		write_changes();
	}
	$stage=1;
}

$GEDCOM=$GEDCOM_FILE;
flush();

if (isset($exectime)){
	$oldtime=time()-$exectime;
	$skip_table=0;
}
else $oldtime=time();

if ($stage==1) {
	//print "<span class=\"subheaders\">".$pgv_lang["step4"]." ".$pgv_lang["importing_records"]."</span><br /><br />";
	$FILE_SIZE = filesize($GEDCOM);
	setup_progress_bar($FILE_SIZE);
	// ------------------------------------------------------ Begin importing data
	// -- array of names
	if (!isset($indilist)) $indilist = array();
	if (!isset($famlist)) $famlist = array();
	$sourcelist = array();
	$otherlist = array();
	if (!isset($record_count)) $record_count=0;
	$i=$record_count;

	$fpged = fopen($GEDCOM, "r");
	$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
	if (!isset($fcontents)) $fcontents = "";
	if (!isset($TOTAL_BYTES)) $TOTAL_BYTES = 0;
	fseek($fpged, $TOTAL_BYTES);
	while(!feof($fpged)) {
		$fcontents .= fread($fpged, $BLOCK_SIZE);
		$TOTAL_BYTES += $BLOCK_SIZE;
		$pos1 = 0;
		$listtype= array();
		while($pos1!==false) {
			$pos2 = strpos($fcontents, "\n0", $pos1+1);
			while((!$pos2)&&(!feof($fpged))) {
				$fcontents .= fread($fpged, $BLOCK_SIZE);
				$TOTAL_BYTES += $BLOCK_SIZE;
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
			}
			if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
			else $indirec = substr($fcontents, $pos1);
			$indirec = preg_replace("/\\\/", "/", $indirec);
			if (preg_match("/1 BLOB/", $indirec)==0) import_record(trim($indirec));
			$pos1 = $pos2;
			if (!isset($show_type)){
				$show_type=$type;
				$i_start=1;
				$exectime_start=0;
				$type_BYTES=0;
			}
			$i++;
			if ($show_type!=$type) {
				$newtime = time();
				$exectime = $newtime - $oldtime;
				$show_exectime = $exectime - $exectime_start;
				$show_i=$i-$i_start;
				$type_BYTES=$TOTAL_BYTES-$type_BYTES;
				if (!isset($listtype[$show_type]["type"])) {
					$listtype[$show_type]["exectime"]=$show_exectime;
					$listtype[$show_type]["bytes"]=$type_BYTES;
					$listtype[$show_type]["i"]=$show_i;
					$listtype[$show_type]["type"]=$show_type;
				}
				else {
					$listtype[$show_type]["exectime"]+=$show_exectime;
					$listtype[$show_type]["bytes"]+=$type_BYTES;
					$listtype[$show_type]["i"]+=$show_i;
				}
				$show_type=$type;
				$i_start=$i;
				$exectime_start=$exectime;
				$type_BYTES=$TOTAL_BYTES;
			}
			if ($i%100==0) {
				$newtime = time();
				$exectime = $newtime - $oldtime;
				print "\n<script type=\"text/javascript\">update_progress($TOTAL_BYTES, $exectime);</script>\n";
				flush();
			}
			else print " ";
			$show_gid=$gid;
		}
		$show_table1 = "<table class=\"list_table\"><tr>";
		$show_table1 .= "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
		$show_table1 .= "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["bytes_read"]."&nbsp;</td>\n";
		$show_table1 .= "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
		$show_table1 .= "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
		foreach($listtype as $indexval => $type) {
			$show_table1 .= "<tr><td class=\"list_value indent_rtl\" style=\"text-align: right;\">".$type["exectime"]." ".$pgv_lang["sec"]."</td>";
			$show_table1 .= "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">".($type["bytes"]=="0"?"++":$type["bytes"])."</td>\n";
			$show_table1 .= "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">".$type["i"]."</td>";
			$show_table1 .= "<td class=\"list_value\">&nbsp;".$type["type"]."&nbsp;</td></tr>\n";
		}
		$newtime = time();
		$exectime = $newtime - $oldtime;
		$show_table1 .= "<tr><td class=\"list_label indent_rtl\" style=\"text-align: right;\">$exectime ".$pgv_lang["sec"]."</td>";
		$show_table1 .= "<td class=\"list_label indent_rtl\" style=\"text-align: right;\">$TOTAL_BYTES<script type=\"text/javascript\">update_progress($TOTAL_BYTES, $exectime);</script></td>\n";
		$show_table1 .= "<td class=\"list_label indent_rtl\" style=\"text-align: right;\">".($i-1)."</td>";
		$show_table1 .= "<td class=\"list_label\">&nbsp;</td></tr>\n";
		$fcontents = substr($fcontents, $pos2);
	}
	fclose($fpged);
	$show_table1 .= "</table>\n";
	print "<table cellspacing=\"20px\"><tr><td style=\"vertical-align: top; white-space: nowrap;\">";
	print "<b>".$pgv_lang["reading_file"]." ".$GEDCOM_FILE."</b>";
	if (isset($skip_table)) print "<br />...";
	else print $show_table1;
	print "</td>\n";
	if ($PGV_DATABASE=="index") $stage=2;
	else $stage=4;
	$record_count=0;
}
if ($stage==2) {
	//-- clear the place cache in mysql mode
	if (isset($placecache)) unset($placecache);
	?>

	<script type="text/javascript">
	<!--
	var FILE_SIZE = <?php print count($indilist); ?>;
	//-->
	</script>

	<?php
	$i=0;
	$exectime_start = $exectime;
	print "<td style=\"vertical-align: top; white-space: nowrap;\"><b>".$pgv_lang["updating_is_dead"]."</b><br />\n";
	print "<table class=\"list_table\"><tr>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
	foreach($indilist as $gid=>$indi) {
		if ($i>=$record_count) {
			update_isdead($gid, $indi);
			$i++;
		}
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
	}
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$show_exectime = $exectime - $exectime_start;
	print "<tr><td class=\"list_value indent_rtl\" style=\"text-align: right;\">$show_exectime ".$pgv_lang["sec"]."</td>\n";
	print "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">$i<script type=\"text/javascript\">update_progress($i, $exectime);</script></td>";
	print "<td class=\"list_value\">&nbsp;INDI&nbsp;</td></tr>\n";
	flush();
	print "</table>\n";
	$stage=3;
	$record_count=0;
}
if ($stage==3) {
	?>

	<script type="text/javascript">
	<!--
	var FILE_SIZE = <?php print count($famlist); ?>;
	//-->
	</script>

	<?php
	$i=0;
	$exectime_start = $exectime;
	print "<br /><br /><b>".$pgv_lang["updating_family_names"]."</b><br />\n";
	print "<table class=\"list_table\"><tr>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>\n";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
	foreach($famlist as $gid=>$fam) {
		if ($i>=$record_count) {
			update_family_name($gid, $fam);
			$i++;
		}
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
	}
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$show_exectime = $exectime - $exectime_start;
	print "<tr><td class=\"list_value indent_rtl\" style=\"text-align: right;\">$show_exectime ".$pgv_lang["sec"]."</td>\n";
	print "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">$i<script type=\"text/javascript\">update_progress($i, $exectime);</script></td>";
	print "<td class=\"list_value\">&nbsp;FAM&nbsp;</td></tr>\n";
	print "</table></td>\n";
	print "<script type=\"text/javascript\">wait_progress();</script>";
	flush();
	if ($PGV_DATABASE=="index") $stage=6;
	else $stage=4;
	$record_count=0;
}

if ($stage==4) {
	cleanup_database();
	print "</tr></table>\n";
	?>
	<br /><br /><form method="post" name="marriedform" action="importgedcom.php">
	<input type="hidden" name="ged" value="<?php print $ged; ?>" />
	<input type="hidden" name="stage" value="5" />
	<input type="submit" value="<?php print $pgv_lang["continue_import"]; ?>" />
	</form>
	<?php
	print_footer();
	exit;
}

//-- extract all places
if ($stage==5) {
	$GEDCOM = $FILE;

	$total_count = get_list_size('indilist')+get_list_size('famlist')+get_list_size('sourcelist')+get_list_size('otherlist');
	//print $total_count;
	print "<span class=\"subheaders\">".$pgv_lang["step4"]." ".$pgv_lang["importing_places"]."</span><br /><br />";
	setup_progress_bar($total_count);

	$i=0;
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$exectime_start = $exectime;
	$place_count = 0;
	get_indi_list();
	foreach($indilist as $gid=>$indi) {
		$place_count += update_places($gid, $indi["gedcom"]);
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
		++$i;
	}
	//if ($PGV_DATABASE!="index") unset($indilist);
	//print_execution_stats();
	get_fam_list();
	foreach($famlist as $gid=>$indi) {
		$place_count += update_places($gid, $indi["gedcom"]);
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
		++$i;
	}
	//if ($PGV_DATABASE!="index") unset($famlist);
	//print_execution_stats();
	get_source_list();
	foreach($sourcelist as $gid=>$indi) {
		$place_count += update_places($gid, $indi["gedcom"]);
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
		++$i;
	}
	//if ($PGV_DATABASE!="index") unset($sourcelist);
	//print_execution_stats();
	get_other_list();
	foreach($otherlist as $gid=>$indi) {
		$place_count += update_places($gid, $indi["gedcom"]);
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
		++$i;
	}
	//print_execution_stats();
	print "<table class=\"list_table\"><tr>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$show_exectime = $exectime - $exectime_start;
	print "<tr><td class=\"list_value indent_rtl\" style=\"text-align: right;\">$show_exectime ".$pgv_lang["sec"]."</td>\n";
	print "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">$place_count<script type=\"text/javascript\">update_progress($i, $exectime);</script></td>";
	print "<td class=\"list_value\">&nbsp;PLAC&nbsp;</td></tr>\n";
	flush();
	print "</table>\n";
	$stage=6;
	$record_count=0;
}
if ($stage==6) {
	?>
	<br /><br /><form method="post" name="marriedform" action="importgedcom.php">
	<input type="hidden" name="ged" value="<?php print $ged; ?>" />
	<input type="hidden" name="stage" value="7" />
	<?php print $pgv_lang["marr_name_import_instr"]; ?><br />
	<input type="submit" value="<?php print $pgv_lang["import_marr_names"]; ?>" />
	<?php print_help_link("import_marr_names_help", "qm"); ?>
	</form>
	<?php
	cleanup_database();
	$exec_text = $pgv_lang["exec_time"];
	$go_pedi = $pgv_lang["click_here_to_go_to_pedigree_tree"];
	$go_welc = $pgv_lang["welcome_page"];
	if ($LANGUAGE=="french" || $LANGUAGE=="italian"){
		print "<script type=\"text/javascript\">complete_progress($exectime, \"$exec_text\", \"$go_pedi\", \"$go_welc\");</script>";
	}
	else print "<script type=\"text/javascript\">complete_progress($exectime, '$exec_text', '$go_pedi', '$go_welc');</script>";
	flush();
	print_footer();
	exit;
}
//-- calculate married names
if ($stage==7) {
	$GEDCOM = $FILE;
	get_indi_list();
	get_fam_list();
	print "<span class=\"subheaders\">".$pgv_lang["step4"]." ".$pgv_lang["calc_marr_names"]."</span><br /><br />";
	setup_progress_bar(count($indilist));

	$i=0;
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$exectime_start = $exectime;
	$names_added = 0;
	include_once("includes/functions_edit.php");
	$manual_save = true;
	foreach($indilist as $gid=>$indi) {
		if (preg_match("/1 SEX F/", $indi["gedcom"])>0) {
			$ct = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
			if ($ct>0){
				for($j=0; $j<$ct; $j++) {
					if (isset($famlist[$match[$j][1]])) {
						$marrrec = get_sub_record(1, "1 MARR", $famlist[$match[$j][1]]["gedcom"]);
						if ($marrrec) {
							$parents = find_parents_in_record($famlist[$match[$j][1]]["gedcom"]);
							if ($parents["HUSB"]!=$gid) $spid = $parents["HUSB"];
							else $spid = $parents["WIFE"];
							if (isset($indilist[$spid])) {
								$surname = $indilist[$spid]["names"][0][2];
								$letter = $indilist[$spid]["names"][0][1];
								//-- uncomment the next line to put the maiden name in the given name area
								//$newname = preg_replace("~/(.*)/~", " $1 /".$surname."/", $indi["names"][0][0]);
								$newname = preg_replace("~/(.*)/~", "/".$surname."/", $indi["names"][0][0]);
								if (strpos($indi["gedcom"], "_MARNM $newname")===false) {
									$pos1 = strpos($indi["gedcom"], "1 NAME");
									if ($pos1!==false) {
										$pos1 = strpos($indi["gedcom"], "\n1", $pos1+1);
										if ($pos1!==false) $indi["gedcom"] = substr($indi["gedcom"], 0, $pos1)."\n2 _MARNM $newname\r\n".substr($indi["gedcom"], $pos1+1);
										else $indi["gedcom"]= trim($indi["gedcom"])."\r\n2 _MARNM $newname\r\n";
										$indi["gedcom"] = check_gedcom($indi["gedcom"], false);
										$pos1 = strpos($fcontents, "0 @$gid@");
										$pos2 = strpos($fcontents, "0 @", $pos1+1);
										if ($pos2===false) $pos2=strlen($fcontents);
										$fcontents = substr($fcontents, 0,$pos1).trim($indi["gedcom"])."\r\n".substr($fcontents, $pos2);
										add_new_name($gid, $newname, $letter, $surname, $indi["gedcom"]);
										$names_added++;
									}
								}
							}
						}
					}
				}
			}
		}
		$i++;
		if ($i%100==0) {
			$newtime = time();
			$exectime = $newtime - $oldtime;
			print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
			flush();
		}
	}
	write_file();
	print "<table class=\"list_table\"><tr>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
	print "<td class=\"list_label\" style=\"vertical-align: top\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$show_exectime = $exectime - $exectime_start;
	print "<tr><td class=\"list_value indent_rtl\" style=\"text-align: right;\">$show_exectime ".$pgv_lang["sec"]."</td>\n";
	print "<td class=\"list_value indent_rtl\" style=\"text-align: right;\">$names_added<script type=\"text/javascript\">update_progress($i, $exectime);</script></td>";
	print "<td class=\"list_value\">&nbsp;INDI&nbsp;</td></tr>\n";
	flush();
	print "</table>\n";
	$stage=8;
	$record_count=0;
}
if ($stage=="8") {
	cleanup_database();
	$exec_text = $pgv_lang["exec_time"];
	$go_pedi = $pgv_lang["click_here_to_go_to_pedigree_tree"];
	$go_welc = $pgv_lang["welcome_page"];
	if ($LANGUAGE=="french" || $LANGUAGE=="italian"){
		print "<script type=\"text/javascript\">complete_progress($exectime, \"$exec_text\", \"$go_pedi\", \"$go_welc\");</script>";
	}
	else print "<script type=\"text/javascript\">complete_progress($exectime, '$exec_text', '$go_pedi', '$go_welc');</script>";
	flush();
	print_footer();
}
?>