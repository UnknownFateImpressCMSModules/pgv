<?php
/**
 * Index User and Authentication functions
 *
 * This file contains the Index mode specific functions for working with users and authenticating them.
 * It also handles the internal mail messages, favorites, news/journal, and storage of MyGedView
 * customizations.  Assumes write permissions to the <var>$INDEX_DIRECTORY</var>
 *
 * You can extend PhpGedView to work with other systems by implementing the functions in this file.
 * Other possible options are to use LDAP for authentication.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003	John Finlay and Others
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
 * @subpackage Index
 * @version $Id: authentication_index.php,v 1.3 2006/01/09 00:46:23 skenow Exp $
 * @see authentication_mysql.php
 */
if (strstr($_SERVER["PHP_SELF"],"authentication_index.php")) {
	print "Don't you know that hacking is illegal.";
	exit;
}
//-- file that holds the users array
if (file_exists($INDEX_DIRECTORY."authenticate.php")) require $INDEX_DIRECTORY."authenticate.php";
else if ($CONFIG_VERSION!=$VERSION) {
	if (file_exists("authenticate.php")) require "authenticate.php";
	else $users = array();
}
else $users = array();

/**
 * authenticate a username and password
 *
 * This function takes the given <var>$username</var> and <var>$password</var> and authenticates
 * them against the database.  The passwords are encrypted using the crypt() function.
 * The username is stored in the <var>$_SESSION["pgv_user"]</var> session variable.
 * @param string $username the username for the user attempting to login
 * @param string $password the plain text password to test
 * @return bool return true if the username and password credentials match a user in the database return false if they don't
 */
function authenticateUser($username, $password) {
	global $users, $GEDCOM, $INDEX_DIRECTORY, $pgv_lang;
	$user = getUser($username);
	if ($user!==false) {
		if (crypt($password, $user["password"])==$user["password"]) {
	        if (!isset($user["verified"])) $user["verified"] = "";
	        if (!isset($user["verified_by_admin"])) $user["verified_by_admin"] = "";
	        if ((($user["verified"] == "yes") and ($user["verified_by_admin"] == "yes")) or ($user["canadmin"] != "")){
		        $users[$username]["loggedin"]="Y";
		        $users[$username]["sessiontime"]=time();
		        if (is_writable($INDEX_DIRECTORY."authenticate.php")) storeUsers();
			AddToLog("Login Successful ->" . $username ."<-");
			$_SESSION = array();
			$_SESSION['pgv_user'] = $username;
			$_SESSION['cookie_login'] = false;
			if (isset($pgv_lang[$user["language"]])) $_SESSION['CLANGUAGE'] = $user['language'];
				foreach($user["gedcomid"] as $ged=>$id) {
					if (!empty($id)) $_SESSION['GEDCOM']=$ged;
				}
				return true;
			}
		}
	}
	AddToLog("Login Failed ->" . $username ."<-");
	return false;
}

//----------------------------------- userLogout
//-- logs a user out of the system
function userLogout($username = "") {
	global $users, $GEDCOM, $LANGUAGE;

	if ($username=="") {
		if (isset($_SESSION["pgv_user"])) $username = $_SESSION["pgv_user"];
		else if (isset($_COOKIE["pgv_rem"])) $username = $_COOKIE["pgv_rem"];
		else return;
	}
	$users[$username]["loggedin"]="N";
	storeUsers();
	AddToLog("Logout - " . $username);

	if ((isset($_SESSION['pgv_user']) && ($_SESSION['pgv_user']==$username)) || (isset($_COOKIE['pgv_rem'])&&$_COOKIE['pgv_rem']==$username)) {
		if ($_SESSION['pgv_user']==$username) {
			$_SESSION['pgv_user'] = "";
			unset($_SESSION['pgv_user']);
			if (isset($_SESSION["pgv_counter"])) $tmphits = $_SESSION["pgv_counter"];
			else $tmphits = -1;
			@session_destroy();
			$_SESSION["gedcom"]=$GEDCOM;
			$_SESSION["show_context_help"]="yes";
			@setcookie("pgv_rem", "", -1000);
			if($tmphits>=0) $_SESSION["pgv_counter"]=$tmphits; //set since it was set before so don't get double hits
		}
	}
}

/**
 * return a sorted array of user
 *
 * returns a sorted array of the users in the system
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#users
 * @param string $field the field in the user array to sort on
 * @param string $order asc or dec
 * @return array returns a sorted array of users
 */
function getUsers($field = "username", $order = "asc") {
	global $users;
	$temparray = array();
	foreach($users as $username=>$user){
		$temparray[strtoupper($user[$field].$user["username"])] = $user;
	}
	if ($order=="asc") ksort($temparray);
	else krsort($temparray);
	$users = array();
	foreach($temparray as $username=>$user){
		//-- convert old <3.1 access levels to the new 3.2 access levels
		if (!isset($user["canedit"])||!is_array($user["canedit"])) $user["canedit"] = array();
		foreach($user["canedit"] as $key=>$value) {
			if ($value=="no") $user["canedit"][$key] = "access";
			if ($value=="yes") $user["canedit"][$key] = "edit";
		}
		if (!isset($user["visibleonline"])) $user["visibleonline"] = true;
		if (!isset($user["editaccount"])) $user["editaccount"] = true;
		if (!isset($user["verified_by_admin"])) $user["verified_by_admin"] = false;
		if (!isset($user["verified"])) $user["verified"] = true;
		if (!isset($user["default_tab"])) $user["default_tab"] = 0;
		if (!empty($user["username"])) $users[$user["username"]] = $user;
	}
	unset ($temparray);
	return $users;
}

//----------------------------------- getUserName
//-- retrieve the username from the state
//-- however you are storing it.  The default
//-- implemenation uses a cookie
function getUserName() {
	global $ALLOW_REMEMBER_ME, $DBCONN, $logout, $SERVER_URL;
	//-- this section checks if the session exists and uses it to get the username
	if (isset($_SESSION)) {
		if (!empty($_SESSION['pgv_user'])) return $_SESSION['pgv_user'];
	}
	if (isset($HTTP_SESSION_VARS)) {
		if (!empty($HTTP_SESSION_VARS['pgv_user'])) return $HTTP_SESSION_VARS['pgv_user'];
	}
	if ($ALLOW_REMEMBER_ME) {
		$tSERVER_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $SERVER_URL);
		if ((isset($_SERVER['HTTP_REFERER'])) && (stristr($_SERVER['HTTP_REFERER'],$tSERVER_URL)!==false)) $referrer_found=true;
		if (!empty($_COOKIE["pgv_rem"])&& (empty($referrer_found)) && empty($logout)) {
			if (!is_object($DBCONN)) return $_COOKIE["pgv_rem"];
			$user = getUser($_COOKIE["pgv_rem"]);
			if ($user) {
				if (time() - $user["sessiontime"] < 60*60*24*7) {
					$_SESSION['pgv_user'] = $_COOKIE["pgv_rem"];
					$_SESSION['cookie_login'] = true;
					return $_COOKIE["pgv_rem"];
				}
			}
		}
	}
	return "";
}

/**
 * check if given username is an admin
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files
 */
function userIsAdmin($username) {
	if (empty($username)) return false;
	if ($_SESSION['cookie_login']) return false;
	$user = getUser($username);
	if (!$user) return false;
	return $user["canadmin"];
}

/**
 * check if given username is an admin for the current gedcom
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files for the currently active gedcom
 */
function userGedcomAdmin($username, $ged="") {
	global $GEDCOM;

	if (empty($ged)) $ged = $GEDCOM;
//	if (!isset($_SESSION['cookie_login'])) return false;
	if ($_SESSION['cookie_login']) return false;
	if (userIsAdmin($username)) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$ged])) {
		if ($user["canedit"][$ged]=="admin") return true;
		else return false;
	}
	else return false;
}

/**
 * check if the given user has access privileges on this gedcom
 *
 * takes a username and checks if the user has access privileges to view the private
 * gedcom data.
 * @param string $username the username of the user to check
 * @return boolean true if user can access false if they cannot
 */
function userCanAccess($username) {
	global $GEDCOM;

	if (userIsAdmin($username)) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$GEDCOM]) || $user["canadmin"]) {
		if ($user["canedit"][$GEDCOM]!="none" || $user["canadmin"]) return true;
		else return true;
	}
	else return false;
}

/**
 * check if the given user has write privileges on this gedcom
 *
 * takes a username and checks if the user has write privileges to change
 * the gedcom data. First check if the administrator has turned on editing privileges for this gedcom
 * @param string $username the username of the user to check
 * @return boolean true if user can edit false if they cannot
 */
function userCanEdit($username) {
	global $ALLOW_EDIT_GEDCOM, $GEDCOM;

	if (!$ALLOW_EDIT_GEDCOM) return false;
	if (userIsAdmin($username)) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if ($user["canadmin"]) return true;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]=="yes" || $user["canedit"][$GEDCOM]=="edit" || $user["canedit"][$GEDCOM]=="accept" || $user["canedit"][$GEDCOM]=="admin" || $user["canedit"][$GEDCOM]===true) return true;
		else return false;
	}
	else return false;
}

//----------------------------------- userCanAccept
//-- takes a username and checks if the
//-- user has write privileges to change
//-- the gedcom data and accept changes
function userCanAccept($username) {
	global $ALLOW_EDIT_GEDCOM, $GEDCOM;

	if ($_SESSION['cookie_login']) return false;
	if (userIsAdmin($username)) return true;
	if (!$ALLOW_EDIT_GEDCOM) return false;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]=="accept") return true;
		if ($user["canedit"][$GEDCOM]=="admin") return true;
		else return false;
	}
	else return false;
}

//----------------------------------- adminUserExists
//-- return true if an admin user has been defined
function adminUserExists() {
	global $users;

	if (count($users)==0) return false;
	return true;
}

//----------------------------------- storeUsers
//-- writes the users to the file
function storeUsers() {
	global $users, $INDEX_DIRECTORY, $pgv_lang;

	$authtext = "<?php\n\n\$users = array();\n\n";
	foreach($users as $key=>$user) {
		$user["fullname"] = addslashes($user["fullname"]);
		$user["username"] = $key;
		$authtext .= "\$user = array();\n";
		foreach($user as $ukey=>$value) {
			if (!is_array($value)) {
				$value = preg_replace('/"/', '\\"', $value);
				$value = preg_replace("/;/", "", $value);
				$value = preg_replace("/\(.*\)/", "", $value);
				$authtext .= "\$user[\"$ukey\"] = '$value';\n";
			}
			else {
				$authtext .= "\$user[\"$ukey\"] = array();\n";
				foreach($value as $subkey=>$subvalue) {
					$subvalue = preg_replace('/"/', '\\"', $subvalue);
					$subvalue = preg_replace("/;/", "", $subvalue);
					$subvalue = preg_replace("/\(.*\)/", "", $subvalue);
					$authtext .= "\$user[\"$ukey\"][\"$subkey\"] = '$subvalue';\n";
				}
			}
		}
		$authtext .= "\$users[\"$key\"] = \$user;\n\n";
	}
	$authtext .= "?>\n";
	if (file_exists($INDEX_DIRECTORY."authenticate.php")&&(!is_writeable($INDEX_DIRECTORY."authenticate.php"))) {
		print "<span class=\"error\"><b>Unable to write to ".$INDEX_DIRECTORY."authenticate.php<br /></b></span>";
		$_SESSION["authenticate.php"]=$authtext;
		print "<br /><br /><a href=\"config_download.php?file=authenticate.php\">".$pgv_lang["download_here"]."</a><br /><br />\n";
		return false;
	}
	else {
		$fp = fopen($INDEX_DIRECTORY."authenticate.php", "w");
		fwrite($fp, $authtext);
		fclose($fp);
		return true;
	}
}

//----------------------------------- addUser
//-- adds a new user.
//-- requires the newuser parameter to be an array
function addUser($newuser, $msg = "added") {
	global $users;

	$activeuser = getUserName();
	if ($activeuser == "") $activeuser = "Anonymous user";
	AddToLog($activeuser." ".$msg." user -> ".$newuser["username"]." <-");
	$users[$newuser["username"]]=$newuser;

	$res = storeUsers();
	if (!$res) unset($users[$newuser["username"]]);
	return $res;

}

//----------------------------------- deleteUser
//-- deletes the user with the given username.
function deleteUser($username, $msg = "deleted") {
	global $users;

	$activeuser = getUserName();
	if ($activeuser == "") $activeuser = "Anonymous user";
	if (($msg != "changed") && ($msg != "reqested password for")) AddToLog($activeuser." ".$msg." user -> ".$username." <-");
	unset($users[$username]);
	return storeUsers();
}

/**
 * get a user array
 *
 * finds a user from the given username and returns a user array of the form
 * defined at {@link http://www.phpgedview.net/devdocs/arrays.php#user}
 * @param string $username the username of the user to return
 * @return array the user array to return
 */
function getUser($username) {
	global $users;

	if (empty($username)) return false;
	if (isset($users[$username])) {
		if (!isset($users[$username]["contactmethod"])) $users[$username]["contactmethod"]="messaging2";
		if (!isset($users[$username]["visibleonline"])) $users[$username]["visibleonline"]=true;
		if (!isset($users[$username]["editaccount"])) $users[$username]["editaccount"]=true;
		if (!isset($users[$username]["canadmin"])) $users[$username]["canadmin"]=false;
		if (!isset($users[$username]["username"])) $users[$username]["username"]=$username;
		//-- convert old <3.1 access levels to the new 3.2 access levels
		if (!isset($users[$username]["canedit"]) || !is_array($users[$username]["canedit"])) $users[$username]["canedit"] = array();
		foreach($users[$username]["canedit"] as $key=>$value) {
			if ($value=="no") $users[$username]["canedit"][$key] = "access";
			if ($value=="yes") $users[$username]["canedit"][$key] = "edit";
		}
		return $users[$username];
	}
	return false;

}

//----------------------------------- AddToLog
//-- requires a string to add into the log-file
function AddToLog($LogString) {
	global $INDEX_DIRECTORY, $LOGFILE_CREATE;

	if ($LOGFILE_CREATE=="none") return;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];

	if ($LOGFILE_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ymd") . ".log";
	if ($LOGFILE_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . "-week" . date("W") . ".log";
	if ($LOGFILE_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . ".log";
	if ($LOGFILE_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Y") . ".log";

	# $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ymd") . ".log";
	if (is_writable($INDEX_DIRECTORY)) {
		$logline = date("Y.m.d H:i:s") . " - " . $REMOTE_ADDR . " - " . $LogString . "\r\n";
		$fp = fopen($logfile, "a");
		flock($fp, 2);
		fputs($fp, $logline);
		flock($fp, 3);
		fclose($fp);
	}
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($LogString, $allgeds) {
	global $INDEX_DIRECTORY, $SEARCHLOG_CREATE, $GEDCOM, $GEDCOMS, $username;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);
	
	$oldged = $GEDCOM;
	if ($allgeds) $geds = $GEDCOMS;
	else $geds[$GEDCOM]["gedcom"] = $GEDCOM;
	foreach($geds as $indexval => $value) {
		$GEDCOM = $value["gedcom"];
		include(get_config_file());
		if ($SEARCHLOG_CREATE != "none") {
			$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
			if (empty($SEARCHLOG_CREATE)) $SEARCHLOG_CREATE="daily";
			if ($SEARCHLOG_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ymd") . ".log";
			if ($SEARCHLOG_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ym") . "-week" . date("W") . ".log";
			if ($SEARCHLOG_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ym") . ".log";
			if ($SEARCHLOG_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Y") . ".log";
			if (is_writable($INDEX_DIRECTORY)) {
				$logline = "Date / Time: ".date("d.m.Y H:i:s") . " - IP: " . $REMOTE_ADDR . " - User: " .  getUserName() . "<br />";
				if ($allgeds) $logline .= "Searchtype: Global<br />"; else $logline .= "Searchtype: Gedcom<br />";
				$logline .= $LogString . "<br /><br />\r\n";
				$fp = fopen($logfile, "a");
				flock($fp, 2);
				fputs($fp, $logline);
				flock($fp, 3);
				fclose($fp);
			}
		}
	}
	$GEDCOM = $oldged;
	include(get_config_file());
}

//----------------------------------- addMessage
//-- stores a new message in the database
function addMessage($message) {
	global $messages, $CONTACT_METHOD, $pgv_lang, $CHARACTER_SET, $LANGUAGE, $PGV_STORE_MESSAGES, $SERVER_URL, $pgv_language, $PGV_BASE_DIRECTORY, $PGV_SIMPLE_MAIL;
	global $TEXT_DIRECTION, $TEXT_DIRECTION_array, $DATE_FORMAT, $DATE_FORMAT_array, $TIME_FORMAT, $TIME_FORMAT_array, $WEEK_START, $WEEK_START_array, $NAME_REVERSE, $NAME_REVERSE_array;

	if (!isset($messages)) readMessages();

	//-- setup the message body for the from user
	$email2 = stripslashes($message["body"]);
	if (isset($message["from_name"])) $email2 = $pgv_lang["message_from_name"]." ".$message["from_name"]."\r\n".$pgv_lang["message_from"]." ".$message["from_email"]."\r\n\r\n".$email2;
	if (!empty($message["url"])) $email2 .= "\r\n\r\n--------------------------------------\r\n\r\n".$pgv_lang["viewing_url"]."\r\n".$SERVER_URL.$message["url"]."\r\n";
	$email2 .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
	$email2 .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
	$email2 .= "LANGUAGE: $LANGUAGE\r\n";
	$subject2 = "[".$pgv_lang["phpgedview_message"]."] ".stripslashes($message["subject"]);
	$from ="";
	$fuser = getUser($message["from"]);
	if (!$fuser) {
		$from = $message["from"];
		$email2 = $pgv_lang["message_email3"]."\r\n\r\n".stripslashes($email2);
	}
	else {
		//FIXME should the hex4email be removed?
		if (!$PGV_SIMPLE_MAIL) $from = "'".hex4email(stripslashes($fuser["fullname"]),$CHARACTER_SET). "' <".$fuser["email"].">";
		else $from = $fuser["email"];
		$email2 = $pgv_lang["message_email2"]."\r\n\r\n".stripslashes($email2);
	}

	//-- get the to users language
	$tuser = getUser($message["to"]);
	$oldlanguage = $LANGUAGE;
	if (($tuser)&&(!empty($tuser["language"]))&&($tuser["language"]!=$LANGUAGE)) {
		$LANGUAGE = $tuser["language"];
		if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START	= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];
	}
	if (isset($message["from_name"])) $message["body"] = $pgv_lang["message_from_name"]." ".$message["from_name"]."\r\n".$pgv_lang["message_from"]." ".$message["from_email"]."\r\n\r\n".$message["body"];
	if (!empty($message["url"])) $message["body"] .= "\r\n\r\n--------------------------------------\r\n\r\n".$pgv_lang["viewing_url"]."\r\n".$SERVER_URL.$message["url"]."\r\n";
	$message["body"] .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
	$message["body"] .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
	$message["body"] .= "LANGUAGE: $LANGUAGE\r\n";
	if (!isset($message["created"])) $message["created"] = gmdate ("M d Y H:i:s");
	if ($PGV_STORE_MESSAGES && $message["method"]!="messaging3") $messages[] = $message;
	if ($message["method"]!="messaging") {
		$subject1 = "[".$pgv_lang["phpgedview_message"]."] ".stripslashes($message["subject"]);
		if (!$fuser) {
			$email1 = $pgv_lang["message_email1"];
			if (!empty($message["from_name"])) $email1 .= $message["from_name"]."\r\n\r\n".stripslashes($message["body"]);
			else $email1 .= $from."\r\n\r\n".stripslashes($message["body"]);
		}
		else {
			$email1 = $pgv_lang["message_email1"];
			$email1 .= stripslashes($fuser["fullname"])."\r\n\r\n".stripslashes($message["body"]);
		}
		$tuser = getUser($message["to"]);
		if (!$tuser) {
			//-- the to user must be a valid user in the system before it will send any mails
			return false;
		} else {
			if (!$PGV_SIMPLE_MAIL) $to = "'".hex4email(stripslashes($tuser["fullname"]),$CHARACTER_SET). "' <".$tuser["email"].">";
			else $to = $tuser["email"];
		}
		if (!$fuser) {
			//$header2 = "From: phpgedview-noreply@".$_SERVER["SERVER_NAME"]."\r\nContent-type: text/plain; charset=$CHARACTER_SET; format=flowed\r\nContent-Transfer-Encoding: 8bit\r\n";
			$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
			$header2 = "From: phpgedview-noreply@".$host;
		} else {
			//$header2 = "From: ".$to."\r\nContent-type: text/plain; charset=$CHARACTER_SET; format=flowed\r\nContent-Transfer-Encoding: 8bit\r\n";
			$header2 = "From: ".$to;
		}
		if (!empty($tuser["email"])) {
			//mail($to, hex4email($subject,$CHARACTER_SET), $email1, "From: ".$from."\r\nContent-type: text/plain; charset=$CHARACTER_SET; format=flowed\r\nContent-Transfer-Encoding: 8bit\r\n");
			pgvMail($to, $subject1, $email1, "From: ".$from);
		}
	}
	if (($tuser)&&(!empty($LANGUAGE))&&($oldlanguage!=$LANGUAGE)) {
		$LANGUAGE = $oldlanguage;
		if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START	= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];
	}
	if ($message["method"]!="messaging") {
		if (!isset($message["no_from"])) {
			//mail($from, hex4email($subject,$CHARACTER_SET), $email2, $header2);
			pgvMail($from, $subject2, $email2, $header2);
		}
	}
	return storeMessages();
}

//----------------------------------- deleteMessage
//-- deletes a message in the database
function deleteMessage($message_id) {
	global $messages;

	if (!isset($messages)) readMessages();
	unset($messages[$message_id]);
	return storeMessages();
}

//----------------------------------- getUserMessages
//-- Return an array of a users messages
function getUserMessages($username) {
	global $messages;

	if (!isset($messages)) readMessages();
	$umessages = array();
	foreach($messages as $k=>$m) {
		if ($m["to"]==$username) $umessages[$k] = $m;
	}
	return $umessages;
}

//----------------------------------- storeMessages
//-- Store the messages in a file
function storeMessages() {
	global $messages, $INDEX_DIRECTORY;

	$mstring = serialize($messages);
	$fp = fopen($INDEX_DIRECTORY."messages.dat", "wb");
	fwrite($fp, $mstring);
	fclose($fp);
	return true;
}

//----------------------------------- readMessages
//-- read the messages in a file
function readMessages() {
	global $messages, $INDEX_DIRECTORY;

	$messages = array();
	if (file_exists($INDEX_DIRECTORY."messages.dat")) {
		$fp = fopen($INDEX_DIRECTORY."messages.dat", "rb");
		$mstring = fread($fp, filesize($INDEX_DIRECTORY."messages.dat"));
		fclose($fp);
		$messages = unserialize($mstring);
	}
}

//----------------------------------- addFavorite
//-- stores a new message in the database
function addFavorite($favorite) {
	global $favorites;

	if (!isset($favorites)) $favorites = readFavorites();
	foreach($favorites as $indexval => $f) {
		if (($f["gid"]==$favorite["gid"]) and ($f["file"]==$favorite["file"]) and ($f["username"]==$favorite["username"])) return false;
	}
	$favorites[] = $favorite;
	return storeFavorites();
}

//----------------------------------- deleteFavorite
//-- deletes a message in the database
function deleteFavorite($fv_id) {
	global $favorites;

	if (!isset($favorites)) $favorites = readFavorites();
	unset($favorites[$fv_id]);
	return storeFavorites();
}

//----------------------------------- getUserFavorites
//-- Return an array of a users messages
function getUserFavorites($username) {
	global $favorites, $GEDCOMS;

	if (!isset($favorites)) $favorites = readFavorites();
	$ufavorites = array();
	if (isset($favorites)) {
		foreach($favorites as $k=>$m) {
			if ($m["username"]==$username){
				if (isset($GEDCOMS[$m["file"]])) $ufavorites[$k] = $m;
			}
		}
	}
	return $ufavorites;
}

//----------------------------------- storeFavorites
//-- Store the favorites in a file
function storeFavorites() {
	global $favorites, $INDEX_DIRECTORY;
	$mstring = serialize($favorites);
	$fp = fopen($INDEX_DIRECTORY."favorites.dat", "wb");
	fwrite($fp, $mstring);
	fclose($fp);
	return true;
}

//----------------------------------- readFavorites
//-- read the messages in a file
function readFavorites() {
	global $favorites, $INDEX_DIRECTORY;

	$favorites = array();
	if (file_exists($INDEX_DIRECTORY."favorites.dat")) {
		$fp = fopen($INDEX_DIRECTORY."favorites.dat", "rb");
		$mstring = fread($fp, filesize($INDEX_DIRECTORY."favorites.dat"));
		fclose($fp);
		$favorites = unserialize($mstring);
	}
	return $favorites;
}

//----------------------------------- getBlocks
//-- Return an array of a users blocks
function getBlocks($username) {
	global $blocks, $GEDCOMS;

	if (!isset($blocks)) $blocks = readBlocks();
	$ublocks = array();
	$ublocks["main"] = array();
	$ublocks["right"] = array();
	if (isset($blocks)) {
		foreach($blocks as $k=>$b) {
			if (!isset($b["config"])) $b["config"]="";
			if (!isset($b["username"])) $b["username"] = "";
			if ($b["username"]==$username){
				if ($b["location"]=="main") $ublocks["main"][$b["order"]] = array($b["name"], $b["config"]);
				if ($b["location"]=="right") $ublocks["right"][$b["order"]] = array($b["name"], $b["config"]);
			}
		}
	}
	return $ublocks;
}

//----------------------------------- storeBlocks
//-- Store the favorites in a file
function storeBlocks() {
	global $blocks, $INDEX_DIRECTORY;
	$newblocks = array();
	foreach($blocks as $k=>$b) {
		if (isset($b["username"])) {
			$newblocks[$k] = $b;
		}
	}
	$blocks = $newblocks;
	$mstring = serialize($blocks);
	$fp = fopen($INDEX_DIRECTORY."blocks.dat", "wb");
	fwrite($fp, $mstring);
	fclose($fp);
	return true;
}

//----------------------------------- readBlocks
//-- read the messages in a file
function readBlocks() {
	global $blocks, $INDEX_DIRECTORY;

	$blocks = array();
	if (file_exists($INDEX_DIRECTORY."blocks.dat")) {
		$fp = fopen($INDEX_DIRECTORY."blocks.dat", "rb");
		$mstring = fread($fp, filesize($INDEX_DIRECTORY."blocks.dat"));
		fclose($fp);
		$blocks = unserialize($mstring);
	}
	return $blocks;
}

/**
 * Set Blocks
 *
 * Sets the blocks for a gedcom or user portal
 * the $setdefault parameter tells the program to also store these blocks as the blocks used by default
 * @param String $username the username or gedcom name to update the blocks for
 * @param array $ublocks the new blocks to set for the user or gedcom
 * @param boolean $setdefault	if true tells the program to also set these blocks as the blocks for the defaultuser
 */
function setBlocks($username, $ublocks, $setdefault=false) {
	global $blocks;

	if (!isset($blocks)) $blocks = readBlocks();
	$newblocks = array();
	if (isset($blocks)) {
		foreach($blocks as $i=>$block) {
			if (isset($block["username"])) {
				if ($block["username"]!=$username) {
					if ((!$setdefault)||($block["username"]!='defaultuser')) $newblocks[] = $block;
				}
			}
		}
		$blocks = $newblocks;
	}
	foreach($ublocks["main"] as $order=>$block) {
		$blocks[] = array("username"=>$username, "location"=>"main", "order"=>$order, "name"=>$block[0], "config"=>$block[1]);
		if ($setdefault) $blocks[] = array("username"=>"defaultuser", "location"=>"main", "order"=>$order, "name"=>$block[0], "config"=>$block[1]);
	}
	foreach($ublocks["right"] as $order=>$block) {
		$blocks[] = array("username"=>$username, "location"=>"right", "order"=>$order, "name"=>$block[0], "config"=>$block[1]);
		if ($setdefault) $blocks[] = array("username"=>"defaultuser", "location"=>"right", "order"=>$order, "name"=>$block, "config"=>$block[1]);
	}
	storeBlocks();
}

/**
 * Adds a news item to the database
 *
 * This function adds a news item represented by the $news array to the database.
 * If the $news array has an ["id"] field then the function assumes that it is
 * as update of an older news item.
 *
 * @param array $news a news item array
 */
function addNews($news) {
	global $pgv_news;

	if (!isset($pgv_news)) readNews();

	$news["title"] = stripslashes($news["title"]);
	$news["text"] = stripslashes($news["text"]);
	if (!isset($news["date"])) $news["date"] = time()-$_SESSION["timediff"];
	if (!empty($news["id"])) $pgv_news[$news["id"]] = $news;
	else $pgv_news[] = $news;
	storeNews();
}

/**
 * Deletes a news item from the database
 *
 * @author John Finlay
 * @param int $news_id the id number of the news item to delete
 */
function deleteNews($news_id) {
	global $pgv_news;

	if (!isset($pgv_news)) readNews();
	unset($pgv_news[$news_id]);
	return storeNews();
}

/**
 * Gets the news items for the given user or gedcom
 *
 * @author John Finlay
 * @param String $username the username or gedcom file name to get news items for
 */
function getUserNews($username) {
	global $pgv_news;

	if (!isset($pgv_news)) readNews();
	$unews = array();
	foreach($pgv_news as $k=>$n) {
		if ($n["username"]==$username) $unews[$k] = $n;
	}
	$unews = array_reverse($unews,true);
	return $unews;
}

/**
 * Store the news items in a file
 *
 * @author John Finlay
 */
function storeNews() {
	global $pgv_news, $INDEX_DIRECTORY;

	$mstring = serialize($pgv_news);
	$fp = fopen($INDEX_DIRECTORY."news.dat", "wb");
	fwrite($fp, $mstring);
	fclose($fp);
	return true;
}

/**
 * Read the news items from a file
 *
 * @author John Finlay
 */
# function readMessages() {
function readNews() {
	global $pgv_news, $INDEX_DIRECTORY;

	$pgv_news = array();
	if (file_exists($INDEX_DIRECTORY."news.dat")) {
		$fp = fopen($INDEX_DIRECTORY."news.dat", "rb");
		$mstring = fread($fp, filesize($INDEX_DIRECTORY."news.dat"));
		fclose($fp);
		$pgv_news = unserialize($mstring);
	}
}
/**
 * Gets the news item for the given news id
 *
 * @author John Finlay
 * @param int $news_id the id of the news entry to get
 */
function getNewsItem($news_id) {
	global $pgv_news;

	if (!isset($pgv_news)) readNews();
	return $pgv_news[$news_id];
}
?>