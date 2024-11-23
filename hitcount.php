<?php
/*=================================================
	Project: phpGedView
	File: hitcount.php
	Author: Jeremy Eden
	Comments:
		Counts how many hits.

	Change Log:
		2/17/04 - File Created

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
# $Id: hitcount.php,v 1.1 2005/10/07 18:08:01 skenow Exp $
if (strstr($_SERVER["PHP_SELF"],"hitcount")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

// Check if file_get_contents() is supported
if (!function_exists("file_get_contents")) {
  function file_get_contents($filename) {
   $filestring = "";
   $file = @fopen($filename, "r");
   if ($file) {
     while (!feof($file)) $filestring .= fread($file, 1024);
     fclose($file);
   }
   return $filestring;
  }
}

//only do counter stuff if counters are enabled
if($SHOW_COUNTER)
{
  $PGV_COUNTER_FILENAME = $INDEX_DIRECTORY.$GEDCOM."pgv_counters.txt";
  $PGV_COUNTER_NAME     = $GEDCOM."pgv_counter";
  $PGV_INDI_COUNTER_NAME = $GEDCOM."pgv_indi_counter";

  // if counter file doesn't exist create it
  if(!file_exists($PGV_COUNTER_FILENAME))
  {
      $fp=fopen($PGV_COUNTER_FILENAME,"w");
      fputs($fp,"0");
      fclose($fp);
  }

  if(isset($pid)) //individual counter
  {
    //see if already viewed individual this session
    if(isset($_SESSION[$PGV_INDI_COUNTER_NAME][$pid]))
  	{
  	  $hits = $_SESSION[$PGV_INDI_COUNTER_NAME][$pid];
  	}
  	else //haven't viewed individual this session
  	{
      $l_fcontents = file_get_contents($PGV_COUNTER_FILENAME);
    	$ct = preg_match_all ("/@$pid@\s(\d+)/",$l_fcontents,$matches);
    	if($ct>0) //found individual increment counter
    	{
    		$hits = $matches[1][0];
    		$hits++;
    		$l_fcontents = preg_replace("/(@$pid@) (\d+)/","$1 $hits",$l_fcontents);
    		$fp=fopen($PGV_COUNTER_FILENAME,"r+");
    		fputs($fp,$l_fcontents);
    		fclose($fp);
    	}
    	else //first view of individual
    	{
    	  $fp=fopen($PGV_COUNTER_FILENAME,"r+");
    		fseek($fp,0,SEEK_END);
    		fputs($fp,"\r\n@".$pid."@ 1");
    		fclose($fp);
    		$hits=1;
    	}
  		$_SESSION[$PGV_INDI_COUNTER_NAME][$pid] = $hits;
  	}
  }
  else //web site counter
  {
    // has user started a session on site yet
    if(isset($_SESSION[$PGV_COUNTER_NAME]))
    {
  	  $hits = $_SESSION[$PGV_COUNTER_NAME];
    }
    else //new user so increment counter and save
    {
    	$l_fcontents = file_get_contents($PGV_COUNTER_FILENAME);
    	$ct = preg_match ("/^(\d+)/",$l_fcontents,$matches);
    	if($ct)
        {
				   $hits = $matches[0];
					 $hits++;
					 $ct = preg_match("/^(\d+)@/",$l_fcontents,$matches);
					 if($ct) //found missing return & newline
					   $l_fcontents = preg_replace("/^(\d+)/","$hits\r\n",$l_fcontents);
					 else  //returns & newline exist
					   $l_fcontents = preg_replace("/^(\d+)/","$hits",$l_fcontents);
    		   $fp=fopen($PGV_COUNTER_FILENAME,"r+");
    		   fputs($fp,$l_fcontents);
    		   fclose($fp);
				 }
				 else
				   $hits=0;
     		 $_SESSION[$PGV_COUNTER_NAME]=$hits;
    }
  }

  //replace the numbers with their images
  for($i=0;$i<10;$i++)
    $hits = str_replace("$i","<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$i]["digit"]."\" alt='pgv_counter' />","$hits");

  $hits = str_replace('alt=""','alt="" border="0" height="12" width="10"',$hits);
  $hits = '<span dir="ltr">'.$hits.'</span>';
}
?>