<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Wizard to help make tables (eg. for tt_content elements) of type "table".
 * Each line is a table row, each cell divided by a |
 *
 * $Id$
 * Revised for TYPO3 3.6 November/2003 by Kasper Skaarhoj
 * XHTML compliant
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   84: class SC_wizard_table
 *  116:     function init()
 *  158:     function main()
 *  173:     function printContent()
 *  184:     function tableWizard()
 *
 *              SECTION: Helper functions
 *  223:     function getConfigCode($row)
 *  293:     function getTableHTML($cfgArr,$row)
 *  450:     function changeFunc()
 *  572:     function cfgArray2CfgString($cfgArr)
 *  603:     function cfgString2CfgArray($cfgStr,$cols)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */




require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile('EXT:lang/locallang_wizards.xml');











/**
 * Script Class for rendering the Table Wizard
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class scWizardFactory {

			// Internal, dynamic:
	/**
	 * document template object
	 *
	 * @var mediumDoc
	 */
	var $doc;
	var $content;				// Content accumulation for the module.
	var $include_once=array();	// List of files to include.
	var $inputStyle=0;			// True, then <input> fields are shown, not textareas.


		// Internal, static:
	var $xmlStorage=0;			// If set, the string version of the content is interpreted/written as XML instead of the original linebased kind. This variable still needs binding to the wizard parameters - but support is ready!
	var $numNewRows=1;			// Number of new rows to add in bottom of wizard
	var $colsFieldName='cols';	// Name of field in parent record which MAY contain the number of columns for the table - here hardcoded to the value of tt_content. Should be set by TCEform parameters (from P)


		// Internal, static: GPvars
	var $P;						// Wizard parameters, coming from TCEforms linking to the wizard.
	var $TABLECFG;				// The array which is constantly submitted by the multidimensional form of this wizard.

		// table parsing
	var $tableParsing_quote;			// quoting of table cells
	var $tableParsing_delimiter;		// delimiter between table cells





	/**
	 * Initialization of the class
	 *
	 * @return	void
	 */
	function init()	{
		global $BACK_PATH;

			// GPvars:
		$this->P = t3lib_div::_GP('P');
		$this->TABLECFG = t3lib_div::_GP('TABLE');

			// Setting options:
		$this->xmlStorage = $this->P['params']['xmlOutput'];
		$this->numNewRows = t3lib_div::intInRange($this->P['params']['numNewRows'],1,50,5);

			// Textareas or input fields:
		$this->inputStyle=isset($this->TABLECFG['textFields']) ? $this->TABLECFG['textFields'] : 1;

			// Document template object:
		$this->doc = t3lib_div::makeInstance('bigDoc');
		$this->doc->docType = 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;
		$this->doc->JScode=$this->doc->wrapScriptTags('
			function jumpToUrl(URL,formEl)	{	//
				window.location.href = URL;
			}
		');

			// Setting form tag:
		list($rUri) = explode('#',t3lib_div::getIndpEnv('REQUEST_URI'));
		$this->doc->form ='<form action="'.htmlspecialchars($rUri).'" method="post" name="wizardForm">';

			// Start page:
		$this->content.=$this->doc->startPage('Table');

			// If save command found, include tcemain:
		if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{
			$this->include_once[]=PATH_t3lib.'class.t3lib_tcemain.php';
		}

		$this->tableParsing_delimiter = '|';
		$this->tableParsing_quote = '';
	}

	/**
	 * Main function, rendering the table wizard
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;
		
		$content= '

			<!--
				Save buttons:
			-->
			<div id="c-saveButtonPanel">';
		$content.= '<input type="image" class="c-inputButton" name="savedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/savedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc',1).'" />';
		$content.= '<input type="image" class="c-inputButton" name="saveandclosedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/saveandclosedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveCloseDoc',1).'" />';
		$content.= '<a href="#" onclick="'.htmlspecialchars('jumpToUrl(unescape(\''.rawurlencode($this->P['returnUrl']).'\')); return false;').'">'.
					'<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/closedok.gif','width="21" height="16"').' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.closeDoc',1).'" alt="" />'.
					'</a>';
		$content.= '<input type="image" class="c-inputButton" name="_refresh"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/refresh_n.gif','').' title="'.$LANG->getLL('forms_refresh',1).'" />';
		$content.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'wizard_table_wiz_buttons', $GLOBALS['BACK_PATH'],'');
		$content.= '
			</div>
			';
		
		if ($this->P['table'] && $this->P['field'] && $this->P['uid'])	{
			$this->content.=$this->doc->section($LANG->getLL('table_title'),$this->wizardRender().$content,0,1);
		} else {
			$this->content.=$this->doc->section($LANG->getLL('table_title'),'<span class="typo3-red">'.$LANG->getLL('table_noData',1).'</span>',0,1);
		}
	}

	/**
	 * Outputting the accumulated content to screen
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

	/**
	 * Draws the table wizard content
	 *
	 * @return	string		HTML content for the form.
	 */
	function wizardRender()	{

			// First, check the references by selecting the record:
		$row = t3lib_BEfunc::getRecord($this->P['table'],$this->P['uid']);
		if (!is_array($row))	{
			t3lib_BEfunc::typo3PrintError ('Wizard Error','No reference to record',0);
			exit;
		}
		
		$wizardClass = t3lib_div::makeInstance('pippoWizard');
		if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{

					// Make TCEmain object:
				$tce = t3lib_div::makeInstance('t3lib_TCEmain');
				$tce->stripslashes_values=0;

					// Put content into the data array:
				$data=array();
				
				$data[$this->P['table']][$this->P['uid']][$this->P['field']]=$wizardClass->getCompactField($_POST);;

					// Perform the update:
				$tce->start($data,array());
				$tce->process_datamap();

					// If the save/close button was pressed, then redirect the screen:
				if ($_POST['saveandclosedok_x'])	{
					header('Location: '.t3lib_div::locationHeaderUrl($this->P['returnUrl']));
					exit;
				}
			}
		else{
		
		//if()
		
		
		$content = $wizardClass->render($row['test']);
		
			// This will get the content of the form configuration code field to us - possibly cleaned up, saved to database etc. if the form has been submitted in the meantime.
		//$tableCfgArray = $this->getConfigCode($row);

			// Generation of the Table Wizards HTML code:
		//$content = $this->getTableHTML($tableCfgArray,$row);

			// Return content:
		return $content;
		}
	}







	/***************************
	 *
	 * Helper functions
	 *
	 ***************************/

	/**
	 * Will get and return the configuration code string
	 * Will also save (and possibly redirect/exit) the content if a save button has been pressed
	 *
	 * @param	array		Current parent record row
	 * @return	array		Table config code in an array
	 * @access private
	 */
	function getConfigCode($row)	{

			// get delimiter settings
		$flexForm = t3lib_div::xml2array($row['pi_flexform']);

		if (is_array($flexForm)) {
			$this->tableParsing_quote = $flexForm['data']['s_parsing']['lDEF']['tableparsing_quote']['vDEF']?chr(intval($flexForm['data']['s_parsing']['lDEF']['tableparsing_quote']['vDEF'])):'';
			$this->tableParsing_delimiter = $flexForm['data']['s_parsing']['lDEF']['tableparsing_delimiter']['vDEF']?chr(intval($flexForm['data']['s_parsing']['lDEF']['tableparsing_delimiter']['vDEF'])):'|';
		}

			// If some data has been submitted, then construct
		if (isset($this->TABLECFG['c']))	{

				// Process incoming:
			$this->changeFunc();


				// Convert to string (either line based or XML):
			if ($this->xmlStorage)	{
					// Convert the input array to XML:
				$bodyText = t3lib_div::array2xml_cs($this->TABLECFG['c'],'T3TableWizard');

					// Setting cfgArr directly from the input:
				$cfgArr = $this->TABLECFG['c'];
			} else {
					// Convert the input array to a string of configuration code:
				$bodyText = $this->cfgArray2CfgString($this->TABLECFG['c']);

					// Create cfgArr from the string based configuration - that way it is cleaned up and any incompatibilities will be removed!
				$cfgArr = $this->cfgString2CfgArray($bodyText,$row[$this->colsFieldName]);
			}

				// If a save button has been pressed, then save the new field content:
			if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{

					// Make TCEmain object:
				$tce = t3lib_div::makeInstance('t3lib_TCEmain');
				$tce->stripslashes_values=0;

					// Put content into the data array:
				$data=array();
				$data[$this->P['table']][$this->P['uid']][$this->P['field']]=$bodyText;

					// Perform the update:
				$tce->start($data,array());
				$tce->process_datamap();

					// If the save/close button was pressed, then redirect the screen:
				if ($_POST['saveandclosedok_x'])	{
					header('Location: '.t3lib_div::locationHeaderUrl($this->P['returnUrl']));
					exit;
				}
			}
		} else {	// If nothing has been submitted, load the $bodyText variable from the selected database row:
			if ($this->xmlStorage)	{
				$cfgArr = t3lib_div::xml2array($row[$this->P['field']]);
			} else {	// Regular linebased table configuration:
				$cfgArr = $this->cfgString2CfgArray($row[$this->P['field']],$row[$this->colsFieldName]);
			}
			$cfgArr = is_array($cfgArr) ? $cfgArr : array();
		}

		return $cfgArr;
	}

	
}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/wizard_table.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/wizard_table.php']);
}












// Make instance:
$SOBE = t3lib_div::makeInstance('scWizardFactory');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
?>
