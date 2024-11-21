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
 * @version $Id: edituser.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
 */

require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

if (!isset($action)) $action="";
if (isset($fullname)) $fullname = stripslashes($fullname);

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)||$_SESSION["cookie_login"]) {
	header("Location: login.php?url=edituser.php");
	exit;
}
$user = getUser($uname);
if (!isset($user["default_tab"])) $user["default_tab"]=0;
//-- prevent users with editing account disabled from being able to edit their account
if (!$user["editaccount"]) {
	header("Location: index.php?command=user");
	exit;
}
print_header("PhpGedView ".$pgv_lang["user_admin"]);
print "\n\t<h2>".$pgv_lang["editowndata"]."</h2>";
print "<div class=\"center\">\n";

//-- section to update a user by first deleting them
//-- and then adding them again
if ($action=="edituser2") {
	if (($username!=$oldusername)&&(getUser($username)!==false)) {
		print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
	}
	else if ($pass1==$pass2) {
		$alphabet = getAlphabet();
		$alphabet .= "_-. ";
		$i = 1;
		$pass = TRUE;
		while (strlen($username) > $i) {
			if (stristr($alphabet, $username{$i}) != TRUE){
				$pass = FALSE;
				break;
			}
			$i++;
		}
		if ($pass) {
			$newuser = array();
			$olduser = getUser($oldusername);
			$newuser = $olduser;
			if (empty($pass1)) $newuser["password"]=$olduser["password"];
			else $newuser["password"]=crypt($pass1);
			deleteUser($oldusername, "changed");
			$newuser["username"]=$username;
			$newuser["fullname"]=$fullname;
			$newuser["rootid"][$GEDCOM] = $rootid;
			if (isset($user_language)) $newuser["language"]=$user_language;
			$newuser["email"] = $user_email;
			if (isset($user_theme)) $newuser["theme"] = $user_theme;
			if (isset($new_contact_method)) $newuser["contactmethod"] = $new_contact_method;
			if ((isset($new_visibleonline))&&($new_visibleonline=='yes')) $newuser["visibleonline"] = true;
			else $newuser["visibleonline"] = false;
			if (isset($new_default_tab)) $newuser["default_tab"] = $new_default_tab;
			addUser($newuser, "changed");
			$user = $newuser;
		}
		else {
			print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
		$action="edituser";
	}
}
//-- print the form to edit a user
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
		if ((frm.user_email.value=="")||(frm.user_email.value.indexOf("@")==-1)) {
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.user_email.focus();
			return false;
		}
		return true;
	}
	var pasteto;
	function open_find(textbox) {
		pasteto = textbox;
		findwin = window.open('findid.php', '', 'left=50,top=50,width=450,height=450,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
</script>
<form name="editform" method="post" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="edituser2" />
<input type="hidden" name="oldusername" value="<?php print $uname; ?>" />
<table class="list_table, <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="facts_label"><?php print $pgv_lang["username"];print_help_link("edituser_username_help", "qm")?></td><td class="facts_value"><input type="text" name="username" value="<?php print $user['username']?>" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["fullname"];print_help_link("edituser_fullname_help", "qm")?></td><td class="facts_value"><input type="text" name="fullname" value="<?php print $user['fullname']?>" /></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["gedcomid"];print_help_link("edituser_gedcomid_help", "qm")?></td><td class="facts_value">
		<?php
			if (!empty($user['gedcomid'][$GEDCOM])) {
				print "<ul>";
				print_list_person($user['gedcomid'][$GEDCOM], array(get_person_name($user['gedcomid'][$GEDCOM]), $GEDCOM));
				print "</ul>";
			}
		?>
	</td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["rootid"];print_help_link("edituser_rootid_help", "qm")?></td><td class="facts_value"><input type="text" name="rootid" value="<?php if (isset($user['rootid'][$GEDCOM])) print $user['rootid'][$GEDCOM]; ?>" /><a href="#" onclick="open_find(document.editform.rootid); return false;"> <?php print $pgv_lang["find_id"];?></a></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["password"];print_help_link("edituser_password_help", "qm")?></td><td class="facts_value"><input type="password" name="pass1" /><br /><?php print $pgv_lang["leave_blank"];?></td></tr>
	<tr><td class="facts_label"><?php print $pgv_lang["confirm"];print_help_link("edituser_conf_password_help", "qm")?></td><td class="facts_value"><input type="password" name="pass2" /></td></tr>
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
    <tr><td class="facts_label"><?php print $pgv_lang["emailadress"];print_help_link("edituser_email_help", "qm");?></td><td class="facts_value" valign="top"><input type="text" name="user_email" value="<?php print $user["email"]; ?>" size="50" /><br /><?php print $pgv_lang["pls_note03"];?></td></tr>
    <?php if ($ALLOW_USER_THEMES) { ?>
    <tr><td class="facts_label"><?php print $pgv_lang["user_theme"];print_help_link("edituser_user_theme_help", "qm");?></td><td class="facts_value" valign="top">
    	<select name="user_theme">
    	<option value=""><?php print $pgv_lang["site_default"]; ?></option>
				<?php
					$themes = get_theme_names();
					foreach($themes as $indexval => $themedir) {
						print "<option value=\"".$themedir["dir"]."\"";
						if ($themedir["dir"] == $user["theme"]) print " selected=\"selected\"";
						print ">".$themedir["name"]."</option>\n";
					}
				?>
			</select>
	</td></tr>
	<?php } ?>
	<tr>
		<td class="facts_label"><?php print $pgv_lang["user_contact_method"];print_help_link("edituser_user_contact_help", "qm")?></td>
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
      <td class="facts_value"><input type="checkbox" name="new_visibleonline" value="yes" <?php if ($user['visibleonline']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
		<td class="facts_label"><?php print $pgv_lang["user_default_tab"]; print_help_link("edituser_user_default_tab_help", "qm")?></td>
		<td class="facts_value"><select name="new_default_tab">
				<option value="0" <?php if ($user['default_tab']==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1" <?php if ($user['default_tab']==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"];?></option>
				<option value="2" <?php if ($user['default_tab']==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3" <?php if ($user['default_tab']==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"];?></option>
				<option value="4" <?php if ($user['default_tab']==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"];?></option>
			</select>
		</td>
	</tr>
</table>
<input type="submit" value="<?php print $pgv_lang["update_myaccount"]; ?>" />
</form><br />
</div>
<?php
print_footer();
?>