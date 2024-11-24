<?php
/*=================================================
	Project: phpGedView
	File: functions_print.php
	Author:
		John Finlay
		Roland Dalmulder
	Comments:
		Tells the user that a browser upgrade is required.

	Change Log:
		11/29/03 - File Created

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
# $Id: nosupport.php,v 1.2 2006/01/09 00:46:23 skenow Exp $
require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];
print_header($pgv_lang["index_header"]);
print "<span class=\"error\">".$pgv_lang["no_support"]."<span>";
?>