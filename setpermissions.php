<?php
/**
 * Use this file to try and use PHP to set permissions on the neccessary files.
 */

if (chmod("config.php", 0777)) print "Successfully set permissions for config.php.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for config.php<br /></b></font>";

if (chmod("index", 0777)) print "Successfully set permissions for index directory.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for index directory<br /></b></font>";

if (chmod("index/*", 0777)) print "Successfully set permissions for files in the index directory.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for files in the index directory<br /></b></font>";
?>