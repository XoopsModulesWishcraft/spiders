<?php
function xoops_module_update_spiders(&$module) {
	ini_set("max_execution_time", "600");  
	$GLOBALS['xoopsDB']->queryF("ALTER TABLE ".$GLOBALS['xoopsDB']->prefix("spiders").' ADD COLUMN `robot-safeuseragent` VARCHAR(255) DEFAULT NULL');
	$spiders_handler =& xoops_getmodulehandler('spiders', _MI_SPIDERS_DIRNAME);
	$spiders = $spiders_handler->getObjects(NULL);
	foreach($spiders as $spider) {
		$GLOBALS['xoopsDB']->queryF("UPDATE  ".$GLOBALS['xoopsDB']->prefix("spiders")." SET `robot-safeuseragent` = '" . $spiders_handler->safeAgent($spider->getVar('robot-useragent')) . "' WHERE `id` = '".$spider->getVar('id')."'");
		echo "&nbsp;&nbsp;UPDATE  ".$GLOBALS['xoopsDB']->prefix("spiders")." SET `robot-safeuseragent` = '" . $spiders_handler->safeAgent($spider->getVar('robot-useragent')) . "' WHERE `id` = '".$spider->getVar('id')."'<br/>";
	}
	return true;
	
}

?>