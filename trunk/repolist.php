<?php
/**
 * Repositories List
 *
 * Parses gedcom file and displays a list of the repositories in the file.
 *
 * The alphabet bar shows all the available letters users can click. The bar is built
 * up from the lastnames first letter. Added to this bar is the symbol @, which is
 * shown as a translated version of the variable <var>pgv_lang["NN"]</var>, and a
 * translated version of the word ALL by means of variable <var>$pgv_lang["all"]</var>.
 *
 * The details can be shown in two ways, with surnames or without surnames. By default
 * the user first sees a list of surnames of the chosen letter and by clicking on a
 * surname a list with names of people with that chosen surname is displayed.
 *
 * Beneath the details list is the option to skip the surname list or show it.
 * Depending on the current status of the list.
 *
 * @package PhpGedView
 * @subpackage Lists
 * @version $Id: repolist.php,v 1.1 2005/10/07 18:12:20 skenow Exp $
 */

require("config.php");

$repolist = get_repo_list();               //-- array of regular repository titles 

$cr = count($repolist);

print_header($pgv_lang["repo_list"]);
print "<div class=\"center\">";
print "<h2>".$pgv_lang["repo_list"]."</h2>\n\t";

print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr><td class=\"list_label\"";
if($cr>12)	print " colspan=\"2\"";
if (isset($PGV_IMAGES["repository"]["large"])) $icon="repository"; else $icon="source";
print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$icon]["large"]."\" border=\"0\" width=\"25\" title=\"".$pgv_lang["titles_found"]."\" alt=\"".$pgv_lang["titles_found"]."\" />&nbsp;&nbsp;";
print $pgv_lang["titles_found"];
print "</td></tr><tr><td class=\"$TEXT_DIRECTION list_value_wrap\"><ul>";

if ($cr>0){
	$i=0;
	// -- print the array
	foreach ($repolist as $key => $value) {
		if (begRTLText($key)) 
		     print "\n\t\t\t<li type=\"circle\" class=\"rtl\" dir=\"rtl\">";
		else print "\n\t\t\t<li type=\"circle\" class=\"ltr\" dir=\"ltr\">";

		$id = substr($value["id"], 1, -1);
		print "<a href=\"repo.php?rid=$id\" class=\"list_item\">";
		print PrintReady($key);
		if ($SHOW_ID_NUMBERS) {
			if ($TEXT_DIRECTION=="ltr") print " &lrm;($id)&lrm;";
			else print " &rlm;($id)&rlm;";
		}

		print "</a></li>\n";

		if ($i==ceil($cr/2) && $cr>12) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		$i++;
	}
	print "\n\t\t</ul></td>\n\t\t";
 
	print "</tr><tr><td>".$pgv_lang["total_repositories"]." ".$cr;
}
else print "<span class=\"warning\"><i>".$pgv_lang["no_results"]."</span>";

print "</td>\n\t\t</tr>\n\t</table>";

print_help_link("repolist_listbox_help", "qm");
print "</div>";
print "<br /><br />";
print_footer();
?>