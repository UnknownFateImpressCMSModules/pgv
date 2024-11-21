<?php
/*=================================================
	Project: phpGedView
	File: uploadmedia.php
	Author: John Finlay
	Comments:
		Allow admin users to upload media files using a
		web interface.

	phpGedView: Genealogy Viewer
    Copyright (C) 2002 to 2003  John Finlay and Others

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

===================================================*/
# $Id: uploadmedia.php,v 1.2 2006/01/09 00:46:23 skenow Exp $

require "config.php";

if (!userCanEdit(getUserName())) {
	header("Location: login.php?url=uploadmedia.php");
	exit;
}

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?ged=$GEDCOM&url=uploadmedia.php");
	exit;
}

print_header($pgv_lang["upload_media"]);
$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
?>
<center>
<?php
	print "<span class=\"subheaders\">".str2upper($pgv_lang["upload_media"])."</span><br /><br />\n";
	if ((isset($action)) && ($action=="upload")) {
		for($i=1; $i<6; $i++) {
			$error="";
			if (!empty($_FILES['mediafile'.$i]["name"])) {
				AddToLog("Media file ".$MEDIA_DIRECTORY.basename($_FILES['mediafile'.$i]['name'])." uploaded by >".getUserName()."<");
				$thumbgenned = false;
				if (!move_uploaded_file($_FILES['mediafile'.$i]['tmp_name'], $MEDIA_DIRECTORY.basename($_FILES['mediafile'.$i]['name']))) {
					$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['mediafile'.$i]['error']]."<br />";
				}
				else {
					//-- automatically generate thumbnail
					if (!empty($_POST['genthumb'.$i]) && ($_POST['genthumb'.$i]=="yes")) {
						$filename = $MEDIA_DIRECTORY.basename($_FILES['mediafile'.$i]['name']);
						$thumbnail = $MEDIA_DIRECTORY."thumbs/".basename($_FILES['mediafile'.$i]['name']);
						$thumbgenned = generate_thumbnail($filename, $thumbnail);
						if (!$thumbgenned) $error .= $pgv_lang["thumbgen_error"].$filename."<br />";
						else print $thumbnail." ".$pgv_lang["thumb_genned"]."<br />";
					}
				}
				AddToLog("Media thumbnail ".$MEDIA_DIRECTORY."thumbs/".basename($_FILES['thumbnail'.$i]['name'])." uploaded by >".getUserName()."<");
				if (!$thumbgenned) {
					if (!move_uploaded_file($_FILES['thumbnail'.$i]['tmp_name'], $MEDIA_DIRECTORY."thumbs/".basename($_FILES['thumbnail'.$i]['name']))) {
						$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['thumbnail'.$i]['error']]."<br />";
					}
				}
				if (!empty($error)) print "<span class=\"error\">".$error."</span><br />\n";
				else {
					print $pgv_lang["upload_successful"]."<br /><br />";
					$imgsize = getimagesize($MEDIA_DIRECTORY.$_FILES['mediafile'.$i]['name']);
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+50;
					print "<a href=\"#\" onclick=\"return openImage('".urlencode($MEDIA_DIRECTORY.$_FILES['mediafile'.$i]['name'])."',$imgwidth, $imgheight);\">".$_FILES['mediafile'.$i]['name']."</a>";
					print"<br /><br />";
				}
			}
		}
	}
	
	if (!is_writable($MEDIA_DIRECTORY) || !$MULTI_MEDIA) {
		print "<span class=\"error\"><b>";
		print_text("no_upload");
		print "</b></span><br /><br /><b>";
		print_media_nav("uploadmedia");
		print "</b>";
	} else {
		print "<table width=\"70%\" class=\"$TEXT_DIRECTION\"><tr><td>";
		print_text("upload_help");
		print "&lrm; <b>".$MEDIA_DIRECTORY."</b>&lrm;<br />";
		if (!$filesize = ini_get('upload_max_filesize')) {
			$filesize = "2M";
	}
		print_text("max_upload_size");
		print " $filesize<br /><br />";
		print "</td></tr></table>";
		
		print "<form enctype=\"multipart/form-data\" method=\"post\" action=\"uploadmedia.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"upload\" />";
		print "<table border=0 cellpadding=0 cellspacing=0>";
		for($i=1; $i<6; $i++) {
			print "<tr>";
				print "<td ";
				write_align_with_textdir_check("right");
				print ">";
					print_text("media_file");
					print "&nbsp;";
				print "</td>";
				print "<td>";
					print "<input name=\"mediafile".$i."\" type=\"file\" size=60 />";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td ";
				write_align_with_textdir_check("right");
				print ">";
					print_text("thumbnail");
					print "&nbsp;";
				print "</td>";
				print "<td>";
					print "<input name=\"thumbnail".$i."\" type=\"file\" size=60 />";
				print "</td>";
			print "</tr>";

			$ThumbSupport = "";
			if (function_exists("imagecreatefromjpeg") and function_exists("imagejpeg")) $ThumbSupport .= ", JPG";
			if (function_exists("imagecreatefromgif") and function_exists("imagegif")) $ThumbSupport .= ", GIF";
			if (function_exists("imagecreatefrompng") and function_exists("imagepng")) $ThumbSupport .= ", PNG";
			
			if ($ThumbSupport != "") {
				$ThumbSupport = substr($ThumbSupport, 2);	// Trim off first ", "
				print "<tr>";
					print "<td colspan=2 class=\"center\">";
						print "<input type=\"checkbox\" name=\"genthumb".$i."\" value=\"yes\" /> ";
						print_text("generate_thumbnail");
						print $ThumbSupport;
	print_help_link("generate_thumb_help", "qm");
					print "</td>";
				print "</tr>";
			}			
			print "<tr><td><br /><br /></td></tr>";
		}
		print "</table>";
		print "<br />";
		print "<input type=\"submit\" value=\"";
		print $pgv_lang["upload_media"];
		print "\" />";
		print "</form>";
		print "<br />";
		print_media_nav("uploadmedia");
		print "<br /><br />";
		print "</center>";
	}
	print_footer();
?>