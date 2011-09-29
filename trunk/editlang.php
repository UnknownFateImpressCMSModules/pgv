<?php
/**
 * Display a diff between two language files to help in translating.
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
 * @subpackage Languages
 * @version $Id: editlang.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require "config.php";
require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];

require $PGV_BASE_DIRECTORY . "includes/functions_editlang.php";

if (!isset($action)) $action="";
if (!isset($hide_translated)) $hide_translated=false;
if (!isset($language2)) $language2 = $LANGUAGE;
if (!isset($file_type)) $file_type = "lang";
if (!isset($language1)) $language1="english";
$lang_shortcut = $language_settings[$language2]["lang_short_cut"];

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
	header("Location: login.php?url=editlang.php");
	exit;
}

switch ($action){
  case "edit"	: print_header($pgv_lang["edit_lang_utility"]); break;
  case "export"	: print_header($pgv_lang["export_lang_utility"]); break;
  case "compare": print_header($pgv_lang["compare_lang_utility"]); break;
  default	: print_header($pgv_lang["edit_langdiff"]); break;
}

$QUERY_STRING = preg_replace("/&amp;/", "&", $QUERY_STRING);
$QUERY_STRING = preg_replace("/&&/", "&", $QUERY_STRING);
if (strpos($QUERY_STRING,"&dv="))$QUERY_STRING = substr($QUERY_STRING,0,strpos($QUERY_STRING,"&dv="));

print "<script language=\"JavaScript\" type=\"text/javascript\">\n";
print "<!--\n";
print "var helpWin;\n";
print "function helpPopup00(which) {\n";
print "if ((!helpWin)||(helpWin.closed)){helpWin = window.open('editlang_edit.php?' + which, '' , 'left=50, top=30, width=700, height=600, resizable=1, scrollbars=1'); helpWin.focus();}\n";
print "else helpWin.location = 'editlang_edit.php?' + which;\n";
print "return false;\n";
print "}\n";
print "function showchanges(which2) {\n";
print "\twindow.location = '$PHP_SELF?$QUERY_STRING'+which2;\n";
print "}\n";
print "function helpPopup02(which) {\n";
print "if ((!helpWin)||(helpWin.closed)){helpWin = window.open('editlang_edit_settings.php?' + which, '' , 'left=50, top=30, width=700, height=600, resizable=1, scrollbars=1'); helpWin.focus();}\n";
print "else helpWin.location = 'editlang_edit_settings.php?' + which;\n";
print "return false;\n";
print "}\n";
print "function helpPopup03(which) {\n";
print "if ((!helpWin)||(helpWin.closed)){helpWin = window.open('editlang_edit_settings.php?' + which + '&new_shortcut=' + document.new_lang_form.new_shortcut.value, '' , 'left=50, top=30, width=700, height=600, resizable=1, scrollbars=1'); helpWin.focus();}\n";
print "else helpWin.location = 'editlang_edit_settings.php?' + which + '&new_shortcut=' + document.new_lang_form.new_shortcut.value;\n";
print "return false;\n";
print "}\n";
print "//-->\n";
print "</script>\n";

print "<div class=\"center\">";
print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
print "<tr>";
print "<td class=\"facts_label03\">";
print_text("translation_forum");
print "</td>";
print "</tr>";
print "<tr>";
print "<td class=\"facts_value\">";
print_text("translation_forum_help");
print "</td>";
print "</tr>";
print "</table><br />";
print  "<a href=\"admin.php\"><b>";
print_text("lang_back_admin");
print "</b></a><br /><br />";
print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";

// Sort the Language table into localized language name order
foreach ($pgv_language as $key => $value){
	$d_LangName = "lang_name_".$key;
	$Sorted_Langs[$key] = $pgv_lang[$d_LangName];
}
asort($Sorted_Langs);

/* Language File Settings Mask */

if (($action != "edit") and ($action != "export") and ($action != "compare") and ($action != "config_lang") and ($action != "new_lang")){
    //-- Choose the language you want to edit the settings of
    print "\n<a name=\"a4_0\"></a>\n";
    print "<br />";
    print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
    print "<tr>";
	print "<td class=\"facts_label03\">";
	print_text("config_lang_utility");
	print "</td>";
	print "</tr>";
	print "<tr>";
	print "<td class=\"facts_value\">";
	print_text("config_lang_utility_help");
	print "</td>";
	print "</tr>";
	print "</table>";

    print "<form name=\"lang_config_form\" method=\"get\" action=\"$PHP_SELF\">";
	print "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />";
	print "<input type=\"hidden\" name=\"action\" value=\"config_lang\" />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
	print "<tr>";

	// Column headings, left set
	print "<td class=\"facts_label03\">";
	print_text("lang_language");
	print "</td>";

	print "<td class=\"facts_label03\">";
	print_text("active");
	print "</td>";

	print "<td class=\"facts_label03\">";
	print_text("edit_settings");
	print "</td>";

	// Separator
	print "<td class=\"facts_label03\">" . "&nbsp;" . "</td>";

	// Column headings, right set
	print "<td class=\"facts_label03\">";
	print_text("lang_language");
	print "</td>";

	print "<td class=\"facts_label03\">";
	print_text("active");
	print "</td>";

	print "<td class=\"facts_label03\">";
	print_text("edit_settings");
	print "</td>";

	// End of row
	print "</tr>\n";

// Print the Language table in sorted name order
	$cnt = 0;
	foreach ($Sorted_Langs as $key => $value){
		if (($cnt % 2) == 0){print "<tr>";}	/* Even number */
		if ($pgv_lang_use["$key"]) {
			$Colour = "";
		} else {
			$Colour = "red";
		}
		print "<td class=\"facts_value$Colour\" style=\"text-align: center;\">";
		$d_LangName = "lang_name_" . $key;
		print $pgv_lang[$d_LangName];
		print "</td>";
		print "<td class=\"facts_value$Colour\" style=\"text-align: center;\">";
		if ($pgv_lang_use["$key"]) print $pgv_lang["yes"]; else print $pgv_lang["no"];
		print "</td>";
		print "<td class=\"facts_value$Colour\" style=\"text-align: center;\">";
		print "<a href=\"#\" onclick=\"return helpPopup02('" . "ln=" . $key . "&amp;" . session_name() . "=" . session_id() . "&amp;anchor=a4_0" . "');\">";
		print $pgv_lang["lang_edit"];
		print "</a>";
		print "</td>";

		/* Odd number closes <tr> - even number adds empty <td>*/
		if (($cnt % 2) == 1) print "</tr>"; else print "<td class=\"facts_label03\">&nbsp;</td>";
		$cnt++;
	}
    /* Odd number */
    if (($cnt % 2) == 1){print "<td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td></tr>";}

    print "</table>";
    print "</form>";

    if ($action == "config_lang") {
	    print  "<a href=\"editlang.php\"><b>";
	    print_text("lang_back");
	    print "</b></a><br /><br />";
    }
    print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
  }

/* Language File Edit Mask */

if (($action == "") || ($action == "edit") || ($action == "bom")){
	//-- Check for the BOM code
    print "\n<a name=\"a1_0\"></a>\n";
    print "<br />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
    print "<tr>";
    print "<td class=\"facts_label03\">";
    print_text("bom_check");
    print "</td>";
    print "</tr>";
    print "<tr>";
    print "<td class=\"facts_value\">";
    print_text("bom_check_help");
    print "</td>";
    print "</tr>";
    print "</table>";
    print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
    print "<tr>";
    if ($action == "bom"){
	    print "<td class=\"facts_value\" style=\"text-align: left; \">";
	    check_bom();
	    print "</td>";
    }
    print "<td class=\"facts_value\" valign=\"top\" style=\"text-align: center; \">";
	print "<a href=\"editlang.php?action=bom\">".$pgv_lang["check"]."</a>";
	print "</td>";
    print "</tr>";
    print "</table><br />";
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";

    //-- Choose the language and the file type you want do edit the content of
    print "\n<a name=\"a1_0\"></a>\n";
    print "<br />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
    print "<tr>";
    print "<td class=\"facts_label03\">";
    print_text("edit_lang_utility");
    print "</td>";
    print "</tr>";
    print "<tr>";
    print "<td class=\"facts_value\">";
    print_text("edit_lang_utility_help");
    print "</td>";
    print "</tr>";
    print "</table>";

    print "<form name=\"choose_form\" method=\"get\" action=\"$PHP_SELF\">";
    print "<input type=\"hidden\" name=\"action\" value=\"edit\" />";
    print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
    print "<tr>";
    print "<td class=\"facts_value\">";
    print_text("language_to_edit");
    print ":";
    print_help_link("language_to_edit_help", "qm");
    print "<br />";
    print "<select name=\"language2\">";
	foreach ($Sorted_Langs as $key => $value){
    	print "\n\t\t\t<option value=\"$key\"";
        if ($key == $language2) print " selected=\"selected\"";
        print ">".$pgv_lang["lang_name_".$key]."</option>";
    }
    print "</select>";
    print "</td>";
   	print "<td class=\"facts_value\">";
   	print_text("file_to_edit");
   	print ":";
    print_help_link("file_to_edit_help", "qm");
    print "<br />";
    print "<select name=\"file_type\">";
    print "\n\t\t\t<option value=\"lang\"";
    if ($file_type == "lang") print " selected=\"selected\"";
    print ">"."lang.xx.php"."</option>";

    print "\n\t\t\t<option value=\"facts\"";
    if ($file_type == "facts") print " selected=\"selected\"";
    print ">" . "facts.xx.php" . "</option>";

    print "\n\t\t\t<option value=\"configure_help\"";
    if ($file_type == "configure_help") print " selected=\"selected\"";
    print ">" . "configure_help.xx.php" . "</option>";

    print "\n\t\t\t<option value=\"help_text\"";
    if ($file_type == "help_text") print " selected=\"selected\"";
    print ">" . "help_text.xx.php" . "</option>";

    print "</select>";
  	print "</td>";

	print "<td class=\"facts_value\">";
	print_text("hide_translated");
	print ":";
	print_help_link("hide_translated_help", "qm");
	print "<br />";
	print "<select name=\"hide_translated\">";
	print "<option";
	if (!$hide_translated) print " selected=\"selected\"";
	print " value=\"";
	print "0";
	print "\">";
	print $pgv_lang["no"];
	print "</option>";

	print "<option";
	if ($hide_translated) print " selected=\"selected\"";
	print " value=\"";
	print "1";
	print "\">";
	print $pgv_lang["yes"];
	print "</option>";

	print "</select>";
	print "</td>";

	print "<td class=\"facts_value\" style=\"text-align: center; \">";
	print "<input type=\"submit\" value=\"" . $pgv_lang["edit"] . "\" />";
	print "</td>";
	print "</tr>";
	print "</table>";
    print "</form>";
    if ($action == "edit") {
	    print "<br /><a href=\"editlang.php\"><b>";
	    print_text("lang_back");
	    print "</b></a><br /><br />";
    }
    print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";

  }

  /* Language File Export Mask */

  if (($action != "edit") and ($action != "compare") and ($action != "config_lang") and ($action != "new_lang"))
  {
  	//-- Choose the language you want to export the help messages into a seperate HTML-File
	print "\n<a name=\"a2_0\"></a>\n";
	print "<br />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
	print "<tr>";
	print "<td class=\"facts_label03\">";
	print_text("export_lang_utility");
	print "</td>";
	print "</tr>";
	print "<tr>";
	print "<td class=\"facts_value\">";
	print_text("export_lang_utility_help");
	print "</td>";
	print "</tr>";
	print "</table>";

	print "<form name=\"export_form\" method=\"get\" action=\"$PHP_SELF\">";
	print "<input type=\"hidden\" name=\"action\" value=\"export\" />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
	print "<tr>";
	print "<td class=\"facts_value\">";
	print_text("language_to_export");
	print ":";
	print_help_link("language_to_export_help", "qm");
	print "<br />";
	print "<select name=\"language2\">";
	foreach ($Sorted_Langs as $key => $value){
		print "\n\t\t\t<option value=\"$key\"";
		if ($key == $language2) print " selected=\"selected\"";
		print ">".$pgv_lang["lang_name_".$key]."</option>";
	}
	print "</select>";
	print "</td>";

	print "<td class=\"facts_value\" style=\"text-align: center; \">";
	print "<input type=\"submit\" value=\"" . $pgv_lang["export"] . "\" />";
	print "</td>";
	print "</tr>";
	print "</table>";
	print "</form>";
    if ($action != "export") print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
  }

  /* Language File Comparision Mask */

if (($action != "edit") and ($action != "export") and ($action != "config_lang") and ($action != "new_lang")){
    //-- Choose the languages you want to compare the content of
	print "\n<a name=\"a3_0\"></a>\n";
	print "<br />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
	print "<tr>";
	print "<td class=\"facts_label03\">";
	print_text("compare_lang_utility");
	print "</td>";
	print "</tr>";
	print "<tr>";
	print "<td class=\"facts_value\">";
	print_text("compare_lang_utility_help");
	print "</td>";
	print "</tr>";
	print "</table>";

	print "<form name=\"langdiff_form\" method=\"get\" action=\"$PHP_SELF\">";
	print "<input type=\"hidden\" name=\"action\" value=\"compare\" />";
	print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
	print "<tr>";
	print "<td class=\"facts_value\">";
	print_text("new_language");
	print ":";
	print_help_link("new_language_help", "qm");
	print "<br />";
	print "<select name=\"language1\">";
	foreach ($Sorted_Langs as $key => $value){
		print "\n\t\t\t<option value=\"$key\"";
		if ($key == $language1) print " selected=\"selected\"";
		print ">".$pgv_lang["lang_name_".$key]."</option>";
	}
	print "</select>";
	print "</td>";
	print "<td class=\"facts_value\">";
	print_text("old_language");
	print ":";
	print_help_link("old_language_help", "qm");
	print "<br />";
	print "<select name=\"language2\">";
	foreach ($Sorted_Langs as $key => $value){
		print "\n\t\t\t<option value=\"$key\"";
		if ($key == $language2) print " selected=\"selected\"";
		print ">".$pgv_lang["lang_name_".$key]."</option>";
	}
	print "</select>";
	print "</td>";

	print "<td class=\"facts_value\" style=\"text-align: center; \">";
	print "<input type=\"submit\" value=\"" . $pgv_lang["compare"] . "\" />";
	print "</td>";
	print "</tr>";
	print "</table>";
	print "</form>";
	if ($action == "compare") {
		print "<a href=\"editlang.php\"><b>";
		print_text("lang_back");
		print "</b></a><br /><br />";
	}
	print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
}

  /* Add new Language Mask */
  if (($action != "edit") && ($action != "export") && ($action != "compare") && ($action != "config_lang") && ($action != "bom")) {
    print "\n<a name=\"a5_0\"></a>\n";
    print "<br />";
    print "<form name=\"new_lang_form\" method=\"get\" action=\"$PHP_SELF\">";
      print "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />";
      print "<input type=\"hidden\" name=\"action\" value=\"new_lang\" />";
      print "<table class=\"facts_table, $TEXT_DIRECTION\" style=\"width:70%; \">";
        print "<tr>";
          print "<td colspan=\"7\" class=\"facts_label03\">";
          print_text("add_new_language");
          print "</td>";
        print "</tr>";

        print "<tr>";
          print "<td colspan=\"7\" class=\"facts_value\">";
          print_text("add_new_language_help");
          print "</td>";
        print "</tr>";

        if ($action == "" || $action == "bom")
        {
        print "<tr>";
          print "<td colspan=\"7\" class=\"facts_value\" style=\"text-align: center;\" >\n\n";
            require($PGV_BASE_DIRECTORY . "includes/lang_codes_std.php");
            print "<select name=\"new_shortcut\">\n";
              foreach ($lng_codes as $key => $value)
              {
              	$showLang = true;
              	foreach ($lang_short_cut as $key02=>$value)
		{
		  if ($value == $key)
		  {
		    $showLang = false;
		    break;
		  }
		}

              	if ($showLang)
              	{
              	  print "<option value=\"$key\"";
              	  print ">".$lng_codes[$key]."</option>\n";
              	}
              }
            print "</select>\n\n";
            print_help_link("add_new_lang_help", "qm");

            print "<input type=\"submit\" value=\"" . $pgv_lang["add_new_lang_button"] . "\" onclick=\"return helpPopup03('" . "action=new_lang" . "&amp;" . session_name() . "=" . session_id() . "'); \" />";
          print "</td>";
        print "</tr>";
        }

      print "</table>";
    print "</form>";
    if ($action == "")
    {
      print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br /><br />\n";
    }
  }


  if ($action == "") {
	  print "<a href=\"admin.php\"><b>";
	  print_text("lang_back_admin");
	  print "</b></a><br /><br />";
  }

  /* Language File Edit Utility routines */

  if ($action == "edit")
  {
    print "<br /><span class=\"subheaders\">" . $pgv_lang["listing"] . ": \"";
    switch ($file_type)
    {
      case "lang"		: print $pgv_language["english"] . "\" ";
          			  print $pgv_lang["and"] . " \"";
          			  print $pgv_language[$language2];
          			  // read the english lang.en.php file into array
          			  $english_language_array = array();
          			  $english_language_array = read_complete_file_into_array($pgv_language["english"], "pgv_lang[");
          			  // read the chosen lang.xx.php file into array
          			  $new_language_array = array();
          			  $new_language_array = read_complete_file_into_array($pgv_language[$language2], "pgv_lang[");
          			  break;
      case "facts"		: print $factsfile["english"]."\" ";
          			  print $pgv_lang["and"] . " \"";
          			  print $factsfile[$language2];
          			  // read the english lang.en.php file into array
          			  $english_language_array = array();
          			  $english_language_array = read_complete_file_into_array($factsfile["english"], "factarray[");
          			  // read the chosen lang.xx.php file into array
          			  $new_language_array = array();
          			  $new_language_array = read_complete_file_into_array($factsfile[$language2], "factarray[");
          			  break;
      case "configure_help"	: print $confighelpfile["english"]."\" ";
          			  print $pgv_lang["and"] . " \"";
          			  print $confighelpfile[$language2];
          			  // read the english lang.en.php file into array
          			  $english_language_array = array();
          			  $english_language_array = read_complete_file_into_array($confighelpfile["english"], "pgv_lang[");
          			  // read the chosen lang.xx.php file into array
          			  $new_language_array = array();
          			  $new_language_array = read_complete_file_into_array($confighelpfile[$language2], "pgv_lang[");
          			  break;
      case "help_text"		: print $helptextfile["english"]."\" ";
          			  print $pgv_lang["and"] . " \"";
          			  print $helptextfile[$language2];
          			  // read the english lang.en.php file into array
          			  $english_language_array = array();
          			  $english_language_array = read_complete_file_into_array($helptextfile["english"], "pgv_lang[");
          			  // read the chosen lang.xx.php file into array
          			  $new_language_array = array();
          			  $new_language_array = read_complete_file_into_array($helptextfile[$language2], "pgv_lang[");
          			  break;
    }

    print "\"</span><br /><br />\n";
    print "<span class=\"subheaders\">" . $pgv_lang["contents"] . ":</span>";
    print "<table class=\"facts_table, $TEXT_DIRECTION\">\n";
      $lastfound = (-1);
      for ($z = 0; $z < sizeof($english_language_array); $z++)
      {
        if (isset($english_language_array[$z][1]))
        {
	  $dummy_output = "";
	  $dummy_output .= "<tr>";
	    $dummy_output .= "<td class=\"facts_label\" rowspan=\"2\" dir=\"ltr\">";
	      $dummy_output .= $english_language_array[$z][0];
	    $dummy_output .= "</td>\n";
	    $dummy_output .= "<td class=\"facts_value\">";
	      $dummy_output .= "\n<a name=\"a1_".$z."\"></a>\n";
	      if (stripslashes(mask_all($english_language_array[$z][1])) == "")
	      {
	        $dummy_output .= "<strong style=\"color: #FF0000\">" . str_replace("#LANGUAGE_FILE#", $pgv_language[$language1], $pgv_lang["message_empty_warning"]) . "</strong>";
	      }
	      else $dummy_output .= "<i>" . stripslashes(mask_all($english_language_array[$z][1])) . "</i>";
	    $dummy_output .= "</td>";
	  $dummy_output .= "</tr>\n";
	  $dummy_output_02 = "";
	  $dummy_output_02 .= "<tr>\n";
	    $dummy_output_02 .= "<td class=\"facts_value\">";

	      $found = false;
	      for ($y = 0; $y < sizeof($new_language_array); $y++)
	      {
	        if (isset($new_language_array[$y][1]))
	        {
	          if ($new_language_array[$y][0] == $english_language_array[$z][0])
	          {
	            $dDummy =  $new_language_array[$y][1];
	            $dummy_output_02 .= "<a href=\"#\" onclick=\"return helpPopup00('" . "ls01=" . $z . "&amp;ls02=" . $y . "&amp;language2=" . $language2 . "&amp;file_type=" . $file_type . "&amp;" . session_name() . "=" . session_id() . "&amp;anchor=a1_" . $z . "');\">";
	            $dummy_output_02 .= stripslashes(mask_all($dDummy));
	            if (stripslashes(mask_all($dDummy)) == "")
	            {
	              $dummy_output_02 .= "<strong style=\"color: #FF0000\">" . str_replace("#LANGUAGE_FILE#", $pgv_language[$language2], $pgv_lang["message_empty_warning"]) . "</strong>";
	            }
	            $dummy_output_02 .= "</a>";
	            $found = true;
	            $lastfound = $y;
	            break;
	          }
	        }
	      }
	      if ((($hide_translated) and (!$found)) or (!$hide_translated))
	      {
	  print $dummy_output;
	      	print $dummy_output_02;
	      if (!$found)
	      {
	        print "<a style=\"color: #FF0000\" href=\"#\" onclick=\"return helpPopup00('" . "ls01=" . $z . "&amp;ls02=" . (0 - intval($lastfound) - 1) . "&amp;language2=" . $language2 . "&amp;file_type=" . $file_type . "&amp;anchor=a1_" . $z . "');\">";
	        print "<i>";
	        if (stripslashes(mask_all($english_language_array[$z][1])) == "")
	        {
	          print "&nbsp;";
	        }
	        else print stripslashes(mask_all($english_language_array[$z][1]));
	        print "</i>";
	        print "</a>";
	      }

	    print "</td>";
	  print "</tr>\n";
	      }
	}
      }
    print "</table>\n";
    print  "<br /><br />" . "<a href=\"editlang.php\"><b>";
    print_text("lang_back");
    print "</b></a><br /><br />";
  }

  /* Language File Export Utility routines */

  if ($action == "export")
  {
    $FileName = $confighelpfile[$language2] . ".html";
    $fp = @fopen($FileName, "w");

    fwrite($fp, "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\r\n");
    $language_array = array();
    $language_array = read_export_file_into_array($confighelpfile[$language2], "pgv_lang[");
    $new_language_array = array();
    $new_language_array_counter = 0;;

    for ($z = 0; $z < sizeof($language_array); $z++)
    {
      if (isset($language_array[$z][0]))
      {
      if (strpos($language_array[$z][0], "_help") > 0)
      {
        $language_array[$z][0] = substr($language_array[$z][0], strpos($language_array[$z][0], "\"") + 1);
        $language_array[$z][0] = substr($language_array[$z][0], 0, strpos($language_array[$z][0], "\""));
        $new_language_array[$new_language_array_counter] = $language_array[$z];
        $new_language_array_counter++;
      }
      }
    }

    fwrite($fp, "<ol>");

    for ($z = 0; $z < sizeof($new_language_array); $z++)
    {
      for ($x = 0; $x < sizeof($language_array); $x++)
      {
        $dDummy = $new_language_array[$z][0];
        $dDummy = substr($dDummy, 0, strpos($dDummy, "_help"));

        if (isset($language_array[$x][0]))
        {
        if (strpos($language_array[$x][0], "\"" . $dDummy . "\"") > 0)
        {
          if ($new_language_array[$z][0] != "config_help")
          {
            if ($new_language_array[$z][0] != "welcome_help")
            {
              $new_language_array[$z][0] = $language_array[$x][1];
            }
          }
          break;
        }
        }
      }
    }

    // Temporarily switch languages to match the language selected for Export,
    //   so that function export_help_text will substitute text in the correct language
	if ($language2 != $LANGUAGE) {			// Only necessary when languages differ
		require $PGV_BASE_DIRECTORY . $pgv_language["english"];		// Load English first
		require $PGV_BASE_DIRECTORY . $pgv_language[$language2];	//   then output lang.
		require $PGV_BASE_DIRECTORY . $factsfile["english"];
		require $PGV_BASE_DIRECTORY . $factsfile[$language2];
		require $PGV_BASE_DIRECTORY . $helptextfile["english"];
		require $PGV_BASE_DIRECTORY . $helptextfile[$language2];
	  	require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
	  	require $PGV_BASE_DIRECTORY . $confighelpfile[$language2];
  	}

    for ($z = 0; $z < sizeof($new_language_array); $z++)
    {
      if ($new_language_array[$z][0] != "config_help")
      {
        if ($new_language_array[$z][0] != "welcome_help")
        {
          fwrite($fp, "<li><strong>".stripslashes(print_text($new_language_array[$z][0],0,2)) . "</strong><br />");
          fwrite($fp, stripslashes(print_text($new_language_array[$z][1],0,2)) . "<br /><br /></li>\r\n");
        }
      }
    }

    // Restore language to original setting -- we're done
	if ($language2 != $LANGUAGE) {			// Only necessary when languages differ
		require $PGV_BASE_DIRECTORY . $pgv_language["english"];		// Load English first
		require $PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE];		//   then active lang.
		require $PGV_BASE_DIRECTORY . $factsfile["english"];
		require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];
		require $PGV_BASE_DIRECTORY . $helptextfile["english"];
		require $PGV_BASE_DIRECTORY . $helptextfile[$LANGUAGE];
	  	require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
	  	require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];
  	}

    fwrite($fp, "</ol>");
    fwrite($fp, "</body></html>\r\n");
    fclose($fp);
    print "<br /><strong>";
    print_text("export_ok");
    print "</strong><br />";
    print_text("export_filename");
    print " " . $FileName;
    print  "<br /><br />" . "<a href=\"editlang.php\"><b>";
    print_text("lang_back");
    print "</b></a><br /><br />";

  }

  /* Language File Comparison Utility routines */

  if ($action == "compare")
  {
    $d_pgv_lang["comparing"] = $pgv_lang["comparing"];
    $d_pgv_lang["no_additions"] = $pgv_lang["no_additions"];
    $d_pgv_lang["additions"] = $pgv_lang["additions"];
    $d_pgv_lang["subtractions"] = $pgv_lang["subtractions"];
    $d_pgv_lang["no_subtractions"] = $pgv_lang["no_subtractions"];

    print "<br /><span class=\"subheaders\">".$d_pgv_lang["comparing"]."<br />\"".$pgv_language[$language1]."\" <---> \"".$pgv_language[$language2]."\"</span><br /><br />\n";
    $pgv_lang=array();
    require $PGV_BASE_DIRECTORY.$pgv_language[$language1];
    $lang1 = $pgv_lang;
    print "<span class=\"subheaders\">".$d_pgv_lang["additions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
    $pgv_lang=array();
    if (file_exists($PGV_BASE_DIRECTORY.$pgv_language[$language2])) require $PGV_BASE_DIRECTORY.$pgv_language[$language2];
    $count=0;
    foreach($lang1 as $key=>$value)
    {
      if (!array_key_exists($key, $pgv_lang))
      {
      	print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	$count++;
      }
    }
    if ($count==0)
    {
      print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_additions"]."</td></tr>\n";
    }
    print "</table><br /><br />\n";
    print "<span class=\"subheaders\">".$d_pgv_lang["subtractions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
    $count=0;
    foreach($pgv_lang as $key=>$value)
    {
      if (!array_key_exists($key, $lang1))
      {
      	print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	$count++;
      }
    }
    if ($count==0)
    {
      print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_subtractions"]."</td></tr>\n";
    }
    print "</table><br /><br />\n";

    print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
    print "<span class=\"subheaders\">".$d_pgv_lang["comparing"]."<br />\"".$factsfile[$language1]."\" <---> \"".$factsfile[$language2]."\"<br /><br /></span>\n";
    $factsarray=array();
    require $PGV_BASE_DIRECTORY.$factsfile[$language1];
    $lang1 = $factarray;
    $factarray=array();
    if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$language2])) require $PGV_BASE_DIRECTORY.$factsfile[$language2];
    print "<span class=\"subheaders\">".$d_pgv_lang["additions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
    $count=0;
    foreach($lang1 as $key=>$value)
    {
      if (!array_key_exists($key, $factarray))
      {
      	print "<tr><td class=\"facts_label\">\$factarray[\"$key\"]</td>\n";
      	print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	$count++;
      }
    }
    if ($count==0)
    {
      print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_additions"]."</td></tr>\n";
    }
    print "</table><br /><br />\n";
    print "<span class=\"subheaders\">".$d_pgv_lang["subtractions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
    $count=0;
    foreach($factarray as $key=>$value)
    {
      if (!array_key_exists($key, $lang1))
      {
      	print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	$count++;
      }
    }
    if ($count==0)
    {
      print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_subtractions"]."</td></tr>\n";
    }
    print "</table><br /><br />\n";

    if (file_exists($confighelpfile[$language2]))
    {
      print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
      print "<span class=\"subheaders\">".$d_pgv_lang["comparing"]."<br />\"".$confighelpfile[$language1]."\" <---> \"".$confighelpfile[$language2]."\"</span><br /><br />\n";
      $pgv_lang=array();
      require $PGV_BASE_DIRECTORY.$confighelpfile[$language1];
      $lang1 = $pgv_lang;
      $pgv_lang=array();
      if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$language2])) require $PGV_BASE_DIRECTORY.$confighelpfile[$language2];
      print "<span class=\"subheaders\">".$d_pgv_lang["additions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
      $count=0;
      foreach($lang1 as $key=>$value)
      {
      	if (!array_key_exists($key, $pgv_lang))
      	{
      	  print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	  print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	  $count++;
      	}
      }
      if ($count==0)
      {
        print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_additions"]."</td></tr>\n";
      }

      print "</table><br /><br />\n";
      print "<span class=\"subheaders\">".$d_pgv_lang["subtractions"].":</span>:<table class=\"facts_table, $TEXT_DIRECTION\">\n";
      $count=0;
      foreach($pgv_lang as $key=>$value)
      {
      	if (!array_key_exists($key, $lang1))
      	{
      	  print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	  print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	  $count++;
      	}
      }
      if ($count==0)
      {
      	print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_subtractions"]."</td></tr>\n";
      }
      print "</table><br /><br />\n";
    }
    require $PGV_BASE_DIRECTORY.$pgv_language[$language1];
    require $PGV_BASE_DIRECTORY.$pgv_language[$language2];

    if (file_exists($helptextfile[$language2]))
    {
      print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
      print "<span class=\"subheaders\">".$d_pgv_lang["comparing"]."<br />\"".$helptextfile[$language1]."\" <---> \"".$helptextfile[$language2]."\"</span><br /><br />\n";
      $pgv_lang=array();
      require $PGV_BASE_DIRECTORY.$helptextfile[$language1];
      $lang1 = $pgv_lang;
      $pgv_lang=array();
      if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$language2])) require $PGV_BASE_DIRECTORY.$helptextfile[$language2];
      print "<span class=\"subheaders\">".$d_pgv_lang["additions"].":</span><table class=\"facts_table, $TEXT_DIRECTION\">\n";
      $count=0;
      foreach($lang1 as $key=>$value)
      {
      	if (!array_key_exists($key, $pgv_lang))
      	{
      	  print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	  print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	  $count++;
      	}
      }
      if ($count==0)
      {
        print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_additions"]."</td></tr>\n";
      }

      print "</table><br /><br />\n";
      print "<span class=\"subheaders\">".$d_pgv_lang["subtractions"].":</span>:<table class=\"facts_table, $TEXT_DIRECTION\">\n";
      $count=0;
      foreach($pgv_lang as $key=>$value)
      {
      	if (!array_key_exists($key, $lang1))
      	{
      	  print "<tr><td class=\"facts_label\">\$pgv_lang[\"$key\"]</td>\n";
      	  print "<td class=\"facts_value\">\"$value\";</td></tr>\n";
      	  $count++;
      	}
      }
      if ($count==0)
      {
      	print "<tr><td colspan=\"2\" class=\"facts_value\">".$d_pgv_lang["no_subtractions"]."</td></tr>\n";
      }
      print "</table><br /><br />\n";
    }
    require $PGV_BASE_DIRECTORY.$pgv_language[$language1];
    require $PGV_BASE_DIRECTORY.$pgv_language[$language2];
    require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
    if (file_exists($PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];
    print  "<br /><br />" . "<a href=\"editlang.php\"><b>";
    print_text("lang_back");
    print "</b></a><br /><br />";
  }

print "</div>";

if (file_exists($INDEX_DIRECTORY . "lang_settings.php")) require($INDEX_DIRECTORY . "lang_settings.php");
else require($PGV_BASE_DIRECTORY . "includes/lang_settings_std.php");
require $PGV_BASE_DIRECTORY.$pgv_language["english"];
require $PGV_BASE_DIRECTORY.$pgv_language[$LANGUAGE];

print_footer();

?>