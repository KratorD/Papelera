<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadminapi.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Obtener los registros borrados de otros modulos
 * @param $args['Modulo'] Nombre del Módulo
 * @param $args['Usuario'] Nombre del Usuario
 * @return bool true on success, false on failure
 */
function Papelera_adminapi_getAll($args)
{
	// Security check
    if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_OVERVIEW)) {
        return $items;
    }

	//Construir la clausula WHERE
	if (isset($args['Modulo']) && $args['Modulo'] != ''){
		// Obtiene los registros por el nombre del módulo
		$queryargs[] = "`Modulo` LIKE '%".$args['Modulo']."%'";
	}
	if (isset($args['Usuario']) && $args['Usuario'] != ''){
		// Obtiene todos los mapas porque el autor contiene...
		$queryargs[] = "`Usuario` LIKE '%".$args['Usuario']."%'";
	}
	
	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}
	//Ordenar la tabla de resultados
	if (!isset($args['order']) || empty($args['order'])) {
		$args['order'] = 'Modulo';
	}
	if (!isset($args['tipoOrden']) || empty($args['tipoOrden'])) {
		$args['order'].= ' desc';
	} else {
		$args['order'].= ' '.$args['tipoOrden'];
	}
	//Paginación y nº de resultados
	if (!isset($args['startnum']) || empty($args['startnum'])) {
		$args['startnum'] = 1;
	}
	if (!isset($args['numitems']) || empty($args['numitems'])) {
		$args['numitems'] = -1;
	}
	if (!is_numeric($args['startnum']) ||
		!is_numeric($args['numitems'])) {
		return LogUtil::registerArgsError();
	}

	$objArray = DBUtil::selectObjectArray ('Papelera', $where, $args['order'], $args['startnum']-1, $args['numitems']);
	
	//Return objects retrieved
	return $objArray;
		
}

/**
 * Obtener un registro especifico
 * @param int $args['ID'] id of example item to get
 * @return mixed item array, or false on failure
 */
function Papelera_adminapi_get($args)
{
    // Argument check
    if (!isset($args['ID'])) {
        return LogUtil::registerArgsError();
    }

    // retrieve the category object
    $result = DBUtil::selectObjectByID('Papelera', (int)$args['ID'], 'ID');
    if (!$result) {
        return false;
    }

    // Return the item array
    return $result;
}

/**
 * Borrar un Registro
 * @param $args['id'] ID del Registro
 * @return bool true on success, false on failure
 */
function Papelera_adminapi_delete($args)
{
	// Argument check
	if (!isset($args['id'])) {
		return LogUtil::registerArgsError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');

	//Confirmamos que el registro que queremos borrar, existe.
	$item = pnModAPIFunc('Papelera', 'admin', 'get', array('ID' => $args['id']));

	if ($item === false) {
		return LogUtil::registerError(__('No such item found.', $dom));
	}

	if (!DBUtil::deleteObjectByID('Papelera', $args['id'], 'ID')) {
		return LogUtil::registerError(__('Error! Deletion attempt failed.', $dom));
	}

	// The item has been modified, so we clear all cached pages of this item.
	$render = & pnRender::getInstance('Papelera');
	$render->clear_cache(null, $args['id']);
	$render->clear_cache('Papelera_admin_view.htm');

	return true;
}

/**
 * Función que recuperará el registro borrado de otro módulo
 * @param $args['id'] ID interno del registro
 * @return bool true on success, false on failure
 */
function Papelera_adminapi_recover($args)
{
	
	// Valida los parámetros requeridos
	if (!isset($args['id']) ) {
		return LogUtil::registerArgsError();
	}else{
		$id = $args['id'];
	}

	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');

	//Confirmamos que el registro que queremos recuperar, existe.
	$item = pnModAPIFunc('Papelera', 'admin', 'get', array('ID' => $id));

	if ($item === false) {
		return LogUtil::registerError(__('No such item found.', $dom));
	}

	//Recuperar el nombre de la tabla
	$meta = pnModDBInfoLoad($item['Modulo']);
	$tabla = $meta[$item['Modulo']];
	
	//Dividir la cadena por columnas
	$columnas = explode("#", $item['Registro'], -1); //El -1 sirve para que no tome el ultimo elemento vacio, muy util!

	//Construccion de la sentencia
	$cabecera = "INSERT INTO `$tabla` ";
	
	foreach ($columnas as $columna){
		//Dividir por clave/valor
		$cad1= explode("->", $columna);
		$cad2[]= "`".$cad1[0]."` = '".$cad1[1]."'";
	}
	
	if (count($cad2) > 0) {
		$set = ' SET ' . implode(' , ', $cad2);
	}
	
	//Obtener la clausula WHERE
	//$where = " WHERE ". $cad2[0];
	
	//Generar la consulta SQL
	$sql = $cabecera.$set;
	
	/*Este código está preparado para usarse fácilmente con un UPDATE. De hecho la sentencia pertenece más a un 
	  UPDATE que a un INSERT INTO, pero igualmente funciona, así que lo dejo de esta forma
	*/
	
	//Insertar el registro en la BD
	return DBUtil::executeSQL($sql);

}

/**
 * Función para módulos externos que almacenará el registro en "Papelera"
 * @param $args['mid'] Mapa
 * @param $args['score'] Puntuación
 * @return bool true on success, false on failure
 */
function Papelera_adminapi_store($args)
{
	
	// Valida los parámetros requeridos
	if (!isset($args['Modulo']) ) {
		return LogUtil::registerArgsError();
	}else{
		$args['Modulo'] = pnVarPrepForStore($args['Modulo']);
	}
	if (!isset($args['Tabla']) ) {
		return LogUtil::registerArgsError();
	}else{
		$args['Tabla'] = pnVarPrepForStore($args['Tabla']);
	}
	if (!isset($args['Usuario']) ) {
		return LogUtil::registerArgsError();
	}else{
		$args['Usuario'] = pnVarPrepForStore($args['Usuario']);
	}
	if (!isset($args['Registro']) ) {
		return LogUtil::registerArgsError();
	}else{
		$idRegistro = $args['Registro'];
	}
	
	//Recuperar la fecha actual.
	$args['Fecha'] = date('Y-m-d H:i');
	$args['Fecha'] = pnVarPrepForStore($args['Fecha']);
	
	//Recuperar el nombre de las columnas de la tabla del modulo
	$columnas = DBUtil::getColumnsArray($args['Tabla']);
	//Recuperar el registro del Modulo
	$registro = DBUtil::selectObjectByID($args['Tabla'], $idRegistro, $columnas[0]);
	
	foreach ($columnas as $columna){
		$cadena.= $columna . "->" . $registro[$columna] . "#";
	}
	
	$args['Registro'] = '';
	$args['Registro'] = $cadena;
	
	//Generar el registro
	return DBUtil::insertObject($args, 'Papelera','ID', false, true);

}

/**
 * Contar los registros por una condicion
 * @param $args['Modulo'] Modulo
 * @param $args['Usuario'] Usuario
 * @return Número de registros encontrados
 */
function Papelera_userapi_countitems($args)
{
	
	$queryargs = array();

	if (isset($args['Modulo']) && $args['Modulo'] != ''){
		// Obtiene los registros por el nombre del módulo
		$queryargs[] = "`Modulo` LIKE '%".$args['Modulo']."%'";
	}
	if (isset($args['Usuario']) && $args['Usuario'] != ''){
		// Obtiene todos los mapas porque el autor contiene...
		$queryargs[] = "`Usuario` LIKE '%".$args['Usuario']."%'";
	}
	
	$where = '';
	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}

	return DBUtil::selectObjectCount ('Papelera', $where, 'ID', false, '');

}