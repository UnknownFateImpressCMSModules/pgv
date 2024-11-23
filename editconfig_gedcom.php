<?php
/**
 * UI for online updating of the gedcom config file.
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
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: editconfig_gedcom.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require "config.php";
if (empty($action)) $action="";
if (!userGedcomAdmin(getUserName())) {
	header("Location: editgedcoms.php");
	exit;
}

require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];

if (!isset($_POST)) $_POST = $HTTP_POST_VARS;

// Remove slashes
if (isset($_POST["NEW_COMMON_NAMES_ADD"])) $_POST["NEW_COMMON_NAMES_ADD"] = stripslashes($_POST["NEW_COMMON_NAMES_ADD"]);
if (isset($_POST["NEW_COMMON_NAMES_REMOVE"])) $_POST["NEW_COMMON_NAMES_REMOVE"] = stripslashes($_POST["NEW_COMMON_NAMES_REMOVE"]);

if (empty($oldged)) $oldged = "";
if (!empty($ged)) {
	$GEDCOMPATH = $GEDCOMS[$ged]["path"];
	$gedcom_title = $GEDCOMS[$ged]["title"];
	$gedcom_config = $GEDCOMS[$ged]["config"];
	$gedcom_privacy = $GEDCOMS[$ged]["privacy"];
	$FILE = $ged;
	$oldged = $ged;
}
else {
	if (empty($_POST["GEDCOMPATH"])) {
		$GEDCOMPATH = "";
		$gedcom_title = "";
	}
	$gedcom_config = "config_gedcom.php";
	$gedcom_privacy = "privacy.php";
}

$USERLANG = $LANGUAGE;
$temp = $THEME_DIR;
require($gedcom_config);
if (!isset($_POST["GEDCOMLANG"])) $GEDCOMLANG = $LANGUAGE;
$LANGUAGE = $USERLANG;
$error_msg = "";

if (!file_exists($GEDCOMPATH)) $action="add";
if ($action=="update") {
	$errors = false;
	if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
	//-- only get the filename from the path
	$_POST["GEDCOMPATH"] = preg_replace('/\\\/','/',$_POST["GEDCOMPATH"]);
	$GEDCOMPATH = $_POST["GEDCOMPATH"];
	$slpos = strrpos($GEDCOMPATH, "/");
	if (!$slpos) $slpos = strrpos($GEDCOMPATH,"\\");
	if ($slpos) $FILE = substr($GEDCOMPATH, $slpos+1);
	else $FILE=$GEDCOMPATH;
	$newgedcom=false;
	$gedcom_config="config_gedcom.php";
	if (copy($gedcom_config, $INDEX_DIRECTORY.$FILE."_conf.php")) {
		$gedcom_config = "\${INDEX_DIRECTORY}".$FILE."_conf.php";
	}
	if (!file_exists($INDEX_DIRECTORY.$FILE."_priv.php")) {
		if (copy($gedcom_privacy, $INDEX_DIRECTORY.$FILE."_priv.php")) {
			$gedcom_privacy = "\${INDEX_DIRECTORY}".$FILE."_priv.php";
		}
	}
	if (!empty($oldged)) {
		$gedcom_privacy = $INDEX_DIRECTORY.$FILE."_priv.php";
		unset($GEDCOMS[$oldged]);
		if ($oldged!=$FILE) {
			$newgedcom = true;
			delete_gedcom($oldged);
		}
	}
	$gedarray = array();
	$gedarray["gedcom"] = $FILE;
	$gedarray["config"] = $gedcom_config;
	$gedarray["privacy"] = $gedcom_privacy;
	$gedarray["title"] = $gedcom_title;
	$gedarray["path"] =$GEDCOMPATH;
	// Check that add/remove common surnames are separated by [,;] blank
	$_POST["NEW_COMMON_NAMES_REMOVE"] = preg_replace("/[,;]\b/", ", ", $_POST["NEW_COMMON_NAMES_REMOVE"]);
	$_POST["NEW_COMMON_NAMES_ADD"] = preg_replace("/[,;]\b/", ", ", $_POST["NEW_COMMON_NAMES_ADD"]);
	$COMMON_NAMES_THRESHOLD = $_POST["NEW_COMMON_NAMES_THRESHOLD"];
	$COMMON_NAMES_ADD = $_POST["NEW_COMMON_NAMES_ADD"];
	$COMMON_NAMES_REMOVE = $_POST["NEW_COMMON_NAMES_REMOVE"];
	$gedarray["commonsurnames"] = "";
	$GEDCOMS[$FILE] = $gedarray;
	store_gedcoms();

	require($INDEX_DIRECTORY."gedcoms.php");
	$boolarray = array();
	$boolarray["yes"]="true";
	$boolarray["no"]="false";
	$boolarray[false]="false";
	$boolarray[true]="true";
	$configtext = implode('', file("config_gedcom.php"));

	$_POST["NEW_MEDIA_DIRECTORY"] = preg_replace('/\\\/','/',$_POST["NEW_MEDIA_DIRECTORY"]);
	$ct = preg_match("'/$'", $_POST["NEW_MEDIA_DIRECTORY"]);
	if ($ct==0) $_POST["NEW_MEDIA_DIRECTORY"] .= "/";
	if(preg_match("/.*[a-zA-Z]{1}:.*/",$_POST["NEW_MEDIA_DIRECTORY"])>0)
	{
		$errors = true;
	}
	if (preg_match("'://'", $_POST["NEW_HOME_SITE_URL"])==0) $_POST["NEW_HOME_SITE_URL"] = "http://".$_POST["NEW_HOME_SITE_URL"];
	$_POST["NEW_PEDIGREE_ROOT_ID"] = trim($_POST["NEW_PEDIGREE_ROOT_ID"]);

	/*
	-- do we need to check extensions and add a trailing slash to the HOME_SITE_URL?
	$extensions = array(".html", ".php", ".asp", ".shtm", ".phtml", ".htm");
	$i = FALSE;
	foreach ($extensions as $item => $ext) {
		if (stristr($_POST["NEW_HOME_SITE_URL"], $ext)) {
			$i = TRUE;
			break;
		}
	}
	if ($i != TRUE && preg_match("'/'", $_POST["NEW_HOME_SITE_URL"])==0) $_POST["NEW_HOME_SITE_URL"] .= "/";
	*/
	$configtext = preg_replace('/\$LANGUAGE\s*=\s*".*";/', "\$LANGUAGE = \"".$_POST["GEDCOMLANG"]."\";", $configtext);
	$configtext = preg_replace('/\$CALENDAR_FORMAT\s*=\s*".*";/', "\$CALENDAR_FORMAT = \"".$_POST["NEW_CALENDAR_FORMAT"]."\";", $configtext);
	$configtext = preg_replace('/\$DISPLAY_JEWISH_THOUSANDS\s*=\s*.*;/', "\$DISPLAY_JEWISH_THOUSANDS = ".$boolarray[$_POST["NEW_DISPLAY_JEWISH_THOUSANDS"]].";", $configtext);
	$configtext = preg_replace('/\$DISPLAY_JEWISH_GERESHAYIM\s*=\s*.*;/', "\$DISPLAY_JEWISH_GERESHAYIM = ".$boolarray[$_POST["NEW_DISPLAY_JEWISH_GERESHAYIM"]].";", $configtext);
	$configtext = preg_replace('/\$JEWISH_ASHKENAZ_PRONUNCIATION\s*=\s*.*;/', "\$JEWISH_ASHKENAZ_PRONUNCIATION = ".$boolarray[$_POST["NEW_JEWISH_ASHKENAZ_PRONUNCIATION"]].";", $configtext);
	$configtext = preg_replace('/\$USE_RTL_FUNCTIONS\s*=\s*.*;/', "\$USE_RTL_FUNCTIONS = ".$boolarray[$_POST["NEW_USE_RTL_FUNCTIONS"]].";", $configtext);
	$configtext = preg_replace('/\$CHARACTER_SET\s*=\s*".*";/', "\$CHARACTER_SET = \"".$_POST["NEW_CHARACTER_SET"]."\";", $configtext);
	$configtext = preg_replace('/\$ENABLE_MULTI_LANGUAGE\s*=\s*.*;/', "\$ENABLE_MULTI_LANGUAGE = ".$boolarray[$_POST["NEW_ENABLE_MULTI_LANGUAGE"]].";", $configtext);
	$configtext = preg_replace('/\$DEFAULT_PEDIGREE_GENERATIONS\s*=\s*".*";/', "\$DEFAULT_PEDIGREE_GENERATIONS = \"".$_POST["NEW_DEFAULT_PEDIGREE_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_PEDIGREE_GENERATIONS\s*=\s*".*";/', "\$MAX_PEDIGREE_GENERATIONS = \"".$_POST["NEW_MAX_PEDIGREE_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_DESCENDANCY_GENERATIONS\s*=\s*".*";/', "\$MAX_DESCENDANCY_GENERATIONS = \"".$_POST["NEW_MAX_DESCENDANCY_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$USE_RIN\s*=\s*.*;/', "\$USE_RIN = ".$boolarray[$_POST["NEW_USE_RIN"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_ROOT_ID\s*=\s*".*";/', "\$PEDIGREE_ROOT_ID = \"".$_POST["NEW_PEDIGREE_ROOT_ID"]."\";", $configtext);
	$configtext = preg_replace('/\$GEDCOM_ID_PREFIX\s*=\s*".*";/', "\$GEDCOM_ID_PREFIX = \"".$_POST["NEW_GEDCOM_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$FAM_ID_PREFIX\s*=\s*".*";/', "\$FAM_ID_PREFIX = \"".$_POST["NEW_FAM_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$SOURCE_ID_PREFIX\s*=\s*".*";/', "\$SOURCE_ID_PREFIX = \"".$_POST["NEW_SOURCE_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$REPO_ID_PREFIX\s*=\s*".*";/', "\$REPO_ID_PREFIX = \"".$_POST["NEW_REPO_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_FULL_DETAILS\s*=\s*.*;/', "\$PEDIGREE_FULL_DETAILS = ".$boolarray[$_POST["NEW_PEDIGREE_FULL_DETAILS"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_LAYOUT\s*=\s*.*;/', "\$PEDIGREE_LAYOUT = ".$boolarray[$_POST["NEW_PEDIGREE_LAYOUT"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_EMPTY_BOXES\s*=\s*.*;/', "\$SHOW_EMPTY_BOXES = ".$boolarray[$_POST["NEW_SHOW_EMPTY_BOXES"]].";", $configtext);
	$configtext = preg_replace('/\$ZOOM_BOXES\s*=\s*\".*\";/', "\$ZOOM_BOXES = \"".$_POST["NEW_ZOOM_BOXES"]."\";", $configtext);
	$configtext = preg_replace('/\$LINK_ICONS\s*=\s*\".*\";/', "\$LINK_ICONS = \"".$_POST["NEW_LINK_ICONS"]."\";", $configtext);
	$configtext = preg_replace('/\$ABBREVIATE_CHART_LABELS\s*=\s*.*;/', "\$ABBREVIATE_CHART_LABELS = ".$boolarray[$_POST["NEW_ABBREVIATE_CHART_LABELS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_PARENTS_AGE\s*=\s*.*;/', "\$SHOW_PARENTS_AGE = ".$boolarray[$_POST["NEW_SHOW_PARENTS_AGE"]].";", $configtext);
	$configtext = preg_replace('/\$HIDE_LIVE_PEOPLE\s*=\s*.*;/', "\$HIDE_LIVE_PEOPLE = ".$boolarray[$_POST["NEW_HIDE_LIVE_PEOPLE"]].";", $configtext);
	$configtext = preg_replace('/\$REQUIRE_AUTHENTICATION\s*=\s*.*;/', "\$REQUIRE_AUTHENTICATION = ".$boolarray[$_POST["NEW_REQUIRE_AUTHENTICATION"]].";", $configtext);
	$configtext = preg_replace('/\$WELCOME_TEXT_AUTH_MODE\s*=\s*".*";/', "\$WELCOME_TEXT_AUTH_MODE = \"".$_POST["NEW_WELCOME_TEXT_AUTH_MODE"]."\";", $configtext);
	$configtext = preg_replace('/\$WELCOME_TEXT_CUST_HEAD\s*=\s*.*;/', "\$WELCOME_TEXT_CUST_HEAD = ".$boolarray[$_POST["NEW_WELCOME_TEXT_CUST_HEAD"]].";", $configtext);
	$configtext = preg_replace('/\$WELCOME_TEXT_AUTH_MODE_4\s*=\s*".*";/', "\$WELCOME_TEXT_AUTH_MODE_4 = \"".$_POST["NEW_WELCOME_TEXT_AUTH_MODE_4"]."\";", $configtext);// new
	$configtext = preg_replace('/\$CHECK_CHILD_DATES\s*=\s*.*;/', "\$CHECK_CHILD_DATES = ".$boolarray[$_POST["NEW_CHECK_CHILD_DATES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_GEDCOM_RECORD\s*=\s*.*;/', "\$SHOW_GEDCOM_RECORD = ".$boolarray[$_POST["NEW_SHOW_GEDCOM_RECORD"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_EDIT_GEDCOM\s*=\s*.*;/', "\$ALLOW_EDIT_GEDCOM = ".$boolarray[$_POST["NEW_ALLOW_EDIT_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$ALPHA_INDEX_LISTS\s*=\s*.*;/', "\$ALPHA_INDEX_LISTS = ".$boolarray[$_POST["NEW_ALPHA_INDEX_LISTS"]].";", $configtext);
	$configtext = preg_replace('/\$NAME_FROM_GEDCOM\s*=\s*.*;/', "\$NAME_FROM_GEDCOM = ".$boolarray[$_POST["NEW_NAME_FROM_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_MARRIED_NAMES\s*=\s*.*;/', "\$SHOW_MARRIED_NAMES = ".$boolarray[$_POST["NEW_SHOW_MARRIED_NAMES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_ID_NUMBERS\s*=\s*.*;/', "\$SHOW_ID_NUMBERS = ".$boolarray[$_POST["NEW_SHOW_ID_NUMBERS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_FAM_ID_NUMBERS\s*=\s*.*;/', "\$SHOW_FAM_ID_NUMBERS = ".$boolarray[$_POST["NEW_SHOW_FAM_ID_NUMBERS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_PEDIGREE_PLACES\s*=\s*".*";/', "\$SHOW_PEDIGREE_PLACES = \"".$_POST["NEW_SHOW_PEDIGREE_PLACES"]."\";", $configtext);
	$configtext = preg_replace('/\$MULTI_MEDIA\s*=\s*.*;/', "\$MULTI_MEDIA = ".$boolarray[$_POST["NEW_MULTI_MEDIA"]].";", $configtext);
	$configtext = preg_replace('/\$MULTI_MEDIA_DB\s*=\s*.*;/', "\$MULTI_MEDIA_DB = ".$boolarray[$_POST["NEW_MULTI_MEDIA_DB"]].";", $configtext);
	$configtext = preg_replace('/\$MEDIA_EXTERNAL\s*=\s*.*;/', "\$MEDIA_EXTERNAL = ".$boolarray[$_POST["NEW_MEDIA_EXTERNAL"]].";", $configtext);
	$configtext = preg_replace('/\$MEDIA_DIRECTORY\s*=\s*".*";/', "\$MEDIA_DIRECTORY = \"".$_POST["NEW_MEDIA_DIRECTORY"]."\";", $configtext);
	$configtext = preg_replace('/\$MEDIA_DIRECTORY_LEVELS\s*=\s*".*";/', "\$MEDIA_DIRECTORY_LEVELS = \"".$_POST["NEW_MEDIA_DIRECTORY_LEVELS"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_HIGHLIGHT_IMAGES\s*=\s*.*;/', "\$SHOW_HIGHLIGHT_IMAGES = ".$boolarray[$_POST["NEW_SHOW_HIGHLIGHT_IMAGES"]].";", $configtext);
	$configtext = preg_replace('/\$USE_THUMBS_MAIN\s*=\s*.*;/', "\$USE_THUMBS_MAIN = ".$boolarray[$_POST["NEW_USE_THUMBS_MAIN"]].";", $configtext);
	$configtext = preg_replace('/\$HIDE_GEDCOM_ERRORS\s*=\s*.*;/', "\$HIDE_GEDCOM_ERRORS = ".$boolarray[$_POST["NEW_HIDE_GEDCOM_ERRORS"]].";", $configtext);
	$configtext = preg_replace('/\$WORD_WRAPPED_NOTES\s*=\s*.*;/', "\$WORD_WRAPPED_NOTES = ".$boolarray[$_POST["NEW_WORD_WRAPPED_NOTES"]].";", $configtext);
	$configtext = preg_replace('/\$GEDCOM_DEFAULT_TAB\s*=\s*".*";/', "\$GEDCOM_DEFAULT_TAB = \"".$_POST["NEW_GEDCOM_DEFAULT_TAB"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_CONTEXT_HELP\s*=\s*.*;/', "\$SHOW_CONTEXT_HELP = ".$boolarray[$_POST["NEW_SHOW_CONTEXT_HELP"]].";", $configtext);
	$configtext = preg_replace('/\$HOME_SITE_URL\s*=\s*".*";/', "\$HOME_SITE_URL = \"".$_POST["NEW_HOME_SITE_URL"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_THRESHOLD\s*=\s*".*";/', "\$COMMON_NAMES_THRESHOLD = \"".$_POST["NEW_COMMON_NAMES_THRESHOLD"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_ADD\s*=\s*".*";/', "\$COMMON_NAMES_ADD = \"".$_POST["NEW_COMMON_NAMES_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_REMOVE\s*=\s*".*";/', "\$COMMON_NAMES_REMOVE = \"".$_POST["NEW_COMMON_NAMES_REMOVE"]."\";", $configtext);
	$configtext = preg_replace('/\$HOME_SITE_TEXT\s*=\s*".*";/', "\$HOME_SITE_TEXT = \"".$_POST["NEW_HOME_SITE_TEXT"]."\";", $configtext);
	$configtext = preg_replace('/\$CONTACT_EMAIL\s*=\s*".*";/', "\$CONTACT_EMAIL = \"".$_POST["NEW_CONTACT_EMAIL"]."\";", $configtext);
	$configtext = preg_replace('/\$CONTACT_METHOD\s*=\s*".*";/', "\$CONTACT_METHOD = \"".$_POST["NEW_CONTACT_METHOD"]."\";", $configtext);
	$configtext = preg_replace('/\$WEBMASTER_EMAIL\s*=\s*".*";/', "\$WEBMASTER_EMAIL = \"".$_POST["NEW_WEBMASTER_EMAIL"]."\";", $configtext);
	$configtext = preg_replace('/\$SUPPORT_METHOD\s*=\s*".*";/', "\$SUPPORT_METHOD = \"".$_POST["NEW_SUPPORT_METHOD"]."\";", $configtext);
	$configtext = preg_replace('/\$FAVICON\s*=\s*".*";/', "\$FAVICON = \"".$_POST["NEW_FAVICON"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_STATS\s*=\s*.*;/', "\$SHOW_STATS = ".$boolarray[$_POST["NEW_SHOW_STATS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_COUNTER\s*=\s*.*;/', "\$SHOW_COUNTER = ".$boolarray[$_POST["NEW_SHOW_COUNTER"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_THEME_DROPDOWN\s*=\s*.*;/', "\$ALLOW_THEME_DROPDOWN = ".$boolarray[$_POST["NEW_ALLOW_THEME_DROPDOWN"]].";", $configtext);
	$configtext = preg_replace('/\$META_AUTHOR\s*=\s*".*";/', "\$META_AUTHOR = \"".$_POST["NEW_META_AUTHOR"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PUBLISHER\s*=\s*".*";/', "\$META_PUBLISHER = \"".$_POST["NEW_META_PUBLISHER"]."\";", $configtext);
	$configtext = preg_replace('/\$META_COPYRIGHT\s*=\s*".*";/', "\$META_COPYRIGHT = \"".$_POST["NEW_META_COPYRIGHT"]."\";", $configtext);
	$configtext = preg_replace('/\$META_DESCRIPTION\s*=\s*".*";/', "\$META_DESCRIPTION = \"".$_POST["NEW_META_DESCRIPTION"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PAGE_TOPIC\s*=\s*".*";/', "\$META_PAGE_TOPIC = \"".$_POST["NEW_META_PAGE_TOPIC"]."\";", $configtext);
	$configtext = preg_replace('/\$META_AUDIENCE\s*=\s*".*";/', "\$META_AUDIENCE = \"".$_POST["NEW_META_AUDIENCE"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PAGE_TYPE\s*=\s*".*";/', "\$META_PAGE_TYPE = \"".$_POST["NEW_META_PAGE_TYPE"]."\";", $configtext);
	$configtext = preg_replace('/\$META_ROBOTS\s*=\s*".*";/', "\$META_ROBOTS = \"".$_POST["NEW_META_ROBOTS"]."\";", $configtext);
	$configtext = preg_replace('/\$META_REVISIT\s*=\s*".*";/', "\$META_REVISIT = \"".$_POST["NEW_META_REVISIT"]."\";", $configtext);
	$configtext = preg_replace('/\$META_KEYWORDS\s*=\s*".*";/', "\$META_KEYWORDS = \"".$_POST["NEW_META_KEYWORDS"]."\";", $configtext);
	$configtext = preg_replace('/\$META_TITLE\s*=\s*".*";/', "\$META_TITLE = \"".$_POST["NEW_META_TITLE"]."\";", $configtext);
	$configtext = preg_replace('/\$META_SURNAME_KEYWORDS\s*=\s*.*;/', "\$META_SURNAME_KEYWORDS = ".$boolarray[$_POST["NEW_META_SURNAME_KEYWORDS"]].";", $configtext);
	$configtext = preg_replace('/\$CHART_BOX_TAGS\s*=\s*".*";/', "\$CHART_BOX_TAGS = \"".$_POST["NEW_CHART_BOX_TAGS"]."\";", $configtext);
	$configtext = preg_replace('/\$USE_QUICK_UPDATE\s*=\s*.*;/', "\$USE_QUICK_UPDATE = ".$boolarray[$_POST["NEW_USE_QUICK_UPDATE"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_QUICK_RESN\s*=\s*.*;/', "\$SHOW_QUICK_RESN = ".$boolarray[$_POST["NEW_SHOW_QUICK_RESN"]].";", $configtext);
	$configtext = preg_replace('/\$SEARCHLOG_CREATE\s*=\s*".*";/', "\$SEARCHLOG_CREATE = \"".$_POST["NEW_SEARCHLOG_CREATE"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_LDS_AT_GLANCE\s*=\s*.*;/', "\$SHOW_LDS_AT_GLANCE = ".$boolarray[$_POST["NEW_SHOW_LDS_AT_GLANCE"]].";", $configtext);
	$configtext = preg_replace('/\$UNDERLINE_NAME_QUOTES\s*=\s*.*;/', "\$UNDERLINE_NAME_QUOTES = ".$boolarray[$_POST["NEW_UNDERLINE_NAME_QUOTES"]].";", $configtext);
	$configtext = preg_replace('/\$SPLIT_PLACES\s*=\s*.*;/', "\$SPLIT_PLACES = ".$boolarray[$_POST["NEW_SPLIT_PLACES"]].";", $configtext);
	if (file_exists($NTHEME_DIR)) $configtext = preg_replace('/\$THEME_DIR\s*=\s*".*";/', "\$THEME_DIR = \"".$_POST["NTHEME_DIR"]."\";", $configtext);
	else {
		$errors = true;
	}
	$configtext = preg_replace('/\$TIME_LIMIT\s*=\s*".*";/', "\$TIME_LIMIT = \"".$_POST["NEW_TIME_LIMIT"]."\";", $configtext);
	if (!is_writable($INDEX_DIRECTORY.$FILE."_conf.php")) {
		$errors = true;
		$error_msg .= "<span class=\"error\"><b>Unable to write to $gedcom_config<br /></b></span>";
		$_SESSION[$gedcom_config]=$configtext;
		$error_msg .= "<br /><br /><a href=\"config_download.php?file=$gedcom_config\">".$pgv_lang["download_gedconf"]."</a> ".$pgv_lang["upload_to_index"]."$INDEX_DIRECTORY<br /><br />\n";
	}
	$fp = fopen($INDEX_DIRECTORY.$FILE."_conf.php", "wb");
	if (!$fp) {
		$errors = true;
		$error_msg .= "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $configtext);
		fclose($fp);
	}
	foreach($_POST as $key=>$value) {
		$key=preg_replace("/NEW_/", "", $key);
		if ($value=='yes') $$key=true;
		else if ($value=='no') $$key=false;
		else $$key=$value;
	}
	AddToLog("Gedcom configuration ".$gedcom_config." updated by >".getUserName()."<");
	if (!$errors) {
		$gednews = getUserNews($FILE);
		if (count($gednews)==0) {
			$news = array();
			$news["title"] = "#default_news_title#";
			$news["username"] = $FILE;
			$news["text"] = "#default_news_text#";
			$news["date"] = time()-$_SESSION["timediff"];
			addNews($news);
		}
		if (!check_for_import($FILE)) header("Location: validategedcom.php?ged=$FILE");
		else header("Location: editgedcoms.php");
		exit;
	}
}

//-- output starts here
$temp2 = $THEME_DIR;
$THEME_DIR = $temp;
print_header($pgv_lang["gedconf_head"]);
$THEME_DIR = $temp2;
if (!check_for_import($FILE)) print "<span class=\"subheaders\">".$pgv_lang["step2"]." ".$pgv_lang["configure"]." + ".$pgv_lang["ged_gedcom"]."</span><br /><br />";
if (!isset($NTHEME_DIR)) $NTHEME_DIR=$THEME_DIR;
if (!isset($themeselect)) $themeselect="";
?>
<script language="JavaScript" type="text/javascript">
<!--
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
	function getHelp(which) {
		if ((helpWin)&&(!helpWin.closed)) helpWin.location='editconfig_help.php?help='+which;
	}
	function closeHelp() {
		if (helpWin) helpWin.close();
	}
	function show_jewish(dbselect, sid) {
		var sbox = document.getElementById(sid);
		var sbox_style = sbox.style;

		if ((dbselect.options[dbselect.selectedIndex].value=='jewish')
			||(dbselect.options[dbselect.selectedIndex].value=='hebrew')
			||(dbselect.options[dbselect.selectedIndex].value=='jewish_and_gregorian')
			||(dbselect.options[dbselect.selectedIndex].value=='hebrew_and_gregorian')) {
			sbox_style.display='block';
		}
		else {
			sbox_style.display='none';
		}
	}
	var pasteto;
	function open_find(textbox) {
		pasteto = textbox;
		findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
//-->
</script>

<form method="post" name="configform" action="editconfig_gedcom.php">

<table class="facts_table, <?php print $TEXT_DIRECTION ?>", width="99%">
  <tr>
    <td colspan="2" class="facts_label"><?php 
    	print "<h2>".$pgv_lang["gedconf_head"]." - ".$GEDCOMS[$ged]["title"]. "</h2>";
    	?>
    </td>
  </tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="oldged" value="<?php print $oldged; ?>" />
<?php
	if (!empty($error_msg)) print "<br /><span class=\"error\">".$error_msg."</span><br />\n";
	$i = 0;
?>

<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('file-options');return false\"><img id=\"file-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["gedcom_conf"];
print "</a>";
?></td></tr></table/>
<div id="file-options" style="display: block">
<table class="facts_table">
		<td class="facts_label"><?php print $pgv_lang["gedcom_path"];?> <a href="#" onclick="return helpPopup('gedcom_path_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="GEDCOMPATH" value="<?php print preg_replace('/\\*/', '\\', $GEDCOMPATH);?>" size="40" dir ="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('gedcom_path_help');" />
		<?php
			if (!file_exists($GEDCOMPATH)) {
				if (strtolower(substr(trim($GEDCOMPATH), -4)) != ".ged") $GEDCOMPATH .= ".ged";
			}
			if ((!strstr($GEDCOMPATH, "://"))&&(!file_exists($GEDCOMPATH))) {
				$gedcomsplit = preg_split("/\//", $GEDCOMPATH);
				foreach ($gedcomsplit as $indexval => $gedcomname){
					if (stristr($gedcomname, "ged")){
						print "<br /><span class=\"error\">".str_replace("#GEDCOM#", $gedcomname, $pgv_lang["error_header"])."</span>\n";
					}
				}
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print_text("gedcom_title");?> <a href="#" onclick="return helpPopup('gedcom_title_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="gedcom_title" value="<?php print preg_replace("/\"/", "&quot;", PrintReady($gedcom_title)); ?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('gedcom_title_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["LANGUAGE"];?> <a href="#" onclick="return helpPopup('LANGUAGE_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="hidden" name="changelanguage" value="yes" />
		<select name="GEDCOMLANG" onfocus="getHelp('LANGUAGE_help');" tabindex="<?php $i++; print $i?>">
		<?php
			foreach ($pgv_language as $key=>$value) {
				if ($language_settings[$key]["pgv_lang_use"]) {
					print "\n\t\t\t<option value=\"$key\"";
					if ($GEDCOMLANG==$key) print " selected=\"selected\"";
					print ">".$pgv_lang[$key]."</option>";
				}
			}
		?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CHARACTER_SET"];?> <a href="#" onclick="return helpPopup('CHARACTER_SET_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_CHARACTER_SET" value="<?php print $CHARACTER_SET?>" onfocus="getHelp('CHARACTER_SET_help');" tabindex="<?php $i++; print $i?>" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PEDIGREE_ROOT_ID"];?> <a href="#" onclick="return helpPopup('PEDIGREE_ROOT_ID_help');"><b>?</b></a></td>
		<?php
		if ((!empty($GEDCOMPATH))&&(file_exists($GEDCOMPATH))&&(!empty($PEDIGREE_ROOT_ID))) {
			//-- the following section of code was modified from the find_record_in_file function of functions.php
			$fpged = fopen($GEDCOMPATH, "r");
			if ($fpged) {
				$gid = $PEDIGREE_ROOT_ID;
				$prefix = "";
				$suffix = $gid;
				$ct = preg_match("/^([a-zA-Z]+)/", $gid, $match);
				if ($ct>0) $prefix = $match[1];
				$ct = preg_match("/([\d\.]+)$/", $gid, $match);
				if ($ct>0) $suffix = $match[1];
				//print "prefix:$prefix suffix:$suffix";
				$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
				$fcontents = "";
				while(!feof($fpged)) {
					$fcontents = fread($fpged, $BLOCK_SIZE);
					//-- convert mac line endings
					$fcontents = preg_replace("/\r(\d)/", "\n$1", $fcontents);
					$ct = preg_match("/0 @(".$prefix."0*".$suffix.")@ INDI/", $fcontents, $match);
					if ($ct>0) {
						$gid = $match[1];
						$pos1 = strpos($fcontents, "0 @$gid@", 0);
						if ($pos1===false) $fcontents = "";
						else {
							$PEDIGREE_ROOT_ID = $gid;
							$pos2 = strpos($fcontents, "\n0", $pos1+1);
							while((!$pos2)&&(!feof($fpged))) {
								$fcontents .= fread($fpged, $BLOCK_SIZE);
								$pos2 = strpos($fcontents, "\n0", $pos1+1);
							}
							if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
							else $indirec = substr($fcontents, $pos1);
							break;
						}
					}
					else $fcontents = "";
				}
				fclose($fpged);
			}
		}
	?>
	<td class="facts_value"><input type="text" name="NEW_PEDIGREE_ROOT_ID" value="<?php print $PEDIGREE_ROOT_ID?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PEDIGREE_ROOT_ID_help');" />
			<?php
			if (!empty($indirec)) {
				if (!check_for_import($GEDCOM)) {
					$indilist[$PEDIGREE_ROOT_ID]["gedcom"] = $indirec;
					$indilist[$PEDIGREE_ROOT_ID]["names"] = get_indi_names($indirec);
					$indilist[$PEDIGREE_ROOT_ID]["isdead"] = 1;
					$indilist[$PEDIGREE_ROOT_ID]["file"] = $GEDCOM;
				}
				print "\n<span class=\"list_item\">".get_person_name($PEDIGREE_ROOT_ID);
				print_first_major_fact($PEDIGREE_ROOT_ID);
				print "</span>\n";
		    }
		    else {
				print "<span class=\"error\">";
				print $pgv_lang["unable_to_find_indi"];
				print "</span>";
			}
			if (check_for_import($GEDCOM)) print '<a href="#" onclick="open_find(document.configform.NEW_PEDIGREE_ROOT_ID); return false;"> '.$pgv_lang["find_id"].'</a>';
		?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CALENDAR_FORMAT"];?> <a href="#" onclick="return helpPopup('CALENDAR_FORMAT_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_CALENDAR_FORMAT" tabindex="<?php $i++; print $i?>"  onfocus="getHelp('CALENDAR_FORMAT_help');" onchange="show_jewish(this, 'hebrew-cal');">
				<option value="gregorian" <?php if ($CALENDAR_FORMAT=='gregorian') print "selected=\"selected\""; ?>><?php print $pgv_lang["gregorian"];?></option>
				<option value="julian" <?php if ($CALENDAR_FORMAT=='julian') print "selected=\"selected\""; ?>><?php print $pgv_lang["julian"];?></option>
				<option value="french" <?php if ($CALENDAR_FORMAT=='french') print "selected=\"selected\""; ?>><?php print $pgv_lang["config_french"];?></option>
				<option value="jewish" <?php if ($CALENDAR_FORMAT=='jewish') print "selected=\"selected\""; ?>><?php print $pgv_lang["jewish"];?></option>
				<option value="jewish_and_gregorian" <?php if ($CALENDAR_FORMAT=='jewish_and_gregorian') print "selected=\"selected\""; ?>><?php print $pgv_lang["jewish_and_gregorian"];?></option>
				<option value="hebrew" <?php if ($CALENDAR_FORMAT=='hebrew') print "selected=\"selected\""; ?>><?php print $pgv_lang["config_hebrew"];?></option>
				<option value="hebrew_and_gregorian" <?php if ($CALENDAR_FORMAT=='hebrew_and_gregorian') print "selected=\"selected\""; ?>><?php print $pgv_lang["hebrew_and_gregorian"];?></option>
				<option value="arabic" <?php if ($CALENDAR_FORMAT=='arabic') print "selected=\"selected\""; ?>><?php print $pgv_lang["arabic_cal"];?></option>
				<option value="hijri" <?php if ($CALENDAR_FORMAT=='hijri') print "selected=\"selected\""; ?>><?php print $pgv_lang["hijri"];?></option>
			</select>
		</td>
	</tr>
	</table>
	<div id="hebrew-cal" style="display: <?php if (($CALENDAR_FORMAT=='jewish')||($CALENDAR_FORMAT=='jewish_and_gregorian')||($CALENDAR_FORMAT=='hebrew')||($CALENDAR_FORMAT=='hebrew_and_gregorian')) print 'block'; else print 'none';?>;">
	<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DISPLAY_JEWISH_THOUSANDS"];?> <a href="#" onclick="return helpPopup('DISPLAY_JEWISH_THOUSANDS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_DISPLAY_JEWISH_THOUSANDS" onfocus="getHelp('DISPLAY_JEWISH_THOUSANDS_help');">
				<option value="yes" <?php if ($DISPLAY_JEWISH_THOUSANDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$DISPLAY_JEWISH_THOUSANDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DISPLAY_JEWISH_GERESHAYIM"];?> <a href="#" onclick="return helpPopup('DISPLAY_JEWISH_GERESHAYIM_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_DISPLAY_JEWISH_GERESHAYIM" onfocus="getHelp('DISPLAY_JEWISH_GERESHAYIM_help');">
				<option value="yes" <?php if ($DISPLAY_JEWISH_GERESHAYIM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$DISPLAY_JEWISH_GERESHAYIM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["JEWISH_ASHKENAZ_PRONUNCIATION"];?> <a href="#" onclick="return helpPopup('JEWISH_ASHKENAZ_PRONUNCIATION_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_JEWISH_ASHKENAZ_PRONUNCIATION" onfocus="getHelp('JEWISH_ASHKENAZ_PRONUNCIATION_help');">
				<option value="yes" <?php if ($JEWISH_ASHKENAZ_PRONUNCIATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$JEWISH_ASHKENAZ_PRONUNCIATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	</table>
	</div>
	<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["USE_RTL_FUNCTIONS"];?> <a href="#" onclick="return helpPopup('USE_RTL_FUNCTIONS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_USE_RTL_FUNCTIONS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_RTL_FUNCTIONS_help');">
				<option value="yes" <?php if ($USE_RTL_FUNCTIONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_RTL_FUNCTIONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["USE_RIN"];?> <a href="#" onclick="return helpPopup('USE_RIN_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_USE_RIN" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_RIN_help');">
				<option value="yes" <?php if ($USE_RIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_RIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["GEDCOM_ID_PREFIX"];?> <a href="#" onclick="return helpPopup('GEDCOM_ID_PREFIX_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_GEDCOM_ID_PREFIX" value="<?php print $GEDCOM_ID_PREFIX?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('GEDCOM_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["FAM_ID_PREFIX"];?> <a href="#" onclick="return helpPopup('FAM_ID_PREFIX_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_FAM_ID_PREFIX" value="<?php print $FAM_ID_PREFIX?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('FAM_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SOURCE_ID_PREFIX"];?> <a href="#" onclick="return helpPopup('SOURCE_ID_PREFIX_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_SOURCE_ID_PREFIX" value="<?php print $SOURCE_ID_PREFIX?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SOURCE_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["REPO_ID_PREFIX"];?> <a href="#" onclick="return helpPopup('REPO_ID_PREFIX_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_REPO_ID_PREFIX" value="<?php print $REPO_ID_PREFIX?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('REPO_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SEARCHLOG_CREATE"];?> <a href="#" onclick="return helpPopup('SEARCHLOG_CREATE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SEARCHLOG_CREATE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SEARCHLOG_CREATE_help');">
				<option value="none" <?php if ($SEARCHLOG_CREATE=="none") print "selected=\"selected\""; ?>><?php print $pgv_lang["no_logs"];?></option>
				<option value="daily" <?php if ($SEARCHLOG_CREATE=="daily") print "selected=\"selected\""; ?>><?php print $pgv_lang["daily"];?></option>
				<option value="weekly" <?php if ($SEARCHLOG_CREATE=="weekly") print "selected=\"selected\""; ?>><?php print $pgv_lang["weekly"];?></option>
				<option value="monthly" <?php if ($SEARCHLOG_CREATE=="monthly") print "selected=\"selected\""; ?>><?php print $pgv_lang["monthly"];?></option>
				<option value="yearly" <?php if ($SEARCHLOG_CREATE=="yearly") print "selected=\"selected\""; ?>><?php print $pgv_lang["yearly"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["TIME_LIMIT"];?> <a href="#" onclick="return helpPopup('TIME_LIMIT_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_TIME_LIMIT" value="<?php print $TIME_LIMIT?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('TIME_LIMIT_help');" /></td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('config-media');return false\"><img id=\"config-media_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["media_conf"];
print "</a>";
?></td></tr></table>
<div id="config-media" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MULTI_MEDIA"];?> <a href="#" onclick="return helpPopup('MULTI_MEDIA_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_MULTI_MEDIA" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MULTI_MEDIA_help');">
				<option value="yes" <?php if ($MULTI_MEDIA) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$MULTI_MEDIA) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MULTI_MEDIA_DB"];?> <a href="#" onclick="return helpPopup('MULTI_MEDIA_DB_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_MULTI_MEDIA_DB" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MULTI_MEDIA_DB_help');">
				<option value="yes" <?php if ($MULTI_MEDIA_DB) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$MULTI_MEDIA_DB) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MEDIA_EXTERNAL"];?> <a href="#" onclick="return helpPopup('MEDIA_EXTERNAL_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_MEDIA_EXTERNAL" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MEDIA_EXTERNAL_help');">
				<option value="yes" <?php if ($MEDIA_EXTERNAL) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$MEDIA_EXTERNAL) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MEDIA_DIRECTORY"];?> <a href="#" onclick="return helpPopup('MEDIA_DIRECTORY_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" size="50" name="NEW_MEDIA_DIRECTORY" value="<?php print $MEDIA_DIRECTORY?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MEDIA_DIRECTORY_help');" />
		<?php
		if(preg_match("/.*[a-zA-Z]{1}:.*/",$MEDIA_DIRECTORY)>0) print "<span class=\"error\">".$pgv_lang["media_drive_letter"]."</span>\n";
		?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MEDIA_DIRECTORY_LEVELS"];?> <a href="#" onclick="return helpPopup('MEDIA_DIRECTORY_LEVELS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_MEDIA_DIRECTORY_LEVELS" value="<?php print $MEDIA_DIRECTORY_LEVELS?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MEDIA_DIRECTORY_LEVELS_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_HIGHLIGHT_IMAGES"];?> <a href="#" onclick="return helpPopup('SHOW_HIGHLIGHT_IMAGES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_HIGHLIGHT_IMAGES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_HIGHLIGHT_IMAGES_help');">
				<option value="yes" <?php if ($SHOW_HIGHLIGHT_IMAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_HIGHLIGHT_IMAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["USE_THUMBS_MAIN"];?> <a href="#" onclick="return helpPopup('USE_THUMBS_MAIN_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_USE_THUMBS_MAIN" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_THUMBS_MAIN_help');">
				<option value="yes" <?php if ($USE_THUMBS_MAIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_THUMBS_MAIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('access-options');return false\"><img id=\"access-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["accpriv_conf"];
print "</a>";
?></td></tr></table>
<div id="access-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["HIDE_LIVE_PEOPLE"];?> <a href="#" onclick="return helpPopup('HIDE_LIVE_PEOPLE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_HIDE_LIVE_PEOPLE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('HIDE_LIVE_PEOPLE_help');">
				<option value="yes" <?php if ($HIDE_LIVE_PEOPLE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$HIDE_LIVE_PEOPLE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["REQUIRE_AUTHENTICATION"];?> <a href="#" onclick="return helpPopup('REQUIRE_AUTHENTICATION_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_REQUIRE_AUTHENTICATION" tabindex="<?php $i++; print $i?>" onfocus="getHelp('REQUIRE_AUTHENTICATION_help');">
				<option value="yes" <?php if ($REQUIRE_AUTHENTICATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$REQUIRE_AUTHENTICATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE"];?> <a href="#" onclick="return helpPopup('WELCOME_TEXT_AUTH_MODE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_WELCOME_TEXT_AUTH_MODE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_help');">
				<option value="1" <?php if ($WELCOME_TEXT_AUTH_MODE=='1') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT1"];?></option>
				<option value="2" <?php if ($WELCOME_TEXT_AUTH_MODE=='2') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT2"];?></option>
				<option value="3" <?php if ($WELCOME_TEXT_AUTH_MODE=='3') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT3"];?></option>
				<option value="4" <?php if ($WELCOME_TEXT_AUTH_MODE=='4') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT4"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST_HEAD"];?> <a href="#" onclick="return helpPopup('WELCOME_TEXT_AUTH_MODE_CUST_HEAD_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_WELCOME_TEXT_CUST_HEAD" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_CUST_HEAD_help');" tabindex="<?php $i++; print $i?>" >
				<option value="yes" <?php if ($WELCOME_TEXT_CUST_HEAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$WELCOME_TEXT_CUST_HEAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST"];?> <a href="#" onclick="return helpPopup('WELCOME_TEXT_AUTH_MODE_CUST_help');"><b>?</b></a></td>
		<td class="facts_value"><textarea name="NEW_WELCOME_TEXT_AUTH_MODE_4" rows="5" cols="60" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_CUST_help');"><?php print  $WELCOME_TEXT_AUTH_MODE_4 ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CHECK_CHILD_DATES"];?> <a href="#" onclick="return helpPopup('CHECK_CHILD_DATES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_CHECK_CHILD_DATES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('CHECK_CHILD_DATES_help');">
				<option value="yes" <?php if ($CHECK_CHILD_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$CHECK_CHILD_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('layout-options');return false\"><img id=\"layout-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["displ_conf"];
print "</a>";
?></td></tr></table/>
<div id="layout-options" style="display: none">

<table class="facts_table"><tr><td class="facts_value" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('layout-options2');return false\"><img id=\"layout-options2_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["displ_names_conf"];
print "</a>";
?></td></tr></table/>
<div id="layout-options2" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PEDIGREE_FULL_DETAILS"];?> <a href="#" onclick="return helpPopup('PEDIGREE_FULL_DETAILS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_PEDIGREE_FULL_DETAILS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PEDIGREE_FULL_DETAILS_help');">
				<option value="yes" <?php if ($PEDIGREE_FULL_DETAILS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$PEDIGREE_FULL_DETAILS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ABBREVIATE_CHART_LABELS"];?> <a href="#" onclick="return helpPopup('ABBREVIATE_CHART_LABELS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ABBREVIATE_CHART_LABELS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ABBREVIATE_CHART_LABELS_help');">
				<option value="yes" <?php if ($ABBREVIATE_CHART_LABELS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ABBREVIATE_CHART_LABELS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_PARENTS_AGE"];?> <a href="#" onclick="return helpPopup('SHOW_PARENTS_AGE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_PARENTS_AGE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_PARENTS_AGE_help');">
				<option value="yes" <?php if ($SHOW_PARENTS_AGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_PARENTS_AGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_LDS_AT_GLANCE"];?> <a href="#" onclick="return helpPopup('SHOW_LDS_AT_GLANCE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_LDS_AT_GLANCE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_LDS_AT_GLANCE_help');">
				<option value="yes" <?php if ($SHOW_LDS_AT_GLANCE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_LDS_AT_GLANCE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CHART_BOX_TAGS"];?> <a href="#" onclick="return helpPopup('CHART_BOX_TAGS_help');"><b>?</b></a></td>
		<td class="facts_value">
			<input type="text" size="50" name="NEW_CHART_BOX_TAGS" value="<?php print $CHART_BOX_TAGS?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('CHART_BOX_TAGS_help');" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_MARRIED_NAMES"];?> <a href="#" onclick="return helpPopup('SHOW_MARRIED_NAMES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_MARRIED_NAMES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_MARRIED_NAMES_help');">
				<option value="yes" <?php if ($SHOW_MARRIED_NAMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_MARRIED_NAMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["UNDERLINE_NAME_QUOTES"];?> <a href="#" onclick="return helpPopup('UNDERLINE_NAME_QUOTES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_UNDERLINE_NAME_QUOTES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('UNDERLINE_NAME_QUOTES_help');">
				<option value="yes" <?php if ($UNDERLINE_NAME_QUOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$UNDERLINE_NAME_QUOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_ID_NUMBERS"];?> <a href="#" onclick="return helpPopup('SHOW_ID_NUMBERS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_ID_NUMBERS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_ID_NUMBERS_help');">
				<option value="yes" <?php if ($SHOW_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_FAM_ID_NUMBERS"];?> <a href="#" onclick="return helpPopup('SHOW_FAM_ID_NUMBERS_help');"><b>?</b></a></td>
        <td class="facts_value"><select name="NEW_SHOW_FAM_ID_NUMBERS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_FAM_ID_NUMBERS_help');">
			<option value="yes" <?php if ($SHOW_FAM_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
            <option value="no" <?php if (!$SHOW_FAM_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
            </select>
        </td>
    </tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["NAME_FROM_GEDCOM"];?> <a href="#" onclick="return helpPopup('NAME_FROM_GEDCOM_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_NAME_FROM_GEDCOM" tabindex="<?php $i++; print $i?>" onfocus="getHelp('NAME_FROM_GEDCOM_help');">
				<option value="yes" <?php if ($NAME_FROM_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$NAME_FROM_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="facts_value" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('layout-options3');return false\"><img id=\"layout-options3_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["displ_comsurn_conf"];
print "</a>";
?></td></tr></table/>
<div id="layout-options3" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["COMMON_NAMES_THRESHOLD"];?> <a href="#" onclick="return helpPopup('COMMON_NAMES_THRESHOLD_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_COMMON_NAMES_THRESHOLD" value="<?php print $COMMON_NAMES_THRESHOLD?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('COMMON_NAMES_THRESHOLD_help');" /></td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["COMMON_NAMES_ADD"];?> <a href="#" onclick="return helpPopup('COMMON_NAMES_ADD_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_COMMON_NAMES_ADD" value="<?php print $COMMON_NAMES_ADD?>" size="50" tabindex="<?php $i++; print $i?>" onfocus="getHelp('COMMON_NAMES_ADD_help');" /></td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["COMMON_NAMES_REMOVE"];?> <a href="#" onclick="return helpPopup('COMMON_NAMES_REMOVE_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_COMMON_NAMES_REMOVE" value="<?php print $COMMON_NAMES_REMOVE?>" size="50" tabindex="<?php $i++; print $i?>" onfocus="getHelp('COMMON_NAMES_REMOVE_help');" /></td>
	</tr>
</table>
</div>


<table class="facts_table"><tr><td class="facts_value" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('layout-options4');return false\"><img id=\"layout-options4_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["displ_layout_conf"];
print "</a>";
?></td></tr></table/>
<div id="layout-options4" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DEFAULT_PEDIGREE_GENERATIONS"];?> <a href="#" onclick="return helpPopup('DEFAULT_PEDIGREE_GENERATIONS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_DEFAULT_PEDIGREE_GENERATIONS" value="<?php print $DEFAULT_PEDIGREE_GENERATIONS?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DEFAULT_PEDIGREE_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MAX_PEDIGREE_GENERATIONS"];?> <a href="#" onclick="return helpPopup('MAX_PEDIGREE_GENERATIONS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_MAX_PEDIGREE_GENERATIONS" value="<?php print $MAX_PEDIGREE_GENERATIONS?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MAX_PEDIGREE_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["MAX_DESCENDANCY_GENERATIONS"];?> <a href="#" onclick="return helpPopup('MAX_DESCENDANCY_GENERATIONS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_MAX_DESCENDANCY_GENERATIONS" value="<?php print $MAX_DESCENDANCY_GENERATIONS?>" size="5" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DMAX_DESCENDANCY_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PEDIGREE_LAYOUT"];?> <a href="#" onclick="return helpPopup('PEDIGREE_LAYOUT_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_PEDIGREE_LAYOUT" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PEDIGREE_LAYOUT_help');">
				<option value="yes" <?php if ($PEDIGREE_LAYOUT) print "selected=\"selected\""; ?>><?php print $pgv_lang["landscape"];?></option>
				<option value="no" <?php if (!$PEDIGREE_LAYOUT) print "selected=\"selected\""; ?>><?php print $pgv_lang["portrait"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_PEDIGREE_PLACES"];?> <a href="#" onclick="return helpPopup('SHOW_PEDIGREE_PLACES_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" size="5" name="NEW_SHOW_PEDIGREE_PLACES" value="<?php print $SHOW_PEDIGREE_PLACES; ?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_PEDIGREE_PLACES_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ZOOM_BOXES"];?> <a href="#" onclick="return helpPopup('ZOOM_BOXES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ZOOM_BOXES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ZOOM_BOXES_help');">
				<option value="disabled" <?php if ($ZOOM_BOXES=='disabled') print "selected=\"selected\""; ?>><?php print $pgv_lang["disabled"];?></option>
				<option value="mouseover" <?php if ($ZOOM_BOXES=='mouseover') print "selected=\"selected\""; ?>><?php print $pgv_lang["mouseover"];?></option>
				<option value="mousedown" <?php if ($ZOOM_BOXES=='mousedown') print "selected=\"selected\""; ?>><?php print $pgv_lang["mousedown"];?></option>
				<option value="click" <?php if ($ZOOM_BOXES=='click') print "selected=\"selected\""; ?>><?php print $pgv_lang["click"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["LINK_ICONS"];?> <a href="#" onclick="return helpPopup('LINK_ICONS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_LINK_ICONS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('LINK_ICONS_help');">
				<option value="disabled" <?php if ($LINK_ICONS=='disabled') print "selected=\"selected\""; ?>><?php print $pgv_lang["disabled"];?></option>
				<option value="mouseover" <?php if ($LINK_ICONS=='mouseover') print "selected=\"selected\""; ?>><?php print $pgv_lang["mouseover"];?></option>
				<option value="click" <?php if ($LINK_ICONS=='click') print "selected=\"selected\""; ?>><?php print $pgv_lang["click"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["GEDCOM_DEFAULT_TAB"];?> <a href="#" onclick="return helpPopup('GEDCOM_DEFAULT_TAB_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_GEDCOM_DEFAULT_TAB" tabindex="<?php $i++; print $i?>" onfocus="getHelp('GEDCOM_DEFAULT_TAB_help');">
				<option value="0" <?php if ($GEDCOM_DEFAULT_TAB==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1" <?php if ($GEDCOM_DEFAULT_TAB==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"];?></option>
				<option value="2" <?php if ($GEDCOM_DEFAULT_TAB==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3" <?php if ($GEDCOM_DEFAULT_TAB==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"];?></option>
				<option value="4" <?php if ($GEDCOM_DEFAULT_TAB==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALPHA_INDEX_LISTS"];?> <a href="#" onclick="return helpPopup('ALPHA_INDEX_LISTS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALPHA_INDEX_LISTS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALPHA_INDEX_LISTS_help');">
				<option value="yes" <?php if ($ALPHA_INDEX_LISTS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALPHA_INDEX_LISTS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>


<table class="facts_table"><tr><td class="facts_value" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('layout-options5');return false\"><img id=\"layout-options5_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["displ_hide_conf"];
print "</a>";
?></td></tr></table/>
<div id="layout-options5" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_EMPTY_BOXES"];?> <a href="#" onclick="return helpPopup('SHOW_EMPTY_BOXES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_EMPTY_BOXES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_EMPTY_BOXES_help');">
				<option value="yes" <?php if ($SHOW_EMPTY_BOXES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_EMPTY_BOXES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_GEDCOM_RECORD"];?> <a href="#" onclick="return helpPopup('SHOW_GEDCOM_RECORD_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_GEDCOM_RECORD" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_GEDCOM_RECORD_help');">
				<option value="yes" <?php if ($SHOW_GEDCOM_RECORD) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_GEDCOM_RECORD) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["HIDE_GEDCOM_ERRORS"];?> <a href="#" onclick="return helpPopup('HIDE_GEDCOM_ERRORS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_HIDE_GEDCOM_ERRORS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('HIDE_GEDCOM_ERRORS_help');">
				<option value="yes" <?php if ($HIDE_GEDCOM_ERRORS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$HIDE_GEDCOM_ERRORS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["WORD_WRAPPED_NOTES"];?> <a href="#" onclick="return helpPopup('WORD_WRAPPED_NOTES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_WORD_WRAPPED_NOTES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('WORD_WRAPPED_NOTES_help');">
				<option value="yes" <?php if ($WORD_WRAPPED_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$WORD_WRAPPED_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["FAVICON"];?> <a href="#" onclick="return helpPopup('FAVICON_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_FAVICON" value="<?php print $FAVICON?>" size="40" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('FAVICON_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_COUNTER"];?> <a href="#" onclick="return helpPopup('SHOW_COUNTER_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_COUNTER" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_COUNTER_help');">
				<option value="yes" <?php if ($SHOW_COUNTER) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_COUNTER) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_STATS"];?> <a href="#" onclick="return helpPopup('SHOW_STATS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_STATS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_STATS_help');">
				<option value="yes" <?php if ($SHOW_STATS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_STATS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>
</div>
<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('user-options');return false\"><img id=\"user-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["useropt_conf"];
print "</a>";
?></td></tr></table/>
<div id="user-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALLOW_EDIT_GEDCOM"];?> <a href="#" onclick="return helpPopup('ALLOW_EDIT_GEDCOM_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALLOW_EDIT_GEDCOM" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_EDIT_GEDCOM_help');">
				<option value="yes" <?php if ($ALLOW_EDIT_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_EDIT_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SPLIT_PLACES"];?> <a href="#" onclick="return helpPopup('SPLIT_PLACES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SPLIT_PLACES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SPLIT_PLACES_help');">
				<option value="yes" <?php if ($SPLIT_PLACES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SPLIT_PLACES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print_text("USE_QUICK_UPDATE");?> <a href="#" onclick="return helpPopup('USE_QUICK_UPDATE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_USE_QUICK_UPDATE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_QUICK_UPDATE_help');">
				<option value="yes" <?php if ($USE_QUICK_UPDATE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_QUICK_UPDATE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print_text("SHOW_QUICK_RESN");?> <a href="#" onclick="return helpPopup('SHOW_QUICK_RESN_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_QUICK_RESN" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_QUICK_RESN_help');">
				<option value="yes" <?php if ($SHOW_QUICK_RESN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_QUICK_RESN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ENABLE_MULTI_LANGUAGE"];?> <a href="#" onclick="return helpPopup('ENABLE_MULTI_LANGUAGE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ENABLE_MULTI_LANGUAGE" onfocus="getHelp('ENABLE_MULTI_LANGUAGE_help');" tabindex="<?php $i++; print $i?>" >
				<option value="yes" <?php if ($ENABLE_MULTI_LANGUAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ENABLE_MULTI_LANGUAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SHOW_CONTEXT_HELP"];?> <a href="#" onclick="return helpPopup('SHOW_CONTEXT_HELP_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SHOW_CONTEXT_HELP" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SHOW_CONTEXT_HELP_help');">
				<option value="yes" <?php if ($SHOW_CONTEXT_HELP) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$SHOW_CONTEXT_HELP) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["THEME_DIR"];?> <a href="#" onclick="return helpPopup('THEME_DIR_help');"><b>?</b></a></td>
		<td class="facts_value">
			<select name="themeselect" tabindex="<?php $i++; print $i?>"  onchange="document.configform.NTHEME_DIR.value=document.configform.themeselect.options[document.configform.themeselect.selectedIndex].value;">
				<?php
					$themes = get_theme_names();
					foreach($themes as $indexval => $themedir) {
						print "<option value=\"".$themedir["dir"]."\"";
						if ($themedir["dir"] == $NTHEME_DIR) print " selected=\"selected\"";
						print ">".$themedir["name"]."</option>\n";
					}
				?>
				<option value="themes/" <?php if($themeselect=="themes//") print "selected=\"selected\""; ?>><?php print $pgv_lang["other_theme"]; ?></option>
			</select>
			<input type="text" name="NTHEME_DIR" value="<?php print $NTHEME_DIR?>" size="40" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('THEME_DIR_help');" />
	<?php
	if (!file_exists($NTHEME_DIR)) {
		print "<span class=\"error\">$NTHEME_DIR ";
		print $pgv_lang["does_not_exist"];
		print "</span>\n";
		$NTHEME_DIR=$THEME_DIR;
	}
	?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALLOW_THEME_DROPDOWN"];?> <a href="#" onclick="return helpPopup('ALLOW_THEME_DROPDOWN_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALLOW_THEME_DROPDOWN" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_THEME_DROPDOWN_help');">
				<option value="yes" <?php if ($ALLOW_THEME_DROPDOWN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_THEME_DROPDOWN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>



<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('contact-options');return false\"><img id=\"contact-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["contact_conf"];
print "</a>";
?></td></tr></table/>
<div id="contact-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CONTACT_EMAIL"];?> <a href="#" onclick="return helpPopup('CONTACT_EMAIL_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_CONTACT_EMAIL" tabindex="<?php $i++; print $i?>" onfocus="getHelp('CONTACT_EMAIL_help');">
		<?php
			if ($CONTACT_EMAIL=="you@yourdomain.com") $CONTACT_EMAIL = getUserName();
			$users = getUsers();
			foreach($users as $indexval => $user) {
				if ($user["verified_by_admin"]=="yes") {
					print "<option value=\"".$user["username"]."\"";
					if ($CONTACT_EMAIL==$user["username"]) print " selected=\"selected\"";
					print ">".$user["fullname"]." - ".$user["username"]."</option>\n";
				}
			}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["CONTACT_METHOD"];?> <a href="#" onclick="return helpPopup('CONTACT_METHOD_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_CONTACT_METHOD" tabindex="<?php $i++; print $i?>" onfocus="getHelp('CONTACT_METHOD_help');">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($CONTACT_METHOD=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" <?php if ($CONTACT_METHOD=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"];?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($CONTACT_METHOD=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"];?></option>
		<?php } ?>
				<option value="mailto" <?php if ($CONTACT_METHOD=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"];?></option>
				<option value="none" <?php if ($CONTACT_METHOD=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["WEBMASTER_EMAIL"];?> <a href="#" onclick="return helpPopup('WEBMASTER_EMAIL_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_WEBMASTER_EMAIL" tabindex="<?php $i++; print $i?>" onfocus="getHelp('WEBMASTER_EMAIL_help');">
		<?php
			$users = getUsers();
			if ($WEBMASTER_EMAIL=="webmaster@yourdomain.com") $WEBMASTER_EMAIL = getUserName();
			uasort($users, "usersort");
			foreach($users as $indexval => $user) {
				if (userIsAdmin($user["username"])) {
					print "<option value=\"".$user["username"]."\"";
					if ($WEBMASTER_EMAIL==$user["username"]) print " selected=\"selected\"";
					print ">".$user["fullname"]." - ".$user["username"]."</option>\n";
				}
			}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SUPPORT_METHOD"];?> <a href="#" onclick="return helpPopup('SUPPORT_METHOD_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_SUPPORT_METHOD" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SUPPORT_METHOD_help');">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($SUPPORT_METHOD=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" <?php if ($SUPPORT_METHOD=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"];?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($SUPPORT_METHOD=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"];?></option>
		<?php } ?>
				<option value="mailto" <?php if ($SUPPORT_METHOD=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"];?></option>
				<option value="none" <?php if ($SUPPORT_METHOD=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"];?></option>
			</select>
		</td>
	</tr>
</table>
</div>
<table class="facts_table"><tr><td class="facts_label03" style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
<?php
print "<a href=\"#\" onclick=\"expand_layer('config-meta');return false\"><img id=\"config-meta_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /> ";
print $pgv_lang["meta_conf"];
print " </a>";
?></td></tr></table/>
<div id="config-meta" style="display: none">
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["HOME_SITE_URL"];?> <a href="#" onclick="return helpPopup('HOME_SITE_URL_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_HOME_SITE_URL" value="<?php print $HOME_SITE_URL?>" size="50" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('HOME_SITE_URL_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["HOME_SITE_TEXT"];?> <a href="#" onclick="return helpPopup('HOME_SITE_TEXT_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_HOME_SITE_TEXT" value="<?php print htmlspecialchars($HOME_SITE_TEXT);?>" size="50" tabindex="<?php $i++; print $i?>" onfocus="getHelp('HOME_SITE_TEXT_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_AUTHOR"];?> <a href="#" onclick="return helpPopup('META_AUTHOR_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_AUTHOR" value="<?php print $META_AUTHOR?>" onfocus="getHelp('META_AUTHOR_help');" tabindex="<?php $i++; print $i?>" /><br />
		<?php print print_text("META_AUTHOR_descr"); ?></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_PUBLISHER"]?> <a href="#" onclick="return helpPopup('META_PUBLISHER_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_PUBLISHER" value="<?php print $META_PUBLISHER?>" onfocus="getHelp('META_PUBLISHER_help');" tabindex="<?php $i++; print $i?>" /><br />
		<?php print print_text("META_PUBLISHER_descr"); ?></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_COPYRIGHT"];?> <a href="#" onclick="return helpPopup('META_COPYRIGHT_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_COPYRIGHT" value="<?php print $META_COPYRIGHT?>" onfocus="getHelp('META_COPYRIGHT_help');" tabindex="<?php $i++; print $i?>" /><br />
		<?php print print_text("META_COPYRIGHT_descr"); ?></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_DESCRIPTION"];?> <a href="#" onclick="return helpPopup('META_DESCRIPTION_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_DESCRIPTION" value="<?php print $META_DESCRIPTION?>" onfocus="getHelp('META_DESCRIPTION_help');" tabindex="<?php $i++; print $i?>" /><br />
		<?php print $pgv_lang["META_DESCRIPTION_descr"]; ?></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_PAGE_TOPIC"];?> <a href="#" onclick="return helpPopup('META_PAGE_TOPIC_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_PAGE_TOPIC" value="<?php print $META_PAGE_TOPIC?>" onfocus="getHelp('META_PAGE_TOPIC_help');" tabindex="<?php $i++; print $i?>" /><br />
		<?php print $pgv_lang["META_PAGE_TOPIC_descr"]; ?></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_AUDIENCE"];?> <a href="#" onclick="return helpPopup('META_AUDIENCE_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_AUDIENCE" value="<?php print $META_AUDIENCE?>" onfocus="getHelp('META_AUDIENCE_help');" tabindex="<?php $i++; print $i?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_PAGE_TYPE"];?> <a href="#" onclick="return helpPopup('META_PAGE_TYPE_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_PAGE_TYPE" value="<?php print $META_PAGE_TYPE?>" onfocus="getHelp('META_PAGE_TYPE_help');" tabindex="<?php $i++; print $i?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_ROBOTS"];?> <a href="#" onclick="return helpPopup('META_ROBOTS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_ROBOTS" value="<?php print $META_ROBOTS?>" onfocus="getHelp('META_ROBOTS_help');" tabindex="<?php $i++; print $i?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_REVISIT"];?> <a href="#" onclick="return helpPopup('META_REVISIT_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_REVISIT" value="<?php print $META_REVISIT?>" onfocus="getHelp('META_REVISIT_help');" tabindex="<?php $i++; print $i?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_KEYWORDS"];?> <a href="#" onclick="return helpPopup('META_KEYWORDS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_KEYWORDS" value="<?php print $META_KEYWORDS?>" onfocus="getHelp('META_KEYWORDS_help');" tabindex="<?php $i++; print $i?>" size="75" /><br />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_SURNAME_KEYWORDS"];?> <a href="#" onclick="return helpPopup('META_SURNAME_KEYWORDS_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_META_SURNAME_KEYWORDS" tabindex="<?php $i++; print $i?>" onfocus="getHelp('META_SURNAME_KEYWORDS_help');">
				<option value="yes" <?php if ($META_SURNAME_KEYWORDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$META_SURNAME_KEYWORDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["META_TITLE"];?> <a href="#" onclick="return helpPopup('META_TITLE_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_META_TITLE" value="<?php print $META_TITLE?>" onfocus="getHelp('META_TITLE_help');" tabindex="<?php $i++; print $i?>" size="75" /></td>
	</tr>
</table>
</div>
<table class="facts_table" border="0">
<tr><td><br /></td></tr>
<tr><td>
<input type="submit" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["save_config"]?>" onclick="closeHelp();">
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["reset"]?>" /><br />
</td></tr>
</table>
</form>
<br /><?php if (!check_for_import($FILE)) print_text("return_editconfig_gedcom"); ?><br />
<?php if (count($GEDCOMS)==0) { ?>
<script language="JavaScript" type="text/javascript">
	helpPopup('welcome_new_help');
</script>
<?php
}

print_footer();

?>