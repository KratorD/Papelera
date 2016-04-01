<?php
/**
 * Papelera Module for Zikula
 *
 * @copyright (c) 2011, Krator
 * @link http://www.heroesofmightandmagic.es
 * @version $Id: pntables.php 2011-10-17 $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*/

/**
 * Populate pntables array
 *
 * @author Krator
 * @return array pntables array
 */
function Papelera_pntables()
{
    $pntable = array();

    // Tabla que contiene los registros borrados de otros modulos
    $pntable['Papelera'] = DBUtil::getLimitedTablename('Papelera');
    $pntable['Papelera_column'] = array('ID'   	 	=> 'ID',
										'Modulo'	=> 'Modulo',
										'Fecha'    	=> 'Fecha',
										'Usuario'   => 'Usuario',
										'Registro'	=> 'Registro'
										);
	//http://community.zikula.org/index.php?module=Wiki&tag=ADOdbDataDictionary
    $pntable['Papelera_column_def'] = array(	'ID'   	 	=> "I NOTNULL AUTO PRIMARY",
											'Modulo'  	=> "C(64) NOTNULL",
											'Fecha'  	=> "T NOTNULL",
											'Usuario'  	=> "C(25) NOTNULL",
											'Registro'	=> "X NOTNULL"
										    );
    $pntable['Papelera_column_idx'] = array('ID' => 'ID');
	
	return $pntable;
}
