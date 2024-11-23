<?php
// $Id: xoops_version.php,v 1.3 2006/03/06 00:26:33 skenow Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System //
// Copyright (c) 2000 XOOPS.org //
// <http://www.xoops.org/> //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify //
// it under the terms of the GNU General Public License as published by //
// the Free Software Foundation; either version 2 of the License, or //
// (at your option) any later version. //
// //
// You may not change or alter any portion of this comment or credits //
// of supporting developers from this source code or any supporting //
// source code which is considered copyrighted (c) material of the //
// original comment or credit authors. //
// //
// This program is distributed in the hope that it will be useful, //
// but WITHOUT ANY WARRANTY; without even the implied warranty of //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the //
// GNU General Public License for more details. //
// //
// You should have received a copy of the GNU General Public License //
// along with this program; if not, write to the Free Software //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA //
// ------------------------------------------------------------------------ //
$modversion = array(
	'name' => 'PHPGedView',
	'version' => '2.0.0',
	'status' => 'Beta',
	'description' => 'PhpGedView is a revolutionary genealogy program which allows you to view and edit your genealogy on your website.',
	'credits' => 'John Finlay & Others (http://www.PhpGedView.net/)',
	'author' => 'Steve Kenow, Patrick Kellum',
	'help' => '',
	'license' => 'GNU General Public License (GPL) see LICENSE',
	'official' => 0,
	'image' => 'pgv_slogo.png',
	'dirname' => basename(__DIR__),
	'onInstall' => 'xoops_install_funcs.php',
	'onUninstall' => 'xoops_install_funcs.php',
	'onUpdate' => 'xoops_install_funcs.php');

// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'] = array(
	'pgv_blocks', 
	'pgv_dates', 
	'pgv_families', 
	'pgv_favorites', 
	'pgv_individuals', 
	'pgv_messages', 
	'pgv_names', 
	'pgv_news', 
	'pgv_other', 
	'pgv_placelinks', 
	'pgv_places', 
	'pgv_sources', 
	'pgv_users', 
	'pgv_media', 
	'pgv_media_mapping', 
	'pgv_tblver'
);

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin.php';
$modversion['adminmenu'] = 'xoops_adminmenu.php';

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = 'Welcome Page';
$modversion['sub'][1]['url'] = 'index.php?command=gedcom';
$modversion['sub'][2]['name'] = 'MyGedView Portal';
$modversion['sub'][2]['url'] = 'index.php?command=user';

// Smarty
$modversion['use_smarty'] = 1;
