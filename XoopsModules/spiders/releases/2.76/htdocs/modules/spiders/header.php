<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."mainfile.php");
include(dirname(__FILE__).DS.'include'.DS.'functions.php');
include(dirname(__FILE__).DS.'include'.DS.'forms.php');

$module_handler = xoops_gethandler('module');
$config_handler = xoops_gethandler('config');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid')); 
	
xoops_load('pagenav');	
xoops_load('xoopslists');
xoops_load('xoopsformloader');

include_once $GLOBALS['xoops']->path('class'.DS.'xoopsmailer.php');
include_once $GLOBALS['xoops']->path('class'.DS.'xoopstree.php');

xoops_loadLanguage('main', 'spiders');

$GLOBALS['myts'] =& MyTextSanitizer::getInstance();

?>