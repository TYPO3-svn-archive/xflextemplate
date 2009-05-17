<?php
$GLOBALS['configuration'] = array(
	'subElement' => array(
		'main' => array('titleLabel', 'typeLabel', 'xtypeLabel', 'paletteLabel'),
		'type' => array('inputType', 'textType', 'radioType', 'checkType', 'groupType', 'cObjectType','wizardType'),
		'xtype' => array('noneXtype', 'textXtype', 'imageXtype', 'multimediaXtype'),
		'inputType' => array('max', 'size', 'default', 'is_in', 'eval', 'checkbox', 'maxval', 'minval'),
		'textType' => array('cols', 'rows', 'default', 'wrap', 'defaultExtras'),
		'checkType' => array('items','cols', 'default', 'itemprocfunc'),
		'radioType' => array('items', 'default', 'itemprocfunc'),
		'groupType' => array('internal_type', 'allowed', 'disallowed', 'MM', 'max_size', 'show_thumbs', 'maxitems', 'minitems', 'size', 'autoSizeMax', 'multiple'),
		'cObjectType' => array(),
		'wizardType' => array('wizicon','classPath','className')
	),
);
?>