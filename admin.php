<?php
/**
 * Administrative User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * @subpackage Admin
 * @version $Id: admin.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

/**
 * load the main configuration and context
 */
require "config.php";
if (!userGedcomAdmin(getUserName())) {
	header("Location: login.php?url=admin.php");
	exit;
}

require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];

if (!isset($action)) $action="";

print_header($pgv_lang["administration"]);

$d_pgv_changes = "";
if (count($pgv_changes) > 0) $d_pgv_changes = "<a href=\"#\" onclick=\"window.open('edit_changes.php','','width=600,height=600,resizable=1,scrollbars=1'); return false;\">" . $pgv_lang["accept_changes"] . "</a>\n";

if (!isset($logfilename)) $logfilename = "";
$file_nr = 0;
$dir_var = opendir ($INDEX_DIRECTORY);
while ($file = readdir ($dir_var))
{
  if ((strpos($file, ".log") > 0) && (strstr($file, "pgv-") !== false )){$dir_array[$file_nr] = $file; $file_nr++;}
}
closedir($dir_var);
$d_logfile_str  = "<form action=\"admin.php\" method=\"post\">";
$d_logfile_str .= $pgv_lang["view_logs"] . ": ";
$d_logfile_str .= "\n<select name=\"logfilename\">\n";
$ct = count($dir_array);
for($x = 0; $x < $file_nr; $x++)

{
  $ct--;
  $d_logfile_str .= "<option value=\"";
  $d_logfile_str .= $dir_array[$ct];
  if ($dir_array[$ct] == $logfilename) $d_logfile_str .= "\" selected=\"selected";
  $d_logfile_str .= "\">";
  $d_logfile_str .= $dir_array[$ct];
  $d_logfile_str .= "</option>\n";
}
$d_logfile_str .= "</select>\n";
$d_logfile_str .= "<input type=\"submit\" name=\"logfile\" value=\" &gt; \" />";
$d_logfile_str .= "</form>";

$usermanual_filename = "docs/english/PGV-manual-en.html";
$d_LangName = "lang_name_" . "english";
$doc_lang = $pgv_lang[$d_LangName];
$new_usermanual_filename = "docs/" . $languages[$LANGUAGE] . "/PGV-manual-" . $language_settings[$LANGUAGE]["lang_short_cut"] . ".html";
if (file_exists($new_usermanual_filename)){$usermanual_filename = $new_usermanual_filename; $d_LangName = "lang_name_" . $languages[$LANGUAGE]; $doc_lang = $pgv_lang[$d_LangName];}

$d_img_module_str = "&nbsp;";
if (file_exists($PGV_BASE_DIRECTORY."img_editconfig.php")) $d_img_module_str = "<a href=\"img_editconfig.php?action=edit\">".$pgv_lang["img_admin_settings"]."</a><br />";

$err_write = file_is_writeable("config.php");

$users = getUsers();
$verify_msg = false;
foreach($users as $indexval => $user) {
	if (!$user["verified_by_admin"] && $user["verified"])  {
		$verify_msg = true;
	}
}
?>
<div class="center"><!--center-->
  <table class="facts_table, <?php print $TEXT_DIRECTION ?>">
    <tr>
      <td colspan="2" class="facts_label03">
      <?php 
      	print "<h2>PhpGedView v" . $VERSION . " " . $VERSION_RELEASE . "<br />";
      	print_text("administration");
      	print "</h2>";
      	print_text("system_time");
      	print " ".get_changed_date(date("j M Y"))." - ".date($TIME_FORMAT);
      	if (userIsAdmin(getUserName())) {
		  if ($err_write) {
			  print "<br /><span class=\"error\">";
			  print_text("config_still_writable");
			  print "</span><br /><br />";
		  }
		  if ($verify_msg) {
			  print "<br /><span class=\"error\">";
			  print_text("admin_verification_waiting");
			  print "</span><br /><br />";
		  }
	    }
	  ?>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="facts_value" style="text-align:center; "><?php print_text("select_an_option"); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
	<tr>
	  <td colspan="2" class="facts_label03" style="text-align:center; "><?php print_text("admin_info"); ?></td>
	</tr>
	<tr>
	  <td class="facts_value"><a href="readme.txt" target="manual"><?php print_text("readme_documentation");?></a></td>
      <td class="facts_value"><a href="phpinfo.php"><?php print_text("phpinfo");?></a></td>
	</tr>
	<tr>
      <td class="facts_value"><a href="http://phpgedview.sourceforge.net/registry.php" target="_blank"><?php print_text("pgv_registry");?></a></td>
	  <td class="facts_value"><a href="changelog.php" target="manual"><?php print_text("changelog"); ?></a></td>
	</tr>
	<tr>
	  <td colspan="2" class="facts_label03" style="text-align:center; "><?php print_text("admin_geds"); ?></td>
	</tr>
	<tr>
	  <td class="facts_value"><a href="editgedcoms.php"><?php print_text("manage_gedcoms");?></a></td>
	  <td class="facts_value"><a href="edit_merge.php"><?php print_text("merge_records"); ?></a></td>
	</tr>
	<tr>
     <td class="facts_value"><a href="#" onclick="addnewchild(''); return false;"><?php print_text("add_unlinked_person"); ?></a></td>
     <td class="facts_value"><?php if ($d_pgv_changes != "") print $d_pgv_changes; else print "&nbsp;"; ?></td>
	</tr>
<?php if ($MULTI_MEDIA) {?>
   <tr>
      <td class="facts_value"><a href="uploadmedia.php"><?php print_text("upload_media");?></a></td>
      <td class="facts_value"><?php
      if ($MEDIA_DIRECTORY_LEVELS) {
			print "<a href=\"findmedia.php?embed=true\">";
			print_text("manage_media_files");
			print "</a>";
		}
      else print "&nbsp;";?>
      </td>
   </tr><?php } ?>
   <?php if ($MULTI_MEDIA_DB && userIsAdmin(getUserName())) {?>
      <tr><td class="facts_value"><a href=addmedia.php?ged=<?php print $GEDCOM; ?>><?php print_text("add_media_records");?></a></td>
      <td class="facts_value"><a href=linkmedia.php?ged=<?php print $GEDCOM; ?> target="media_win"><?php print_text("link_media_records");?></a></td>
      </tr><?php }?>
   <?php if (userIsAdmin(getUserName())) { ?>
   <tr>
	  <td colspan="2" class="facts_label03" style="text-align:center; "><?php print_text("admin_site"); ?></td>
   </tr>
   <tr>
      <td class="facts_value"><a href="editconfig.php"><?php print_text("configuration");?></a></td>
      <td class="facts_value"><a href="usermigrate.php?proceed=migrate"><?php print_text("um_header");?></a></td>
   </tr>
   <tr>
      <td class="facts_value"><a href="useradmin.php"><?php print_text("user_admin");?></a></td>
	  <td class="facts_value"><a href="usermigrate.php?proceed=backup"><?php print_text("um_backup");?></a></td>
   </tr>
   <tr>
      <td class="facts_value"><a href="editlang.php"><?php print_text("edit_langdiff");?></a></td>
      <td class="facts_value"><?php print $d_logfile_str; ?></td>
   </tr>
   <?php } ?>
  </table>
<!--/center--></div>

<?php
  if (isset($logfilename) and ($logfilename != ""))
  {
    print "<hr><table align=\"center\" width=\"70%\"><tr><td class=\"listlog\">";
    print "<strong>";
    print_text("logfile_content");
    print " [" . $INDEX_DIRECTORY . $logfilename . "]</strong><br /><br />";
    $lines=file($INDEX_DIRECTORY . $logfilename);
    $num = sizeof($lines); for ($i = 0; $i < $num ; $i++)
    {
      print $lines[$i] . "<br />";
    }
    print "</td></tr></table><hr>";
  }
?>
<br /><br />
<?php
print_footer();
?>
