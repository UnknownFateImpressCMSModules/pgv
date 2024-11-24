Module Name: 		PGVXoops
Version Number:		1.0

Based on PhpGedView 3.3.5 and original Xoops patch by Patrick Kellum

License
======================
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.


Environment Conditions
======================
Xoops: 2.0.13.2
PHP: 4.3.x
 Safe_Mode: ON
 Register_Globals: OFF
Xoops Protector Module (2.54) installed and active


Files Added to PGV
======================
pgv_slogo.png
readme_xoops.txt (this file)
xoops_version.php
xoops_headfoot.php
xoops_adminmenu.php
xoops_install_funcs.php
includes/authentication_xoops.php
sql/mysql.sql


Files Changed in PGV
======================
config.php
descendancy.php
editconfig.php
help_text_vars.php (SunTzu patch)
index.php
login_register.php(SunTzu patch)
pedigree.php
phpgedview.js
useradmin.php(SunTzu patch)
includes/authentication_index.php(SunTzu patch)
includes/functions_print.php
themes/*.html
themes/*.css


Installation
======================

*Be sure your Xoops user (or the users you select for contacts) has a 'real name'
*Upload to /modules
*Install module through Xoops Admin
*Click on the PHPGedView icon in the Administration menu and complete the installation steps. (more complete instructions are found in readme.txt)

-----------------------------------------------------------------------------------------------------------------
*You will see a database connect error message and popup - close it and complete the general configuration - DB username, DB password, DB name, DB table prefix (be sure it is correct! XoopsPrefix_pgv_)
*Go on to Administer GEDCOMs
*Step 1 of 4: upload GEDCOM (browse to location). This is limited to a 2MB upload. If it is larger than 2MB, FTP it to your site, then select Add Gedcom and provide the path and name for the GEDCOM file.
*Step 2 of 4: Configure PhpGedView + GEDCOM file. In GEDCOM Basics, set the default person for pedigree and descendancy charts (often times your first dead ancestor). In Web Site & META Tag Settings, set your Main WebSite URL and text. Save Configuration
*Step 3 of 4: Validate Gedcom. Click Cleanup
*Step 4 of 4: Importing records into database. If you get a warning about maximum execution time being exceeded, refresh your browser (resubmit warning may appear -  click Retry), it will continue. Once the first set of data is imported (Import progress should show 100%), click on 'Continue importing places'. If the execution times out, refresh your browser. Once the import is complete, you will see a box on the page indicating it has completed.. If you would like to search and list females by their married names, click on the 'Import Married Names' button. You can skip this and follow the links to the Pedigree tree, the Welcome Page, or the GEDCOM management page.


*Edit GEDCOM privacy settings - General Settings (my recommendation): show living names to public; show sources only to admin users; enable clippings cart only to admin users; limit privacy by age of event (yes)
*Update User Account - set GEDCOM INDI record ID (usually yourself), and Pedigree Chart Root Person (same)

*Set config.php to Read Only
------------------------------------------------------------------------------------------------------------------

*Set module access according to your desired settings. Also set module admin to your desired settings (any group that has ANY module admin will be defaulted to admin of PGV)

*Begin customizing your PGV Welcome page and GEDCOM favorites

Known Issues:
DHTML menus show behind select boxes in IE (IE bug). This also exists in Xoops

*********************************************************************************
*********************************************************************************

Original readme from PKellum:
================================================================================
INTRODUCTION

This is a first attempt at integrating phpGedView into the Xoops system.

Patrick Kellum


================================================================================
INSTALLATION

Create a directory in your Xoops modules directory and upload phpGedView to
this directory.  Copy all the files in the pgvxoops directory to the phpGedView
directory.

Activate the phpGedView module in Xoops.

Click on the phpGedView link in your menu on the home page and follow the
install instructions.

Be sure of the following during install.

1. The database prefix should include the Xoops prefix.

2. The Authentication Module should be set to "authentication_xoops.php".


================================================================================
TODO

1. Integrate search system into Xoops.

2. Integrate user system completly into Xoops (can be done, just needs to be
   figured out).


================================================================================
