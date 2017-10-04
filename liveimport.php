<?php

require_once 'liveimport.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function liveimport_civicrm_config(&$config) {
  _liveimport_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function liveimport_civicrm_xmlMenu(&$files) {
  _liveimport_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function liveimport_civicrm_install() {
  _liveimport_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function liveimport_civicrm_postInstall() {
  _liveimport_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function liveimport_civicrm_uninstall() {
  _liveimport_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function liveimport_civicrm_enable() {
  _liveimport_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function liveimport_civicrm_disable() {
  _liveimport_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function liveimport_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _liveimport_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function liveimport_civicrm_managed(&$entities) {
  _liveimport_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function liveimport_civicrm_caseTypes(&$caseTypes) {
  _liveimport_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function liveimport_civicrm_angularModules(&$angularModules) {
  _liveimport_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function liveimport_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _liveimport_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function liveimport_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function liveimport_civicrm_navigationMenu(&$menu) {
  _liveimport_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'nl.roparun.liveimport')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _liveimport_civix_navigationMenu($menu);
} // */

function liveimport_civicrm_pageRun(&$page){
  //Civi::log()->info('Now the following page is runned -> '.get_class($page));
}

function liveimport_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName){
  //Civi::log()->info("alterTemplateFile $formName in template $tplName");
}

function liveimport_civicrm_buildForm($formName, &$form){
 // Civi::log()->info("buildForm $formName ");
}

function liveimport_civicrm_navigationMenu( &$params ) {
  $maxKey = (max(array_keys($params)));
  $params[$maxKey+1] = array (
    'attributes' => array (
      'label'      => 'Roparun',
      'name'       => 'roparun',
      'url'        => null,
      //'permission' => 'access CiviCRM',
      'operator'   => null,
      'separator'  => null,
      'parentID'   => null,
      'navID'      => $maxKey+1,
      'active'     => 1
    ),
    'child' =>  array (
      '1' => array (
        'attributes' => array (
          'label'      => 'Live Import Upload',
          'name'       => 'roparun_liveimport',
          'url'        => CRM_Utils_System::url('civicrm/liveimport/upload', 'reset=1', true),
          'operator'   => null,
          'separator'  => 0,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      ),
    ));
}
