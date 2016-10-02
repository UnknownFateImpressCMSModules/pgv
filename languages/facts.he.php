<?php

/*=================================================

              charset=utf-8

	Project:	phpGedView

	File:		facts.he.php

	Author:		John Finlay

	Developer:	KosherJava

	Translator:	Meliza

	Comments:	Defines an array of GEDCOM codes and the Hebrew name facts that they

			represent.

	Change Log:	See LANG_CHANGELOG.txt

   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)

===================================================*/ 

# $Id: facts.he.php,v 1.1 2005/10/07 18:08:36 skenow Exp $

if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {

	print "אין לך גישה ישירה לקובץ השפות.";

	exit;

}



// -- Define a fact array to map GEDCOM tags with their English values

$factarray["ABBR"] 	= "קיצור";

$factarray["ADDR"] 	= "כתובת";

$factarray["ADR1"] 	= "כתובת 1";

$factarray["ADR2"] 	= "כתובת 2";

$factarray["ADOP"]	= "אמוץ";

$factarray["AGE"] 	= "גיל";

$factarray["AFN"]	= "מספר קובץ אב-אבות (AFN)";

$factarray["AGNC"]	= "סוכנות";

$factarray["ALIA"]	= "שם נרדף";

$factarray["ANCE"]	= "אב-אבות";

$factarray["ANCI"]	= "עניין אב-אבות";

$factarray["ANUL"]	= "ביטול";

$factarray["ASSO"]	= "שותפים";

$factarray["AUTH"] 	= "מחבר";

$factarray["BAPL"]	= "טבילת מורמונים";

$factarray["BAPM"]	= "טבילה";

$factarray["BARM"] 	= "בר מצווה";

$factarray["BASM"] 	= "בת מצווה";

$factarray["BIRT"] 	= "לידה";

$factarray["BLES"]	= "ברכה";

$factarray["BLOB"] 	= "אובייקט נתונים בינארי";

$factarray["BURI"] 	= "קבורה";

$factarray["CALN"]	= "מספר קריאה";

$factarray["CAST"]	= "כת / מעמד חברתי";

$factarray["CAUS"]	= "גורם המות";

$factarray["CEME"]  = "בית קברות";

$factarray["CENS"]	= "מפקד אוכלוסין";

$factarray["CHAN"]	= "שנוי אחרון";

$factarray["CHAR"] 	= "ערכת תווים";

$factarray["CHIL"] 	= "ילד/ה";

$factarray["CHR"]	= "הטבלה";

$factarray["CHRA"]	= "הטבלת מבוגרים";

$factarray["CITY"] 	= "עיר";

$factarray["CONF"]	= "ברית";

$factarray["CONL"]	= "ברית המורמונים";

$factarray["COPR"] 	= "זכויות יוצרים";

$factarray["CORP"]	= "חברה";

$factarray["CREM"]	= "שריפת גופה";

$factarray["CTRY"] 	= "מדינה";

$factarray["DATA"] 	= "נתונים";

$factarray["DATE"] 	= "תאריך";

$factarray["DEAT"] 	= "פטירה";

$factarray["DESC"]	= "צאצאים";

$factarray["DESI"]	= "עניין הצאצאים";

$factarray["DEST"]	= "יעד";

$factarray["DIV"]	= "גירושין";

$factarray["DIVF"]	= "הגשת בקשה לגירושים";

$factarray["EDUC"]	= "השכלה";

$factarray["DSCR"] 	= "תיאור";

$factarray["EMIG"] 	= "הגירה";

$factarray["ENDL"]	= "סמיכה של מיקדש המורמונים";

$factarray["ENGA"] 	= "אירוסין";

$factarray["EVEN"] 	= "אירוע";

$factarray["FAM"] 	= "משפחה";

$factarray["FAMC"]	= "משפחה כילד";

$factarray["FAMF"] 	= "קובץ משפחה";

$factarray["FAMS"]	= "משפחה כבן/בת זוג";

$factarray["FCOM"]	= "הסעודה הראשונה";

$factarray["FILE"]	= "קובץ  חיצוני";

$factarray["FORM"] 	= "תבנית";

$factarray["GIVN"] 	= "שמות פרטיים";

$factarray["GRAD"] 	= "סיום לימודים";

$factarray["LEGA"]	= "יורש";

$factarray["IDNO"] 	= "קוד זיהוי";

$factarray["IMMI"] 	= "עליה";

$factarray["MARB"]	= "הודעת נישואין";

$factarray["MARC"] 	= "כתובה";

$factarray["MARL"] 	= "רשיון נישואין";

$factarray["MEDI"]	= "סוג מדיה";

$factarray["NCHI"] 	= "מספר ילדים";

$factarray["NICK"] 	= "כנוי";

$factarray["NMR"] 	= "מספר נישואין";

$factarray["PAGE"]	= "פירטי ציטוט";

$factarray["PLAC"] 	= "מקום";

$factarray["PHON"] 	= "טלפון";

$factarray["POST"]	= "מיקוד";

$factarray["PUBL"]	= "הוצאה לאור";

$factarray["QUAY"]	= "איכות נתונים";

$factarray["RELI"]	= "דת";

$factarray["ROLE"]	= "תפקיד";

$factarray["REFN"]	= "מספר התייחסות";

$factarray["RELA"]	= "קשר משפחתי";

$factarray["RESN"]	= "הגבלה";

$factarray["RETI"]	= "פרישה";

$factarray["RFN"]	= "מספר קובץ רשום";

$factarray["RIN"]	= "קוד זיהוי רשום (ID)";

$factarray["RESI"]	= "מגורים";

$factarray["REPO"]	= "מאגר";

$factarray["SPFX"]	= "קידומת שם משפחה";

$factarray["SSN"]	= "מספר מזהה (SSN)";

$factarray["STAE"]	= "מדינה";

$factarray["STAT"]	= "סטטוס";

$factarray["SUBM"]	= "מגיש";

$factarray["SUBN"]	= "הגשה";

$factarray["PROP"]	= "נכס";

$factarray["PROB"]	= "אישור צוואה";

$factarray["SLGC"]	= "חותמת מורמונים - ילד";

$factarray["SLGS"]	= "חותמת מורמונים - בן זוג";

$factarray["SOUR"]  	= "מקור";

$factarray["SURN"]  	= "שם משפחה";

$factarray["TEMP"]	= "מקדש";

$factarray["TEXT"]  	= "טקסט";

$factarray["TIME"] 	= "זמן";

$factarray["TITL"]   	= "כותרת";

$factarray["TYPE"] 	= "סוג";

$factarray["WILL"]	= "צוואה";

$factarray["_EMAIL"]	= "כתובת דואר אלקטרוני";

$factarray["EMAIL"]	= "כתובת דואר אלקטרוני";

$factarray["_TODO"]	= "משימות";

$factarray["_UID"]	= "מזהה כללי";

$factarray["_PGVU"]	= "השינוי האחרון ע\"י";

$factarray["SEX"] 	= "מין";

$factarray["NAME"] 	= "שם";

$factarray["MARS"]	= "הסדר נישואין";

$factarray["NATI"] 	= "לאום";

$factarray["NATU"] 	= "התאזרחות";

$factarray["MARR"] 	= "נישואין";

$factarray["PAGE"]	= "פרטי ציטוט";

$factarray["OCCU"]	= "מקצוע";

$factarray["ORDI"]	= "הסמכה";

$factarray["ORDN"]	= "הסמכה לכמורה";

$factarray["NOTE"] 	= "הערה";

$factarray["NSFX"] 	= "צירוף סופי";

$factarray["NPFX"]	= "צירוף ראשי";

$factarray["OBJE"] 	= "מולטימדיה";

$factarray["PEDI"] 	= "יחוס";

$factarray["_PRIM"]	= "תמונה מודגשת";

$factarray["_THUM"]	= "השתמש בתמונה זו כתמונה ממוזערת?"; 



// These facts are specific to GEDCOM exports from Family Tree Maker

$factarray["_MDCL"]	= "רפואי";

$factarray["_DEG"]	= "דרגה";

$factarray["_MILT"] 	= "שרות צבא";

$factarray["_SEPR"]	= "פרוד";

$factarray["_DETS"]	= "מוות של אחד מבני הזוג";

$factarray["CITN"]	= "אזרחות";

$factarray["_FA1"] 	= "עובדה 1";

$factarray["_FA2"] 	= "עובדה 2";

$factarray["_FA3"] 	= "עובדה 3";

$factarray["_FA4"] 	= "עובדה 4";

$factarray["_FA5"] 	= "עובדה 5";

$factarray["_FA6"] 	= "עובדה 6";

$factarray["_FA7"] 	= "עובדה 7";

$factarray["_FA8"] 	= "עובדה 8";

$factarray["_FA9"] 	= "עובדה 9";

$factarray["_FA10"] 	= "עובדה 10";

$factarray["_FA11"] 	= "עובדה 11";

$factarray["_FA12"] 	= "עובדה 12";

$factarray["_FA13"] 	= "עובדה 13";

$factarray["_MREL"] 	= "קשר אל אמא";

$factarray["_FREL"] 	= "קשר אל אבא";

$factarray["_MSTAT"] = "מעמד תחילת נישואים";

$factarray["_MEND"] 	= "מעמד סיום נישואים";



// GEDCOM 5.5.1 related facts

$factarray["FAX"] 	= "פקס";

$factarray["FACT"] 	= "עובדה";

$factarray["WWW"] 	= "דף בית";

$factarray["MAP"] 	= "מפה";

$factarray["LATI"]	= "קו רוחב";

$factarray["LONG"]	= "קו אורך";

$factarray["FONE"] 	= "פונטי";

$factarray["ROMN"]	= "לטיני";



// PAF related facts

$factarray["_NAME"] 	= "שם למשלוח דואר";

$factarray["URL"] 		= "URL";

$factarray["_HEB"] 		= "עברי";

$factarray["_SCBK"] 	= "אלבום הדבקות";

$factarray["_TYPE"] 	= "סוג מדיה";

$factarray["_SSHOW"] 	= "מצגת שקופיות";



// Rootsmagic

$factarray["_SUBQ"]	= "גרסה קצרה";

$factarray["_BIBL"] 		= "ביבליוגרפיה";



// Other common customized facts

$factarray["_ADPF"]		= "אמוץ ע\"י אבא";

$factarray["_ADPM"]	= "אמוץ ע\"י אמא";

$factarray["_AKAN"]		= "ידוע בשם";

$factarray["_AKA"] 		= "ידוע בשם";

$factarray["_BRTM"] 	= "ברית מילה";

$factarray["_COML"]	= "ידוע בציבור";

$factarray["_EYEC"]		= "צבע עיניים";

$factarray["_FNRL"]		= "הלוויה";

$factarray["_HAIR"]		= "צבע שיער";

$factarray["_HEIG"]		= "גובה";

$factarray["_HOL"] 		= "שואה";

$factarray["_INTE"]		= "קבור";

$factarray["_MARI"]		= "כוונת נישואים";

$factarray["_MBON"]	= "קשר נישואים";

$factarray["_MEDC"]	= "מצב רפואי";

$factarray["_MILI"] 		= "צבא";

$factarray["_NMR"]		= "לא נשוי";

$factarray["_NLIV"] 		= "לא בחיים";

$factarray["_NMAR"]	= "רווק";

$factarray["_PRMN"]	= "מספר קבוע";

$factarray["_WEIG"] 	= "משקל";

$factarray["_YART"]		= "יום השנה";

$factarray["_MARNM"] 	= "שם נישואין";

$factarray["_STAT"] 	= "מעמד נישואין";

$factarray["COMM"]		= "הערה";



// Aldfaer related facts

$factarray["MARR_CIVIL"] 	      = "נישואין אזרחיים";

$factarray["MARR_RELIGIOUS"]  = "נישואין דתיים";

$factarray["MARR_PARTNERS"] = "שותפות רשמית";

$factarray["MARR_UNKNOWN"]  = "סוג הנישואין אינו ידוע";



$factarray["_HNM"] 		= "שם עברי";



if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.he.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.he.extra.php";



?>