<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Markus Stauffiger (markus@4eyes.ch)
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
 * Plugin '4eyes Person database' for the 'x4epersdb' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */



/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      61:     function proc( $wizardItems )
 *      94:     function includeLocalLang
 * 
 *              TOTAL FUNCTIONS: 2
 */

class tx_x4epersdb_pi3_wizicon
{
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Wizard items functions.
     ***************************************************************/
    
    /**
     * Processing the wizard items array
     *
     * @param       array       $wizardItems        The wizard items
     * @return      array       Modified array with wizard items
     */
    function proc( $wizardItems )
    {
        global $LANG;
        
        // Get locallang values
        $LL = $this->includeLocalLang();
        
        // Wizard item
        $wizardItems[ 'plugins_tx_x4epersdb_pi3' ] = array(
            
            // Icon
            'icon'        => t3lib_extMgm::extRelPath( 'x4epersdb' ) . 'pi3/ce_wiz.gif',
            
            // Title
            'title'       => $LANG->getLLL( 'pi1_title', $LL ),
            
            // Description
            'description' => $LANG->getLLL( 'pi1_plus_wiz_description', $LL ),
            
            // Parameters
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=x4epersdb_pi3'
        );
        
        // Return items
        return $wizardItems;
    }
    
    /**
     * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array
     * found in that file.
     *
     * @return      array       The array with language labels
     */
    function includeLocalLang()
    {
        global $LANG;
        
        // Include file
        $LOCAL_LANG = $LANG->includeLLFile( 'EXT:x4epersdb/pi3/locallang.php', false );
        
        // Return file content
        return $LOCAL_LANG;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi3/class.tx_x4epersdb_pi3_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/pi3/class.tx_x4epersdb_pi3_wizicon.php']);
}
