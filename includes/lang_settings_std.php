<?php
/**
 * Standard file of language_settings.php
 *
 * -> NEVER manually delete or edit this file <-
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
 * $Id: lang_settings_std.php,v 1.6 2005/08/19 12:40:14 canajun2eh Exp $
 *
 * @package PhpGedView
 * @subpackage Languages
 */

//-- NEVER manually delete or edit this entry and every line below this entry! --START--//

// Array definition of language_settings
$language_settings = array();

//-- settings for czech
$lang = array();
$lang["pgv_langname"]		= "czech";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Čeština";
$lang["lang_short_cut"]		= "cz";
$lang["langcode"]		= "cs;cz;";
$lang["pgv_language"]		= "languages/lang.cz.php";
$lang["confighelpfile"]		= "languages/configure_help.cz.php";
$lang["helptextfile"]		= "languages/help_text.cz.php";
$lang["flagsfile"]		= "images/flags/cz.gif";
$lang["factsfile"]		= "languages/facts.cz.php";
$lang["DATE_FORMAT"]		= "D. M Y";
$lang["TIME_FORMAT"]		= "G:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "AÁBCČDĎEĚÉFGHIÍJKLMNŇOÓPQRŘSŠTŤUÚŮVWXYÝZŽ";
$lang["ALPHABET_lower"]		= "aábcčdďeěéfghiíjklmnňoópqrřsštťuúůvwxyýzž";
$language_settings["czech"]	= $lang;

//-- settings for german
$lang = array();
$lang["pgv_langname"]		= "german";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Deutsch";
$lang["lang_short_cut"]		= "de";
$lang["langcode"]		= "de;de-de;de-at;de-li;de-lu;de-ch;";
$lang["pgv_language"]		= "languages/lang.de.php";
$lang["confighelpfile"]		= "languages/configure_help.de.php";
$lang["helptextfile"]		= "languages/help_text.de.php";
$lang["flagsfile"]		= "images/flags/de.gif";
$lang["factsfile"]		= "languages/facts.de.php";
$lang["DATE_FORMAT"]		= "D. M Y";
$lang["TIME_FORMAT"]		= "H:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyzäöüß";
$language_settings["german"]	= $lang;

//-- settings for english
$lang = array();
$lang["pgv_langname"]		= "english";
$lang["pgv_lang_use"]		= true;
$lang["pgv_lang"]		= "English";
$lang["lang_short_cut"]		= "en";
$lang["langcode"]		= "en;en-us;en-au;en-bz;en-ca;en-ie;en-jm;en-nz;en-ph;en-za;en-tt;en-gb;en-zw;";
$lang["pgv_language"]		= "languages/lang.en.php";
$lang["confighelpfile"]		= "languages/configure_help.en.php";
$lang["helptextfile"]		= "languages/help_text.en.php";
$lang["flagsfile"]		= "images/flags/en.gif";
$lang["factsfile"]		= "languages/facts.en.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyz";
$language_settings["english"]	= $lang;

//-- settings for spanish
$lang = array();
$lang["pgv_langname"]		= "spanish";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Español";
$lang["lang_short_cut"]		= "es";
$lang["langcode"]		= "es;es-bo;es-cl;es-co;es-cr;es-do;es-ec;es-sv;es-gt;es-hn;es-mx;es-ni;es-pa;es-py;es-pe;es-pr;es-us;es-uy;es-ve;";
$lang["pgv_language"]		= "languages/lang.es.php";
$lang["confighelpfile"]		= "languages/configure_help.es.php";
$lang["helptextfile"]		= "languages/help_text.es.php";
$lang["flagsfile"]		= "images/flags/es.gif";
$lang["factsfile"]		= "languages/facts.es.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnñopqrstuvwxyz";
$language_settings["spanish"]	= $lang;

//-- settings for spanish-ar
$lang = array();
$lang["pgv_langname"]		= "spanish-ar";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Español Latinoamericano";
$lang["lang_short_cut"]		= "es-ar";
$lang["langcode"]		= "es-ar;";
$lang["pgv_language"]		= "languages/lang.es-ar.php";
$lang["confighelpfile"]		= "languages/configure_help.es-ar.php";
$lang["helptextfile"]		= "languages/help_text.es-ar.php";
$lang["flagsfile"]		= "images/flags/es-ar.gif";
$lang["factsfile"]		= "languages/facts.es-ar.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnñopqrstuvwxyz";
$language_settings["spanish-ar"]	= $lang;

//-- settings for french
$lang = array();
$lang["pgv_langname"]		= "french";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Français";
$lang["lang_short_cut"]		= "fr";
$lang["langcode"]		= "fr;fr-be;fr-ca;fr-lu;fr-mc;fr-ch;";
$lang["pgv_language"]		= "languages/lang.fr.php";
$lang["confighelpfile"]		= "languages/configure_help.fr.php";
$lang["helptextfile"]		= "languages/help_text.fr.php";
$lang["flagsfile"]		= "images/flags/fr.gif";
$lang["factsfile"]		= "languages/facts.fr.php";
$lang["DATE_FORMAT"]		= "D j F Y";
$lang["TIME_FORMAT"]		= "H:i:s";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "AÀÂÆBCÇDEÉÈËÊFGHIÏÎJKLMNOÔŒPQRSTUÙÛVWXYZ";
$lang["ALPHABET_lower"]		= "aàâæbcçdeéèëêfghiïîjklmnoôœpqrstuùûvwxyz";

$language_settings["french"]	= $lang;

//-- settings for italian
$lang = array();
$lang["pgv_langname"]		= "italian";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Italiano";
$lang["lang_short_cut"]		= "it";
$lang["langcode"]		= "it;it-ch;";
$lang["pgv_language"]		= "languages/lang.it.php";
$lang["confighelpfile"]		= "languages/configure_help.it.php";
$lang["helptextfile"]		= "languages/help_text.it.php";
$lang["flagsfile"]		= "images/flags/it.gif";
$lang["factsfile"]		= "languages/facts.it.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyz";
$language_settings["italian"]	= $lang;

//-- settings for hungarian
$lang = array();
$lang["pgv_langname"]		= "hungarian";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Magyar";
$lang["lang_short_cut"]		= "hu";
$lang["langcode"]		= "hu;";
$lang["pgv_language"]		= "languages/lang.hu.php";
$lang["confighelpfile"]		= "languages/configure_help.hu.php";
$lang["helptextfile"]		= "languages/help_text.hu.php";
$lang["flagsfile"]		= "images/flags/hu.gif";
$lang["factsfile"]		= "languages/facts.hu.php";
$lang["DATE_FORMAT"]		= "Y. M D.";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= true;
$lang["ALPHABET_upper"]		= "AÁBCDEÉFGHIÍJKLMNOÓÖŐPQRSTUÚÜŰVWXYZ";
$lang["ALPHABET_lower"]		= "aábcdeéfghiíjklmnoóöőpqrstuúüűvwxyz";
$language_settings["hungarian"]	= $lang;

//-- settings for dutch
$lang = array();
$lang["pgv_langname"]		= "dutch";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Nederlands";
$lang["lang_short_cut"]		= "nl";
$lang["langcode"]		= "nl;nl-be;";
$lang["pgv_language"]		= "languages/lang.nl.php";
$lang["confighelpfile"]		= "languages/configure_help.nl.php";
$lang["helptextfile"]		= "languages/help_text.nl.php";
$lang["flagsfile"]		= "images/flags/nl.gif";
$lang["factsfile"]		= "languages/facts.nl.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "G:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyz";
$language_settings["dutch"]	= $lang;

//-- settings for norwegian
$lang = array();
$lang["pgv_langname"]		= "norwegian";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Norsk";
$lang["lang_short_cut"]		= "no";
$lang["langcode"]		= "no;nb;nn;";
$lang["pgv_language"]		= "languages/lang.no.php";
$lang["confighelpfile"]		= "languages/configure_help.no.php";
$lang["helptextfile"]		= "languages/help_text.no.php";
$lang["flagsfile"]		= "images/flags/no.gif";
$lang["factsfile"]		= "languages/facts.no.php";
$lang["DATE_FORMAT"]		= "D. M Y";
$lang["TIME_FORMAT"]		= "H:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyzæøå";
$language_settings["norwegian"]	= $lang;

//-- settings for polish
$lang = array();
$lang["pgv_langname"]		= "polish";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Polski";
$lang["lang_short_cut"]		= "pl";
$lang["langcode"]		= "pl;";
$lang["pgv_language"]		= "languages/lang.pl.php";
$lang["confighelpfile"]		= "languages/configure_help.pl.php";
$lang["helptextfile"]		= "languages/help_text.pl.php";
$lang["flagsfile"]		= "images/flags/pl.gif";
$lang["factsfile"]		= "languages/facts.pl.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "G:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "AĄBCĆDEĘFGHIJKLŁMNŃOÓPQRSŚTUVWXYZŹŻ";
$lang["ALPHABET_lower"]		= "aąbcćdeęfghijklłmnńoópqrsśtuvwxyzźż";
$language_settings["polish"]	= $lang;

//-- settings for portuguese-br
$lang = array();
$lang["pgv_langname"]		= "portuguese-br";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Portuguese";
$lang["lang_short_cut"]		= "pt-br";
$lang["langcode"]		= "pt;pt-br;";
$lang["pgv_language"]		= "languages/lang.pt-br.php";
$lang["confighelpfile"]		= "languages/configure_help.pt-br.php";
$lang["helptextfile"]		= "languages/help_text.pt-br.php";
$lang["flagsfile"]		= "images/flags/pt-br.gif";
$lang["factsfile"]		= "languages/facts.pt-br.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnñopqrstuvwxyz";
$language_settings["portuguese-br"]	= $lang;

//-- settings for finnish
$lang = array();
$lang["pgv_langname"]		= "finnish";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Suomi";
$lang["lang_short_cut"]		= "fi";
$lang["langcode"]		= "fi;";
$lang["pgv_language"]		= "languages/lang.fi.php";
$lang["confighelpfile"]		= "languages/configure_help.fi.php";
$lang["helptextfile"]		= "languages/help_text.fi.php";
$lang["flagsfile"]		= "images/flags/fi.gif";
$lang["factsfile"]		= "languages/facts.fi.php";
$lang["DATE_FORMAT"]		= "D. M Y";
$lang["TIME_FORMAT"]		= "H:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyzåäö";
$language_settings["finnish"]	= $lang;

//-- settings for swedish
$lang = array();
$lang["pgv_langname"]		= "swedish";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Svenska";
$lang["lang_short_cut"]		= "sv";
$lang["langcode"]		= "sv;sv-fi;";
$lang["pgv_language"]		= "languages/lang.sv.php";
$lang["confighelpfile"]		= "languages/configure_help.sv.php";
$lang["helptextfile"]		= "languages/help_text.sv.php";
$lang["flagsfile"]		= "images/flags/sv.gif";
$lang["factsfile"]		= "languages/facts.sv.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "H:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyzåäö";
$language_settings["swedish"]	= $lang;

//-- settings for turkish
$lang = array();
$lang["pgv_langname"]		= "turkish";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Türkçe";
$lang["lang_short_cut"]		= "tr";
$lang["langcode"]		= "tr;";
$lang["pgv_language"]		= "languages/lang.tr.php";
$lang["confighelpfile"]		= "languages/configure_help.tr.php";
$lang["helptextfile"]		= "languages/help_text.tr.php";
$lang["flagsfile"]		= "images/flags/tr.gif";
$lang["factsfile"]		= "languages/facts.tr.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "G:i:s";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ABCÇDEFGĞHIİJKLMNOÖPRSŞTUÜVYZ";
$lang["ALPHABET_lower"]		= "abcçdefgğhıijklmnoöprsştuüvyz";
$language_settings["turkish"]	= $lang;

//-- settings for chinese
$lang = array();
$lang["pgv_langname"]		= "chinese";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "繁體中文";
$lang["lang_short_cut"]		= "zh";
$lang["langcode"]		= "zh;zh-cn;zh-hk;zh-mo;zh-sg;zh-tw;";
$lang["pgv_language"]		= "languages/lang.zh.php";
$lang["confighelpfile"]		= "languages/configure_help.zh.php";
$lang["helptextfile"]		= "languages/help_text.zh.php";
$lang["flagsfile"]		= "images/flags/zh.gif";
$lang["factsfile"]		= "languages/facts.zh.php";
$lang["DATE_FORMAT"]		= "Y年 M D日";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= true;
$lang["ALPHABET_upper"]		= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]		= "abcdefghijklmnopqrstuvwxyz";
$language_settings["chinese"]	= $lang;

//-- settings for hebrew
$lang = array();
$lang["pgv_langname"]		= "hebrew";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "‏עברית";
$lang["lang_short_cut"]		= "he";
$lang["langcode"]		= "he;";
$lang["pgv_language"]		= "languages/lang.he.php";
$lang["confighelpfile"]		= "languages/configure_help.he.php";
$lang["helptextfile"]		= "languages/help_text.he.php";
$lang["flagsfile"]		= "images/flags/he.gif";
$lang["factsfile"]		= "languages/facts.he.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "G:i:s";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "rtl";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "אבגדהוזחטיכךלמםנןסעפףצץקרשת";
$lang["ALPHABET_lower"]		= "אבגדהוזחטיכךלמםנןסעפףצץקרשת";
$language_settings["hebrew"]	= $lang;

//-- settings for russian
$lang = array();
$lang["pgv_langname"]		= "russian";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "русский";
$lang["lang_short_cut"]		= "ru";
$lang["langcode"]		= "ru;ru-md;";
$lang["pgv_language"]		= "languages/lang.ru.php";
$lang["confighelpfile"]		= "languages/configure_help.ru.php";
$lang["helptextfile"]		= "languages/help_text.ru.php";
$lang["flagsfile"]		= "images/flags/ru.gif";
$lang["factsfile"]		= "languages/facts.ru.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
$lang["ALPHABET_lower"]		= "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
$language_settings["russian"]	= $lang;

//-- settings for greek
$lang = array();
$lang["pgv_langname"]		= "greek";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Ελληνικά";
$lang["lang_short_cut"]		= "el";
$lang["langcode"]		= "el;";
$lang["pgv_language"]		= "languages/lang.el.php";
$lang["confighelpfile"]		= "languages/configure_help.el.php";
$lang["helptextfile"]		= "languages/help_text.el.php";
$lang["flagsfile"]		= "images/flags/el.gif";
$lang["factsfile"]		= "languages/facts.el.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ΆΑΒΓΔΈΕΖΗΘΊΪΪΙΚΛΜΝΞΌΟΠΡΣΣΤΎΫΫΥΦΧΨΏΩ";
$lang["ALPHABET_lower"]		= "άαβγδέεζηθίϊΐικλμνξόοπρσςτύϋΰυφχψώω";
$language_settings["greek"]	= $lang;

//-- settings for arabic
$lang = array();
$lang["pgv_langname"]		= "arabic";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "عربي";
$lang["lang_short_cut"]		= "ar";
$lang["langcode"]		= "ar;ar-ae;ar-bh;ar-dz;ar-eg;ar-iq;ar-jo;ar-kw;ar-lb;ar-ly;ar-ma;ar-om;ar-qa;ar-sa;ar-sy;ar-tn;ar-ye;";
$lang["pgv_language"]		= "languages/lang.ar.php";
$lang["confighelpfile"]		= "languages/configure_help.ar.php";
$lang["helptextfile"]		= "languages/help_text.ar.php";
$lang["flagsfile"]		= "images/flags/ar.gif";
$lang["factsfile"]		= "languages/facts.ar.php";
$lang["DATE_FORMAT"]		= "D M Y";
$lang["TIME_FORMAT"]		= "h:i:sA";
$lang["WEEK_START"]		= "0";
$lang["TEXT_DIRECTION"]		= "rtl";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی";
$lang["ALPHABET_lower"]		= "ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی";
// arabian numbers                    "٠١٢٣٤٥٦٧٨٩"
// iranian/pakistani/indian numbers   "۰۱۲۳۴۵۶۷۸۹"; 
// 
$language_settings["arabic"]	= $lang;

//-- settings for lithuanian
$lang = array();
$lang["pgv_langname"]		= "lithuanian";
$lang["pgv_lang_use"]		= false;
$lang["pgv_lang"]		= "Lithuanian";
$lang["lang_short_cut"]		= "lt";
$lang["langcode"]		= "lt;";
$lang["pgv_language"]		= "languages/lang.lt.php";
$lang["confighelpfile"]		= "languages/configure_help.lt.php";
$lang["helptextfile"]		= "languages/help_text.lt.php";
$lang["flagsfile"]		= "images/flags/lt.gif";
$lang["factsfile"]		= "languages/facts.lt.php";
$lang["DATE_FORMAT"]		= "Y M D";
$lang["TIME_FORMAT"]		= "g:i:sa";
$lang["WEEK_START"]		= "1";
$lang["TEXT_DIRECTION"]		= "ltr";
$lang["NAME_REVERSE"]		= false;
$lang["ALPHABET_upper"]		= "AĄBCČDEĘĖFGHIYĮJKLMNOPRSŠTUŲŪVZŽ";
$lang["ALPHABET_lower"]		= "aąbcčdeęėfghiyįjklmnoprsštuųūvzž";
$language_settings["lithuanian"]	= $lang;
//-- NEVER manually delete or edit this entry and every line above this entry! --END--//

// Array definitions
$languages	= array();
$pgv_lang_use	= array();
$pgv_lang	= array();
$lang_short_cut	= array();
$lang_langcode	= array();
$pgv_language	= array();
$confighelpfile	= array();
$helptextfile	= array();
$flagsfile	= array();	//-- flags obtained from http://w3f.com/gifs/index.html
$factsfile	= array();
$factsarray	= array();
$pgv_lang_name	= array();
$langcode	= array();
$ALPHABET_upper	= array();
$ALPHABET_lower	= array();
$DATE_FORMAT_array	= array();
$TIME_FORMAT_array	= array();
$WEEK_START_array	= array();
$TEXT_DIRECTION_array	= array();
$NAME_REVERSE_array	= array();

foreach ($language_settings as $key => $value)
{
  $languages[$key] 	= $value["pgv_langname"];
  $pgv_lang_use[$key]	= $value["pgv_lang_use"];
  $pgv_lang[$key]	= $value["pgv_lang"];
  $lang_short_cut[$key]	= $value["lang_short_cut"];
  $lang_langcode[$key]	= $value["langcode"];
  $pgv_language[$key]	= $value["pgv_language"];
  $confighelpfile[$key]	= $value["confighelpfile"];
  $helptextfile[$key]	= $value["helptextfile"];
  $flagsfile[$key]	= $value["flagsfile"];
  $factsfile[$key]	= $value["factsfile"];
  $ALPHABET_upper[$key]	= $value["ALPHABET_upper"];
  $ALPHABET_lower[$key]	= $value["ALPHABET_lower"];
  $DATE_FORMAT_array[$key]	= $value["DATE_FORMAT"];
  $TIME_FORMAT_array[$key]	= $value["TIME_FORMAT"];;
  $WEEK_START_array[$key]	= $value["WEEK_START"];
  $TEXT_DIRECTION_array[$key]	= $value["TEXT_DIRECTION"];
  $NAME_REVERSE_array[$key]	= $value["NAME_REVERSE"];

  $pgv_lang["lang_name_$key"]	= $value["pgv_lang"];

  $dDummy = $value["langcode"];
  $ct = strpos($dDummy, ";");
  while ($ct > 1)
  {
    $shrtcut = substr($dDummy,0,$ct);
    $dDummy = substr($dDummy,$ct+1);
    $langcode[$shrtcut]		= $key;
    $ct = strpos($dDummy, ";");
  }
}
?>