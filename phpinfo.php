<?php
/*=================================================
   Project: phpGedView
   File: phpinfo.php
   Author: Roland Dalmulder
   Comments:
      Displays information on the PHP installation

   phpGedView: Genealogy Viewer
    Copyright (C) 2002 to 2004  John Finlay and Others

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

	$Id: phpinfo.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
===================================================*/

require "config.php";
if (!userGedcomAdmin(getUserName())) {
	 header("Location: login.php?url=phpinfo.php");
exit;
}

require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];

print_header($pgv_lang["phpinfo"]);
 ?>
<div class="center">
		<?php
		
		ob_start();
		  
		   phpinfo();
		   $php_info = ob_get_contents();
		      
		ob_end_clean();
		
		$php_info    = str_replace(" width=\"600\"", " width=\"\"", $php_info);
		$php_info    = str_replace("</body></html>", "", $php_info);
		$php_info    = str_replace("<table", "<table class=\"facts_table, ltr\"", $php_info);
		$php_info    = str_replace("td class=\"e\"", "td class=\"facts_value\"", $php_info);
		$php_info    = str_replace("td class=\"v\"", "td class=\"facts_value\"", $php_info);
		$php_info    = str_replace("tr class=\"v\"", "tr", $php_info);
		$php_info    = str_replace("tr class=\"h\"", "tr", $php_info);
		
		$php_info    = str_replace(";", "; ", $php_info);
		$php_info    = str_replace(",", ", ", $php_info);
		
		// Put logo in table header
		
		$logo_offset = strpos($php_info, "<td>");
		$php_info = substr_replace($php_info, "<td colspan=\"3\" class=\"facts_label03\">", $logo_offset, 4);
		$logo_width_offset = strpos($php_info, "width=\"\"");
		$php_info = substr_replace($php_info, "width=\"800\"", $logo_width_offset, 8);
		$php_info    = str_replace(" width=\"\"", "", $php_info);
		
		
		$offset          = strpos($php_info, "<table");
		$php_info	= substr($php_info, $offset);
		
		print $php_info;
		
		?>		
</div>
<?php
print_footer();
?>