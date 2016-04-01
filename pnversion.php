<?php
/**
 * Papelera  Module
 *
 * @package      Papelera
 * @version      $Id: pnversion.php 2011-10-17$
 * @author       Krator
 * @link         http://www.heroesofmightandmagic.es
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

$dom = ZLanguage::getModuleDomain('Papelera');
$modversion['name']           = 'Papelera';
$modversion['displayname']    = __('Papelera', $dom);
$modversion['url']            = __('Papelera', $dom);
$modversion['version']        = '1.0';
$modversion['description']    = __('Store of records deleted', $dom);
$modversion['credits']        = 'pndocs/changelog.txt';
$modversion['help']           = 'pndocs/readme.txt';
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Krator';
$modversion['contact']        = 'http://www.heroesofmightandmagic.es';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('Papelera::' => 'Papelera::');
