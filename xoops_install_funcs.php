<?php
function xoops_module_install_phpGedView($xoopsMod)
{
	return true;
}

function xoops_module_uninstall_phpGedView($xoopsMod)
{
	return true;
}

function xoops_module_update_phpGedView($xoopsMod, $oldversion)
{
	switch ($oldversion)
	{
		case 100:
		{
			// reserved for future use :)
			break;
		}
	}
}
?>