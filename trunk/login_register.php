<?php
/**
 * Register as a new User or request new password if it is lost
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
 * @version $Id: login_register.php,v 1.3 2006/01/09 00:46:23 skenow Exp $
 */

require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

// Remove slashes
if (isset($user_fullname)) $user_fullname = stripslashes($user_fullname);

$message="";
if (!isset($action)) $action = "";
if (!isset($url)) $url = "index.php";

switch ($action)
{
  case "pwlost" :	print_header("PhpGedView - " . $pgv_lang["lost_pw_reset"]);
  			?>
  			<script language="JavaScript" type="text/javascript">
			<!--
  			  function checkform(frm)
  			  {
  			    if (frm.user_email.value == "")
  			    {
  			      alert("<?php print $pgv_lang["enter_email"]; ?>");
  			      frm.user_email.focus();
  			      return false;
  			    }
  			    return true;
  			  }
			//-->
  			</script>

  			<?php
  			print "<center>";
			print "<table width=\"80%\" class=\"$TEXT_DIRECTION\"><tr><td>";
  			print "<span class=\"subheaders\"><center>";
  			print_text("lost_pw_reset");
  			print "</center></span><br /><br />";
  			print_text("pls_note11"); 
  			print "</td></tr></table><br /><br />";
  			?>
  			  <form name="requestpwform" action="login_register.php" method="post" onsubmit="t = new Date(); document.requestpwform.time.value=t.toUTCString(); return checkform(this);">
  			  	<input type="hidden" name="time" value="" />
  			    <input type="hidden" name="action" value="requestpw" />
  			    <span class="warning"><?php print $message?></span>
  			    <table>
  			      <tr><td <?php write_align_with_textdir_check("right");?>><?php print $pgv_lang["username"]?></td><td><input type="text" name="user_name" value="" /></td></tr>
  			      <tr><td <?php write_align_with_textdir_check("right");?>><?php print $pgv_lang["emailadress"]?></td><td><input type="text" name="user_email" value="" /></td></tr>
  			    </table>
  			    <input type="submit" value="<?php print $pgv_lang["lost_pw_reset"]; ?>" />
  			  </form>
  			  <br /><br />
  			<?php
  			print "</center>";
  			break;
  case "requestpw" :	$QUERY_STRING = "";
  			print_header("PhpGedView - " . $pgv_lang["lost_pw_reset"]);
  			print "<center>";
  			$users = getUsers();
  			foreach($users as $indexval => $user) {
	  			if ($user["email"]==$user_email) $newuser = $user;
  			}
  			if (!isset($newuser) || ($newuser["username"]!=$user_name)) {
  			  print "<span class=\"warning\">";
  			  print_text("user_not_found");
  			  print "</span><br />";
  			}
  			else {
  			  $user_new_pw = md5 (uniqid (rand()));
  			  $newuser = getUser($user_name);
  			  $olduser = $newuser;
  			  deleteUser($user_name, "reqested password for");

  			  $newuser["password"] = crypt($user_new_pw, $user_new_pw);
  			  $newuser["pwrequested"] = "1";
  			  $newuser["reg_timestamp"] = date("U");
  			  addUser($newuser, "reqested password for");

				// switch language to user settings
				$oldlanguage = $LANGUAGE;
				$LANGUAGE = $newuser["language"];
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];


  			  $mail_body = "";
  			  $mail_body .= str_replace("#user_fullname#", $newuser["fullname"], $pgv_lang["mail04_line01"]) . "\r\n\r\n";
  			  $mail_body .= $pgv_lang["mail04_line02"] . "\r\n\r\n";
  			  $mail_body .= $pgv_lang["username"] . " " . $newuser["username"] . "\r\n";

  			  $mail_body .= $pgv_lang["password"] . " " . $user_new_pw . "\r\n\r\n";
  			  $mail_body .= $pgv_lang["mail04_line03"] . "\r\n";
  			  $mail_body .= $pgv_lang["mail04_line04"] . "\r\n\r\n";

	  			if (substr($SERVER_URL, -1) == "/"){
					$mail_body .= substr($SERVER_URL,0, (strlen($SERVER_URL)-1));
				}
				else {
					$mail_body .= $SERVER_URL;
				}

				$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
  			  $headers = "From: phpgedview-noreply@".$host;
  			  pgvMail($newuser["email"], str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["mail04_subject"]), $mail_body, $headers);

				// Reset language to original page language
				$LANGUAGE = $oldlanguage;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

			  print "<table width=\"80%\" class=\"$TEXT_DIRECTION\"><tr><td>";
			  print str_replace("#user[email]#", $newuser["email"], $pgv_lang["pwreqinfo"]);
  			  print "</td></tr></table><br /><br />";
  			  AddToLog("Password request was sent to user: ".$user_name);


  			}
  			print "</center>";
  			break;
  case "register" :
  			if (!$USE_REGISTRATION_MODULE) {
  				header("Location: index.php");
  				exit;
  			}
  			print_header("PhpGedView - " . $pgv_lang["requestaccount"]);
  			// Empty user array in case any details might be left
  			// and faulty users are requested and created
  			$user = array();
  			?>
  			<script language="JavaScript" type="text/javascript">
			<!--
  			  function checkform(frm)
  			  {
  			    if (frm.user_name.value == "")
  			    {
  			      alert("<?php print $pgv_lang["enter_username"]; ?>");
  			      frm.user_name.focus();
  			      return false;
  			    }
  			    if (frm.user_password01.value == "")
  			    {
  			      alert("<?php print $pgv_lang["enter_password"]; ?>");
  			      frm.user_password01.focus();
  			      return false;
  			    }
  			    if (frm.user_password02.value == "")
  			    {
  			      alert("<?php print $pgv_lang["confirm_password"]; ?>");
  			      frm.user_password02.focus();
  			      return false;
  			    }
  			    if (frm.user_password01.value != frm.user_password02.value)
  			    {
  			      alert("<?php print $pgv_lang["password_mismatch"]; ?>");
  			      frm.user_password01.value = "";
  			      frm.user_password02.value = "";
  			      frm.user_password01.focus();
  			      return false;
  			    }
  			    if (frm.user_password01.value.length < 6)
  			    {
  			      alert("<?php print $pgv_lang["passwordlength"]; ?>");
  			      frm.user_password01.value = "";
  			      frm.user_password02.value = "";
  			      frm.user_password01.focus();
  			      return false;
  			    }
  			    if (frm.user_fullname.value == "")
  			    {
  			      alert("<?php print $pgv_lang["enter_fullname"]; ?>");
  			      frm.user_fullname.focus();
  			      return false;
  			    }
  			    if ((frm.user_email.value == "")||(frm.user_email.value.indexOf('@')==-1))
  			    {
  			      alert("<?php print $pgv_lang["enter_email"]; ?>");
  			      frm.user_email.focus();
  			      return false;
  			    }
  			    if (frm.user_comments.value == "") {
	  			    alert("<?php print $pgv_lang["enter_comments"]; ?>");
	  			    frm.user_comments.focus();
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
			//-->
			</script>

  			<center>
  			  <form name="registerform" method="post" action="login_register.php" onsubmit="t = new Date(); document.registerform.time.value=t.toUTCString(); return checkform(this);">
  			    <input type="hidden" name="action" value="registernew" />
  			    <input type="hidden" name="time" value="" />
  			    <table width="80%" border="0">
  			      <tr><td colspan="2"><center><span class="subheaders"><?php print_text("requestaccount"); ?></span></center><br /><br />  			      <?php print_text("register_info_01"); ?></td></tr>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["username"];print_help_link("username_help", "qm"); ?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="text" name="user_name" value="" /> <span class="warning">*</span><br /><font class="warning" size="2"><?php print $pgv_lang["pls_note01"];?></font></td></tr>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["password"];print_help_link("edituser_password_help", "qm"); ?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="password" name="user_password01" value="" /> <span class="warning">*</span><br /><font class="warning" size="2">(<?php print $pgv_lang["min6chars"];?>)</font></td></tr>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["confirm"];print_help_link("edituser_conf_password_help", "qm"); ?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="password" name="user_password02" value="" /> <span class="warning">*</span><br /><font  size="2"><?php print $pgv_lang["pls_note02"];?></font></td></tr>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["fullname"];print_help_link("new_user_fullname_help", "qm"); ?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="text" name="user_fullname" value="" /> <span class="warning">*</span></td></tr>
  			      <?php	
  			      if ($ENABLE_MULTI_LANGUAGE) {
					print "<tr><td class=\"facts_label\" valign=\"top\" align=\"left\">";
					print $pgv_lang["change_lang"];print_help_link("edituser_change_lang_help", "qm");
					print "</td><td class=\"facts_value\" valign=\"top\"";
					write_align_with_textdir_check("left");
					print "><select name=\"user_language\" style=\"{ font-size: 9pt; }\">";
  			      	foreach ($pgv_language as $key => $value) {
						if ($language_settings[$key]["pgv_lang_use"]) {
  			      	    	print "\n\t\t\t<option value=\"$key\"";
  			      	    	if ($key == $LANGUAGE) print " selected=\"selected\"";
  			      	    	print ">" . $pgv_lang[$key] . "</option>";
			      	    }
  			      	}
  			      	print "</select>\n\t\t";
  			      	print "</td></tr>\n";
  			      }
  			      ?>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["emailadress"];print_help_link("edituser_email_help", "qm");?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="text" size="30" name="user_email" value="" /> <span class="warning">*</span><br /><font class="warning" size="2"><?php print $pgv_lang["pls_note03"];?></font></td></tr>
  			      <?php if (!$REQUIRE_AUTHENTICATION && $SHOW_LIVING_NAMES>=$PRIV_PUBLIC) { ?>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["gedcomid"];print_help_link("register_gedcomid_help", "qm");?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><input type="text" size="10" name="user_gedcomid" value="" /> <font size="1"> <a href="javascript:open_find(document.registerform.user_gedcomid);"><?php print $pgv_lang["find_id"]; ?></a></font></td></tr>
  			      <?php } ?>
  			      <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["comments"]; print_help_link("register_comments_help", "qm");?></td><td class="facts_value" valign="top" <?php write_align_with_textdir_check("left");?>><textarea cols="50" rows="5" name="user_comments" value="" /></textarea><span class="warning">*</span><br /><?php print $pgv_lang["pls_note12"];?></td></tr>
  			      <tr><td align="left" colspan="2" <?php write_align_with_textdir_check("left");?>><font class="warning" size="2"><?php print $pgv_lang["pls_note04"];?></font></td></tr>
  			      <tr><td align="center" colspan="2"><br /><input type="submit" value="<?php print $pgv_lang["requestaccount"]; ?>" /><br /><br /></td></tr>
  			      <tr><td colspan="2"><?php print_text("pls_note05");?></td></tr>
  			    </table>
  			  </form>
  			</center>
  			<?php
  			break;
  case "registernew" :	
  			if (!$USE_REGISTRATION_MODULE) {
  				header("Location: index.php");
  				exit;
  			}
  			if (preg_match("/SUNTZU/i", $user_name) || preg_match("/SUNTZU/i", $user_email)) {
	  			AddToLog("SUNTZU hacker");
	  			print "Go Away!";
  				exit;
  			}
  			$QUERY_STRING = "";
  			print_header("PhpGedView - " . $pgv_lang["registernew"]);

  			print "<center>";
			$alphabet = getAlphabet();
			$alphabet .= "_-.";
			$i = 1;
			$pass = TRUE;
			while (strlen($user_name) > $i) {
				if (stristr($alphabet, $user_name{$i}) != TRUE){
					$pass = FALSE;
					break;
				}
				$i++;
			}
			if ($pass == TRUE){
	  			$user_created_ok = false;

	  			AddToLog("User registration requested for: ".$user_name);

	  			if (getUser($user_name)!== false) {
	  			  print "<span class=\"warning\">";
	  			  print_text("duplicate_username");
	  			  print "</span><br /><br />";
	  			}
	  			else if ($user_password01 == $user_password02) {
  			       $user = array();
  			       $user["username"] = $user_name;
  			       $user["fullname"] = $user_fullname;
  			       $user["email"] = $user_email;
  			       if (!isset($user_language)) $user_language = $LANGUAGE;
  			       $user["language"] = $user_language;
  			       $user["verified"] = "";
  			       $user["verified_by_admin"] = "";
  			       $user["pwrequested"] = "";
  			       $user["reg_timestamp"] = date("U");
  			       srand((double)microtime()*1000000);
  			       $user["reg_hashcode"] = crypt(rand(), $user_password01);
  			       $user["gedcomid"] = array();
  			       $user["rootid"] = array();
  			       $user["canedit"] = array();
  			       $user["theme"] = "";
  			       $user["loggedin"] = "N";
  			       $user["sessiontime"] = 0;
  			       $user["contactmethod"] = "messaging2";
  			       $user["default_tab"] = $GEDCOM_DEFAULT_TAB;
  			       if (!empty($user_gedcomid)) {
  			          $user["gedcomid"][$GEDCOM] = $user_gedcomid;
  			          $user["rootid"][$GEDCOM] = $user_gedcomid;
			       }
  			       $user["password"] = crypt($user_password01, $user_password01);
  			       if ((isset($canadmin)) && ($canadmin == "yes")) $user["canadmin"] = true;
  			       else $user["canadmin"] = false;
  			       $user["visibleonline"] = true;
  			       $user["editaccount"] = true;
  			       $au = addUser($user, "added");
  			       if ($au) {
  			         $user_created_ok = true;
  			       } else {
  			         print "<span class=\"warning\">";
  			         print_text("user_create_error");
  			         print "<br /></span>";
  			       }
  			     } else {
  			       print "<span class=\"warning\">";
  			       print_text("password_mismatch");
  			       print "</span><br />";
  			     }
	  			if ($user_created_ok)
	  			{
	  			 // switch to the users language
				$oldlanguage = $LANGUAGE;
				$LANGUAGE = $user_language;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

				  $mail_body = "";
	  			  $mail_body .= str_replace("#user_fullname#", $user_fullname, $pgv_lang["mail01_line01"]) . "\r\n\r\n";
	  			  $mail_body .= str_replace("#user_email#", $user_email, str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["mail01_line02"])) . "\r\n";
	  			  $mail_body .= $pgv_lang["mail01_line03"] . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["mail01_line04"] . "\r\n\r\n";
	  			if (substr($SERVER_URL, -1) == "/"){
					$mail_body .= substr($SERVER_URL,0, (strlen($SERVER_URL)-1)). "/login_register.php?action=userverify&user_name=".urlencode($user_name)."&user_hashcode=".urlencode($user["reg_hashcode"]) . "\r\n";
				}
				else {
					$mail_body .= $SERVER_URL. "/login_register.php?action=userverify&user_name=".urlencode($user_name)."&user_hashcode=".urlencode($user["reg_hashcode"]) . "\r\n";
				}
	  			  $mail_body .= $pgv_lang["username"] . " " . $user_name . "\r\n";
	  			  //-- sending the password back to the user is a security risk
	  			  //--$mail_body .= $pgv_lang["password"] . " " . $user_password01 . "\r\n";
	  			  $mail_body .= $pgv_lang["hashcode"] . " " . $user["reg_hashcode"] . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["comments"].": " . $user_comments . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["mail01_line05"] . "\r\n";
	  			  $mail_body .= $pgv_lang["mail01_line06"] . "\r\n";
	  			  $host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
	  			  $headers = "From: phpgedview-noreply@".$host;
	  			  pgvMail($user_email, str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["mail01_subject"]), $mail_body, $headers);

				// switch language to webmaster settings
				$admuser = getuser($WEBMASTER_EMAIL);
				$LANGUAGE = $admuser["language"];
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

	  			  $mail_body = "";
	  			  $mail_body .= $pgv_lang["mail02_line01"] . "\r\n\r\n";
	  			  $mail_body .= str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["mail02_line02"]) . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["username"] . " " . $user_name . "\r\n";
	  			  $mail_body .= $pgv_lang["fullname"] . " " . $user_fullname . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["comments"].": " . $user_comments . "\r\n\r\n";
	  			  $mail_body .= $pgv_lang["mail02_line03"] . "\r\n";
  			      if ($REQUIRE_ADMIN_AUTH_REGISTRATION) { 
	  			  	$mail_body .= $pgv_lang["mail02_line04"] . "\r\n";
  			      } else {
	  			  	$mail_body .= $pgv_lang["mail02_line04a"] . "\r\n";
  			      }
	  			  $host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
	  			  $headers = "From: phpgedview-noreply@".$host;
		  			$message = array();
					$message["to"]=$WEBMASTER_EMAIL;
					$message["from"]=$user_email;
					$message["subject"] = str_replace("#SERVER_NAME#", $SERVER_URL, str_replace("#user_email#", $user_email, $pgv_lang["mail02_subject"]));
					$message["body"] = $mail_body;
					$message["created"] = $time;
					$message["method"] = $SUPPORT_METHOD;
					$message["no_from"] = true;
					addMessage($message);

				// switch language back to earlier settings
				$LANGUAGE = $oldlanguage;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) 	require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

	  			  print "<table width=\"80%\"><tr><td>";
	  			  print "<span class=\"subheaders\">";
	  			  print str_replace("#user_fullname#", $user_fullname, $pgv_lang["thankyou"]);
	  			  print "</span><br /><br />";
  			      if ($REQUIRE_ADMIN_AUTH_REGISTRATION) { 
	  			  	print str_replace("#user_email#", $user_email, $pgv_lang["pls_note06"]);
  			      } else {
	  			  	print str_replace("#user_email#", $user_email, $pgv_lang["pls_note06a"]);
  			      }
	  			  print "</td></tr></table>";
	  			}
				print "</center>";
			}
			else {
				print "<span class=\"error\">";
				print_text("invalid_username");
				print "</span><br />";
				print "<a href=\"javascript:history.back()\">".$pgv_lang["back"]."</a><br />";
			}
			break;
  case "userverify" :
  			if (!$USE_REGISTRATION_MODULE) {
  				header("Location: index.php");
  				exit;
  			}
  			if (!isset($user_name)) $user_name = "";
  			if (!isset($user_hashcode)) $user_hashcode = "";
  			print_header("PhpGedView - " . $pgv_lang["user_verify"]);
  			print "<div class=\"center\">";
  			?><form name="verifyform" method="post" onsubmit="t = new Date(); document.verifyform.time.value=t.toUTCString();">
  			  <input type="hidden" name="action" value="verify_hash" />
  			  <input type="hidden" name="time" value="" />
  			  <table width="80%" class="<?php print $TEXT_DIRECTION;?>">
  			    <tr><td align="center" colspan="2"><span class="subheaders"><?php print $pgv_lang["user_verify"];?><br /><br /></span></td></tr>
  			    <tr><td colspan="2"><?php print $pgv_lang["pls_note07"];?><br /><br /></td></tr>
  			    <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["username"]; ?></td><td class="facts_value" valign="top"><input type="text" name="user_name" value="<?php print $user_name; ?>" /><br /><font class="warning" size="2"><?php print $pgv_lang["pls_note01"];?></font></td></tr>
  			    <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["password"]; ?></td><td class="facts_value" valign="top"><input type="password" name="user_password" value="" /></td></tr>
  			    <tr><td class="facts_label" valign="top" align="left"><?php print $pgv_lang["hashcode"]; ?></td><td class="facts_value" valign="top"><input type="text" name="user_hashcode" value="<?php print $user_hashcode; ?>" /></td></tr>
  			    <tr><td align="left">&nbsp;</td><td><br /><input type="submit" value="<?php print $pgv_lang["send"]; ?>" /><br /><br /></td></tr>
  			  </table>
  			</form><?php
				print "</center>";
				break;
  case "verify_hash" :	
  			if (!$USE_REGISTRATION_MODULE) {
  				header("Location: index.php");
  				exit;
  			}
  			$QUERY_STRING = "";
  			AddToLog("User attempted to verify hashcode: ".$user_name);
  			print_header("PhpGedView - " . $pgv_lang["user_verify"]);# <-- better verification of authentication code
  			print "<center>";

  			print "<table width=\"80%\"><tr><td>";
  			print "<span class=\"subheaders\"><center>";
  			print_text("user_verify");
  			print "</center></span><br /><br />";
  			print str_replace("#user_name#", $user_name, $pgv_lang["pls_note08"]);
  			print "<br /><br /></td></tr>";
  			  $user = getUser($user_name);
  			  if ($user!==false) {
  			    $pw_ok = ($user["password"] == crypt($user_password, $user["password"]));
  			    $hc_ok = ($user["reg_hashcode"] == $user_hashcode);
  			    if (($pw_ok) and ($hc_ok)) {
  			      $newuser = $user;
  			      $olduser = $user;
  			      deleteUser($user_name, "verified");
  			      storeUsers();

  			      $newuser["verified"] = "yes";
  			      $newuser["pwrequested"] = "";
  			      $newuser["reg_timestamp"] = date("U");
  			      $newuser["hashcode"] = "";
  			      if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) {
	  			      $newuser["verified_by_admin"] = "yes";
  			      }
  			      addUser($newuser, "verified");

				// switch language to webmaster settings
				$admuser = getuser($WEBMASTER_EMAIL);
				$oldlanguage = $LANGUAGE;
				$LANGUAGE = $admuser["language"];
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

  			      $mail_body = "";
  			      $mail_body .= $pgv_lang["mail03_line01"] . "\r\n\r\n";
  			      $mail_body .= str_replace("#newuser[username]# ( #newuser[fullname]# )", $newuser["username"] . " (" . $newuser["fullname"] . ") ", $pgv_lang["mail03_line02"]) . "\r\n\r\n";
  			      if ($REQUIRE_ADMIN_AUTH_REGISTRATION) { 
	  			  	$mail_body .= $pgv_lang["mail03_line03"] . "\r\n";
  			      } else {
	  			  	$mail_body .= $pgv_lang["mail03_line03a"] . "\r\n";
  			      }
  			      $path = substr($PHP_SELF, 0, strrpos($PHP_SELF, "/"));
  			      $mail_body .= "http://".$_SERVER['SERVER_NAME'] . $path."/useradmin.php?action=edituser&username=" . urlencode($newuser["username"]) . "\r\n";
					$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
					$headers = "From: phpgedview-noreply@".$host;
		  			$message = array();
					$message["to"]=$WEBMASTER_EMAIL;
					$message["from"]="phpgedview-noreply@".$host;
					$message["subject"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["mail03_subject"]);
					$message["body"] = $mail_body;
					$message["created"] = $time;
					$message["method"] = $SUPPORT_METHOD;
					$message["no_from"] = true;
					addMessage($message);

				// Reset language to original page language
				$LANGUAGE = $oldlanguage;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];


  			      print "<tr><td>";
  			      print_text("pls_note09");
  			      print "<br /><br />";
  			      if ($REQUIRE_ADMIN_AUTH_REGISTRATION) { 
	  			      print_text("pls_note10");
  			      } else {
	  			      print_text("pls_note10a");
  			      }
	  			  print "<br /><br /></td></tr>";
  			    } else {
  			      print "<tr><td align=\"center\">";
  			      print "<span class=\"warning\">";
  			      print_text("data_incorrect");
  			      print "</span><br /><br /></td></tr>";
  			    }
  			  } else {
  			    print "<tr><td align=\"center\">";
  			    print "<span class=\"warning\">";
  			    print_text("user_not_found");
  			    print "</span><br /><br /></td></tr>";
  			  }
  			print "</table>";
			print "</center>";
			break;
  default :		header("Location: $url"); break;
}

print_footer();
?>