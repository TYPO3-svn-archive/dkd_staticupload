<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Andreas Otto (andreas.otto@dkd.de)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'd.k.d Static Upload' for the 'dkd_staticupload' extension.
 *
 * @author	Andreas Otto <andreas.otto@dkd.de>
 * @version $Id$
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_dkdstaticupload_module1 extends t3lib_SCbase
 *   69:     function init()
 *  100:     function menuConfig()
 *  117:     function main()
 *  137:     function jumpToUrl(URL)
 *  186:     function printContent()
 *  198:     function getDomainStartPages()
 *  220:     function moduleContent()
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once ($BACK_PATH.'template.php');

require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_tstemplate.php');
require_once (PATH_t3lib.'class.t3lib_page.php');

class tx_dkdstaticupload_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $prefixId = 'tx_dkdstaticupload_module1';
	var $modVars = array();		// variables from this module
	var $sysPage = '';		// page selector obj
	var $tmpl = '';		// TS template obj

	/**
	 * Initializes the backend module by setting internal variables, initializing the menu.
	 *
	 * @return	void
	 * @see menuConfig()
	 */
	function init()	{
		parent::init();

			// Get page ID and set $this->id
		$domainUid = intval( t3lib_div::_GP( 'domain' ) );
		if ( !empty( $domainUid ) ) {
				// Get the domain name
			$this->domain = t3lib_BEfunc::getRecord( 'sys_domain', $domainUid, 'pid,domainName' );

				// set the permissions clause publishing function
			$permission_bitmask = ( $this->permissions['show'] | $this->permissions['content'] );
			$this->publish_permsClause = $GLOBALS['BE_USER']->getPagePermsClause( $permission_bitmask );

				// initialize the page selector
			$this->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
			$this->sys_page->init(true);

				// initialize the TS template
			$this->tmpl = t3lib_div::makeInstance('t3lib_TStemplate');
			$this->tmpl->init();
			$rootline = $this->sys_page->getRootLine( $this->domain['pid'] );
			$this->tmpl->start($rootline);
		}

	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	[type]		...
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
				"2" => $LANG->getLL("function2"),
			)
		);
		parent::menuConfig();
	}

		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->domain['pid'],$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->domain['pid'] && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->docType = 'xhtml_trans';
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	[type]		...
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}


	/**
	 * Prints out a DropDown list with all domain records found.
	 *
	 * @return	string	HTML DropDown list. Error message if no domain records are found.
	 */
	function getDomainStartPages() {
		$out = '';
		$domains = t3lib_BEfunc::getRecordsByField('sys_domain','hidden',0);
		if ( is_array( $domains ) ) {
			$options = array();
			foreach ( $domains as $key => $var ) {
				$options[$key] = sprintf( '<option value="%d">%s</option>', $var['uid'], $var['domainName'] );
			}

			$out = sprintf( '<select name="domain" size="1">%s</select>', implode( chr( 10 ), $options ) );
		}else{
			$out = sprintf( '<p>%s</p>', $GLOBALS['LANG']->getLL( 'function2.no_domain', 'No domain records found.' ) );
		}

		return $out;
	}

	/**
	 * Generates the module content
	 *
	 * @return	[type]		...
	 */
	function moduleContent()	{
		switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
				$code='';

				require_once(t3lib_extMgm::extPath('dkd_staticupload').'mod1/class.tx_dkdstaticupload_svlist.php');
				$list = t3lib_div::makeInstance('tx_dkdstaticupload_svlist');
				$list->pObj = &$this;

				$code.= $list->serviceTypeList_loaded();

				$code.= $GLOBALS['LANG']->getLL( 'function1.text', 'Overview of available services' );

				$this->content.=$this->doc->section( $GLOBALS['LANG']->getLL( 'function1.section', 'Services' ), $code,0,1 );
			break;
			case 2:
				if ( empty( $this->domain ) ){
					$submitButton = sprintf( '<input type="submit" value="%s" />', $GLOBALS['LANG']->getLL( 'function2.button_select_domain', 'Upload pages' ) );
					$code = sprintf( '%s<br />%s', $this->getDomainStartPages(), $submitButton );
				}else{
					unset( $serviceObj, $code, $conf );
					// Load the configuration array and add the BE User language setting
					$conf = $this->tmpl->setup['service.']['staticUpload.'][str_replace( '.', '_', $this->domain['domainName'] ).'.'];
					$conf['lang'] = $GLOBALS['BE_USER']->uc['lang'];

					if (!is_object( $serviceObj = t3lib_div::makeInstanceService('staticUpload', $conf['service']) ) ){
						$code = sprintf( '<p>%s</p>', $GLOBALS['LANG']->getLL( 'function2.no_service', 'No service available.' ) );
					}else{
						$code = sprintf( '<p>%s</p>', sprintf( $GLOBALS['LANG']->getLL( $serviceObj->process( $conf ) ), $conf['server'] ) );
						if ( t3lib_div::validEmail( $conf['email'] ) ) {
							$code .= sprintf( '<p>%s</p>', sprintf( $GLOBALS['LANG']->getLL( 'function2.service_email', 'An eMail will be sent to "%s".' ), $conf['email'] ) );
						}
					}
				}

				$this->content .= $this->doc->section( $GLOBALS['LANG']->getLL( 'function2.section', 'Starting staticUpload' ), $code,0,1);
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dkd_staticupload/mod1/class.tx_dkdstaticupload_module1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dkd_staticupload/mod1/class.tx_dkdstaticupload_module1.php']);
}

?>