<?php


require_once(PATH_typo3 .'interfaces/interface.cms_newcontentelementwizarditemshook.php');
class tx_xflextemplate_pi1_newContentElementWizardItemsHook implements cms_newContentElementWizardsHook{

	public function manipulateWizardItems(&$wizardItems, &$object){
		global $LANG;
		$language = $LANG;
		$language->includeLLFile('EXT:xflextemplate/locallang_db.xml');

		$xflextemplateWizardArray= array(
			'icon' => t3lib_extMgm::extRelPath("xflextemplate") . 'pi1/xft_wiz.gif',
		    'title' => 'Xflextemplate',
		    'description' => $language->getLL('wizardText'),
			'params' => '&defVals[tt_content][CType]=xflextemplate_pi1',
		    'tt_content_defValues' =>
		    array (
		      'CType' => 'xflextemplate_pi1',
		    )
		);
		$tempArray = $wizardItems;
		$wizardItems = array();

		foreach($tempArray as $key=>$item){
			$wizardItems[$key] = $item;
			if($key == 'common'){
				$wizardItems['common_xflextemplate_pi1'] = $xflextemplateWizardArray;
			}
		}
	}
}