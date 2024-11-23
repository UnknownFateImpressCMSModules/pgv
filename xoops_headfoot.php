<?php
	ob_start();
require_once '../../mainfile.php';
if (
	(stristr($_SERVER['SCRIPT_NAME'], 'addmedia') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'admin') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'changelanguage') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'downloadgedcom') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'edit_merge') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'edit_privacy') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'editconfig') == true) || // also catches editconfig_gedcom
	(stristr($_SERVER['SCRIPT_NAME'], 'editgedcoms') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'editlang') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'importgedcom') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'linkmedia') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'pgvinfo') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'uploadgedcom') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'uploadmedia') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'useradmin') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'usermigrate') == true) ||
	(stristr($_SERVER['SCRIPT_NAME'], 'validategedcom') == true)
)
{
	include_once XOOPS_ROOT_PATH.'/include/cp_functions.php';
	global $xoopsConfig, $xoopsUser;
	if ($xoopsConfig['gzip_compression'] == 1) {
		ob_start("ob_gzhandler");
	} else {
		ob_start();
	}
	xoops_cp_header();
	print '{pgvsplit}';
	xoops_cp_footer();
	$output = ob_get_contents();
	ob_end_clean();
	$output = str_replace('</head>', "\n{pgvsplit}\n</head>", $output);
}
else
{
	require_once XOOPS_ROOT_PATH.'/header.php';
	$xoopsTpl->assign('xoops_pagetitle', '{pgvtitle}');
	$xoopsTpl->assign('xoops_module_header', '{pgvsplit}');
	print '{pgvsplit}';
	require_once XOOPS_ROOT_PATH.'/footer.php';
	$output = ob_get_contents();
	ob_end_clean();
}
$output = explode('{pgvsplit}', $output);
$GLOBALS['wrapper'] = array(
	'header1' => $output[0],
	'header2' => $output[1],
	'footer' => $output[2]
);
?>