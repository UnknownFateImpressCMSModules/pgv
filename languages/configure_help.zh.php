<?php
/*=================================================
   charset=utf-8
   Project:	phpGedView
   File:	configure_help.zh.php
   Author:	John Finlay
   Comments:	Chinese Language Configure Help file for PhpGedView
   Change Log:	2004/01/03 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: configure_help.zh.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
if (preg_match("/configure_help\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
//-- CONFIGURE FILE MESSAGES
$pgv_lang["configure"]			= "Configure PhpGedView";

//-- edit privacy messages
$pgv_lang["edit_privacy"]		= "Configuration of the privacy-file";

//-- language edit utility

//-- language edit utility
$pgv_lang["edit_langdiff"]		= "編輯語言文件的內容";
$pgv_lang["edit_lang_utility"]		= "語言文件編輯公共事業";
$pgv_lang["edit_lang_utility_help"]	= "您能使用這項公共事業編輯語言文件的內容由使用內容英國一個。<br />它將列出您原始的英語文件的內容和您選上的語言內容<br />在點擊在您選上的文件消息以後一個新窗口將打開您能改變和保存您選上的語言消息的地方。";
$pgv_lang["language_to_edit"]		= "語言編輯";
$pgv_lang["file_to_edit"]		= "語言文件類型編輯";
$pgv_lang["lang_save"]			= "之外";
$pgv_lang["contents"]			= "內容";
$pgv_lang["listing"]			= "目錄";
$pgv_lang["no_content"]			= "沒有內容";
$pgv_lang["editlang_help"]		= "編輯消息從語言文件";
$pgv_lang["cancel"]			= "取消";
$pgv_lang["savelang_help"]		= "保存被編輯的消息";
$pgv_lang["original_message"]		= "原始的消息";
$pgv_lang["message_to_edit"]		= "消息編輯";

?>