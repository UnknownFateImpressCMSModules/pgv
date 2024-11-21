<?php
/*=================================================
   charset=utf-8
   Project:	phpGedView
   File:	configure_help.fi.php
   Author:	John Finlay
   Translation: Meliza
   Comments:	Finnish Language Configure Help file for PhpGedView
   Change Log:	14/6/04 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: configure_help.fi.php,v 1.1 2005/10/07 18:08:36 skenow Exp $

if (preg_match("/configure_help\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

//-- CONFIGURE FILE MESSAGES
$pgv_lang["can_admin"]			= "Käyttäjä voi ylläpitä";
$pgv_lang["can_edit"]			= "Käyttäjä voi muokata";
$pgv_lang["add_user"]			= "Lisää uusi käyttäjä";
$pgv_lang["current_users"]		= "Nykyinen käyttäjälista";
$pgv_lang["leave_blank"]		= "Jätä salasana tyhjäksi jos et halua muuttaa sitä.";
$pgv_lang["messaging2"]			= "Sisäiset viestit ja sähköposti";
$pgv_lang["messaging3"]			= "PhpGedView lähettää sähköposteja ilman säilytystä";
$pgv_lang["no_messaging"]		= "Ei mitään yhteystapaa";
$pgv_lang["privileges"]			= "Etuoikeudet";
$pgv_lang["date_registered"]	= "Rekisteröintipäivä";
$pgv_lang["last_login"]			= "Viimeksi kirjautunut";
$pgv_lang["show_phpinfo"]		= "Näytä PHPInfosivu";

//-- edit privacy messages

//-- language edit utility
$pgv_lang["edit_langdiff"]		= "Editoi ja konfiguroi kielitiedostoja";
$pgv_lang["lang_back_admin"]	= "Palaa ylläpitovalikkoon";
$pgv_lang["system_time"]		= "Nykyinen järjestelmäaika:";
$pgv_lang["never"]				= "Ei koskaan";

?>