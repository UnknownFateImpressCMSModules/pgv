<?php
/**
 * MySQL User and Authentication functions
 *
 * This file contains the MySQL specific functions for working with users and authenticating them.
 * It also handles the internal mail messages, favorites, news/journal, and storage of MyGedView
 * customizations.  Assumes that a database connection has already been established.
 *
 * You can extend PhpGedView to work with other systems by implementing the functions in this file.
 * Other possible options are to use LDAP for authentication.
 *
 * $Id: authentication_xoops.php,v 1.1 2005/10/07 18:08:21 skenow Exp $
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
 * @package PhpGedView
 * @subpackage DB
 * @see authentication_index.php
 */
if (strstr($_SERVER['SCRIPT_NAME'],'authentication'))
{
	print 'Hacking is discouraged';
	exit;
}

function pgvGetDbExtUser($uid)
{
	global
		$TBLPREFIX
	;
	$uid = db_prep($uid);
	$sql = "SELECT * FROM {$TBLPREFIX}users WHERE u_xoopsid='{$uid}'";
	$res =& dbquery($sql, false);
	if (DB::isError($res))
	{
		return false;
	}
	if ($res->numRows() == 0)
	{
		$user = array (
			'xoopsid'		=> $uid,
			'gedcomid'		=> array(),
			'rootid'		=> array(),
			'canedit'		=> array(),
			'contactmethod'	=> 'messaging',
			'defaulttab'	=> 0
		);
		pgvAddDbExtUser($user);
		return $user;
	}
	if ($res)
	{
		$xuser =& $res->fetchRow(DB_FETCHMODE_ASSOC);
		$res->free();
		$user = array(
			'xoopsid'		=> $xuser['u_xoopsid'],
			'gedcomid'		=> unserialize($xuser['u_gedcomid']),
			'rootid'		=> unserialize($xuser['u_rootid']),
			'canedit'		=> unserialize($xuser['u_canedit']),
			'contactmethod'	=> $xuser['u_contactmethod'],
			'defaulttab'	=> $xuser['u_defaulttab']
		);
		return $user;
	}
	return false;
}

function pgvAddDbExtUser($data)
{
	global
		$TBLPREFIX
	;
	$data = db_prep($data);
	$data['gedcomid'] = serialize($data['gedcomid']);
	$data['rootid'] = serialize($data['rootid']);
	$data['canedit'] = serialize($data['canedit']);
	$sql = "INSERT INTO {$TBLPREFIX}users (u_xoopsid, u_gedcomid, u_rootid, u_canedit, u_contactmethod, u_defaulttab) VALUES ('{$data['xoopsid']}', '{$data['gedcomid']}', '{$data['rootid']}', '{$data['canedit']}', '{$data['contactmethod']}', '{$data['defaulttab']}')";
	$res =& dbquery($sql, false);
	if (DB::isError($res))
	{
		return false;
	}
	return true;
}

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
function authenticateUser($username, $password)
{
    if ($username=="") return false;
    return true;
}

//----------------------------------- userLogout
//-- logs a user out of the system
function userLogout($username = "") {
	return true;
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
	global $TBLPREFIX, $member_handler;

	//$criteria = new Criteria();
	//$criteria->setSort($field);
	//$criteria->setOrder($order);
	$xusers = $member_handler->getUsers(null, false);
	if (count($xusers) == 0) return array();

	$users = array ();
	foreach ($xusers as $xuser)
	{
		$puser = pgvGetDbExtUser($xuser->getVar('uid'));
		$user = array (
			'xoopsid'			=> $xuser->getVar('uid'),
			'username'			=> $xuser->getVar('uname'),
			'fullname'			=> $xuser->getVar('name'),
			'gedcomid'			=> $puser['gedcomid'],
			'rootid'			=> $puser['rootid'],
			'password'			=> $xuser->getVar('pass'),
			'canadmin'			=> $xuser->isAdmin(),
			'canedit'			=> $puser['canedit'],
			'email'				=> $xuser->getVar('email'),
			'verified'			=> 'yes',
			'verified_by_admin'	=> 'yes',
			'language'			=> 'english',		// xoops doesn't support multi language???
			'pwrequested'		=> '',
			'reg_timestamp'		=> $xuser->getVar('user_regdate'),
			'reg_hashcode'		=> '',				// no idea, need to see if needed
			'theme'				=> '',
			'loggedin'			=> 'Y',
			'sessiontime'		=> $xuser->getVar('last_login'),	// just a guess
			'contactmethod'		=> $puser['contactmethod'],
			'visibleonline'		=> true,
			'editaccount'		=> false,
			'default_tab'		=> $puser['defaulttab'],
			'comment'			=> '',
			'comment_exp'		=> ''
		);
		$users[$user['username']] = $user;
	}
	$GLOBALS['users'] = $users;
	return $users;
}

//----------------------------------- getUserName
//-- retrieve the username from the state
//-- however you are storing it.  The default
//-- implemenation uses a cookie
function getUserName()
{
	global
		$xoopsUser
	;
	//-- this section checks if the session exists and uses it to get the username
/*	if (isset($_SESSION))
	{
		if (!empty($_SESSION['pgv_user']))
		{
			return $_SESSION['pgv_user'];
		}
	}*/
	if (!$xoopsUser)
	{
		return "";
	}
	$uid = $xoopsUser->getVar('uid');
	$username = $xoopsUser->getUnameFromId($uid);
	$_SESSION['pgv_user'] = $username;
	//$_SESSION['cookie_login'] = true;
	$_SESSION['cookie_login'] = false;
	return $username;
}

/**
 * check if given username is an admin
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files
 */
function userIsAdmin($username) {
	global $xoopsUser;

	if (empty($username)) return false;

	return $xoopsUser->isAdmin();
}

/**
 * check if given username is an admin for the current gedcom
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files for the currently active gedcom
 */
function userGedcomAdmin($username, $ged = '')
{
	global
		$GEDCOM
	;
	if (empty($ged))
	{
		$ged = $GEDCOM;
	}
	if (userIsAdmin($username))
	{
		return true;
	}
	if (empty($username))
	{
		return false;
	}
	$user = getUser($username);
	if (!$user)
	{
		return false;
	}
	if (isset($user['canedit'][$ged]))
	{
		if ($user['canedit'][$ged] == 'admin')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
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
	if (!$user)
	{
		return false;
	}
	if (isset($user['canedit'][$GEDCOM]))
	{
		if ($user['canedit'][$GEDCOM] != 'none' || $user['canadmin'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
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

	if (empty($username)) return false;
	if (userIsAdmin($username)) return true;
	if (!$ALLOW_EDIT_GEDCOM) return false;
	$user = getUser($username);
	if (!$user) return false;
	if ($user["canadmin"]) return true;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]=="yes" || $user["canedit"][$GEDCOM]=="edit" || $user["canedit"][$GEDCOM]=="admin"|| $user["canedit"][$GEDCOM]=="accept" || $user["canedit"][$GEDCOM]===true) return true;
		else return false;
	}
	else return false;
}

//----------------------------------- userCanAccept
//-- takes a username and checks if the
//-- user has write privileges to change
//-- the gedcom data and accept changes
function userCanAccept($username)
{
	global
		$ALLOW_EDIT_GEDCOM,
		$GEDCOM
	;
	if (empty($username))
	{
		return false;
	}
	if (userIsAdmin($username))
	{
		return true;
	}
	if (!$ALLOW_EDIT_GEDCOM)
	{
		return false;
	}
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
	return true;
}

//----------------------------------- storeUsers
//-- writes the users to the file
function storeUsers() {
	return true;
}

//----------------------------------- addUser
//-- adds a new user.
//-- requires the newuser parameter to be an array
function addUser($newuser, $msg = "added") {
	global
		$TBLPREFIX,
		$DBCONN
	;
	if (!pgvAddDbExtUser($newuser))
	{
		return false;
	}
	$activeuser = getUserName();
	if ($activeuser == '')
	{
		$activeuser = 'Anonymous user';
	}
	AddToLog("{$activeuser} {$msg} user -> {$newuser['username']} <-");
	return true;
}

//----------------------------------- deleteUser
//-- deletes the user with the given username.
function deleteUser($username, $msg = "deleted") {
	global
		$TBLPREFIX,
		$users
	;
	unset ($users[$username]);
	$user = getUser($username);
	$username = db_prep($user['username']);
	$sql = "DELETE FROM {$TBLPREFIX}users WHERE u_xoopsid='{$user['xoopsid']}'";
	$res =& dbquery($sql);
	$activeuser = getUserName();
	if ($activeuser == '')
	{
		$activeuser = 'Anonymous user';
	}
	if (($msg != 'changed') && ($msg != 'reqested password for') && ($msg != 'verified'))
	{
		AddToLog("{$activeuser} {$msg} user -> {$username} <-");
	}
	return true;
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
	global $TBLPREFIX, $users, $REGEXP_DB, $xoopsUser, $member_handler;

	if (empty($username) || $username == "") return false;
	if (isset($users[$username])) return $users[$username];

	$xuser = $member_handler->getUsers(new Criteria('uname', $username), false);
	if (count($xuser) == 0)	return false;

	$xuser = array_shift($xuser);
	$puser = pgvGetDbExtUser($xuser->getVar('uid'));
	$user = array (
		'xoopsid'			=> $xuser->getVar('uid'),
		'username'			=> $xuser->getVar('uname'),
		'fullname'			=> $xuser->getVar('name'),
		'gedcomid'			=> $puser['gedcomid'],
		'rootid'			=> $puser['rootid'],
		'password'			=> $xuser->getVar('pass'),
		'canadmin'			=> $xuser->isAdmin(),
		'canedit'			=> $puser['canedit'],
		'email'				=> $xuser->getVar('email'),
		'verified'			=> 'yes',
		'verified_by_admin'	=> 'yes',
		'language'			=> 'english',		// xoops doesn't support multi language???
		'pwrequested'		=> '',
		'reg_timestamp'		=> $xuser->getVar('user_regdate'),
		'reg_hashcode'		=> '',				// no idea, need to see if needed
		'theme'				=> '',
		'loggedin'			=> 'Y',
		'sessiontime'		=> $xuser->getVar('last_login'),	// just a guess
		'contactmethod'		=> $puser['contactmethod'],
		'visibleonline'		=> true,
		'editaccount'		=> false,
		'default_tab'		=> $puser['defaulttab'],
		'comment'			=> '',
		'comment_exp'		=> ''
	);
	$users[$username] = $user;
	return $user;
}

//----------------------------------- AddToLog
//-- requires a string to add into the log-file
function AddToLog($LogString, $savelangerror=false) {
	global $INDEX_DIRECTORY, $LOGFILE_CREATE;

	if ($LOGFILE_CREATE=="none") return;

	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	if ($LOGFILE_CREATE !== "none" && $savelangerror === false) {
		if (empty($LOGFILE_CREATE)) $LOGFILE_CREATE="daily";
		if ($LOGFILE_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ymd") . ".log";
		if ($LOGFILE_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . "-week" . date("W") . ".log";
		if ($LOGFILE_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . ".log";
		if ($LOGFILE_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Y") . ".log";
		if (is_writable($INDEX_DIRECTORY)) {
			$logline = date("d.m.Y H:i:s") . " - " . $REMOTE_ADDR . " - " . $LogString . "\r\n";
			$fp = fopen($logfile, "a");
			flock($fp, 2);
			fputs($fp, $logline);
			flock($fp, 3);
			fclose($fp);
		}
	}
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($LogString, $allgeds) {
	global $INDEX_DIRECTORY, $SEARCHLOG_CREATE, $GEDCOM, $GEDCOMS, $username;

	if (!isset($allgeds)) return;
	if (count($allgeds) == 0) return;
	$oldged = $GEDCOM;
	foreach($allgeds as $indexval => $value) {
		$GEDCOM = $value;
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
				if (count($allgeds) == count($GEDCOMS)) $logline .= "Searchtype: Global<br />"; else $logline .= "Searchtype: Gedcom<br />";
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
	global $TBLPREFIX, $CONTACT_METHOD, $pgv_lang,$CHARACTER_SET, $LANGUAGE, $PGV_STORE_MESSAGES, $SERVER_URL, $pgv_language, $PGV_BASE_DIRECTORY, $PGV_SIMPLE_MAIL, $WEBMASTER_EMAIL, $DBCONN;
	global $TEXT_DIRECTION, $TEXT_DIRECTION_array, $DATE_FORMAT, $DATE_FORMAT_array, $TIME_FORMAT, $TIME_FORMAT_array, $WEEK_START, $WEEK_START_array, $NAME_REVERSE, $NAME_REVERSE_array;

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
	if ($PGV_STORE_MESSAGES && ($message["method"]!="messaging3" && $message["method"]!="mailto" && $message["method"]!="none")) {
		$newid = get_next_id("messages", "m_id");
		$sql = "INSERT INTO ".$TBLPREFIX."messages VALUES ($newid, '".$DBCONN->escapeSimple($message["from"])."','".$DBCONN->escapeSimple($message["to"])."','".$DBCONN->escapeSimple($message["subject"])."','".$DBCONN->escapeSimple($message["body"])."','".$DBCONN->escapeSimple($message["created"])."')";
		$res =& dbquery($sql);
	}
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
			$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
			$header2 = "From: phpgedview-noreply@".$host;
		} else {
			$header2 = "From: ".$to;
		}
		if (!empty($tuser["email"])) {
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
			if (stristr($from, "phpgedview-noreply@")){
				$admuser = getuser($WEBMASTER_EMAIL);
				$from = $admuser["email"];
			}
			pgvMail($from, $subject2, $email2, $header2);
		}
	}
	return true;
}

//----------------------------------- deleteMessage
//-- deletes a message in the database
function deleteMessage($message_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM ".$TBLPREFIX."messages WHERE m_id=".$message_id;
	$res =& dbquery($sql);
	if ($res) return true;
	else return false;
}

//----------------------------------- getUserMessages
//-- Return an array of a users messages
function getUserMessages($username) {
	global $TBLPREFIX;

	$messages = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."messages WHERE m_to='$username' ORDER BY m_id DESC";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$message = array();
		$message["id"] = $row["m_id"];
		$message["to"] = $row["m_to"];
		$message["from"] = $row["m_from"];
		$message["subject"] = stripslashes($row["m_subject"]);
		$message["body"] = stripslashes($row["m_body"]);
		$message["created"] = $row["m_created"];
		$messages[] = $message;
	}
	return $messages;
}

//----------------------------------- addFavorite
//-- stores a new message in the database
function addFavorite($favorite) {
	global $TBLPREFIX, $DBCONN;

	$sql = "SELECT * FROM ".$TBLPREFIX."favorites WHERE fv_gid='".$favorite["gid"]."' AND fv_file='".$DBCONN->escapeSimple($favorite["file"])."' AND fv_username='".$DBCONN->escapeSimple($favorite["username"])."'";
	$res =& dbquery($sql);
	if ($res->numRows()>0) return false;
	$newid = get_next_id("favorites", "fv_id");
	$sql = "INSERT INTO ".$TBLPREFIX."favorites VALUES ($newid, '".$DBCONN->escapeSimple($favorite["username"])."','".$DBCONN->escapeSimple($favorite["gid"])."','".$DBCONN->escapeSimple($favorite["type"])."','".$DBCONN->escapeSimple($favorite["file"])."')";
	$res =& dbquery($sql);
	if ($res) return true;
	else return false;
}

//----------------------------------- deleteFavorite
//-- deletes a message in the database
function deleteFavorite($fv_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM ".$TBLPREFIX."favorites WHERE fv_id=".$fv_id;
	$res =& dbquery($sql);
	if ($res) return true;
	else return false;
}

//----------------------------------- getUserFavorites
//-- Return an array of a users messages
function getUserFavorites($username) {
	global $TBLPREFIX, $GEDCOMS, $DBCONN, $CONFIGURED;

	$favorites = array();
	if (!$CONFIGURED || DB::isError($DBCONN)) return $favorites;
	$sql = "SELECT * FROM ".$TBLPREFIX."favorites WHERE fv_username='".$DBCONN->escapeSimple($username)."'";
	$res =& dbquery($sql);
	if (!$res) return $favorites;
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (isset($GEDCOMS[$row["fv_file"]])) {
			$favorite = array();
			$favorite["id"] = $row["fv_id"];
			$favorite["username"] = $row["fv_username"];
			$favorite["gid"] = $row["fv_gid"];
			$favorite["type"] = $row["fv_type"];
			$favorite["file"] = $row["fv_file"];
			$favorites[] = $favorite;
		}
	}
	$res->free();
	return $favorites;
}

/**
 * get blocks for the given username
 *
 * retrieve the block configuration for the given user
 * if no blocks have been set yet, and the username is a valid user (not a gedcom) then try and load
 * the defaultuser blocks.
 * @param string $username	the username or gedcom name for the blocks
 * @return array	an array of the blocks.  The two main indexes in the array are "main" and "right"
 */
function getBlocks($username) {
	global $TBLPREFIX, $GEDCOMS, $DBCONN;

	$blocks = array();
	$blocks["main"] = array();
	$blocks["right"] = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."blocks WHERE b_username='".$DBCONN->escapeSimple($username)."' ORDER BY b_location, b_order";
	$res =& dbquery($sql);
	if ($res->numRows() > 0) {
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			$row = db_cleanup($row);
			if (!isset($row["b_config"])) $row["b_config"]="";
			if ($row["b_location"]=="main") $blocks["main"][$row["b_order"]] = array($row["b_name"], unserialize($row["b_config"]));
			if ($row["b_location"]=="right") $blocks["right"][$row["b_order"]] = array($row["b_name"], unserialize($row["b_config"]));
		}
	}
	else {
		$user = getUser($username);
		if ($user) {
			//-- if no blocks found, check for a default block setting
			$sql = "SELECT * FROM ".$TBLPREFIX."blocks WHERE b_username='defaultuser' ORDER BY b_location, b_order";
			$res2 =& dbquery($sql);
			while($row =& $res2->fetchRow(DB_FETCHMODE_ASSOC)){
				$row = db_cleanup($row);
				if (!isset($row["b_config"])) $row["b_config"]="";
				if ($row["b_location"]=="main") $blocks["main"][$row["b_order"]] = array($row["b_name"], unserialize($row["b_config"]));
				if ($row["b_location"]=="right") $blocks["right"][$row["b_order"]] = array($row["b_name"], unserialize($row["b_config"]));
			}
			$res2->free();
		}
	}
	$res->free();
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
	global $TBLPREFIX, $DBCONN;

	$sql = "DELETE FROM ".$TBLPREFIX."blocks WHERE b_username='".$DBCONN->escapeSimple($username)."'";
	$res =& dbquery($sql);
	foreach($ublocks["main"] as $order=>$block) {
		$newid = get_next_id("blocks", "b_id");
		$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES ($newid, '".$DBCONN->escapeSimple($username)."', 'main', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
		$res =& dbquery($sql);
		if ($setdefault) {
			$newid = get_next_id("blocks", "b_id");
			$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES ($newid, 'defaultuser', 'main', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
			$res =& dbquery($sql);
		}
	}
	foreach($ublocks["right"] as $order=>$block) {
		$newid = get_next_id("blocks", "b_id");
		$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES ($newid, '".$DBCONN->escapeSimple($username)."', 'right', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
		$res =& dbquery($sql);
		if ($setdefault) {
			$newid = get_next_id("blocks", "b_id");
			$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES ($newid, 'defaultuser', 'right', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
			$res =& dbquery($sql);
		}
	}
}

/**
 * Adds a news item to the database
 *
 * This function adds a news item represented by the $news array to the database.
 * If the $news array has an ["id"] field then the function assumes that it is
 * as update of an older news item.
 *
 * @author John Finlay
 * @param array $news a news item array
 */
function addNews($news) {
	global $TBLPREFIX, $DBCONN;

	if (!isset($news["date"])) $news["date"] = time()-$_SESSION["timediff"];
	//$sql = "CREATE TABLE ".$TBLPREFIX."news (n_id INT NOT NULL auto_increment, n_username VARCHAR(100), n_date INT, n_text TEXT, PRIMARY KEY(n_id))";
	if (!empty($news["id"])) {
		// In case news items are added from usermigrate, it will also contain an ID.
		// So we check first if the ID exists in the database. If not, insert instead of update.
		$sql = "SELECT * FROM ".$TBLPREFIX."news where n_id=".$news["id"];
		$res =& dbquery($sql);
		if ($res->numRows() == 0) {
			$sql = "INSERT INTO ".$TBLPREFIX."news VALUES (".$news["id"].", '".$DBCONN->escapeSimple($news["username"])."','".$DBCONN->escapeSimple($news["date"])."','".$DBCONN->escapeSimple($news["title"])."','".$DBCONN->escapeSimple($news["text"])."')";
		}
		else {
			$sql = "UPDATE ".$TBLPREFIX."news SET n_date='".$DBCONN->escapeSimple($news["date"])."', n_title='".$DBCONN->escapeSimple($news["title"])."', n_text='".$DBCONN->escapeSimple($news["text"])."' WHERE n_id=".$news["id"];
		}
		$res->free();
	}
	else {
		$newid = get_next_id("news", "n_id");
		$sql = "INSERT INTO ".$TBLPREFIX."news VALUES ($newid, '".$DBCONN->escapeSimple($news["username"])."','".$DBCONN->escapeSimple($news["date"])."','".$DBCONN->escapeSimple($news["title"])."','".$DBCONN->escapeSimple($news["text"])."')";
	}
	$res =& dbquery($sql);
	if ($res) return true;
	else return false;
}

/**
 * Deletes a news item from the database
 *
 * @author John Finlay
 * @param int $news_id the id number of the news item to delete
 */
function deleteNews($news_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM ".$TBLPREFIX."news WHERE n_id=".$news_id;
	$res =& dbquery($sql);
	if ($res) return true;
	else return false;
}

/**
 * Gets the news items for the given user or gedcom
 *
 * @param String $username the username or gedcom file name to get news items for
 */
function getUserNews($username) {
	global $TBLPREFIX, $DBCONN;

	$news = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."news WHERE n_username='".$DBCONN->escapeSimple($username)."' ORDER BY n_date DESC";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$n = array();
		$n["id"] = $row["n_id"];
		$n["username"] = $row["n_username"];
		$n["date"] = $row["n_date"];
		$n["title"] = stripslashes($row["n_title"]);
		$n["text"] = stripslashes($row["n_text"]);
		$n["anchor"] = "article".$row["n_id"];
		$news[$row["n_id"]] = $n;
	}
	$res->free();
	return $news;
}

/**
 * Gets the news item for the given news id
 *
 * @param int $news_id the id of the news entry to get
 */
function getNewsItem($news_id) {
	global $TBLPREFIX;

	$news = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."news WHERE n_id='$news_id'";
	$res =& dbquery($sql);
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$n = array();
		$n["id"] = $row["n_id"];
		$n["username"] = $row["n_username"];
		$n["date"] = $row["n_date"];
		$n["title"] = stripslashes($row["n_title"]);
		$n["text"] = stripslashes($row["n_text"]);
		$n["anchor"] = "article".$row["n_id"];
		$res->free();
		return $n;
	}
}

?>
