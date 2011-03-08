<?php
/**
 * Helper class to fill the flexform
 */
class tx_x4epersdb_helper {

	function main(&$params,&$pObj)	{
		global $TCA;
		t3lib_div::loadTCA($params['config']['table']);

		$params['items']=array();
		if (is_array($TCA[$params['config']['table']]['columns']))	{
			foreach($TCA[$params['config']['table']]['columns'] as $key => $config)	{
				if ($config['label'] && !t3lib_div::inList('password',$key))	{
					$label = t3lib_div::fixed_lgd(ereg_replace(':$','',$GLOBALS['LANG']->sL($config['label'])),30).' ('.$key.')';
					$params['items'][]=Array($label, $key);
				}
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/class.tx_x4epersdb_helper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4epersdb/class.tx_x4epersdb_helper.php']);
}
?>