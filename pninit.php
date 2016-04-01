<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pninit.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Papelera_init()
{
    $tables = array('Papelera');
    foreach ($tables as $table) {
        if (!DBUtil::createTable($table)) {
            return false;
        }
    }
	    
    pnModSetVar('Papelera', 'modulestylesheet', 'Papelera.css');
	pnModSetVar('Papelera', 'itemsperpage', '25');
	
    return true;
}

function Papelera_upgrade($oldversion)
{
    $dom = ZLanguage::getModuleDomain('Papelera');

    // Upgrade dependent on old version number
    switch ($oldversion)
    {
		case '0.1':
			
			$tables = pnDBGetTables();
			//Tiene Navegacion para a ser un estilo
			$sql = "ALTER TABLE $tables[Mapas] DROP `TieneNav`";
			
			if (!DBUtil::executeSQL($sql)) {
				LogUtil::registerError(__('Error! Could not update table.', $dom));
				return '0.1';
			}
			// add standard data fields
			if (!DBUtil::changeTable('Mapas')) {
                return false;
            }
	}
	
	return true;
}

function Papelera_delete()
{
    $tables = array('Papelera');
    foreach ($tables as $table) {
        if (!DBUtil::dropTable($table)) {
            return false;
        }
    }
    
    pnModDelVar('Papelera');
    return true;
}
