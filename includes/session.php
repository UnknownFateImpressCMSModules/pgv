<?php
/**
 * Startup and session logic
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
 * @subpackage Reports
 * @version $Id: session.php,v 1.1 2005/10/07 18:08:22 skenow Exp $
 */
if (strstr($_SERVER["PHP_SELF"],"session")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

//-- check for the sanity worm to save bandwidth
if (eregi("LWP::Simple",getenv("HTTP_USER_AGENT"),$regs) or eregi("lwp-trivial",getenv("HTTP_USER_AGENT"),$regs)
		|| eregi("HTTrack",getenv("HTTP_USER_AGENT"),$regs)) {
	print "Bad Worm! Bad!  Crawl back into your hole.";
	exit;
}

@ini_set('arg_separator.output', '&amp;');
@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', '1');

//-- version of phpgedview
$VERSION = "3.3.5";
$VERSION_RELEASE = "final";
$REQUIRED_PRIVACY_VERSION = "3.1";
$REQUIRED_CONFIG_VERSION = "3.1";

set_magic_quotes_runtime(0);

if (phpversion()<4.1) {
	//-- detect old versions of PHP and display error message
	//-- cannot add this to the language files because the language has not been established yet.
	print "<html>\n<body><b style=\"color: red;\">PhpGedView requires PHP version 4.1.0 or later.</b><br /><br />\nYour server is running PHP version ".phpversion().".  Please ask your server's Administrator to upgrade the PHP installation.</body></html>";
	exit;
}

if ((empty($PHP_SELF))&&(!empty($_SERVER["PHP_SELF"]))) $PHP_SELF=$_SERVER["PHP_SELF"];
if (!empty($_SERVER["QUERY_STRING"])) $QUERY_STRING = $_SERVER["QUERY_STRING"];
else $QUERY_STRING="";
$QUERY_STRING = preg_replace(array("/&/","/</"), array("&amp;","&lt;"), $QUERY_STRING);
$QUERY_STRING = preg_replace("/show_context_help=(no|yes)/", "", $QUERY_STRING);

//-- if not configured then redirect to the configuration script
if (!$CONFIGURED) {
   if ((strstr($PHP_SELF, "admin.php")===false)
   &&(strstr($PHP_SELF, "login.php")===false)
   &&(strstr($PHP_SELF, "editconfig.php")===false)
   &&(strstr($PHP_SELF, "config_download.php")===false)
   &&(strstr($PHP_SELF, "editconfig_help.php")===false)) {
      header("Location: editconfig.php");
      exit;
   }
}
//-- allow user to cancel
ignore_user_abort(false);

//-- check if they are trying to hack
$CONFIG_VARS = array();
$CONFIG_VARS[] = "PGV_BASE_DIRECTORY";
$CONFIG_VARS[] = "PGV_DATABASE";
$CONFIG_VARS[] = "DBTYPE";
$CONFIG_VARS[] = "DBHOST";
$CONFIG_VARS[] = "DBUSER";
$CONFIG_VARS[] = "DBPASS";
$CONFIG_VARS[] = "DBNAME";
$CONFIG_VARS[] = "TBLPREFIX";
$CONFIG_VARS[] = "INDEX_DIRECTORY";
$CONFIG_VARS[] = "AUTHENTICATION_MODULE";
$CONFIG_VARS[] = "USE_REGISTRATION_MODULE";
$CONFIG_VARS[] = "ALLOW_USER_THEMES";
$CONFIG_VARS[] = "ALLOW_REMEMBER_ME";
$CONFIG_VARS[] = "DEFAULT_GEDCOM";
$CONFIG_VARS[] = "ALLOW_CHANGE_GEDCOM";
$CONFIG_VARS[] = "LOGFILE_CREATE";
$CONFIG_VARS[] = "PGV_SESSION_SAVE_PATH";
$CONFIG_VARS[] = "PGV_SESSION_TIME";
$CONFIG_VARS[] = "GEDCOMS";
$CONFIG_VARS[] = "SERVER_URL";
$CONFIG_VARS[] = "LOGIN_URL";
$CONFIG_VARS[] = "PGV_MEMORY_LIMIT";
$CONFIG_VARS[] = "PGV_STORE_MESSAGES";
$CONFIG_VARS[] = "PGV_SIMPLE_MAIL";
$CONFIG_VARS[] = "CONFIG_VERSION";
$CONFIG_VARS[] = "CONFIGURED";

foreach($CONFIG_VARS as $indexval => $VAR) {
	$incoming = array_keys($_REQUEST);
	if (in_array($VAR, $incoming)) {
		print "Config variable override detected. Possible hacking attempt. Script terminated.\n";
		if ((!ini_get('register_globals'))||(ini_get('register_globals')=="Off")) {
			//--load common functions
			require_once("includes/functions.php");
			//-- load db specific functions
			require_once("includes/functions_$PGV_DATABASE.php");
			require_once("includes/".$AUTHENTICATION_MODULE);      // -- load the authentication system
			AddToLog("Config variable override detected. Possible hacking attempt. Script terminated.");
		}
		exit;
	}
}

if (empty($CONFIG_VERSION)) $CONFIG_VERSION = "2.65";
if (empty($SERVER_URL)) $SERVER_URL = stripslashes("http://".$_SERVER["SERVER_NAME"].dirname($PHP_SELF)."/");
if (!isset($ALLOW_REMEMBER_ME)) $ALLOW_REMEMBER_ME = true;
if (!isset($PGV_SIMPLE_MAIL)) $PGV_SIMPLE_MAIL = false;

if (empty($PGV_MEMORY_LIMIT)) $PGV_MEMORY_LIMIT = "32M";
@ini_set('memory_limit', $PGV_MEMORY_LIMIT);

//-- backwards compatibility with v < 3.1
if ($PGV_DATABASE=="mysql") {
	$PGV_DATABASE = 'db';
	$DBTYPE = 'mysql';
}
//--load common functions
require_once($PGV_BASE_DIRECTORY."includes/functions.php");
//-- load db specific functions
require_once($PGV_BASE_DIRECTORY."includes/functions_".$PGV_DATABASE.".php");

//-- setup execution timer
$start_time = getmicrotime();

//-- start the php session
$time = time()+$PGV_SESSION_TIME;
$date = date("D M j H:i:s T Y", $time);
session_set_cookie_params($date, "/");
if (($PGV_SESSION_TIME>0)&&(function_exists('session_cache_expire'))) session_cache_expire($PGV_SESSION_TIME/60);
if (!empty($PGV_SESSION_SAVE_PATH)) session_save_path($PGV_SESSION_SAVE_PATH);
@session_start();

//-- import the post, get, and cookie variable into the scope on new versions of php
if (phpversion() >= '4.1') {
	@import_request_variables("cgp");
}
if (phpversion() > '4.2.2') {
	//-- prevent sql and code injection
	foreach($_REQUEST as $key=>$value) {
		if (!is_array($value)) {
			if ($PGV_DATABASE!="index") {
				if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,200}(\s(FROM)|(INTO)|(TABLE)\s)/i", $value, $imatch)>0) {
					print "Possible SQL injection detected: $key=>$value.  <b>$imatch[0]</b> Script terminated.";
					require_once("includes/".$AUTHENTICATION_MODULE);      // -- load the authentication system
					AddToLog("Possible SQL injection detected: $key=>$value. <b>$imatch[0]</b> Script terminated.");
					exit;
				}
			}
			//-- don't let any html in
			if (!empty($value)) ${$key} = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $value);
		}
		else {
			foreach($value as $key1=>$val) {
				if (!is_array($val)) {
					if ($PGV_DATABASE!="index") {
						if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,200}(\s(FROM)|(INTO)|(TABLE)\s)/i", $val, $imatch)>0) {
							print "Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.";
							require_once("includes/".$AUTHENTICATION_MODULE);      // -- load the authentication system
							AddToLog("Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.");
							exit;
						}
					}
					//-- don't let any html in
					if (!empty($val)) ${$key}[$key1] = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $val);
				}
			}
		}
	}
}
//-- import the gedcoms array
if (file_exists($INDEX_DIRECTORY."gedcoms.php")) {
	require_once($INDEX_DIRECTORY."gedcoms.php");
	if (!is_array($GEDCOMS)) $GEDCOMS = array();
	foreach ($GEDCOMS as $key => $gedcom) {
		$GEDCOMS[$key]["commonsurnames"] = stripslashes($gedcom["commonsurnames"]);
	}
}
else if (floor($CONFIG_VERSION)==floor($VERSION)) $GEDCOMS=array();

if (isset($_REQUEST["GEDCOM"])){
   $_REQUEST["GEDCOM"] = trim($_REQUEST["GEDCOM"]);
}
if (!isset($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = "";
if (empty($_REQUEST["GEDCOM"])) {
   if (isset($_SESSION["GEDCOM"])) $GEDCOM = $_SESSION["GEDCOM"];
   else {
      if ((empty($GEDCOM))||(empty($GEDCOMS[$GEDCOM]))) $GEDCOM=$DEFAULT_GEDCOM;
      else if ((empty($GEDCOM))&&(count($GEDCOMS)>0)) {
	      check_db();
         foreach($GEDCOMS as $ged_file=>$ged_array) {
	         $GEDCOM = $ged_file;
	         if (check_for_import($ged_file)) break;
         }
      }
   }
}
else {
	$GEDCOM = $_REQUEST["GEDCOM"];
}
if (isset($_REQUEST["ged"])) {
	$GEDCOM = trim($_REQUEST["ged"]);
}
$_SESSION["GEDCOM"] = $GEDCOM;
$INDILIST_RETRIEVED = false;
$FAMLIST_RETRIEVED = false;

require_once($PGV_BASE_DIRECTORY."config_gedcom.php");
require_once(get_config_file());

require_once($PGV_BASE_DIRECTORY."includes/functions_name.php");

/**
 * do not include print functions when using the gdbi protocol
 */
if (strstr($PHP_SELF, "client.php")===false) {
	require_once($PGV_BASE_DIRECTORY."includes/functions_print.php");
	require_once($PGV_BASE_DIRECTORY."includes/functions_rtl.php");
	require_once($PGV_BASE_DIRECTORY."includes/functions_date.php");
}

if (empty($PEDIGREE_GENERATIONS)) $PEDIGREE_GENERATIONS = $DEFAULT_PEDIGREE_GENERATIONS;
//-- load file for language settings
require_once($PGV_BASE_DIRECTORY . "includes/lang_settings_std.php");
$Languages_Default = true;
if (file_exists($INDEX_DIRECTORY . "lang_settings.php")) {
	require_once($INDEX_DIRECTORY . "lang_settings.php");
	$Languages_Default = false;
}

if (($ENABLE_MULTI_LANGUAGE)&&(empty($_SESSION["CLANGUAGE"]))) {
   if (isset($HTTP_ACCEPT_LANGUAGE)) $accept_langs = $HTTP_ACCEPT_LANGUAGE;
   else if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accept_langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
   if (isset($accept_langs)) {
      if (strstr($accept_langs, ",")) {
         $langs_array = preg_split("/(,\s*)|(;\s*)/", $accept_langs);
        for ($i=0; $i<count($langs_array); $i++) {
            if (!empty($langcode[$langs_array[$i]])) {
               $LANGUAGE = $langcode[$langs_array[$i]];
               break;
            }
         }
      }
      else {
         if (!empty($langcode[$accept_langs])) $LANGUAGE = $langcode[$accept_langs];
      }
   }
}
$deflang = $LANGUAGE;

require_once("includes/".$AUTHENTICATION_MODULE);      // -- load the authentication system
if (!isset($pgv_username)) $pgv_username = getUserName();

if (!empty($_SESSION['CLANGUAGE'])) $CLANGUAGE = $_SESSION['CLANGUAGE'];
else if (!empty($HTTP_SESSION_VARS['CLANGUAGE'])) $CLANGUAGE = $HTTP_SESSION_VARS['CLANGUAGE'];
if (!empty($CLANGUAGE)) {
   $LANGUAGE = $CLANGUAGE;
}

if ($ENABLE_MULTI_LANGUAGE) {
	if ((isset($changelanguage))&&($changelanguage=="yes")) {
		if (!empty($NEWLANGUAGE) && isset($pgv_language[$NEWLANGUAGE])) {
			$LANGUAGE=$NEWLANGUAGE;
			unset($_SESSION["upcoming_events"]);
			unset($_SESSION["todays_events"]);
		}
	}
}

require_once($PGV_BASE_DIRECTORY . $pgv_language["english"]);	//-- load english as the default language
if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require_once($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
require_once($PGV_BASE_DIRECTORY . "includes/templecodes.php");		//-- load in the LDS temple code translations

require_once("privacy.php");
//-- load the privacy file
require_once(get_privacy_file());
//-- load the privacy functions
require_once($PGV_BASE_DIRECTORY."includes/functions_privacy.php");

if (!isset($PHP_SELF)) $PHP_SELF=$_SERVER["PHP_SELF"];

if (empty($TEXT_DIRECTION)) $TEXT_DIRECTION="ltr";
$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
$WEEK_START	= $WEEK_START_array[$LANGUAGE];
$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

$monthtonum = array();
$monthtonum["jan"] = 1;
$monthtonum["feb"] = 2;
$monthtonum["mar"] = 3;
$monthtonum["apr"] = 4;
$monthtonum["may"] = 5;
$monthtonum["jun"] = 6;
$monthtonum["jul"] = 7;
$monthtonum["aug"] = 8;
$monthtonum["sep"] = 9;
$monthtonum["oct"] = 10;
$monthtonum["nov"] = 11;
$monthtonum["dec"] = 12;
$monthtonum["tsh"] = 1;
$monthtonum["csh"] = 2;
$monthtonum["ksl"] = 3;
$monthtonum["tvt"] = 4;
$monthtonum["shv"] = 5;
$monthtonum["adr"] = 6;
$monthtonum["ads"] = 7;
$monthtonum["nsn"] = 8;
$monthtonum["iyr"] = 9;
$monthtonum["svn"] = 10;
$monthtonum["tmz"] = 11;
$monthtonum["aav"] = 12;
$monthtonum["ell"] = 13;

if (!isset($show_context_help)) $show_context_help = "";
if (!isset($_SESSION["show_context_help"])) $_SESSION["show_context_help"] = $SHOW_CONTEXT_HELP;
if (!isset($_SESSION["pgv_user"])) $_SESSION["pgv_user"] = "";
if (!isset($_SESSION["cookie_login"])) $_SESSION["cookie_login"] = false;
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='yes') $_SESSION["show_context_help"] = true;
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='no') $_SESSION["show_context_help"] = false;
if (!isset($USE_THUMBS_MAIN)) $USE_THUMBS_MAIN = false;
if ((strstr($PHP_SELF, "editconfig.php")===false)
   &&(strstr($PHP_SELF, "editconfig_help.php")===false)) {
   if ((!check_db())||(!adminUserExists())) {
      header("Location: editconfig.php");
      exit;
   }

   //-- if the configversion is not equal to the program version then run the upgrade script
   if (($CONFIG_VERSION < $REQUIRED_CONFIG_VERSION)
   &&(strstr($PHP_SELF, "login.php")===false)
   &&(strstr($PHP_SELF, "admin.php")===false)
   &&(strstr($PHP_SELF, "help_text.php")===false)
   &&(strstr($PHP_SELF, "upgrade.php")===false)) {
		header("Location: upgrade.php");
		exit;
   }

   if ((strstr($PHP_SELF, "editconfig_gedcom.php")===false)
   &&(strstr($PHP_SELF, "help_text.php")===false)
   &&(strstr($PHP_SELF, "editconfig_help.php")===false)
   &&(strstr($PHP_SELF, "editgedcoms.php")===false)
   &&(strstr($PHP_SELF, "uploadgedcom.php")===false)
   &&(strstr($PHP_SELF, "login.php")===false)
   &&(strstr($PHP_SELF, "admin.php")===false)
   &&(strstr($PHP_SELF, "config_download.php")===false)
   &&(strstr($PHP_SELF, "addnewgedcom.php")===false)
   &&(strstr($PHP_SELF, "validategedcom.php")===false)
   &&(strstr($PHP_SELF, "addmedia.php")===false)
   &&(strstr($PHP_SELF, "importgedcom.php")===false)
   &&(strstr($PHP_SELF, "client.php")===false)
   &&(strstr($PHP_SELF, "edit_privacy.php")===false)
   &&(strstr($PHP_SELF, "useradmin.php")===false)) {
   	if ((count($GEDCOMS)==0)||(!check_for_import($GEDCOM))) {
		header("Location: editgedcoms.php");
		exit;
   	}
   }

	//-----------------------------------
	//-- if user wishes to logout this is where we will do it
	if ((!empty($logout))&&($logout==1)) {
		userLogout();
		if ($REQUIRE_AUTHENTICATION) {
			header("Location: ".$HOME_SITE_URL);
			exit;
		}
	}

	if ($REQUIRE_AUTHENTICATION) {
		if (empty($pgv_username)) {
			if ((strstr($PHP_SELF, "login.php")===false)
				&&(strstr($PHP_SELF, "login_register.php")===false)
				&&(strstr($PHP_SELF, "client.php")===false)
				&&(strstr($PHP_SELF, "help_text.php")===false)
				&&(strstr($PHP_SELF, "message.php")===false)) {
				$url = basename($_SERVER["PHP_SELF"])."?".$QUERY_STRING;
				if (stristr($url, "index.php")!==false) {
					if (stristr($url, "command=")===false) {
						if ((!isset($_SERVER['HTTP_REFERER'])) || (stristr($_SERVER['HTTP_REFERER'],$SERVER_URL)===false)) $url .= "&command=gedcom";
					}
				}
				if (stristr($url, "ged=")===false)  {
					$url.="&ged=".$GEDCOM;
				}
				header("Location: login.php?url=".urlencode($url));
				exit;
			}
		}
	}

   // -- setup session information for tree clippings cart features
   if (!isset($_SESSION['cart'])) {
     $_SESSION['cart'] = array();
   }
   $cart = $_SESSION['cart'];

   $_SESSION['CLANGUAGE'] = $LANGUAGE;
   if (!isset($_SESSION["timediff"])) {
	   $_SESSION["timediff"] = 0;
   }

   //-- load any editing changes
   if (userCanEdit(getUserName())) {
      if (file_exists($INDEX_DIRECTORY."pgv_changes.php")) require_once($INDEX_DIRECTORY."pgv_changes.php");
      else $pgv_changes = array();
   }
   else $pgv_changes = array();

   if (empty($LOGIN_URL)) $LOGIN_URL = "login.php";

} else {
	check_db();
}

//-- load the user specific theme
if ((!empty($pgv_username))&&(!isset($logout))) {
	$tempuser = getUser($pgv_username);
	$usertheme = $tempuser["theme"];
	if ((!empty($_POST["user_theme"]))&&(!empty($_POST["oldusername"]))&&($_POST["oldusername"]==$pgv_username)) $usertheme = $_POST["user_theme"];
	if ((!empty($usertheme)) && (file_exists($usertheme."theme.php")))  {
		$THEME_DIR = $usertheme;
	}
}

if (isset($_SESSION["theme_dir"]))
{
	$THEME_DIR = $_SESSION["theme_dir"];
	if (!empty($pgv_username))
	{
		$tempuser = getUser($pgv_username);
		if ($tempuser["editaccount"]) unset($_SESSION["theme_dir"]);
	}
}

if (empty($THEME_DIR)) $THEME_DIR="standard/";
if (file_exists($PGV_BASE_DIRECTORY.$THEME_DIR."theme.php")) require_once($PGV_BASE_DIRECTORY.$THEME_DIR."theme.php");
else {
	$THEME_DIR = $PGV_BASE_DIRECTORY."themes/standard/";
	require_once($THEME_DIR."theme.php");
}

require_once($PGV_BASE_DIRECTORY."hitcount.php"); //--load the hit counter

if ($Languages_Default) {					// If Languages not yet configured
	$pgv_lang_use["english"] = false;		//   disable English
	$pgv_lang_use["$LANGUAGE"] = true;		//     and enable according to Browser pref.
	$language_settings["english"]["pgv_lang_use"] = false;
	$language_settings["$LANGUAGE"]["pgv_lang_use"] = true;
}

?>