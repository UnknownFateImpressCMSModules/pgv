<?php
/**
 * Edit Privacy Settings
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
 * @subpackage Privacy
 * @version $Id: edit_privacy.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);

if (empty($ged)) $ged = $GEDCOM;

if ((!userGedcomAdmin(getUserName(), $ged))||(empty($ged))) {
	header("Location: editgedcoms.php");
	exit;
}

$PRIVACY_CONSTANTS = array();
$PRIVACY_CONSTANTS[$PRIV_HIDE] = "\$PRIV_HIDE";
$PRIVACY_CONSTANTS[$PRIV_PUBLIC] = "\$PRIV_PUBLIC";
$PRIVACY_CONSTANTS[$PRIV_USER] = "\$PRIV_USER";
$PRIVACY_CONSTANTS[$PRIV_NONE] = "\$PRIV_NONE";
if (!isset($PRIVACY_BY_YEAR)) $PRIVACY_BY_YEAR = false;
if (!isset($MAX_ALIVE_AGE)) $MAX_ALIVE_AGE = 120;

/**
 * print write_access option
 *
 * @param string $checkVar
 */
function write_access_option($checkVar)
{
  global $PRIV_HIDE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE;
  global $pgv_lang;
  
  print "<option value=\"\$PRIV_PUBLIC\"";
  if ($checkVar==$PRIV_PUBLIC) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_PUBLIC"]."</option>\n";
  print "<option value=\"\$PRIV_USER\"";
  if ($checkVar==$PRIV_USER) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_USER"]."</option>\n";
  print "<option value=\"\$PRIV_NONE\"";
  if ($checkVar==$PRIV_NONE) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_NONE"]."</option>\n";
  print "<option value=\"\$PRIV_HIDE\"";
  if ($checkVar==$PRIV_HIDE) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_HIDE"]."</option>\n";
}

/**
 * print yes/no select option
 *
 * @param string $checkVar
 */
function write_yes_no($checkVar)
{
  global $pgv_lang;

  print "<option";
  if ($checkVar == false) print " selected=\"selected\"";
  print " value=\"no\">";
  print $pgv_lang["no"];
  print "</option>\n";

  print "<option";
  if ($checkVar == true) print " selected=\"selected\"";
  print " value=\"yes\">";
  print $pgv_lang["yes"];
  print "</option>";
}

/**
 * print find and print gedcom record ID
 *
 * @param string $checkVar	gedcom key
 * @param string $outputVar	error message style
 */
function search_ID_details($checkVar, $outputVar) {
	global $GEDCOMS, $GEDCOM;
	global $pgv_lang;

	# print $GEDCOMS[$GEDCOM]["path"]; exit;

	$indirec = find_gedcom_record($checkVar);
	if (empty($indirec)) $indirec = find_record_in_file($checkVar);
    
	if (!empty($indirec)) {
		$ct = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
		if ($ct>0) {
			$pid = $match[1];
			$type = trim($match[2]);
		}
		if ($type=="INDI") {
			$name = get_person_name($pid);
			print "\n<span class=\"list_item\">$name";
			print_first_major_fact($pid);
			print "</span>\n";
		}
		else if ($type=="SOUR") {
			$name = get_source_descriptor($pid);
			print "\n<span class=\"list_item\">$name";
			print "</span>\n";
		}
		else if ($type=="FAM") {
			$name = get_family_descriptor($pid);
			print "\n<span class=\"list_item\">$name";
			print "</span>\n";
		}
		else print "$type $pid";
	}
	else {
		print "<span class=\"error\">";
		if ($outputVar == 1) {
			print_text("unable_to_find_privacy_indi");
			print "<br />[" . $checkVar . "]";
		}
		if ($outputVar == 2) {
			print_text("unable_to_find_privacy_indi");
		}
		print "</span><br /><br />";
	}
}


if (empty($action)) $action="";
$PRIVACY_MODULE = get_privacy_file();

print_header($pgv_lang["privacy_header"]);
print "<div class=\"center\">\n";
print "<span class=\"subheaders\">";
print_text("edit_privacy_title");
print "</span>";
print "<div dir=\"ltr\">(".$PRIVACY_MODULE . ")</div>";
print "<br />";
print_text("help_info");
print  "<br /><br /><a href=\"editgedcoms.php\"><b>";
print_text("lang_back_manage_gedcoms");
print "</b></a><br /><br />";

if ($action=="update") {
	if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
	$boolarray = array();
	$boolarray["yes"]="true";
	$boolarray["no"]="false";
	$boolarray[false]="false";
	$boolarray[true]="true";
	print_text("performing_update");
	print "<br />";
	$configtext = implode('', file("privacy.php"));
	print_text("config_file_read");
	print "<br />\n";
	$configtext = preg_replace('/\$SHOW_DEAD_PEOPLE\s*=\s*.*;/', "\$SHOW_DEAD_PEOPLE = ".$_POST["v_SHOW_DEAD_PEOPLE"].";", $configtext);
	$configtext = preg_replace('/\$SHOW_LIVING_NAMES\s*=\s*.*;/', "\$SHOW_LIVING_NAMES = ".$_POST["v_SHOW_LIVING_NAMES"].";", $configtext);
	$configtext = preg_replace('/\$SHOW_SOURCES\s*=\s*.*;/', "\$SHOW_SOURCES = ".$_POST["v_SHOW_SOURCES"].";", $configtext);
	$configtext = preg_replace('/\$MAX_ALIVE_AGE\s*=\s*".*";/', "\$MAX_ALIVE_AGE = \"".$_POST["v_MAX_ALIVE_AGE"]."\";", $configtext);
	if (file_exists("modules/researchlog.php")) {
		$configtext = preg_replace('/\$SHOW_RESEARCH_LOG\s*=\s*.*;/', "\$SHOW_RESEARCH_LOG = ".$_POST["v_SHOW_RESEARCH_LOG"].";", $configtext);
	}
	$configtext = preg_replace('/\$ENABLE_CLIPPINGS_CART\s*=\s*.*;/', "\$ENABLE_CLIPPINGS_CART = ".$_POST["v_ENABLE_CLIPPINGS_CART"].";", $configtext);
	$configtext = preg_replace('/\$PRIVACY_BY_YEAR\s*=\s*.*;/', "\$PRIVACY_BY_YEAR = ".$boolarray[$_POST["v_PRIVACY_BY_YEAR"]].";", $configtext);
	$configtext = preg_replace('/\$PRIVACY_BY_RESN\s*=\s*.*;/', "\$PRIVACY_BY_RESN = ".$boolarray[$_POST["v_PRIVACY_BY_RESN"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_DEAD_PEOPLE\s*=\s*.*;/', "\$SHOW_DEAD_PEOPLE = ".$_POST["v_SHOW_DEAD_PEOPLE"].";", $configtext);
	$configtext = preg_replace('/\$USE_RELATIONSHIP_PRIVACY\s*=\s*.*;/', "\$USE_RELATIONSHIP_PRIVACY = ".$boolarray[$_POST["v_USE_RELATIONSHIP_PRIVACY"]].";", $configtext);
	$configtext = preg_replace('/\$MAX_RELATION_PATH_LENGTH\s*=\s*.*;/', "\$MAX_RELATION_PATH_LENGTH = \"".$_POST["v_MAX_RELATION_PATH_LENGTH"]."\";", $configtext);
	$configtext = preg_replace('/\$CHECK_MARRIAGE_RELATIONS\s*=\s*.*;/', "\$CHECK_MARRIAGE_RELATIONS = ".$boolarray[$_POST["v_CHECK_MARRIAGE_RELATIONS"]].";", $configtext);
	
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start person privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end person privacy --//"));
	$person_privacy_text = "//-- start person privacy --//\n\$person_privacy = array();\n";
	if (!isset($v_person_privacy_del)) $v_person_privacy_del = array();
	if (!is_array($v_person_privacy_del)) $v_person_privacy_del = array();
	if (!isset($v_person_privacy)) $v_person_privacy = array();
	if (!is_array($v_person_privacy)) $v_person_privacy = array();
	foreach($person_privacy as $key=>$value) {
		if (!isset($v_person_privacy_del[$key])) {
			if (isset($v_person_privacy[$key])) $person_privacy_text .= "\$person_privacy['$key'] = ".$v_person_privacy[$key].";\n";
			else $person_privacy_text .= "\$person_privacy['$key'] = ".$PRIVACY_CONSTANTS[$value].";\n";
		}
	}
	if ((!empty($v_new_person_privacy_access_ID))&&(!empty($v_new_person_privacy_acess_option))) {
		$person_privacy_text .= "\$person_privacy['$v_new_person_privacy_access_ID'] = ".$v_new_person_privacy_acess_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;
	
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start user privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end user privacy --//"));
	$person_privacy_text = "//-- start user privacy --//\n\$user_privacy = array();\n";
	if (!isset($v_user_privacy_del)) $v_user_privacy_del = array();
	if (!is_array($v_user_privacy_del)) $v_user_privacy_del = array();
	if (!isset($v_user_privacy)) $v_user_privacy = array();
	if (!is_array($v_user_privacy)) $v_user_privacy = array();
	foreach($user_privacy as $key=>$value) {
		foreach($value as $id=>$setting) {
			if (!isset($v_user_privacy_del[$key][$id])) {
				if (isset($v_user_privacy[$key][$id])) $person_privacy_text .= "\$user_privacy['$key']['$id'] = ".$v_user_privacy[$key][$id].";\n";
				else $person_privacy_text .= "\$user_privacy['$key']['$id'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
			}
		}
	}
	if ((!empty($v_new_user_privacy_username))&&(!empty($v_new_user_privacy_access_ID))&&(!empty($v_new_user_privacy_acess_option))) {
		$person_privacy_text .= "\$user_privacy['$v_new_user_privacy_username']['$v_new_user_privacy_access_ID'] = ".$v_new_user_privacy_acess_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;
	
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start global facts privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end global facts privacy --//"));
	$person_privacy_text = "//-- start global facts privacy --//\n\$global_facts = array();\n";
	if (!isset($v_global_facts_del)) $v_global_facts_del = array();
	if (!is_array($v_global_facts_del)) $v_global_facts_del = array();
	if (!isset($v_global_facts)) $v_global_facts = array();
	if (!is_array($v_global_facts)) $v_global_facts = array();
	foreach($global_facts as $tag=>$value) {
		foreach($value as $key=>$setting) {
			if (!isset($v_global_facts_del[$tag][$key])) {
				if (isset($v_global_facts[$tag][$key])) $person_privacy_text .= "\$global_facts['$tag']['$key'] = ".$v_global_facts[$tag][$key].";\n";
				else $person_privacy_text .= "\$global_facts['$tag']['$key'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
			}
		}
	}
	if ((!empty($v_new_global_facts_pass))&&(!empty($v_new_global_facts_abbr))&&(!empty($v_new_global_facts_choice))&&(!empty($v_new_global_facts_acess_option))) {
		$person_privacy_text .= "\$global_facts['$v_new_global_facts_abbr']['$v_new_global_facts_choice'] = ".$v_new_global_facts_acess_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;
	
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start person facts privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end person facts privacy --//"));
	$person_privacy_text = "//-- start person facts privacy --//\n\$person_facts = array();\n";
	if (!isset($v_person_facts_del)) $v_person_facts_del = array();
	if (!is_array($v_person_facts_del)) $v_person_facts_del = array();
	if (!isset($v_person_facts)) $v_person_facts = array();
	if (!is_array($v_person_facts)) $v_person_facts = array();
	foreach($person_facts as $id=>$value) {
		foreach($value as $tag=>$value1) {
			foreach($value1 as $key=>$setting) {
				if (!isset($v_person_facts_del[$id][$tag][$key])) {
					if (isset($v_person_facts[$id][$tag][$key])) $person_privacy_text .= "\$person_facts['$id']['$tag']['$key'] = ".$v_person_facts[$id][$tag][$key].";\n";
					else $person_privacy_text .= "\$person_facts['$id']['$tag']['$key'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
				}
			}
		}
	}
	if ((!empty($v_new_person_facts_access_ID))&&(!empty($v_new_person_facts_abbr))&&(!empty($v_new_global_facts_choice))&&(!empty($v_new_global_facts_acess_option))) {
		$person_privacy_text .= "\$person_facts['$v_new_person_facts_access_ID']['$v_new_person_facts_abbr']['$v_new_person_facts_choice'] = ".$v_new_person_facts_acess_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;
	
	//print $configtext;
	
	$PRIVACY_MODULE = $INDEX_DIRECTORY.$GEDCOM."_priv.php";
	$fp = fopen($PRIVACY_MODULE, "wb");
	if (!$fp) {
		print "<span class=\"error\">";
		print_text("gedcom_config_write_error");
		print "<br /></span>\n";
	}
	else {
		fwrite($fp, $configtext);
		fclose($fp);
	}
	//-- load the new variables
	include $INDEX_DIRECTORY.$GEDCOM."_priv.php";
	AddToLog("Privacy file $PRIVACY_MODULE updated by >".getUserName()."<");
	
}

?>
<script language="JavaScript" type="text/javascript">
  var pasteto;
  function open_find(textbox)
  {
    pasteto = textbox;
    findwin = window.open('findid.php', '', 'left=50,top=50,width=410,height=320,resizable=1,scrollbars=1');
  }
  function open_find_source(textbox)
  {
    pasteto = textbox;
    findwin = window.open('findsource.php', '', 'left=50,top=50,width=410,height=320,resizable=1,scrollbars=1');
  }
  function open_find_family(textbox)
  {
    pasteto = textbox;
    findwin = window.open('findfamily.php', '', 'left=50,top=50,width=410,height=320,resizable=1,scrollbars=1');
  }
  function paste_id(value)
  {
    pasteto.value=value;
  }
  	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
</script>

<form name="editprivacyform" method="post" action="edit_privacy.php">
    <input type="hidden" name="action" value="update" />
    <?php print "<input type=\"hidden\" name=\"ged\" value=\"".$GEDCOM."\" />\n"; ?>
    <table>
      <tr>
        <td class="list_label" colspan="2"><b><?php print_text("general_privacy"); ?><a href="#" onclick="return helpPopup('general_privacy_help');"><span class="error"><b> ?</b></span></a></b></td>
      </tr>
      <tr>
        <td class="list_label"><?php print_text("SHOW_DEAD_PEOPLE"); ?> <a href="#" onclick="return helpPopup('SHOW_DEAD_PEOPLE_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_SHOW_DEAD_PEOPLE"><?php write_access_option($SHOW_DEAD_PEOPLE); ?></select>
        </td>
      </tr>
      <tr>
        <td class="list_label"><?php print_text("SHOW_LIVING_NAMES"); ?> <a href="#" onclick="return helpPopup('SHOW_LIVING_NAMES_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_SHOW_LIVING_NAMES"><?php write_access_option($SHOW_LIVING_NAMES); ?></select>
        </td>
      </tr>
      <tr>
        <td class="list_label"><?php print_text("SHOW_SOURCES"); ?> <a href="#" onclick="return helpPopup('SHOW_SOURCES_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_SHOW_SOURCES"><?php write_access_option($SHOW_SOURCES); ?></select>
        </td>
      </tr>
      
      <tr>
        <td class="list_label"><?php print_text("ENABLE_CLIPPINGS_CART"); ?> <a href="#" onclick="return helpPopup('ENABLE_CLIPPINGS_CART_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_ENABLE_CLIPPINGS_CART"><?php write_access_option($ENABLE_CLIPPINGS_CART); ?></select>
        </td>
      </tr>

      <?php if (file_exists("modules/researchlog.php")) { ?>
      <tr>
        <td class="list_label"><?php print_text("SHOW_RESEARCH_LOG"); ?> <a href="#" onclick="return helpPopup('SHOW_RESEARCH_LOG_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_SHOW_RESEARCH_LOG"><?php write_access_option($SHOW_RESEARCH_LOG); ?></select>
        </td>
      </tr>
      <?php } ?>

      <tr>
        <td class="list_label"><?php print_text("PRIVACY_BY_YEAR"); ?> <a href="#" onclick="return helpPopup('PRIVACY_BY_YEAR_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_PRIVACY_BY_YEAR"><?php write_yes_no($PRIVACY_BY_YEAR); ?></select>
        </td>
      </tr>
      
      <tr>
        <td class="list_label"><?php print_text("PRIVACY_BY_RESN"); ?> <a href="#" onclick="return helpPopup('PRIVACY_BY_RESN_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_PRIVACY_BY_RESN"><?php write_yes_no($PRIVACY_BY_RESN); ?></select>
        </td>
      </tr>
      
      <tr>
        <td class="list_label"><?php print_text("USE_RELATIONSHIP_PRIVACY"); ?> <a href="#" onclick="return helpPopup('USE_RELATIONSHIP_PRIVACY_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_USE_RELATIONSHIP_PRIVACY"><?php write_yes_no($USE_RELATIONSHIP_PRIVACY); ?></select>
        </td>
      </tr>

      <tr>
        <td class="list_label"><?php print_text("MAX_RELATION_PATH_LENGTH"); ?> <a href="#" onclick="return helpPopup('MAX_RELATION_PATH_LENGTH_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_MAX_RELATION_PATH_LENGTH"><?php
          for ($y = 1; $y <= 10; $y++)
          {
            print "<option";
            if ($MAX_RELATION_PATH_LENGTH == $y) print " selected=\"selected\"";
            print ">";
            print $y;
            print "</option>";
          }
          ?></select>
        </td>
      </tr>

      <tr>
        <td class="list_label"><?php print_text("CHECK_MARRIAGE_RELATIONS"); ?> <a href="#" onclick="return helpPopup('CHECK_MARRIAGE_RELATIONS_help');"><span class="error"><b> ?</b></span></a></td>
        <td class="list_value <?php print $TEXT_DIRECTION; ?>">
          <select size="1" name="v_CHECK_MARRIAGE_RELATIONS"><?php write_yes_no($CHECK_MARRIAGE_RELATIONS); ?></select>
        </td>
      </tr>
	<tr>
		<td class="list_label"><?php print_text("MAX_ALIVE_AGE")?> <a href="#" onclick="return helpPopup('MAX_ALIVE_AGE_help');"><span class="error"><b> ?</b></span></a></td>
		<td class="list_value <?php print $TEXT_DIRECTION; ?>"><input type="text" name="v_MAX_ALIVE_AGE" value="<?php print $MAX_ALIVE_AGE?>" size="5"/></td>
	</tr>
      <tr>
        <td class="list_label" colspan="2"><input type="submit" name="B001" value="<?php print_text("save_changed_settings"); ?>" /></td>
      </tr>
    </table>
  <br />
  <br /> 
  <?php //--------------person_privacy------------------------------------------------------------------------ 
  ?>
    <table>
      <tr>
        <td class="list_label"><b><?php print_text("person_privacy"); ?></b> <a href="#" onclick="return helpPopup('person_privacy_help');"><span class="error"><b> ?</b></span></a></td>
      </tr>
      <tr>
        <td class="list_label">
          <table>
            <tr>
              <td class="list_label" colspan="2"><b><?php print_text("add_new_pp_setting"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("id"); ?></b></td>
              <td class="list_label"><b><?php print_text("accessible_by"); ?></b></td>
            </tr>
            <tr>
              <td class="list_value">
                <input type="text" class="pedigree_form" name="v_new_person_privacy_access_ID" size="4" />
                <?php
                print "<br /><a href=\"#\" onclick=\"open_find(document.editprivacyform.v_new_person_privacy_access_ID); return false;\">";
                print $pgv_lang["find_id"];
                print "</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_family(document.editprivacyform.v_new_person_privacy_access_ID); return false;\">".$pgv_lang["find_family"]."</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_source(document.editprivacyform.v_new_person_privacy_access_ID); return false;\">".$pgv_lang["find_sourceid"]."</a>";
                ?>
              </td>
              <td class="list_value" style="vertical-align: middle;">
                <select size="1" name="v_new_person_privacy_acess_option"><?php write_access_option(""); ?></select>
              </td>
            </tr>
            <tr>
              <td class="list_label" colspan="2"><input type="submit" name="B002" value="<?php print_text("add_new_pp_setting"); ?>" /></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <?php
          if (count($person_privacy) > 0) {
          ?>
          <table>
            <tr>
              <td class="list_label" colspan="4"><?php print_text("edit_exist_person_privacy_settings"); ?></td>
            </tr>
            <tr>
              <td class="list_label"><?php print_text("delete"); ?></td>
              <td class="list_label"><?php print_text("id"); ?></td>
              <td class="list_label"><?php print_text("full_name"); ?></td>
              <td class="list_label"><?php print_text("accessible_by"); ?></td>
            </tr>
            <?php
            foreach($person_privacy as $key=>$value) {
            ?>
            <tr>
              <td class="list_value">
              <input type="checkbox" name="v_person_privacy_del[<?php print $key; ?>]" value="1" /></td>
              <td class="list_value"><?php print $key; ?></td>
              <td class="list_value"><?php search_ID_details($key, 1); ?></td>
              <td class="list_value">
                <select size="1" name="v_person_privacy[<?php print $key; ?>]"><?php write_access_option($value); ?></select>
              </td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td class="list_label" colspan="4"><input type="submit" name="B003" value="<?php print_text("save_changed_settings"); ?>" /></td>
            </tr>
          </table>
          <?php
          }
          else print "&nbsp;";
          ?>
        </td>
      </tr>
    </table>
  <br /> 
  <?php //--------------user_privacy-------------------------------------------------------------------------- 
  ?>
    <table>
      <tr>
        <td class="list_label"><b><?php print_text("user_privacy"); ?></b> <a href="#" onclick="return helpPopup('user_privacy_help');"><span class="error"><b> ?</b></span></a></td>
      </tr>
      <tr>
        <td class="list_label">
          <table>
            <tr>
              <td class="list_label" colspan="3"><b><?php print_text("add_new_up_setting"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("user_name"); ?></b></td>
              <td class="list_label"><b><?php print_text("id"); ?></b></td>
              <td class="list_label"><b><?php print_text("show_question"); ?></b></td>
            </tr>
            <tr>
              <td class="list_value">
                <select size="1" name="v_new_user_privacy_username">
                <?php
                $users = getUsers();
                foreach($users as $username => $user)
                {
                  print "<option";
                  print " value=\"";
                  print $username;
                  print "\">";
                  print $user["fullname"];
                  print "</option>";
                }
                ?>
                </select>
              </td>
              <td class="list_value">
                <input type="text" class="pedigree_form" name="v_new_user_privacy_access_ID" size="4" />
                <?php
                print "<br /><a href=\"#\" onclick=\"open_find(document.editprivacyform.v_new_user_privacy_access_ID); return false;\">";
                print_text("find_id");
                print "</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_family(document.editprivacyform.v_new_user_privacy_access_ID); return false;\">";
                print_text("find_family");
                print "</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_source(document.editprivacyform.v_new_user_privacy_access_ID); return false;\">";
                print_text("find_sourceid");
                print "</a>";
                ?>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_user_privacy_acess_option"><?php write_access_option(""); ?></select>
              </td>
            </tr>
            <tr>
              <td class="list_label" colspan="3"><input type="submit" name="B004" value="<?php print_text("add_new_up_setting"); ?>" /></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="list_label">
          <?php
          if (count($user_privacy) > 0) {
          ?>
          <table>
            <tr>
              <td class="list_label" colspan="4"><?php print_text("edit_exist_user_privacy_settings"); ?></td>
            </tr>
            <tr>
              <td class="list_label"><?php print_text("delete"); ?></td>
              <td class="list_label"><?php print_text("user_name"); ?></td>
              <td class="list_label"><?php print_text("id"); ?></td>
              <td class="list_label"><?php print_text("show_question"); ?></td>
            </tr>
            <?php
            foreach($user_privacy as $key=>$value) {
	            foreach($value as $id=>$setting) {
            ?>
            <tr>
              <td class="list_value">
              <input type="checkbox" name="v_user_privacy_del[<?php print $key; ?>][<?php print $id; ?>]" value="1" /></td>
              <td class="list_value"><?php print $key; ?></td>
              <td class="list_value"><?php search_ID_details($id, 2); ?><br /><?php print $id; ?></td>
              <td class="list_value">
                <select size="1" name="v_user_privacy[<?php print $key; ?>][<?php print $id; ?>]"><?php write_access_option($setting); ?></select>
              </td>
            </tr>
            <?php
        		}
            }
            ?>
            <tr>
              <td class="list_label" colspan="4"><input type="submit" name="B005" value="<?php print_text("save_changed_settings"); ?>" /></td>
            </tr>
          </table>
          <?php
          }
          else print "&nbsp;";
          ?>
        </td>
      </tr>
    </table>
  <br /> 
  <?php //-------------global_facts------------------------------------------------------------------------ 
  ?>
    <table>
      <tr>
        <td class="list_label"><b><?php print_text("global_facts"); ?></b> <a href="#" onclick="return helpPopup('global_facts_help');"><span class="error"><b> ?</b></span></a></td>
      </tr>
      <tr>
        <td class="list_label">
          <table>
            <tr>
              <td class="list_label" colspan="3"><b><?php print_text("add_new_gf_setting"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("name_of_fact"); ?></b></td>
              <td class="list_label"><b><?php print_text("choice"); ?></b></td>
              <td class="list_label"><b><?php print_text("accessible_by"); ?></b></td>
            </tr>
            <tr>
              <td class="list_value">
              	<input type="hidden" name="v_new_global_facts_pass" value="" />
                <select size="1" name="v_new_global_facts_abbr">
                <?php
                foreach($factarray as $tag=>$label) {
                  print "<option";
                  print " value=\"";
                  print $tag;
                  print "\">";
                  print $tag . " - " . str_replace("<br />", " ", $label);
                  print "</option>";
                }
                ?>
                </select>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_global_facts_choice">
                  <option value="details"><?php print_text("fact_details"); ?></option>
                  <option value="show"><?php print_text("fact_show"); ?></option>
                </select>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_global_facts_acess_option"><?php write_access_option(""); ?></select>
              </td>
            </tr>
            <tr>
              <td class="list_label" colspan="3"><input type="submit" name="B006" value="<?php print_text("add_new_gf_setting"); ?>" onclick="document.editprivacyform.v_new_global_facts_pass.value='1';" /></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="list_label">
          <?php
          if (count($global_facts) > 0) {
          ?>
          <table>
            <tr>
              <td class="list_label" colspan="4"><b><?php print_text("edit_exist_global_facts_settings"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("delete"); ?></b></td>
              <td class="list_label"><b><?php print_text("name_of_fact"); ?></b></td>
              <td class="list_label"><b><?php print_text("choice"); ?></b></td>
              <td class="list_label"><b><?php print_text("accessible_by"); ?></b></td>
            </tr>
            <?php
            foreach($global_facts as $tag=>$value) {
	            foreach($value as $key=>$setting) {
            ?>
            <tr>
              <td class="list_value">
              <input type="checkbox" name="v_global_facts_del[<?php print $tag; ?>][<?php print $key; ?>]" value="1" /></td>
              <td class="list_value" style="text-align: center; ">
              <?php
                if (isset($factarray[$tag])) print $factarray[$tag];
                else print $tag;
                ?>
              </td>
              <td class="list_value" style="text-align: center; "><?php
              if ($key == "show") print_text("fact_show");
              if ($key == "details") print_text("fact_details");
              ?></td>
              <td class="list_value">
                <select size="1" name="v_global_facts[<?php print $tag; ?>][<?php print $key; ?>]"><?php write_access_option($setting); ?></select>
              </td>
            </tr>
            <?php
            	}
        	}
            ?>
            <tr>
              <td class="list_label" colspan="4"><input type="submit" name="B007" value="<?php print_text("save_changed_settings"); ?>" /></td>
            </tr>
          </table>
          <?php
          }
          else print "&nbsp;";
          ?>
        </td>
      </tr>
    </table>
  <br /> <?php //-------------person_facts------------------------------------------------------------------------ 
  ?>
    <table>
      <tr>
        <td class="list_label"><b><?php print_text("person_facts"); ?></b> <a href="#" onclick="return helpPopup('person_facts_help');"><span class="error"><b> ?</b></span></a></td>
      </tr>
      <tr>
        <td class="list_label">
          <table>
            <?php //--Start--add person_facts for individuals----------------------------------------------- 
            ?>
            <tr>
              <td class="list_label" colspan="4"><b><?php print_text("add_new_pf_setting_indi"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("privacy_indi_id"); ?></b></td>
              <td class="list_label"><b><?php print_text("name_of_fact"); ?></b></td>
              <td class="list_label"><b><?php print_text("choice"); ?></b></td>
              <td class="list_label"><b><?php print_text("accessible_by"); ?></b></td>
            </tr>
            <tr>
              <td class="list_value">
                <input type="text" class="pedigree_form" name="v_new_person_facts_access_ID" size="4" />
                <?php
                print "<br /><a href=\"javascript:open_find(document.editprivacyform.v_new_person_facts_access_ID);\">";
                print_text("find_id");
                print "</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_family(document.editprivacyform.v_new_person_facts_access_ID); return false;\">";
                print_text("find_family");
                print "</a>";
                print "<br /><a href=\"#\" onclick=\"open_find_source(document.editprivacyform.v_new_person_facts_access_ID); return false;\">";
                print_text("find_sourceid");
                print "</a>";
                ?>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_person_facts_abbr">
                <?php
                foreach($factarray as $tag=>$label) {
                  print "<option";
                  print " value=\"";
                  print $tag;
                  print "\">";
                  print $tag . " - " . str_replace("<br />", " ", $label);
                  print "</option>";
                }
                ?>
                </select>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_person_facts_choice">
                  <option value="details"><?php print_text("fact_details"); ?></option>
                  <option value="show"><?php print_text("fact_show"); ?></option>
                </select>
              </td>
              <td class="list_value">
                <select size="1" name="v_new_person_facts_acess_option"><?php write_access_option(""); ?></select>
              </td>
            </tr>
            <?php //--End----add person_facts for individuals-----------------------------------------------
         	?>
            <tr>
              <td class="list_label" colspan="4"><input type="submit" name="B008" value="<?php print_text("add_new_pf_setting"); ?>" /></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="list_label">
          <?php
          if (count($person_facts) > 0) {
          ?>
          <table>
            <tr>
              <td class="list_label" colspan="6"><b><?php print_text("edit_exist_person_facts_settings"); ?></b></td>
            </tr>
            <tr>
              <td class="list_label"><b><?php print_text("delete"); ?></b></td>
              <td class="list_label"><b><?php print_text("id"); ?></b></td>
              <td class="list_label"><b><?php print_text("name_of_fact"); ?></b></td>
              <td class="list_label"><b><?php print_text("choice"); ?></b></td>
              <td class="list_label"><b><?php print_text("accessible_by"); ?></b></td>
            </tr>
            <?php
            foreach($person_facts as $id=>$value) {
	            foreach($value as $tag=>$value1) {
		            foreach($value1 as $key=>$setting) {
            ?>
            <tr>
              <td class="list_value" style="text-align: center; ">
              <input type="checkbox" name="v_person_facts_del[<?php print $id; ?>][<?php print $tag; ?>][<?php print $key; ?>]" value="1" /></td>
              <td class="list_value" style="text-align: center; "><?php print $id; ?></td>
              <td class="list_value" style="text-align: center; "><?php
                  search_ID_details($id, 2);
                  print "<br />" . $id;
              ?></td>
              <td class="list_value" style="text-align: center; ">
              <?php
                print $tag. " - ".$factarray[$tag];
              ?></td>
              <td class="list_value" style="text-align: center; "><?php
              if ($key == "show") print_text("fact_show");
              if ($key == "details") print_text("fact_details");
              ?></td>
              <td class="list_value">
                <select size="1" name="v_person_facts[<?php print $id; ?>][<?php print $tag; ?>][<?php print $key; ?>]"><?php write_access_option($setting); ?></select>
              </td>
            </tr>
            <?php
        			}
        		}
            }
            ?>
            <tr>
              <td class="list_label" colspan="6"><input type="submit" name="B009" value="<?php print_text("save_changed_settings"); ?>" /></td>
            </tr>
          </table>
          <?php
          }
          else print "&nbsp;";
          ?>
        </td>
      </tr>
    </table>
    </form>
</div>

<?php
print_footer();

?>