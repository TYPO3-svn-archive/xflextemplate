<?php
$GLOBALS['configuration'] = array(
	'subElement' => array(
		'main' => array('titleLabel', 'typeLabel', 'xtypeLabel', 'paletteLabel'),
		'type' => array('inputType', 'textType', 'radioType', 'checkType', 'groupType', 'cObjectType'),
		'xtype' => array('noneXtype', 'textXtype', 'imageXtype', 'multimediaXtype'),
		'inputType' => array('maxlength', 'size', 'default', 'listallowed', 'datatype', 'checkbox', 'maxval', 'minval'),
		'textType' => array('cols', 'rows', 'default', 'wrap', 'extras'),
		'checkType' => array('items','cols', 'default', 'itemprocfunc'),
		'radioType' => array('items', 'default', 'itemprocfunc'),
		'groupType' => array('hardtype', 'extallowed', 'extnotallowed', 'relations', 'maxdimension', 'thumbnail', 'maxelements', 'minelements', 'autodimension', 'multiplicity'),
		'cObjectType' => array(),
	),
);
?>