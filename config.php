<?php
/**
 * Main configuration file required by all other files in PGV
 *
 * The variables in this file are the main configuration variable for the site
 * Gedcom specific configuration variables are stored in the config_gedcom.php file.
 * Site administrators may edit these settings online through the editconfig.php file.
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
 * @subpackage Admin
 * @see editconfig.php
 * @version $Id: config.php,v 1.3 2006/03/06 00:26:33 skenow Exp $
 */

if (preg_match("/\Wconfig.php/", $_SERVER["PHP_SELF"])>0) {
	print "Got your hand caught in the cookie jar.";
	exit;
}
require_once "../../mainfile.php";
/**
 * Absolut Path to PhpGedView installation
 *
 * this is mostly used when running PGV as a module under a NUKE environment
 * @global $PGV_BASE_DIRECTORY
 */
$PGV_BASE_DIRECTORY = "";						//-- path to phpGedView (Only needed when running as phpGedView from another php program such as postNuke, otherwise leave it blank)
$PGV_DATABASE = "db";						//-- which database is being used, file indexes or mysql
$DBTYPE = "mysql";								//-- type of database to connect when using the PEAR:DB module
$DBHOST = XOOPS_DB_HOST;							//-- Host where MySQL database is kept
$DBUSER = XOOPS_DB_USER;									//-- MySQL database User Name
$DBPASS = XOOPS_DB_PASS;									//-- MySQL database User Password
$DBNAME = XOOPS_DB_NAME;							//-- The MySQL database name where you want PHPGedView to build its tables
$TBLPREFIX = XOOPS_DB_PREFIX."_pgv_";							//-- prefix to include on table names
$INDEX_DIRECTORY = "./index/";					//-- Readable and Writeable Directory to store index files (include the trailing "/")
$AUTHENTICATION_MODULE = "authentication_xoops.php";	//-- File that contains authentication functions
$PGV_STORE_MESSAGES = true;						//-- allow messages sent to users to be stored in the PGV system
$PGV_SIMPLE_MAIL = false;						//-- allow admins to set this so that they can override the name <emailaddress> combination in the emails
$USE_REGISTRATION_MODULE = false;				//-- turn on the user self registration module
$REQUIRE_ADMIN_AUTH_REGISTRATION = true;		//-- require an admin user to authorize a new registration before a user can login
$ALLOW_USER_THEMES = true;						//-- Allow user to set their own theme
$ALLOW_CHANGE_GEDCOM = false;					//-- A true value will provide a link in the footer to allow users to change the gedcom they are viewing
$LOGFILE_CREATE = "monthly";					//-- set how often new log files are created, "none" turns logs off, "daily", "weekly", "monthly", "yearly"
$PGV_SESSION_SAVE_PATH = "";					//-- Path to save PHP session Files -- DO NOT MODIFY unless you know what you are doing
												//-- leaving it blank will use the default path for your php configuration as found in php.ini
$PGV_SESSION_TIME = "7200";						//-- number of seconds to wait before an inactive session times out
$SERVER_URL = XOOPS_URL."/modules/pgv/";								//-- the URL used to access this server
$LOGIN_URL = "";								//-- the URL to use to go to the login page, use this value if you want to redirect to a different site when users login, useful for switching from http to https
$PGV_MEMORY_LIMIT = "32M";						//-- the maximum amount of memory that PGV should be allowed to consume
$ALLOW_REMEMBER_ME = true;						//-- whether the users have the option of being remembered on the current computer
$CONFIG_VERSION = "3.2";						//-- the PGV version that corresponds to this config schema

$CONFIGURED = false;
require 'xoops_headfoot.php';
require_once($PGV_BASE_DIRECTORY."includes/session.php");
?>