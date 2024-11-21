<?php
/**
 * Displays the details about a source record.  Also shows how many people and families
 * reference this source.
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
 * @subpackage Lists
 * @version $Id: source.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

if (empty($action)) $action="";
if (empty($show_changes)) $show_changes = "yes";
if (empty($sid)) $sid = " ";
$sid = clean_input($sid);

global $PGV_IMAGES;

$display = displayDetailsByID($sid, "SOUR");
if (!$display) {
	print_header($pgv_lang["private"]." ".$pgv_lang["source_info"]);
	print_privacy_error($CONTACT_EMAIL);
	print_footer();
	exit;
}

$accept_success=false;
if (userCanAccept(getUserName())) {
	if ($action=="accept") {
		if (accept_changes($sid."_".$GEDCOM)) {
			$show_changes="no";
			$accept_success=true;
		}
	}
}

$source = find_source_record($sid);
//-- make sure we have the true id from the record
$ct = preg_match("/0 @(.*)@/", $source, $match);
if ($ct>0) $sid = trim($match[1]);

$nonfacts = array();

$name = get_source_descriptor($sid);

//-- MA Print additional source title
$add_descriptor = get_add_source_descriptor($sid);
if ($add_descriptor) $name .= " - ".$add_descriptor;

print_header("$name - $sid - ".$pgv_lang["source_info"]);

//print_help_link("sources_help", "page_help");
?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record() {
		var recwin = window.open("gedrecord.php?pid=<?php print $sid ?>", "", "top=0,left=0,width=300,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
		window.location = '<?php print $PHP_SELF."?".$QUERY_STRING."&show_changes=yes"; ?>';
	}
//-->
</script>
<table width="100%"><tr><td>
<?php
if ($accept_success) print "<b>".$pgv_lang["accept_successful"]."</b><br />";
//print "\n\t<span class=\"name_head\">$name";
print "\n\t<span class=\"name_head\">".PrintReady($name);

if ($SHOW_ID_NUMBERS) print " &lrm;($sid)&lrm;";
print "</span><br />";
if (userCanEdit(getUserName())) {
	if ($view!="preview") {
		if (isset($pgv_changes[$sid."_".$GEDCOM])) {
			if (!isset($show_changes)) {
				print "<a href=\"source.php?sid=$sid&amp;show_changes=yes\">".$pgv_lang["show_changes"]."</a>"."  ";
			}
			else {
				if (userCanAccept(getUserName())) print "<a href=\"source.php?sid=$sid&amp;action=accept\">".$pgv_lang["accept_all"]."</a> | ";
				print "<a href=\"source.php?sid=$sid\">".$pgv_lang["hide_changes"]."</a>"."  ";
			}
			print_help_link("show_changes_help", "qm");
			print "<br />";
		}
		print "<a href=\"#\" onclick=\"return edit_raw('$sid');\">".$pgv_lang["edit_raw"]."</a>";
		print_help_link("edit_raw_gedcom_help", "qm");
		print " | ";
		print "<a href=\"#\" onclick=\"return deletesource('$sid');\">".$pgv_lang["delete_source"]."</a>";
		print_help_link("delete_source_help", "qm");
		print "<br />\n";
	}
	if (isset($show_changes)) {
		$newsource = trim(find_record_in_file($sid));
	}
}
print "<br />";

$sourcefacts = array();
$gedlines = preg_split("/\n/", $source);
$lct = count($gedlines);
$factrec = "";	// -- complete fact record
$line = "";	// -- temporary line buffer
$linenum = 1;
for($i=1; $i<=$lct; $i++) {
	if ($i<$lct) $line = $gedlines[$i];
	else $line=" ";
	if (empty($line)) $line=" ";
	if (($i==$lct)||($line{0}==1)) {
		if (!empty($factrec) ) {
			$sourcefacts[] = array($factrec, $linenum);
		}
		$factrec = $line;
		$linenum = $i;
	}
	else $factrec .= "\n".$line;
}

//-- get new source records
if (!empty($newsource)) {
	$newsourcefacts = array();
	$gedlines = preg_split("/\n/", $newsource);
	$lct = count($gedlines);
	$factrec = "";	// -- complete fact record
	$line = "";	// -- temporary line buffer
	$linenum = 0;
	for($i=1; $i<=$lct; $i++) {
		if ($i<$lct) $line = $gedlines[$i];
		else $line=" ";
		if (empty($line)) $line=" ";
		if (($i==$lct)||($line{0}==1)) {
			$newsourcefacts[] = array($factrec, $linenum);
			$factrec = $line;
			$linenum = $i;
		}
		else $factrec .= "\n".$line;
	}

	if (!empty($show_changes)) {
		//-- update old facts
		foreach($sourcefacts as $key=>$fact) {
			$found = false;
			foreach($newsourcefacts as $indexval => $newfact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$sourcefacts[$key][0].="\nPGV_OLD\n";
			}
		}
		//-- look for new facts
		foreach($newsourcefacts as $key=>$newfact) {
			$found = false;
			foreach($sourcefacts as $indexval => $fact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$newfact[0].="\nPGV_NEW\n";
				$sourcefacts[]=$newfact;
			}
		}
	}
}
print "\n<table class=\"facts_table\">";
foreach($sourcefacts as $indexval => $fact) {
	$factrec = $fact[0];
	$linenum = $fact[1];
	$ft = preg_match("/1\s(_?\w+)\s(.*)/", $factrec, $match);
	if ($ft>0) $fact = $match[1];
	else $fact="";
	$fact = trim($fact);
	if (!empty($fact)) {
		if (showFact($fact, $sid)) {
			if ($fact=="OBJE") {
				print_main_media($factrec, 1, $sid, $linenum);
			}
			else if ($fact=="NOTE") {
				print_main_notes($factrec, 1, $sid, $linenum);
			}
			else {
				print_fact($factrec, $sid, $linenum);
			}
		}
	}
}
//-- new fact link
if (($view!="preview") &&(userCanEdit(getUserName()))&&($display)) {
	$addfacts = array_merge(CheckFactUnique(array("AUTH","ABBR","TITL","PUBL","TEXT"), $sourcefacts, "SOUR"), array("NOTE","OBJE","REPO"));
	usort($addfacts, "factsort");
   print "<tr><td class=\"facts_label\">".$pgv_lang["add_fact"]."</td>";
   print "<td class=\"facts_value\">";
   print "<form method=\"get\" name=\"newfactform\">\n";
   print "<select id=\"newfact\" name=\"newfact\">\n";
   foreach($addfacts as $indexval => $fact) {
	  print "<option value=\"$fact\">".$factarray[$fact]."</option>\n";
   }
   if (!empty($_SESSION["clipboard"])) {
		foreach($_SESSION["clipboard"] as $key=>$fact) {
			if ($fact["type"]=="SOUR") {
				print "<option value=\"clipboard_$key\">".$pgv_lang["add_from_clipboard"]." ".$factarray[$fact["fact"]]."</option>\n";
			}
		}
	}
   print "</select>";
   print "<input type=\"button\" value=\"".$pgv_lang["add"]."\" onclick=\"add_record('$sid', 'newfact');\" />\n";
   print_help_link("add_new_facts_help", "qm");
   print "</form>\n";
   print "</td></tr>\n";
}
print "</table>\n\n";
print "\n\t\t<br /><br /><span class=\"label\">".$pgv_lang["other_records"]."</span>";
flush();

$query = "SOUR @$sid@";
if (!$REGEXP_DB) $query = "%".$query."%";
// -- array of names
$myindilist = array();
$myfamlist = array();

$myindilist = search_indis($query);
uasort($myindilist, "itemsort");
$myfamlist = search_fams($query);
uasort($myfamlist, "itemsort");
$ci=count($myindilist);
$cf=count($myfamlist);

if (($ci>0)||($cf>0)) {
	print_help_link("sources_listbox_help", "qm");
	print "\n\t\t<table class=\"list_table\">\n\t\t<tr>";
	if ($ci>0) print "<td class=\"list_label\">".$pgv_lang["individuals"]."</td>\n";
	if ($cf>0) print "<td class=\"list_label\">".$pgv_lang["families"]."</td>";
	print "</tr>\n";
	print "\t\t<tr>";
	if (count($myindilist)>0) {
		print "<td class=\"list_value_wrap\"><ul>";
		$i=0;
		$indi_private=0;
		$indi_hide=0;
		foreach ($myindilist as $key => $value) {
			print_list_person($key, array(check_NN(get_sortable_name($key)), $value["file"]));
			print "\n";
			$i++;
		}
		if ($indi_hide>0) {
			print "<li>".$pgv_lang["hidden"]." (".$indi_hide.")";
			print_help_link("privacy_error_help", "qm");
			print "</li>";
		}
		print "</ul>\n\t\t</td>\n\t\t";
	}
	if (count($myfamlist)>0) {
		$i=0;
		$fam_private=0;
		$fam_hide=0;
		if ($cf>0) {
			print "<td class=\"list_value_wrap\"><ul>";
			foreach ($myfamlist as $key => $value) {
				$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/","/^[a-z. ]*/"), array(",",",",""), $value["name"]);
				$names = preg_split("/[,+]/", $name);
				$value["name"] = check_NN($names);
				print_list_family($key, array(get_family_descriptor($key), $value["file"]));
			    $i++;
			}
			if ($fam_hide>0) {
				print "<li>".$pgv_lang["hidden"]." (".$fam_hide.")";
				print_help_link("privacy_error_help", "qm");
				print "</li>";
			}
			print "\n\t\t</ul></td>";
		}
	}
	print "</tr><tr>";
	if ($ci>0) {
		print "<td>";
		$ci -= $indi_hide;
		if ($ci>0)print $pgv_lang["total_indis"]." ".$ci;
		if ($indi_private>0) print "&nbsp;(".$pgv_lang["private"]." ".$indi_private.")";
		if ($ci>0 && $indi_hide>0) print "&nbsp;--&nbsp;";
		if ($indi_hide>0) print $pgv_lang["hidden"]." ".$indi_hide;
		print "</td>\n";
	}
	if ($cf>0) {
		print "<td>";
		$cf -= $fam_hide;
		if ($cf>0)print $pgv_lang["total_fams"]." ".$cf;
		if ($fam_private>0) print "&nbsp;(".$pgv_lang["private"]." ".$fam_private.")";
		if ($cf>0 && $fam_hide>0) print "&nbsp;--&nbsp;";
		if ($fam_hide>0) print $pgv_lang["hidden"]." ".$fam_hide;
		print "</td>\n";
	}
	print "</tr>\n\t</table>";
}
else print "&nbsp;&nbsp;&nbsp;<span class=\"warning\"><i>".$pgv_lang["no_results"]."</span>";

print "<br /><br /></td><td valign=\"top\">";

if ($view!="preview") {
	print "\n\t<table cellspacing=\"10\" align=\"right\"><tr>";
	if ($SHOW_GEDCOM_RECORD) {
		print "\n\t\t<td align=\"center\" valign=\"top\"><span class=\"link\"><a href=\"javascript:show_gedcom_record();\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]."\" border=\"0\" alt=\"\" /><br />".$pgv_lang["view_gedcom"]."</a>";
		print_help_link("show_source_gedcom_help", "qm");
		print "</span></td>";
	}
	if($SHOW_GEDCOM_RECORD && ($ENABLE_CLIPPINGS_CART>=getUserAccessLevel())){
		print "</tr>\n\t\t<tr>";
	}
	if ($ENABLE_CLIPPINGS_CART>=getUserAccessLevel()) {
		print "<td align=\"center\" valign=\"top\"><span class=\"link\"><a href=\"clippings.php?action=add&amp;id=$sid&amp;type=source\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]."\" border=\"0\" alt=\"\" /><br />".$pgv_lang["add_to_cart"]."</a>";
		print_help_link("add_source_clip_help", "qm");
		print "</span></td>";
	}
	if(!$SHOW_GEDCOM_RECORD && ($ENABLE_CLIPPINGS_CART<getUserAccessLevel())){
		print "<td>&nbsp;</td>";
	}
	print "</tr></table>";
}
print "&nbsp;</td></tr></table>\n";
print_footer();

?>