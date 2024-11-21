<?php
/**
 * Routines to aid in media database version upgrades
 * 
 * As this may(should) be called when a specific versions variables
 * are not around suring upgrade process this will try to be as 
 * atomic as possible with all required information passed as parameters. 
 * This should keep version dependencies to a minimum.
 *
 * The one assumption it makes is that the database connection has
 * already been made to the correct database.
 *
 * Some of the routines may seem anal in their checking and duplication
 * but this is peoples data we are playing with, better safe than sorry.
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
 * @subpackage MediaDB
 */
 
//-- if not called from check_media_db then we need some of the function in that file 
if (!function_exists("check_media_db")) {require("functions_mediadb.php");};
 
/**
 * Version 3x to 3.2 upgrade to 3.3 routine
 *
 * There is one scenario where users may feel they have lost data.
 * If the user has linked a single media item to many individuals and given the media
 * a different title for each instance. There is a commented line which will add the 
 * original title for each link as a note on the link. It has been marked clearly in
 * the code.
 *
 * Old structure of 3.2  
 * m_id int(11) NOT NULL auto_increment,
 * m_indi varchar(15) NOT NULL,
 * m_ext char(3) NOT NULL,
 * m_ind char(1) NOT NULL,
 * m_titl varchar(255),
 * m_order int(11) NOT NULL,
 * m_file varchar(255) NOT NULL,
 * m_gedfile varchar(255) NOT NULL,
 *
 * Steps taken
 *
 * Backup the users data to a place this script will never touch again 
 *
 * upgrade to new format
 * 
 * 1: rename existing table with _bak
 * 2: build the current media tables empty
 * 3: walk the old table 
 *   4a insert unique filename/gedcom pairs into the media table
 *   4b adding xrefs to the mapping table
 * 5: remove working table
 * 5: now ready for next reimport the gedcom
 *
 * @param string $TBLPREFIX prefix to use for the table names
 * @return bool Success flag.
 */
function media_db_upgrade_3x_33($TBLPREFIX){
 	
 	
 	//-- Step 1: Just in case the user has to run this twice make an original copy and never touch it thereafter
	$sql = "SHOW TABLES";
	$res =& dbquery($sql);
	$has_org = false;
	while($table =& $res->fetchRow()) {
		if ($table[0]==$TBLPREFIX."mediaorg") $has_org = true;
	}
 	
 	if (!$has_org) {
 	    // -- Don't recreate the table with auto inc
		$sql = "CREATE TABLE ".$TBLPREFIX."mediaorg (
		m_id int(11) NOT NULL,
		m_indi varchar(15) NOT NULL,
		m_ext char(3) NOT NULL,
		m_ind char(1) NOT NULL,
		m_titl varchar(255),
		m_order int(11) NOT NULL,
		m_file varchar(255) NOT NULL,
		m_gedfile varchar(255) NOT NULL,
		PRIMARY KEY ( m_id ))";
			$res =& dbquery($sql);
			if(!DB::isError($res)) {
				
		    $sql = "INSERT INTO `".$TBLPREFIX."mediaorg` SELECT * FROM `".$TBLPREFIX."media` ";
				$res =& dbquery($sql);
				if(DB::isError($res)) {
					print "Media DB Upgrade stage 2 failed: could not populate primary backup table";
					return false;
				}
			} else {
				print "Media DB Upgrade stage 1 failed: could not create primary backup table";
				return false;
 		}
 	}
 	
 	
 	//-- Step 2: ok we have our permanent backup now for our working table 	 
 	$sql =  "ALTER TABLE `".$TBLPREFIX."media` RENAME `".$TBLPREFIX."media_bak`"; 
 	$res =& dbquery($sql);
	if(DB::isError($res)) {
		print "Media DB Upgrade stage 3 failed: could not create working backup table";
		return false;
	}
	
	//-- Step 3: Create the new table structure
 	if (!media_db_make_current_version($TBLPREFIX)) {
		print "Media DB Upgrade stage 4 failed: could not create new tables";
		return false;
 	}
 		
 	//-- Step 4: Import the data
 	$sql = "SELECT * FROM  ".$TBLPREFIX."media_bak ORDER BY m_gedfile, m_file";
 	$res =& dbquery($sql);
	
 	$oldfilename = "";
 	$m_media_id = "";
 	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
 		// add the media table entry if this is the first time we have seen this filename
 		if ($oldfilename != stripslashes($row["m_file"])) {
			$sql = "INSERT INTO ".$TBLPREFIX."media VALUES(NULL, '','".addslashes(stripslashes($row["m_ext"]))."','".addslashes(stripslashes($row["m_titl"]))."','".addslashes(stripslashes($row["m_file"]))."','".addslashes(stripslashes($row["m_gedfile"]))."','')";
			$res1 =& dbquery($sql);
			$sql1 = "SELECT LAST_INSERT_ID() `lindex`";
			$res1 =& dbquery($sql1);
			$ct = $res1->numRows();
			if ($ct==1) {
				$row1 =& $res1->fetchRow(DB_FETCHMODE_ASSOC);
				$m_id = $row1["lindex"];
			}
			$pad = array("0000","000","00","0","","","");
			$m_media = "M".$pad[strlen($m_id)].$m_id;
			$sql1 = "UPDATE ".$TBLPREFIX."media SET m_media = '".$m_media."' WHERE m_id = ".$m_id;
			$res1 =& dbquery($sql1);
 		
 			$m_media_id = $m_media;
 			$oldfilename = stripslashes($row["m_file"]);
 		}
		// build the gedrec, we want the m_ind which translates to the _PRIM tag and 
		// store the m_titl in a note just in case the old data had different titles 
		// for the same picture against different individuals 		
 	    $gedrec = "1 OBJE @".$m_media_id."@";
		$gedrec .= "\r\n2 _PRIM ".$row["m_ind"];
		$gedrec .= "\r\n2 _THUM N";

/*************************************************************************************************************************
**************************************************************************************************************************
**************************************************************************************************************************
*																														 *	
*	UNCOMMENT THIS LINE IF YOU WANT TO KEEP ORIGNAL TITLES ASSIGNED TO INDIVIUAL LINKS AS NOTES ON THE LINK				 *
*		$gedrec .= "\r\n".trim(textblock_to_note(2,stripslashes($row["m_titl"])));										 *
*																														 *
**************************************************************************************************************************
**************************************************************************************************************************		
**************************************************************************************************************************/		
 		
		$sql1 = "INSERT INTO ".$TBLPREFIX."media_mapping VALUES(NULL, '".$m_media_id."','".addslashes(stripslashes($row["m_indi"]))."','".addslashes(stripslashes($row["m_order"]))."','".addslashes(stripslashes($row["m_gedfile"]))."','".addslashes($gedrec)."')";
		$res1 =& dbquery($sql1);
 		
 	}

 	//-- well we finally got here so delete the working table
	$sql = "DROP TABLE `".$TBLPREFIX."media_bak`";
	$res =& dbquery($sql,false);
	if(DB::isError($res)) {
		print "Media DB Upgrade Warning: Successfully transfered the data but could not remove working table";
	}
 	
 	return true;
}

/** Older versions 265
	m_indi varchar(15) NOT NULL,
	m_indiname varchar(30) NOT NULL,
	m_name varchar(30) NOT NULL,
	m_ext char(3) NOT NULL,
	m_ind char(1) NOT NULL,
	m_titl varchar(255) NULL,
	m_order int(11) NOT NULL,
	m_file varchar(255) NOT NULL,
	PRIMARY KEY (m_indi , m_name, m_file))";
*/
function media_db_upgrade_2x_33($TBLPREFIX) {
  print "<div class=\"error\">Automatic upgrade from phpGedView 2.x is not yet available, please see readme_media_db</div>";	
}

 

/**
 * Create the database to the current schema
 *
 * Uses parameter $TBLPREFIX instead of global in case some upgrade
 * scripting wants to use this instead of the day to day checking with
 * all variable in a sane state.
 *
 * @param string $TBLPREFIX prefix to use for the table names
 * @return bool Success flag.
 */
function media_db_make_current_version($TBLPREFIX) {

	$sql = "SHOW TABLES";
	$res =& dbquery($sql);
	$has_media = false;
	$has_media_mapping = false;
	while($table =& $res->fetchRow()) {
		if ($table[0]==$TBLPREFIX."media") $has_media = true;
		if ($table[0]==$TBLPREFIX."media_mapping") $has_media_mapping = true;
	}
	
	// this is really redundant code but here just in case a dev forgets to delete a table
	// or the routine has to be run twice. Also will delete mapping table and contents
	// if the media table is destroyed, if that has gone no point in a mapping table.
	if ($has_media) {
		$sql = "DROP TABLE `".$TBLPREFIX."media`";
 		$res =& dbquery($sql,false);
	}
	if ($has_media_mapping) {
		$sql = "DROP TABLE `".$TBLPREFIX."media_mapping`";
 		$res =& dbquery($sql,false);
	}
	
	// create the mapping table
	if(!media_db_create_mapping($TBLPREFIX)) {return false;}
	
	// media table
	$sql = "CREATE TABLE ".$TBLPREFIX."media (
	m_id int(11) NOT NULL auto_increment,
	m_media varchar(15) NOT NULL,
	m_ext char(6) NOT NULL,
	m_titl varchar(255) NULL,
	m_file varchar(255) NOT NULL,
	m_gedfile varchar(255) NOT NULL,
	m_gedrec text,	
	PRIMARY KEY(m_id))";
	$res =& dbquery($sql);
	if(DB::isError($res)) {return false;}
	
	$sql = "ALTER TABLE `".$TBLPREFIX."media` ADD INDEX ( `m_media` )";
	$res =& dbquery($sql);
	
	return true;
}

/**
 * Creates the mapping table to current schema
 * 
 * Seperated from the main build all script as this table may get
 * lost (dropped accidentally etc) so it is recreated on it's own
 * with a warning that data may have been lost.
 *
 * @param string $TBLPREFIX prefix to use for the table names
 * @return bool Success flag.
 */
function media_db_create_mapping($TBLPREFIX) {
	
	// create the mapping table
	$sql = "CREATE TABLE ".$TBLPREFIX."media_mapping (
	m_id int(11) NOT NULL auto_increment,
	m_media varchar(15) NOT NULL,
	m_indi varchar(15) NOT NULL,
	m_order int(11) NOT NULL,
	m_gedfile varchar(255) NOT NULL,
	m_gedrec text,	
	PRIMARY KEY(m_id))";
	$res =& dbquery($sql);
	if(DB::isError($res)) {return false;}
	
	// can live without these
	$sql = "ALTER TABLE `".$TBLPREFIX."media_mapping` ADD INDEX ( `m_media` )";
	$res =& dbquery($sql);

	return true;	
}
?>