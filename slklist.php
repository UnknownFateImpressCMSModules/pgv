<?php
/*=================================================
	Project: phpGedView-MySQL
	File: slklist.php
	Author: Dick Kaas (dick@kaas.nl)
	Copyright (C) 2004
	Prerequisit:
		First run patriarchlist before running this program. It needs the list produced by that program.
	Comments:
		The program converts the GEDCOM file (and other information) into a SLK EXCEL file
		This file is input for EXCEL and has the same values as the file (in .csv format) that 
		is needed for the PERL program namen.pl
	Change Log:
		9/8/03 - File Created
		25/10/03 - complete output as required by namen.pl (in excel format)
		19/12/03 - added EXCEL file name in SLK file
		23/02/04 - added an extra sort on birthdate of patriarch's with the same name (and ignored later)
		25/05/04 - change. I now take most of the information out of the original GEDCOM file
			for every item I collect the individual stuff (before only the first item was taken
			This will help also later on action to merge differt files
		06/01/05 - included are saving of NICK names, BAPT dates
		16/01/05 - splitindilines changed in the way that every line will produce a result and not
				only lines on a maximum
		23/07/05 - implement nicknames on output. Also refer to lang file for output
		03/08/05 - a progress indicator was implemented. Both on checking all individuals and on writing the file


to be done;
- just run first part only if neccessary (file changes)
- add helpfile
- put different routines in subroutine file
- In the future I want to make a slk file defined by the user by way of unique codes. i.e. give a column number
	to codes like DEAT/PLACE, DEAT/DATE, NAME/NICK etc. A default can be prefixed.
- add maker references at the start and source references at the end
- reading long lines in EXCEL, produced by SLKLIST, until now will result in a warning. I.e. commentlines


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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02pos_none1-1307  USA

===================================================*/
require("config.php");

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!userIsAdmin(getUserName())) 
{
	header("Location: login.php?url=editgedcoms.php");
	exit;
}

global $newtime, $oldtime;
$oldtime= time();

//-- print time routines
function print_my_time($s)
{
global $newtime, $oldtime;
	$newtime = time();
	$exectime = $newtime - $oldtime;
//--	print "cpu tijd = " . $s . ": ". $exectime . "\n";
}

print_my_time("before header");
print_header($pgv_lang["slklist_header"]);
print "\n\t<center><h2>".$pgv_lang["slklist_header"]."</h2>\n\t";
print "</center>";
flush();


//-- locations in the EXCEL SLK file
define ("pos_type", 1);
define ("pos_RESN", 2);
define ("pos_GENlevel", 3);
define ("pos_GENgen", 4);
define ("pos_GENref", 5);
define ("pos_source_PUBL", 5);
define ("pos_NAME_SURN", 6);
define ("pos_source_ABBR", 6);
define ("pos_GENinitials", 7);
define ("pos_NAME_BIRT", 8);
define ("pos_source_AUTH", 8);
define ("pos_NAME_NICK", 9);
define ("pos_SEX", 10);
define ("pos_MARR_TYPE", 10);
define ("pos_CHR_DATE", 11);
define ("pos_BIRT_DATE", 12);
define ("pos_MARR_DATE", 12);
define ("pos_source_DATE", 12);
define ("pos_DEAT_DATE", 13);
define ("pos_DIV_DATE", 13);
define ("pos_FATHERref",14);
define ("pos_source_TITL",14);
define ("pos_MOTHERref",15);
define ("pos_BIRT_PLAC",16);
define ("pos_MARR_PLAC",16);
define ("pos_DEAT_PLAC",17);
define ("pos_DIV_PLAC",17);
define ("pos_BIRT_WITN",18);
define ("pos_MARR_WITN",18);
define ("pos_DEAT_WITN",19);
define ("pos_DIV_WITN",19);
//--define ("pos_DOC",18);
//--define ("pos_PICT",19);
define ("pos_INFO",20);
define ("pos_OCCU",21);
define ("pos_REFN",22);
define ("pos_NOTE",23);
define ("pos_CHAN_DATE",24);
define ("pos_CHAN_NOTE",25);
define ("pos_SOUR",26);
define ("pos_max",27);
define ("pos_none",28);

//-- locations in the saving intermediate file
define ("mytype", 1);
define ("mylevel", 2);
define ("mygennum", 3);
define ("mykey", 4);
define ("myfather", 5);
define ("mymother", 6);
define ("myfam",7);
define ("mytabblad", 8);

//-- locations in the GEDCOM file
define ("inp_nr", 0);
define ("inp_naam", 1);
define ("indidelimeter", ":");

global $newtime, $oldtime;
$oldtime= time();


//--	========================= progress routines in Java====================== 
?>
	
	<script type="text/javascript">
	<!--
	var FILE_SIZE = <?php print count($indilist); ?>;

	function complete_progress(string,progress_div) {
		perc = 100;
		progress = document.getElementById(progress_div);
		progress.style.width = perc+"%";
		progress.innerHTML = string;
	}

	function update_progress(percentage,progress_div) {
		perc = Math.round(percentage);
		progress = document.getElementById(progress_div);
		progress.style.width = perc+"%";
		progress.innerHTML = perc+"%";
	}
	
	//-->
	</script>
<?php

function my_progress_init($s,$progress_div1)
{
	print "<BR>\n";
	print "<div class=\"person_box\" style=\"width: 350px; text-align: center;\">\n";
	print "<b>".$s."</b>";
	print "<div style=\"width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
	print "<div id=\"". $progress_div1 . "\" class=\"person_box\" style=\"width: 1%; height: 18px; align: center; text-align: center; overflow: hidden;\">1%</div>\n";
//--	print "<div id=\"progress_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
	print "</div>\n";
	print "</div>\n";
//--	print "<table class=\"list_table\"><tr><td class=\"list_label\">".$pgv_lang["exec_time"]."</td><td class=\"list_label\">".$pgv_lang["bytes_read"]."</td>\n";
//--	print "<td class=\"list_label\">".$pgv_lang["found_record"]."</td><td class=\"list_label\">".$pgv_lang["id"]."</td><td class=\"list_label\">".$pgv_lang["type"]."</td></tr>\n";
	flush();
global $ilast;
	$ilast=0;
	my_progress_check(0,5000,$progress_div1);

}

function my_progress_complete($s,$s2)
{
	print "</table>\n";
	print "<script type=\"text/javascript\">complete_progress('$s','$s2');</script>\n";
	flush();
}

global $ilast;

function my_progress_check($count,$maxcount,$divid)
{
global $ilast;
	$i= floor(($count / $maxcount)* 100);
	if ($i > 100) {$i=100;}
//--printf ("%06.2f,%06.0f,%06.0f,%10s<br>",$i,$count,$maxcount,$divid);		
	if ($i<>$ilast) 
			{
				$ilast= $i;
//--				$newtime = time();
//--				$exectime = $newtime - $oldtime;
//--				print "<tr><td class=\"list_value\">$exectime ".$pgv_lang["sec"]."</td>\n";
//--				print "<tr><td class=\"list_value\">$i<script type=\"text/javascript\">update_progress($i);</script></td></tr>\n";
				print "<script type=\"text/javascript\">update_progress($i,'$divid');</script>\n";
//--				print "<script type=\"text/javascript\">update_progress(10);</script>\n";
				flush();
			}

}

//--	=========================== end progress routines ==================

function sort_patriarch_list()
{
global $ct,$patriarchlist,$patriarchalpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;
global $romeins;
$patriarch= array();
$exceltab = array();
global $numtabs,$patriarch,$exceltab;
global $match1,$match2,$usedinitials;

$personkey1= array();
$fatherkey1= array();
$motherkey1= array();
$famkey1= array();
$years= array();
/*	purpose
	to sort the patriarchlist in a way that the names are alphbetically sorted (so they come from patriarchlist)
	and to sort within the same name in a way that the oldest patriarch will be on the first place
	I can change keys and values but how do I combine them in patriarchlist??
	The algorim is as follows.
	i=i + 1 
		put the year of that name in $years[i]
		until a name changes. 
		then loop from j until i-1 and k until i-1
			if year[k] < year[j] change the values and keys 
			[if there are much of the same name just change the index and fill a new array later on]
	until last-i
	so far I do not use the sorting
*/
	$keys = array_keys($patriarchlist);
	$values = array_values($patriarchlist);
	$i=0; $j=0; $oldnaam=""; $oldyear= 0;
	return;
//-- regel 146
	while ($i<$ct)
	{
		$ref= $keys[$i];
		$value= $values[$i]["name"];
		$person= find_person_record($ref);
		$naam="";
//--	print "getnameitem 2:" . $value . "<BR>";
		if (getnameitem($value)!==false)
		{	$naam= $match1[1];
		}
		if ($naam !== $oldnaam)
		{	$oldnaam= $naam; $j=$i; $oldyear=0;
		}
		$year= 10;
		if ($oldyear > $year)
		{	$k= $i;
			while ($k > $j)
			{
				$k--;
			}
		}
//--pak de naam
//--pak het geb jaar
//--check voor gelijke namen of laagste geb jaar als eerst is. anders omruilen
		$i++;
	}
}

function roots2number()
{
//--	$maxgen= integer;
//--	$maxsingle= integer;
//--	$maxmulti= integer;
	$notesingle= array();
	$parents= array();
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;

global $perccounter,$maxcounter;

function fill_in($nr,$key,$value,$level,$nrgenstr,$father,$mother,$tabblad)
//--	nr= nr of familys found
//--	key= value of I number
//--	value= record belonging to key
//--	level= level of anchestors
//--	nrgenstr= string that contains anchestorline so far
{
//-- print "start fill_in<br />";
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfather,$mymother,$mytabblad;
global $nrgen, $levelgen;
global $perccounter,$maxcounter;

//-- print ("start fill:".$nr.":".$key.":".$value.":".$level.":".$nrgenstr.":".$romeins[$level]."<br />");
#regel 179
	$levelgen["$key"]= $romeins[$level];
	$nrgen["$key"]= $nrgenstr;
	$tabbladname["$key"]= $tabblad;
	$kk=0;

	$person= find_person_record($key);
	$fams="";
	$ctf= preg_match_all("/1\s*FAMS\s*@(.*)@/",$person,$match,PREG_SET_ORDER);
//--	If first call check if this dynasty is a single person or has a lot of children with the same name

$perccounter++;
	my_progress_check($perccounter,$maxcounter,'progress_div1');

//--	loop for the recursive trail
	$ii=0;
	//-- print ("aantal relaties:".$ctf.":"."<br />");
	while ($ii < $ctf)
//--	loop for every relation
	{
		$fams= $match[$ii][1]; $ii++;
		$famlines= find_family_record($fams);
//--	check if there is a husband. If so stop
		$parents= find_parents($fams);
		$stop=1;
		if ($parents["WIFE"] == $key)
		{
	//-- print ($key . "is vrouw<br />");
			if ($parents["HUSB"] != "")
			{
	//-- print ($key . "is vrouw met man<br />");
				$stop=0;
			}
		}
//-- print ("parents zijn:".$parents["HUSB"].":".$parents["WIFE"]."<br />");
	$xfather= $parents["HUSB"]; $xmother= $parents["WIFE"];

//--	loop for every child
		if ($stop > 0)
		{
		$chil="";
		$ctc= preg_match_all("/1\s*CHIL\s*@(.*)@/",$famlines,$match1,PREG_SET_ORDER);
	//-- print ("aantal kinderen van:".$fams.":".$ctc."<br />");

		$jj=0;
		while ($jj < $ctc)
		{	$chil= $match1[$jj][1]; $jj++;
			$kk= $kk+1;
			$fullname= get_sortable_name($chil);
			$nrgenstr1= $nrgenstr . $kk . ".";
//-- print ("volgnr en kind:".$kk.":".$chil."---".$nrgenstr1."---".$fullname."<br />");
			$maxgen1= fill_in($kk,$chil,$fullname,$level+1,$nrgenstr1,$xfather,$xmother,$tabblad);
		}
		}
	};
}

function fill_in_array($maxperson,$personkey,$famkey,$fatherkey,$motherkey,$level)
//--	maxperson = number of keys in personkey
//--	personkey = arry of keys
//--	level= level of anchestors
{
//-- print "start fill_in_array<br />";
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
global $nrgen, $levelgen;

//--$maxperson1= integer;
$personkey1= array();
$fatherkey1= array();
$motherkey1= array();

	$maxperson1= 0;
	$ll=0;
	$lastfam= "";
	while ($ll < $maxperson)
{	$ll++;
	$key= $personkey[$ll];
	$myfam= $famkey[$ll];
	$myfather= $fatherkey[$ll];
	$mymother= $motherkey[$ll];
//--if ($key == "") {print ("zoek5 I646:".$key.":".$myfam.":".$myfather.":".$mymother.":"."<br />");}
	if ($key == "")
	{
		$lastfam= $myfam;
//--		put the relation record of this person in the list
		$mytype= 2; $mylevel= ""; $mygennum= ""; $mykey= $myfam;
		$myrecord++; putmylist();
		continue;
	}

//--	in all other cases put the record of this person in the list
	$mylevel= $levelgen["$key"];
	$mygennum= $nrgen["$key"];
	$mytabblad= $tabbladname["$key"];
	$mytype= 1; $mykey= $key;
	$myrecord++; putmylist();
$value="--";
$nr="--";
$nrgenstr="--";

//--	print ("start fillin array:".$nr.":".$key.":".$value.":".$level.":".$nrgenstr.":".$romeins[$level]."<br />");
	$kk=0;

	$person= find_person_record($key);
	$fams="";
	$ctf= preg_match_all("/1\s*FAMS\s*@(.*)@/",$person,$match,PREG_SET_ORDER);
//--	If first call check if this dynasty is a single person or has a lot of children with the same name

	$ii=0;
//--	print ("aantal relaties:".$ctf.":"."<br />");
	while ($ii < $ctf)
//--	loop for every relation
	{
		$fams= $match[$ii][1]; $ii++;
		$famlines= find_family_record($fams);
//--	check if there is a husband. If so stop
		$parents= find_parents($fams);
		$stop=1;
		if ($parents["WIFE"] == $key)
		{
//--			print ($key . "is vrouw<br />");
			if ($parents["HUSB"] != "")
			{
//--				print ($key . "is vrouw met man<br />");
				$stop=0;
			}
		}
//-- 		print ("parents zijn:".$parents["HUSB"].":".$parents["WIFE"]."<br />");
		$xfather= $parents["HUSB"]; $xmother= $parents["WIFE"]; $xfam= $fams;

//--	fill in for every relation that has to be filled in
		if ($stop > 0)
		{
			$chil="";
//-- this is a dummy child just to recognize it is a relation
			$maxperson1++;
			$personkey1[$maxperson1]= $chil; $famkey1[$maxperson1]= $xfam;
			$fatherkey1[$maxperson1]= $xfather; $motherkey1[$maxperson1]= $xmother;

			$ctc= preg_match_all("/1\s*CHIL\s*@(.*)@/",$famlines,$match1,PREG_SET_ORDER);
//--	print ("aantal kinderen van:".$fams.":".$ctc."<br />");

			$jj=0;
//--	loop for every child
			while ($jj < $ctc)
			{	$chil= $match1[$jj][1]; $jj++;
				$maxperson1++;
				$personkey1[$maxperson1]= $chil; $famkey1[$maxperson1]= $xfam;
				$fatherkey1[$maxperson1]= $xfather; $motherkey1[$maxperson1]= $xmother;
			}
		}
	};
//-- 	if all children are in the array go for the next generation
}
//-- 	this was the loop for all elements in the array

	if ($maxperson1 > 0)
	{
		fill_in_array($maxperson1,$personkey1,$famkey1,$fatherkey1,$motherkey1,$level+1);
	}
}

function initbasetab()
{
//-- initialize $tabbladnr for all keys.
global $ct,$patriarchlist,$patriarchalpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;

	$i=0;

	while($i<$ct)
	{
		$value = $values[$i]["name"];
		$key = $keys[$i];
		$tabbladnr["$key"]= 0;
		$i++;
	}
}

function setbasetab($code,$tabblad,$name1)
{
//-- look for the different familys. Each should later on be put on a separate EXCEL tab.
//-- code=0 initialisation; 1= given family, 2=all familys not used before
//-- print "start setbasetab<br />";

global $ct,$patriarchlist,$patriarchalpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;

//--	$keys = array_keys($patriarchlist);
//--	$values = array_values($patriarchlist);
	$name= $name1;
	$i=0;

	while($i<$ct)
	{
		$value = $values[$i]["name"];
		$key = $keys[$i];
		if ($code < 2)
		{
			$namen= get_person_surname($key);
//--print(",key,namen,namen:".$code.":".$i.":".$ct.":".$key.":".$namen.":".$name.":"."<br />");
			if ($namen == $name)
			{	$maxmulti= $maxmulti + 1;
				$tabbladname["$key"]= $tabblad;
				$tabbladnr["$key"]= $maxmulti;
				$tabbladnrreverse[$maxmulti]= $i;
print ("gevonden:".$maxmulti.":".$key.":".$name.":".$tabbladnr["$key"]."<br />");
			}
		}
		elseif ($tabbladnr["$key"] < 1)
		{
			$maxmulti= $maxmulti + 1;
			$tabbladname["$key"]= $tabblad;
			$tabbladnr["$key"]= $maxmulti;
			$tabbladnrreverse[$maxmulti]= $i;
 //--print ("gevonden:".$code.":".$tabbladnr["$key"].":".$maxmulti.":".$key.":".$tabblad."<br />");
		}
	$i++;
	}
}
//-- end basetab

function fillpatriarch($par1,$par2)
{
global $numtabs,$patriarch,$exceltab;
	$numtabs++;
	$exceltab [$numtabs]= $par1;
	$patriarch[$numtabs]= $par2;
}

function filltabs()
{
//--	filltabs will read the list of patriarch-s and the EXCEL tabs they should be listed on.

global $numtabs,$patriarch,$exceltab;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	$numtabs= 0;
define ("fullist",0);
	if (fullist == 1)
	{
	fillpatriarch("kaas","Kaas");
	fillpatriarch("kommer","Commerscheit");
	fillpatriarch("kostelijk","Kostelijk");
	fillpatriarch("huibers","Huibers");
	fillpatriarch("strijbis","Strijbes");
	fillpatriarch("bak","Bak");
	fillpatriarch("wagenaar","Wagenaar");
	}
	fillpatriarch("$GEDCOM","");
}
//-- end filltabs

global $ct,$patriarchlist,$patriarchalpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;
global $romeins;
global $pgv_lang;

$patriarch= array();
$exceltab = array();
global $numtabs,$patriarch,$exceltab;

$personkey1= array();
$fatherkey1= array();
$motherkey1= array();
$famkey1= array();


	$keys = array_keys($patriarchlist);
	$values = array_values($patriarchlist);
//--print("basetab2. key,value:".$keys[1].":".$keys[2].":".$values[1].":".$values[2]."<br />");
//--	read the different family's to deal with and assign a tabname(EXCEL) to it. Name overig will always be the last one

	filltabs();
	initbasetab();
//--	Now set the name of the tab to every patriarch. Setting will be as follows
//--	all persons related to a given family will be in that tab
//--	all persons with no parents (remark we only have earthfather/mothers) and with children with a given name
//--	will be in that tab (normally wifes or men with no children)

	$maxsingle= 0;
	$maxmulti= 0;
	$jj= 1;
	while ($jj <= $numtabs)
	{	$kk= 1;
		if ($jj == $numtabs) {$kk= 2;}
		setbasetab($kk,$exceltab[$jj],$patriarch[$jj]);
		$jj++;
	}
//--	print("==============".$ct.":".$maxmulti."============<br />");
	$endmulti= $maxmulti;
//--	$kk= 3; $jj= $numtabs;
//--		setbasetab($kk,$exceltab[$jj],$patriarch[$jj]);
//--	$endmulti= $maxmulti;
//--	print("==============".$ct.":".$maxmulti."============<br />");
//--	================= remove later on. check on case!!!=====

	$oldtab= "";
	$j=1;
	while($j<=$maxmulti)
	{	$i= $tabbladnrreverse[$j];
		$value = $values[$i]["name"];
		$key = $keys[$i];
//--if ($key == "I2473") {print("reversetab:".$j.":".$i.":".$key.":".$value.":"."<br />");}

//--/??	print_list_person($key, array($value, $GEDCOM));
		$maxgen= 0;
		$level=1;
		$tabnr= $tabbladnr["$key"];
		$tabblad= $tabbladname["$key"];
		if ($oldtab !== $tabblad)
		{ $begintab["$tabblad"]= $myrecord; $oldtab= $tabblad;
//--print ("====tabbladname en beginwaarde:". $tabblad . " : " . $myrecord . "<br />");
		}
		$nrgenstr= (string) $tabnr . ".";
//-- print("==========next item:".$tabnr.":".$key.":".$nrgenstr.":".$tabblad.":<br />");

		$maxgen= fill_in($tabnr,$key,$value,$level,$nrgenstr,"","",$tabblad);
		$maxperson1=1;
		$personkey1[1]= $key;
		$famkey1[1]="";
		$fatherkey1[1]= "";
		$motherkey1[1]= "";
		$maxgen= fill_in_array($maxperson1,$personkey1,$famkey1,$fatherkey1,$motherkey1,$level);
		$j++;
	}
}

function getmylist()
{
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
//-- print "start getmylist<br />";
		$mytype=	$individual["mytype"];
		$mylevel=	$individual["mylevel"];
		$mygennum=	$individual["mygennum"];
		$mykey=	$individual["mykey"];
		$myfam=	$individual["myfam"];
		$myfather=	$individual["myfather"];
		$mymother=	$individual["mymother"];
		$mytabblad=	$individual["mytabblad"];
}


function putmylist()
{
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
global $refrecord;
global $tabname, $begintab;
//-- print "start putmylist<br />";
		$individual["mytype"]=		$mytype;
		$individual["mylevel"]=		$mylevel;
		$individual["mygennum"]=	$mygennum;
		$individual["mykey"]=		$mykey;
		$individual["myfam"]=		$myfam;
		$individual["myfather"]=	$myfather;
		$individual["mymother"]=	$mymother;
		$individual["mytabblad"]=	$mytabblad;
		$mylist[$myrecord]= $individual;
		$refrecord["$mykey"]= $myrecord;
 		$tabname[$myrecord]= $mytabblad;
//--	print("I2473===".$mykey.":".$myrecord."<br />");
//--		print "<pre>";
//--		printf ("%4s,%10s,%2s,%5s,%30s,%6s,%6s,%6s,%6s",$myrecord,$mytabblad,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother);
//--		print "</pre>";
}

//--	======================= following routines for saving 'roots' ============================

function get_patriarch_list()
{
//-- save the items in the database
global $ct,$patriarchlist,$patriarchalpha;

//-- print "start roots2database<br />";
	global $GEDCOM,$INDEX_DIRECTORY, $FP, $pgv_lang;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_patriarch.php";
//--	fclose($FP);
	$FP = fopen($indexfile, "r");
//--	fwrite($FP, "<?php\r\n\$indilist = array();\r\n\$famlist = array();\r\n\$sourcelist = array();\r\n\$otherlist = array();\r\n\r\n");
	if (!$FP) {
		print "<font class=\"error\">".$pgv_lang["unable_to_create_index"]."</font>";
		exit;
	}

	$fcontents = fread($FP, filesize($indexfile));
	fclose($FP);
	$lists = unserialize($fcontents);
	unset($fcontents);
	$patriarchlist = $lists["patriarchlist"];
	$patriarchalpha = $lists["patriarchalpha"];
}



//--	======================= following routines for creating EXCEL database ============================

//-- function to print a more complete date
function get_number_date($datestr)
{
global $pgv_lang, $DATE_FORMAT, $LANGUAGE, $USE_HEBREW_DATES;
//-- print "start get-number_date<br />";
	$monthtonum["jan"] = 1;
	$monthtonum["feb"] = 2;
	$monthtonum["mar"] = 3;
	$monthtonum["apr"] = 4;
	$monthtonum["may"] = 5;
	$monthtonum["jun"] = 6;
	$monthtonum["jul"] = 7;
	$monthtonum["aug"] = 8;
	$monthtonum["sep"] = 9;
	$monthtonum["oct"] = 10;
	$monthtonum["nov"] = 11;
	$monthtonum["dec"] = 12;
	$monthtonum["abt"] = 13;
	$monthtonum["bef"] = 14;
	$monthtonum["aft"] = 15;
	$hstr= "xx";
	$datestrh= $datestr;
if ($datestr !== "")
{
	$ct = preg_match_all("/(\d{1,2})?\s?([a-zA-Z]{3})?\s?(\d{4})/", $datestr, $match, PREG_SET_ORDER);
//--print ("==preg_match :"  . $ct . "::" . $match[0][0] . "::" . $match[0][1] . "::" . $match[0][2] . "::" . $match[0][3] . "<br />");
	for($i=0; $i<$ct; $i++)
	{
		$pos1 = strpos($datestr, $match[$i][0]);
		$pos2 = $pos1 + strlen($match[$i][0]);
		$dstr_beg = trim(strtolower(substr($datestr, 0, $pos1)));
//--print("==preg_match_loop: " . $i . "::" . $pos1 . "::" . $pos2 . "::" . $dstr_beg . "::" . $match[$i][0] . "::" . $match[$i][1] . "<br />");
		if ($dstr_beg == "abt") {$hstr= "ca";};
		if ($dstr_beg == "aft") {$hstr= "gt";};
		if ($dstr_beg == "bef") {$hstr= "lt";};
		$dstr_end = substr($datestr, $pos2);
		$day = trim($match[$i][1]);
		$month = trim(strtolower($match[$i][2]));
		if ($month == "abt") {$hstr= "ca"; $month= ""; $day= "";};
		if ($month == "bef") {$hstr= "lt"; $month= ""; $day= "";};
		if ($month == "aft") {$hstr= "gt"; $month= ""; $day= "";};
		if ($day == "") {$day= $hstr;};
		if ($month == "") {$month= $hstr;} else {$month = $monthtonum[$month];};
		$year = $match[$i][3];
//-- check if day and month are in range (are they given) and check year for a non-digit
		$datestr=sprintf("%02s-%02s-%04s",$day,$month,$year);
//--	print("==result date:". $datestrh . ":" .$day.":".$month.":".$year.":".$datestr."<br />");
	}

//--	$array_short = array;
//--	$array_short=("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec",
//--"abt", "aft", "and", "bef", "bet", "cal", "est", "from", "int", "to");
//--	foreach($array_short as $value) {
//--		$datestr = preg_replace("/$value(\W)/i", $pgv_lang[$value]."\$1", $datestr);
//--	}
}
	return $datestr;
}
//--regel 724
function stringinfo($indirec,$lookfor)
//look for a starting string in the gedcom record of a person
//then take the stripped comment
{
//-- print "start stringinfo<br />";
global $match1,$match2,$usedinitials;
	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/".$lookfor." (.*)/", $birthrec, $match1);
			if ($dct < 1)
			{	$match1[1]="";
//-- family treemaker gives a name with no fill and than a continuation as PLAC
				$dct2 = preg_match("/2 PLAC (.*)/", $birthrec, $match1);
				if ($dct2 < 1) {$match1[1]="";}
			}
//-- print("stringinfo:".$dct.":".$lookfor.":".$birthrec.":".$match1[1].":". $match1[2] .":<br />");
			$match1[1]= trim($match1[1]);
			return true;
		}
	else 	{	return false;}
}

function stringevent($indirec,$lookfor)
//look for a starting string in the gedcom record of a person
//then take the stripped comment
{
//-- print "start stringevent<br />";
global $match1,$match2,$usedinitials;
	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
//--first 4.3.3	$dct = preg_match_all("/".$lookfor."(.*)/", $indirec, $match1,PREG_SET_OFFSET,$match2);
			$dct = preg_match_all("/".$lookfor."(.*)/", $indirec, $match1,PREG_SET_OFFSET);
			if ($dct < 1)
			{	$match1[1]="";
//-- family treemaker gives a name with no fill and that a continuation as PLAC
//--				$dct2 = preg_match("/2 PLAC (.*)/", $birthrec, $match1);
//--				if ($dct2 < 1) {$match1[1]="";}
			}
print("stringinfo :".$dct.":".$lookfor.":".$indirec .":<br />");
print("stringinfo1:".$dct.":".$match1[0][0].":". $match2[0] .":<br />");
print("stringinfo2:".$dct.":".$match1[1][0].":". $match2[1] .":<br />");
print("stringinfo3:".$dct.":".$match1[2][0].":". $match2[2] .":<br />");
print("stringinfo4:".$dct.":".$match1[3][0].":". $match2[3] .":<br />");
			$match1[1][0]= trim($match1[1][0]);
			return true;
		}
	else 	{	return false;}
}


function formfile($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and PLACE variables
{
//-- print "start dateplace<br />";
global $match1,$match2,$usedinitials;

	$objerec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($objerec!==false)
		{
			$dct = preg_match("/2 FORM (.*)/", $objerec, $match1);
//-- if ($dct > 0) {print("birthrec + date" . $objerec . ":::" . $match1[1] . "<br />");};
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 FILE (.*)/", $objerec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}

function dateplace($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and PLACE variables
{
//-- print "start dateplace<br />";
global $match1,$match2,$usedinitials;

	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/2 DATE (.*)/", $birthrec, $match1);
//-- if ($dct > 0) {print("birthrec + date" . $birthrec . ":::" . $match1[1] . "<br />");};
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 PLAC (.*)/", $birthrec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}

function datenote($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and NOTE variables
{
//--print "start datenote<br />";
global $match1,$match2,$usedinitials;

	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/2 DATE (.*)/", $birthrec, $match1);
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 NOTE (.*)/", $birthrec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}
// end datenote

//-- regel 850
function getnameitem($namen)
//-- get the different positions of the name part
//-- take care: Mary/Anna Groot/ is non conformant and should be Mary Anna/Groot/
{
//-- print "start getnameitem<br />";
global $match1,$match2,$used ;

	$initialt= "";
	$strpos1 = strpos($namen, ",");
//--	print "namen:" . $namen . ":" . $strpos1 . "==<br>";
	if ($strpos1 !== false)
	{
		$strpos2 = strpos($namen,",",$strpos1+1);
		if ($strpos2 > 0)
		{
			$tussen= trim(substr($namen,$strpos1+1,$strpos2-$strpos1-1));
			$strpos1= $strpos2;
			$initialt= substr($tussen,0,1);
		}
		if ($strpos1==0)
		{	$surname="";}
		else
		{	$surname= substr($namen,0,$strpos1);}
	}
	else {return false;};

	$initials="";
	$rest= trim(substr($namen,$strpos1+1));
	$birthname= $rest;
	while (strlen($rest) > 0)
	{	$rest01= substr($rest,0,1);
		$initials= $initials . $rest01;
		$strpos2= strpos($rest," ");
		if ($strpos2 > 0) {$rest= trim(substr($rest,$strpos2+1));} else {$rest= "";}
	}
//--	print ("naamontleding: " . $namen .":" . 	$surname .":" . $birthname .":" . $tussen . ":<br />");
	$match1[1]= trim($surname);
	$match1[2]= trim($birthname);
	$rest01= substr($surname,0,1);
	if ($rest01 == "(") {$rest01= "U";};
//--	prevent ( from (Unknown) to be in the initials.
	$match1[5]= trim($initials . $initialt . $rest01);
	return true;
}

function addinitials($str1,$str2)
{
global $match1,$match2,$usedinitials;

	$strnew= $str1.$str2;
	$strnew1= $strnew;
	$i= 0;
	$stradd= "";
//--$xltype= gettype($usedinitials[$strnew]);
//--print("addinitials:".$xltype."<br />");
//--if ($str1 == "MB")
//--{print ("addinit: " .$xltype.":" . $strnew . "<br />");};
	while (gettype($usedinitials["$strnew"]) !== "NULL")
	{	$stradd= substr("abcdefghijklmnopqrstuvwxyz",$i,1);
		$strnew= $str1 . $stradd . $str2;
		$i++;
	}
	$usedinitials["$strnew"]= 2;
//--if ($str1 == "MB")
//--{print ("addinit: " . $strnew . " + " . $i . "+" . $stradd . "<br />");};
	return $stradd;
}


function slkvalue_newrow($nr,$myval)
{
global $xval_slk,$FILE_slk;
	fwrite($FILE_slk,"C;Y".$nr.";X1;K".$myval."\n");
	$xval_slk=1;
}

function slkvalue($myval)
{
global $xval_slk,$FILE_slk;
	$xval_slk++;
	if ($myval != "") {fwrite($FILE_slk,"C;X".$xval_slk.";K".$myval."\n");};
}

function slkvaluenr($nr,$myval)
{
global $xval_slk,$FILE_slk;
	$xval_slk=$nr;
	if ($myval != "") {fwrite($FILE_slk,"C;X".$xval_slk.";K".$myval."\n");};
}

function slkformula($mystr)
{
global $xval_slk,$FILE_slk;
	$xval_slk++;
//--	print("slkformula:".$mystr."<br />");
	fwrite($FILE_slk,"C;X".$xval_slk.";K12345;".$mystr."\n");
}

function slkformulanr($nr,$mystr)
{
global $xval_slk,$FILE_slk;
	$xval_slk=$nr;
//--	print("slkformula:".$mystr."<br />");
	fwrite($FILE_slk,"C;X".$xval_slk.";K12345;".$mystr."\n");
}

function slkref($verta,$vertb,$hora,$horb)
{
global $tabname, $begintab;
//--	current position is (verta,hora) related position is (vertb,horb)
//--	make a reference to a different tab
//--	the expression is E<reference>
//--	reference= <tab><Rowref><Columnref>
//--	tab= <tabname>!
//--	Rowref= R[reldif on row] (if reldif=0 then just R
//--	Columnref= C[reldif on Column] (if reldif =0 then just C
//--	C[i]R[j] means relative CiRj absolute adresses
//--	so mytab!R[-2]C[+2] on pos 4,2 means take value of pos 2,4 of tab "mytab"

	$strtab="";
	$strhor= "C";
	$strvert= "R";
	$taba= $tabname[$verta]; $tabb= $tabname[$vertb];
//--	printf ("slkref,%5s,%5s,%5s,%5s,%10s,%10s,%10s,%10s,%10s,%10s<br />",$verta,$vertb,$hora,$horb,$taba,$tabb,$tabname[$verta],$tabname[$vertb],$begintab[$taba],$begintab[$tabb]);

//--	There is obviously a difference in assigning values to a different tab in the same file and a relation
//--	to a different file. in the latter case an absolute (instead of relative) address should be used.
//--	Although it looks like positive values can be used as relative addresses.
	if ($taba !== $tabb)
	{
//--	printf ("slkref,%5s,%5s,%5s,%5s,%10s,%10s,%10s,%10s,%10s,%10s<br />",$verta,$vertb,$hora,$horb,$taba,$tabb,$tabname[$verta],$tabname[$vertb],$begintab[$taba],$begintab[$tabb]);
		$strtab= "[phpgedview.xls]" . $tabb . "!";
		$strhor= "C" . $horb;
		$strvert= "R" . ($vertb- $begintab[$tabb] + 1);
//--	2 is offset. 1 for commentline and 1 because excel strats with 1 instead of 0
	} else
	{
		$hor= $horb - $hora;
		$vert= $vertb - $verta;
		if ($hor !== 0) {$strhor= "C[" . $hor . "]";}
		if ($vert !== 0) {$strvert= "R[" . $vert . "]";}
	}
//--	print("slkref:".$vert.":".$hor.":".$strtab.$strvert.$strhor."<br />");
	slkformula("E" . $strtab . $strvert . $strhor);
}

function slkstr_newrow($nr,$mystr)
{
global $xval_slk,$FILE_slk;
	fwrite($FILE_slk,"C;Y".$nr.";X1;K\"".$mystr."\"\n");
	$xval_slk=1;
}

function slkstr($mystr)
{
global $xval_slk,$FILE_slk;
	$xval_slk++;
	if ($mystr != "")
		{fwrite($FILE_slk,"C;X".$xval_slk.";K\"".$mystr."\"\n");}
}

function slkstrnr($nr,$mystr)
{
global $xval_slk,$FILE_slk;
	$xval_slk=$nr;
	if ($mystr != "")
		{fwrite($FILE_slk,"C;X".$xval_slk.";K\"".$mystr."\"\n");}
}

function open_slk($file)
{
global $xval_slk,$FILE_slk;
//-- print "start open_slk<br />";
//--	open CSV file to put the data in
	($FILE_slk = fopen($file,"w")) or die ("error on opening $file");

	fwrite($FILE_slk, "ID;PWXL;N;E"."\n");
	fwrite($FILE_slk, "P;PGeneral"."\n");
	fwrite($FILE_slk, "F;P0;DG0G8;M255"."\n");
	fwrite($FILE_slk, "O;L;D;V0;K47;G100 0.01"."\n");
//--	prefix text
//--	and now the header


	slkstr_newrow(1,"type(1=person,2=fam)");
	slkstr("RESN");
	slkstr("GEN-level");
	slkstr("GEN-generation");
	slkstr("GEN-reference");
	slkstr("NAME-SURN");
	slkstr("GEN-Initials");
	slkstr("NAME-BIRTH");
	slkstr("NAME-NICK");
	slkstr("SEX");
	slkstr("CHR-DATE");
	slkstr("BIRT/MARR-DATE");
	slkstr("DEAT-DATE");
	slkstr("GEN-FATHERreference");
	slkstr("GEN-MOTHERreference");
	slkstr("BIRT/MARR-PLAC");
	slkstr("DEAT/MARR-PLAC");
	slkstr("BIRT/MARR-WITN");
	slkstr("DEAT/MARR-WITN");
	slkstr("INFO");
	slkstr("OCCU");
	slkstr("REFN");
	slkstr("NOTE");
	slkstr("CHAN-DATE");
	slkstr("CHAN-SOUR");
	slkstr("SOUR");
	return $FILE_slk;
}

function close_slk($FILE_slk)
{
//-- print "start close_slk<br />";
	fwrite($FILE_slk, "E"."\n");
	fclose($FILE_slk);
}

function splitindilines($lct,$indilines)
{
//--	Split the individual lines of a Individual, family or source record and take all unique combinations together
//--	In this way you combine every level together with the last value. 
//--	I.e. 4 lines with 0 @I11@ INDI; 1 NAME Jan /Kostelijk/; 2 PLAC Broek; 3 NOTE okee; 2 DATE 20 OCT 2000 
//--	will result in 4 coded results
//--	@Ix@:NAME= Jan /Kostelijk/; @Ix@:PLAC=Broek; @Ix@:PLAC:NOTE=okee and @Ix@:DATE=20 OCT 2000
//--	This way you gather combinations line DEAT/DATE, DEAT/PLACE, OCCU, NOTE and other lines in one array element
//--	It makes it easier to file the defined slk columns with all (i.e. more than one occupation)

$arcodes= array();
$arcontent= array();
settype($nr,'integer');
settype($naam, 'string');

//--print ("start ontleden:".$lct."<br />");
	$antlijnen=0;
	$lastnr= -1;
	$totstr= "";
	$nrbew= 0;
	$lastcontent= "";
	$antlijnen= 0;
$i1=0;
while ($i1 <= $lct)
{
	$line = "   ";
	if ($i1 < $lct) {$line= $indilines[$i1];}
	$i1= $i1+1;
	$antlijnen=$antlijnen+1;
//--	print ("de GEDCOM regel=".$line."<br />");
#	@elementen= map{split separator,$_} $line;
	$elementen= explode(" ",$line);
//--	print ( " 0:".$elementen[0]." 1:".$elementen[1]." 2:".$elementen[2]. "<br />");

	$nr= $elementen[inp_nr];

//--	if ($nr <= $lastnr)================ was vroeger zo. weet niet meer waarom===== nu wel. bedoeld om lower en 
//--	=========== upper levels te combineren dus bijv: 1 SOUR JAN en 2 Page 20 te combineren
	if (($lastnr > 0) and ($nr <= $lastnr))
	{
		if ($lastcontent != "")
		{
//--		print ("nr en lastnr".$nr.":".$lastnr.":line:".$antlijnen." naam:".$totstr." content:". $lastcontent."<br />");
		$arcodes{$totstr}= $totstr;
		if (isset($arcontent{$totstr}))
			{	$arcontent{$totstr} .= "," . trim($lastcontent);
#				print ("toegevoegde waarde");
			}
			else
			{	$arcontent{$totstr}= trim($lastcontent);};
		$lastcontent= "";
		}
	};

	$naam= trim($elementen[inp_naam]);
#	if ($naam == 'CONC') {$naam= 'CONT';}
//--	Just get the information from the second space in $content (after level and keyword. I.e. 1 NAME John)
	$i= strpos($line, " ",1);
	$j= strpos($line, " ",$i+1);
	$content= trim(substr($line,$j));
	if ($j < 1) {$content= "";};

#print ("eerste letter:" . substr($naam,0,1) . "<br />");
//-- Check for a reference. Just translate the number to x
	if (substr($naam,0,1) == '@')
	{
		$naam= substr($naam,0,2) . "x@";
		$content= "";
//--	This can go wrong in cases where the first letter is not I (for INDI), F (for FAM) or S (for SOURCE)
//--	I ignore in those cases the content
//--		print (substr($naam,0,1).".....:".$naam."<br />");
	}

	$mystr= $naam;
	$strings[$nr]= $mystr;
	$nrm1= $nr-1;
	$totstr= $mystr;
	if ($nr > 0)
		{	$totstr= $totstrings[$nrm1]. ":" . $mystr;
//--			print ("nr>0:<br />");
		};

	$totstrings[$nr]= $totstr;

	$lastnr= $nr;
	if ($content != "")
		{	
			if ($lastcontent != "")
				{$lastcontent= $lastcontent . indidelimeter . $content;}
			else 
				{$lastcontent= $content;}
		}

//--	print ("nr,nrm1,content,lastcontent,totstrings:".$nr.":".$nrm1.":".$content.":".$lastcontent."=0:".$strings[0]."=1:".$strings[1]."=2:".$strings[2]."=3:".$strings[3]."=tot=".$totstrings[$nr]."==".$totstr."<br />");

}
// end of main loop for number of lines

global $nrcode, $arcode, $ar1content;
		$ant_namen= 0; $nrcode=0;
		foreach ($arcodes as $ai)
		{
			$ant_namen= $ant_namen+1; $str= $ai;
//--			print ("nr:".$ant_namen." code=". $str. " content=".$arcontent{$str}."<br />");
			$nrcode= $nrcode + 1;
			$arcode[$nrcode]= $str;
			$ar1content{$str}= trim($arcontent{$str});
		}
//--	Deliver in $nrcode(nr of codes), $arcode (keywords) and $ar1content (contents)
}

function indi2elements($indirec)
{
global $nrcode, $arcode, $ar1content, $arelement;
global $defs;

	$nrcode=0;
	$arcode= (array) "";
	$ar1content= (array) "";
	$arelement= (array) "";

//-- in case you want prints for debugging
	$indilines = split("\n", $indirec);
//--  find the number of lines in the individuals record
	$lct = count($indilines);
#	print ($indirec."<br />");
#	print ("mytype,mykey,myfam,lct:".$mytype.":".$mykey.":".$myfam.":".$lct.":".$indirec[0]."<br />");
#	$i1=0;
#	while($i1<$lct-1)
#	{
#		print ($indilines[$i1]."<br />");
#		$i1++;
#	}
	splitindilines($lct,$indilines);
	$i1=0;
	$notes= "";
	$notes_none= "";
	while($i1<$nrcode)
	{
		$i1++;
		$str= $arcode[$i1];
		$check= $defs{$str};
		if ($check == '')
			{print ("new code. Ask to implement:".$str."<br />"); $check= pos_none; $defs{$str}= pos_none;}
		if ($check == pos_none)
		{	if ($notes_none == "") 
				{$notes_none= $str . ":". $ar1content{$str};}
			else	{$notes_none= $notes_none . "," . $str . ":". $ar1content{$str};}
		}
		if ($check > 0) 
		{	$archeck= $arelement[$check]; $arcontent= "";
			if (isset($ar1content{$str})) {$arcontent= trim($ar1content{$str});}
//--	There can be situations where diferent codes transform to 1 element (i.e. @Ix@:SOUR and @Ix@:SOUR:PAGE)
			if (isset($archeck) and ($archeck !== ""))
			{	$arelement[$check]= $archeck . "," .  $arcontent;}
			else
			{	$arelement[$check]= $arcontent;}
		}
	}
	$arelement[pos_NOTE]= $arelement[pos_NOTE] . $notes;
	$arelement[pos_none]= $arelement[pos_none] . $notes_none;

}

function roots2excel()
{
//-- print "start roots2excel<br />";
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
//--	$myrecord= number of lines to be created in the excel database
//--	$mylist= array of records ($individual) belonging to individual lines
//--	for every line it contains values for:mytype,mylevel,mygennum,mykey,myfather,mymother,myfam,mytabblad

global $refrecord;
global $match1,$match2,$usedinitials;
global $pgv_lang;
global $xval_slk,$FILE_slk;
global $tabname, $begintab;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;
global $nrcode, $arcode, $ar1content, $arelement;
global $defs;
global $perccounter,$maxcounter;


	$first= 1;
	$oldtabblad= "1";
	$file= $INDEX_DIRECTORY.$GEDCOM. ".slk";
//--	CSV file to put the data in

//--	go in the loop
	$yval_slk=0;
	$i=0;
	while($i<$myrecord)
	{	$i++;
$perccounter++;
my_progress_check($perccounter,$maxcounter,"progress_div2");


		$individual= $mylist["$i"];
		getmylist();
		if ($oldtabblad !== $mytabblad)
		{
			if ($first == 0)
			{
//-- later on enter source2excel
				close_slk($FILE_slk); $first= 1;
			}
			$file=  $INDEX_DIRECTORY.$mytabblad . ".slk";
			if ($first == 1)
			{
				$FILE_slk= open_slk($file);
				$yval_slk=1;
			}
			$first= 0;
 			print ($pgv_lang["slklist_tab"] . $mytabblad . $pgv_lang["slklist_create"] . $file . "<br />");
			$oldtabblad= $mytabblad;
		}
		$absfa= 0; $absmo= 0;
		if ($myfather !== "") {$absfa= $refrecord["$myfather"];}
		if ($mymother !== "") {$absmo= $refrecord["$mymother"];}
if ($absmo == "")
{
//--	print ("refrecord:".$i.":".$mykey.":".$refrecord["$mykey"].":".$myfather.":".$absfa.":".$mymother.":".$absmo."<br />");
}
		$myparent= $myfather; if ($myparent == ""){$myparent= $mymother;}
		if ($mytype == 1) 
			{$namen= get_sortable_name($mykey);} 
		else  {$namen= get_sortable_name($myparent);}
		$person= find_person_record($mykey);
//--	printf ("%2s,%5s,%30s,%6s,%6s,%6s,%6s,%20s<br />",$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$namen);


//-- find all the fact information
	if ($mytype == 1)
	{	$indirec = find_person_record($mykey);}
	else
	{	$indirec= find_family_record($myfam);}

	indi2elements($indirec);
	$arelement[pos_GENlevel]= $mylevel;
	$arelement[pos_GENgen]= $mygennum;

//--	set the privacy element
	$arelement[pos_type]= $mytype;
	if ($arelement[pos_RESN] == "privacy")
		{$arelement[pos_RESN]= "0";}
	else
		{$arelement[pos_RESN]= "1";}

	$arelement[pos_NAME_SURN]= "None";
	if ($mytype == 1)
	{
		$arelement[pos_NAME_BIRT]= "None";
		$arelement[pos_GENinitials]= "NN";
	}
//--	print "getnameitem 1:" . $namen . "<BR>";
	if (getnameitem($namen)!==false)
	{	$surname= $match1[1];
		$birthname= $match1[2];
		$initials= $match1[5];
		$arelement[pos_NAME_SURN]= $match1[1];
		if ($mytype == 1)
		{	$arelement[pos_NAME_BIRT]= $match1[2];
			$arelement[pos_GENinitials]= $match1[5];
		}
	}

if ($mytype == 1)
{
	$arelement[pos_BIRT_DATE]= get_number_date($arelement[pos_BIRT_DATE]);
	$arelement[pos_CHR_DATE]= get_number_date($arelement[pos_CHR_DATE]);
	$arelement[pos_DEAT_DATE]= get_number_date($arelement[pos_DEAT_DATE]);
//--	translate sex (M,F, <blank>) to 1,2 and 3
	if ($arelement[pos_SEX] == "M") {$arelement[pos_SEX]= 1;}
		else
	if ($arelement[pos_SEX] == "F") {$arelement[pos_SEX]= 2;}
		else
		 {$arelement[pos_SEX]= 3;}
//-- print ("sexe=".$match1[1].":".$sex."<br />");


//--	check if the combination of initials and birthdate is unique. if not add a character to initials
	$initials= $arelement[pos_GENinitials];
	$birthdate= $arelement[pos_BIRT_DATE];
	$addchar= addinitials($initials,$birthdate);
	$initials= $initials . $addchar;
	$arelement[pos_GENinitials]= $initials;

}

	$arelement[pos_CHAN_DATE]= get_number_date($arelement[pos_CHAN_DATE]);

if ($mytype == 2)
{
//--	print ("fam record:" . $indirec."<br />");
	$arelement[pos_MARR_DATE]= get_number_date($arelement[pos_MARR_DATE]);
	$arelement[pos_DIV_DATE]= get_number_date($arelement[pos_DIV_DATE]);
}

//--	write the line in SYLK format
//--	notation R[vertikaal]C[horizontal]

//-- regel 1429
	$yval_slk++;
	slkvalue_newrow($yval_slk,$arelement[pos_type]);
	slkstr  ($arelement[pos_RESN]);
if ($mytype == 1)
{	slkstr  ($arelement[pos_GENlevel]);
	slkstr  ($arelement[pos_GENgen]);
	slkformula("ECONCATENATE(RC[+2],TEXT(RC[+7],\"dd-mm-jjjj\"))");
} else
{	slkstr  ("");
	slkstr  ("");
	slkstr  ("");
}
	slkstr  ($arelement[pos_NAME_SURN]);
	slkstr  ($arelement[pos_GENinitials]);
	slkstr  ($arelement[pos_NAME_BIRT]);
$nick_temp= $arelement[pos_NAME_NICK];
if ($nick_temp <> "")
{	
	$s1= strpos($nick_temp,indidelimeter);
	$arelement[pos_NAME_NICK]= substr($nick_temp,$s1+1);
}
	slkstr  ($arelement[pos_NAME_NICK]);
	slkstr  ($arelement[pos_SEX]);
//-- normally this is a value (1=marriage, 0= living together. bu also values like civil and religious are valid and have to be put into namen.pl

if ($mytype == 1)
{
	slkstr  ($arelement[pos_CHR_DATE]);
	slkstr  ($arelement[pos_BIRT_DATE]);
	slkstr  ($arelement[pos_DEAT_DATE]);
//--	next two items are the references to the father and mother records
	if ($myfather != "") {slkref($i,$absfa,pos_FATHERref,pos_GENref);} else {	slkstr  ("");}
	if ($mymother != "") {slkref($i,$absmo,pos_MOTHERref,pos_GENref);} else {	slkstr  ("");}
	slkstr  ($arelement[pos_BIRT_PLAC]);
	slkstr  ($arelement[pos_DEAT_PLAC]);
	slkstr  ($arelement[pos_BIRT_WITN]);
	slkstr  ($arelement[pos_DEAT_WITN]);
} else
{
//--	You even can do without difference in $mytype because the references (i.e. pos_MARR_DATE == pos_BIRT_DATE)are the same
	slkstr  ("");
	slkstr  ($arelement[pos_MARR_DATE]);
	slkstr  ($arelement[pos_DIV_DATE]);
	if ($myfather != "") {slkref($i,$absfa,pos_FATHERref,pos_GENref);} else {	slkstr  ("");}
	if ($mymother != "") {slkref($i,$absmo,pos_MOTHERref,pos_GENref);} else {	slkstr  ("");}
	slkstr  ($arelement[pos_MARR_PLAC]);
	slkstr  ($arelement[pos_DIV_PLAC]);
	slkstr  ($arelement[pos_MARR_WITN]);
	slkstr  ($arelement[pos_DIV_WITN]);
}
	slkstr($arelement[pos_INFO]);
	slkstr($arelement[pos_OCCU]);
	slkstr($arelement[pos_REFN]);
	slkstr($arelement[pos_NOTE]);
	slkstr($arelement[pos_CHAN_DATE]);
	slkstr($arelement[pos_CHAN_NOTE]);
//--print "loop replace<br>\n";
	sour_replace_nr();
	slkstr($arelement[pos_SOUR]);
//--	end of loop
	}
	source2excel($yval_slk);
	close_slk($FILE_slk);
}

function sour_replace_nr()
{	
global $arelement;

	$mystr= $arelement[pos_SOUR];
if ((isset($mystr)) and ($mystr !== ""))
{
//--	print "<br>in loop replace:" . $mystr . ":<br>";
	$p1= strpos($mystr,'@',0); if ($p1 > -1) {$x=0;} else {$p1= -1;}
	$nrlen= strlen($mystr);
	if ($p1 > 0) {$newstr= substr($mystr,0,$p1-1);}
	while ($p1 > -1)
	{	$i= $p1;
		$j= strpos($mystr,'@',$i+1);
		$ij1= $j-$i -1;		
		$key= substr($mystr,$i+1,$ij1);
//--	print "i,j,key,newstr=" . $i . ":" . $j . ":" . $key . "==" . $newstr . ":<br>";
		if ($key !== "")
		{	$source = trim(find_source_record($key));
//--	print "source:" . $source . "<br>";
			$abbrev = trim(substr(get_sub_record(1, "1 ABBR", $source),6));
			$newstr= $newstr . $abbrev;
//--	print "abbr=" . $abbrev . ":<br>";
		}
		if ($mystr[$j+1] == " ") {$mystr[$j+1]= ":";}
		$i= strpos($mystr,'@',$j+1);
		$p1= $i;
		if ($i > -1) {$x=0;} else {$p1= -1; $i= $nrlen;};
		$ij1= $i-$j -1;
		$newstr= $newstr . substr($mystr,$j+1,$ij1);
//--	print "p1,i,j,newstr" . $p1 .":" . $i . ":" . $j . "==" . $newstr . "==<br>";
	}
//--	print "change source: " . $mystr . "==" . $newstr . "==<br>";
	$arelement[pos_SOUR]= trim($newstr);
}
}

function source2excel($yval_slk)
{
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
global $nrcode, $arcode, $ar1content, $arelement;
global $defs;

//--	$myrecord= number of lines to be created in the excel database
//--	$mylist= array of records ($individual) belonging to individual lines
//--	for every line it contains values for:mytype,mylevel,mygennum,mykey,myfather,mymother,myfam,mytabblad


global $refrecord;
global $match1,$match2,$usedinitials;
global $pgv_lang;
global $xval_slk,$FILE_slk;
global $tabname, $begintab;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;
global $perccounter, $maxcounter;

//-- there can also be source records in the gedcom file. Normally they look like:
//--	0 SOUR @S<integer>@
//--	1 TITLE <title>
//--	1 REPO
//--	2 CALN
//--	3 MEDI BOOK
//--	So far not implemented
//-- overall info
//--	$sourcelist = get_source_list();
//--	sourcelist al eerder ingelezen
	uasort($sourcelist, "itemsort");
	$ct = count($sourcelist);

$i=0;

foreach ($sourcelist as $key => $value) 
{
$perccounter++;
my_progress_check($perccounter,$maxcounter,"progress_div2");

	$source = trim(find_source_record($key));
//--print "source".$source."\n";
	indi2elements($source);

//fill out on the slk file
// until now we supply every element. That can be less (just give the x-value and the element)

	$yval_slk++;
	slkvalue_newrow($yval_slk,3);
	slkstrnr(pos_source_PUBL,$arelement[pos_source_PUBL]);
	slkstrnr(pos_source_ABBR,$arelement[pos_source_ABBR]);
	slkstrnr(pos_source_AUTH,$arelement[pos_source_AUTH]);
	$arelement[pos_source_DATE]= get_number_date($arelement[pos_source_DATE]);
	slkstrnr(pos_source_DATE,$arelement[pos_source_DATE]);
	slkstrnr(pos_source_TITL,$arelement[pos_source_TITL]);

	$i++;
	}
}

function maakromein($nr,$str)
{
//--	print "start maakromein<br />";
global $romeins;
		$romeins[$nr]= $str;
//--	print ("romeins:".$nr.":".$romeins[$i]."<br />");
}

function maakdefs($str,$ok)
{
//-- function to fill default gedcom combinations
global $defs;
	$defs{$str}= $ok;
//-- print ("defs:".$ok.":".$str."<br />");
}


//--	========= start of main program =========
// -- build index array in mem
// -- array of names
$patriarchlist = array();
$patriarchalpha = array();
$myfamlist= array();
$myindilist= array();

global $ct,$patriarchlist,$patriarchalpha;


//-- default start of program
$tabbladnr= array();
$tabbladname= array();
$tabbladnrreverse= array();
$tabname= array();
$begintab= array();
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;

$usedinitials= array();
global $match1,$match2,$usedinitials;

$romeins= array();
$pidused= array();
$levelgen= array();
$nrgen=array();
$mylist= array();

$individual= array();
$refrecord= array();


//-- ========================================================================================
//--global $romeins;

print_my_time("start slklist");
	maakromein(1,"I"); maakromein(2,"II"); maakromein(3,"III"); maakromein(4,"IV"); maakromein(5,"V");
	maakromein(6,"VI"); maakromein(7,"VII"); maakromein(8,"VIII"); maakromein(9,"IX"); maakromein(10,"X");
	maakromein(11,"XI"); maakromein(12,"XII"); maakromein(13,"XIII"); maakromein(14,"XIV"); maakromein(15,"XV");
	maakromein(16,"XVI"); maakromein(17,"XVII"); maakromein(18,"XVIII"); maakromein(19,"XIX"); maakromein(20,"XX");
#regel 1511
$defs= array();
global $defs;

//--	Put all known GEDCOM combinations in an array
//--	first parameter is: name combination. 
//--	second parameter is: nr to store in (now 1 = ok and pos_none= last). negative is forget.
	maakdefs("@Ix@:RESN",pos_RESN);
	maakdefs("@Ix@:NAME",-1); maakdefs("@Ix@:NAME:NOTE",pos_none); maakdefs("@Ix@:NAME:NOTE:CONC",pos_none);maakdefs("@Ix@:NAME:NOTE:CONT",pos_none);
	maakdefs("@Ix@:NAME:NICK",pos_NAME_NICK);
	maakdefs("@Ix@:NAME:SOUR",pos_none);
	maakdefs("@Ix@:SEX",pos_SEX);
	maakdefs("@Ix@:RELI:PLAC",pos_none);
	maakdefs("@Ix@:CHR:DATE",pos_CHR_DATE); maakdefs("@Ix@:CHR:PLAC",pos_none);
	maakdefs("@Ix@:CHR:RELI",pos_none); maakdefs("@Ix@:CHR:WITN",pos_none);
	maakdefs("@Ix@:CHR:NOTE",pos_none); maakdefs("@Ix@:CHR:NOTE:CONC",pos_none);
	maakdefs("@Ix@:BIRT:DATE",pos_BIRT_DATE); maakdefs("@Ix@:BIRT:PLAC",pos_BIRT_PLAC); maakdefs("@Ix@:BIRT:WITN",pos_BIRT_WITN);
	maakdefs("@Ix@:BIRT:NOTE",pos_none); maakdefs("@Ix@:BIRT:NOTE:CONT",pos_none);
	maakdefs("@Ix@:BIRT:TYPE",pos_none);
	maakdefs("@Ix@:DEAT:DATE",pos_DEAT_DATE); maakdefs("@Ix@:DEAT:PLAC",pos_DEAT_PLAC); maakdefs("@Ix@:DEAT:WITN",pos_DEAT_WITN);
	maakdefs("@Ix@:DEAT:NOTE",pos_none); maakdefs("@Ix@:DEAT:NOTE:CONT",pos_none); maakdefs("@Ix@:DEAT:NOTE:CONC",pos_none);
	maakdefs("@Ix@:DEAT:TYPE",pos_none);
	maakdefs("@Ix@:DEAT:CAUS",pos_none);
	maakdefs("@Ix@:BURI:DATE",pos_none); maakdefs("@Ix@:BURI:PLAC",pos_none);
	maakdefs("@Ix@:BURI:NOTE",pos_none); maakdefs("@Ix@:BURI:NOTE:CONT",pos_none);
	maakdefs("@Ix@:BURI:TYPE",pos_none);
	maakdefs("@Ix@:CHAN:DATE",pos_CHAN_DATE); maakdefs("@Ix@:CHAN:NOTE",pos_CHAN_NOTE);
	maakdefs("@Ix@:RESI:DATE",pos_none); maakdefs("@Ix@:RESI:ROLE",pos_none);
	maakdefs("@Ix@:_ORIG:DATE",pos_none); maakdefs("@Ix@:_ORIG:PLAC",pos_none);
	maakdefs("@Ix@:REFN",pos_REFN);
	maakdefs("@Ix@:ADDR",pos_none);
	maakdefs("@Ix@:SOUR",pos_SOUR); maakdefs("@Ix@:SOUR:PAGE",pos_SOUR);
	maakdefs("@Ix@:OCCU",pos_OCCU); maakdefs("@Ix@:OCCU:PLAC",pos_none);
	maakdefs("@Ix@:EVEN",pos_none); maakdefs("@Ix@:EVEN:TYPE",pos_none); maakdefs("@Ix@:EVEN:PLAC",pos_none);
//--	maakdefs("@Ix@:PICT",pos_INFO);
	maakdefs("@Ix@:OBJE",-1); maakdefs("@Ix@:OBJE:FORM",-1); maakdefs("@Ix@:OBJE:FILE",pos_INFO);
	maakdefs("@Ix@:FAMC",-1);
	maakdefs("@Ix@:FAMS",-1);
	maakdefs("@Ix@:NOTE",pos_NOTE); maakdefs("@Ix@:NOTE:CONT",pos_NOTE); maakdefs("@Ix@:NOTE:CONC",pos_NOTE);
	maakdefs("@Fx@:MARR:DATE",pos_MARR_DATE); maakdefs("@Fx@:MARR:PLAC",pos_MARR_PLAC); maakdefs("@Fx@:MARR:WITN",pos_MARR_WITN);
	maakdefs("@Fx@:MARR:TYPE",pos_MARR_TYPE);
	maakdefs("@Fx@:MARR:RELI",pos_none);
	maakdefs("@Fx@:MARR:NOTE",pos_none);maakdefs("@Fx@:MARR:NOTE:CONT",pos_none);  maakdefs("@Fx@:MARR:NOTE:CONC",pos_none);
	maakdefs("@Fx@:MARB:DATE",pos_MARR_DATE); maakdefs("@Fx@:MARB:PLAC",pos_MARR_PLAC); maakdefs("@Fx@:MARB:WITN",pos_MARR_WITN);
	maakdefs("@Fx@:MARB:TYPE",pos_MARR_TYPE);
	maakdefs("@Fx@:MARS:DATE",pos_MARR_DATE); maakdefs("@Fx@:MARS:PLAC",pos_MARR_PLAC); maakdefs("@Fx@:MARS:WITN",pos_MARR_WITN);
	maakdefs("@Fx@:MARS:TYPE",pos_MARR_TYPE);
	maakdefs("@Fx@:WITN",pos_MARR_WITN);
	maakdefs("@Fx@:DIV",pos_none); 
	maakdefs("@Fx@:DIV:DATE",pos_DIV_DATE); maakdefs("@Fx@:DIV:PLAC",pos_DIV_PLAC); maakdefs("@Fx@:DIV:WITN",pos_DIV_WITN);
	maakdefs("@Fx@:CHAN:DATE",pos_CHAN_DATE); maakdefs("@Fx@:CHAN:NOTE",pos_CHAN_NOTE);
	maakdefs("@Fx@:TYPE",pos_none);
	maakdefs("@Fx@:_STRT:DATE",pos_none);
	maakdefs("@Fx@:HUSB",-1); maakdefs("@Fx@:WIFE",-1);
	maakdefs("@Fx@:CHIL",-1); maakdefs("@Fx@:CHIL:ADOP",pos_none);
	maakdefs("@Fx@:OBJE",-1); maakdefs("@Fx@:OBJE:FORM",-1); maakdefs("@Fx@:OBJE:FILE",pos_INFO);
	maakdefs("@Fx@:REFN",pos_REFN);
	maakdefs("@Fx@:SOUR",pos_SOUR); maakdefs("@Fx@:SOUR:PAGE",pos_SOUR);
	maakdefs("@Fx@:EVEN",pos_none); maakdefs("@Fx@:EVEN:TYPE",pos_none); maakdefs("@Fx@:EVEN:PLAC",pos_none);
	maakdefs("@Fx@:NOTE",pos_NOTE); maakdefs("@Fx@:NOTE:CONT",pos_NOTE); maakdefs("@Fx@:NOTE:CONC",pos_NOTE);
	maakdefs("@Sx@:PUBL",pos_source_PUBL);
	maakdefs("@Sx@:ABBR",pos_source_ABBR);
	maakdefs("@Sx@:AUTH",pos_source_AUTH);
	maakdefs("@Sx@:DATE",pos_source_DATE);
	maakdefs("@Sx@:TITL",pos_source_TITL);


global $nrcode, $arcode, $ar1content;
global $perccounter, $maxcounter;
$arcode = array();
$arcontent= array();

	get_patriarch_list();
	$myrecord= 0;
	$ct= count($patriarchlist);
	$myindilist= get_indi_list();
	$nrpers= count($myindilist);
	$sourcelist = get_source_list();
	uasort($sourcelist, "itemsort");
	$ct1 = count($sourcelist);
//--	sort_patriarch_list();

//--print ("aantal namen=".$ct."<br />");
print_my_time("start roots2number");
	$maxcounter= $nrpers;
	$perccounter= 0;
	my_progress_init($pgv_lang["slklist_progress1"],"progress_div1");
	roots2number();
	my_progress_complete ($pgv_lang["slklist_lezen"],"progress_div1");

	error_reporting(E_ALL ^E_NOTICE);
print_my_time("start roots2exel");
	$maxcounter= $ct1+ $myrecord;
	$perccounter= 0;
	my_progress_init($pgv_lang["slklist_progress2"],"progress_div2");
	roots2excel();
	my_progress_complete ($pgv_lang["slklist_maken"],"progress_div2");
//-- next line is now called from roots2exel
//--	source2excel();
print_my_time("end slklist");

print "\n\t\t</td>\n\t\t</tr>\n\t</table></center>";
print "<br />";
print_footer();

?>
