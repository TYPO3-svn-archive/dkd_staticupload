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
 *   59: class tx_dkdstaticupload_module1 extends t3lib_SCbase
 *   65:     function init()
 *   82:     function menuConfig()
 *  100:     function main()
 *  119:     function jumpToUrl(URL)
 *  168:     function printContent()
 *  179:     function moduleContent()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
	// ....(But no access check here...)
	// DEFAULT initialization of a module [END]

require_once( t3lib_extMgm::extPath('dkd_staticupload') . 'mod1/class.tx_dkdstaticupload_module1.php' );

	// TimeTrack'ing is needed e.g. by t3lib_TStemplate
require_once(PATH_t3lib.'class.t3lib_timetrack.php');

$TT = new t3lib_timeTrack;
$TT->start();
$TT->push('','Script start (dkd_staticupload/mod1)');

$GLOBALS['LANG']->includeLLFile('EXT:dkd_staticupload/mod1/locallang.php');

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dkdstaticupload_module1');
$SOBE->init();


$SOBE->main();
$SOBE->printContent();

$TT->pull();

?>