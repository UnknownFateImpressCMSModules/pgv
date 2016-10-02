<?php
/*=================================================
   charset=UTF-8
   Project:	PhpGedView
   File:	lang.ar.php
   Author:	John Finlay
   Translation:	 
   Comments:	Arabic Language file for PhpGedView.
   Change Log:	See LANG_CHANGELOG.txt
		2004-11-29 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
if (preg_match("/lang\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

//-- CONFIG FILE MESSAGES
$pgv_lang["yes"]					= "نعم";
$pgv_lang["no"] 					= "لا";
$pgv_lang["phone"]					= "رقم الهاتف";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["birth"]					= "مولود";
$pgv_lang["death"]					= "مات";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["male"]					= "ذكر";
$pgv_lang["female"] 				= "انثا";
$pgv_lang["PN"] 					= "غير معروف";
$pgv_lang["family"] 				= "الاسره";
$pgv_lang["NN"] 					= "غير معروف";
$pgv_lang["name"]					= "اسم";
$pgv_lang["given_name"] 			= "اِسم الخاص:";
$pgv_lang["surname"]				= "إسم العائلة:";
$pgv_lang["sex"]					= "جنس";
$pgv_lang["type"]					= "النوع";
$pgv_lang["date"]					= "اليوم";
$pgv_lang["father"] 				= "الأب";
$pgv_lang["mother"] 				= "الأم";
$pgv_lang["husband"]				= "زوج";
$pgv_lang["wife"]					= "زوجه";
$pgv_lang["marriage"]				= "زواج";
$pgv_lang["children"]				= "الأبناء";
$pgv_lang["unknown"]				= "غير معروف";
$pgv_lang["firstname_search"]		= "الأسم الخاص";
$pgv_lang["lastname_search"]		= "أسم الأسره";
$pgv_lang["search_place"]			= "منطقه";
$pgv_lang["search_year"]			= "سنه";
$pgv_lang["sec"]					= "ثوانى";
$pgv_lang["total_fams"] 			= "عدد الأسر";
$pgv_lang["all"]					= "كل";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]					= "عمر";

//-- MONTH NAMES
$pgv_lang["jan"]					= "ياناير";
$pgv_lang["feb"]					= "فبراير";
$pgv_lang["mar"]					= "مارس";
$pgv_lang["apr"]					= "ابريل";
$pgv_lang["may"]					= "مايو";
$pgv_lang["jun"]					= "يونيو";
$pgv_lang["jul"]					= "يوليو";
$pgv_lang["aug"]					= "اغسطس";
$pgv_lang["sep"]					= "سبتمبر";
$pgv_lang["oct"]					= "اكتوبر";
$pgv_lang["nov"]					= "نوفمبر";
$pgv_lang["dec"]					= "ديسمبر";
$pgv_lang["abt"]					= "حوالى";
$pgv_lang["aft"]					= "بعد";
$pgv_lang["and"]					= "و";
$pgv_lang["bef"]					= "قبل";
$pgv_lang["bet"]					= "بين";
$pgv_lang["cal"]					= "محسوب";
$pgv_lang["from"]					= "من";
$pgv_lang["to"] 					= "الى";
$pgv_lang["apx"]					= "تقريبا";
$pgv_lang["full_name"]				= "الاسم الكامل";
$pgv_lang["son"]					= "ابن";
$pgv_lang["daughter"]				= "بنت";
$pgv_lang["select_date"]			= "اختار اليوم";
$pgv_lang["today"]					= "الأن";
$pgv_lang["day"]					= "اليوم:";
$pgv_lang["month"]					= "الشهر:";
$pgv_lang["mail01_line01"]			= "...#user_fullname# السلام عليكم ";
$pgv_lang["mail02_line01"]			= "Administrator السلام عليكم";
$pgv_lang["alive"]					= "حى";
$pgv_lang["dead"]					= "ميت";
$pgv_lang["alive_in_year"]			= "احياء فى السنه";
$pgv_lang["stplperc"]			 = "عدد فى المئه";
$pgv_lang["stplage"]			 = "عمر";
$pgv_lang["stplmonth"]			 = "شهر";
$pgv_lang["stpltype"]			 = "نوع:";
$pgv_lang["stat_301_mf"]			 = "ذكر أم أنثى";
$pgv_lang["twin"] = "توئم";
$pgv_lang["priest"] = "قسيس";
$pgv_lang["friend"] = "صاحب";
$pgv_lang["sosa_3"] 				= "الأم";
$pgv_lang["sosa_2"] 				= "الأب";
$pgv_lang["page"]					= "صفحه";
$pgv_lang["upgrade_help"]			= "ساعدنى";
$pgv_lang["gedcom_news"]			= "اخبار";
$pgv_lang["gedcom_created_on2"] 	= "<b>#DATE#</b> فى";
$pgv_lang["message_to"] 			= "رساله الى:";
$pgv_lang["message_from_name"]		= "اسمك:";
$pgv_lang["date_created"]			= "اترسل اليوم:";
$pgv_lang["message_from"]			= "عنوان البريد الاليكترونى";
$pgv_lang["my_messages"]			= "رسالاتى";
$pgv_lang["message"]				= "ارسل رساله";
$pgv_lang["welcome"]				= "مرحبا";
$pgv_lang["mail03_line01"]			= "Administrator السلام عليكم";
$pgv_lang["send"]					= "ارسل";
$pgv_lang["emailadress"]			= "عنوان البريد الايكترونى";
$pgv_lang["saturday"]				= "السبت";
$pgv_lang["friday"] 				= "الجمعه";
$pgv_lang["thursday"]				= "الخميس";
$pgv_lang["wednesday"]				= "الاربعاء";
$pgv_lang["tuesday"]				= "الثلاثاء";
$pgv_lang["monday"] 				= "الاثنين";
$pgv_lang["sunday"] 				= "الاحد";

if (file_exists($PGV_BASE_DIRECTORY . "languages/lang.ar.extra.php")) require $PGV_BASE_DIRECTORY . "languages/lang.ar.extra.php";
$pgv_lang["total_living"]			= "عدد الاحياء";
$pgv_lang["total_dead"]				= "عدد الميت";
$pgv_lang["total_names"]			= "عدد الاسماء";
$pgv_lang["page_help"]				= "مساعده";
$pgv_lang["help_for_this_page"] 	= "مساعده فى هاذا الصفحه";
$pgv_lang["fullname"]				= "الاسم الكامل";
$pgv_lang["changelog"]				= " #VERSION# التّغييرات للنّسخة ";
$pgv_lang["html_block_descr"]		= "هذه كتلة إتش تي إم إل بسيطة الّتي يمكن أن تضعها على صفحتك لإضافة أيّ نوع للرّسالة الّتي قد تريدها";
$pgv_lang["days_to_show"]			= "عدد الأيّام سيظهر";
$pgv_lang["num_to_show"]			= "عدد البنود سيظهر";
$pgv_lang["html_block_name"]		= "كتلة إتش تي إم إل";

?>