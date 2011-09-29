<?php
/**
 * Administrative User Interface.
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
 * @version $Id: useradmin.php,v 1.3 2006/01/09 00:46:23 skenow Exp $
 */

/**
 * load configuration and context
 */
require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
global $TEXT_DIRECTION;
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

// Remove slashes
if (isset($ufullname)) $ufullname = stripslashes($ufullname);

if (!isset($action)) $action="";

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!userIsAdmin(getUserName())) {
	header("Location: login.php?url=useradmin.php");
	exit;
}
print_header("PhpGedView ".$pgv_lang["user_admin"]);

// Javascript for edit form
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checkform(frm) {
		if (frm.uusername.value=="") {
			alert("<?php print $pgv_lang["enter_username"]; ?>");
			frm.uusername.focus();
			return false;
		}
		if (frm.ufullname.value=="") {
			alert("<?php print $pgv_lang["enter_fullname"]; ?>");
			frm.ufullname.focus();
			return false;
		}
	    if ((frm.pass1.value!="")&&(frm.pass1.value.length < 6)) {
	      alert("<?php print $pgv_lang["passwordlength"]; ?>");
	      frm.pass1.value = "";
	      frm.pass2.value = "";
	      frm.pass1.focus();
	      return false;
	    }
		if ((frm.emailadress.value!="")&&(frm.emailadress.value.indexOf("@")==-1)) {
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.emailadress.focus();
			return false;
		}
		return true;
	}
	var pasteto;
	function open_find(textbox, gedcom) {
		pasteto = textbox;
		findwin = window.open('findid.php?GEDCOM='+gedcom, '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
//-->
</script>
<?php
print "<div class=\"center\"><table class=\"list_table $TEXT_DIRECTION\"><tr><td>";
//-- section to create a new user
if ($action=="createuser") {
	$alphabet = getAlphabet();
	$alphabet .= "_-. ";
	$i = 1;
	$pass = TRUE;
	while (strlen($uusername) > $i) {
		if (stristr($alphabet, $uusername{$i}) != TRUE){
			$pass = FALSE;
			break;
		}
		$i++;
	}
	if ($pass == TRUE){
		if (getUser($uusername)!==false) {
			print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
		}
		else if ($pass1==$pass2) {
			$user = array();
			$user["username"]=$uusername;
			$user["fullname"]=$ufullname;

			/* added by Kurt - start */
			$user["email"]=$emailadress;
			if (!isset($verified)) $verified = "";
			$user["verified"] = $verified;
			if (!isset($verified_by_admin)) $verified_by_admin = "";
			$user["verified_by_admin"] = $verified_by_admin;
			if (!empty($user_language)) $user["language"] = $user_language;
			else $user["language"] = $LANGUAGE;
			$user["pwrequested"] = $pwrequested;
			$user["reg_timestamp"] = $reg_timestamp;
			$user["reg_hashcode"] = $reg_hashcode;
			/* added by Kurt - end */

			$user["gedcomid"]=array();
			$user["rootid"]=array();
			$user["canedit"]=array();
			foreach($GEDCOMS as $ged=>$gedarray) {
				$file = $ged;
				$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
				$varname = "gedcomid_$ged";
				if (isset($$varname)) $user["gedcomid"][$file]=$$varname;
				$varname = "rootid_$ged";
				if (isset($$varname)) $user["rootid"][$file]=$$varname;
				$varname = "canedit_$ged";
				if (isset($$varname)) $user["canedit"][$file]=$$varname;
				else $user["canedit"][$file]="none";
			}
			$user["password"]=crypt($pass1);
			if ((isset($canadmin))&&($canadmin=="yes")) $user["canadmin"]=true;
			else $user["canadmin"]=false;
			if ((isset($visibleonline))&&($visibleonline=="yes")) $user["visibleonline"]=true;
			else $user["visibleonline"]=false;
			if ((isset($editaccount))&&($editaccount=="yes")) $user["editaccount"]=true;
			else $user["editaccount"]=false;
			if (!isset($new_user_theme)) $new_user_theme="";
			$user["theme"] = $new_user_theme;
			$user["loggedin"] = "N";
			$user["sessiontime"] = 0;
			if (!isset($new_contact_method)) $new_contact_method="messaging2";
			$user["contactmethod"] = $new_contact_method;
			if (isset($new_default_tab)) $user["default_tab"] = $new_default_tab;
			$au = addUser($user, "added");
			if ($au) {
				print $pgv_lang["user_created"]."<br />";
			}
			else {
				print "<span class=\"error\">".$pgv_lang["user_create_error"]."<br /></span>";
			}
		}
		else {
			print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
	}
}
//-- section to delete a user
if ($action=="deleteuser") {
	deleteUser($username, "deleted");
}
//-- section to update a user by first deleting them
//-- and then adding them again
if ($action=="edituser2") {
	$alphabet = getAlphabet();
	$alphabet .= "_-. ";
	$i = 1;
	$pass = TRUE;
	while (strlen($uusername) > $i) {
		if (stristr($alphabet, $uusername{$i}) != TRUE){
			$pass = FALSE;
			break;
		}
		$i++;
	}
	if ($pass == TRUE){
		if (($uusername!=$oldusername)&&(getUser($uusername)!==false)) {
			print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
			$action="edituser";
		}
		else if ($pass1==$pass2) {
			$newuser = array();
			$olduser = getUser($oldusername);
			$newuser = $olduser;

			if (empty($pass1)) $newuser["password"]=$olduser["password"];
			else $newuser["password"]=crypt($pass1);
			deleteUser($oldusername, "changed");
			$newuser["username"]=$uusername;
			$newuser["fullname"]=$ufullname;

			if (!empty($user_language)) $newuser["language"] = $user_language;

			/* added by Kurt - start */
			$newuser["email"]=$emailadress;
			if (!isset($verified)) $verified = "";
			$newuser["verified"] = $verified;
			if (!isset($verified_by_admin)) $verified_by_admin = "";
			$newuser["verified_by_admin"] = $verified_by_admin;
			/* added by Kurt - end */

			if (!empty($new_contact_method)) $newuser["contactmethod"] = $new_contact_method;
			if (isset($new_default_tab)) $newuser["default_tab"] = $new_default_tab;

			if (!isset($user_theme)) $user_theme="";
			$newuser["theme"] = $user_theme;
			$newuser["gedcomid"]=array();
			$newuser["rootid"]=array();
			$newuser["canedit"]=array();
			foreach($GEDCOMS as $ged=>$gedarray) {
				$file = $ged;
				$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
				$varname = "gedcomid_$ged";
				if (isset($$varname)) $newuser["gedcomid"][$file]=$$varname;
				$varname = "rootid_$ged";
				if (isset($$varname)) $newuser["rootid"][$file]=$$varname;
				$varname = "canedit_$ged";
				if (isset($$varname)) $newuser["canedit"][$file]=$$varname;
				else $user["canedit"][$file]="none";
			}
			if ($olduser["username"]!=getUserName()) {
				if ((isset($canadmin))&&($canadmin=="yes")) $newuser["canadmin"]=true;
				else $newuser["canadmin"]=false;
			}
			else $newuser["canadmin"]=$olduser["canadmin"];
			if ((isset($visibleonline))&&($visibleonline=="yes")) $newuser["visibleonline"]=true;
			else $newuser["visibleonline"]=false;
			if ((isset($editaccount))&&($editaccount=="yes")) $newuser["editaccount"]=true;
			else $newuser["editaccount"]=false;
			addUser($newuser, "changed");

			//-- if the user was just verified by the admin, then send the user a message
			if (($olduser["verified_by_admin"]!=$newuser["verified_by_admin"])&&(!empty($newuser["verified_by_admin"]))) {

				// Switch to the users language
				$oldlanguage = $LANGUAGE;
				$LANGUAGE = $newuser["language"];
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

				$message = array();
				$message["to"] = $newuser["username"];
				$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
				$headers = "From: phpgedview-noreply@".$host;
				$message["from"] = getUserName();
				if (substr($SERVER_URL, -1) == "/"){
					$message["subject"] = str_replace("#SERVER_NAME#", substr($SERVER_URL,0, (strlen($SERVER_URL)-1)), $pgv_lang["admin_approved"]);
					$message["body"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]).$pgv_lang["you_may_login"]."\r\n\r\n".substr($SERVER_URL,0, (strlen($SERVER_URL)-1))."/index.php?command=user\r\n";
				}
				else {
					$message["subject"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]);
					$message["body"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]).$pgv_lang["you_may_login"]."\r\n\r\n".$SERVER_URL."/index.php?command=user\r\n";
				}
				$message["created"] = "";
				$message["method"] = "messaging2";
				addMessage($message);

				// Switch back to the page language
				$LANGUAGE = $oldlanguage;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];
			}
		}
		else {
			print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
			$action="edituser";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
	}
}
//-- print the form to edit a user
if ($action=="edituser") {
	$user = getUser($username);
if (!isset($user['contactmethod'])) $user['contactmethod'] = "none";
print "\n\t<h2>".$pgv_lang["update_user"]."</h2>";
?>
<form name="editform" method="post" action="useradmin.php" onsubmit="return checkform(this);">
  <input type="hidden" name="action" value="edituser2" />
  <input type="hidden" name="oldusername" value="<?php print $username; ?>" />
  <table class="list_table <?php print $TEXT_DIRECTION;?>" border="0">
    <tr>
      <td class="facts_label"><?php print $pgv_lang["username"];print_help_link("useradmin_username_help", "qm")?></td>
      <td class="facts_value"><input type="text" name="uusername" value="<?php print $user['username']?>" /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["fullname"];print_help_link("useradmin_fullname_help", "qm")?></td>
      <td class="facts_value"><input type="text" name="ufullname" value="<?php print PrintReady($user['fullname'])?>" size="50" /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["gedcomid"];print_help_link("useradmin_gedcomid_help", "qm")?></td>
      <td class="facts_value">
	<table align="<?php print$TEXT_DIRECTION=="rtl"?"right":"left"; ?>">
         <?php
	  foreach($GEDCOMS as $ged=>$gedarray)
	  {
	    $file = $ged;
	    $ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
	  ?><tr>
	  <td><?php print $file;?> :</td>
	  <td> <input type="text" name="<?php print "gedcomid_$ged"; ?>" value="<?php
	    if (isset($user['gedcomid'][$file])) print $user['gedcomid'][$file];
	    print "\" /><font size=\"1\"><a href=\"javascript:open_find(document.editform.gedcomid_$ged, '$file');\"> ".$pgv_lang["find_id"]."</a>";
		if (isset($user['gedcomid'][$file])) {
			$sged = $GEDCOM;
			$GEDCOM = $file;
			print "\n<span class=\"list_item\">   ".get_person_name($user['gedcomid'][$file]);
			print_first_major_fact($user['gedcomid'][$file]);
			$GEDCOM = $sged;
			print "</span>\n";
		}
		print "</font>";
	  ?></td>
	  </tr>
	<?php } ?></table>
      </td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["rootid"];print_help_link("useradmin_rootid_help", "qm")?></td>
      <td class="facts_value">
	<table align="<?php print$TEXT_DIRECTION=="rtl"?"right":"left"; ?>">
	  <?php
	  foreach($GEDCOMS as $ged=>$gedarray)
	  {
	    $file = $ged;
	    $ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
	  ?><tr>
	    <td><?php print $file;?> :</td>
	    <td> <input type="text" name="<?php print "rootid_$ged"; ?>" value="<?php
	    if (isset($user['rootid'][$file])) print $user['rootid'][$file];
	    print "\" /><font size=\"1\"><a href=\"javascript:open_find(document.editform.rootid_$ged, '$file');\"> ".$pgv_lang["find_id"]."</a>";
		if (isset($user['rootid'][$file])) {
			$sged = $GEDCOM;
			$GEDCOM = $file;
			print "\n<span class=\"list_item\">".get_person_name($user['rootid'][$file]);
			print_first_major_fact($user['rootid'][$file]);
			$GEDCOM = $sged;
			print "</span>\n";
		}
		print "</font>";
	    ?></td>
	  </tr>
	<?php } ?></table>
      </td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["password"];print_help_link("useradmin_password_help", "qm")?></td>
      <td class="facts_value"><input type="password" name="pass1" /><br /><?php print $pgv_lang["leave_blank"];?></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["confirm"];print_help_link("useradmin_conf_password_help", "qm")?></td>
      <td class="facts_value"><input type="password" name="pass2" /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["can_admin"];print_help_link("useradmin_can_admin_help", "qm")?></td>
      <td class="facts_value"><input type="checkbox" name="canadmin" value="yes" <?php if ($user['canadmin']) print "checked=\"checked\""; if ($user["username"]==getUserName()) print " disabled=\"disabled\""; ?> /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["can_edit"];print_help_link("useradmin_can_edit_help", "qm")?></td>
      <td class="facts_value">
      <?php
	foreach($GEDCOMS as $ged=>$gedarray) {
		$file = $ged;
		$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
		if (isset($user['canedit'][$file])) {
			if ($user['canedit'][$file]===true) $user['canedit'][$file]="yes";
		}
		else $user['canedit'][$file]="no";
		print "<select name=\"canedit_$ged\" tabindex=\"8\">\n";
		print "<option value=\"none\"";
		if ($user['canedit'][$file]=="none") print " selected=\"selected\"";
		print ">".$pgv_lang["none"]."</option>\n";
		print "<option value=\"access\"";
		if ($user['canedit'][$file]=="access") print " selected=\"selected\"";
		print ">".$pgv_lang["access"]."</option>\n";
		print "<option value=\"edit\"";
		if ($user['canedit'][$file]=="edit") print " selected=\"selected\"";
		print ">".$pgv_lang["edit"]."</option>\n";
		print "<option value=\"accept\"";
		if ($user['canedit'][$file]=="accept") print " selected=\"selected\"";
		print ">".$pgv_lang["accept"]."</option>\n";
		print "<option value=\"admin\"";
		if ($user['canedit'][$file]=="admin") print " selected=\"selected\"";
		print ">".$pgv_lang["admin_gedcom"]."</option>\n";
		print "</select> $file<br />\n";
	}
	?>
      </td>
    </tr>
    <?php /* added by Kurt - start */ ?>
    <tr><td class="facts_label"><?php print $pgv_lang["emailadress"];print_help_link("useradmin_email_help", "qm")?></td><td class="facts_value"><input type="text" name="emailadress" value="<?php print $user['email']?>" size="50" /></td></tr>
    <tr><td class="facts_label"><?php print $pgv_lang["verified"];print_help_link("useradmin_verified_help", "qm")?></td><td class="facts_value"><input type="checkbox" name="verified" value="yes" <?php if ($user['verified']) print "checked=\"checked\"";?> /></td></tr>
    <tr><td class="facts_label"><?php print $pgv_lang["verified_by_admin"];print_help_link("useradmin_verbyadmin_help", "qm")?></td><td class="facts_value"><input type="checkbox" name="verified_by_admin" value="yes" <?php if ($user['verified_by_admin']) print "checked=\"checked\""; ?> /></td></tr>
    <?php /* added by Kurt - end */ ?>
    <tr><td class="facts_label"><?php print $pgv_lang["change_lang"];print_help_link("edituser_change_lang_help", "qm"); ?></td><td class="facts_value" valign="top"><?php
	if ($ENABLE_MULTI_LANGUAGE) {
		print "<select name=\"user_language\" style=\"{ font-size: 9pt; }\">";
		foreach ($pgv_language as $key => $value) {
			if ($language_settings[$key]["pgv_lang_use"]) {
				print "\n\t\t\t<option value=\"$key\"";
				if ($key == $user["language"]) print " selected=\"selected\"";
				print ">" . $pgv_lang[$key] . "</option>";
			}
		}
		print "</select>\n\t\t";
	}
	else print "&nbsp;";
    ?></td></tr>
    <?php if ($ALLOW_USER_THEMES) { ?>
    <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["user_theme"];print_help_link("useradmin_user_theme_help", "qm");?></td><td class="facts_value" valign="top">
    	<select name="user_theme">
    	  <option value=""><?php print $pgv_lang["site_default"]; ?></option>
    	  <?php
    	    $themes = get_theme_names();
    	    foreach($themes as $indexval => $themedir)
    	    {
    	      print "<option value=\"".$themedir["dir"]."\"";
    	      if ($themedir["dir"] == $user["theme"]) print " selected=\"selected\"";
    	      print ">".$themedir["name"]."</option>\n";
    	    }
	?></select>
      </td>
    </tr>
    <?php } ?>
    <tr>
		<td class="facts_label"><?php print $pgv_lang["user_contact_method"];print_help_link("useradmin_user_contact_help", "qm")?></td>
		<td class="facts_value"><select name="new_contact_method">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($user['contactmethod']=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" <?php if ($user['contactmethod']=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"];?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($user['contactmethod']=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"];?></option>
		<?php } ?>
				<option value="mailto" <?php if ($user['contactmethod']=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"];?></option>
				<option value="none" <?php if ($user['contactmethod']=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"];?></option>
			</select>
		</td>
	</tr>
	<tr>
      <td class="facts_label"><?php print $pgv_lang["visibleonline"];print_help_link("useradmin_visibleonline_help", "qm")?></td>
      <td class="facts_value"><input type="checkbox" name="visibleonline" value="yes" <?php if ($user['visibleonline']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["editaccount"];print_help_link("useradmin_editaccount_help", "qm")?></td>
      <td class="facts_value"><input type="checkbox" name="editaccount" value="yes" <?php if ($user['editaccount']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
		<td class="facts_label"><?php print $pgv_lang["user_default_tab"]; print_help_link("useradmin_user_default_tab_help", "qm")?></td>
		<td class="facts_value"><select name="new_default_tab">
				<option value="0" <?php if (@$user['default_tab']==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1" <?php if (@$user['default_tab']==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"];?></option>
				<option value="2" <?php if (@$user['default_tab']==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3" <?php if (@$user['default_tab']==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"];?></option>
				<option value="4" <?php if (@$user['default_tab']==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"];?></option>
			</select>
		</td>
	</tr>
  </table>
  <input type="submit" value="<?php print $pgv_lang["update_user"]; ?>" />
</form>
<br />
</td></tr></table></div>
<?php
  print_footer();
  exit;
}
//-- end of $action=='edituser'

//-- print out a list of the current users
?>
<span class="subheaders"><?php print $pgv_lang["current_users"]; ?></span><br />
<table class="list_table <?php print $TEXT_DIRECTION; ?>">
<tr>
	<td class="list_label_wrap"><?php print $pgv_lang["delete"]; ?></td>
	<td class="list_label_wrap"><?php print $pgv_lang["edit"]; ?></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortuname\">"; ?><?php print $pgv_lang["username"]; ?></a></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortfname\">"; ?><?php print $pgv_lang["full_name"]; ?></a></td>
	<td class="list_label_wrap"><?php print $pgv_lang["inc_languages"]; ?></td>
	<td class="list_label_wrap"><?php print $pgv_lang["privileges"]; ?></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortreg\">"; ?><?php print $pgv_lang["date_registered"]; ?></a></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortllgn\">"; ?><?php print $pgv_lang["last_login"]; ?></a></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortver\">"; ?><?php print $pgv_lang["verified"]; ?></a></td>
	<td class="list_label_wrap"><?php print "<a href=\"useradmin.php?action=sortveradm\">"; ?><?php print $pgv_lang["verified_by_admin"]; ?></a></td>
</tr>
<?php
switch ($action){
	case "sortfname":
		$users = getUsers("fullname","asc");
		break;
	case "sortllgn":
		$users = getUsers("sessiontime","desc");
		break;
	case "sortuname":
		$users = getUsers("username","asc");
		break;
	case "sortreg":
		$users = getUsers("reg_timestamp","desc");
		break;
	case "sortver":
		$users = getUsers("verified","asc");
		break;
	case "sortveradm":
		$users = getUsers("verified_by_admin","asc");
		break;
	default: $users = getUsers("username","asc");
}

foreach($users as $username=>$user) {
	if (empty($user["language"])) $user["language"]=$LANGUAGE;
	if (!isset($language_settings[$user["language"]])) $user["language"]=$LANGUAGE;
	print "<tr>\n";
	if ($TEXT_DIRECTION=="ltr") print "\t<td class=\"list_value_wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."\" onclick=\"return confirm('".$pgv_lang["confirm_user_delete"]." $username?');\">".$pgv_lang["delete"]."</a></td>\n";
    else if (begRTLText($username)) print "\t<td class=\"list_value_wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."\" onclick=\"return confirm('?".$pgv_lang["confirm_user_delete"]." $username');\">".$pgv_lang["delete"]."</a></td>\n";
    else print "\t<td class=\"list_value_wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."\" onclick=\"return confirm('?$username ".$pgv_lang["confirm_user_delete"]." ');\">".$pgv_lang["delete"]."</a></td>\n";
	print "\t<td class=\"list_value_wrap\"><a href=\"useradmin.php?action=edituser&amp;username=".urlencode($username)."\">".$pgv_lang["edit"]."</a></td>\n";
	print "\t<td class=\"list_value_wrap\">".$username."</td>\n";
    if ($TEXT_DIRECTION=="ltr") print "\t<td class=\"list_value_wrap\">".$user["fullname"]."&lrm;</td>\n";
    else                        print "\t<td class=\"list_value_wrap\">".$user["fullname"]."&rlm;</td>\n";
	print "\t<td class=\"list_value_wrap\">".$pgv_lang["lang_name_".$user["language"]]."<br /><img src=\"".$language_settings[$user["language"]]["flagsfile"]."\" class=\"flag\" alt=\"".$pgv_lang["lang_name_".$user["language"]]."\" title=\"".$pgv_lang["lang_name_".$user["language"]]."\" /></td>\n";
	print "\t<td class=\"list_value_wrap\">";
	print "<ul>";
	if ($user["canadmin"]) print "<li class=\"warning\">".$pgv_lang["can_admin"]."</li>\n";
	uksort($GEDCOMS, "strnatcasecmp");
	reset($GEDCOMS);
	foreach($GEDCOMS as $gedid=>$gedcom) {
		if (isset($user["canedit"][$gedid])) $vval = $user["canedit"][$gedid];
		else $vval = "none";
		if ($vval == "") $vval = "none";
		if (isset($user["gedcomid"][$gedid])) $uged = $user["gedcomid"][$gedid];
		else $uged = "";
			if ($vval=="accept" || $vval=="admin") print "<li class=\"warning\">";
			else print "<li>";
		print $pgv_lang[$vval]." ";
		if ($uged != "") print "<a href=\"individual.php?pid=".$uged."&ged=".$gedid."\">".$gedid."</a></li>\n";
		else print $gedid."</li>\n";
	}
	print "</ul>";
	print "</td>\n";
	print "\t<td class=\"list_value_wrap\">";
	print get_changed_date(date("d", $user["reg_timestamp"])." ".date("M", $user["reg_timestamp"])." ".date("Y", $user["reg_timestamp"]))." - ".date($TIME_FORMAT, $user["reg_timestamp"]);
	print "</td>\n";
	print "\t<td class=\"list_value_wrap\">";
		if ($user["reg_timestamp"] > $user["sessiontime"]) {
		print $pgv_lang["never"];
	}
	else {
		print get_changed_date(date("d", $user["sessiontime"])." ".date("M", $user["sessiontime"])." ".date("Y", $user["sessiontime"]))." - ".date($TIME_FORMAT, $user["sessiontime"]);
	}
	print "</td>\n";
	print "\t<td class=\"list_value_wrap\">";
	if ($user["verified"]=="yes") print $pgv_lang["yes"];
	else print $pgv_lang["no"];
	print "</td>\n";
	print "\t<td class=\"list_value_wrap\">";
	if ($user["verified_by_admin"]=="yes") print $pgv_lang["yes"];
	else print $pgv_lang["no"];
	print "</td>\n";
	print "</tr>\n";
}

//-- print out the form to add a new user
?>
</table>
<script language="JavaScript" type="text/javascript">
<!--
	function checkform(frm) {
		if (frm.uusername.value=="") {
			alert("<?php print $pgv_lang["enter_username"]; ?>");
			frm.uusername.focus();
			return false;
		}
		if (frm.ufullname.value=="") {
			alert("<?php print $pgv_lang["enter_fullname"]; ?>");
			frm.ufullname.focus();
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
	    if (frm.pass1.value.length < 6) {
	      alert("<?php print $pgv_lang["passwordlength"]; ?>");
	      frm.pass1.value = "";
	      frm.pass2.value = "";
	      frm.pass1.focus();
	      return false;
	    }
		if ((frm.emailadress.value!="")&&(frm.emailadress.value.indexOf("@")==-1)) {
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.emailadress.focus();
			return false;
		}
		return true;
	}
//-->
</script>
<br /><br />
<form name="newform" method="post" action="<?php print $PHP_SELF;?>" onsubmit="return checkform(this);">
<span class="subheaders"><?php print $pgv_lang["add_user"]?></span><br />
<input type="hidden" name="action" value="createuser" />
<!--table-->
<table class="list_table <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="facts_label"><?php print $pgv_lang["username"];print_help_link("useradmin_username_help", "qm")?></td><td class="facts_value"><input type="text" name="uusername" tabindex="1" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["fullname"];print_help_link("useradmin_fullname_help", "qm")?></td><td class="facts_value"><input type="text" name="ufullname" tabindex="2" size="50" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["gedcomid"];print_help_link("useradmin_gedcomid_help", "qm")?></td><td class="facts_value">

	<table align="<?php print $TEXT_DIRECTION=="rtl"?"right":"left"; ?>">
	<?php
	foreach($GEDCOMS as $ged=>$gedarray) {
		$file = $ged;
		$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
		print "<tr><td>$file : </td><td><input type=\"text\" name=\"gedcomid_$ged\" value=\"";
		print "\" tabindex=\"3\" /><font size=\"1\"><a href=\"javascript:open_find(document.newform.gedcomid_$ged, '$file');\"> ".$pgv_lang["find_id"]."</a></font><br />\n";
		print "</td></tr>\n";
	}
	print "</table>";
	?>
	</td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["rootid"];print_help_link("useradmin_rootid_help", "qm")?></td><td class="facts_value">
	<table align="<?php print $TEXT_DIRECTION=="rtl"?"right":"left"; ?>">
	<?php
	foreach($GEDCOMS as $ged=>$gedarray) {
		$file = $ged;
		$ged = preg_replace(array("/\./","/-/"), array("_","_"), $ged);
		print "<tr><td>$file : </td><td><input type=\"text\" name=\"rootid_$ged\" value=\"";
		print "\" tabindex=\"4\" /><font size=\"1\"><a href=\"javascript:open_find(document.newform.rootid_$ged, '$file');\"> ".$pgv_lang["find_id"]."</a></font><br />\n";
		print "</td></tr>\n";
	}
	print "</table>";
	?>
	</td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["password"];print_help_link("useradmin_password_help", "qm")?></td><td class="facts_value"><input type="password" name="pass1" tabindex="5" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["confirm"];print_help_link("useradmin_conf_password_help", "qm")?></td><td class="facts_value"><input type="password" name="pass2" tabindex="6" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["can_admin"];print_help_link("useradmin_can_admin_help", "qm")?></td><td class="facts_value"><input type="checkbox" name="canadmin" value="yes" tabindex="7" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["can_edit"];print_help_link("useradmin_can_edit_help", "qm")?></td><td class="facts_value">
	<?php
	foreach($GEDCOMS as $ged=>$gedarray) {
		$file = $ged;
		$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
		print "<select name=\"canedit_$ged\" tabindex=\"8\">\n";
		print "<option value=\"none\"";
		print ">".$pgv_lang["none"]."</option>\n";
		print "<option value=\"access\"";
		print ">".$pgv_lang["access"]."</option>\n";
		print "<option value=\"edit\"";
		print ">".$pgv_lang["edit"]."</option>\n";
		print "<option value=\"accept\"";
		print ">".$pgv_lang["accept"]."</option>\n";
		print "<option value=\"admin\"";
		print ">".$pgv_lang["admin_gedcom"]."</option>\n";
		print "</select> $file<br />\n";
	}
	?>
	</td></tr>

	<?php /* added by Kurt - start */ ?>
	<tr><td class="facts_label"><?php print $pgv_lang["emailadress"];print_help_link("useradmin_email_help", "qm")?></td><td class="facts_value"><input type="text" name="emailadress" value="" size="50" tabindex="9" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["verified"];print_help_link("useradmin_verified_help", "qm")?></td><td class="facts_value"><input type="checkbox" name="verified" value="yes" tabindex="10" checked="checked" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["verified_by_admin"];print_help_link("useradmin_verbyadmin_help", "qm")?></td><td class="facts_value"><input type="checkbox" name="verified_by_admin" value="yes" tabindex="11" checked="checked" /></td></tr>
	<?php /* added by Kurt - end */ ?>
	<tr><td class="facts_label"><?php print $pgv_lang["change_lang"];print_help_link("useradmin_change_lang_help", "qm"); ?></td><td class="facts_value" valign="top"><?php
      if ($ENABLE_MULTI_LANGUAGE)
      {
      	print "<select name=\"user_language\" style=\"{ font-size: 9pt; }\">";
      	foreach ($pgv_language as $key => $value)
      	{
      	  print "\n\t\t\t<option value=\"$key\"";
      	  if ($key == $user["language"])
      	  {
      	    print " selected=\"selected\"";
      	  }
      	  print ">" . $pgv_lang[$key] . "</option>";
      	}
      	print "</select>\n\t\t";
      }
      else print "&nbsp;";
    ?></td></tr>
	<?php if ($ALLOW_USER_THEMES) { ?>
    <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["user_theme"];print_help_link("useradmin_user_theme_help", "qm");?></td><td class="facts_value" valign="top">
    	<select name="new_user_theme" tabindex="12">
    	<option value="" selected="selected"><?php print $pgv_lang["site_default"]; ?></option>
				<?php
					$themes = get_theme_names();
					foreach($themes as $indexval => $themedir) {
						print "<option value=\"".$themedir["dir"]."\"";
						print ">".$themedir["name"]."</option>\n";
					}
				?>
			</select>
	</td></tr>
	<?php } ?>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["user_contact_method"];print_help_link("useradmin_user_contact_help", "qm")?></td>
		<td class="facts_value"><select name="new_contact_method">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging"><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" selected="selected"><?php print $pgv_lang["messaging2"];?></option>
		<?php } else { ?>
				<option value="messaging3" selected="selected"><?php print $pgv_lang["messaging3"];?></option>
		<?php } ?>
				<option value="mailto"><?php print $pgv_lang["mailto"];?></option>
				<option value="none"><?php print $pgv_lang["no_messaging"];?></option>
			</select>
		</td>
	</tr>
	<tr>
      <td class="facts_label"><?php print $pgv_lang["visibleonline"];print_help_link("useradmin_visibleonline_help", "qm")?></td>
      <td class="facts_value"><input type="checkbox" name="visibleonline" value="yes" <?php print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
      <td class="facts_label"><?php print $pgv_lang["editaccount"];print_help_link("useradmin_editaccount_help", "qm")?></td>
      <td class="facts_value"><input type="checkbox" name="editaccount" value="yes" <?php print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
		<td class="facts_label"><?php print $pgv_lang["user_default_tab"]; print_help_link("useradmin_user_default_tab_help", "qm")?></td>
		<td class="facts_value"><select name="new_default_tab">
				<option value="0"><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1"><?php print $pgv_lang["notes"];?></option>
				<option value="2"><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3"><?php print $pgv_lang["media"];?></option>
				<option value="4"><?php print $pgv_lang["relatives"];?></option>
			</select>
		</td>
	</tr>
</table>

<?php /* added by Kurt - start */ ?>
<input type="hidden" name="pwrequested" value="" />
<input type="hidden" name="reg_timestamp" value="<?php print date("U");?>" />
<input type="hidden" name="reg_hashcode" value="" />
<?php /* added by Kurt - end */ ?>

<input type="submit" value="<?php print $pgv_lang["create_user"]; ?>" tabindex="13" />
</form>
</td></tr></table></div>
<?php
print_footer();
?>