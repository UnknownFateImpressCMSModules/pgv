<?php
/**
 * Online UI for editing config.php site configuration variables
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
 * @see config.php
 * @version $Id: editconfig.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
 */

require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
if (!defined("DB_ERROR")) require_once('includes/DB.php');

if (empty($action)) $action="";
if (!isset($LOGIN_URL)) $LOGIN_URL = "";

if ($CONFIGURED) {
if (($PGV_DATABASE=='index')||(check_db())) {
	//-- check if no users have been defined and create the main admin user
	if (!adminUserExists()) {
		print_header($pgv_lang["configure_head"]);
		print "<span class=\"subheaders\">".$pgv_lang["configure"]."</span><br />";
		print $pgv_lang["welcome_new"]."<br />";
		if ($action=="createadminuser") {
			if ($pass1==$pass2) {
				$user = array();
				$user["username"]=$username;
				$user["fullname"]=$fullname;
				$user["password"]=crypt($pass1);
				$user["canedit"] = array();
				$user["rootid"] = array();
				$user["gedcomid"] = array();
				$user["canadmin"]=true;
				$user["email"]=$emailadress;
				$user["verified"] = "yes";
				$user["verified_by_admin"] = "yes";
				$user["pwrequested"] = "";
				$user["theme"] = "";
				$user["theme"] = "Y";
				$user["language"] = $LANGUAGE;
				$user["reg_timestamp"] = date("U");
				$user["reg_hashcode"] = "";
				$user["loggedin"] = "Y";
				$user["sessiontime"] = 0;
				$user["contactmethod"] = "messaging2";
				$user["visibleonline"] = true;
				$user["editaccount"] = true;
				$user["default_tab"] = 0;
				$au = addUser($user);
				if ($au) {
					print $pgv_lang["user_created"];
					print "<br />";
					print "<a href=\"editgedcoms.php\">";
					print $pgv_lang["click_here_to_continue"];
					print "</a><br />";
					$_SESSION["pgv_user"]=$username;
					print_footer();
					exit;
				}
				else {
					print "<span class=\"error\">";
					print $pgv_lang["user_create_error"];
					print "<br /></span>";
					print_footer();
					exit;
				}
			}
			else {
				print "<span class=\"error\">";
				print $pgv_lang["password_mismatch"];
				print "<br /></span>";
				print_footer();
				exit;
			}
		}
		else {
			?>
			<script language="JavaScript" type="text/javascript">
				function checkform(frm) {
					if (frm.username.value=="") {
						alert("<?php print $pgv_lang["enter_username"]; ?>");
						frm.username.focus();
						return false;
					}
					if (frm.fullname.value=="") {
						alert("<?php print $pgv_lang["enter_fullname"]; ?>");
						frm.fullname.focus();
						return false;
					}
					if (frm.pass1.value=="") {
						alert("<?php print $pgv_lang["enter_password"]; ?>");
						frm.pass1.focus();
						return false;
					}
					if (frm.pass2.value=="") {
						alert("<?php print $pgv_lang["confirm_password"]; ?>");
						frm.pass2.focus();
						return false;
					}
					return true;
				}
			</script>
			<form method="post" onsubmit="return checkform(this);">
			<input type="hidden" name="action" value="createadminuser" />
			<b><?php print $pgv_lang["default_user"];?></b><br />
			<?php print $pgv_lang["about_user"];?><br /><br />
			<table>
				<tr><td align="right"><?php print $pgv_lang["username"];?></td><td><input type="text" name="username" /></td></tr>
				<tr><td align="right"><?php print $pgv_lang["fullname"];?></td><td><input type="text" name="fullname" /></td></tr>
				<tr><td align="right"><?php print $pgv_lang["password"];?></td><td><input type="password" name="pass1" /></td></tr>
				<tr><td align="right"><?php print $pgv_lang["confirm"];?></td><td><input type="password" name="pass2" /></td></tr>
				<tr><td align="right"><?php print $pgv_lang["emailadress"];?></td><td><input type="text" name="emailadress" size="45" /></td></tr>
			</table>
			<input type="submit" value="<?php print $pgv_lang["create_user"]; ?>" />
			</form>
			<?php
			print_footer();
			exit;
		}
	}
	if (!userIsAdmin(getUserName())) {
		//-- upgrade the database   
		//setup_database(1);
		cleanup_database();   
   		header("Location: login.php?url=editconfig.php");
		exit;
	}
}
}
/*else {   
	//-- set the default to sqlite for php 5+   
    if (empty($action)) {   
		if (phpversion()>=5) {   
			$PGV_DATABASE = "db";   
            $DBTYPE="sqlite";   
            $DBNAME="index/phpgedview.db";   
            $AUTHENTICATION_MODULE = "authentication_mysql.php";   
        }   
     }   
 } */


print_header($pgv_lang["configure_head"]);
print "<span class=\"subheaders\">";
print "<center>";
print $pgv_lang["configure"];
print "</center>";
print "</span><br /><b>";
print $pgv_lang["welcome"];
print "</b><br />";
if ($action=="update") {
	if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
	$boolarray = array();
	$boolarray["yes"]="true";
	$boolarray["no"]="false";
	$boolarray[false]="false";
	$boolarray[true]="true";
	print $pgv_lang["performing_update"];
	print "<br />";
	$configtext = implode('', file("config.php"));
	print $pgv_lang["config_file_read"];
	print "<br />\n";
	if (preg_match("'://'", $NEW_SERVER_URL)==0) $NEW_SERVER_URL = "http://".$NEW_SERVER_URL;
	if (preg_match("'/$'", $NEW_SERVER_URL)==0) $NEW_SERVER_URL .= "/";
	$_POST["NEW_INDEX_DIRECTORY"] = preg_replace('/\\\/','/',$_POST["NEW_INDEX_DIRECTORY"]);
	$configtext = preg_replace('/\$PGV_DATABASE\s*=\s*".*";/', "\$PGV_DATABASE = \"".$_POST["NEW_PGV_DATABASE"]."\";", $configtext);
	if ($_POST["NEW_PGV_DATABASE"]=="index") $_POST["NEW_DBTYPE"] = "";
	if (preg_match('/\$DBTYPE\s*=\s*".*";/', $configtext)>0) {
		$configtext = preg_replace('/\$DBTYPE\s*=\s*".*";/', "\$DBTYPE = \"".$_POST["NEW_DBTYPE"]."\";", $configtext);
	}
	else {
		$configtext = preg_replace('/\$DBHOST/', "\$DBTYPE = \"".$_POST["NEW_DBTYPE"]."\";\r\n\$DBHOST", $configtext);
	}
	$configtext = preg_replace('/\$DBHOST\s*=\s*".*";/', "\$DBHOST = \"".$_POST["NEW_DBHOST"]."\";", $configtext);
	$configtext = preg_replace('/\$DBUSER\s*=\s*".*";/', "\$DBUSER = \"".$_POST["NEW_DBUSER"]."\";", $configtext);
	if (!empty($_POST["NEW_DBPASS"])) $configtext = preg_replace('/\$DBPASS\s*=\s*".*";/', "\$DBPASS = \"".$_POST["NEW_DBPASS"]."\";", $configtext);
	$configtext = preg_replace('/\$DBNAME\s*=\s*".*";/', "\$DBNAME = \"".$_POST["NEW_DBNAME"]."\";", $configtext);
	$configtext = preg_replace('/\$TBLPREFIX\s*=\s*".*";/', "\$TBLPREFIX = \"".$_POST["NEW_TBLPREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$ALLOW_CHANGE_GEDCOM\s*=\s*.*;/', "\$ALLOW_CHANGE_GEDCOM = ".$boolarray[$_POST["NEW_ALLOW_CHANGE_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$USE_REGISTRATION_MODULE\s*=\s*.*;/', "\$USE_REGISTRATION_MODULE = ".$boolarray[$_POST["NEW_USE_REGISTRATION_MODULE"]].";", $configtext);
	$configtext = preg_replace('/\$REQUIRE_ADMIN_AUTH_REGISTRATION\s*=\s*.*;/', "\$REQUIRE_ADMIN_AUTH_REGISTRATION = ".$boolarray[$_POST["NEW_REQUIRE_ADMIN_AUTH_REGISTRATION"]].";", $configtext);
	$configtext = preg_replace('/\$PGV_SIMPLE_MAIL\s*=\s*.*;/', "\$PGV_SIMPLE_MAIL = ".$boolarray[$_POST["NEW_PGV_SIMPLE_MAIL"]].";", $configtext);
	$configtext = preg_replace('/\$PGV_STORE_MESSAGES\s*=\s*.*;/', "\$PGV_STORE_MESSAGES = ".$boolarray[$_POST["NEW_PGV_STORE_MESSAGES"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_USER_THEMES\s*=\s*.*;/', "\$ALLOW_USER_THEMES = ".$boolarray[$_POST["NEW_ALLOW_USER_THEMES"]].";", $configtext);
    $configtext = preg_replace('/\$ALLOW_REMEMBER_ME\s*=\s*.*;/', "\$ALLOW_REMEMBER_ME = ".$boolarray[$_POST["NEW_ALLOW_REMEMBER_ME"]].";", $configtext);   
	$configtext = preg_replace('/\$INDEX_DIRECTORY\s*=\s*".*";/', "\$INDEX_DIRECTORY = \"".$_POST["NEW_INDEX_DIRECTORY"]."\";", $configtext);
	$configtext = preg_replace('/\$AUTHENTICATION_MODULE\s*=\s*".*";/', "\$AUTHENTICATION_MODULE = \"".$_POST["NEW_AUTHENTICATION_MODULE"]."\";", $configtext);
	$configtext = preg_replace('/\$LOGFILE_CREATE\s*=\s*".*";/', "\$LOGFILE_CREATE = \"".$_POST["NEW_LOGFILE_CREATE"]."\";", $configtext);
	$configtext = preg_replace('/\$PGV_SESSION_SAVE_PATH\s*=\s*".*";/', "\$PGV_SESSION_SAVE_PATH = \"".$_POST["NEW_PGV_SESSION_SAVE_PATH"]."\";", $configtext);
	$configtext = preg_replace('/\$PGV_SESSION_TIME\s*=\s*".*";/', "\$PGV_SESSION_TIME = \"".$_POST["NEW_PGV_SESSION_TIME"]."\";", $configtext);
	$configtext = preg_replace('/\$SERVER_URL\s*=\s*".*";/', "\$SERVER_URL = \"".$_POST["NEW_SERVER_URL"]."\";", $configtext);
	if (preg_match('/\$DBTYPE\s*=\s*".*";/', $configtext)>0) {
		$configtext = preg_replace('/\$LOGIN_URL\s*=\s*".*";/', "\$LOGIN_URL = \"".$_POST["NEW_LOGIN_URL"]."\";", $configtext);
	}
	else {
		$configtext = preg_replace('/\$PGV_MEMORY_LIMIT/', "\$LOGIN_URL = \"".$_POST["NEW_LOGIN_URL"]."\";\r\n\$PGV_MEMORY_LIMIT", $configtext);
	}
	$configtext = preg_replace('/\$PGV_MEMORY_LIMIT\s*=\s*".*";/', "\$PGV_MEMORY_LIMIT = \"".$_POST["NEW_PGV_MEMORY_LIMIT"]."\";", $configtext);
	$PGV_DATABASE = $_POST["NEW_PGV_DATABASE"];
	$DBHOST = $_POST["NEW_DBHOST"];
    $DBTYPE = $_POST["NEW_DBTYPE"];
	$DBUSER = $_POST["NEW_DBUSER"];
	$DBNAME = $_POST["NEW_DBNAME"];
	if (!empty($_POST["NEW_DBPASS"])) $DBPASS = $_POST["NEW_DBPASS"];
	
	//-- make sure the database configuration is set properly
	if (check_db()) {
		$configtext = preg_replace('/\$CONFIGURED\s*=\s*.*;/', "\$CONFIGURED = true;", $configtext);
		$CONFIGURED = true;
        //-- upgrade the database   
        //setup_database(1);
        cleanup_database(); 
	}
	
	if (!isset($download)) {
		$fp = fopen("config.php", "wb");
		if (!$fp) {
			print "<span class=\"error\">";
			print $pgv_lang["pgv_config_write_error"];
			print "<br /></span>\n";
		}
		else {
			fwrite($fp, $configtext);
			fclose($fp);
			if ($CONFIGURED) print "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.location = 'editconfig.php';\n</script>\n";
		}
		foreach($_POST as $key=>$value) {
			$key=preg_replace("/NEW_/", "", $key);
			if ($value=='yes') $$key=true;
			else if ($value=='no') $$key=false;
			else $$key=$value;
		}
	}
	else {
		$_SESSION["config.php"]=$configtext;
		print "<br /><br /><a href=\"config_download.php?file=config.php\">";
		print $pgv_lang["download_here"];
		print "</a><br /><br />\n";
	}
	AddToLog("Site configuration config.php updated by >".getUserName()."<");
}

?>
<script language="JavaScript" type="text/javascript">
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
	function show_mysql(dbselect, sid) {
		var sbox = document.getElementById(sid);
		var sbox_style = sbox.style;
		var auth_loc = document.getElementById('NEW_AUTHENTICATION_MODULE');

		if (dbselect.options[dbselect.selectedIndex].value=='db') {
			sbox_style.display='block';
			auth_loc.value = 'authentication_mysql.php';
		}
		else {
			sbox_style.display='none';
			auth_loc.value = 'authentication_index.php';
		}
	}
</script>
<form method="post" name="configform" action="editconfig.php">
<?php
	if (!check_db()) {
		print "<span class=\"error\">";
		print $pgv_lang["db_setup_bad"];
		print "</span><br />";
		print "<span class=\"error\">".$DBCONN->getMessage()." ".$DBCONN->getUserInfo()."</span><br />";
		if (($CONFIGURED==true)&&($PGV_DATABASE=="db")) {
			//-- force the incoming user to enter the database password before they can configure the site for security.
			if (!isset($_POST["security_check"]) || !isset($_POST["security_user"]) || (($_POST["security_check"]!=$DBPASS)&&($_POST["security_user"]==$DBUSER))) {
				print "<br /><br />";
				print_text("enter_db_pass");
				print "<br />";
				print $pgv_lang["DBUSER"];
				print " <input type=\"text\" name=\"security_user\" /><br />\n";
				print $pgv_lang["DBPASS"];
				print " <input type=\"password\" name=\"security_check\" /><br />\n";
				print "<input type=\"submit\" value=\"";
				print $pgv_lang["login"];
				print "\" />\n";
				print "</form>\n";
				print_footer();
				exit;
			}
		}
	}

	print $pgv_lang["review_readme"];
	print_text("return_editconfig");
	if ($CONFIGURED) {
		print "<a href=\"editgedcoms.php\"><b>";
		print $pgv_lang["admin_gedcoms"];
		print "</b></a><br /><br />\n";
	}
	$i = 0;
?>
<input type="hidden" name="action" value="update" />
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_DATABASE"];?> <a href="#" onclick="return helpPopup('PGV_DATABASE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_PGV_DATABASE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_DATABASE_help');" onchange="show_mysql(this, 'mysql-options');">
				<option value="index" <?php if ($PGV_DATABASE=='index') print "selected=\"selected\""; ?>><?php print $pgv_lang["index"];?></option>
				<option value="db" <?php if ($PGV_DATABASE=='db') print "selected=\"selected\""; ?>><?php print $pgv_lang["db"];?></option>
			</select>
		</td>
	</tr>
</table>
	<div id="mysql-options" style="display: <?php if ($PGV_DATABASE=='db') print 'block'; else print 'none'; ?>;">
	<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DBTYPE"];?> <a href="#" onclick="return helpPopup('DBTYPE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_DBTYPE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBTYPE_help');">
				<!--<option value="dbase" <?php if ($DBTYPE=='dbase') print "selected=\"selected\""; ?>><?php print $pgv_lang["dbase"];?></option>-->
				<option value="fbsql" <?php if ($DBTYPE=='fbsql') print "selected=\"selected\""; ?>><?php print $pgv_lang["fbsql"];?></option>
				<option value="ibase" <?php if ($DBTYPE=='ibase') print "selected=\"selected\""; ?>><?php print $pgv_lang["ibase"];?></option>
				<option value="ifx" <?php if ($DBTYPE=='ifx') print "selected=\"selected\""; ?>><?php print $pgv_lang["ifx"];?></option>
				<option value="msql" <?php if ($DBTYPE=='msql') print "selected=\"selected\""; ?>><?php print $pgv_lang["msql"];?></option>
				<option value="mssql" <?php if ($DBTYPE=='mssql') print "selected=\"selected\""; ?>><?php print $pgv_lang["mssql"];?></option>
				<option value="mysql" <?php if ($DBTYPE=='mysql') print "selected=\"selected\""; ?>><?php print $pgv_lang["mysql"];?></option>
				<option value="mysqli" <?php if ($DBTYPE=='mysqli') print "selected=\"selected\""; ?>><?php print $pgv_lang["mysqli"];?></option>
				<option value="oci8" <?php if ($DBTYPE=='oci8') print "selected=\"selected\""; ?>><?php print $pgv_lang["oci8"];?></option>
				<option value="pgsql" <?php if ($DBTYPE=='pgsql') print "selected=\"selected\""; ?>><?php print $pgv_lang["pgsql"];?></option>
				<option value="sqlite" <?php if ($DBTYPE=='sqlite') print "selected=\"selected\""; ?>><?php print $pgv_lang["sqlite"];?></option>
				<!--<option value="sybase" <?php if ($DBTYPE=='sybase') print "selected=\"selected\""; ?>><?php print $pgv_lang["sybase"];?></option>-->
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DBHOST"];?> <a href="#" onclick="return helpPopup('DBHOST_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_DBHOST" value="<?php print XOOPS_DB_HOST ?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBHOST_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DBUSER"];?> <a href="#" onclick="return helpPopup('DBUSER_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_DBUSER" value="<?php print XOOPS_DB_USER ?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBUSER_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DBPASS"];?> <a href="#" onclick="return helpPopup('DBPASS_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="password" name="NEW_DBPASS" value="<?php print XOOPS_DB_PASS ?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBPASS_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["DBNAME"];?> <a href="#" onclick="return helpPopup('DBNAME_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_DBNAME" value="<?php print XOOPS_DB_NAME ?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBNAME_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["TBLPREFIX"];?> <a href="#" onclick="return helpPopup('TBLPREFIX_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_TBLPREFIX" value="<?php print XOOPS_DB_PREFIX.'_pgv_' ?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('TBLPREFIX_help');" /></td>
	</tr>
	</table>
	</div>
	<table class="facts_table">
	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALLOW_CHANGE_GEDCOM"];?> <a href="#" onclick="return helpPopup('ALLOW_CHANGE_GEDCOM_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALLOW_CHANGE_GEDCOM" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_CHANGE_GEDCOM_help');">
				<option value="yes" <?php if ($ALLOW_CHANGE_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_CHANGE_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["INDEX_DIRECTORY"];?> <a href="#" onclick="return helpPopup('INDEX_DIRECTORY_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" size="50" name="NEW_INDEX_DIRECTORY" value="<?php print $INDEX_DIRECTORY?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('INDEX_DIRECTORY_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["AUTHENTICATION_MODULE"];?> <a href="#" onclick="return helpPopup('AUTHENTICATION_MODULE_help');"><b>?</b></a></td>
		<td class="facts_value"><input size="50" type="text" id="NEW_AUTHENTICATION_MODULE" name="NEW_AUTHENTICATION_MODULE" value="<?php print $AUTHENTICATION_MODULE?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('AUTHENTICATION_MODULE_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_STORE_MESSAGES"];?> <a href="#" onclick="return helpPopup('PGV_STORE_MESSAGES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_PGV_STORE_MESSAGES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_STORE_MESSAGES_help');">
				<option value="yes" <?php if ($PGV_STORE_MESSAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$PGV_STORE_MESSAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["USE_REGISTRATION_MODULE"];?> <a href="#" onclick="return helpPopup('USE_REGISTRATION_MODULE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_USE_REGISTRATION_MODULE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_REGISTRATION_MODULE_help');">
				<option value="yes" <?php if ($USE_REGISTRATION_MODULE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_REGISTRATION_MODULE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
 	
 	<tr>
 		<td class="facts_label"><?php print $pgv_lang["REQUIRE_ADMIN_AUTH_REGISTRATION"];?> <a href="#" onclick="return helpPopup('REQUIRE_ADMIN_AUTH_REGISTRATION_help');"><b>?</b></a></td>
 		<td class="facts_value"><select name="NEW_REQUIRE_ADMIN_AUTH_REGISTRATION" tabindex="<?php $i++; print $i?>" onfocus="getHelp('REQUIRE_ADMIN_AUTH_REGISTRATION_help');">
 				<option value="yes" <?php if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
 				<option value="no" <?php if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
 		</td>
 	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_SIMPLE_MAIL"];?> <a href="#" onclick="return helpPopup('PGV_SIMPLE_MAIL_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_PGV_SIMPLE_MAIL" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SIMPLE_MAIL_help');">
				<option value="yes" <?php if ($PGV_SIMPLE_MAIL) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$PGV_SIMPLE_MAIL) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALLOW_USER_THEMES"];?> <a href="#" onclick="return helpPopup('ALLOW_USER_THEMES_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALLOW_USER_THEMES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_USER_THEMES_help');">
				<option value="yes" <?php if ($ALLOW_USER_THEMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_USER_THEMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["ALLOW_REMEMBER_ME"]?> <a href="#" onclick="return helpPopup('ALLOW_REMEMBER_ME_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_ALLOW_REMEMBER_ME" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_REMEMBER_ME_help');">
 				<option value="yes" <?php if ($ALLOW_REMEMBER_ME) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
 				<option value="no" <?php if (!$ALLOW_REMEMBER_ME) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="facts_label"><?php print $pgv_lang["LOGFILE_CREATE"]?> <a href="#" onclick="return helpPopup('LOGFILE_CREATE_help');"><b>?</b></a></td>
		<td class="facts_value"><select name="NEW_LOGFILE_CREATE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('LOGFILE_CREATE_help');">
				<option value="none" <?php if ($LOGFILE_CREATE=="none") print "selected=\"selected\""; ?>><?php print $pgv_lang["no_logs"];?></option>
				<option value="daily" <?php if ($LOGFILE_CREATE=="daily") print "selected=\"selected\""; ?>><?php print $pgv_lang["daily"];?></option>
				<option value="weekly" <?php if ($LOGFILE_CREATE=="weekly") print "selected=\"selected\""; ?>><?php print $pgv_lang["weekly"];?></option>
				<option value="monthly" <?php if ($LOGFILE_CREATE=="monthly") print "selected=\"selected\""; ?>><?php print $pgv_lang["monthly"];?></option>
				<option value="yearly" <?php if ($LOGFILE_CREATE=="yearly") print "selected=\"selected\""; ?>><?php print $pgv_lang["yearly"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["SERVER_URL"];?> <a href="#" onclick="return helpPopup('SERVER_URL_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_SERVER_URL" value="<?php print $SERVER_URL?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SERVER_URL_help');" size="100" />
		<br /><?php 
			$GUESS_URL = stripslashes("http://".$_SERVER["SERVER_NAME"].dirname($PHP_SELF)."/");
			print_text("server_url_note"); 
			?>
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["LOGIN_URL"];?> <a href="#" onclick="return helpPopup('LOGIN_URL_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_LOGIN_URL" value="<?php print $LOGIN_URL?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('LOGIN_URL_help');" size="100" />
		</td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_SESSION_SAVE_PATH"];?> <a href="#" onclick="return helpPopup('PGV_SESSION_SAVE_PATH_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" size="50" name="NEW_PGV_SESSION_SAVE_PATH" value="<?php print $PGV_SESSION_SAVE_PATH?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SESSION_SAVE_PATH_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_SESSION_TIME"];?> <a href="#" onclick="return helpPopup('PGV_SESSION_TIME_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_PGV_SESSION_TIME" value="<?php print $PGV_SESSION_TIME?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SESSION_TIME_help');" /></td>
	</tr>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["PGV_MEMORY_LIMIT"];?> <a href="#" onclick="return helpPopup('PGV_MEMORY_LIMIT_help');"><b>?</b></a></td>
		<td class="facts_value"><input type="text" name="NEW_PGV_MEMORY_LIMIT" value="<?php print $PGV_MEMORY_LIMIT?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_MEMORY_LIMIT_help');" /></td>
	</tr>
</table>
<br />
<input type="submit" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["save_config"];?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["reset"];?>" /><br />
<?php
	if (!is_writable("config.php")) {
			print_text("not_writable");
			print "<br /><input type=\"submit\" value=\"";
			print $pgv_lang["download_file"];
			print "\" name=\"download\" /><br />\n";
	}
?>
</form>
<?php if (!$CONFIGURED) { ?>
<script language="JavaScript" type="text/javascript">
	helpPopup('welcome_new_help');
</script>
<?php
}

print_footer();

?>