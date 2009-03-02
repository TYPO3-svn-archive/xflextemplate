<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:tx_flextemplate/class.tx_xflextemplate_tceforms.php:tx_xflextemplate_tceforms';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:tx_xflextemplate/class.tx_xflextemplate_tcemain.php:tx_xflextemplate_tcemain';

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_xflextemplate_pi1.php','_pi1','CType',1);

?>