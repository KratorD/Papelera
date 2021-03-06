<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 31 2012-02-07 20:55:41Z Krator $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Papelera_admin_main()
{
    if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
	
	return Papelera_admin_view();
}

//Muestra la lista completa de registros borrados de otros modulos.
function Papelera_admin_view($args)
{
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$page  = (int)FormUtil::getPassedValue('page', isset($args['page']) ? $args['page'] : 1, 'GET');
	$order = FormUtil::getPassedValue('order', isset($args['order']) ? $args['order'] : 'ID', 'GET');
	$modulo = FormUtil::getPassedValue('modulo', isset($args['modulo']), 'GET');
	$usuario = FormUtil::getPassedValue('usuario', isset($args['usuario']), 'GET');

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Papelera');
	
	$itemsperpage = $modvars['itemsperpage'];

	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');
	
	// Primer elemento a obtener de la paginacion
	$startnum = (($page - 1) * $itemsperpage) + 1;
	
	// Procesamos los datos con los APIs necesarios
	$listado = pnModAPIFunc('Papelera', 'admin', 'getAll', 
						array(	'Modulo'	=> $modulo,
								'Usuario'	=> $usuario,
								'startnum'  => $startnum,
								'numitems'  => $itemsperpage,
								'order'     => $order));
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Papelera');

	//Enviarlas a la plantilla
	$render->assign('listado', $listado);
	
	// Asignar los valores al sistema de paginaci�n
	$render->assign('pager', array(	'numitems' => pnModAPIFunc('Papelera', 'user', 'countitems',
													array(	'Modulo'	=> $modulo,
															'Usuario'	=> $usuario)),
									'itemsperpage' => $itemsperpage));
	
	return $render->fetch('Papelera_admin_view.htm');
	
}

//Muestra el registro borrado unico
function Papelera_admin_display($args)
{
	
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$id  = (int)FormUtil::getPassedValue('id', isset($args['id']) , 'GET');
	
  	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');
	
	//Obtenemos los datos del registro borrado en otro modulo
	$record = pnModAPIFunc('Papelera', 'admin', 'get', array('ID' => $id));
	if ($record == false)
		return LogUtil::registerError(__('Error! Record not found.', $dom));

	//Separamos por columnas para hacerlo m�s legible y presentable
	$columnas = explode("#", $record['Registro']);
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Papelera');
	
	//Pasamos las variables a la plantilla
	$render->assign('record', $record);
	$render->assign('columnas', $columnas);
	
	return $render->fetch('Papelera_admin_display.htm');
	
}

//Funcion para borrar un registro para siempre en La Torre
function Papelera_admin_delete($args)
{
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	$confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');
	
	//Recuperar los parametros
	$id = isset($args['id']) ? $args['id'] : FormUtil::getPassedValue('id', null, 'REQUEST');

	// Check for confirmation.
	if (empty($confirmation)) {
  	// No confirmation yet
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Papelera');
		$render->assign('id', $id);

		// Return the output that has been generated by this function
		return $render->fetch('Papelera_admin_delete.htm');
	}
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (pnModURL('Papelera', 'admin', 'view'));
	}
	
	//Confirmamos que el registro que queremos borrar, existe.
	$lista = pnModAPIFunc('Papelera', 'admin', 'get', array('ID' => $id));
	
	if ($lista == false) {
		return LogUtil::registerError(__('Error! The record not exists.', $dom));
	}
	
	if (pnModAPIFunc('Papelera', 'admin', 'delete', array('id' =>$id))) {
		// Success
		LogUtil::registerStatus (__('Record deleted sucessfully.', $dom));
	}else{
		// Error
		return LogUtil::registerError(__('Error! The record was not deleted.', $dom));
	}
	return pnRedirect(pnModURL('Papelera', 'admin', 'view'));
	
}

//Funcion para deshacer el borrado de los otros modulos.
function Papelera_admin_restart($args)
{
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$id = isset($args['id']) ? $args['id'] : FormUtil::getPassedValue('id', null, 'REQUEST');
	$confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');
	
	// Check for confirmation.
	if (empty($confirmation)) {
  	// No confirmation yet
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Papelera');
		$render->assign('id', $id);

		// Return the output that has been generated by this function
		return $render->fetch('Papelera_admin_restart.htm');
	}
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (pnModURL('Papelera', 'admin', 'view'));
	}
	
	//Confirmamos que el registro que queremos borrar, existe.
	$lista = pnModAPIFunc('Papelera', 'admin', 'get', array('ID' => $id));
	
	if ($lista == false) {
		return LogUtil::registerError(__('Error! The record not exists.', $dom));
	}
	
	if (pnModAPIFunc('Papelera', 'admin', 'recover', array('id' =>$id))) {
		//Si ha tenido �xito, se borra el registro de "Papelera"
		pnModAPIFunc('Papelera', 'admin', 'delete', array('id' =>$id));
		// Success
		LogUtil::registerStatus (__('Record recover sucessfully.', $dom));
	}else{
		// Error
		return LogUtil::registerError(__('Error! The record was not recover.', $dom));
	}
	return pnRedirect(pnModURL('Papelera', 'admin', 'view'));
	
}

//Funcion para mostrar la configuracion
function Papelera_admin_modifyconfig($args)
{
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Papelera');

	$itemsperpage = $modvars['itemsperpage'];

	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Papelera');

	$render->assign('itemsperpage', $itemsperpage);

	return $render->fetch('Papelera_admin_modifyconfig.htm');
	
}

//Funciona para actualizar la configuracion del modulo
function Papelera_admin_updateconfig($args)
{
	if (!SecurityUtil::checkPermission('Papelera::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Papelera');

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Papelera');

	$itemsperpage = $modvars['itemsperpage'];

	//Valor de itemsperpage
	$txtitemsperpage = FormUtil::getPassedValue('txtitemsperpage', null, 'POST');
	if ($txtitemsperpage != $itemsperpage){
		pnModSetVar('Papelera', 'itemsperpage', $txtitemsperpage);
	}

	// the module configuration has been updated successfuly
    LogUtil::registerStatus (__('Done! Module configuration updated.', $dom));

    return pnRedirect(pnModURL('Papelera', 'admin', 'main'));
	
}