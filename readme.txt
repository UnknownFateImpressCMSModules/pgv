=======================================================
	PhpGedView

	Version 3.3.8
	Copyright 2004 John Finlay and others

	This and other information can be found online at
	http://www.PhpGedView.net

	# $Id: readme.txt,v 1.122 2005/12/05 17:00:22 yalnifj Exp $
=======================================================

CONTENTS
	 1. LICENSE
	 2. INTRODUCTION
	 3. SYSTEM REQUIREMENTS
	 4. QUICK INSTALL
	 5. INSTALLATION
	 6. UPGRADING
	 7. UPDATING GEDCOMS
	 8. THEMES
	 9. MULTIMEDIA OBJECTS
	10. RSS FEED
	11. DATABASE TABLE LAYOUT
	12. MANUAL CONFIGURATION
	13. SECURITY
	14. LANGUAGES
	15. NON-STANDARD GEDCOM CODES
	16. LANGUAGE EXTENSION FILES
	17. MIGRATING FROM SQL TO INDEX MODE AND VICE VERSA
	18. POSTNUKE AND PHPNUKE INTEGRATION
        19. BACKUP

-------------------------------------------------------
LICENSE

PhpGedView: Genealogy Viewer
Copyright (C) 2002 to 2004  John Finlay and Others

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

See the file GPL.txt included with this software for more
detailed licensing information.

-------------------------------------------------------
INTRODUCTION

PhpGedView is a revolutionary genealogy program which allows you to view and 
edit your genealogy on your website. PhpGedView has full editing capabilities, 
full privacy functions, and supports multimedia like photos and document 
images. PhpGedView also simplifies the process of collaborating with others 
working on your family lines. Your latest information is always on your website
and available for others to see. For more information and to see working
demos, visit http://www.PhpGedView.net/

PhpGedView is Open Source software that has been produced by people from many
countries freely donating their time and talents to the project.  All service,
support, and future development is dependent upon the time developers are
willing to donate to the project often at the expense of work, recreation, and
family.  Beyond the few donations received from users, developers receive no
compensation for the time they spend working on the project.  There is also no
outside source of revenue to support the project.

-------------------------------------------------------
SYSTEM REQUIREMENTS

PhpGedView requires a webserver with at least PHP v4.3 and around 20MB of 
webspace.  The default installations of PHP on most servers should provide 
you with all of the PHP functionality you should need.

To use the reporting engine, PHP needs to be compiled with XML support.  This
is compiled into PHP by default unless it is specifically disabled.  See
http://us3.php.net/manual/en/ref.xml.php

Some features of PhpGedView require the GD library and that PHP be compiled
with GD support.  Most precompiled versions of PHP include GD support.  If you
are compiling PHP yourself you will need to configure it with the --with-gd
option.  See http://us3.php.net/manual/en/ref.image.php

The advanced calendar features of PhpGedView for converting Gregorian dates to
the Hebrew or Jewish calendars require that PHP be compiled with the
--enable-calendar configuration option.  See the following website for more
information: http://us2.php.net/manual/en/ref.calendar.php

For GEDCOMs larger than 2MB, you will likely need to research different
hosting options and find one that will accept a request to modify the 
default memory and time limits built into PHP.  See the FAQ at
http://www.phpgedview.net/faq.php for more information about large GEDCOMs.

-------------------------------------------------------
QUICK INSTALLATION

Follow the instructions in this section to install PhpGedView if you are
already familiar with the program or are familiar with installing other PHP web
applications.

1. Upload the files to your webserver
2. Set Write permissions on config.php and the "index" directory.  For optimal
   security, you should move the "index" directory to a location where it is not
   accessible from the Internet.
3. Point your browser to the directory where you uploaded your PhpGedView files
   (for example, http://www.yourserver.com/PhpGedView/)
4. Enter your configuration settings.  If you moved the index directory be sure
   to specify the correct location to it on this page.  Save the configuration
   parameters.
5. Enter the default administrator user
6. Login as this user and upload your GEDCOM file
7. Save the GEDCOM configuration settings
8. Import the GEDCOM

Optional Steps
9.  If you want to use the language editing features you will need to set Write
    permissions for all of the files in the ./languages folder
10. If you want to upload media files using the Upload Media section of the
    Admin menu then you need to set Write permissions for your ./media and
    ./media/thumbs directories
11. If you want to edit your GEDCOM file online, the GEDCOM file must have
    Write permissions set for the PHP user.
12. If you want to use the Backup feature of the Upgrade utility in PhpGedView
    you will need to either set Write permission on the PhpGedView folder
    itself or create a folder named "backup" with Write permissions. Write
    permissions for the PhpGedView folder can be removed as soon as the backup
    folder is in place and has the appropriate permissions.
13. For security you should set the permissions back to Read-only when you are
    done editing or uploading files.

-------------------------------------------------------
INSTALLATION

Follow these instructions if you are not familiar with PhpGedView or installing
PHP applications.

*A. Upload Files:
To install PhpGedView unzip the compressed package and upload the files to a
directory on your web server.  If you have limited space on your server, you
can save space in the following ways:
1. Do not upload the "docs" folder
2. Delete the themes from the themes folder that you do not plan to use
3. Delete some of the language files that you do not want.  English files are
   named configure_help.en.php, countries.en.php, facts.en.php, help_text.en.php
   and lang.en.ph.  French files, for example, are named with ".fr." in place of
   ".en.".  Hebrew files use ".he." in place of ".en.", and so on.

For optimal security, you may want to move the "index" directory to a different
location outside of your Internet accessible space.  You will specify the 
location of this directory during the online configuration.  See the SECURITY
section for more information.

*B. Required File Permissions:
PhpGedView requires that Read permissions be set for all files in the
PhpGedView directory tree.  Some hosts also require Execute permissions
(chmod 755).  PhpGedView requires full Write permissions on the index directory
(chmod 777 under most hosting configurations).  PhpGedView also requires that
Write permissions (chmod 777) be set temporarily for the config.php file.

To help with the setting of permissions a file called setpermissions.php has
been included with the project. This file will attempt to set 777 permissions
to the config.php, ./index, and all of the files inside the ./index directory.
Because host settings vary on the ability of PHP programs to set file
permissions, you have to run this file manually.

If at any time you have trouble during configuration, check your permissions
again.

There are some advanced features that require more Write permissions to be set.
If you want to use the language editing features you will need to set Write
permissions for all of the files in the ./languages folder (chmod 777). If you
want to upload media files using the Upload Media section of the Admin menu
then you need to set Write permissions (chmod 777) for your ./media and
./media/thumbs directories.  If you want to edit your GEDCOM file online, the
GEDCOM file must have Write permissions set to the PHP user (chmod 777).

*C. Configuration:
Next point your web browser to the PhpGedView folder
(http://www.yourserver.com/PhpGedView/) to automatically begin the online
configuration process.  Information about each of the configuration options can
be found online by clicking on the question mark (?) near each label.

The first configuration option allows you to choose whether you want to use
Index files or a Database for your datastore when you import your GEDCOMs.
Choosing Index files will import your GEDCOM records into a serialized file
that will be created and saved in the "index" directory.  Choosing Database
will import your GEDCOMs into a PEAR:DB supported database like MySQL or
PostgreSQL.  Currently, MySQL is the only fully tested DB.

Using a DB requires that an existing user, password, and database already
exist.  You can find more information about whether you should choose index
files or MySQL in the FAQs (http://www.PhpGedView.net/faq.php)

If you choose Index files make sure that the browser correctly selects the
authentication_index.php authentication module.  With a Database you can use
authentication_mysql.php module or the index module.  This should change
automatically when you change from Index to Database mode.

You may reconfigure PhpGedView at any time by going to PhpGedView/admin.php and
logging in as an administrator user and clicking on the "Configuration" link.

*D. Create Admin User
After you press the Save button, you will be asked to create an administrator
user and login as this user.  Then click on the link labeled "Click here to
continue" where you will be taken to the "Manage GEDCOMs" area.  In the "Manage
GEDCOMs" area you can add GEDCOMs to your site, edit the GEDCOM configuration,
edit Privacy settings, and import the GEDCOM into the datastore.

*E. Add GEDCOM file
To add GEDCOM files to the system, you can upload your GEDCOM file using the
"Upload GEDCOM" option from the Admin menu.  All files uploaded using the
"Upload GEDCOM" page are saved in your index directory.  You can also upload
your GEDCOM manually using ftp or any other file upload method.  Most hosts
limit the size of files that can be uploaded from a web form for security 
reasons, so you may be forced to use a manual method.

*F. Set GEDCOM Configuration Settings
After uploading your GEDCOM, you will be asked to set the configuration
parameters for it.  There are too many parameters to list all of their options
in this document.  Please use the online Help documentation to guide you 
through the configuration process.

*G. Validate GEDCOM
After you save the GEDCOM configuration PhpGedView will validate your GEDCOM
and automatically fix any errors that it can.  If any errors found in the
GEDCOM require user input, you will be prompted to choose how to proceed.
Again use the online Help ? for more information.

*H. Import GEDCOM
You are almost done.  This is the final step before you can begin viewing your
data.  After validating the GEDCOM and fixing any errors, you will need to
import the GEDCOM into the datastore.  During the Import you will see a lot of
processing statistics printed on the screen.  If the Import completed
successfully you will see a blue "Import Complete" message.  Everything is now
set up and you can begin using PhpGedView with your GEDCOM.

*I. Deleting GEDCOMs
You may delete GEDCOMs from your site from the "Manage GEDCOMs" area.
Deleting a GEDCOM from the site will remove it from the database but will not
delete the original GEDCOM file that you imported.

*J. Reset config.php Permissions
For security you should set the permissions of config.php back to Read-only
(chmod 755) when you have finished configuring for the first time. Write
permissions will only need to be set for config.php when you use the 
Admin->Configuration link.  Everything else will be stored in the index 
directory.

*K. Custom Themes
You can customize the look and feel of PhpGedView by modifying one of the
provided themes.  See the THEMES section of this readme file for more
information.

*L. HTTP Compression
Pages generated by PhpGedView can be large and use up a lot of bandwidth.
Compression of the data between the server and browser using gzip compression
can compress the bandwidth by up to 90% (usually 80% - 90% for PhpGedView that
were tested) If your web server is Apache, configuring this is very easy. Add
the following 2 lines to your php.ini file:

php_flag zlib.output_compression On
php_value zlib.output_compression_level 5

If you have no access to the php.ini file, create a blank file named .htaccess
(including the dot) and add the lines to that file (or add them to an existing
.htaccess file and upload the file to your PhpGedView directory.
Note: If your host is using mod_gzip or an other compression method, using this
technique can cause problems. Compression will have no effect on browsers that
do not support it. You can test the compression at
http://leknor.com/code/gziped.php


If you need help or support visit:
http://www.PhpGedView.net/support.php

-------------------------------------------------------
UPGRADING

With version 3.3, we have changed from the default of having all languages 
enabled to having all of the languages disabled.  To enable other languages
after upgrading please login as an administrator and go to the "Edit and
Configure Languages" page to enable the languages that you want on your site.

To upgrade from v3.3.x to the latest 3.3.x, simply upload the new files over the old files
except for config.php and the index directory.

Use the following steps to upgrade to v3.3 from v3.x.  These steps assume that
you are familiar with PhpGedView and have successfully installed it before.

Version 3.3 includes support for SQLite and Postgresql, so if you were 
previously using Index files, you now have more options.  SQLite is chosen as 
the default if you are using PHP 5.

1. Move your old PhpGedView files to a new location by renaming the folder they
   are in.  For example, if you installed the old version in 
   public_html/phpGedView, you should rename the folder to public_html/pgvold.
2. Upload the new 3.3 files to a new folder on your server.  Because the file
   structure has changed, you should not copy the old files over the new files
   as you may have been instructed to do in the past.
3. Copy your old ./index directory and any customized themes to the new 3.3 
   directory.  If you have configured your index directory to be outside your
   PGV directory as recommended in the SECURITY section, you don't need to do 
   this step.
4. Set Write permissions for config.php and the ./index directory.
5. Access your new site in a browser and reconfigure it by setting the index 
   directory to the location of your old index directory and entering the
   same database information used in the old site.
5. Re-import your GEDCOM files by going to Admin->Mangage GEDCOMs->Import
6. If you are using a customized theme you will need to update your theme
   with new stylesheets and variables.  An excellent tool that can help you to
   merge themes is the WinMerge project http://winmerge.sourceforge.net/
   
-------------------------------------------------------
UPDATING GEDCOMS

When you change your genealogy data outside of PhpGedView, it is not necessary 
to delete your GEDCOMs from PhpGedView and start over.  Follow these steps to 
update a GEDCOM that has already been imported:

1. The first step is to replace your old GEDCOM on the site with your new 
   GEDCOM.  You can do this using FTP, or by going to the "Upload GEDCOM" page
   and uploading a new GEDCOM with the same filename as the old one.  Please 
   remember that file names are case sensitive.
2. Re-import the GEDCOM file by going to Admin->Manage GEDCOMs->Import GEDCOM.
   The GEDCOM will be validated again before importing.
3. The Import script will detect that the GEDCOM has already been imported and
   will ask if you want to replace the old data.  Click the "Yes" button.
4. You will again see the Import statistics and the Import Complete message at
   the bottom of the page when the Import is complete.

-------------------------------------------------------
THEMES

PhpGedView uses a theme based architecture allowing you to have greater
flexibility over the appearance of the site. The "themes" directory contains the
standard themes that come packaged with PhpGedView.  You may customize any of
these themes to your liking or create your own theme by copying the "standard"
theme to a new folder and modifying it.  When you configure PhpGedView, you
should tell it to look in your new theme directory.

A theme directory must contain at least the following 6 files:
  footer.html	    # PHP/HTML that appears at the bottom of every page on your site.
  header.html	    # PHP/HTML that appears at the top of every page on your site.
  print_footer.html # PHP/HTML that appears at the bottom of every print preview page.
  print_header.html # PHP/HTML that appears at the top of every print preview page.
  style.css	    	# A CSS stylesheet containing all the styles for you site.
  sublinks.html	    # PHP/HTML to print the links to other places on the individual page.
  theme.php	    	# The PHP design variables that you may customize.
  toplinks.html	    # PHP/HTML that appears just below the header.html with the menu.
     
For a guide to building your own custom PhpGedView theme, go to:
http://www.PhpGedView.net/styleguide.php

If you really like a theme that you have done and would like it included with
the project, you should send your theme files to the developers at
yalnifj@users.sourceforge.net.

-------------------------------------------------------
MULTIMEDIA OBJECTS

The GEDCOM 5.5 standard supports multimedia files of all types.  Currently
PhpGedView supports multimedia objects only as external files.  Multimedia
embedded in the GEDCOM file itself will be ignored.  To use the multimedia
support in PhpGedView you must copy the multimedia files external to your
GEDCOM to the "media" directory in the folder where you installed PhpGedView.

In choosing which picture to show on charts, PhpGedView will choose the first
one with the _PRIM Y marker.  If there are no _PRIM tags in your media object
records then the first object found will be used.  You can disable all photos
on charts for a particular person by setting _PRIM N on all media objects.
Most genealogy programs will do this for you automatically.

You can find all of the images referenced in your file by opening your GEDCOM
in a text editor and looking for the OBJE or FILE tags.

PhpGedView includes a "media/thumbs" directory where you can place thumbnails
of your media files for display in lists and on other pages.  PhpGedView allows
you to create your own thumbnails so that you can maintain artistic control
over your media and to avoid the installation of other server side software.
Make a copy your images and reduce them to an appropriate thumbnail size
somewhere around 100px width and upload them to the "media/thumbs" directory.
Keep the filename the same as the original.  Thumbnails can be created for non
image media files as well.  To do this create a thumbnail image in either gif,
jpeg, png or bmp formats and name them the same name as the media file
including the file extension (even if the media is a non image such as a PDF or
an AVI file, name the thumbnail IMAGE with the PDF or AVI file extension).

There is an ImageModule that fully integrates with PGV and that will
automatically create thumbnails for you if you use it to upload your files.  It
is not included with the main PGV files because it requires external libraries
that not all hosts will have installed.  You should be able to get it to
work by following the instructions included with it.  You can download the
ImageModule from:
http://sourceforge.net/project/showfiles.php?group_id=55456&package_id=88140

You can configure PhpGedView to recognize subdirectories in your media folder.
The subdirectories must be the same names as the subdirectories in your media
file paths pointed to in your GEDCOM file.  For example if you have the
following media references in your GEDCOM file:
C:\Pictures\Genealogy\photo.jpg
C:\Pictures\Scans\scan1.jpg
scan2.jpg

With the media depth set to 1 you need to set up your directory structure like
this:
media/Genealogy/photo.jpg
media/Scans/scan1.jpg
media/scan2.jpg
media/thumbs/Genealogy/photo.jpg
media/thumbs/Scans/scan1.jpg
media/thumbs/scan2.jpg

With the media depth set to 2 you need to set up your directory structure like
this:
media/Pictures/Genealogy/photo.jpg
media/Pictures/Scans/scan1.jpg
media/scan2.jpg
media/thumbs/Pictures/Genealogy/photo.jpg
media/thumbs/Pictures/Scans/scan1.jpg
media/thumbs/scan2.jpg

-------------------------------------------------------
RSS FEED

PGV now includes an RSS feed. RSS is an XML format that allows other sites
to get news and other data from your site. The language used is the default
language of the site. The language of the feed can be set to any language
supported by PGV by changing the URL that your RSS aggregator uses from the
default /phpGedView/rss.php to /phpGedView/rss.php?lang=english (or any
language supported by PGV such as rss.php?lang=french). Currently only the
default site GEDCOM is supported for the feed info. Other options available in
the RSS feed are the ability to specify the feed type via the rssStyle parameter.
The PGV default is "RSS1.0". Passing any supported type including "PIE0.1", 
"mbox","RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3", "HTML", "JS" will change
the feed type. For example calling  /phpGedView/rss.php?rssStyle=HTML will create
HTML output suitable for inclusion in an other page via an iFrame. The JS option
will output JavaScript that can be included in an other page without using an iFrame.
You can also specify a module that you want to output (only 1) so that only that 
module will be output. This is done via the module parameter. For example, 
/phpGedView/rss.php?module=GEDCOMStats will only output the GEDCOM Stats block.
These parameters can be chained so that
/phpGedView/rss.php?lang=hebrew&module=GEDCOMStats&rssStyle=HTML will output the GEDCOM
Stats module in hebrew in HTML.


-------------------------------------------------------
DATABASE TABLE LAYOUT

PhpGedView uses a very simple database table layout because it operates 
primarily on the GEDCOM data and only needs the database for search and 
retrieval.  There are only six tables in the database:
  pgv_individuals			# All the individuals in the GEDCOM
  pgv_families				# All the families in the GEDCOM
  pgv_sources				# All the sources in the GEDCOM
  pgv_other                 # All other level 0 GEDCOM records (ie.
                            # repositories, media objects, notes, etc.)
  pgv_users                 # Table for user data (only exists if using default
                            # mysql authentication module)
  pgv_messages				# Messages to and from users
  pgv_favorites				# stores a users favorites
  pgv_places				# place hierarchy
  
The tables are all very similar to each other.  They each have a field for the
GEDCOM id, a field to tell which GEDCOM file the record was imported from, a
few fields for things like quick retrieval of name information, and a field for
the raw GEDCOM record data.

Following is a more detailed description of each table:
  *_individuals:
      i_id VARCHAR(30)			# The GEDCOM individual id
      i_file VARCHAR(255)		# The GEDCOM filename that the record was imported from
      i_rin VARCHAR(30)			# The individual's RIN number
      i_name VARCHAR(255)		# The person's primary name taken from the first
      							#    1 NAME line stored in GEDCOM name format
      i_isdead int(1)			# The status if individual is dead or alive 
      							#    (-1 = not calculated yet 0 = alive 1 = dead)
      i_GEDCOM TEXT				# The raw GEDCOM record for this individual
      i_letter VARCHAR(5)		# The first letter of the individual's surname
      i_surname VARCHAR(100)	# The person's surname
      
  *_families:
      f_id VARCHAR(30)			# The GEDCOM family id
      f_file VARCHAR(255)		# The GEDCOM filename that the record was imported from
      f_husb VARCHAR(30)		# The ID of the husband
      f_wife VARCHAR(30)		# The ID of the wife
      f_chil VARCHAR(255)		# The a semicolon (;) delimeted list of children IDs
      f_GEDCOM TEXT				# The raw GEDCOM record for this family
     
  *_sources:
      s_id VARCHAR(30)			# The GEDCOM source ID
      s_file VARCHAR(255)		# The GEDCOM filename that the record was imported from
      s_name VARCHAR(255)		# The abbreviated title of the source
      s_GEDCOM TEXT				# The raw GEDCOM record for this source
      
  *_other:
      o_id VARCHAR(30)			# The GEDCOM record ID
      o_file VARCHAR(255)		# The GEDCOM filename that the record was imported from
      o_type VARCHAR(20)		# The type of GEDCOM record that this is 
      							#    (REPO, ADDR, NOTE, OBJE, etc)
      o_GEDCOM TEXT				# The raw GEDCOM record for this item
      
  *_names:
      n_gid VARCHAR(30)			# The individual ID that this name corresponds to
      n_file VARCHAR(255)		# The GEDCOM file the name came from
      n_name VARCHAR(255)		# The name in GEDCOM format with / / around the surname
      n_letter VARCHAR(5)		# The first letter of the surname
      n_surname VARCHAR(100)	# The surname for this name record
      n_type VARCHAR(10)		# The type of name, 
      							#    P = primary, A = additional, C=calculated
      
  *_blocks:
      b_id INT(11)				# The record ID
      b_username VARCHAR(100)	# The username whom block belongs to
      b_location VARCHAR(30)	# The location of the block. Main column or right column
      b_order INT(11)			# The position of the block in either 
      							#    the main or right column
      b_name VARCHAR(255)		# The name of the block
      b_config TEXT				# Any configuration settings for this block
  
  *_favorites:
  	  fv_id INT(11)				# The record ID
  	  fv_username VARCHAR(30)	# The username whom the favorite belongs to 
  	  fv_gid VARCHAR(10)		# The ID of the individual favorite
  	  fv_type VARCHAR(10)		# The type of favorite (currently only INDI)
  	  fv_file VARCHAR(100)		# The file that the individual favorite belongs to
  	  
  *_messages:
  	  m_id INT(11)				# The record ID
  	  m_from VARCHAR(255)		# The name or email address of the sender
  	  m_to VARCHAR(30)			# The username the message is sent to
  	  m_subject VARCHAR(255)	# The subject of the message
  	  m_body TEXT				# The body text of the message
  	  m_created VARCHAR(255)	# The time stamp when the message was created
  	  
  *_news:
      n_id INT(11)				# unique identifier
      n_username VARCHAR(100)	# username or GEDCOM that the News item belongs to
      n_date INT(11)			# the time stamp when the News item was created
      n_title VARCHAR(255)		# the title of the article
      n_text TEXT				# the body text of the article
      
  *_places:
  	  p_id INT(11)				# unique identifier
      p_place VARCHAR(150)		# the place name
      p_level INT(11)			# the level of the place in the hierarchy, 
      							#    0 is the country or state
      p_parent_id INT(11)		# the id of this item's parent place in the hierarchy, 
      							#    so a city's parent would be the county it is in
      p_file VARCHAR(255)		# the GEDCOM file the place was imported from
      
  *_placelinks:
      pl_p_id INT(11)			# unique identifier
      pl_gid VARCHAR(30)		# The family or individual id where this place was found
      pl_file VARCHAR(255)		# The GEDCOM file where this individual and place 
      							#    were found
      
  *_users:
      u_username VARCHAR(30)	# the username
	  u_password VARCHAR(255) 	# the encrypted password
	  u_fullname VARCHAR(255) 	# the user's full name
	  u_GEDCOMid TEXT			# a serialized array representing the GEDCOM ids 
	  							#    for this user
	  u_rootid TEXT				# a serialized array representing the rootids for this user
	  u_canadmin ENUM('Y','N')	# is the user an admin or not
	  u_canedit TEXT			# a serialized array indicating the editing privileges 
	  							#    a user has for each GEDCOM
	  u_email TEXT				# the email addres
	  u_verified VARCHAR(20) 	# the user self verified
	  u_verified_by_admin VARCHAR(20)	# the user has been verified by the admin
	  u_language VARCHAR(50)	# the preferred language for the user
	  u_pwrequested VARCHAR(20)	# the user requested a new password
	  u_reg_timestamp VARCHAR(50)		# registration timestamp
	  u_reg_hashcode VARCHAR(255)		# registration hash key for self registration
	  u_theme VARCHAR(50) 		# the user's preferred theme
	  u_loggedin ENUM('Y','N') 	# whether the user is currently logged in or not
	  u_sessiontime INT(14) 	# the system time stamp when the user last logged in
  	  u_contactmethod VARCHAR(20)		# The user's preferred method of contact 
  	  							#    on the PGV system
  	  u_visibleonline ENUM('Y','N')		# Whether or not the user is visible in 
  	  							#    the logged on users block
  	  u_editaccount ENUM('Y', 'N')		# Whether or not the user can edit their 
  	  							#    own account information
      u_defaulttab INT(10)			# Default tab on the individual page to be shown
						  for this user
  
This table layout has received criticism from some for its simplicity, size,
and because it does not follow a genealogy model like GENTECH.  We admit that
these tables can be hard to interface to because the code has to understand
GEDCOM in order to get information out of them.  We also admit that storing the
raw GEDCOM data could make the tables very large.  

Fortunately the GEDCOM standard is not a very complex or large format; it only 
requires 6 characters per line, which is very good compared to something like 
XML.  However, there are some very compelling reasons why this table structure
was chosen:
  1. Because it stores the raw GEDCOM, the majority of the codebase between the
     MySQL and the standard version is the same.  The same codebase makes the
     two versions easier to maintain and upgrade the code from one to the
     other.  This simplifies life for the developers and means faster upgrades
     for you.
  2. Simpler tables mean fewer and simpler database queries.  This takes a
     large load off the database.
  3. Nothing is lost in the import.  Even though GEDCOM is a standard, each
     genealogy program interprets it a bit differently and adds its own tags.
     Creating a database model that conforms to all the GEDCOM outputs of 
     different genealogy software programs would be very difficult.

-------------------------------------------------------
MANUAL CONFIGURATION
     
Advanced users who understand PHP may want to configure manually by editing the
configuration file config.php  When you have finished editing config.php make 
sure that the variable $CONFIGURED=true; so that the program does not try to 
forward you to the configuration.php script when you launch it for the first
time.

You can manually add GEDCOMS to the system by adding them to the $GEDCOMS array
in the index/GEDCOMs.php file.  The GEDCOM array looks like this:
 $gedarray = array();
 $gedarray["GEDCOM"] = "surname.ged";
 $gedarray["config"] = "./index/surname.ged_conf.php";
 $gedarray["privacy"] = "./index/surname.ged_priv.php";
 $gedarray["title"] = "Surname Genealogy";
 $gedarray["path"] = "./surname.ged";
 $GEDCOMS["surname.ged"] = $gedarray;
"surname" above could be anything, for example, "johnson" or "private".  You
must pay attention to the case of what you enter.  PhpGedView is case sensitive.

Each GEDCOM will need a configuration file.  You can copy the config_GEDCOM.php
file which has all of the default values for each GEDCOM you add manually.
Then set the "config" item of the GEDCOMS array to point to the file you
copied.

Each GEDCOM also needs a Privacy file.  Make a copy the privacy.php file for
each GEDCOM and set the "privacy" item of the GEDCOMS array to the location of
the new privacy.php file.

-------------------------------------------------------
SECURITY

Even though PhpGedView gives you the ability to hide the details of living
individuals, whenever you post the personal details of living individuals on the
Internet, you should first obtain the permission of EACH living person you plan
to include.  There are many people who would not even want their name linked
with their family history made public on the Internet and their wishes should
be respected and honored.  Most family history programs allow you to choose the
people who are exported when you create your GEDCOM file.  The most secure
option is to deselect all living people in your genealogy program when you
export your genealogical data to a GEDCOM file.

If you wish to protect your GEDCOM file itself from being downloaded over the
Internet you should place it outside the root directory of your web server
or virtual host and set the value of the $GEDCOM variable to point to that
location.  For example, if your home directory is something like
"/home/username" and if the root directory for your website is
"/home/username/public_html" and you have installed PhpGedView in the
"public_html/PhpGedView" directory then you would place your GEDCOM file in
your home directory at the same level as your "public_html" directory.  You
would then set the file path to "/home/username/GEDCOM.ged" by editing the
GEDCOM configuration.

You can also manually set the location by changing the "path" line in
index/GEDCOMs.php:
	$gedarray["path"] = "../../GEDCOM.ged";
or
	$gedarray["path"] = "/home/username/GEDCOM.ged";

Since your GEDCOM file resides in a directory outside of your web server's root
directory, your web server will not be able to fullfill requests to download it.
However, PhpGedView will still be able to read and display its contents.

In the end it is YOUR responsibility to guarentee that there has been no
violation of an individual's privacy and YOU could be held liable should
private information be made public on the web sites that you administer.

For more privacy options visit:
http://www.PhpGedView.net/privacy.php

-------------------------------------------------------
LANGUAGES

PhpGedView has built-in support for multiple languages.  PHP does not support
unicode (UTF-16).  It does support UTF-8 and that is the recommended character
encoding for GEDCOMs with PhpGedView.  If you have characters in your GEDCOM
outside the standard ASCII alphabet, you should probably use the UTF-8
encoding.  There are many differences between UTF-8 and UTF-16, but anything
that you can encode in UTF-16 you should be able to encode in UTF-8.  It is
also quite easy to convert from Unicode to UTF-8.  Simply open your Unicode
GEDCOM file in Windows Notepad and select "File->Save As.." from the menu
and choose UTF-8 as the encoding option.  You shouldn't lose any of the
characters in the translation.

You should check the Patches section of
http://sourceforge.net/projects/PhpGedView to get the latest language files.

Discussion and questions about the multiple language features of PhpGedView
including translations, should be posted in the Translations forum available
from the PhpGedView project page here:
http://sourceforge.net/forum/forum.php?forum_id=294245

To translate PhpGedView into another language that is not currently supported 
you must first login to PhpGedView as an administrator and go to the Language 
Edit Utitility by clicking on "Admin-> Edit and configure language files".  At 
the bottom of that page is an option to Add a new language.  Choose your 
language from the dropdown list and click on the "Add new Language" button.  
A popup window will appear that allows you to edit the default settings for 
your language.  Each of the settings has online help available by clicking on 
the "?".  You might want to look at the settings for some of the other 
Languages on the edit language page to see how they are set up.  When you have 
finished editing the settings, click the Save button.  This will create a new
lang_settings.php file in the index directory.  You will notice that your
language now appears in the supported languages list.

Next create a copy of the "lang.en.php", "facts.en.php",
"configure_help.en.php" and "help_text.en.php" files located in the
"./languages/" and change the "en" part to match the two letter language code
of your language.  The "facts" file contains all of the translations for the
various GEDCOM tag such as BIRT = Birth.  The "lang" file contains all of the
language variables used throughout the site.  The "configure_help.en.php" and
"help_text.en.php" provide configuration and help instructions.

You can translate these files using the online Language File Edit utility.
Just select your language from the drop-down list and then select the file you
want to edit and click the Edit button.  Your file will be compared to the
English language file so that you can easily translate the files online.

You can also translate these files manually by opening them in any text editor.
If you manually edit the files, you must be sure to save them in the UTF-8 
character set.  Some text editors like "Notepad" add a 3 byte Byte-Order-Mark 
(BOM) to files they save in UTF-8.  PHP does not like the BOM and it should be 
removed before testing the files in PhpGedView.

You should obtain a flag file from http://w3f.com/gifs/index.html and size it
to match the other flags in the images/flags directory.

To help maintain languages, a language change log is provided in the languages
directory.  This change log is named LANG_CHANGELOG.txt.

If you make a new translation of PhpGedView or update another translation, and
would like to contribute it to the community please post your language files 
and your index/lang_settings.php file to as new Patch in the Patches section 
of the sourceforge project site at http://www.sourceforge.net/projects/phpgedview

-------------------------------------------------------
NON-STANDARD GEDCOM CODES

The GEDCOM 5.5 standard has a defined set of codes.  You can read the
specification online at http://www.PhpGedView.net/ged551-5.pdf  Part of the
standard allows for genealogy software to define their own codes, and requests
that they begin with an "_" underscore.  When PhpGedView comes across a tag
that is not defined it will display an error message.  You can disable these
error messages in the config.php file by setting $HIDE_GEDCOM_ERRORS=true.
PhpGedView can also be customized to work with these codes by adding them to
the facts array in a new language file named facts.en.extra.php.  If you add it
to the English facts file you should also add it to the other facts language 
files you are using on your site if you want other languages to translate the 
tag correctly.

The format of the facts file is a PHP associative array.  Each tag requires one
line in the array.  The following line defines the label "Abbreviation" for the
ABBR GEDCOM tag.
$factarray["ABBR"] = "Abbreviation";

As an example, if you use a genealogy program that generates the tag "_ZZZZ"
you can customize PhpGedView to accept this code by adding the following
lines to the facts.en.extra.php file:
<?php
$factarray["_ZZZZ"] = "Tag Label goes here";
?>

-------------------------------------------------------
LANGUAGE EXTENSION FILES

Language extension files are custom PHP files that you can use to make your own
language specific extensions to PhpGedView.  To add a language file extension,
create a new PHP file called lang.xx.extra.php replacing the "xx" with the
code for the language you want to extend.  These files are not automatically 
included with the package so that when you upgrade, your extensions are not 
overwritten.

If this file exists for the language that is chosen, it is the very last thing
that is loaded before the display starts.  These files were designed to be
language file extensions, but you could easily use it to make settings changes
based on the chosen language.

What sort of things can you do with language extensions?  
 - Customize any of the text that appears on the site.
 - Change configuration options based on language
 - Change to a different GEDCOM when someone views your site in a different
   language

The only settings that you should not override in this file are the privacy
settings.

If, for example, you wanted to change the GEDCOM title when you changed the
language, you could change the title for each language by adding the following
line to your lang.xx.extra.php:
$GEDCOMS["surname.ged"]["title"] = "Title in Chinese";

In this file you could also change the text on the buttons: 
$pgv_lang["view"]="Show";

With this file you could also change the GEDCOM that is displayed per language.
Suppose you had a GEDCOM that was in German and one that was in English.  In 
the lang.de.extra.php file I would add the following lines:

if ($GEDCOM=="english.ged") {
    header("Location: $PHP_SELF?$QUERY_STRING&GEDCOM=german.ged");
    exit;
}

These lines say that if we are using the German language files, but are using
the English GEDCOM, then we need to reload the page with the German GEDCOM.
You need to reload the page so that the configuration settings for the GEDCOM
get loaded.  This assumes that you have both "english.ged" and "german.ged"
imported into the database and that the english.ged and the german.ged have the
same people in them, just in a different language.  Thus I0001 in english.ged
should refer to the same I0001 in german.ged.


-------------------------------------------------------
MIGRATING FROM DATABASE TO INDEX MODE AND VICE VERSA

Basically it's possible to switch a PhpGedView installation from Index to DATABASE
mode or vice versa without losing any settings. The following steps have to be
made:

DATABASE to Index
--------------
1.  Make sure you have all rights in the ./index/ folder on your web site and on
    the file ./config.php
2.  Copy the file config.php to configsql.php (or any other name) to backup the
    old configuration. You may also use the Backup function from the Admin menu to
    backup all vital files before switching mode.
3.  DO NOT remove any files from your index directory, as some of them (Privacy
    and GEDCOM settings) will also be used in Index mode
4.  In DATABASE mode, log in to PhpGedView with Admin rights
5.  Go to the Administration page and select the User Information Migration tool
6.  Choose the Export function
7.  Usermigrate will try to create the following files:
    - authenticate.php  - user accounts and settings
    - favorites.dat     - user and GEDCOM favorites
    - blocks.dat        - block layout of Welcome and MyGedView pages
    - news.dat          - User and GEDCOM news
    - messages.dat      - User messages
    If any of the files already exist in your index directory, you will be prompted 
    to overwrite them. If there are problems creating the files, if, for instance,
    because you don't have sufficient rights, you can always correct the problem
    and run the Export function again, as nothing irreversible has happened.
8.  Check that the above files exist in your index directory
9.  Go to Admin, Configuration, change mode to Index and save the configuration
10. As all Index mode related files are already present, you should be able to
    use your website in Index mode immediately.
10. Import your GEDCOM files again to build the Index database. You don't need to change
    any GEDCOM settings, as they still exist in the index directory and will be used
    again.
11. Test all settings and functions thoroughly before you remove your SQL
    database from your website.

Index to DATABASE
--------------
1.  Make sure you have all rights in the ./index/ folder on your web site and on
    the file ./config.php
2.  Copy the file config.php to configindex.php (or any other name) to backup the
    old configuration. You can also use the Backup function from the Admin menu to
    backup all vital files before switching mode.
3.  DO NOT remove any files from your index directory, as all of them (Privacy
    and GEDCOM settings) will also be used in DATABASE mode, or will be used to migrate
    the information to DATABASE mode.
4.  Connect to your SQL DBMS with your regular administration tool, and define a
    database, without any tables.
5.  Create a user in your SQL-DBMS with the following rights on the database:
    SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER.
6.  In PhpGedView, go to Admin, Configuration, and change mode to DATABASE, fill in the
    appropriate SQL-DBMS and database name, user and user password and save the
    configuration.
7.  PhpGedView will ask you to create an admin user. Create one. This user will be 
    overwritten later with the migrated information.
8.  From the admin menu, choose the User Information Migration tool, then choose 
	import.
9.  PhpGedview will now import all settings from Index mode to DATABASE mode.
8.  Go to Admin, Manage GEDCOMs and Edit Privacy, and then import all your GEDCOM
    files again. There is no need to change GEDCOM settings and Privacy
    settings, as all settings made in Index mode will be used.
10. Test all settings and functions thoroughly before you remove Index related
    files (.\index\*.dat and .\index\authenticate.php) from your website

-------------------------------------------------------
POSTNUKE AND PHPNUKE INTEGRATION

PhpGedView can integrate with PostNuke and phpNuke so that your users do not have
to login twice.

After you have PhpGedView up and running, you should follow the instructions 
in the readme.txt file in the pgvnuke folder.

The files that make the integration magic happen were donated by Jim Carey.
    
===========================================================
BACKUP

With the Backup function in the administration menu, you can make a simple backup of all
important PhpGedview files. With this backup, it's possible to rebuild your PhpGedView
site to the situation at backup time.
The backup will contain the following files (if selected on the backup page):
- config.php with all configuration settings of your installation
- all GEDCOM files that were present in your installation
- all GEDCOM options and privacy settings for the above files
- counters, PhpGedView- and search-logfiles
- user definitions and options (block definitions, favorites, messages and news

The files will be gathered into a ZIP-file, which can be downloaded by clicking the link
on the page.

Note: The database itself will not be included in the backup, since it can be rebuilt
using the files in the backup.

Note: All pending changes (not approved or rejected yet by the administrator) will be
present in the GEDCOM files but cannot be identified as changes anymore. If the database
is rebuilt using the gedcoms, these changes therefore cannot be rejected anymore.

The Backup function uses the PclZip library, which is written by Vincent Blavet and
can be found at http://www.phpconcept.net/pclzip.
