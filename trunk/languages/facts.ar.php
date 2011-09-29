<?php
/*=================================================
   charset=utf-8
   Projekt: phpGedView
   Datei: facts.ar.php
   Autor: John Finlay
   Translation:	 
   Comments:	Arabic Language Facts file for PHPGedView.
   Change Log:	See LANG_CHANGELOG.txt
		2004-11-29 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: facts.ar.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
 
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
        print "You cannot access a language file directly.";
        exit;
}

$factarray["AGE"]	= "عمر";
$factarray["ALIA"]	= "االأسم الأخر";
$factarray["ANCE"]	= "النسب";
$factarray["AUTH"]	= "الكاتب";
$factarray["BIRT"]	= "مولود";
$factarray["BURI"]	= "مدفون";
$factarray["CAUS"]	= "سبب الموت";
$factarray["CEME"]  = "المقبره";
$factarray["CITY"]	= "مدينه";
$factarray["CTRY"]	= "بلد";
$factarray["DATE"]	= "اليوم";
$factarray["DEAT"]	= "مات";
$factarray["DIV"]	= "طلاق";
$factarray["ENGA"]	= "خطوبه";
$factarray["FAM"]	= "اسره";
$factarray["GIVN"]	= "اِسم";
$factarray["MARR"]	= "زواج";
$factarray["NAME"]	= "أسم";
$factarray["PEDI"]	= "نسب";
$factarray["PLAC"]	= "منطغه";
$factarray["PHON"]	= "هاتف";
$factarray["RELI"]	= "دين";
$factarray["SEX"]	= "جنس";
$factarray["SURN"]	= "إسم العائلة";
$factarray["_EMAIL"]	= "عنوان البريد الاليكترونى";
$factarray["TYPE"]	= "نوع";
$factarray["TIME"]	= "الوقت";
$factarray["_DEG"]	= "شهاده";
$factarray["MAP"] = "خريطه";
$factarray["URL"] = "موقع عبر الانترنت";
$factarray["_EYEC"]	= "القوات المسلحه";
$factarray["_HAIR"]	= "لون الشعر";
$factarray["_HEIG"]	= "الطول";
$factarray["_MILI"]	= "تجنيد";

if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.ar.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.ar.extra.php";
$factarray["ADDR"]	= "العنوان";

?>